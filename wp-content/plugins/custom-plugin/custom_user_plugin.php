<?php
    /**
     * Plugin Name: Custom user Plugin
     * Plugin URI: https://example.com/my-custom-plugin
     * Description: A custom plugin example.
     * Version: 1.0.0
     * Author: Your Name
     */
     add_action('admin_enqueue_scripts', 'enqueue_bootstrap_in_admin');
      function enqueue_bootstrap_in_admin(){
            wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
            wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], null, true);

            wp_register_style('custom-plugin', plugins_url('style.css', __FILE__));
            wp_enqueue_style('custom-plugin');

            wp_register_script( 'custom-script', plugins_url( 'script.js', __FILE__ ));
            wp_enqueue_script( 'custom-script' );
        }

        function my_custom_plugin_menu() {
            add_menu_page(
                'Users',    // Page title
                'All Users',       // Menu title
                'manage_options',           // Capability
                'my-custom-plugin',         // Menu slug
                'my_custom_plugin_page_content', // Callback function
                'dashicons-admin-generic',  // Icon (optional)
                6                           // Position (optional)
                );
                add_submenu_page(
                'my-custom-plugin',  // Parent Slug (main menu slug)
                'Add Roles',       // Page Title
                'Add Roles',       //Menu Title
                'manage_options',     // Capability
                'my-plugin-submenu',  // Menu Slug
                'my_plugin_submenu_page' // Callback Function
            );
        }
        add_action('admin_menu', 'my_custom_plugin_menu');

   

    //All users menu
     function my_custom_plugin_page_content(){
        // add user Handle form submission
        $error_msg = $fname_error = $lname_error = $email_error = $pass_error = $cpass_error = $role_error = "";
        $f_name = $l_name = $email = $pass = $role = "";

        if (isset($_POST['submit'])){

            $f_name = trim($_POST['fname']);
            $l_name = trim($_POST['lname']);
            $email = trim($_POST['email']);
            $pass = $_POST['pass'];
            $conn_pass = $_POST['conn_pass'];
            $role = $_POST['add_roles'];

            // form Validation
            if(empty($f_name)) {
                $fname_error = "First name cannot be empty";
            }
             elseif (strlen($f_name) > 15){
                $fname_error = "<p style='color:red;'>Role name must not exceed 15 words.</p>";
                }

            if (empty($l_name)) {
                $lname_error = "Last name cannot be empty";
            }

            if (empty($email)) {
                $email_error = "Email cannot be empty";
            } elseif (username_exists($email)) {
                $email_error = "Email is already taken";
            }

            if (empty($pass)) {
                $pass_error = "Password cannot be empty";
            } elseif (strlen($pass) < 8) {
                $pass_error = "Password must be at least 8 characters long";
            }

            if (empty($conn_pass)) {
                $cpass_error = "Confirm password cannot be empty";
            } elseif ($pass !== $conn_pass) {
                $cpass_error = "Password and confirm password do not match";
            }

            if(empty($role)) {
                $role_error = "Please select a role";
            }

            // Only proceed if all errors are empty
            if (empty($fname_error) && empty($lname_error) && empty($email_error) && empty($pass_error) && empty($cpass_error) && empty($role_error)) {

                $user_data = [
                    'user_login'    => $email,
                    'user_email'    => $email,
                    'first_name'    => $f_name,
                    'last_name'     => $l_name,
                    'display_name'  => $f_name . ' ' . $l_name,
                    'user_pass'     => $pass,
                    'role'          => $role
                ];

                $result = wp_insert_user($user_data);

                if (!is_wp_error($result)) {
                    echo "<div class='alert alert-success'>User added successfully!</div>";

                    // Clear form data
                    $f_name = $l_name = $email = $pass = $conn_pass = $role = "";
                } else {
                    $error_msg = "Failed to add user: " . $result->get_error_message();
                }
            } else{
                $error_msg = "Please fix the errors below.";
            }
        }
        // edit user
            $edit_f_name = $edit_l_name = $edit_email = $edit_pass = $edit_role = "";
            $edit_fname_error = $edit_lname_error = $edit_email_error = $edit_pass_error = $edit_role_error = "";
            $edit_modal_error = false;

            if (isset($_POST['update']) && current_user_can('edit_users')){
                $user_id = intval($_POST['user_id']);
                $edit_f_name = trim($_POST['f_name']);
                $edit_l_name = trim($_POST['l_name']);
                $edit_email = trim($_POST['user_email']);
                $edit_pass = $_POST['edit_pass'];
                $edit_role = $_POST['roles'];

                // Validation
                if (empty($edit_f_name)) {
                    $edit_fname_error = "First name cannot be empty";
                    $edit_modal_error = true;
                }

                if (strlen($edit_f_name) > 25){
                    $edit_fname_error = "<p style='color:red;'>Role name must not exceed 25 words.</p>";
                    $edit_modal_error = true;
                }

                if (empty($edit_l_name)) {
                    $edit_lname_error = "Last name cannot be empty";
                    $edit_modal_error = true;
                }

                if (empty($edit_email)) {
                    $edit_email_error = "Email cannot be empty";
                    $edit_modal_error = true;
                }

                if (!empty($edit_pass) && strlen($edit_pass) < 8) {
                    $edit_pass_error = "Password must be at least 8 characters";
                    $edit_modal_error = true;
                }

                if (empty($edit_role)) {
                    $edit_role_error = "Please select a role";
                    $edit_modal_error = true;
                }

                //If validation passed
                if(!$edit_modal_error){
                    update_user_meta($user_id, 'first_name', sanitize_text_field($edit_f_name));
                    update_user_meta($user_id, 'last_name', sanitize_text_field($edit_l_name));

                    $user_data = [
                        'ID' => $user_id,
                        'user_email' => sanitize_email($edit_email),
                        'display_name' => $edit_f_name . ' ' . $edit_l_name
                    ];
                    if (!empty($edit_pass)) {
                        $user_data['user_pass'] = $edit_pass;
                    }
                    wp_update_user($user_data);

                    // Update role
                    $user = new WP_User($user_id);
                    $user->set_role(sanitize_text_field($edit_role));

                    // Success: reload
                    echo "<script>location.reload();</script>";
                    exit;
                }
            }
            ?>
            <div class="wrap">
                    <?php
                // user deletion
                if(isset($_POST['delete_user']) && current_user_can('delete_users')) {
                    $del_user = intval($_POST['user_id']);
                    if (get_userdata($del_user)) {
                        wp_delete_user($del_user);
                    }
                }
                // Get current page and search term
                $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
                $users_per_page = 10;
                $offset = ($paged - 1) * $users_per_page;
                $search_term = sanitize_text_field($_GET['user_search'] ?? '');

                // Build user query arguments
                $args = [
                    'orderby' => 'display_name',
                    'order' => 'ASC',
                    'number' => $users_per_page,
                    'offset' => $offset
                ];

                if (!empty($search_term)){
                    $args['search'] = '*' . esc_attr($search_term) . '*';
                    $args['search_columns'] = ['user_login', 'user_email', 'display_name'];
                }

                //search user role
                $selected_role = sanitize_text_field($_GET['user_role'] ?? '');
                 if (!empty($selected_role)) {
                    $args['role'] = $selected_role;
                }

                // Fetch users for the current page
                $blogusers = get_users($args);

                // Get total user count (respecting search)
                if(!empty($search_term)  || !empty($selected_role)){
                    $count_args = $args;
                    unset($count_args['number'], $count_args['offset']);
                    $total_users = count(get_users($count_args));
                } else {
                    $total_users = count_users()['total_users'];
                }

                $total_pages = ceil($total_users / $users_per_page);

                ?>
                <!-- Top Header and Search Form -->
                <div class="top-header d-flex justify-content-between">
                    <div class="user d-flex">
                        <h1 class="text-black fw-bold me-4">
                            <?php echo esc_html(get_admin_page_title()); ?>
                        </h1>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Add User
                        </button>
                    </div>
            </div>

             <div class="top-header d-flex justify-content-between my-4">
                <div class="d-flex">

                 <!-- Bulk Action Dropdown -->
                  

                    <!-- user role search -->
                  <form method="get" action=""  class="" >
                    <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page'] ?? ''); ?>">
                    <select name="user_role" id="user_role" style="width:233px;">
                        <option value="">All Roles</option>
                        <?php
                        global $wp_roles;
                        $current_role = $_GET['user_role'] ?? '';
                        foreach($wp_roles->roles as $role_key => $role){
                            echo '<option value="' . esc_attr($role_key) . '" ' . selected($current_role, $role_key, false) . '>' . esc_html($role['name']) . '</option>';
                        }
                        ?>
                    </select>
                    <input type="submit" value="Filter"  class="btn btn-primary btn-sm">
                </form>

                    </div>
                   
                 <!-- search all user-->
                    <form method="get" action="<?php //echo esc_url(admin_url('admin.php')); ?>">
                        <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page'] ?? ''); ?>">
                        <input type="text" name="user_search" id="user-search" placeholder="Search users..." value="<?php echo esc_attr($_GET['user_search'] ?? ''); ?>">
                        <input type="submit" value="Search" class="btn btn-primary btn-sm">
                    </form>
                    </div>
                    
                     
                    <!-- Users Table -->
                    <form method="post"  id="bulk_action_form">
                  <table class="user-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($blogusers)) : ?>
                            <?php foreach ($blogusers as $user): ?>
                                <tr>
                                    <td><?php echo esc_html($user->user_nicename); ?></td>
                                    <td style="text-transform: capitalize;"><?php echo esc_html($user->display_name); ?></td>
                                    <td><?php echo esc_html($user->user_email); ?></td>
                                    <td style="text-transform: capitalize;"><?php echo implode(', ', $user->roles); ?></td>
                                    <td>
                                        
                                            <input type="hidden" name="user_id" value="<?php echo esc_attr($user->ID); ?>">
                                            <button type="submit" name="delete_user" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                            <button type="button" class="edit-btn btn btn-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#staticBackdrop"
                                                data-user-id="<?php echo esc_attr($user->ID); ?>"
                                                data-first-name="<?php echo esc_attr(get_user_meta($user->ID, 'first_name', true)); ?>"
                                                data-last-name="<?php echo esc_attr(get_user_meta($user->ID, 'last_name', true)); ?>"
                                                data-email="<?php echo esc_attr($user->user_email); ?>"
                                                data-role="<?php echo esc_attr(implode(', ', $user->roles)); ?>">
                                                Edit
                                            </button>
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="5">No users found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                        </form>

                         <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <div class="pagination m-4" style="float:right;">
                                <?php
                                $base_url = remove_query_arg(['paged'], $_SERVER['REQUEST_URI']);
                                if (!empty($search_term)) {
                                    $base_url = add_query_arg('user_search', urlencode($search_term), $base_url);
                                }
                                $base_url = add_query_arg('page', $_GET['page'] ?? '', $base_url);

                                // Previous
                                if ($paged > 1) {
                                    echo '<a href="' . esc_url(add_query_arg('paged', $paged - 1, $base_url)) . '" class="btn btn-secondary">Previous</a> ';
                                }

                                // Numbered
                                for ($i = 1; $i <= $total_pages; $i++) {
                                    echo '<a href="' . esc_url(add_query_arg('paged', $i, $base_url)) . '" class="btn ' . ($paged == $i ? 'btn-primary' : 'btn-light') . '">' . $i . '</a> ';
                                }

                                // Next
                                if ($paged < $total_pages) {
                                    echo '<a href="' . esc_url(add_query_arg('paged', $paged + 1, $base_url)) . '" class="btn btn-secondary">Next</a>';
                                }
                                ?>
                            </div>
                        <?php endif; 

                    
                        ?>    


                     <!-- Add User Modal -->
                    <div class="modal fade <?php echo (!empty($error_msg)) ? 'modal-error' : ''; ?>" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Add User</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form method="post">
                            <div class="modal-body">

                            <!-- Error Message -->
                            <?php if (!empty($error_msg)): ?>
                                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
                            <?php endif; ?>

                            <!-- First Name -->
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="fname" class="form-control" value="<?php echo htmlspecialchars($f_name); ?>">
                                <span class="text-danger"><?php echo $fname_error; ?></span>
                            </div>

                            <!-- Last Name -->
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="lname" class="form-control" value="<?php echo htmlspecialchars($l_name); ?>">
                                <span class="text-danger"><?php echo $lname_error; ?></span>
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label class="form-label">Email address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
                                <span class="text-danger"><?php echo $email_error; ?></span>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="pass" class="form-control">
                                <span class="text-danger"><?php echo $pass_error; ?></span>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="conn_pass" class="form-control">
                                <span class="text-danger"><?php echo $cpass_error; ?></span>
                            </div>

                            <!-- Role -->
                            <div class="mb-3">
                                <label class="form-label">Select Role</label>
                                <select name="add_roles" class="form-select">
                                <option value="">-- Select Role --</option>
                                <?php
                                $roles = wp_roles()->roles;
                                foreach ($roles as $role_key => $role_data) {
                                    $selected = ($role === $role_key) ? 'selected' : '';
                                    echo '<option value="' . esc_attr($role_key) . '" ' . $selected . '>' . esc_html($role_data['name']) . '</option>';
                                }
                                ?>
                                </select>
                                <span class="text-danger"><?php echo $role_error; ?></span>
                            </div>

                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-modal">Close</button>
                            <button type="submit" name="submit" class="btn btn-primary">Add User</button>
                            </div>
                        </form>
                        </div>
                    </div>
                    </div>

                    <!--edit user Form Modal -->
                    <div class="modal fade <?php echo ($edit_modal_error ? 'modal-error' : ''); ?>" id="staticBackdrop" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Update User</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <form method="post">
                                <div class="modal-body">

                                <input type="hidden" name="user_id" id="edit_user_id">

                                <div class="mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="f_name" class="form-control" value="<?php echo htmlspecialchars($edit_f_name); ?>">
                                    <span class="text-danger"><?php echo $edit_fname_error; ?></span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="l_name" class="form-control" value="<?php echo htmlspecialchars($edit_l_name); ?>">
                                    <span class="text-danger"><?php echo $edit_lname_error; ?></span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email address</label>
                                    <input type="email" name="user_email" class="form-control" value="<?php echo htmlspecialchars($edit_email); ?>">
                                    <span class="text-danger"><?php echo $edit_email_error; ?></span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password <small>(Leave blank to keep current)</small></label>
                                    <input type="password" name="edit_pass" class="form-control">
                                    <span class="text-danger"><?php echo $edit_pass_error; ?></span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Select Role</label>
                                    <select name="roles" class="form-select" id="edit_role_select">
                                    <option value="">-- Select Role --</option>
                                    <?php
                                    $roles = wp_roles()->roles;
                                    foreach ($roles as $role_key => $role_data) {
                                        $selected = ($edit_role === $role_key) ? 'selected' : '';
                                        echo '<option value="' . esc_attr($role_key) . '" ' . $selected . '>' . esc_html($role_data['name']) . '</option>';
                                    }
                                    ?>
                                    </select>
                                    <span class="text-danger"><?php echo $edit_role_error; ?></span>
                                </div>

                                </div>

                                <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="update" class="btn btn-success">Update User</button>
                                </div>
                            </form>

                            </div>
                        </div>
                    </div>
               </div>
        <?php
        ?>
       <?php

    }


    //add roles Submenu callback function 
    function my_plugin_submenu_page(){

        //add user role
        $err_msg = "";
        $role_name = "";
        if(isset($_POST['submit_custom_role']) && check_admin_referer('create_custom_role_action', 'custom_role_nonce')) {
            if (empty($_POST['custom_role_name'])) {
                $err_msg = "<p style='color:red;'>Role name is required.</p>";
            } else {
                $role_name = sanitize_text_field($_POST['custom_role_name']);

                if (strlen($role_name) > 15) {
                    $err_msg = "<p style='color:red;'>Role name must not exceed 15 character.</p>";
                }else {
                    $role_slug = sanitize_key(strtolower(str_replace(' ', '_', $role_name)));

                    // Check for duplicate role
                    if (get_role($role_slug)){
                        $err_msg = "<p style='color:red;'>This role already exists.</p>";
                    }
                }
            }
            if (empty($err_msg)){
                // Get selected capabilities
                $capabilities = [];
                if (!empty($_POST['capabilities']) && is_array($_POST['capabilities'])){
                    foreach($_POST['capabilities'] as $cap) {
                        $capabilities[sanitize_text_field($cap)] = true;
                    }
                }
                if (empty($capabilities)){
                    $capabilities['read'] = true;
                }
                // Create role
                add_role($role_slug, $role_name, $capabilities);

                echo '<div class="notice notice-success"><p>Role created successfully.</p></div>';
                // Optional: clear form input
                $role_name = "";
            }
        }

       //delete role
        if(
        isset($_POST['delete_custom_role']) &&
        isset($_POST['delete_role_slug']) &&
        check_admin_referer('delete_role_action', 'delete_role_nonce_' . sanitize_key($_POST['delete_role_slug']))
       )
       {
            $role_slug = sanitize_key($_POST['delete_role_slug']);
            if(get_role($role_slug)){
                remove_role($role_slug);
                echo '<div class="notice notice-success"><p>Role deleted successfully.</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Role not found.</p></div>';
            }
       }
       // edit user roles
        $edit_role_msg = '';
        $role_slug = '';
        $role_name = '';
        $role_caps = [];

        if (isset($_POST['update_custom_role'])) {
            $role_slug = sanitize_text_field($_POST['roles_slug']);
            $role_name = sanitize_text_field($_POST['roles_name']);
            $submitted_caps = $_POST['custom_capabilities'] ?? [];

            global $wp_roles;
            $current_caps = $wp_roles->roles[$role_slug]['capabilities'] ?? [];

            // Validate inputs
            if (empty($role_name)) {
                $edit_role_msg = "Role name cannot be empty.";
            } elseif(strlen($role_name) > 15) {
                $edit_role_msg = "Role name must be under 15 characters.";
            }

            if (empty($edit_role_msg)) {
                remove_role($role_slug);
                add_role($role_slug, $role_name, array_fill_keys($submitted_caps, true));
                echo '<div class="notice notice-success"><p>Role updated successfully.</p></div>';
                echo '<script>location.reload();</script>';
                exit;
            }

            $role_caps = $submitted_caps;
        }
        ?>
        <div class="wrap">
           <h3 class="text-black"><?php echo esc_html(get_admin_page_title()); ?></h3>
           <!-- add user role -->
           <form method="post">
                <?php wp_nonce_field('create_custom_role_action', 'custom_role_nonce'); ?>
                <table class="form-table">
                <tr>
                    <th><label for="custom_role_name">Role Name</label></th>
                    <td>
                        <input type="text" name="custom_role_name" id="custom_role_name">
                        <?php if (!empty($err_msg)) echo $err_msg; ?>
                    </td>
                </tr>
                <tr>
                    <th>Capabilities</th>
                    <td>
                        <label><input type="checkbox" name="capabilities[]" value="read" checked> Read</label><br>
                        <label><input type="checkbox" name="capabilities[]" value="edit_posts"> Edit Posts</label><br>
                        <label><input type="checkbox" name="capabilities[]" value="delete_posts"> Delete Posts</label><br>
                        <label><input type="checkbox" name="capabilities[]" value="upload_files"> Upload Files</label><br>
                        <label><input type="checkbox" name="capabilities[]" value="publish_posts"> Publish Posts</label><br>
                        <label><input type="checkbox" name="capabilities[]" value="edit_pages"> Edit Pages</label><br>
                        <label><input type="checkbox" name="capabilities[]" value="manage_options"> Manage Options</label><br>
                    </td>
                </tr>
                </table>
                <p>
                <input type="submit" name="submit_custom_role" class="btn btn-primary text-light" value="Create Role">
                </p>
           </form>

             <!--  List All Roles -->
             <h3 class="mt-4">All Registered Role</h3>
             <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>User role</th>
                        <th>Capabilities</th>
                        <th></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php         
                    global $wp_roles; $all_caps = ['read', 'edit_posts', 'delete_posts', 'upload_files', 'publish_posts', 'edit_pages', 'manage_options'];
                    foreach ( $wp_roles->roles as $slug => $role) {
                        echo '<tr>';
                        echo '<td>' . esc_html($role['name']).'</td>';

                      $caps = array_filter($role['capabilities'], function($value) {
                        return $value === true;
                      });

                    //Join capability keys with commas for display
                    $cap_names = implode(', ',array_keys($caps));
                    $data_caps = array_keys(array_filter($role['capabilities'], fn($v) => $v === true));

                    echo '<td style="max-width:300px; word-wrap:break-word;">' . substr_replace($cap_names, "...", 90)
                     . '</td>';
                    echo '<td></td>';
                    echo '<td>

                            <button 
                                type="button" 
                                class=" role-btn action-buttons btn btn-success text-light" 
                                data-role-name="' .esc_attr($role['name']) . '" 
                                data-role-slug="' .esc_attr($slug) . '" 
                                data-role-caps=\'' . json_encode($data_caps) . '\'
                                data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                Edit
                             </button> 
                                <!-- Delete Button Form -->
                                <form method="post" style="display:inline-block; margin-left: 10px;" onsubmit="return confirm(\'Are you sure you want to delete this role?\')">
                                ' .wp_nonce_field('delete_role_action', 'delete_role_nonce_' . $slug, true, false) . '
                                <input type="hidden" name="delete_role_slug" value="' . esc_attr($slug) . '">
                                <input type="submit" name="delete_custom_role" class="action-buttons role-btn btn btn-danger text-light" value="Delete">
                            </form>
                    </td>';
                    echo '</tr>';
                    }
                     $all_caps = ['read', 'edit_posts', 'delete_posts', 'upload_files', 'publish_posts', 'edit_pages', 'manage_options'];
                    ?>
                </tbody>
             </table>

            <!-- Edit Role Modal -->
            <div class="modal fade <?php echo ($edit_role_msg ? 'modal-error' : ''); ?>" id="exampleModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

              <div class="modal-body">
                    <input type="hidden" name="roles_slug" id="custom_role_slug">
                    <div class="mb-3">
                        <label><strong>Role Name</strong></label>
                        <input type="text" name="roles_name" id="roles_name" class="form-control" >
                        <span id="name-error" style="color: red;">
                        <?php if(!empty($edit_role_msg)) echo esc_html($edit_role_msg); ?>
                        </span>
                    </div>

                  <div class="mb-3">
                    <label><strong>Capabilities:</strong></label><br>
                        <?php foreach ($all_caps as $cap): ?>
                        <label>
                            <input type="checkbox"
                                class="cap-checkbox"
                                name="custom_capabilities[]"
                                value="<?php echo esc_attr($cap); ?>"
                                <?php echo (in_array($cap, $role_caps) ? 'checked' : ''); ?>>
                            <?php echo esc_html($cap);?>
                        </label><br>
                        <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <input type="submit" name="update_custom_role" class="btn btn-primary" value="Save">
            </div>
        </form>
        </div>
    </div>
    </div>
        </div>
        <?php   
    }
    ?>
<?php










    










