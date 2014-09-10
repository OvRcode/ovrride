
;(function($) {
    $(document).ready(function() {
        // Tie upload feature to button
        $('#upload_css_button').click(function(ev) {
            var field = $('#upload_css').attr('name');
            tb_show('', 'media-upload.php?type=file&amp;TB_iframe=true');
            ev.preventDefault();
            return false;
        });

        /* Toggle the custom CSS row of the table depending on the value
         * selected in the theme dropdown.
         */
        var showHideCustomRow = function(ev) {
            var _this = $('#jquery_ui_theme');
            var jquery_ui_theme = $(_this).find(":selected").attr('value');

            if ('custom' == jquery_ui_theme) {
                $('#custom-css-row').show('fast');
            }
            else {
                $('#custom-css-row').hide('fast');
            }
        };
        $('#jquery_ui_theme').change(showHideCustomRow);
        showHideCustomRow();
    });

    window.send_to_editor = function(html) {
        var url = $(html).attr('href');
        $('#upload_css').val(url);
        tb_remove();
    }
})(jQuery);

