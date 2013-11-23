<?php

class TaoapiModel extends Model {

    protected $tableName = "taoapi_self";

    public function getAppKey($taobaoItemId, $oldAppKey = null) {
        $len = strlen($taobaoItemId);
        $id = substr($taobaoItemId, $len-2, $len);
        $id = floor(($id) / 5);

        if ($oldAppKey != null) {
            // TODO: change id to fetch new appkey
        }

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