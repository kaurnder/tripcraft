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
function hello_elementor_child_scripts_styles(){
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



add_action( 'wp_enqueue_scripts', 'astra_child_style');
function astra_child_style(){
        
    $isUserLogin = is_user_logged_in() ? 'true' : 'false';
     $vall ="hello";
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css',array('parent-style') );
	wp_enqueue_style( 'style', get_stylesheet_directory_uri(). '/css/style.css?' . strtotime('now') );
	wp_enqueue_style( 'style', get_stylesheet_directory_uri(). '/css/new-style.css?' . strtotime('now') );
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
        'val'=> $vall,
        ]
    );
    wp_enqueue_script('jquery');
    wp_localize_script('custom-ajax-script', 'AjaxSearch', array(
        'ajax_url' => admin_url('admin-ajax.php')));
  
}

//login logout
add_filter('body_class', 'add_logged_in_class');
function add_logged_in_class($classes){
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
        <h3 class="tour-title">
            <?php echo wp_trim_words(get_the_title(), 4, '...');?>
        </h3>

        <?php if (has_post_thumbnail()) : ?>
        <div class="tour-image">
            <?php the_post_thumbnail('small');?>
        </div>
        <?php endif; ?>

        <?php if ($description) : ?>
        <p class="tour-desc">
            <?php echo esc_html($description); ?>
        </p>
        <?php endif; ?>

        <div class="tour-bottom">
            <?php if ($duration) : ?>
            <span class="tour-duration">
                <?php echo esc_html($duration); ?>
            </span>
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
        while ($query->have_posts()){
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
            echo '<h3 class="card-title">' . get_the_title(). '</h3>';

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

//apply slick slider
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

           
    $('.testimonial-slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        prevArrow: $('.slick-prev'),
        nextArrow: $('.slick-next'),
        autoplay: true,
        autoplaySpeed: 3000
    });

        });

    ");
}
add_action('wp_enqueue_scripts', 'enqueue_slick_slider_assets');


// function enqueue_slick_slider() {
//     // Register & enqueue Slick Slider
//     wp_enqueue_style('slick-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css');
//     wp_enqueue_style('slick-theme-css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css');
//     wp_enqueue_script('slick-js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array('jquery'), null, true);

//     // Add Inline Script for Slick Initialization
//     wp_add_inline_script('slick-js', "
//         jQuery(document).ready(function($) {
//             $('.testimonial-slider').slick({
//                 arrows: true,
//                 dots: false,
//                 autoplay: true,
//                 autoplaySpeed: 4000,
//                 prevArrow: '<button type=\"button\" class=\"slick-prev\"><img src=\"' + slick_vars.theme_url + '/images/arrow-left-icon.png\" alt=\"Previous\" class=\"arrow-img\" /></button>',
//                 nextArrow: '<button type=\"button\" class=\"slick-next\"><img src=\"' + slick_vars.theme_url + '/images/arrow-right.png\" alt=\"Next\" class=\"arrow-img\" /></button>'
//             });
//         });
//     ");
// }
// add_action('wp_enqueue_scripts', 'enqueue_slick_slider');



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
            
        //Change this if your plugin uses a different field
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
                <h4 class="post-title">
                    <?php the_title(); ?>
                </h4>
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
                        <h4 class="post-title">
                            <?php the_title(); ?>
                        </h4>
                    </a>
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
    <div class="recent-posts">
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
                        <h4 class="post-title">
                            <?php the_title(); ?>
                        </h4>
                    </a>
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
    <h3 class="recent-posts-heading">Recent Posts</h3>

    <div class="recent-posts">
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
                    <h4 class="post-title">
                        <?php the_title(); ?>
                    </h4>
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
                <h4 class="post-title">
                    <?php the_title(); ?>
                </h4>
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

//post serach box and fetch blog post
function custom_search_box() {
    ob_start();
    ?>
<div class="search-container">
    <form class="search-box" method="post" id="ajax-blogpost-search-btn">
        <input type="text" placeholder="Search..." id="ajax-blogpost-search">
        <button type="submit">Search</button>
    </form>

    <!-- fetch destination texonomy -->
    <div class="blog_textonomy">
        <select name="destination" id="destination_val">
            <option value="">Select Destination</option>
            <?php
                    $terms = get_terms(array(
                        'taxonomy' => 'destination',
                        'hide_empty' => false,
                    ));
                    foreach ($terms as $term) {
                        echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                    }
                    ?>
        </select>
        <!-- fetch culture-experienc texonomy -->
        <select name="culture_experienc" id="texonomy_val">
            <option value="">Select Culture Experienc</option>
            <?php
                    $terms = get_terms(array(
                        'taxonomy' => 'culture-experienc',
                        'hide_empty' => false,
                    ));
                    foreach ($terms as $term) {
                        echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                    }
                    ?>
        </select>
    </div>

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
function get_blog_posts($paged = 1, $search = null,  $taxonomy= null, $Cult_taxonomy= null) {
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

    if (!empty($taxonomy) || !empty($Cult_taxonomy)) {
            $query_args['tax_query'] = array(
                'relation' => 'OR', // Require both taxonomy conditions to be met
         );

    if (!empty($taxonomy)) {
            $query_args['tax_query'][] = array(
                array(
                    'taxonomy' => 'destination',
                    'field'    => 'slug',
                    'terms'    => $taxonomy,
                ),
            );
        }

        if (!empty($Cult_taxonomy)) {
            $query_args['tax_query'][] = array(
                array(
                    'taxonomy' => 'culture-experienc',
                    'field'    => 'slug',
                    'terms'    => $Cult_taxonomy,
                ),
            );
        }
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
    <div class="blog-image">
        <?php the_post_thumbnail('large'); ?>
    </div>
    <?php endif; ?>

    <div class="blog-header">
        <div class="blog-meta">
            <i class="fas fa-user icon"></i>
            <span class="username">
                <?php echo esc_html($username); ?>
            </span>
        </div>
        <div class="blog-meta date-meta">
            <i class="fas fa-calendar-alt icon"></i>
            <?php if ($date) {
                            echo '<span class="blog-date">' . date("F j, Y", strtotime($date)) . '</span>';
                        } ?>
        </div>
    </div>
    <h2 class="blog-title">
        <?php the_title(); ?>
    </h2>

    <p class="blog-cate">
        <?php $terms = get_the_terms(get_the_id() ,'destination'); 
                  foreach($terms as $term) { echo "$term->name"; } 
                  ?>
    </p>

    <p class="blog-description">
        <?php echo esc_html($desc); ?>
    </p>

    <p class="blog-cate">
        <?php $terms = get_the_terms(get_the_id() ,'culture-experienc'); 
                  foreach($terms as $term) { echo "$term->name"; } 
                  ?>
    </p>

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
         wp_send_json_error(['message' => 'No blog posts found.']); 
         wp_die(); 
    }

    return ob_get_clean(); // Return the full HTML
}
//serach blog post
add_action('wp_ajax_search_blog_posts_ajax', 'ajax_search_blog_posts_ajax');
add_action('wp_ajax_nopriv_search_blog_posts_ajax', 'ajax_search_blog_posts_ajax');

function ajax_search_blog_posts_ajax(){
  
    $paged  = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $search = isset($_POST['search_blog']) ? sanitize_text_field($_POST['search_blog']) : '';
    $taxonomy = sanitize_text_field($_POST['my_texonomy']);
    $culture_text = sanitize_text_field($_POST['culture_text']);

    $html = get_blog_posts($paged, $search,   $taxonomy ,$culture_text);
    wp_send_json_success(['message' => $html]);  
}

//create custom textnomy
function create_blog_texonomy() {
    register_taxonomy('destination', 'blog', array(
        'label' => 'Destination',
        'rewrite' => array('slug' => 'destination'),
        'hierarchical' => true,
    ));
}
add_action('init', 'create_blog_texonomy');

function create_blogs_texonomy(){
    register_taxonomy('culture-experienc', 'blog', array(
        'label' => 'Culture Experienc',
        'rewrite' => array('slug' => 'culture_experienc'),
        'hierarchical' => true,
    ));
}
add_action('init', 'create_blogs_texonomy');

// function create_blogss_texonomy(){
//     register_taxonomy('culture experienc', 'blog', array(
//         'label' => 'Culture Experienc',
//         'rewrite' => array('slug' => 'culture_experienc'),
//         'hierarchical' => true,
//     ));
// }
// add_action('init', 'create_blogss_texonomy');

//, "Historical", "Cultural", "Food & Cuisine", "Festivals", "Nightlife"



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
            <h4 class="post-title">
                <?php the_title(); ?>
            </h4>
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
            <h4 class="post-title">
                <?php the_title(); ?>
            </h4>
        </a>
        <div class="post-meta">
            <i class="fas fa-calendar-alt icon"></i>
            <?php
                            $date_raw = get_field('description');
                            if($date_raw) {
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




function practics_blog_posts()
{
    ?>
<div class="search-container">
    <form class="search-box" method="post" id="p_blog">
        <input type="text" placeholder="Search..." id="val_blog">
        <button type="submit" id="myid">Search</button>
    </form>
    <div class="blog-cards-wrapper">
        <div id="blog_msg"></div>
        <div id="ajax-practics-results">
        <?php 
        $page = (get_query_var('paged')) ? get_query_var('paged') : 1;
        echo fetch_blog_post(null,$page); ?>
        </div>
    </div>
</div>

<?php
}
add_shortcode('practics_blog_post','practics_blog_posts');

function fetch_blog_post($val = null,$paged=1,){
    ob_start();
    $args= array(
        'post_type'=>'blog', // Your post type name
        'posts_per_page' => 3,
        'paged' => $paged,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'blog_status',
                'value' => 'active',
                'compare' => '=',
            )
        )
    );
    if(!empty($val))
    {
        $args['custom_search']= $val;
    }

    $query = new WP_query($args);
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
    <div class="blog-image">
        <?php the_post_thumbnail('large'); ?>
    </div>
    <?php endif; ?>

    <div class="blog-header">
        <div class="blog-meta">
            <i class="fas fa-user icon"></i>
            <span class="username">
                <?php echo esc_html($username); ?>
            </span>
        </div>
        <div class="blog-meta date-meta">
            <i class="fas fa-calendar-alt icon"></i>
            <?php if ($date) {
                            echo '<span class="blog-date">' . date("F j, Y", strtotime($date)) . '</span>';
                        } ?>
        </div>
    </div>
    <h2 class="blog-title">
        <?php the_title(); ?>
    </h2>
    <p class="blog-description">
        <?php echo esc_html($desc); ?>
    </p>

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
        echo '<div class="pagination blog-pagination-practics">';
        echo paginate_links([
            'total'     => $query->max_num_pages,
            'current'   => $paged,
            'format'    => '#',
            'prev_text' => '« Prev',
            'next_text' => 'Next »',
            'type'      => 'plain',
        ]);
        echo '</div>';
    } else {
        wp_send_json_error(['blog_msg' => 'No blog posts found.']); 
        wp_die(); 
    }
    wp_reset_postdata();
    return ob_get_clean(); // Return the full HTML

    // echo '<pre>';
    // print_r($query);
    // echo '</pre>';
}
add_action('wp_ajax_blog_practics', 'ajax_blog_practics');
add_action('wp_ajax_nopriv_blog_practics', 'ajax_blog_practics');
function ajax_blog_practics(){

   $input_val = $_POST['input_val'];
   $page_val = $_POST['my_page'];
   $result =  fetch_blog_post($input_val, $page_val);
   wp_send_json_success(['blog_msg' => $result]);

}

function load_slick_slider_assets(){
    wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');
    
    wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), null, true);

    // Optional: your custom JS
    wp_enqueue_script('custom-slider-init', get_template_directory_uri() . '/js/custom-slider-init.js', array('jquery', 'slick-js'), null, true);
}
add_action('wp_enqueue_scripts', 'load_slick_slider_assets');


 //testimonial  and review  shortcode
function Review_Testimonials_shortcodeyy(){
    ob_start();
    $args = array(
        'post_type' => 'testimonial',
        'posts_per_page' => -1
    );
    ?>
    <section class="testimonial">
        <div class="container">
            <?php $query = new WP_Query($args);
            if ($query->have_posts()) : ?>
              <div class="testimonial-navigation">
            <div class="arrow-group">
                <button class="slick-prev">←</button>
                <button class="slick-next">→</button>
            </div>
        </div>
                <div class="testimonial-slider">
                    <?php while ($query->have_posts()) : $query->the_post();
                        $desc = get_the_content();
                        $title = get_the_title();
                        $post_id = get_the_id();
                        $designation = get_post_meta($post_id, 'tss_designation', true);
                        $rating = get_post_meta($post_id, 'tss_rating', true); ?>
                        
                        <div class="testimonial-slide">
                            <div class="testimonial-outer">
                                <div class="testimonial-left">
                                    <div class="client-image-wrapper">
                                        <div class="client-image">
                                            <?php echo get_the_post_thumbnail(get_the_ID(), 'thumbnail'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="testimonial-right">
                                    <div class="rating">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo '<i class="fas fa-star' . ($i <= ceil($rating) ? ' active' : '') . '"></i>';
                                        }
                                        ?>
                                    </div>
                                    <p class="testimonial-text"><?php echo esc_html($desc); ?></p>
                                    <div class="testimonial-name">
                                        <h4><?php echo esc_html($title); ?></h4>
                                        <span><?php echo esc_html($designation); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('Review_Testimonials_section', 'Review_Testimonials_shortcodeyy');

//custom post 
add_action('init', 'products_post_type');
function products_post_type() {
    register_post_type('products', array(
        'label'        => 'Products',
        'public'       => true,
        'supports'     => array('title', 'editor', 'thumbnail',),
        'has_archive'  => true,
        'rewrite'      => array('slug' => 'products'),
    ));
}
// i want to fetch google review in my shortcode and design according to me suggest best plugin to show google review and custom descign
add_shortcode('product_shortcode', 'Review_Testimonials_shortcode');
function Review_Testimonials_shortcode() {
    $args = array(
        'post_type' => 'products',
        'posts_per_page' => -1,
        'order' => 'DESC',
        'post_status' => 'publish',
    );
    $query = new WP_Query($args);
    ob_start();
    ?>
    <div class="products">
        <?php if ($query->have_posts()) : ?>
            <?php while ($query->have_posts()) : $query->the_post(); 
                $desc = get_the_content();
                $title = get_the_title();
                $product_url = get_field('products_url');
            ?>
                <a href="<?php echo esc_url($product_url); ?>" target="_blank" class="product-link">
                    <div class="product-wrap">
                        <div class="product-icon">
                            <?php the_post_thumbnail('thumbnail'); ?>
                        </div>
                        <h3><?php echo esc_html($title); ?></h3>
                        <div class="product-description">
                            <p><?php echo esc_html($desc); ?></p>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

//create custom post type
add_action('init', 'servicess_post_type');
function servicess_post_type() {
    register_post_type('ace_services', array(
        'label'        => 'Ace Services',
        'public'       => true,
        'supports'     => array('title', 'editor', 'thumbnail',),
        'has_archive'  => true,
        'rewrite'      => array('slug' => 'ace_services'),
    ));
}

//create custom categories textonomies
function create_categories_texonomy() {
    register_taxonomy('categories', 'ace_services', array(
        'label' => 'Categories',
        'rewrite' => array('slug' => 'categories'),
        'hierarchical' => true,
    ));
}
add_action('init', 'create_categories_texonomy');

//services shortcode
function services_fetch_shortcode(){ ?>
  <section class="services card-box"><?php
     $args = array(
        'post_type' => 'ace_services',
        'posts_per_page' => -1,
        'order' => 'DESC',
        'post_status' => 'publish',
    );
    $query = new WP_Query($args);
    ob_start();
    ?>
    <div class="container">
        <?php if ($query->have_posts()) : ?>
            <?php while ($query->have_posts()) : $query->the_post();
             $title = get_the_title(); 
             $desc = get_the_content();
            ?>
               <div class="service-box-card">
                           <div class="icon-box"> <?php the_post_thumbnail('thumbnail'); ?></div>
                           <div class="service-title"><?php echo esc_html($title); ?></div>
                           <div class="service-description"><?php echo wp_trim_words(get_the_content(), 4, '...');  ?></div>
                           <a href="<?php get_permalink( $query->ID ) ?>" class="">Read More</a>
                </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('services_shortcode','services_fetch_shortcode');

//single services search post how to i learn wordpress coding skill
add_action('wp_ajax_search_ace_services_posts', 'ajax_search_ace_services_posts');
add_action('wp_ajax_nopriv_search_ace_services_posts', 'ajax_search_ace_services_posts');

function ajax_search_ace_services_posts() {

    $sch = sanitize_text_field($_POST['search_services']);
    $args = array(
        'post_type' => 'ace_services',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        's' => $sch, 
    );
    ob_start();
    $query = new WP_Query($args);
       if ( $query ->have_posts()) :
            while ( $query ->have_posts()) : $query->the_post(); ?>
                <div class="more blogs-post-content">
                    <a href=" "> <?php the_title(); ?></a>
                    <p> <img src="images/blog-sinle-calender.svg" alt="">
                    <?php
                    $date_raw = get_field('sub_title');
                    if($date_raw) {
                        echo get_the_date();
                    }
                    ?>
                </p>
                </div>
               <?php wp_reset_postdata();
             endwhile; 
         else :
            wp_send_json_error(['ace_msg' => 'No blog posts found..']);
            wp_die();
        endif;

        $outputt = ob_get_clean();
        wp_send_json_success(['ace_msg' => $outputt]);
        wp_die(); 
}

//google reviws 
function myfun(){
    ?>
 <!-- Start testimonials section -->
<section class="testimonial">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="testimonial-wrp">
                    <h4 class="subtitle">Review & Testimonials</h4>
                    <h2 class="title">HAPPY CUSTOMER</h2>
                </div>
                <div class="testimonial-slider">
                    <?php echo do_shortcode('[grw id=5847]'); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!--End testimonials section -->

<?php
}

function acewebx_time_ago($date_string) {
    try {
        $date = new DateTime($date_string);
    } catch (Exception $e) {
        return '';
    }

    $now = new DateTime();
    $interval = $now->diff($date);

    if ($interval->y >= 1) {
        return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
    } elseif ($interval->m >= 1) {
        return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
    } elseif ($interval->d >= 1) {
        return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
    } elseif ($interval->h >= 1) {
        return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
    } elseif ($interval->i >= 1) {
        return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
    } else {
        return 'just now';
    }
}

//live google reviews fetch widget for google review plugin
function acewebx_custom_reviews_output() {
    $reviews_html = do_shortcode('[grw id=5847]');
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    
    $dom->loadHTML(mb_convert_encoding($reviews_html, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new DOMXPath($dom);

    $review_items = $xpath->query("//*[contains(@class, 'grw-review-inner') and contains(@class, 'grw-review-inner grw-backgnd')]");

    
    ob_start(); 

    $headerNode = $xpath->query("//*[contains(@class, 'grw-header-inner')]");
    if ($headerNode->length > 0) {
    $header = $headerNode->item(0);

    // Business name
    $nameNode = $xpath->query(".//*[contains(@class, 'wp-google-name')]", $header);
    $business_name = ($nameNode->length > 0) ? trim($nameNode->item(0)->textContent) : 'No name';

    $google_rating = $xpath->query(".//*[contains(@class, 'wp-google-rating')]", $header);
    $google_name = ($google_rating ->length > 0) ? trim($google_rating ->item(0)->textContent) : 'No name';


   //Rating (counting full and half stars)
   $starsNode = $xpath->query(".//*[contains(@class, 'wp-google-stars')]", $item);
   $rating = 0;

   if ($starsNode->length > 0) {
    // Get all SVG paths with a 'fill' attribute inside the stars node
    $svgPaths = $xpath->query(".//svg/path[@fill]", $starsNode->item(0));
    foreach ($svgPaths as $path) {
        $fill = strtolower($path->getAttribute('fill'));
        if ($fill === '#fb8e28'){
            $rating++; 
        }
    }
}
    // Output to verify 
    echo "<p>custom reviews</p>";
    echo "<p>$google_name</p>";?>
    <div class="rating">
        <?php
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                echo '<i class="fas fa-star" style="color:#fb8e28;"></i>';
            } else {
                echo '<i class="fas fa-star" style="color: #ccc;"></i>';
            }
        }
        ?>
    </div>
<?php
}
?>
    <section class="testimonial">
        <div class="container">
                    <div class="testimonial-slider ">
                        <?php
                        $count = 0;
                        foreach ($review_items as $item) {
                            if (++$count > 30) break;
                            // Image
                        // Get image URL from DOM
                            $imgNode = $xpath->query(".//img", $item);
                            $img = ($imgNode->length > 0) ? $imgNode->item(0)->getAttribute('src') : '';
                            $default_img =  get_stylesheet_directory_uri();
                            $final_img = !empty($img) ? esc_url($img) : esc_url($default_img);

                            // Name
                            $nameNode = $xpath->query(".//*[contains(@class, 'wp-google-name')]", $item);
                            $name = $nameNode->length > 0 ? trim($nameNode->item(0)->textContent) : 'Anonymous';

                            // Review text
                            $textNode = $xpath->query(".//*[contains(@class, 'wp-google-feedback')]", $item);
                            $text = $textNode->length > 0 ? trim($textNode->item(0)->textContent) : '';

                             // Review date
                            $dateNode = $xpath->query(".//*[contains(@class, 'wp-google-time')]", $item);
                            $raw_date = $dateNode->length > 0 ? trim($dateNode->item(0)->textContent) : '';
                            $time_ago = $raw_date ? acewebx_time_ago($raw_date) : '';


                            //Rating by counting SVG fill colors
                             if(empty($text)) continue;
                              $rating = 0;
                              $starsNode = $xpath->query(".//*[contains(@class, 'wp-google-stars')]", $item);
                              if($starsNode->length > 0) {
                                $svgPaths = $xpath->query(".//svg/path[@fill]", $starsNode->item(0));
                                foreach ($svgPaths as $path) {
                                    $fill = $path->getAttribute('fill');
                                    if(strtolower($fill) == '#fb8e28') {
                                        $rating++;
                                    }
                                }
                            }
                            ?>
                            <div class="testimonial-slide">
                                <div class="testimonial-outer">
                                    <div class="testimonial-left">
                                        <div class="client-image-wrapper">
                                        <img loading="lazy"
                                        src="<?php echo $final_img; ?>"
                                        onerror="this.onerror=null;this.src='<?php echo esc_url($default_img); ?>/images/testimonials.png';"
                                        alt="review image"
                                        class="client-image" />
                                        </div>
                                    </div>
                                    <div class="testimonial-right">
                                        <div class="rating">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $rating) {
                                                    echo '<i class="fas fa-star" style="color: #fb8e28;"></i>';
                                                } else {
                                                    echo '<i class="fas fa-star" style="color: #ccc;"></i>';
                                                }
                                            }
                                            ?>
                                        </div>
                                        <p class="testimonial-text"><?php echo esc_html($text); ?></p>
                                        <div class="testimonial-name">
                                            <h4><?php echo esc_html($name); ?></h4>
                                            <h4><?php echo esc_html($time_ago); ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php }  ?>
                    </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('acewebx_custom_reviews', 'acewebx_custom_reviews_output');

//live google reviews rating  fetch 
function acewebx_custom_google_summary() {
    $reviews_html = do_shortcode('[grw id=5847]');
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML(mb_convert_encoding($reviews_html, 'HTML-ENTITIES', 'UTF-8'));
    $xpath = new DOMXPath($dom);

    $headerNode = $xpath->query("//*[contains(@class, 'grw-header-inner')]");
    if ($headerNode->length === 0) return 'No review data found.';
    
    $header = $headerNode->item(0);

    $ratingTextNode = $xpath->query(".//*[contains(@class, 'wp-google-rating')]", $header);
    $google_rating = ($ratingTextNode->length > 0) ? trim($ratingTextNode->item(0)->textContent) : '0.0';

    $starsNode = $xpath->query(".//*[contains(@class, 'wp-google-stars')]", $header);
    $rating = 0;
    if ($starsNode->length > 0) {
        $svgPaths = $xpath->query(".//svg/path[@fill]", $starsNode->item(0));
        foreach ($svgPaths as $path) {
            if (strtolower($path->getAttribute('fill')) === '#fb8e28') {
                $rating++;
            }
        }
    }
    ob_start(); ?>
    <div class="acewebx-google-summary">
        <div class="google-logo">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/testimonials.png" alt="Google Reviews" />
        </div>
        <h3>Custom Reviews</h3>
    </div>
    <div class="rating-wrapper">
            <span class="google-rating"><?php echo esc_html($google_rating); ?></span>
            <div class="google-stars">
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    echo '<i class="fas fa-star" style="color:' . ($i <= $rating ? '#fb8e28' : '#ccc') . ';"></i>';
                }
                ?>
            </div>
        </div>
    <?php
    return ob_get_clean();
}
add_shortcode('acewebx_google_summary', 'acewebx_custom_google_summary');

//add custom role
//function add_custom_roles(){
    // add_role( 'Manager', __( 'Manager', 'your-text-domain' ), array(
    //     'read' => true,
    //     'create_posts' => true,
    //     'edit_posts' => true,
    //     'delete_posts' => false,
    // ) );

    // add_role( 'Reportor', __( 'Reportor', 'your-text-domain' ), array(
    //     'read' => true,
    //     'edit_posts' => true,
    //     'upload_files' => true,
    // ) );

  //remove_role('mani');//delete role

//}
//add_action( 'init', 'add_custom_roles' );


//get acf filed url id
add_action('wp', function () {
    if (is_singular('about')) {
        global $post, $linked_post_id_from_url;
        $about_url = get_field('about_url', $post->ID);
        $linked_post_id_from_url = get_post_id_from_custom_url($about_url);
    }
});
function get_post_id_from_custom_url($url) {
    if (empty($url)) return 0;
    $parsed_url = wp_parse_url($url);
    $allowed_post_types = get_post_types(['public' => true]);
    if (!empty($parsed_url['query'])) {
        parse_str($parsed_url['query'], $query_vars);
        if (!empty($query_vars['p'])) {
            return (int)$query_vars['p'];
        }
        if (!empty($query_vars['page_id'])) {
            return (int)$query_vars['page_id'];
        }
        if (!empty($query_vars['slug']) && !empty($query_vars['post_type'])) {
            $slug = sanitize_title($query_vars['slug']);
            $post_type = sanitize_title($query_vars['post_type']);
            if (in_array($post_type, $allowed_post_types, true)) {
                $post = get_page_by_path($slug, OBJECT, $post_type);
                return $post ? $post->ID : 0;
            }
        }
        foreach ($allowed_post_types as $pt) {
            if (!empty($query_vars[$pt])) {
                $slug = sanitize_title($query_vars[$pt]);
                $post = get_page_by_path($slug, OBJECT, $pt);
                return $post ? $post->ID : 0;
            }
        }
    }
    if (!empty($parsed_url['path'])) {
        $path_parts = explode('/', trim($parsed_url['path'], '/'));
        $slug = end($path_parts);
        if (!empty($slug)) {
            $post = get_page_by_path($slug, OBJECT, $allowed_post_types);
            return $post ? $post->ID : 0;
        }
    }
    return 0;
}



//1. Add checkbox column to the Users table
add_filter('manage_users_columns', 'add_users_bulk_action_column');
function add_users_bulk_action_column($columns) {
    $columns['cb'] = '<input type="checkbox" class="users-checkbox-all" />';
    return $columns;
}

// 2. Register custom bulk action
add_filter('bulk_actions-my-custom-plugin', 'add_users_bulk_action');
function add_users_bulk_action($bulk_actions) {
    $bulk_actions['delete_users_bulk'] = __('Delete Users', 'project');
    return $bulk_actions;
}

// 3. Output checkbox for each user row
add_action('manage_users_custom_column',  'add_users_bulk_action_column_content', 10, 3);
function add_users_bulk_action_column_content($value, $column_name, $user_id) {
    if($column_name == 'cb'){
        return '<input type="checkbox" name="users[]" class="users-checkbox" value="' . esc_attr($user_id) . '" />';
    }
    return $value;
}

// 4. Handle the bulk delete action
add_filter('handle_bulk_actions-my-custom-plugin', 'handle_users_bulk_action', 10, 3);
function handle_users_bulk_action($redirect_to, $action, $user_ids) {
    if ($action !== 'delete_users_bulk'){
        return $redirect_to;
    }

    $deleted_count = 0;

    foreach ($user_ids as $user_id) {
        if (! current_user_can('delete_users')) {
            wp_die(__('You do not have permission to delete users.', 'your-textdomain'));
        }

        if (! wp_delete_user($user_id)) {
            wp_die(__('Error deleting user with ID: ', 'your-textdomain') . $user_id);
        }

        $deleted_count++;
    }

    $redirect_to = add_query_arg('deleted', $deleted_count, $redirect_to);
    return $redirect_to;
}

// 5. Show admin notice after deletion
// add_action('admin_notices', 'users_bulk_action_admin_notices');
// function users_bulk_action_admin_notices(){
//     if (! empty($_REQUEST['deleted'])) {
//         $deleted_count = intval($_REQUEST['deleted']);
//         printf(
//             '<div id="message" class="updated notice is-dismissible"><p>%s</p></div>',
//             sprintf(_n('%s user deleted.', '%s users deleted.', $deleted_count, 'your-textdomain'), $deleted_count)
//         );
//     }
// }




//edit bulk action post
add_filter('bulk_actions-edit-blog', 'my_custom_plugin_register_edit_posts_bulk_action');

function my_custom_plugin_register_edit_posts_bulk_action($actions) {
    $actions['edit_posts_custom'] = __('Move to Decative Status', 'my-custom-plugin-textdomain');
    return $actions;
}

// In your plugin's main file or an included file
add_filter('handle_bulk_actions-edit-blog', 'my_custom_plugin_handle_edit_posts_bulk_action', 10, 3);

function my_custom_plugin_handle_edit_posts_bulk_action($redirect_url, $action, $post_ids) {
    if($action !== 'edit_posts_custom'){
        return $redirect_url;
    }

    // Perform security checks 
    if (!current_user_can('edit_posts')) { 
        wp_die(__('You are not allowed to edit posts.', 'my-custom-plugin-textdomain'));
    }
    check_admin_referer('bulk-posts'); 

    foreach($post_ids as $post_id) {

        // wp_update_post([
        //     'ID' => $post_id,
        //     'post_status' => 'draft',
        // ]);
        
        update_field('blog_status', 'decative', $post_id);
    }

$redirect_url = add_query_arg('changed-to-published', count($post_ids), $redirect_url);
    return $redirect_url;
}


add_action('admin_notices', function() {
	if (!empty($_REQUEST['changed-to-published'])) {
		$num_changed = (int) $_REQUEST['changed-to-published'];
		printf('<div id="message" class="updated notice is-dismissable"><p>' . __('update %d posts.', 'txtdomain') . '</p></div>', $num_changed);
	}
});



// 1.  add acf field in post as column sort column Add custom column to blog CPT
function add_blog_columns($columns) {
    $new_columns = [];

    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;

        // Insert our custom column *right after* the title column
        if ($key === 'title') {
            $new_columns['blog_Status'] = __('Blog Status', 'your-text-domain');
        }
    }

    return $new_columns;
}
add_filter('manage_blog_posts_columns', 'add_blog_columns');
// 2. Populate the custom column
function blog_custom_column_content($column, $post_id){
    if ($column == 'blog_Status') {
        $status = get_field('blog_status', $post_id); // or get_post_meta($post_id, 'blog_status', true);
        echo ucfirst($status);
    }
}
add_action('manage_blog_posts_custom_column', 'blog_custom_column_content', 10, 2);

// function make_blog_status_column_sortable($columns) {
//     $columns['blog_Status'] = 'blog_status';
//     return $columns;
// }
// add_filter('manage_edit-blog_sortable_columns', 'make_blog_status_column_sortable');
