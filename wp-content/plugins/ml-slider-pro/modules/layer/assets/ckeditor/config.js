/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.editorConfig = function( config ) {
	config.extraPlugins = 'sharedspace,font,justify,sourcedialog,colorbutton,wpmedialibrary';
	config.sharedSpaces = {
		top : 'wysiwyg_buttons'
	};

	config.allowedContent = true;
	config.defaultLanguage = 'en';

	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'paragraph', groups: ['align', 'font'] },
		{ name: 'links' },
		{ name: 'font' },
		{ name: 'basicstyles' },
		{ name: 'document', groups: [ 'mode' ] },
		{ name: 'colors' },
		{ name: 'others' },
		{ name: 'insert' },
        { name: 'styles' }
	];

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript,Table,HorizontalRule,RemoveFormat,Strike,Anchor,SpecialChar,BlockQuote,Smiley,PageBreak,NewPage,Flash,IFrame,Styles,JustifyBlock';

	// Se the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Make dialogs simpler.
	config.removeDialogTabs = 'image:advanced;link:advanced';

	config.font_names = 'Arial/Arial, Helvetica, sans-serif;' +
		'Comic Sans MS/Comic Sans MS, cursive;' +
		'Courier New/Courier New, Courier, monospace;' +
		'Georgia/Georgia, serif;' +
		'Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif;' +
		'Tahoma/Tahoma, Geneva, sans-serif;' +
		'Times New Roman/Times New Roman, Times, serif;' +
		'Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;' +
		'Verdana/Verdana, Geneva, sans-serif;' + metasliderpro.ck_editor_font_list;

	config.colorButton_colors = 'FFF,000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA' + metasliderpro.ck_editor_colors;

	config.fontSize_sizes = '8/0.5em;10/0.625em;12/0.75em;14/0.875em;16/1em;20/1.25em;24/1.5em;30/1.875em;38/2.375em;42/2.625em;48/3em;72/4.5em;' + metasliderpro.ck_editor_font_sizes;

	CKEDITOR.dtd.$removeEmpty.i = 0;
	config.autoParagraph = false; // stops automatic insertion of <p> on focus

};
