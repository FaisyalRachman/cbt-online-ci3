/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'insert' },
		
		{ name: 'styles' },
		{ name: 'colors' },
	];
	
	config.startupFocus = true;
	config.removePlugins = 'easyimageupload,easyimage,gambar,image,uploadimage,horizontalrule';
	config.extraPlugins = 'pastebase64,eqneditor,mathjax,imageku,imageresizerowandcolumn,justify';
};
