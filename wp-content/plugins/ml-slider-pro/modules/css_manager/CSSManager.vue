<template>
	<div class="overflow-hidden">
		<h2 class="text-xl m-0 p-4 border-b border-gray-light">{{ __('Add Custom CSS', 'ml-slider-pro') }}</h2>
		<div class="flex flex-col-reverse md:flex-row md:-mx-8 min-h-half">
			<div class="flex flex-grow w-full md:w-2/3 md:pl-8 md:border-r border-gray-light">
				<div class="w-full min-h-full m-4 md:m-0 overflow-scroll">
					<editor
						v-model="content"
						ref="cssModule"
						:options="options"
						@init="editorInit"
						lang="css"
						theme="chrome"
						:height="'100%'"/>
				</div>
			</div>
			<div class="w-full md:w-1/3 p-4 md:px-8 md:py-4 rtl:pr-0 rtl:pr-0 md:border-l border-gray-light">
				<div class="md:pr-8 rtl:pl-8 rtl:pr-0">
					<h3 class="mt-0">{{ __('Welcome to the CSS editor', 'ml-slider') }}</h3>
					<p>{{ __('You may add any CSS styles here that you want to load on pages that have this slideshow.', 'ml-slider-pro') }}</p>
					<p class="mb-0">{{ __('To make a style specific to this slideshow alone, use the following prefix:', 'ml-slider') }}</p>
					<div class="hidden md:block my-8">
						<code @click="addToEditor($event.target.innerHTML + ' {}', true)" class="cursor-pointer tipsy-tooltip-top" :title="__('Click to add', 'ml-slider-pro')">#metaslider-id-{{ currentSlideshowId() }}</code>
					</div>
					<hr class="hidden md:block">
					<div class="hidden md:block mt-4">
						<h3 class="mt-0">{{ __('Quick Recipes', 'ml-slider-pro') }}</h3>
						<p>{{ __('Click on a recipe below to add it to the stylesheet.', 'ml-slider') }}</p>
						<ul class="list-reset mt-4">
							<li
								v-for="(snippet, key) in snippets"
								:key="key">
								<button
									@click="addToEditor(buildSnippet(snippet))"
									class="cursor-pointer text-blue-dark underline">{{ snippet.name }}</button>
							</li>
							<li class="mt-6">{{ __('More recipes coming soon.', 'ml-slider') }}</li>
							<li>{{ __('Please let us know any suggestions you might have.', 'ml-slider') }}</li>
							<li v-html="sprintf(__('If you would like to leave us a 5-star review. Please go <a %s href=\'%s\'>here</a>.', 'ml-slider'), 'target=\'_blank\'', 'https://wordpress.org/support/plugin/ml-slider/reviews/?rate=5#new-post')"/>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="flex justify-end p-4 border-t border-gray-light">
			<button
				@click="save()"
				class="flex items-center bg-blue-dark text-white rounded px-3 py-2"
				:class="{'text-white-40': !hasChanges || saving}"
				:disabled="!hasChanges || saving">
                <svg v-if="saving" class="mr-1 w-4 inline ms-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg v-else class="mr-1 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>

				<template v-if="hasChanges">
					{{ __('Save CSS', 'ml-slider-pro') }}
				</template>
				<template v-else>
					{{ __('Saved', 'ml-slider-pro') }}
				</template>

			</button>
		</div>
	</div>
</template>

<script>
import Ace from 'vue2-ace-editor';
import CSSManager from './routes.js'
import snippets from './snippets.js'
export default {
	filename: 'CSSManager',
	data() {
		return {
			options: {
				highlightActiveLine: true,
				tabSize: 2
			},
			content: '',
			savedContent: '',
			editor: {},
			snippets: {},
			keySequence: false,
			saving: false
		}
	},
	components: {
		editor: Ace
	},
	watch: {
		content() {
			this.$parent.forceOpen = this.hasChanges ? () => this.warnBeforeClose() : false
		}
	},
	computed: {
		hasChanges() {
			return this.content !== this.savedContent
		}
	},
	created() {
		this.snippets = snippets
		this.$parent.classes = 'w-full max-w-6xl'
		this.content = this.savedContent = this.__('Loading...', 'ml-slider-pro')
		CSSManager.getCSS().then(css => {
			this.content = this.savedContent = css
			this.$nextTick(() => {
				this.editor.navigateFileEnd()
				this.editor.focus()
			})
		})
	},
	mounted() {

		// Tab key isn't working, not sure why. This fixes it
		document.addEventListener('keydown', this.tabOverride, true)
		document.addEventListener('keyup', this.keyRelease, true)
	},
	destroyed() {
		document.removeEventListener('keydown', this.tabOverride, true)
		document.removeEventListener('keyup', this.keyRelease, true)
	},
	methods: {
		save() {
			if (!this.hasChanges) return

			// If no changes, reset the warning - The content watcher isn't triggered, and the user might make edits while saving
			this.$parent.forceOpen = false;

			if (this.hasErrors()) {
				this.notifyError('metaslider/extra-css-errors', this.__('The CSS has errors and cannot be saved', 'ml-slider-pro'), true)
				return
			}

			this.saving = true
			this.notifyInfo('metaslider/extra-css-save-success', this.__('Saving CSS', 'ml-slider-pro'), true)
			CSSManager.saveCSS(this.content).then(saved => {
				this.savedContent = saved
				this.notifySuccess('metaslider/saving-extra-css', this.__('CSS file saved', 'ml-slider-pro'), true)
				this.saving = false
			})
		},
		editorInit(editor) {
			require('brace/theme/chrome')
			require('brace/mode/css')
			this.editor = editor

			// Add a standard code editor keyboard shortcut
			this.editor.commands.addCommand({
				name: 'save',
				bindKey: {win: "Ctrl-S", "mac": "Cmd-S"},
				exec: editor => {
					this.save()
				}
			})

		},
		addToEditor(string, force = false) {
			// If the snippet is already there, highlight it instead of adding it again
			let range = this.editor.find(string)
			if (range) {
				this.editor.session.addMarker(range, "blue-highlight", "text", false)
				// setTimeout(() => {
					// this.editor.clearSelection()
				// }, 1000);
				this.editor.focus()
				if (!force) return
			}

			// 1. Go to the end of the file
			this.editor.navigateFileEnd()
			// 2. Make sure it's an empty line
			let currentLine = this.editor.getSelectionRange().start.row
			if (this.editor.session.getLine(currentLine)) {
				this.editor.insert('\n')
			}
			// 3. Insert the snippet
			this.editor.insert(string)
			this.editor.focus()
			this.editor.scrollPageDown()
		},
		buildSnippet(snippet) {

			// Add the description as an inline comment
			let string = snippet.description ? `/* ${snippet.name} - ${snippet.description} */\n${snippet.snippet}` : snippet.snippet

			// If needed, we can add more items to filter specific to this slideshow
			return string.replace('${id}', '#metaslider-id-' + this.currentSlideshowId())
		},
		addSnippet() {
			// TODO: Let users add their own reusable snippets. I think when some code
			// TODO: is highlighted an option shows to save it as a snippet. Watch out for
			// TODO: the mechinism that hightlights code that already exists. Users shoudln't
			// TODO: be able to add snippets that already exist
		},
		tabOverride(event) {
			if (!this.keySequence && 9 === event.keyCode) {
				this.editor.execCommand("indent")
				return
			}

			// Update the key sequence in case this is a combo
			this.keySequence = true
		},
		keyRelease() {
			// Remove the sequence unless it was the tab key released
			if (9 === event.keyCode) return

			this.keySequence = false
		},
		hasErrors() {
			return this.editor.session.getAnnotations().filter(message => {
				return 'error' === message.type
			}).length
		},
		warnBeforeClose() {
			this.notifyWarning('metaslider/close-modal-extra-css-error', this.__('You have unsaved changes. Press again to discard.', 'ml-slider-pro'), true)
		}
	}
}
</script>
