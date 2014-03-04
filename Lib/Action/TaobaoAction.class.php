<?php

import('@.Util.Util');
import('@.Util.OpenAPI');

class TaobaoAction extends CommonAction {

    public function fetch() {
        header("Content-type:text/html;charset=utf-8");
        Util::changeTaoAppkey('100');
        $goodsModel = D('Goods');
        $goodsSpecModel = D('GoodsSpec');
        $unfetchGoods = $goodsModel->getAllUnfetchGoods();
        foreach ($unfetchGoods as $good) {
            $goodsId = $good['goods_id'];
            $taobaoItemId = $good['description'];
            $taobaoItem = OpenAPI::getTaobaoItemWithoutVerify($taobaoItemId, true);
            $goodsModel->updateWithDetails($goodsId, $taobaoItemId, $taobaoItem);
            $goodsSpecModel->deleteSpec($goodsId);
            $goodsSpecModel->addSpec($goodsId, $taobaoItem);
        }
    }

}