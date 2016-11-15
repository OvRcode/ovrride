<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package _tk
 */
?>
			</div><!-- close .*-inner (main-content or sidebar, depending if sidebar is used) -->
		</div><!-- close .row -->
	</div><!-- close .container -->
</div><!-- close .main-content -->
<div class="container-fluid">
	<div class="col-sm-12 col-md-10 col-md-offset-1">
		<div class="row">
			<div class="col-sm-3 col-xs-6">
				<a class="square-link" href="<?php echo get_option('facebook_url'); ?>" target="_blank">
					<div class="footer-square">
						<div class="footer-square-inner">
							<i class='fa fa-facebook icon' aria-hidden='true' ></i>
							<span class="text">FACEBOOK</span>
						</div>
					</div>
				</a>
			</div>
			<div class="col-sm-3 col-xs-6">
				<a class="square-link" href="<?php echo get_option('instagram_url'); ?>" target="_blank">
					<div class="footer-square">
						<div class="footer-square-inner">
							<i class='fa fa-instagram icon' aria-hidden='true'></i>
							<span class="text">INSTAGRAM</span>
						</div>
					</div>
				</a>
			</div>
			<div class="col-sm-3 col-xs-6">
				<a class="square-link" href="<?php echo get_option('twitter_url'); ?>" target="_blank">
					<div class="footer-square">
						<div class="footer-square-inner">
							<i class='fa fa-twitter icon' aria-hidden='true'></i>
							<span class="text">TWITTER</span>
						</div>
					</div>
				</a>
			</div>
			<div class="col-sm-3 col-xs-6">
				<a class="square-link" href="<?php echo get_option('youtube_url'); ?>" target="_blank">
					<div class="footer-square">
						<div class="footer-square-inner">
							<i class='fa fa-youtube icon' aria-hidden='true'></i>
							<span class="text">YOUTUBE</span>
						</div>
					</div>
				</div>
			</a>
		</div>
		<div class="row">
		<div class="col-sm-3 col-xs-6">
			<div class="footer-square">
				<div class="footer-square-inner">
					<i class="fa fa-envelope icon" aria-hidden="true"></i>
					<span class="text">NEWSLETTER</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 col-xs-6">
			<div class="footer-square">
				<div class="footer-square-inner">
					<i class="fa fa-file-text-o icon" aria-hidden="true"></i>
					<span class="text">ABOUT OVRRIDE</span>
				</div>
			</div>
		</div>
		<div class="col-sm-3 col-xs-6">
			<div class="footer-square">
				<div class="footer-square-inner">
					<i class="fa fa-book icon" aria-hidden="true"></i>
					<span class="text">BOOK A TRIP</span>
			</div>
		</div>
	</div>
	<div class="col-sm-3 col-xs-6">
		<div class="footer-square">
			<div class="footer-square-inner">
				<i class="fa fa-pencil icon" aria-hidden="true"></i>
				<span class="text">CONTACT US</span>
			</div>
		</div>
	</div>
</div>
</div><!-- close .container -->
</div>
<footer id="colophon" class="footer" role="contentinfo">
	<div class="row">
		<div class="col-xs-12">
			<i class="fa fa-copyright" aria-hidden="true"></i><?php echo date("Y"); ?> <a href="http://ovrride.com">OVR RIDE LLC.</a>- ALL RIGHTS RESERVED
		</div>
	</div>
</footer><!-- close #colophon -->

<?php wp_footer(); ?>
</body>
</html>
