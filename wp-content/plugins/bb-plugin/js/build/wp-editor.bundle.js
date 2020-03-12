/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 35);
/******/ })
/************************************************************************/
/******/ ({

/***/ 35:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


__webpack_require__(36);

__webpack_require__(37);

__webpack_require__(39);

/***/ }),

/***/ 36:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var registerStore = wp.data.registerStore;


var DEFAULT_STATE = {
	launching: false
};

var actions = {
	setLaunching: function setLaunching(launching) {
		return {
			type: 'SET_LAUNCHING',
			launching: launching
		};
	}
};

var selectors = {
	isLaunching: function isLaunching(state) {
		return state.launching;
	}
};

registerStore('fl-builder', {
	reducer: function reducer() {
		var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : DEFAULT_STATE;
		var action = arguments[1];

		switch (action.type) {
			case 'SET_LAUNCHING':
				state.launching = action.launching;
		}

		return state;
	},

	actions: actions,
	selectors: selectors
});

/***/ }),

/***/ 37:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

__webpack_require__(38);

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var _FLBuilderConfig = FLBuilderConfig,
    builder = _FLBuilderConfig.builder,
    strings = _FLBuilderConfig.strings,
    urls = _FLBuilderConfig.urls;
var _wp$blocks = wp.blocks,
    registerBlockType = _wp$blocks.registerBlockType,
    rawHandler = _wp$blocks.rawHandler,
    serialize = _wp$blocks.serialize;
var _wp$components = wp.components,
    Button = _wp$components.Button,
    Placeholder = _wp$components.Placeholder,
    Spinner = _wp$components.Spinner;
var compose = wp.compose.compose;
var _wp$data = wp.data,
    subscribe = _wp$data.subscribe,
    withDispatch = _wp$data.withDispatch,
    withSelect = _wp$data.withSelect;
var _wp$element = wp.element,
    Component = _wp$element.Component,
    RawHTML = _wp$element.RawHTML;

/**
 * Edit Component
 */

var LayoutBlockEdit = function (_Component) {
	_inherits(LayoutBlockEdit, _Component);

	function LayoutBlockEdit() {
		_classCallCheck(this, LayoutBlockEdit);

		var _this = _possibleConstructorReturn(this, (LayoutBlockEdit.__proto__ || Object.getPrototypeOf(LayoutBlockEdit)).apply(this, arguments));

		_this.unsubscribe = subscribe(_this.storeDidUpdate.bind(_this));
		return _this;
	}

	_createClass(LayoutBlockEdit, [{
		key: 'storeDidUpdate',
		value: function storeDidUpdate() {
			var _props = this.props,
			    isLaunching = _props.isLaunching,
			    isSavingPost = _props.isSavingPost;

			if (isLaunching && !isSavingPost) {
				this.unsubscribe();
				this.redirectToBuilder();
			}
		}
	}, {
		key: 'componentDidMount',
		value: function componentDidMount() {
			var blockCount = this.props.blockCount;

			if (1 === blockCount) {
				this.toggleEditor('disable');
			}
		}
	}, {
		key: 'componentWillUnmount',
		value: function componentWillUnmount() {
			this.unsubscribe();
			this.toggleEditor('enable');
		}
	}, {
		key: 'render',
		value: function render() {
			var _props2 = this.props,
			    blockCount = _props2.blockCount,
			    onReplace = _props2.onReplace,
			    isLaunching = _props2.isLaunching;

			var label = void 0,
			    callback = void 0,
			    description = void 0;

			if (1 === blockCount) {
				label = builder.access ? strings.launch : strings.view;
				callback = this.launchBuilder.bind(this);
			} else {
				label = strings.convert;
				callback = this.convertToBuilder.bind(this);
			}

			if (builder.enabled) {
				description = strings.active;
			} else {
				description = strings.description;
			}

			return React.createElement(
				Placeholder,
				{
					key: 'placeholder',
					instructions: description,
					icon: 'welcome-widgets-menus',
					label: strings.title,
					className: 'fl-builder-layout-launch-view'
				},
				isLaunching && React.createElement(Spinner, null),
				!isLaunching && React.createElement(
					Button,
					{
						isLarge: true,
						isPrimary: true,
						type: 'submit',
						onClick: callback
					},
					label
				),
				!isLaunching && React.createElement(
					Button,
					{
						isLarge: true,
						type: 'submit',
						onClick: this.convertToBlocks.bind(this)
					},
					strings.editor
				)
			);
		}
	}, {
		key: 'toggleEditor',
		value: function toggleEditor() {
			var method = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'enable';
			var classList = document.body.classList;

			var enabledClass = 'fl-builder-layout-enabled';

			if ('enable' === method) {
				if (classList.contains(enabledClass)) {
					classList.remove(enabledClass);
				}
			} else {
				if (!classList.contains(enabledClass)) {
					classList.add(enabledClass);
				}
			}
		}
	}, {
		key: 'redirectToBuilder',
		value: function redirectToBuilder() {
			window.location.href = builder.access ? urls.edit : urls.view;
		}
	}, {
		key: 'launchBuilder',
		value: function launchBuilder() {
			var _props3 = this.props,
			    savePost = _props3.savePost,
			    setLaunching = _props3.setLaunching;

			setLaunching(true);
			savePost();
		}
	}, {
		key: 'convertToBuilder',
		value: function convertToBuilder() {
			var _props4 = this.props,
			    clientId = _props4.clientId,
			    blocks = _props4.blocks,
			    setAttributes = _props4.setAttributes,
			    removeBlocks = _props4.removeBlocks;

			var content = serialize(blocks);
			var clientIds = blocks.map(function (block) {
				return block.clientId;
			}).filter(function (id) {
				return id !== clientId;
			});

			setAttributes({ content: content.replace(/<!--(.*?)-->/g, '') });
			removeBlocks(clientIds);
			this.launchBuilder();
		}
	}, {
		key: 'convertToBlocks',
		value: function convertToBlocks() {
			var _props5 = this.props,
			    attributes = _props5.attributes,
			    clientId = _props5.clientId,
			    replaceBlocks = _props5.replaceBlocks,
			    onReplace = _props5.onReplace;


			if (attributes.content && !confirm(strings.warning)) {
				return;
			} else if (attributes.content) {
				replaceBlocks([clientId], rawHandler({
					HTML: attributes.content,
					mode: 'BLOCKS'
				}));
			} else {
				onReplace([]);
			}
		}
	}]);

	return LayoutBlockEdit;
}(Component);

/**
 * Connect the edit component to editor data.
 */


var LayoutBlockEditConnected = compose(withDispatch(function (dispatch, ownProps) {
	var editor = dispatch('core/editor');
	var builder = dispatch('fl-builder');
	return {
		savePost: editor.savePost,
		removeBlocks: editor.removeBlocks,
		replaceBlocks: editor.replaceBlocks,
		setLaunching: builder.setLaunching
	};
}), withSelect(function (select) {
	var editor = select('core/editor');
	var builder = select('fl-builder');
	return {
		blockCount: editor.getBlockCount(),
		blocks: editor.getBlocks(),
		isSavingPost: editor.isSavingPost(),
		isLaunching: builder.isLaunching()
	};
}))(LayoutBlockEdit);

/**
 * Register the block.
 */
if (builder.access && builder.unrestricted || builder.enabled) {
	registerBlockType('fl-builder/layout', {
		title: strings.title,
		description: strings.description,
		icon: 'welcome-widgets-menus',
		category: 'layout',
		useOnce: true,
		supports: {
			customClassName: false,
			className: false,
			html: false
		},
		attributes: {
			content: {
				type: 'string',
				source: 'html'
			}
		},
		edit: LayoutBlockEditConnected,
		save: function save(_ref) {
			var attributes = _ref.attributes;

			return React.createElement(
				RawHTML,
				null,
				attributes.content
			);
		}
	});
}

/***/ }),

/***/ 38:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 39:
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

__webpack_require__(40);

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _possibleConstructorReturn(self, call) { if (!self) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return call && (typeof call === "object" || typeof call === "function") ? call : self; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function, not " + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var _FLBuilderConfig = FLBuilderConfig,
    strings = _FLBuilderConfig.strings;
var _wp$blocks = wp.blocks,
    createBlock = _wp$blocks.createBlock,
    serialize = _wp$blocks.serialize;
var Button = wp.components.Button;
var compose = wp.compose.compose;
var _wp$data = wp.data,
    withDispatch = _wp$data.withDispatch,
    withSelect = _wp$data.withSelect;
var PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem;
var Component = wp.element.Component;
var registerPlugin = wp.plugins.registerPlugin;

/**
 * Builder menu item for the more menu.
 *
 * More menu items currently only support opening a sidebar.
 * However, we need a click event. For now, that is done in a
 * hacky manner with an absolute div that contains the event.
 * This should be reworked in the future when API supports it.
 */

var BuilderMoreMenuItem = function (_Component) {
	_inherits(BuilderMoreMenuItem, _Component);

	function BuilderMoreMenuItem() {
		_classCallCheck(this, BuilderMoreMenuItem);

		return _possibleConstructorReturn(this, (BuilderMoreMenuItem.__proto__ || Object.getPrototypeOf(BuilderMoreMenuItem)).apply(this, arguments));
	}

	_createClass(BuilderMoreMenuItem, [{
		key: 'render',
		value: function render() {
			return React.createElement(
				PluginSidebarMoreMenuItem,
				null,
				React.createElement('div', {
					className: 'fl-builder-plugin-sidebar-button',
					onClick: this.menuItemClicked.bind(this) }),
				this.hasBuilderBlock() ? strings.launch : strings.convert
			);
		}
	}, {
		key: 'hasBuilderBlock',
		value: function hasBuilderBlock() {
			var blocks = this.props.blocks;

			var builder = blocks.filter(function (block) {
				return 'fl-builder/layout' === block.name;
			});

			return !!builder.length;
		}
	}, {
		key: 'menuItemClicked',
		value: function menuItemClicked() {
			var closeGeneralSidebar = this.props.closeGeneralSidebar;


			if (this.hasBuilderBlock()) {
				this.launchBuilder();
			} else {
				this.convertToBuilder();
			}

			// Another hack because we can't have click events yet :(
			setTimeout(closeGeneralSidebar, 100);
		}
	}, {
		key: 'convertToBuilder',
		value: function convertToBuilder() {
			var _props = this.props,
			    blocks = _props.blocks,
			    insertBlock = _props.insertBlock,
			    removeBlocks = _props.removeBlocks;

			var clientIds = blocks.map(function (block) {
				return block.clientId;
			});
			var content = serialize(blocks).replace(/<!--(.*?)-->/g, '');
			var block = createBlock('fl-builder/layout', { content: content });

			insertBlock(block, 0);
			removeBlocks(clientIds);
		}
	}, {
		key: 'launchBuilder',
		value: function launchBuilder() {
			var _props2 = this.props,
			    savePost = _props2.savePost,
			    setLaunching = _props2.setLaunching;

			setLaunching(true);
			savePost();
		}
	}]);

	return BuilderMoreMenuItem;
}(Component);

/**
 * Connect the menu item to editor data.
 */


var BuilderMoreMenuItemConnected = compose(withDispatch(function (dispatch, ownProps) {
	var editor = dispatch('core/editor');
	var editPost = dispatch('core/edit-post');
	var builder = dispatch('fl-builder');
	return {
		savePost: editor.savePost,
		insertBlock: editor.insertBlock,
		removeBlocks: editor.removeBlocks,
		closeGeneralSidebar: editPost.closeGeneralSidebar,
		setLaunching: builder.setLaunching
	};
}), withSelect(function (select) {
	var editor = select('core/editor');
	return {
		blocks: editor.getBlocks()
	};
}))(BuilderMoreMenuItem);

/**
 * Register the builder more menu plugin.
 */
registerPlugin('fl-builder-plugin-sidebar', {
	icon: 'welcome-widgets-menus',
	render: BuilderMoreMenuItemConnected
});

/***/ }),

/***/ 40:
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ })

/******/ });