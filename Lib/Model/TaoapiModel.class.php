<?php

class TaoapiModel extends ApiModel {

    protected $tableName = "taoapi_self";

    // Override
    public function getAppKey($taobaoItemId, $oldAppKey = null) {
        return array('appkey' => C('taobao_app_key'),
                     'appsecret' => C('taobao_secret_key'),
                     'id' => '');
    }

}

?>