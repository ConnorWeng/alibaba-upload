<?php

require_once 'Config.class.php';
require_once 'Util.class.php';

class OpenAPI {

    public static function offerNewURL($offer, $timestamp, $accessToken) {
        $offerEncoded = urlencode($offer);
        $api = 'param2/1/cn.alibaba.open/offer.new';
        $url = Config::get('open_url').'/'.$api.'/'.Config::get('app_id').'?'.'offer='.$offerEncoded.
               '&_aop_timestamp='.$timestamp.'&access_token='.$accessToken.'&_aop_signature='.
               Util::signDefault(Config::get('open_url'), $api, array('offer' => $offer,
                                                                      '_aop_timestamp' => $timestamp,
                                                                      'access_token' => $accessToken));
        return $url;
    }

    public static function offerNew($offer, $timestamp, $accessToken) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::offerNewURL($offer, $timestamp, $accessToken));
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data);
    }
    
}

?>