function init() {
    tinyMCEPopup.resizeToInnerSize();
}

function insertenjoyinstagramshortcode() {
    var tagtext;
    var shortcode = jQuery('input[name=newshortcode]:checked').val();

    tagtext = "[" + shortcode + "]";

   if(window.tinyMCE) {

    /* get the TinyMCE version to account for API diffs */
    var tmce_ver=window.tinyMCE.majorVersion;

    if (tmce_ver>="4") {
        window.tinyMCE.execCommand('mceInsertContent', false, tagtext);
    } else {
        window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
    }

    tinyMCEPopup.editor.execCommand('mceRepaint');
    tinyMCEPopup.close();
    }
    return;
}
