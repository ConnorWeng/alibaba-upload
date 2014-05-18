<?php

class GoodsModel extends ApiModel {

    protected $tableName = "goods";

    public function getAllUnfetchGoods() {
        $where['type'] = 'unfetch';
        return $this->where($where)->select();
    }

    public function updateWithDetails($goodsId, $taobaoItemId, $taobaoItem) {
        $where['goods_id'] = $goodsId;
        $data['description'] = ''.$taobaoItem->desc;
        $data['type'] = 'material';
        return $this->where($where)->save($data);
    }

}

?>