<?php
/**
 * Template Name: No-Title + Sidebar + Cal + Event + Dest
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
            	<?php get_sidebar( $page ); ?>
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
                                       <p><a href="<?php echo $trip_links[$i]; ?>">Seats Left <?php echo $spots_left[$i]; ?></a></p>
                                    </div>
                                </div>	
                            </div> 
                
                        <hr/>
                
                        <?php 
                    } ?>
                
                <h1 style="margin-top:5px;" class="book-btn"><a href="/shop/">VIEW ALL TRIPS</a></h1>
                </div> <!-- #upcoming-trips -->
            </div>
            <div class="col grid_12_of_12" style="margin:0px;">
            	<h2 class="widgettitle">Destinations</h2>
                <div class="destinations">
                <div id="squelch-taas-accordion-0" class="squelch-taas-accordion squelch-taas-override ui-accordion ui-widget ui-helper-reset" data-active="none" data-disabled="false" data-autoheight="false" data-collapsible="true" role="tablist">

                <h3 id="squelch-taas-header-0" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-0" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-0">New York</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-0 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-0" aria-labelledby="squelch-taas-header-0" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/hunter-mt/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/hunter.png">Hunter MT</a><br>
                <a href="/destinations/windham-mt/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/windham.png">Windham MT</a>
                </div>

                <h3 id="squelch-taas-header-1" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-1" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-1">Pennsylvania</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-1 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-1" aria-labelledby="squelch-taas-header-1" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/camelback/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/camelback.png">Camelback</a>
                </div>

                <h3 id="squelch-taas-header-2" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-2" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-2">Vermont</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-2 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-2" aria-labelledby="squelch-taas-header-2" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/stratton/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/stratton.png">Stratton</a><br>
                <a href="/destinations/mount-snow/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/mount-snow.png">Mount Snow</a><br>
                <a href="/destinations/killington/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/killington.png">Killington</a><br>
                <a href="/destinations/stowe/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/stowe.png">Stowe</a><br>
                <a href="/destinations/sugarbush/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/sugarbush.png">Sugarbush</a>
                </div>

                <h3 id="squelch-taas-header-3" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-3" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-3">Out West</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-3 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-3" aria-labelledby="squelch-taas-header-3" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/lake-tahoe/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/tahoe.png">Lake Tahoe</a><br>
                <a href="/destinations/breckenridge/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/breck.png">Breckenridge</a><br>
                <a href="/destinations/snowbird/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/snowbird.png">Snowbird</a>
                </div>

                <h3 id="squelch-taas-header-4" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-4" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-4">South America</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-4 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-4" aria-labelledby="squelch-taas-header-4" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/north-chile/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/argentina.png">North Chile</a><br>
                <a href="/destinations/south-chile/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/argentina.png">South Chile</a><br>
                <a href="/destinations/north-argentina/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/chile.png">North Argentina</a><br>
                <a href="/destinations/south-argentina/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/chile.png">South Argentina</a>
                </div>

                <h3 id="squelch-taas-header-5" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-5" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-5">Canada</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-5 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-5" aria-labelledby="squelch-taas-header-5" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/whistler/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/whistler.png">Whistler</a>
                </div>

                <h3 id="squelch-taas-header-6" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-6" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-6">Japan</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-6 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-6" aria-labelledby="squelch-taas-header-6" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/japan/"><img style="float: left; margin: 0px 4px 0px 0px;" alt="" src="<?php bloginfo('template_directory');?>/images/destination-imgs/japan-icon.png">Japan</a>
                </div>
                </div>
            </div>
        </div>

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
