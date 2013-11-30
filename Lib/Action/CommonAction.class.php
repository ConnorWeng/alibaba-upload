<?php

import('@.Util.Util');

class CommonAction extends Action {

    protected function checkApiResponseAjax($response) {
        $taobaoItemId = session('current_taobao_item_id');
        if ($response == 'reauth') {
            Util::changeAliAppkey($taobaoItemId, session('alibaba_app_key'));
            $this->ajaxReturn($response.'::'.Util::getAlibabaAuthUrl($taobaoItemId), 'JSON');
        } else if ($response == 'timeout') {
            $this->ajaxReturn($response.'::'.U('Index/signOut'), 'JSON');
        } else if ($response == 'verify') {
            $this->ajaxReturn($response.'::'.U('Index/verifyCode'), 'JSON');
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
        } else if ($response == 'timeout') {
            $this->assign(array(
                'waitSecond' => 6,
            ));
            $this->error('抱歉，会话已超时，请重新登录，谢谢!', U('Index/signOut'));
        } else if ($response == 'verify') {
            U('Index/verifyCode', '', true, true, false);
        } else {
            return $response;
        }
    }

}

?>