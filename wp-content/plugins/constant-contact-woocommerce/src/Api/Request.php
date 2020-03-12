<?php
/**
 * Abstract class for API request objects.
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Api
 * @since   2019-03-07
 */

namespace WebDevStudios\CCForWoo\Api;

/**
 * Class Request
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Api
 * @since   2019-03-07
 */
abstract class Request {
	/**
	 * Structured request data.
	 *
	 * @var array
	 * @since 2019-03-07
	 */
	protected $data = [];

	/**
	 * Prepare the request data.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 */
	abstract public function prepare_data();
}
