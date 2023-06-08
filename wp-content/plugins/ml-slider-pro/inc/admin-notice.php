<?php

/**
 * Class to relay messages to the WP admin notice action
 * Usage:
 * new Metaslider_Admin_Notice('notice-error', 'Something went wrong');
 */
class Metaslider_Admin_Notice
{
    /**
     * Should use WP class names
     *
     * @var string $type The type of notice
     */
    public $type;

    /**
     * The message to be displayed
     *
     * @var string $message The message to be displayed
     */
    public $message;

    /**
     * Method to add a message to the admin_notices stack
     *
     * @param string $type The WP css class name to be used
     * @param string $message The message to be displayed
     */
    public function __construct($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
        add_action('admin_notices', array($this, 'display'));
    }

    /**
     * Public method to be called by WP
     */
    public function display()
    {
        printf(
            '<div style="padding-top:1rem;padding-bottom:1rem" class="metaslider-admin-notice %s notice">%s</div>',
            esc_attr($this->type),
            $this->message // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        );
    }
}
