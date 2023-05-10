jQuery(document).ready(function($) {
    $('.sm-vote-action').on('click', function(e) {
        var $this = $(this);
        e.preventDefault();
        $.ajax({
            type: 'get',
            url: $this.attr('href'),
            beforeSend: function() {
                $this.remove();
                if( !$('.sm-vote-button').length || $this.hasClass('sm-cancel-button') ) {
                    $('.sm-vote').slideUp();
                }
                if (typeof $this.data('action') !== 'undefined') window.open($this.data('action'));
            },
            success: function(data) {}
        });
    });
});