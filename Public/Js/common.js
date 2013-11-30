function checkResponse(response) {
    if (response != null && typeof response == 'string') {
        var url = response.split('::')[1],
            msg = '';
        if (response.indexOf('reauth') == 0) {
            msg = '抱歉，阿里给予您的api调用次数已满，51网已为您更换接口，请重新授权，谢谢！';
        } else if (response.indexOf('timeout') == 0) {
            msg = '抱歉，会话已超时，请重新登录，谢谢!';
        } else if (response.indexOf('verify') == 0) {
            msg = '抱歉，您操作过于频繁，请输入验证码!';
        }
        alert(msg);
        window.location = url;
    }
}
