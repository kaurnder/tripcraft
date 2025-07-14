<?php get_header(); ?>
 <!-- Banner Start -->
    <section class="inner-banner">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="inner-banner-content">
                        <h1> <?php the_title(); ?> </h1>
<p>                  <?php echo get_field('sub_title');?>
                    </p>
                    </div>
                    <div class="page-breadcrumb">
                        <h2> <a href="#"> Home </a>/ Blog</h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Banner End -->

    <!-- Our blog section  start -->
    <section class="single-blogs">
        <div class="container">
            <div class="row">
                <div class="col-xl-8">
                    <div class="single-blog">
                       <div class="blog-inner"><?php the_post_thumbnail('large'); ?></div>
                        <div class="single-blog-iteams">
                            <div class="single-blog-icones">
                                <p><i class="fa-regular fa-user"></i> Rakesh</p>
                            </div>
                            <div class="single-blog-icones">
                                <p> <img src="images/blog-sinle-calender.svg" alt=""> 
                                 <?php
                                        echo get_the_date();
                                    ?>
                            </p>
                            </div>
                            <div class="single-blog-icones">
                                <p> <i class="fa-regular fa-comment"></i>Comments</p>
                            </div>
                        </div>
                        <div class="single-blog-content">
                            <h3><?php the_title();?></h3>
                            <p><?php the_content();?></p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="right-bar-blog">
                        <div class="serch-box">
                            <h6>Search Here</h6>
                            <input type="text" placeholder="Search.." id="ajax-services-search">
                        </div>
                        <div class="more blogs-post">
                            <h5>Popular Post</h5>
                            <?php
                               $args = array(
                                'post_type' => 'ace_services',
                                'posts_per_page' => 3,
                                'post_status' => 'publish',
                                'orderby' => 'date',
                                'order' => 'DESC',
                            );
                            $recent_posts = new WP_Query($args);
                          ?> <div id="ace_msg"></div>
                           <div class ="recent-posts">
                            <div id="ajax-services-post-results">
                            <?php 
                            if ($recent_posts->have_posts()) :
                            while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                                <div class="more blogs-post-content">
                                    <a href="<?php the_permalink(); ?>"> <?php the_title(); ?></a>
                                    <p> <img src="images/blog-sinle-calender.svg" alt="">
                                    <?php
                                        echo get_the_date();
                                    ?>
                                </p>
                                </div>
                              <?php endwhile; ?>
                              <?php endif; ?>
                                </div>
                                </div>
                        </div>
                        <div class="blog-side-bar-outer">
                            <h5>Trending topic</h5>
                            <div class="blog-side-bar-tabs">
                                <ul>
                                    <?php
                                        $terms = get_terms(array(
                                            'taxonomy' => 'categories',
                                            'hide_empty' => false,
                                            'orderby' => 'count',
                                            'order' => 'DESC',
                                        ));
                                        foreach ($terms as $term){
                                            echo '<li> <a href="'.  get_term_link($term).'"> ' . esc_html($term->name) . '</a></li>';
                                        }
                                     ?>
                                     
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>

    
