<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id #maincontentcontainer div and all content after.
 * There are also four footer widgets displayed. These will be displayed from
 * one to four columns, depending on how many widgets are active.
 *
 * @package Quark
 * @since Quark 1.0
 */
?>

	</div> <!-- /#maincontentcontainer -->
    
    <div id="footercontainer-top">
    	<footer class="site-footer row clearfix" role="contentinfo">

			<?php
			// Count how many footer sidebars are active so we can work out how many containers we need
			$footerSidebars = 0;
			for ( $x=1; $x<=4; $x++ ) {
				if ( is_active_sidebar( 'sidebar-footer' . $x ) ) {
					$footerSidebars++;
				}
			}

			// If there's one or more one active sidebars, create a row and add them
			if ( $footerSidebars > 0 ) { ?>
				<?php
				// Work out the container class name based on the number of active footer sidebars
				$containerClass = "grid_" . 12 / $footerSidebars . "_of_12";

				// Display the active footer sidebars
				for ( $x=1; $x<=4; $x++ ) {
					if ( is_active_sidebar( 'sidebar-footer'. $x ) ) { ?>
						<div class="col <?php echo $containerClass?>">
							<div class="widget-area" role="complementary">
								<?php dynamic_sidebar( 'sidebar-footer'. $x ); ?>
							</div>
						</div> <!-- /.col.<?php echo $containerClass?> -->
					<?php }
				} ?>

			<?php } ?>

		</footer> <!-- /.site-footer.row -->
    </div>
        
	<div id="footercontainer">
		<footer class="site-footer row clearfix" role="contentinfo">
            <div class="col grid_3_of_12">
                <a href="http://www.blades.com" target="_blank"><img style="width:40px;height:40px;margin:0px 3px 0px 0px;" src="http://ovrride.com/wp-content/themes/quark/images/shops/blades.jpg" alt="Blades" /></a> 
                <a href="http://skatebrooklynny.com" target="_blank"><img style="width:40px;height:40px;margin:0px 3px 0px 0px;" src="http://ovrride.com/wp-content/themes/quark/images/shops/Sbrooklyn.jpg" alt="Skate Brooklyn" /></a> 
                <a href="http://aegirboardworks.com" target="_blank"><img style="width:40px;height:40px;margin:0px 3px 0px 0px;" src="http://ovrride.com/wp-content/themes/quark/images/shops/aegir.jpg" alt="Aegir" /></a> 
                <a href="http://www.burton.com/default/stores-nyc.html" target="_blank"><img style="width:40px;height:40px;margin:0px 3px 0px 0px;" src="http://ovrride.com/wp-content/themes/quark/images/shops/burton-icon.png" alt="Burton" /></a> 
            </div>
        
            <div style="text-align:center;" class="col grid_6_of_12">
                <a href="http://ovrride.com/">Home</a><span> | </span>
                <a href="http://ovrride.com/blog/">Blog</a><span> | </span>
                <a href="http://ovrride.com/faq/">FAQ</a><span> | </span>
                <a href="http://ovrride.com/terms-and-conditions/">Terms &amp; Conditions</a><span> | </span>
                <a href="http://ovrride.com/contact-us/">Contact Us</a>
                <br />
                <span>&copy; COPYRIGHT 2013 - <a href="http://ovrride.com/">OvR ride LLC.</a> - ALL RIGHTS RESERVED</span>
            </div>
            
            <div style="text-align:right;" class="col grid_3_of_12">
                <a href="http://www.homagebrooklyn.com/site/" target="_blank"><img style="width:40px;height:40px;margin:0px 3px 0px 0px;" src="http://ovrride.com/wp-content/themes/quark/images/shops/homage.jpg" alt="Homage" /></a> 
                <a href="http://www.rei.com/stores/soho.html" target="_blank"><img style="width:40px;height:40px;margin:0px 3px 0px 0px;" src="http://ovrride.com/wp-content/themes/quark/images/shops/rei.jpg" alt="REI" /></a> 
                <a href="http://www.shutnyc.com" target="_blank"><img style="width:40px;height:40px;margin:0px 3px 0px 0px;" src="http://ovrride.com/wp-content/themes/quark/images/shops/shut.jpg" alt="SHUT" /></a> 
            </div>
		</footer>
	</div> <!-- /.footercontainer -->

</div> <!-- /.#wrapper.hfeed.site -->


<?php wp_footer(); ?>
</body>

</html>