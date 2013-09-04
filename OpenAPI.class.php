<?php

require_once 'Config.class.php';
require_once 'Util.class.php';

require_once 'taobao-sdk/TopSdk.php';

class OpenAPI {

    public static function offerNew($offer) {
        $api = 'param2/1/cn.alibaba.open/offer.new';
        return self::callOpenAPI($api, array('offer' => urlencode($offer)));
    }

    public static function categorySearch($keyWord) {
        $api = 'param2/1/cn.alibaba.open/category.search';
        return self::callOpenAPI($api, array('keyWord' => $keyWord));
    }

    public static function getPostCatList($catIDs) {
        $api = 'param2/1/cn.alibaba.open/category.getPostCatList';
        return self::callOpenAPI($api, array('catIDs' => $catIDs));
    }

    private static function callOpenAPI($api, $params) {
        $url = self::makeUrl($api, $params);
        $data = self::sendRequest($url);
        return json_decode($data);
    }

    private static function sendRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    private static function makeUrl($api, $paramsArray) {
        $timestamp = time() * 1000;
        $accessToken = $_SESSION['access_token'];
        $params = '';
        foreach ($paramsArray as $key => $val) {
            $params = $params.$key.'='.$val.'&';
        }
        $url = Config::get('open_url').'/'.$api.'/'.Config::get('app_id').'?'.$params.'_aop_timestamp='.
               $timestamp.'&access_token='.$accessToken.'&_aop_signature='.
               Util::signDefault(Config::get('open_url'), $api, array_merge($paramsArray, array('_aop_timestamp' => $timestamp, 'access_token' => $accessToken)));

        return $url;
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