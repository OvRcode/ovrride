<?php
/**
 * Interface for objects that need to register themselves with WordPress.
 *
 * Objects may often use this interface in conjunction with the Hookable interface. For instance, a Custom Post Type
 * gets registered on the `init` action. An object that registers the custom post type might register its own hooks,
 * and also use this interface as the callback for registering itself.
 *
 * @see     \WebDevStudios\OopsWP\Utility\Hookable
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\OopsWP\Utility
 * @since   1.0.0
 */

namespace WebDevStudios\OopsWP\Utility;

/**
 * Interface Registerable
 *
 * @package WebDevStudios\OopsWP\Utility
 * @since   1.0.0
 */
interface Registerable {
	/**
	 * Register this object with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function register();
}
