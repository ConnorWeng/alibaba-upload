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

        $this->assign(array(
            'memberId' => session('member_id'),
            'basepath' => str_replace('index.php', 'Public', __APP__),
            'infoTitle' => $taobaoItem->title,
            'categoryId' => I('categoryId'),
            'categoryName' => $categoryName,
            'offerDetail' => $taobaoItem->desc,
            'picUrl' => $taobaoItem->pic_url,
            'initSkus' => json_encode(Util::parseSkus($taobaoItem->skus->sku)),
            'offerWeight' => $taobaoItem->item_weight
        ));

        $this->display();
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
        $sendGoodsAddressId = I('sendGoodsAddressId');
        $freightType = I('freightType');
        $freightTemplateId = I('freight-list');
        $offerWeight = I('offerWeight');
        $mixWholeSale = Util::check(I('mixSupport'));
        $skuList = $_REQUEST['skuList']; // FIXME: problem
        $periodOfValidity = I('info-validity');
        $productFeatures = $_REQUEST['productFeatures'];

        /* upload image */
        $picUrl = $_REQUEST['picUrl'];
        $albumId = I('albumId');
        $localImageFile = '@'.OpenAPI::downloadImage($picUrl);
        $uploadResult = OpenAPI::ibankImageUpload($albumId, uniqid(), $localImageFile)->result->toReturn[0];
        unlink(substr($localImageFile,1));
        $imageUriList = '["http://img.china.alibaba.com/'.$uploadResult->url.'"]';
        /* end */

        $offer = '{"bizType":"1","categoryID":"'.$categoryId.'","supportOnlineTrade":"true","pictureAuthOffer":"false","priceAuthOffer":"false","skuTradeSupport":"true","mixWholeSale":"'.$mixWholeSale.'","priceRanges":"1:'.$price.'","amountOnSale":"100","offerDetail":"'.$detail.'","subject":"'.$subject.'","imageUriList":'.$imageUriList.',"freightType":"'.$freightType.'","productFeatures":'.$productFeatures.',"sendGoodsAddressId":"'.$sendGoodsAddressId.'","freightTemplateId":"'.$freightTemplateId.'","offerWeight":"'.$offerWeight.'","skuList":'.$skuList.',"periodOfValidity":'.$periodOfValidity.'}';

        $result = OpenAPI::offerNew(stripslashes($offer));
        if ($result->result->success) {
            $this->success('发布成功', U('Index/index'));
        } else {
            $this->error('发布失败：'.json_encode($result->message), '', 3);
        }
    }

    // 登出
    public function signOut() {
        session(null);
        cookie(null);
        U('Index/index', '', true, true, false);
    }

}