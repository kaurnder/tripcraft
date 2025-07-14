<?php get_header(); ?>

<main id="main" class="site-main">

    <h1><?php single_term_title(); ?></h1>

    <div id="ajax-services-post-results">
        <?php 
        if (have_posts()) :
            while (have_posts()) : the_post(); ?>
                <div class="more blogs-post-content">
                    <a href="<?php the_permalink(); ?>"> <?php the_title(); ?></a>
                    <p>
                        <img src="<?php echo get_template_directory_uri(); ?>/images/blog-sinle-calender.svg" alt="">
                        <?php echo get_the_date(); ?>
                    </p>
                </div>
            <?php endwhile;
        else :
            echo '<p>No posts found in this category.</p>';
        endif;
        ?>
    </div>
</main>

<?php get_footer(); ?>
