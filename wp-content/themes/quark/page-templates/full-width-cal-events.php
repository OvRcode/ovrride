<?php
/**
 * Template Name: No-Title + Sidebar + Cal + Event
 *
 * Description: Displays a full-width page, with no sidebar. This template is great for pages
 * containing large amounts of content.
 *
 * @package Quark
 * @since Quark 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content row clearfix" role="main">
    	
         <div class="col grid_3_of_12 feedbar">
            <div class="col grid_12_of_12" style="margin: 0px 0px 15px 0px;">
            	<?php 
					$id = 1384;
					$p = get_page($id);
					echo apply_filters('the_content', $p->post_content);
				?>
                <h1 style="margin-top:5px;" class="book-btn"><a href="/events/">VIEW FULL CALENDAR</a></h1>
            </div>
            <div class="col grid_12_of_12" style="margin: 0px 0px 15px 0px;">
            	<?php
				    
				/* Upcoming Trips ________________________________________________________________ */ ?>

                <div id="upcoming-trips">
                
                    <h2>Upcoming Trips</h2> 
                
                    <?php
                
                    $args = array( 'post_type' => 'product', 'order' => 'ASC','posts_per_page' => -1 );   
                    $upcoming_product_loop = new WP_Query( $args );
                
                    $trip_dates = [];
                    $trips_titles = [];
                    $trip_links = [];
                    $trip_thumbs = [];
                    $spots_left = [];
                
                    while( $upcoming_product_loop->have_posts() ) : $upcoming_product_loop->the_post();
                        global $post;
                
                        $trip_date = get_field( 'date_picker' );
                
                        // only add trip to array if date_picker field is set.
                        if ( $trip_date ) {
                        $alt_thumb = wp_get_attachment_image_src( get_field( 'alternative_thumbnail', $post->ID ), 'full' );
                
                        $trip_dates[] = $trip_date;
                        $trip_titles[] = get_the_title();
                        $trip_links[] = get_permalink();
                        $trip_thumbs[] = $alt_thumb[0];
                        $spots_left[] = get_post_meta( $post->ID, '_stock', true);
                        }
                
                        array_multisort($trip_dates, $trip_titles, $trip_links, $trip_thumbs, $spots_left);
                    endwhile;
                
                    // count how many trips are listed, if less than 8 have date_picker fields set, set to that number
                    $max_trips = count($trip_dates) < 8 ? count($trip_dates) : 8;
                
                    // foreach ($trip_dates as $key => $value) {
                    for( $i = 0; $i < $max_trips; $i++ ) { ?>
                        
                        <div class="upcoming" style="overflow:hidden;">
                                <div class="upcoming-thumbnail">
                                    <a href="<?php echo $trip_links[$i]; ?>">
                                        <img src="<?php echo $trip_thumbs[$i]; ?>" alt="<?php echo esc_attr( $trip_titles[$i] );?>">
                                    </a>
                                </div>
                                
                                <div class="upcoming-title">
                                    <a href="<?php echo $trip_links[$i]; ?>"><?php echo $trip_titles[$i]; ?></a>
                                </div>
                         		
                                <div class="upcoming-info"> 
                                    <div class="book-this-btn">
                                        <a class="book-btn-smll" href="<?php echo $trip_links[$i]; ?>">BOOK THIS TRIP</a>
                                    </div>
                                    
                                    <div class="seats-left">
                                       <p><a href="<?php echo $trip_links[$i]; ?>">Space Available <?php echo $spots_left[$i]; ?></a></p>
                                    </div>
                                </div>	
                            </div> 
                
                        <hr/>
                
                        <?php 
                    } ?>
                
                <h1 style="margin-top:5px;" class="book-btn"><a href="/shop/">VIEW ALL TRIPS</a></h1>
                </div> <!-- #upcoming-trips -->
        </div>
        
		<div class="col grid_9_of_12 mainbar">

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', 'busstop' ); ?>
					<?php comments_template( '', false ); ?>
				<?php endwhile; // end of the loop. ?>

			<?php endif; // end have_posts() check ?>

		</div> <!-- /.col.grid_9_of_12 -->
	</div><!-- /#primary.site-content.row -->

<?php get_footer(); ?>
