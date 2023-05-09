<?php

/**
 * Class to relay messages to the WP admin notice action
 * Usage:
 * new Metaslider_Admin_Notice('notice-error', 'Something went wrong');
 */
class Metaslider_Admin_Notice {
    /**
     * @var string $type The type of notice
     * Should use WP class names
     */
    public $type;

    /**
     * @var string $message The message to be displayed
     */
    public $message;
    
    /**
     * Method to add a message to the admin_notices stack
     * @param string $type The WP css class name to be used
     * @param string $message The message to be displayed
     */
    public function __construct($type, $message) {
        $this->type = $type;
        $this->message = $message;
        add_action('admin_notices', array($this, 'display'));
    }

    /**
     * Public method to be called by WP
     */
    public function display() {
        printf('<div class="%s notice"><p>%s</p></div>', $this->type, $this->message);
    }
}