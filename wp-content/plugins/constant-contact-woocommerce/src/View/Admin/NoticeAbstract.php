<?php
/**
 * Admin notice abstract class.
 *
 * Used to store and retrieve notices from the database using transients. Define
 * a key by overriding `self::TRANSIENT_KEY`.
 *
 * @since 0.0.1
 * @package wds-view
 */

namespace WebDevStudios\CCForWoo\View\Admin;

/**
 * Notice Abstract
 *
 * @since 0.0.1
 */
abstract class NoticeAbstract {
	/**
	 * The transient key to store notices under.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	const TRANSIENT_KEY = '';

	/**
	 * Array of notices that are queued to display.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	protected static $queued_notices = [];

	/**
	 * Should implement displaying notices.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	abstract public static function maybe_display_notices();

	/**
	 * Class constructor.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @param \WebDevStudios\CCForWoo\View\Admin\NoticeMessage $message The message to display.
	 */
	public function __construct( NoticeMessage $message ) {
		self::$queued_notices[] = $message;
	}

	/**
	 * Store admin notices in the database.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 */
	public static function set_notices() {
		set_transient( static::TRANSIENT_KEY, self::$queued_notices );
	}

	/**
	 * Delete notices from the database.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	protected static function delete_notices() {
		delete_transient( static::TRANSIENT_KEY );
	}

	/**
	 * Get notices from the database.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @return array
	 */
	public static function get_notices() : array {
		return get_transient( static::TRANSIENT_KEY ) ?: [];
	}
}
