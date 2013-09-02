<?php

require_once 'Config.class.php';
require_once 'Util.class.php';

require_once 'taobao-sdk/TopSdk.php';

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

        var_dump($data);

        return json_decode($data);
    }

    public static function getTaobaoItem($numIid) {
        $c = new TopClient;
        $c->appkey = Config::get('taobao_app_key');
        $c->secretKey = Config::get('taobao_secret_key');
	$req = new ItemGetRequest;
	$req->setFields("detail_url,num_iid,title,nick,type,cid,seller_cids,props,input_pids,input_str,desc,pic_url,num,valid_thru,list_time,delist_time,stuff_status,location,price,post_fee,express_fee,ems_fee,has_discount,freight_payer,has_invoice,has_warranty,has_showcase,modified,increment,approve_status,postage_id,product_id,auction_point,property_alias,item_img,prop_img,sku,video,outer_id,is_virtual");
	$req->setNumIid($numIid);
	$resp = $c->execute($req, null);

        return $resp->item;
    }
    
}

?>