<?php
/**
 * Notice Message Class
 *
 * Create an admin notice suitable for passing to a Notice class.
 *
 * @since 0.0.1
 * @package cc-woo
 */

namespace WebDevStudios\CCForWoo\View\Admin;

/**
 * Notice Message
 *
 * @since 0.0.1
 */
class NoticeMessage {
	/**
	 * Message to display in the WordPress admin.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	protected $message = '';

	/**
	 * Optional notification class to set in the admin.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	protected $class = '';

	/**
	 * Whether the notice can be dismissed.
	 *
	 * @since 0.0.1
	 * @var bool
	 */
	protected $is_dismissible = false;

	/**
	 * Create a new message.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $message        The message to display.
	 * @param string $class          The CSS class to apply to the message.
	 * @param bool   $is_dismissible Whether to allow dismissing the notice.
	 */
	public function __construct( string $message, string $class = '', bool $is_dismissible = false ) {
		$this->message        = $message;
		$this->class          = $class;
		$this->is_dismissible = $is_dismissible;
	}

	/**
	 * Get the current message.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @return string
	 */
	public function get_message() : string {
		return $this->message;
	}

	/**
	 * Get the CSS class.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @return string
	 */
	public function get_class() : string {
		return $this->class;
	}

	/**
	 * Return whether the notice can be dismissed.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	public function is_dismissible() : bool {
		return $this->is_dismissible;
	}
}
