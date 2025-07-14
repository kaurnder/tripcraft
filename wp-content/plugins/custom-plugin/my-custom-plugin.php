<?php
/**
 * Plugin Name: custom csv
 * Description: Adds a shortcode [my_posts] and an admin page showing how to use it.
 * Version: 1.0
 * Author: Your Name
 */

// Shortcode to fetch and show 
function my_custom_post_shortcode() { 

 ob_start();
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['my_csv_upload_nonce'])) {
        if (wp_verify_nonce($_POST['my_csv_upload_nonce'], 'my_csv_upload_action')) {
            if (!empty($_FILES['my_csv_file']['name'])) {
                $file_tmp = $_FILES['my_csv_file']['tmp_name'];

                //Validate MIME type
                $mime = mime_content_type($file_tmp); 
                $allowed = ['text/plain', 'text/csv', 'application/vnd.ms-excel'];
                if(!in_array($mime, $allowed)) {
                    echo '<div style="color:red;">Only CSV files are allowed.</div>';
                } else {
                    $csv = fopen($file_tmp, 'r');
                    $row = 0;
                    $inserted = 0;

                    while (($data = fgetcsv($csv, 1000, ',')) !== FALSE) {
                        if ($row === 0){
                            $row++;
                            continue;
                        }
                        $title       = sanitize_text_field($data[0]);
                        $description = sanitize_textarea_field($data[1]);
                        $image_file  = sanitize_file_name($data[2]);
                        $image_path  = ABSPATH . 'wp-content/uploads/csv-images/' . $image_file;

                        // Create the post
                        $post_id = wp_insert_post([
                            'post_title'   => $title,
                            'post_status'  => 'draft',
                            'post_type'    => 'blog',
                        ]);

                        if (!is_wp_error($post_id)){
                            // ACF description
                            update_field('description', $description, $post_id);

                            // Set featured image if exists
                            if (file_exists($image_path)) {
                                require_once ABSPATH . 'wp-admin/includes/image.php';
                                require_once ABSPATH . 'wp-admin/includes/file.php';
                                require_once ABSPATH . 'wp-admin/includes/media.php';

                                $image_data = file_get_contents($image_path);
                                $upload = wp_upload_bits($image_file, null, $image_data);

                                if (!$upload['error']) {
                                    $filetype = wp_check_filetype(basename($upload['file']), null);
                                    
                                    $attachment = [
                                        'post_mime_type' => $filetype['type'],
                                        'post_title'     => sanitize_file_name($image_file),
                                        'post_content'   => '',
                                        'post_status'    => 'inherit',
                                    ];

                                    $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
                                    if(!is_wp_error($attach_id)) {
                                        $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
                                        wp_update_attachment_metadata($attach_id, $attach_data);
                                        set_post_thumbnail($post_id, $attach_id);
                                    }
                                }
                            }
                            $inserted++;
                        }
                        $row++;
                    }

                    fclose($csv);
                    echo '<div style="color:green;">Uploaded and inserted ' . $inserted . ' posts.</div>';
                }
            } else {
                echo '<div style="color:orange;">Please select a file to upload.</div>';
            }
        } else {
            echo '<div style="color:red;">Invalid form submission.</div>';
        }
    }

    // Upload form
    ?>
    <div style="background: #f9f9f9; border: 1px solid #ccc; padding: 20px; max-width: 600px;">
        <h3>Upload CSV to Insert Blog Posts with Images</h3>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('my_csv_upload_action', 'my_csv_upload_nonce'); ?>
            <p><input type="file" name="my_csv_file" accept=".csv" ></p>
            <p><button type="submit" name="submit">Upload and Insert Posts</button></p>
        </form>
    </div>
    <?php

    return ob_get_clean();

}
add_shortcode('csv_upload_post', 'my_custom_post_shortcode');

// Add admin menu page
function my_plugin_add_menu_page() {
    add_menu_page(
        'My Posts Page',                  // Page title
        'csv',                            // Menu label
        'manage_options',                 // Capability
        'my-posts-plugin',                // Slug
        'my_plugin_render_admin_page',    // Callback function
        'dashicons-admin-post',           // Icon
        6                                 // Position
    );
}
add_action('admin_menu', 'my_plugin_add_menu_page');

// Render admin page content
function my_plugin_render_admin_page() {
    ?>
    <div class="wrap">
        <h1>CSV Upload file to insert post</h1>
        <div style="background: #f1f1f1; border-left: 4px solid #0073aa; padding: 10px; margin: 20px 0;">
            <p><strong>Use this shortcode:</strong> <code>[csv_upload_post]</code></p>
            <p>Paste it into any post, page, or widget to use.</p>
        </div>
        <p>This is a demo. You can later define the actual shortcode functionality using <code>add_shortcode()</code>.</p>
    </div>
    <?php
}




