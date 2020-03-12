<?php
/**
 * Admin Notice Class
 *
 * This class is responsible for storing and showing notices in the WordPress
 * admin area.
 *
 * @since 0.0.1
 * @author Zach Owen <zach@webdevstudios.com>
 * @package cc-woo
 */

namespace WebDevStudios\CCForWoo\View\Admin;

/**
 * Notice Class
 *
 * @since 0.0.1
 */
class Notice extends NoticeAbstract {
	/**
	 * Transient key used in the database.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	const TRANSIENT_KEY = 'cc-woo-notices';

	/**
	 * Display notices, if there are any.
	 *
	 * This method will also delete the notices transient if they are displayed.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @return void
	 */
	public static function maybe_display_notices() {
		$notices = self::get_notices();

		if ( empty( $notices ) ) {
			return;
		}

		foreach ( $notices as $notice ) {
			$dismissible_css = $notice->is_dismissible() ? 'is-dismissible' : '';
?>
<div class="notice notice-<?php echo esc_attr( $notice->get_class() ); ?> <?php echo esc_attr( $dismissible_css ); ?>">
	<p><?php echo esc_html( $notice->get_message() ); ?></p>
</div>
<?php
		}

		self::delete_notices();
	}
}
