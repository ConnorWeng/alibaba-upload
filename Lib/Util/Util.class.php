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
        return self::sign(session('alibaba_app_key'), session('alibaba_secret_key'), $url, $api . '/' . session('alibaba_app_key'), $params);
    }

    public static function getAlibabaAuthUrl($state) {
        $appKey = session('alibaba_app_key');
        $appSecret = session('alibaba_secret_key');
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
        $url = 'https://gw.open.1688.com/openapi/http/1/system.oauth2/getToken/'.session('alibaba_app_key').
            '?grant_type=authorization_code&need_refresh_token=true&client_id='.session('alibaba_app_key').
            '&client_secret='.session('alibaba_secret_key').'&redirect_uri='.urlencode(C('host').U(C('redirect_uri'))).
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

    public static function parseItemImgs($itemImgs) {
        $parsedItemImgs = array();
        $count = count($itemImgs);
        for ($i = 0; $i < $count; $i += 1) {
            array_push($parsedItemImgs, self::extractValue($itemImgs[$i]->url->asXML()));
        }
        return $parsedItemImgs;
    }

    public static function extractValue($xml) {
        $s1 = substr($xml, stripos($xml, '>') + 1);
        $s2 = substr($s1, 0, stripos($s1, '<'));
        return $s2;
    }

    public static function check($v) {
        if ($v == 'on') {
            return 'true';
        } else {
            return 'false';
        }
    }

    public static function changeTaoAppkey($taobaoItemId, $oldAppKey = null) {
        $taoapi = D('Taoapi');
        $taoappkey = $taoapi->getAppKey($taobaoItemId, $oldAppKey);
        session('taobao_app_key', $taoappkey['appkey']);
        session('taobao_secret_key', $taoappkey['appsecret']);
        session('current_taobao_app_key_id', $taoappkey['id']);
    }

    public static function changeAliAppkey($taobaoItemId, $oldAppKey = null) {
        $aliapi = D('Aliapi');
        $aliappkey = $aliapi->getAppKey($taobaoItemId, $oldAppKey);
        session('alibaba_app_key', $aliappkey['appkey']);
        session('alibaba_secret_key', $aliappkey['appsecret']);
        session('current_alibaba_app_key_id', $aliappkey['id']);
        session('access_token', null);
    }

}

?>