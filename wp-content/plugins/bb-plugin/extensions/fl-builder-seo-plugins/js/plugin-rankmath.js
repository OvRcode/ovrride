(function($){
	var RankMathIntegration = function() {
			this.hooks()
		}

		RankMathIntegration.prototype.hooks = function() {
			RankMathApp.registerPlugin( 'bb-seo' )
			wp.hooks.addFilter( 'rank_math_content', 'bb-seo', function(content) {
				return window.bb_seo_data.content;
			} )
		}

		RankMathIntegration.prototype.getContent = function( content ) {
			return window.bb_seo_data.content;
		}

		$( document ).ready( function () {
			new RankMathIntegration()
	})
})(jQuery);
