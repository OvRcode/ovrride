import { Axios as api } from '../../routes'

const CSSManager = {
	saveCSS(css) {
		return api.post('slideshow/extra-css', {
			slideshow_id: window.metaslider.app.MetaSlider.currentSlideshowId(),
			css: css,
			action: 'ms_save_extra_css'
		}).then(({ data }) => {
			return data.data
		})
		.catch(error => {
			this.notifyError('metaslider/save-css-error', error.response.data.data.message, true)
			return false
		})
	},
	getCSS() {
		return api.get('slideshow/extra-css', {
			params: {
				slideshow_id: window.metaslider.app.MetaSlider.currentSlideshowId(),
				action: 'ms_get_extra_css'
			}
		}).then(({ data }) => {
			return data.data
		}).catch(error => {
			this.notifyError('metaslider/get-css-error', error.response.data.data.message, true)
			return ''
		})
	}
}

export default CSSManager
