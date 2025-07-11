<?php
/* Template Name: create-an-account */
get_header();
if( is_user_logged_in()) {
         wp_redirect( home_url('/' ) ); // send to homepage
 }
   
?>
 
<?php
$field_errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sanitize input
    $username   = sanitize_user($_POST['username']);
    $email      = sanitize_email($_POST['email']);
    $password   = $_POST['password'];
    $first_name = sanitize_text_field($_POST['fname']);
    $last_name  = sanitize_text_field($_POST['lname']);

    // Validate fields
    if (empty($first_name)) {
        $field_errors['fname'] = "First name is required.";
    }

    if (empty($last_name)) {
        $field_errors['lname'] = "Last name is required.";
    }

    if (empty($username)) {
        $field_errors['username'] = "Username is required.";
    } elseif (username_exists($username)) {
        $field_errors['username'] = "Username already exists.";
    }

    if (empty($email)) {
        $field_errors['email'] = "Email is required.";
    } elseif (!is_email($email)) {
        $field_errors['email'] = "Invalid email format.";
    } elseif (email_exists($email)) {
        $field_errors['email'] = "Email already exists.";
    }

    if (empty($password)) {
        $field_errors['password'] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $field_errors['password'] = "Password must be at least 6 characters.";
    }

    // If no errors, insert the user
    if (empty($field_errors)) {
        $user_id = wp_insert_user([
            'user_login'   => $username,
            'user_pass'    => $password,
            'user_email'   => $email,
            'first_name'   => $first_name,
            'last_name'    => $last_name,
            'display_name' => $first_name . ' ' . $last_name,
            'role'         => 'subscriber'
        ]);

        if (is_wp_error($user_id)) {
            $field_errors['general'] = $user_id->get_error_message();
        } else {
            $success = "Account created successfully!";
        }
    }
}
?>

<div class="container mb-4">
  <div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-sm-10">
      <form class="card shadow p-4 my-5 bg-body-tertiary rounded" method="post">
        <h2 class="text-danger mb-4 text-center">Sign In</h2>

        <!-- Name Inputs -->
        <div class="row mb-3">

        <div class="col-md-6 mb-2 mb-md-0">
          <input type="text" class="form-control" placeholder="First name" name="fname" value="<?php echo esc_attr($_POST['fname'] ?? ''); ?>">
          <?php if (!empty($field_errors['fname'])): ?>
          <div class="text-danger"><?php echo esc_html($field_errors['fname']); ?></div>
          <?php endif; ?>
          </div>


          <div class="col-md-6">
      <input type="text" class="form-control" placeholder="Last name" name="lname" value="<?php echo esc_attr($_POST['lname'] ?? ''); ?>">
      <?php if (!empty($field_errors['lname'])): ?>
        <div class="text-danger"><?php echo esc_html($field_errors['lname']); ?></div>
      <?php endif; ?>
    </div>
        </div>

        <!-- Username & Email -->
        <div class="row mb-3">
        <div class="col-md-6 mb-2 mb-md-0">
      <input type="text" class="form-control" placeholder="Username" name="username" value="<?php echo esc_attr($_POST['username'] ?? ''); ?>">
      <?php if (!empty($field_errors['username'])): ?>
        <div class="text-danger"><?php echo esc_html($field_errors['username']); ?></div>
      <?php endif; ?>
    </div>
    
          <div class="col-md-6">
      <input type="email" class="form-control" placeholder="Email" name="email" value="<?php echo esc_attr($_POST['email'] ?? ''); ?>">
      <?php if (!empty($field_errors['email'])): ?>
        <div class="text-danger"><?php echo esc_html($field_errors['email']); ?></div>
      <?php endif; ?>
    </div>

          

        <!-- Password -->
        <div class="mb-3">
          <label for="exampleInputPassword1" class="form-label"></label>
       
      <input type="password" class="form-control" placeholder="Password" name="password">
       <?php if (!empty($field_errors['password'])): ?>
      <div class="text-danger"><?php echo esc_html($field_errors['password']); ?></div>
      <?php endif; ?>
    
        </div>

        <!-- Checkbox -->
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" id="exampleCheck1">
          <label class="form-check-label" for="exampleCheck1">Check me out</label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-danger w-100" name="submit">Sign In</button>
      </form>
    </div>
  </div>
</div>



 <div class="text-danger"><?php echo esc_html($field_errors['email']); ?></div>