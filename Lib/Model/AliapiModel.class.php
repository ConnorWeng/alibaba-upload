<?php

class AliapiModel extends Model {

    private $apiCount = 4;

    public function getAppKey($taobaoItemId) {
        $len = strlen($taobaoItemId);
        $id = substr($taobaoItemId, $len-2, $len);
        $id = $id % $apiCount;

        $where['id'] = $id;
        $rs = $this->where($where)->select();
        if (count($rs) > 0) {
            return array('appkey' => $rs[0]['appkey'],
                         'appsecret' => $rs[0]['appscret']);
        } else {
            return array('appkey' => C('app_id'),
                         'appsecret' => C('secret_id'));
        }
    }

}

?>