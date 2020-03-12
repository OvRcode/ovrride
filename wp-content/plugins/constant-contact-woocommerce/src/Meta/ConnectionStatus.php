<?php
/**
 * Class to manage details around the CC-Woo connection status.
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Meta
 * @since   2019-03-18
 */

namespace WebDevStudios\CCForWoo\Meta;

/**
 * Class PluginOption
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Meta
 * @since   2019-03-18
 */
class ConnectionStatus {
	/**
	 * Meta key for the connection status option.
	 */
	const CC_CONNECTION_ESTABLISHED_KEY = 'cc_woo_import_connection_established';

	/**
	 * Meta key for the User ID of the successful connection.
	 */
	const CC_CONNECTION_USER_ID = 'cc_woo_api_user_id';

	/**
	 * First connection established key.
	 *
	 * @since 2019-05-30
	 * @var string
	 */
	const CC_FIRST_CONNECTION = 'cc_woo_first_connection';

	/**
	 * Value to check whether the store has attempted a connection with CC.
	 *
	 * @var bool
	 * @since 2019-03-21
	 */
	private $attempted;

	/**
	 * Whether the store is connected to CC.
	 *
	 * @var bool
	 * @since 2019-03-21
	 */
	private $connected;

	/**
	 * Determine whether a connection was attempted.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 * @return bool
	 */
	public function connection_was_attempted() {
		if ( is_null( $this->attempted ) ) {
			$status          = get_option( self::CC_CONNECTION_ESTABLISHED_KEY, null );
			$this->attempted = ! is_null( $status );
			$this->connected = (bool) $status;
		}

		return $this->attempted;
	}

	/**
	 * Check whether a connection to CC has been made.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 * @return bool
	 */
	public function is_connected() : bool {
		return $this->connection_was_attempted() && $this->connected;
	}

	/**
	 * Set the connection status.
	 *
	 * @param int $connected Connected state.
	 * @param int $user_id   Constant Contact User ID for the connection.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-21
	 */
	public function set_connection( int $connected, int $user_id ) {
		$this->connected = $connected;

		update_option( self::CC_CONNECTION_ESTABLISHED_KEY, $connected );
		update_option( self::CC_CONNECTION_USER_ID, $user_id );

		if ( $this->connected ) {
			update_option( self::CC_FIRST_CONNECTION, true );
		}
	}
}
