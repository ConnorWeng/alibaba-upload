<?php

require_once 'Config.class.php';

class Util {

    public static function sign($appKey, $appSecret, $url, $apiInfo, $params) {
        ksort($params);
        foreach ($params as $key=>$val) {
            $signStr .= $key . $val;
        }
        $signStr = $apiInfo . $signStr;
        $codeSign = strtoupper(bin2hex(hash_hmac("sha1", $signStr, $appSecret, true)));
        
        return $codeSign;
    }

    public static function signDefault($url, $api, $params) {
        return self::sign(Config::get('app_id'), Config::get('secret_id'), $url, $api . '/' . Config::get('app_id'), $params);
    }

    
}

?>