<?php
class BDWP_HttpHelpers {

    public static function GetProtocol() {
    	$protocol = 'http://';
        if (is_ssl()) {
            $protocol = 'https://';
        }
        return $protocol;
    }
}
