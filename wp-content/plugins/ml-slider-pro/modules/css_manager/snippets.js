import { __ } from '@wordpress/i18n'

export default {
	'mobile-only' : {
		name: __('Hide on desktop', 'ml-slider-pro'),
		description: __('Display the slideshow on mobile only', 'ml-slider-pro'),
		snippet: '\
\@media (min-width: 768px) {\n\
	${id} {\n\
		display: none !important;\n\
	}\n\
}\n'
	},
	'desktop-only' : {
		name: __('Hide on mobile', 'ml-slider-pro'),
		description: __('Display the slideshow on desktop only', 'ml-slider-pro'),
		snippet: '\
\@media (max-width: 767px) {\n\
	${id} {\n\
		display: none !important;\n\
	}\n\
}\n'
	}
}