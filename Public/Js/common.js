function checkResponse(response) {
    if (response != null && typeof response == 'string') {
        if (response.indexOf('reauth') == 0) {
            var url = response.split('::')[1];
            alert('抱歉，阿里给予您的api调用次数已满，51网已为您更换接口，请重新授权，谢谢！');
            window.location = url;
        } else if (response.indexOf('timeout')) {
            var url = response.split('::')[1];
            alert('抱歉，会话已超时，请重新登录，谢谢!');
            window.location = url;
        }
    }
}
