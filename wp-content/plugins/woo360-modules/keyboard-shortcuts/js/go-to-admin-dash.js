(function( $ ) {
    // Create a function to fire when your custom shortcut is triggered
    function goToAdminDash() {
        // Work done here
        window.open(window.location.origin + "/wp-admin/index.php", "_blank");
    }
    $(function() { // Once the document is ready
        // Register a hook listener using the key that you registered 
        // your shortcut with along with the function it should fire.
        FLBuilder.addHook('goToAdminDash', goToAdminDash );
    });
})( jQuery );