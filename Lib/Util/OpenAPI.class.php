<?php

import('@.Util.Util');
vendor('taobao-sdk.TopSdk');

class OpenAPI {

    public static function offerNew($offer) {
        $api = 'param2/1/cn.alibaba.open/offer.new';
        return self::callOpenAPIWithShortUrl($api, array('offer' => $offer));
    }

    public static function categorySearch($keyWord) {
        $api = 'param2/1/cn.alibaba.open/category.search';
        return self::callOpenAPI($api, array('keyWord' => $keyWord), false);
    }

    public static function getPostCatList($catIDs) {
        $api = 'param2/1/cn.alibaba.open/category.getPostCatList';
        return self::callOpenAPI($api, array('catIDs' => $catIDs), false);
    }

    public static function offerPostFeatures($categoryId) {
        $api = 'param2/1/cn.alibaba.open/offerPostFeatures.get';
        return self::callOpenAPI($api, array('categoryID' => $categoryId), false);
    }

    public static function ibankAlbumList($albumType) {
        $api = 'param2/1/cn.alibaba.open/ibank.album.list';
        return self::callOpenAPI($api, array('albumType' => $albumType), false);
    }

    public static function getSendGoodsAddressList() {
        $api = 'param2/1/cn.alibaba.open/trade.freight.sendGoodsAddressList.get';
        return self::callOpenAPI($api, array('memberId' => session('member_id'),
                                             'returnFields' => 'deliveryAddressId,isCommonUse,location,address'), false);
    }

    public static function getFreightTemplateList() {
        $api = 'param2/1/cn.alibaba.open/trade.freight.freightTemplateList.get';
        return self::callOpenAPI($api, array('memberId' => session('member_id')), false);
    }

    public static function ibankImageUpload($albumId, $name, $imageBytes) {
        $api = 'param2/1/cn.alibaba.open/ibank.image.upload';
        $url = self::makeUrl($api, array('albumId' => $albumId,
                                         'name' => $name,
                                         'imageBytes' => $imageBytes), false);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('imageBytes' => $imageBytes));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);

        return json_decode($data);
    }

    public static function downloadImage($picUrl) {
        $tmpFile = APP_PATH.'Upload/'.uniqid().'.jpg';
        $content = file_get_contents($picUrl);
        file_put_contents($tmpFile, $content);

        return $tmpFile;
    }

    private static function callOpenAPI($api, $params, $urlencode) {
        $url = self::makeUrl($api, $params, $urlencode);
        $data = self::sendRequest($url);
        return json_decode($data);
    }

    private static function sendRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    private static function makeUrl($api, $paramsArray, $urlencode) {
        $timestamp = time() * 1000;
        $accessToken = session('access_token');
        $params = '';
        foreach ($paramsArray as $key => $val) {
            $value = $val;
            if ($urlencode) {
                $value = urlencode($val);
            }
            $params = $params.$key.'='.$value.'&';
        }
        $url = C('open_url').'/'.$api.'/'.session('alibaba_app_key').'?'.$params.'_aop_timestamp='.
               $timestamp.'&access_token='.$accessToken.'&_aop_signature='.
               Util::signDefault(C('open_url'), $api, array_merge($paramsArray, array('_aop_timestamp' => $timestamp, 'access_token' => $accessToken)));

        return $url;
    }

    private static function callOpenAPIWithShortUrl($api, $params) {
        $url = self::makeShortUrl($api, $params);
        $data = self::sendRequestWithShortUrl($url, $params);

        return json_decode($data);
    }

    private static function sendRequestWithShortUrl($url, $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    private static function makeShortUrl($api, $paramsArray) {
        $timestamp = time() * 1000;
        $accessToken = session('access_token');
        $url = C('open_url').'/'.$api.'/'.session('alibaba_app_key').'?_aop_timestamp='.
               $timestamp.'&access_token='.$accessToken.'&_aop_signature='.
               Util::signDefault(C('open_url'), $api, array_merge($paramsArray, array('_aop_timestamp' => $timestamp, 'access_token' => $accessToken)));

        return $url;
    }

    public static function getTaobaoItem($numIid) {
        $c = new TopClient;
        $c->appkey = session('taobao_app_key');
        $c->secretKey = session('taobao_secret_key');
        $req = new ItemGetRequest;
        $req->setFields("title,desc,pic_url,sku,item_weight,property_alias,price,item_img.url");
        $req->setNumIid($numIid);
        $resp = $c->execute($req, null);

        $taoapi = D('Taoapi');
        if ($resp->code == '7') { // accesscontrol.limited-by-app-access-count
            $taoapi->appKeyFail(session('current_taobao_app_key_id'));
            Util::changeTaoAppkey($numIid, session('taobao_app_key'));
            return self::getTaobaoItem($numIid);
        } else if (isset($resp->item)) {
            $taoapi->appKeySuccess(session('current_taobao_app_key_id'));
            return $resp->item;
        } else {
            echo('<h6 style="color:red;">错误:'.$resp->msg.'</h6>');
        }
    }

}

?>