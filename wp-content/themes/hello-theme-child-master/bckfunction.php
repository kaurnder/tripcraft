<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0' );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );



add_action( 'wp_enqueue_scripts', 'astra_child_style' );
function astra_child_style() {
        
    $isUserLogin = is_user_logged_in() ? 'true' : 'false';
    // $vall ="hello";
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css',array('parent-style') );
	wp_enqueue_style( 'style', get_stylesheet_directory_uri(). '/css/style.css?' . strtotime('now') );
	wp_enqueue_style( 'bootstrap', get_stylesheet_directory_uri(). '/css/Bootstrap.min.css?' . strtotime('now') );

    // ajax and jquery included
    // wp_enqueue_script('jquery');
    // wp_enqueue_script('globel', get_stylesheet_directory_uri() . '/js/globel.js?' . strtotime('now'));
    // wp_localize_script('globel', 'ajaxurl', admin_url('admin-ajax.php'));

    wp_enqueue_script('globel-js', get_stylesheet_directory_uri() . '/js/script.js?' . strtotime('now') );
    wp_enqueue_script('bootstrap', get_stylesheet_directory_uri() . '/js/Bootstrap.js?' . strtotime('now') );
    wp_localize_script('globel-js', 'ajax_object', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'isUserLogin' =>  $isUserLogin,
        'val '=> $vall,
        ]
    );
    

    wp_enqueue_script('jquery');
    wp_localize_script('custom-ajax-script', 'AjaxSearch', array(
        'ajax_url' => admin_url('admin-ajax.php')));
  
}


//login logout
add_filter('body_class', 'add_logged_in_class');
function add_logged_in_class($classes) {
    if (is_user_logged_in()) {
        $classes[] = 'logged-in';
    } else {
        $classes[] = 'not-logged-in';
    }
    return $classes;
}

//logout
function custom_ajax_logout(){
    if (isset($_GET['action']) && $_GET['action'] === 'logout'){
        wp_logout();
        wp_redirect(home_url()); // Redirect to homepage after logout
        exit();
    }
} 
add_action('init', 'custom_ajax_logout');

//craete custom post type
add_action('init', 'register_tour_package_post_type');
function register_tour_package_post_type() {
    register_post_type('tour_package', array(
        'label' => 'Tour Packages',
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'tour-packages'),
        'supports'     => array('title', 'thumbnail'),
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-palmtree',
    ));
}


//craete custom post type
add_action('init', 'register_popular_destination_post_type');
function  register_popular_destination_post_type() {
    register_post_type('populardestination', array(
        'label' => 'Popular Destination',
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'popular_destination'),
        'supports'     => array('title', 'thumbnail'),
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-location',
    ));
}

//display tour packages custom post-type as shortcode 
function display_tour_cards_shortcode($atts) {
    
    $atts = shortcode_atts(array(
        'limit' => -1, // -1 means show all posts
    ), $atts, 'tour_package');

      // Query arguments
      $args = array(
        'post_type'      => 'tour_package',
        'posts_per_page' => intval($atts['limit']),
    );

    $query = new WP_Query($args);
    ob_start();

   
    if ($query->have_posts()) :
        echo '<div class="tour-card-grid">';
        while ($query->have_posts()) : $query->the_post();

            // ACF Fields
            $description_acf = get_field('tour_desciption'); // ACF field
            $description = wp_trim_words( $description_acf, 20, '...');
            $title_acf = the_title();
            $title = wp_trim_words( $title_acf, 5, '...');
            $duration = get_field('tour_duration');       // ACF field
            ?>
            <div class="tour-card">
                <h3 class="tour-title">  <?php echo wp_trim_words(get_the_title(), 4, '...'); ?></h3>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="tour-image">
                        <?php the_post_thumbnail('small'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($description) : ?>
                    <p class="tour-desc"><?php echo esc_html($description); ?></p>
                <?php endif; ?>

                <div class="tour-bottom">
                    <?php if ($duration) : ?>
                        <span class="tour-duration"><?php echo esc_html($duration); ?></span>
                    <?php endif; ?>
                    <a href="<?php the_permalink(); ?>" class="view-more-btn">View More</a>
                </div>
            </div>

                   
            
            <?php

        endwhile;
        echo '</div>';
        ?>
         <!-- Custom Arrows -->
         <div class="custom-arrows">
         <button class="slick-prev"><i class="fa-solid fa-left"></i></button>
         <button class="slick-next"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
        <?php


        wp_reset_postdata();
    else :
        echo '<p>No tour packages found.</p>';
    endif;

    return ob_get_clean();
}
add_shortcode('tour_cards', 'display_tour_cards_shortcode');


// use slick slider tour posts


// Popular destination shortcode with 'limit' argument
function popular_destination_cards_shortcode($atts) {
    ob_start();

    // Allow 'limit' as shortcode argument (default: -1 means all)
    $atts = shortcode_atts(array(
        'limit' => -1,
    ), $atts);

    $args = array(
        'post_type' => 'populardestination',
        'posts_per_page' => intval($atts['limit']),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        echo '<div class="destination-card-grid">';
        while ($query->have_posts()) {
            $query->the_post();

            $description_acf = get_field('description');
            $description = wp_trim_words( $description_acf, 5, '...');
            $rating = get_field('review');

     
            echo '<div class="destination-card">';
            
            echo '<a href="' . get_permalink() . '" class="card-link" style="text-decoration: none; color: inherit; display: block;">';

            // Featured image
            if (has_post_thumbnail()) {
                echo '<div class="card-image">' . get_the_post_thumbnail(get_the_ID(), 'medium') . '</div>';
            }

            // Title
            echo '<h3 class="card-title">' . get_the_title() . '</h3>';

            // ACF description
            if ($description) {
                echo '<p class="card-desc">' . esc_html($description) . '</p>';
            }

            // Review section
            echo '<div class="card-review">';
            echo '<span class="star filled">&#9733;</span>'; // one yellow star
            if ($rating) {
                echo '<span class="rating-num">4.9</span>';
                echo '<span class="rating-text">' . esc_html($rating) . '</span>';
            }
            echo '</a>';
            echo '</div>';

            echo '</div>'; 
            // end .destination-card
        }
        echo '</div>'; // end .destination-card-grid
        ?>
        <!-- Custom Arrows -->
        <div class="custom-arrows">
        <button class="slick-prev left-arrow"><i class="fa-solid fa-left"></i></button>
        <button class="slick-next right-arrow"><i class="fa-solid fa-arrow-right"></i></button>
       </div>
       <?php
        wp_reset_postdata();
    }

    return ob_get_clean();
}
add_shortcode('popular_destinations', 'popular_destination_cards_shortcode');




//testimonial
function astra_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
}
add_action('wp_enqueue_scripts', 'astra_child_enqueue_styles');

//slick slider
function enqueue_slick_slider_assets() {
    wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');

    wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), null, true);

    wp_add_inline_script('slick-js', "
        jQuery(document).ready(function($) {
            // Slider 1: Testimonials
            $('.testimonial-grid').slick({
                dots: true,
                autoplay: false,
                autoplaySpeed: 1000,
                slidesToShow: 2,
                arrows: false,
                slidesToScroll: 1,
                responsive: [
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });

            $('.tour-card-grid').slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                arrows: true,
                dots: false,
                prevArrow: $('.slick-prev'),
                nextArrow: $('.slick-next'),
                infinite: true,
                
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: { slidesToShow: 2 }
                    },
                    {
                        breakpoint: 768,
                        settings: { slidesToShow: 1 }
                    }
                ]
            });

        

         $('.destination-card-grid').slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                arrows: true,
                dots:false,
                prevArrow: $('.left-arrow'),
                nextArrow: $('.right-arrow'),
                infinite: true,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: { slidesToShow: 2 }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                         dots: false,
                         arrows: true, 
                        slidesToShow: 1 }
                    }
                ]
            });
        });

    ");
}
add_action('wp_enqueue_scripts', 'enqueue_slick_slider_assets');


function testimonial_grid_shortcode() {
    ob_start();
  
    $args = array(
        'post_type' => 'testimonial',
        'posts_per_page' => -1
    );

    $query = new WP_Query($args);
    

    if ($query->have_posts()) {
        echo '<div class="testimonial-grid">';
        while ($query->have_posts()){
            $query->the_post();
            $desc = get_the_content();
            $name = get_the_title();
            $post_id = get_the_id();
            $location = get_post_meta($post_id, 'tss_location', true);
            $date = get_the_date('F Y');
            
 // Change this if your plugin uses a different field

            echo '<div class="testimonial-card">';
                echo '<p class="testimonial-desc">"' . esc_html($desc) . '"</p>';

                echo '<div class="testimonial-footer">';
                    if (has_post_thumbnail()) {
                        echo '<div class="testimonial-image">' . get_the_post_thumbnail(get_the_ID(), 'thumbnail') . '</div>';
                    }

                    echo '<div class="testimonial-info-date-wrap">';
                    echo '<div class="testimonial-info">';
                        echo '<div class="testimonial-name">' . esc_html($name) . '</div>';
                        if ($location) {
                            echo '<div class="testimonial-location">' . esc_html($location) . '</div>';
                        }
                    echo '</div>';
                    echo '<div class="testimonial-date"> ' . esc_html($date) . '– Family</div>';
                echo '</div>';
                
                echo '</div>';
            echo '</div>';
        }
        echo '</div>'; // end .testimonial-grid
        wp_reset_postdata();
    }
    return ob_get_clean();
}
add_shortcode('testimonial_grid', 'testimonial_grid_shortcode');

//create blog custom post 
add_action('init', 'register_Blog_post_type');
function  register_Blog_post_type() {
    register_post_type('blog', array(
        'label' => 'Blog',
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'blog'),
        'supports'     => array('title', 'thumbnail'),
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-location',
    ));
}




function custom_recent_posts_card() {
    ob_start(); // Start output buffering to capture HTML
    ?>
    <div class="custom-card">
        <!-- Card Heading -->
        <h2 class="card-heading">Search Here</h2>
        
        <!-- Search Box -->
        <div class="search-box">
        <input type="text" placeholder="Search..." class="search-input">
        <i class="fas fa-search search-icon-right"></i>
        </div>
        
        <!-- Divider -->
        <hr class="divider">
        
        <!-- Recent Posts Heading -->
        <h3 class="recent-posts-heading">Recent Posts</h3>

        <!-- Loop for Recent Posts (PHP) -->
        <div class="recent-posts">
            <?php
            $args = array(
                'post_type' => 'blog', // Adjust the post type if needed
                'posts_per_page' => 5,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',  // Limit to 5 posts
            );
            $recent_posts = new WP_Query($args);
            if ($recent_posts->have_posts()) :
                while ($recent_posts->have_posts()) : $recent_posts->the_post();
            ?>
                <div class="post-item">
                    <div class="post-image">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php the_post_thumbnail_url('thumbnail'); ?>" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="post-info">
                        <h4 class="post-title"><?php the_title(); ?></h4>
                        <div class="post-meta">
                        <i class="fas fa-calendar-alt icon"></i>
                        <?php
                            $date_raw = get_field('tour_date');
                            if ($date_raw) {
                                echo get_the_date('F j, Y');
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>
  <!-- Divider -->
  <hr class="divider" style="margin-top:19px;">
        <?php echo '<div class="quote-form">';
                 echo do_shortcode('[contact-form-7 id="88828f2" title="blog form"]'); // Replace with your actual form ID
                 echo '</div>';
?>
    </div>
    <?php
    return ob_get_clean();// Return the buffered content
}
add_shortcode('recent_posts_card', 'custom_recent_posts_card');


//contact form 7 shortcode 
function custom_contact_form_shortcode() {
     echo '<div class="quote-form">';
    echo do_shortcode('[contact-form-7 id="88828f2" title="blog form"]');// Replace with your actual form ID
    echo '</div>';
}
add_shortcode('conatct_form', 'custom_contact_form_shortcode');

//sidebar recently 5 post of tour 
function custom_recent_posts_card_tour() {
    ob_start(); // Start output buffering to capture HTML
    ?>
    <div class="custom-card-tour-post">
        <!-- Card Heading -->
        <h2 class="card-heading">Search Here</h2>
        
        <!-- Search Box -->
        <div class="search-box">
            <input type="text" placeholder="Search..." class="search-input" id="ajax-post-tour-search">
      
            <i class="fas fa-search search-icon-right"></i>
        </div>
        
        <!-- Divider -->
        <hr class="divider">
        
        <!-- Recent Posts Heading -->
        <h3 class="recent-posts-heading">Recent Posts</h3>

        <!-- Loop for Recent Posts (PHP) -->
         <div class="recent-posts">
          <div id="msg"></div> 
          <div id="ajax-post-tour-results">
            <?php
            $args = array(
                'post_type' => 'tour_package', // Adjust the post type if needed
                'posts_per_page' => 5,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',  // Limit to 5 posts
            );
            $recent_posts = new WP_Query($args);
            if ($recent_posts->have_posts()) :
                while ($recent_posts->have_posts()) : $recent_posts->the_post();
            ?>
                <div class="post-item">
                    <div class="post-image">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php the_post_thumbnail_url('thumbnail'); ?>" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="post-info" style="width: 147%;">
                    <a href="<?php the_permalink(); ?>" style="display:block; text-decoration:none; color:inherit;">
                    <h4 class="post-title"><?php the_title(); ?></h4></a>
                        <div class="post-meta">
                        <i class="fas fa-calendar-alt icon"></i>
                        <?php
                            $date_raw = get_field('tour_duration');
                            if ($date_raw) {
                               echo get_the_date('F j, Y');
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
           </div>
        </div>
        
  <!-- Divider -->
  <hr class="divider" style="margin-top:19px;">
        <?php echo '<div class="quote-form">';
                 echo do_shortcode('[contact-form-7 id="88828f2" title="blog form"]'); // Replace with your actual form ID
                 echo '</div>';
?>
    </div>
    <?php
    return ob_get_clean();// Return the buffered content
}
add_shortcode('recent_posts_card_tour', 'custom_recent_posts_card_tour');


//sidebar recently 5 post of tour 
function custom_recent_posts_card_destination() {
    ob_start(); // Start output buffering to capture HTML
    ?>
    <div class="custom-card-tour-post">
        <!-- Card Heading -->
        <h2 class="card-heading">Search Here</h2>
        
        <!-- Search Box -->
        <div class="search-box">
        <input type="text" placeholder="Search..." class="search-input" id="ajax-post-destination-search">

        <i class="fas fa-search search-icon-right"></i>
        </div>
        
        <!-- Divider -->
        <hr class="divider">
        
        <!-- Recent Posts Heading -->
        <h3 class="recent-posts-heading">Recent Posts</h3>

        <!-- Loop for Recent Posts (PHP) -->
          <div id="message"></div>
           <div class="recent-posts" >
            <div id="ajax-post-destination-result">
            <?php
            $args = array(
                'post_type' => 'populardestination', // Adjust the post type if needed
                'posts_per_page' => 5,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',  // Limit to 5 posts
            );
            $recent_posts = new WP_Query($args);
            if ($recent_posts->have_posts()) :
                while ($recent_posts->have_posts()) : $recent_posts->the_post();
            ?>
                <div class="post-item">
                    <div class="post-image postimg">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php the_post_thumbnail_url('thumbnail'); ?>" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="post-info info">
                    <a href="<?php the_permalink(); ?>" style="display:block; text-decoration:none; color:inherit;">
                    <h4 class="post-title"><?php the_title(); ?></h4></a>
                        <div class="post-meta">
                        <i class="fas fa-calendar-alt icon"></i>
                        <?php
                            $date_raw = get_field('description');
                            if ($date_raw) {
                                echo get_the_date();
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>
        </div>
        
  <!-- Divider -->
  <hr class="divider" style="margin-top:19px;">
        <?php echo '<div class="quote-form">';
                 echo do_shortcode('[contact-form-7 id="88828f2" title="blog form"]'); // Replace with your actual form ID
                 echo '</div>';
?>
    </div>
    <?php
    return ob_get_clean();// Return the buffered content
}
add_shortcode('recent_posts_card_destination', 'custom_recent_posts_card_destination');





//blog serch functionality blog page
function custom_recent_posts_card_demo() {
    ob_start(); ?>
    <div class="custom-card">
        <h2 class="card-heading">Search Here</h2>

        <div class="search-box">
            <input type="text" placeholder="Search..." class="search-input" id="ajax-post-search">
      
            <i class="fas fa-search search-icon-right"></i>
        </div>

        <hr class="divider">

        <h3 class="recent-posts-`heading">Recent Posts</h3>
        
        <div class="recent-posts" >
           <div id="msg"></div>
           <div id="ajax-post-results">
            <?php
            $args = array(
                'post_type' => 'blog',
                'posts_per_page' => 5,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC',
            );
            $recent_posts = new WP_Query($args);
            if ($recent_posts->have_posts()) :
                while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                    <div class="post-item">
                        <div class="post-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php the_post_thumbnail_url('thumbnail'); ?>" alt="<?php the_title(); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="post-info">
                            <h4 class="post-title"><?php the_title(); ?></h4>
                            <div class="post-meta">
                                <i class="fas fa-calendar-alt icon"></i>
                                <?php
                                $date_raw = get_field('tour_date');
                                if ($date_raw) {
                                    echo get_the_date('F j, Y');
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>
        </div>

        <hr class="divider" style="margin-top:19px;">
        <div class="quote-form">
            <?php echo do_shortcode('[contact-form-7 id="88828f2" title="blog form"]'); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('recent_posts_card_demo', 'custom_recent_posts_card_demo');



//serach blog
add_action('wp_ajax_search_blog_posts', 'ajax_search_blog_posts');
add_action('wp_ajax_nopriv_search_blog_posts', 'ajax_search_blog_posts');

function ajax_search_blog_posts() {
    $search = sanitize_text_field($_POST['search']);

    $args = array(
        'post_type' => 'blog',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        's' => $search, // WordPress default search (searches title/content)
    );
    ob_start();
    $query = new WP_Query($args);
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post(); ?>
            <div class="post-item">
                <div class="post-image">
                    <?php if (has_post_thumbnail()) : ?>
                        <img src="<?php the_post_thumbnail_url('thumbnail'); ?>" alt="<?php the_title(); ?>">
                    <?php endif; ?>
                </div>
                <div class="post-info">
                    <h4 class="post-title"><?php the_title(); ?></h4>
                    <div class="post-meta">
                        <i class="fas fa-calendar-alt icon"></i>
                        <?php echo get_the_date('F j, Y'); ?>
                    </div>
                </div>
            </div>
        <?php 
        wp_reset_postdata();
       endwhile;
    else :
         wp_send_json_error(['msg' => 'No blog posts found..']);
        wp_die();
    endif;

     $output = ob_get_clean();
      wp_send_json_success(['msg' => $output]);
    wp_die(); // important
}

//post serach box 
function custom_search_box() {
    ob_start();
    ?>
    <div class="search-container">
      <form class="search-box" method="post" id="ajax-blogpost-search-btn">
        <input type="text"  placeholder="Search..."  id="ajax-blogpost-search">
        <button type="submit" >Search</button>
      </form>

      <div id="message"></div>
      <div id="ajax-blogpost-results">
        <?php 
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        echo get_blog_posts($paged); ?>
      </div>
    </div>
    <?php
    return ob_get_clean();
  
}
add_shortcode('custom_searchbox', 'custom_search_box');

//blog descripion search 
function custom_search_where( $where, $wp_query ) {
    global $wpdb;
    if ( $search = $wp_query->get( 'custom_search' )){
        $where .= " AND(
            {$wpdb->posts}.post_title LIKE '%" . esc_sql( $wpdb->esc_like( $search ) ) . "%'
            OR EXISTS(
                SELECT 1 FROM {$wpdb->postmeta}
                WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                AND {$wpdb->postmeta}.meta_key = 'description'
                AND {$wpdb->postmeta}.meta_value LIKE '%" . esc_sql( $wpdb->esc_like( $search ) ) . "%'
            )
        )";
    }
    return $where;
}
add_filter( 'posts_where', 'custom_search_where', 10, 2 );

//common blog post function
function get_blog_posts($paged = 1, $search = null) {
    ob_start(); // Start output buffering

    // Build query args
    $query_args = array(
        'post_type'      => 'blog',
        'post_status'    => 'publish',
        'posts_per_page' => 4,
        'paged'          => $paged,
        'meta_query'     => array(
            array(
                'key'     => 'blog_status',
                'value'   => 'active',
                'compare' => '=',
            ),
        ),
    );

    // Add search keyword if provided
    if (!empty($search)) {
        $query_args['custom_search'] = $search; // WordPress uses 's' for search
    }

    $query = new WP_Query($query_args);

    if ($query->have_posts()) {
        echo '<div class="blog-cards-wrapper">';
        while ($query->have_posts()) {
            $query->the_post();
            $username = get_field('name');
            $date     = get_field('tour_date');
            $desc     = get_field('description');
            ?>
            <div class="blog-card">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="blog-image"><?php the_post_thumbnail('large'); ?></div>
                <?php endif; ?>

                <div class="blog-header">
                    <div class="blog-meta">
                        <i class="fas fa-user icon"></i>
                        <span class="username"><?php echo esc_html($username); ?></span>
                    </div>
                    <div class="blog-meta date-meta">
                        <i class="fas fa-calendar-alt icon"></i>
                        <?php if ($date) {
                            echo '<span class="blog-date">' . date("F j, Y", strtotime($date)) . '</span>';
                        } ?>
                    </div>
                </div>
                <h2 class="blog-title"><?php the_title(); ?></h2>
                <p class="blog-description"><?php echo esc_html($desc); ?></p>
                <div class="blog-arrow">
                    <a href="<?php the_permalink(); ?>" class="card-link">
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <?php
        }
        echo '</div>';

        // Pagination
        echo '<div class="pagination blog-pagination">';
        echo paginate_links([
            'total'     => $query->max_num_pages,
            'current'   => $paged,
            'format'    => '#',
            'prev_text' => '« Prev',
            'next_text' => 'Next »',
            'type'      => 'plain',
        ]);
        echo '</div>';

        wp_reset_postdata();
    } else {
        echo '<p>No blog posts found.</p>';
    }

    return ob_get_clean(); // Return the full HTML
}


//serach blog post
add_action('wp_ajax_search_blog_posts_ajax', 'ajax_search_blog_posts_ajax');
add_action('wp_ajax_nopriv_search_blog_posts_ajax', 'ajax_search_blog_posts_ajax');

function ajax_search_blog_posts_ajax(){
  
    $paged  = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $search = isset($_POST['search_blog']) ? sanitize_text_field($_POST['search_blog']) : '';

    $html = get_blog_posts($paged, $search);
    wp_send_json_success(['message' => $html]);  
}


//serach tour post 
function ajax_search_tour_packages() {
    // Sanitize the input
    $search = isset($_POST['search_tour']) ? sanitize_text_field($_POST['search_tour']) : '';

    $args = array(
        'post_type' => 'tour_package',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        's' => $search,
    );
    ob_start();
    $query = new WP_Query($args);
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
        ?>
            <div class="post-item">
                <div class="post-image">
                    <?php if (has_post_thumbnail()) : ?>
                        <img src="<?php the_post_thumbnail_url('thumbnail'); ?>" alt="<?php the_title(); ?>">
                    <?php endif; ?>
                </div>
                <div class="post-info" style="width: 147%;">
                    <a href="<?php the_permalink(); ?>" style="display:block; text-decoration:none; color:inherit;">
                        <h4 class="post-title"><?php the_title(); ?></h4>
                    </a>
                    <div class="post-meta">
                        <i class="fas fa-calendar-alt icon"></i>
                        <?php echo get_the_date('F j, Y'); ?>
                    </div>
                </div>
            </div>
        <?php
        endwhile;
    else :
         wp_send_json_error(['msg' => 'No tours found...']);
        wp_die();
    endif;

    $outputs = ob_get_clean();
    wp_send_json_success(['msg' => $outputs]);

    wp_reset_postdata();
    wp_die(); // very important

}
add_action('wp_ajax_search_tour_packages', 'ajax_search_tour_packages');
add_action('wp_ajax_nopriv_search_tour_packages', 'ajax_search_tour_packages');


//serach destination post ajax_search_destination_packages
function ajax_search_destination_packages() {
    // Sanitize the input
    $searchs = isset($_POST['search_destination']) ? sanitize_text_field($_POST['search_destination']) : '';

    $args = array(
        'post_type' => 'populardestination',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        's' => $searchs,
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
        ?>
             <div class="post-item">
                    <div class="post-image postimg">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php the_post_thumbnail_url('thumbnail'); ?>" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="post-info info">
                    <a href="<?php the_permalink(); ?>" style="display:block; text-decoration:none; color:inherit;">
                    <h4 class="post-title"><?php the_title(); ?></h4></a>
                        <div class="post-meta">
                        <i class="fas fa-calendar-alt icon"></i>
                        <?php
                            $date_raw = get_field('description');
                            if ($date_raw) {
                                echo get_the_date();
                            }
                            ?>
                        </div>
                    </div>
                </div>
        <?php
        endwhile;
    else :
        wp_send_json_error(['message'=> 'No tours found.']);
        wp_die();
    endif;

    wp_reset_postdata();
    $output = ob_get_clean();
   wp_send_json_success(['message' => $output]);
    wp_die(); // very impo rtant
}
add_action('wp_ajax_search_destination_packages', 'ajax_search_destination_packages');
add_action('wp_ajax_nopriv_search_destination_packages', 'ajax_search_destination_packages');




