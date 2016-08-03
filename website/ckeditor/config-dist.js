/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

var base_url = document.getElementById("base_url").value+"ckeditor/kcfinder/";
CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';
    config.allowedContent = true;
    config.filebrowserBrowseUrl = base_url + 'browse.php?type=files';
    config.filebrowserImageBrowseUrl = base_url + 'browse.php?type=images';
    config.filebrowserFlashBrowseUrl = base_url + 'browse.php?type=flash';
    config.filebrowserUploadUrl = base_url + 'upload.php?type=files';
    config.filebrowserImageUploadUrl = base_url + 'upload.php?type=images';
    config.filebrowserFlashUploadUrl = base_url + 'upload.php?type=flash';
    config.extraPlugins = 'simpleLink,gallery';
};
