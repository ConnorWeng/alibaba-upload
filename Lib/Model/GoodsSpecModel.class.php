<?php

class GoodsSpecModel extends ApiModel {

    protected $tableName = "goods_spec";

    public function addSpec($goodsId, $taobaoItem) {
        $data['goods_id'] = $goodsId;
        $skus = Util::parseSkus($taobaoItem->skus->sku);
        foreach ($skus as $sku) {
            $data['price'] = $sku->price;
            $data['stock'] = $sku->quantity;
            $parts = split(';', $sku->propertiesName);
            if (count($parts) > 0) {
                $data['spec_1'] = split(':', $parts[0])[3];
            }
            if (count($parts) > 1) {
                $data['spec_2'] = split(':', $parts[1])[3];
            }
            $this->add($data);
        }
    }

    public function deleteSpec($goodsId) {
        $where['goods_id'] = $goodsId;
        return $this->where($where)->delete();
    }

}

?>