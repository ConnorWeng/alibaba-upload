<?php

import('@.Util.Util');

class CommonAction extends Action {

    protected function checkApiResponseAjax($response) {
        $taobaoItemId = session('current_taobao_item_id');
        if ($response == 'reauth') {
            Util::changeAliAppkey($taobaoItemId, session('alibaba_app_key'));
            $this->ajaxReturn($response.'::'.Util::getAlibabaAuthUrl($taobaoItemId), 'JSON');
        } else {
            return $response;
        }
    }

    protected function checkApiResponse($response) {
        $taobaoItemId = session('current_taobao_item_id');
        if ($response == 'reauth') {
            $this->assign(array(
                'waitSecond' => 6,
            ));
            Util::changeAliAppkey($taobaoItemId, session('alibaba_app_key'));
            $this->error('抱歉，阿里给予您的api调用次数已满，51网已为您更换接口，请重新授权，谢谢！', Util::getAlibabaAuthUrl($taobaoItemId));
        } else {
            return $response;
        }
    }

}

?>