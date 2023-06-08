import CSSManager from './CSSManager.vue'

if (!window.metaslider.eventHooks)
	window.metaslider.eventHooks = {}

Object.assign(window.metaslider.eventHooks, {
	'metaslider/open-css-managaer': () => {
		window.metaslider.app.EventManager.$emit('metaslider/open-utility-modal', CSSManager)
	}
})

export { CSSManager }
