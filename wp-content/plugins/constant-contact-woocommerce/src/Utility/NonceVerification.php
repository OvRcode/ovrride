<?php
/**
 * Trait for classes that need to verify a single nonce.
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Utility * @since 2019-03-20
 */

namespace WebDevStudios\CCForWoo\Utility;

/**
 * Trait NonceVerification
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Utility
 * @since   2019-03-20
 */
trait NonceVerification {
	/**
	 * Nonce field name.
	 *
	 * @var string
	 * @since 2019-03-20
	 */
	protected $nonce_name;

	/**
	 * Nonce action name.
	 *
	 * @var string
	 * @since 2019-03-20
	 */
	protected $nonce_action;

	/**
	 * Return whether we have a valid nonce or not.
	 *
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @since  2019-03-15
	 * @return bool
	 */
	protected function has_valid_nonce() : bool {
		return wp_verify_nonce(
			filter_input( INPUT_POST, $this->nonce_name, FILTER_SANITIZE_STRING ),
			$this->nonce_action
		);
	}
}
