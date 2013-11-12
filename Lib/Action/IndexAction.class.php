<?php

import('@.Util.Util');
import('@.Util.OpenAPI');

class IndexAction extends Action {

    public function index() {
        $this->display();
    }

    // 跳转到alibaba的认证页面
    public function auth() {
        $taobaoItemId = I('taobaoItemId');
        if (!session('?access_token')) {
            header('location:'.Util::getAlibabaAuthUrl($taobaoItemId));
        } else {
            U('Index/authBack', array('state' => $taobaoItemId), true, true, false);
        }
    }

    // 从alibaba跳转回来的action
    public function authBack() {
        if (!session('?access_token')) {
            $code = I('code');
            $tokens = Util::getTokens($code);

            session('member_id', $tokens->memberId);
            session('access_token', $tokens->access_token);
            cookie('refresh_token', $tokens->refresh_token);
        }

        $taobaoItemId = I('state');
        $taobaoItem = OpenAPI::getTaobaoItem($taobaoItemId);

        $this->assign(array(
            'basepath' => str_replace('index.php', 'Public', __APP__),
            'memberId' => session('member_id'),
            'taobaoItemId' => $taobaoItemId,
            'taobaoItemTitle' => $taobaoItem->title
        ));

        $this->display();
    }

    // 通过关键字查询分类
    public function searchCategory() {
        $keyWord = I('keyWord');
        $searchResult = OpenAPI::categorySearch($keyWord)->result;
        $categoryIds = '';
        if ($searchResult->total > 0) {
            foreach ($searchResult->toReturn as $val) {
                $categoryIds .= $val.',';
            }
        }
        if (stripos($categoryIds, ',')) {
            $categoryIds = substr($categoryIds, 0, strlen($categoryIds) - 1);
        }
        $categoryList = OpenAPI::getPostCatList($categoryIds)->result->toReturn;

        $this->ajaxReturn($categoryList, 'JSON');
    }

    // 编辑页面
    public function editPage() {
        $categoryName = OpenAPI::getPostCatList(I('categoryId'))->result->toReturn[0]->catsName;
        $taobaoItem = OpenAPI::getTaobaoItem(I('taobaoItemId'));

        $khn = $this->getKHN($taobaoItem->title);

        $this->assign(array(
            'taobaoItemId' => I('taobaoItemId'),
            'price' => $taobaoItem->price,
            'memberId' => session('member_id'),
            'basepath' => str_replace('index.php', 'Public', __APP__),
            'infoTitle' => $taobaoItem->title,
            'categoryId' => I('categoryId'),
            'categoryName' => $categoryName,
            'offerDetail' => $taobaoItem->desc,
            'picUrl' => $taobaoItem->pic_url,
            'itemImgs' => json_encode(Util::parseItemImgs($taobaoItem->item_imgs->item_img)),
            'initSkus' => json_encode(Util::parseSkus($taobaoItem->skus->sku)),
            'propsAlias' => $taobaoItem->property_alias,
            'offerWeight' => '0.2',
            'khn' => $khn
        ));

        $this->display();
    }

    private function getKHN($title) {
        $pKh='/[A-Z]?\d+/';
        preg_match_all($pKh,$title,$pKuanhao);
        $pKhnum=count($pKuanhao[0]);
        if($pKhnum>0)
        {
            for($i=0;$i < $pKhnum;$i++)
            {
                if(strlen($pKuanhao[0][$i])==3 || (strlen($pKuanhao[0][$i])==4 && substr($pKuanhao[0][$i], 0,3)!= "201"))
                {
                    $khn = $pKuanhao[0][$i];
                    break;
                }
            }
        }

        return $khn;
    }

    // 获取发布相关属性
    public function getPostFeatures() {
        $categoryId = I('categoryId');
        $features = OpenAPI::offerPostFeatures($categoryId)->result->toReturn;

        $this->ajaxReturn($features, 'JSON');
    }

    // 获取发货地址
    public function getSendGoodsAddress() {
        $addressList = OpenAPI::getSendGoodsAddressList()->result->toReturn;

        $this->ajaxReturn($addressList, 'JSON');
    }

    // 获取运费模版
    public function getFreightTemplateList() {
        $freightTemplateList = OpenAPI::getFreightTemplateList()->result->toReturn;

        $this->ajaxReturn($freightTemplateList, 'JSON');
    }

    // 获取相册列表
    public function getAlbumList() {
        $myAlbumList = OpenAPI::ibankAlbumList('MY')->result->toReturn;
        $customAlbumList = OpenAPI::ibankAlbumList('CUSTOM')->result->toReturn;

        $this->ajaxReturn(array_merge($myAlbumList, $customAlbumList), 'JSON');
    }

    // 发布新产品
    public function offerNew() {
        $categoryId = I('categoryId');
        if (get_magic_quotes_gpc() == 0) {
            $detail = addslashes(addslashes($_REQUEST['details']));
        } else {
            $detail = addslashes(($_REQUEST['details']));
        }
        $subject = I('subject');
        $priceRanges = I('priceRanges');
        $supportOnline = Util::check(I('support-online'));
        $skuTradeSupported = Util::check(I('isSkuTradeSupported'));
        $sendGoodsAddressId = I('sendGoodsAddressId');
        $freightType = I('freightType');
        $freightTemplateId = I('freight-list');
        $offerWeight = I('offerWeight');
        $mixWholeSale = Util::check(I('mixSupport'));
        $skuList = $_REQUEST['skuList']; // FIXME: problem
        $periodOfValidity = I('info-validity');
        $productFeatures = $_REQUEST['productFeatures'];

        /* upload image */
        $imageUriList = '[';
        for ($i = 1; $i <= 3; $i += 1) { // 因为一共三个input: pictureUrl1,pictureUrl2,pictureUrl3
            $picUrl = $_REQUEST['pictureUrl'.$i];
            if ($picUrl != '') {
                $albumId = I('albumId');
                $localImageFile = '@'.OpenAPI::downloadImage($picUrl);
                $uploadResult = OpenAPI::ibankImageUpload($albumId, uniqid(), $localImageFile)->result->toReturn[0];
                unlink(substr($localImageFile,1));
                $imageUriList .= '"http://img.china.alibaba.com/'.$uploadResult->url.'",';
            }
        }
        $imageUriList = substr($imageUriList, 0, strlen($imageUriList) - 1);
        $imageUriList .= ']';
        /* end */

        /* auto off */
        $autoOff = Util::check(I('autoOff'));
        if ($autoOff == 'true') {
            $encNumIid = '51chk'.base64_encode(I('taobaoItemId'));
            $autoOffJpg = 'http://51wangpi.com/'.$encNumIid.'.jpg';
            $autoOffWarnHtml = '<img align="middle" src="'.$autoOffJpg.'"/><br/>';
            if (get_magic_quotes_gpc() == 0) {
                $autoOffWarnHtml = addslashes(addslashes($autoOffWarnHtml));
            } else {
                $autoOffWarnHtml = addslashes($autoOffWarnHtml);
            }
            $detail = $autoOffWarnHtml.$detail;
        }
        /* end */

        $offer = '{"bizType":"1","categoryID":"'.$categoryId.'","supportOnlineTrade":'.$supportOnline.',"pictureAuthOffer":"false","priceAuthOffer":"false","skuTradeSupport":'.$skuTradeSupported.',"mixWholeSale":"'.$mixWholeSale.'","priceRanges":"'.$priceRanges.'","amountOnSale":"100","offerDetail":"'.$detail.'","subject":"'.$subject.'","imageUriList":'.$imageUriList.',"freightType":"'.$freightType.'","productFeatures":'.$productFeatures.',"sendGoodsAddressId":"'.$sendGoodsAddressId.'","freightTemplateId":"'.$freightTemplateId.'","offerWeight":"'.$offerWeight.'","skuList":'.$skuList.',"periodOfValidity":'.$periodOfValidity.'}';

        $result = OpenAPI::offerNew(stripslashes($offer));
        if ($result->result->success) {
            $offerId = $result->result->toReturn[0];
            $itemUrl = "http://detail.1688.com/offer/$offerId.html";
            $this->assign(array(
                'result' => '发布成功啦！',
                'message' => '宝贝已顺利上架哦！祝生意欣荣，财源广进！',
                'itemUrl' => '<li><a href="'.$itemUrl.'">来看看刚上架的宝贝吧！</a></li>'
            ));
        } else {
            $this->assign(array(
                'result' => '发布失败！'.json_encode($result->message),
                'message' => '宝贝没有顺利上架，请不要泄气哦，换个宝贝试试吧！祝生意欣荣，财源广进！',
                'itemUrl' => ''
            ));
        }

        $this->display();
    }

    // 登出
    public function signOut() {
        session(null);
        cookie(null);
        U('Index/index', '', true, true, false);
    }

}