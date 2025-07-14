<?php get_header(); ?>
        <div class="img-banner" style="
            padding-left:150px;
            padding-right:150px;
            background:
            linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
            url('<?php echo get_the_post_thumbnail_url(get_the_ID(),'full'); ?>');
            background-size: cover;
            background-position: right center;
            font-family:jost;
            height: 570px;
            display: flex;
            flex-direction:column;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            text-shadow: 1px 1px 5px black;
            font-size: 50px;
            font-weight: bold;
            ">
             <?php the_title(); ?>
            <div class="bottom-banner" style="
                background-color:#242121B0;
                height:43;
                width:100%;
                margin-top:20px;">
            </div>
       </div>

        <div class="mains">
           <div class="post-infos">
                <div class="featureds-img">
                    <!-- Display featured image -->
                    <?php 
                        if( has_post_thumbnail() ) {
                            the_post_thumbnail('large'); // You can use 'thumbnail', 'medium', 'large', or custom size
                        }
                        ?>
                </div>

                <div class="infos">
                    <i class="fas fa-calendar-alt icon"></i>
                    <?php
                           $date_raw = get_field('description');
                            if ($date_raw) {
                                echo get_the_date();
                            }
                    ?>
                </div>
                <p class="post-review">
                    
                    <?php the_field('review'); ?>
                </p>

                <div class="titles">
                    <h1>
                    <?php the_title(); ?>
                    </h1> 
                </div>
                <div class="acf-fields" style="line-height:29px;">
                    <p>
                   <?php
                   $description = get_field('description'); // Fetch ACF field
                   echo nl2br($description);
                   ?>
                    </p>
               </div>
          </div>
              <!-- Display shortcode output -->
            <div class="shortcode">
               <?php echo do_shortcode('[recent_posts_card_destination]');?> 
            </div>
      </div>
 

<?php get_footer(); ?>