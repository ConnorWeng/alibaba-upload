<?php

require_once 'Config.class.php';
require_once 'Sku.class.php';

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

    public static function authSign($state) {
        $appKey = Config::get('app_id');
        $appSecret = Config::get('secret_id');
        $redirectUrl = urlencode(Config::get('redirect_uri'));
        $stateEncoded = urlencode($state);

        $code_arr = array(
            'client_id' => $appKey,
            'site' => 'china',
            'redirect_uri' => Config::get('redirect_uri'),
            'state' => $state);
        ksort($code_arr);
        foreach ($code_arr as $key=>$val)
                $sign_str .= $key . $val;
        $code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $appSecret, true)));

        $get_code_url = "http://gw.open.1688.com/auth/authorize.htm?client_id={$appKey}&site=china&state={$stateEncoded}&redirect_uri={$redirectUrl}&_aop_signature={$code_sign}";

        return $get_code_url;
    }

    public static function parseSkus($skus) {
        $parsedSkus = array();
        $count = count($skus);
        for ($i = 0; $i < $count; $i += 1) {
            array_push($parsedSkus, new Sku(self::extractValue($skus[$i]->properties_name->asXML()),
                self::extractValue($skus[$i]->price->asXML()),
                self::extractValue($skus[$i]->quantity->asXML())));
        }
        return $parsedSkus;
    }

    private static function extractValue($xml) {
        $s1 = substr($xml, stripos($xml, '>') + 1);
        $s2 = substr($s1, 0, stripos($s1, '<'));
        return $s2;
    }

}

?>