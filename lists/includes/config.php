<?php
/**
 * OvR Lists - The base configurations
 *
 * @package OvR Lists
 * @since Version 0.0.2
 */

# MySQL Database Connection
define("DB_HOST", getenv('MYSQL_HOST'));
define("DB_USER", getenv('MYSQL_USER'));
define("DB_PASS", getenv('MYSQL_PASS'));
define("DB_NAME", getenv('MYSQL_DB'));
?>
