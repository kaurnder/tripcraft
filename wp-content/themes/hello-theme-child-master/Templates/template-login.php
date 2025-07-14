<?php
// /* Template Name: Login */
// if( is_user_logged_in()) {
//     wp_redirect( home_url('/' ) ); // send to homepage
// }
get_header();
if( is_user_logged_in()) {
	wp_redirect( home_url('/') ); // send to homepage
}
?>
<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
    
    <div class="container my-4">
      <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-8">
          <div class="shadow p-4 mb-5 bg-body-tertiary rounded">
            <h2 class="text-danger mb-4 text-center">Login</h2>
            
            <?php wp_login_form(); ?>

            <div class="login-page-col text-center mt-3">
              <div class="login-page-forgot">
                <p><?php _e( "Don't have an account yet?", 'teamed' ); ?></p>
                <a href="<?php echo home_url( '/create-an-account/' ); ?>" class="text-danger">
                  <?php _e( 'Create an Account', 'teamed' ); ?>
                </a><br><br>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php endwhile; ?>
<?php endif; ?>


<?php
get_footer();
?>