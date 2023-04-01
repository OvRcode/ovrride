<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package _tk
 */
 $theme_dir = get_template_directory_uri();
?>
			</div><!-- close .*-inner (main-content or sidebar, depending if sidebar is used) -->
		</div><!-- close .row -->
	</div><!-- close .container -->
</div><!-- close .main-content -->
<div class="container-fluid footer-square-container">
	<div class="col-sm-12 col-md-10 col-md-offset-1">
    <div class="row">
      <div class="col-xs-12 col-sm-6">
        <div class="col-xs-3 col-sm-3">
          <a class="square-link" href="<?php echo get_option('facebook_url'); ?>" target="_blank">
  					<div class="footer-square">
  						<div class="footer-square-inner">
  							<i class='fa fa-facebook icon' aria-hidden='true' ></i>
  							<span class="text">FACEBOOK</span>
  						</div>
  					</div>
  				</a>
        </div>
        <div class="col-xs-3 col-sm-3">
          <a class="square-link" href="<?php echo get_option('instagram_url'); ?>" target="_blank">
  					<div class="footer-square">
  						<div class="footer-square-inner">
  							<i class='fa fa-instagram icon' aria-hidden='true'></i>
  							<span class="text">INSTAGRAM</span>
  						</div>
  					</div>
  				</a>
        </div>
        <div class="col-xs-3 col-sm-3">
          <a class="square-link" href="<?php echo get_option('twitter_url'); ?>" target="_blank">
  					<div class="footer-square">
  						<div class="footer-square-inner">
  							<i class='fa fa-twitter icon' aria-hidden='true'></i>
  							<span class="text">TWITTER</span>
  						</div>
  					</div>
  				</a>
        </div>
        <div class="col-xs-3 col-sm-3">
          <a class="square-link" href="<?php echo get_option('youtube_url'); ?>" target="_blank">
  					<div class="footer-square">
  						<div class="footer-square-inner">
  							<i class='fa fa-youtube icon' aria-hidden='true'></i>
  							<span class="text">YOUTUBE</span>
  						</div>
  					</div>
          </a>
  			</div>

      </div>
      <div class="col-sm-6 col-xs-12">
        <div class="col-xs-3 col-sm-3">
          <a class="square-link" target="_blank" href="https://visitor.r20.constantcontact.com/d.jsp?llr=oc9qxciab&amp;p=oi&amp;m=1108193339470&amp;sit=acf46lmgb&amp;f=032f36cb-3cd6-4a22-babf-0f7d1699e710">
    				<div class="footer-square">
    					<div id="newsletter" class="footer-square-inner">
    						<i class="fa fa-envelope icon" aria-hidden="true"></i>
    						<span class="text">NEWSLETTER</span>
    					</div>
    				</div>
    			</a>
        </div>
        <div class="col-xs-3 col-sm-3">
          <div id="about" class="footer-square">
    				<div class="footer-square-inner">
    					<i class="fa fa-file-text-o icon" aria-hidden="true"></i>
    					<span class="text">ABOUT OVRRIDE</span>
    				</div>
    			</div>
        </div>
        <div class="col-xs-3 col-sm-3">
          <div class="footer-square">
    				<a href="/shop">
    				  <div class="footer-square-inner">
    					  <i class="fa fa-book icon" aria-hidden="true"></i>
    					  <span class="text">BOOK A TRIP</span>
    			    </div>
            </a>
          </div>
        </div>
        <div class="col-xs-3 col-sm-3">
          <div class="footer-square">
      			<a href="/contact-us"
      			<div class="footer-square-inner">
      				<i class="fa fa-pencil icon" aria-hidden="true"></i>
      				<span class="text">CONTACT US</span>
      			</div>
      		</a>
      		</div>
        </div>
      </div>
    </div>

	<div class="col-sm-12">
		<div class="aboutOvR">
			<i class="fa fa-times fa-2x" aria-hidden="true"></i>
				<?php echo get_option('about_ovr');?>
		</div>
	</div>
</div>
</div><!-- close .container -->
</div>
<footer id="colophon" class="footer" role="contentinfo">
	<div class="row ovr-sponsors">
		<div class="col-xs-4 col-sm-2 col-xs-offset-1">
			<a href="http://www.arcteryx.com" class="ovr_sponsor" target="_blank">
				<img src="<?php echo $theme_dir . "/includes/images/arcteryx.png"; ?>">
			</a>
		</div>
		<div class="col-xs-4 col-sm-2">
			<a href="https://www.dangshades.com/" class="ovr_sponsor" target="_blank">
				<img src="<?php echo $theme_dir . "/includes/images/DangShades_logo_OvR_footer-80x80.png"; ?>">
			</a>
		</div>
		<div class="col-xs-4 col-sm-2">
			<a href="http://www.burton.com" class="ovr_sponsor" target="_blank">
				<img src="<?php echo $theme_dir . "/includes/images/Burton_Site.png"; ?>">
			</a>
		</div>
		<div class="col-xs-4 col-sm-2">
			<a href="http://www.patagonia.com/home/" class="ovr_sponsor" target="_blank">
				<img src="<?php echo $theme_dir . "/includes/images/patagonia.png"; ?>">
			</a>
		</div>
		<div class="col-xs-4 col-sm-2">
			<a href="https://discoverpacifico.com/" class="ovr_sponsor" target="_blank">
				<img src="<?php echo $theme_dir . "/includes/images/Pacifico_Site.png"; ?>">
			</a>
		</div>
	</div>
  <div class="row ovr-sponsors">
    <div class="col-xs-4 col-sm-2 col-xs-offset-1">
			<a href="https://www.thenorthface.com/" class="ovr_sponsor" target="_blank">
				<img src="<?php echo $theme_dir ."/includes/images/tnf.png"; ?>">
			</a>
		</div>
    <div class="col-xs-4 col-sm-2">
      <a href="https://www.onepercentfortheplanet.org/" class="ovr_sponsor" target="_blank">
        <img src="<?php echo $theme_dir . "/includes/images/one_percent.png"; ?>">
      </a>
    </div>
    <div class="col-xs-4 col-sm-2">
      <a href="https://www.cutwaterspirits.com/" class="ovr_sponsor" target="_blank">
        <img src="<?php echo $theme_dir . "/includes/images/Cutwater-Spirits-Logo-80x80.jpg"; ?>">
      </a>
    </div>
    <div class="col-xs-4 col-sm-2">
      <a href="https://nutrlusa.com/"  class="ovr_sponsor" target="_blank">
        <img src="<?php echo $theme_dir . "/includes/images/Nutrl_logo_OvR_footer-80x80.png"; ?>" >
      </a>
    </div>
    <div class="col-xs-4 col-sm-2">
      <a href="https://austineastciders.com/" class="ovr_sponsor" target="_blank" >
        <img src="<?php echo $theme_dir . "/includes/images/austinEastCiders.png"; ?>" >
      </a>
    </div>
  </div>
	<div class="row ovr-footer-links">
		<div class="col-xs-10 col-xs-offset-1">
				<a href="/">Home</a> | <a href="/blog">Blog</a> | <a href="/waiver/">Waiver</a> |
				<a href="/terms-and-conditions">Terms &amp; Conditions</a> | <a href="https://ovrride.zendesk.com/hc/en-us">FAQ</a>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<i class="fa fa-copyright" aria-hidden="true"></i><?php echo date("Y"); ?> <a href="http://ovrride.com">OVR RIDE LLC.</a>- ALL RIGHTS RESERVED
		</div>
	</div>
</footer><!-- close #colophon -->

<?php wp_footer(); ?>
</body>
</html>
