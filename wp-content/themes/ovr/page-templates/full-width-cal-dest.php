<?php
/**
 * Template Name: No-Title + Sidebar + Cal + Dest
 *
 * Description: Displays a full-width page, with no sidebar. This template is great for pages
 * containing large amounts of content.
 *
 * @package Quark
 * @since Quark 1.0
 */
$img_path = $img_path;
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
            	<h2 class="widgettitle">Destinations</h2>
                <div class="destinations">
                <div id="squelch-taas-accordion-0" class="squelch-taas-accordion squelch-taas-override ui-accordion ui-widget ui-helper-reset" data-active="none" data-disabled="false" data-autoheight="false" data-collapsible="true" role="tablist">

                <h3 id="squelch-taas-header-0" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-0" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-0">New York</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-0 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-0" aria-labelledby="squelch-taas-header-0" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/hunter-mt/"><div class="location hunter"></div>Hunter MT</a><br>
                <a href="/destinations/windham-mt/"><div class="location windham"></div>Windham MT</a>
                </div>

                <h3 id="squelch-taas-header-1" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-1" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-1">Pennsylvania</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-1 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-1" aria-labelledby="squelch-taas-header-1" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/camelback/"><div class="location camelback"></div>Camelback</a>
                </div>

                <h3 id="squelch-taas-header-2" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-2" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-2">Vermont</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-2 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-2" aria-labelledby="squelch-taas-header-2" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/stratton/"><div class="location stratton"></div>Stratton</a><br>
                <a href="/destinations/mount-snow/"><div class="location mount-snow"></div>Mount Snow</a><br>
                <a href="/destinations/killington/"><div class="location killington"></div>Killington</a><br>
                <a href="/destinations/stowe/"><div class="location stowe"></div>Stowe</a><br>
                <a href="/destinations/sugarbush/"><div class="location sugarbush"></div>Sugarbush</a>
                </div>
                
                <h3 id="squelch-taas-header-3" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-3" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-3">Out West</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-3 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-3" aria-labelledby="squelch-taas-header-3" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/lake-tahoe/"><div class="location tahoe"></div>Lake Tahoe</a><br>
                <a href="/destinations/breckenridge/"><div class="location breckenridge"></div>Breckenridge</a><br>
                <a href="/destinations/snowbird/"><div class="location snowbird"></div>Snowbird</a>
                </div>

                <h3 id="squelch-taas-header-4" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-4" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-4">South America</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-4 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-4" aria-labelledby="squelch-taas-header-4" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/north-chile/"><div class="location chile"></div>North Chile</a><br>
                <a href="/destinations/south-chile/"><div class="location chile"></div>South Chile</a><br>
                <a href="/destinations/north-argentina/"><div class="location argentina"></div>North Argentina</a><br>
                <a href="/destinations/south-argentina/"><div class="location argentina"></div>South Argentina</a>
                </div>
                
                <h3 id="squelch-taas-header-5" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-5" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-5">Canada</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-5 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-5" aria-labelledby="squelch-taas-header-5" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/whistler/"><div class="location whistler"></div>Whistler</a>
                </div>

                <h3 id="squelch-taas-header-6" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all ui-accordion-icons" role="tab" aria-controls="ui-accordion-squelch-taas-accordion-0-panel-6" aria-selected="false" tabindex="-1"><span class="ui-accordion-header-icon ui-icon ui-icon-triangle-1-e"></span><a href="#squelch-taas-accordion-shortcode-content-6">Japan</a></h3>
                <div class="squelch-taas-accordion-shortcode-content-6 ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom" style="display: none; " id="ui-accordion-squelch-taas-accordion-0-panel-6" aria-labelledby="squelch-taas-header-6" role="tabpanel" aria-expanded="false" aria-hidden="true">
                <a href="/destinations/japan/"><div class="location japan"></div>Japan</a>
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