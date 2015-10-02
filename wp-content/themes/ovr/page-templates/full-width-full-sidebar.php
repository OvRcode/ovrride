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
$img_path = get_stylesheet_directory_uri();
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
                
                    $trip_dates = array();
                    $trips_titles = array();
                    $trip_links = array();
                    $trip_thumbs = array();
                    $spots_left = array();

                    // TODO: REWORK WHEN OLD PRODUCTS ARE OUT OF THE SYSTEM
                    while( $upcoming_product_loop->have_posts() ) : $upcoming_product_loop->the_post();
                        global $post;
                
                        $trip_date = get_field( 'date_picker' );
                        if ( ! $trip_date ) {
                            $product = wc_get_product($post->ID);
                            if ( "trip" == $product->product_type ) {
                                $trip_date = $product->wc_trip_start_date;
                            }
                        }
                        // only add trip to array if date_picker field is set.
                        if ( $trip_date ) {
                        $alt_thumb = wp_get_attachment_image_src( get_field( 'alternative_thumbnail', $post->ID ), 'full' );
                
                        $trip_dates[] = $trip_date;
                        $trip_titles[] = $post->post_title;
                        $trip_links[] = get_permalink();
                        $trip_thumbs[] = $alt_thumb[0];
                        $product = wc_get_product( $post->ID );
                        if ( $product->is_purchasable()){
                            $spots_left[] = $product->get_stock_quantity();
                        } else {
                            $spots_left[] = 0;
                        }
                        
                        }
                        
                        array_multisort( $trip_dates, $trip_titles, $trip_links, $trip_thumbs, $spots_left );
                    endwhile;
                    
                    // count how many trips are listed, if less than 8 have date_picker fields set, set to that number
                    $max_trips = count($trip_dates) < 10 ? count($trip_dates) : 10;
                
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
                                    <?php if ($spots_left[$i] !== 0): ?>
                                        <div class="book-this-btn">
                                            <a class="book-btn-smll" href="<?php echo $trip_links[$i]; ?>">BOOK THIS TRIP</a>
                                        </div>
                                        
                                        <div class="seats-left">
                                           <p><a href="<?php echo $trip_links[$i]; ?>">Space Available 
                                    <?php if($spots_left[$i] !== '' && (intval($spots_left[$i]) < 30 && intval($spots_left[$i] > 0))) echo intval($spots_left[$i]); ?></a></p>
                                        </div>
                                    <?php else: ?>
                                        <p class="stock out-of-stock">SOLD OUT</p>
                                    <?php endif; ?>
                                    
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
                
                <h3 id="squelch-taas-header-0" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-0" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-0">Alaska</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-0 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-0" aria-labelledby="squelch-taas-header-0" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destination/alaska/"><div class="location alaska"></div></a>
                </div>
                
                <h3 id="squelch-taas-header-1" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-1" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-1">Canada</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-1 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-1" aria-labelledby="squelch-taas-header-1" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destination/whistler/"><div class="location whistler"></div></a>
                </div>
                
                <h3 id="squelch-taas-header-2" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-2" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-2">Europe</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-2 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-8" aria-labelledby="squelch-taas-header-2" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destination/french-alps/"><div class="location french-alps"></div></a>
                <a href="/destination/austrian-alps/"><div class="location austrian-alps"></div></a>
                </div>
                
                <h3 id="squelch-taas-header-3" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-3" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-3">Japan</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-3 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-6" aria-labelledby="squelch-taas-header-3" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destination/japan/"><div class="location japan"></div></a>
                </div>
                
                <h3 id="squelch-taas-header-4" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-4" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-4">New York</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-4 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-4" aria-labelledby="squelch-taas-header-4" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destination/hunter-mt/"><div class="location hunter"></div></a><br>
                <a href="/destination/windham-mt/"><div class="location windham"></div></a>
                </div>
                
                <h3 id="squelch-taas-header-5" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-5" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-5">Out West</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-5 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-5" aria-labelledby="squelch-taas-header-5" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destination/breckenridge/"><div class="location breckenridge"></div></a><br>
                <a href="/destination/lake-tahoe/"><div class="location tahoe"></div></a><br>
                <a href="/destination/snowbird/"><div class="location snowbird"></div></a>
                </div>
                
                <h3 id="squelch-taas-header-6" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-6" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-6">Pennsylvania</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-6 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-6" aria-labelledby="squelch-taas-header-6" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destination/camelback/"><div class="location camelback"></div></a>
                </div>
                
                <h3 id="squelch-taas-header-7" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-7" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-7">South America</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-7 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-7" aria-labelledby="squelch-taas-header-7" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destination/north-argentina/"><div class="location northern-argentina"></div></a><br>
                <a href="/destination/south-argentina/"><div class="location southern-argentina"></div></a>
                <a href="/destination/north-chile/"><div class="location northern-chile"></div></a><br>
                <a href="/destination/south-chile/"><div class="location southern-chile"></div></a><br>
                </div>
                
                <h3 id="squelch-taas-header-8" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-8" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-8">Vermont</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-8 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-8" aria-labelledby="squelch-taas-header-8" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destination/jay-peak/"><div class="location jay-peak"></div></a><br>
                <a href="/destination/killington/"><div class="location killington"></div></a><br>
                <a href="/destination/mount-snow/"><div class="location mount-snow"></div></a><br>
                <a href="/destination/okemo/"><div class="location okemo"></div></a><br>
                <a href="/destination/stowe/"><div class="location stowe"></div></a><br>
                <a href="/destination/stratton/"><div class="location stratton"></div></a><br>
                <a href="/destination/sugarbush/"><div class="location sugarbush"></div></a>
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