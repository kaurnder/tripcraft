<?php
    /*
    Plugin Name: CSV upload file insert post
    Description: Adds a shortcode [my_posts] and an admin page showing how to use it.
    Author: Wp saints
    Author URI: https://wpsaints.com
    Version: 9.0
    */

    // valid csv file upload with validation 
    function csv_importer_process_upload() {
        if (empty($_REQUEST['sel_posts'])) {
            echo "<p class='csv_error'>Please select a post type before uploading.</p>";
            return;
        }

       if($_FILES['csv_file']['error'] == UPLOAD_ERR_OK) {
        $file_path = $_FILES['csv_file']['tmp_name'];
        $file = fopen($file_path, "r");

        if ($file) {
            $header = fgetcsv($file);
            $rows = [];
            $error_message = '';

            //Validate all rows first
            while(($row = fgetcsv($file)) !== false) {
                if(count($header)!== count($row)) {
                    continue;
                }

                $post_data = array_combine($header, $row);
               
                $post_title   = trim($post_data['postTitle'] ?? '');
                $post_content = trim($post_data['Description'] ?? '');
                $sub_title    = trim($post_data['subtitles'] ?? '');
                $image_name   = trim($post_data['image'] ?? '');

                if (empty($post_title)) {
                    $error_message = "Title is required.";
                    break;
                }

                if (empty($post_content)){
                     $error_message = "Description is required.";
                    break;
                }

                if(empty($sub_title)) {
                    $error_message = "Subtitle is required.";
                    break;
                }
                $rows[] = $post_data;
            }
            fclose($file);
 
            //Show validation error
            if (!empty($error_message)) {
                echo "<p  class='csv_error'>$error_message</p>";
                return;
            }
            $posts_created = 0;
            $post_type = sanitize_text_field($_REQUEST['sel_posts']);

            //insert post data 
            foreach($rows as $post_data){
                $post_title   = trim($post_data['postTitle'] ?? '');
                $post_content = trim($post_data['Description'] ?? '');
                $sub_title    = trim($post_data['subtitles'] ?? '');
                $image_name   = trim($post_data['image'] ?? '');

                
                $upload_dir = wp_upload_dir();
                $image_path = trailingslashit($upload_dir['basedir']) . 'csv-images/' . $image_name;

                if (!file_exists($image_path)) {
                    echo "<p  class='csv_error'>Image file not found: $image_name</p>";
                    continue;
                }
                $new_post = [
                    'post_title'   => $post_title,
                    'post_status'  => 'draft',
                    'post_type'    => $post_type,
                ];

                if($post_type === 'ace_services' ||$post_type === 'products') {
                        $new_post['post_content'] = $post_content;
                }
                $post_id = wp_insert_post($new_post); 

                if ($post_id){
                    if ($post_type === 'blog') {
                        update_field('description', $post_content, $post_id);
                        update_field('sub_title', $sub_title, $post_id);
                    }
                     elseif($post_type === 'populardestination') {
                        update_field('destination_description', $post_content, $post_id);
                        update_field('sub_title_destination', $sub_title, $post_id);
                    }
                    elseif($post_type === 'ace_services')
                    {
                          update_field('post_sub_title', $sub_title, $post_id);
                    }
                     elseif($post_type === 'products')
                    {
                          update_field('product_sub_title', $sub_title, $post_id);
                    }
                
                    // Set featured image
                    $attachment_id = create_attachment_from_local_image($image_path, $post_id);
                    if (is_numeric($attachment_id)) {
                        set_post_thumbnail($post_id, $attachment_id);
                    }

                    $posts_created++;
                }
            }

            if ($posts_created > 0) {
                echo '<p style="text-align:center;" ><strong>' . $posts_created . ' posts created successfully.</strong></p>';
            } else {
                echo '<p class="csv_error"><strong>No posts were created.</strong></p>';
            }
        } else {
            echo '<p class="csv_error"><strong>Error opening file.</strong></p>';
        }
    } else {
        echo '<p class="csv_error"><strong>Error uploading file.</strong></p>';
    }
    }

    //insert featured image helper function
    function create_attachment_from_local_image($image_path, $post_id) {
        $filetype = wp_check_filetype(basename($image_path), null);
        $upload_dir = wp_upload_dir();

        // Copy file to uploads folder if not already there
        $filename = basename($image_path);
        $destination = $upload_dir['path'] . '/' . $filename;

        if (!file_exists($destination)){
            copy($image_path, $destination);
        }

        $attachment = array(
            'guid'           => $upload_dir['url'] . '/' .basename($filename),
            'post_mime_type' => $filetype['type'],
            'post_title'     => sanitize_file_name($filename),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        $attach_id = wp_insert_attachment($attachment, $destination, $post_id);
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attach_data = wp_generate_attachment_metadata($attach_id, $destination);
        wp_update_attachment_metadata($attach_id, $attach_data);
        return $attach_id;
    }

    function insert_post_shortcode(){
        if(isset($_POST['submit'])) {
                csv_importer_process_upload();     
        }
        ?>
         <div class="csv-upload-card">
            <h3>Upload CSV to Insert Posts</h3>
            <form method="post" enctype="multipart/form-data" action="">
                <label for="sel_posts">Select Post Type</label>
                <select name="sel_posts" id="sel_posts">
                    <option value="">-- Select --</option>
                    <option value="blog">Blog</option>
                    <option value="ace_services">Services</option>
                    <option value="populardestination">Destination</option>
                    <option value="products">Products</option>
                </select>

                <label for="csv_file">Choose CSV File</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv">

                <button type="submit" name="submit">Upload and Insert Posts</button>
            </form>
        </div>
      <?php
    }

    add_shortcode('csv_shortcode', 'insert_post_shortcode');
            function the_admin_menu() {
                add_menu_page( 
                    'Form',
                    'csv upload file insert post data', 
                    'manage_options', 
                    'mypage', 
                    'the_admin_menu_page',
                    'dashicons-menu',
                    6 );
                
    }
    add_action( 'admin_menu', 'the_admin_menu' );
    //display content wordpress plugin section 
    function the_admin_menu_page(){
    ?>
    <div class="wrap">
            <h1>CSV Upload file to insert post</h1>
            <div style="background: #f1f1f1; border-left: 4px solid #0073aa; padding: 10px; margin: 20px 0;">
                <p><strong>Use this shortcode:</strong> <code>[csv_shortcode]</code></p>
                <p>Paste it into any post, page, or widget to use.</p>
            </div>
            <p>This is a demo. You can later define the actual shortcode functionality using<code>add_shortcode()</code>.</p>
        </div>
    <?php
    }  
?>

