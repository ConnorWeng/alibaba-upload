<?php

import('@.Util.Config');
import('@.Model.Sku');

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
        return self::sign(C('app_id'), C('secret_id'), $url, $api . '/' . C('app_id'), $params);
    }

    public static function getAlibabaAuthUrl($state) {
        $appKey = C('app_id');
        $appSecret = C('secret_id');
        $redirectUrl = urlencode(C('host').U(C('redirect_uri')));
        $stateEncoded = urlencode($state);

        $code_arr = array(
            'client_id' => $appKey,
            'site' => 'china',
            'redirect_uri' => C('host').U(C('redirect_uri')),
            'state' => $state);
        ksort($code_arr);
        foreach ($code_arr as $key=>$val)
                $sign_str .= $key . $val;
        $code_sign = strtoupper(bin2hex(hash_hmac("sha1", $sign_str, $appSecret, true)));

        $get_code_url = "http://gw.open.1688.com/auth/authorize.htm?client_id={$appKey}&site=china&state={$stateEncoded}&redirect_uri={$redirectUrl}&_aop_signature={$code_sign}";

        return $get_code_url;
    }

    public static function getTokens($code) {
        $url = 'https://gw.open.1688.com/openapi/http/1/system.oauth2/getToken/'.C('app_id').
            '?grant_type=authorization_code&need_refresh_token=true&client_id='.C('app_id').
            '&client_secret='.C('secret_id').'&redirect_uri='.urlencode(C('host').U(C('redirect_uri'))).
            '&code=' . $code;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data);
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

    public function check($v) {
        if ($v == 'on') {
            return 'true';
        } else {
            return 'false';
        }
    }

}

?>