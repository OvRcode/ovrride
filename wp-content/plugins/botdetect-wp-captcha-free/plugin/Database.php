<?php
require_once( ABSPATH . 'wp-load.php' );

class BDWP_Database {

    public static $wpdb;
    public static $TableName;

    public function __construct() {
        global $wpdb;
        self::$wpdb = $wpdb;
        self::$TableName = 'botdetect';
    }

    public static function TableExists() {
        return (self::$wpdb->get_var("SHOW TABLES LIKE '" . self::$TableName . "'") !== null)? true : false;
    }

    public static function GetBotDetectOption($p_Option) {

        $p_Option = trim($p_Option);
        if (empty($p_Option)) {
            return false;
        }

        if (self::TableExists()) {
            $value = self::$wpdb->get_var( self::$wpdb->prepare( "SELECT botdetect_value FROM " . self::$TableName . " WHERE botdetect_name = %s ORDER BY botdetect_id DESC LIMIT 1", $p_Option) );

            if ($value == null) {
                return false;
            } else {
                return self::CanBeUnserialized($value);
            }
        } else {
            return false;
        }
    }

    public static function CanBeUnserialized($p_Data) {
        if (@unserialize($p_Data) !== false) {
            $p_Data = @unserialize($p_Data);
        }
        return $p_Data;
    }

    public static function DeleteBotDetectTable() {
        $tableName = self::$TableName;
        $result = self::$wpdb->query( "DROP TABLE IF EXISTS $tableName" );
        return (!$result)? false : true;
    }
}

new BDWP_Database();
