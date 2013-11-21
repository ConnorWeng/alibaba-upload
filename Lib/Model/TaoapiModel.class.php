<?php

class TaoapiModel extends Model {

    public function getAppKey($taobaoItemId) {
        $len = strlen($taobaoItemId);
        $id = substr($taobaoItemId, $len-2, $len);
        $id = floor(($id) / 5);

        $where['id'] = $id;
        $rs = $this->where($where)->select();
        if (count($rs) > 0) {
            return array('appkey' => $rs[0]['appkey'],
                         'appsecret' => $rs[0]['appscret']);
        } else {
            return array('appkey' => C('taobao_app_key'),
                         'appsecret' => C('taobao_secret_key'));
        }
    }

}

?>