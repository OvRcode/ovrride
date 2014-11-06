<?php
class BDWP_HttpHelpers {

    public static function GetProtocol() {
        if (is_ssl()) {
            return 'https://';
        }
        return 'http://';
    }
}
