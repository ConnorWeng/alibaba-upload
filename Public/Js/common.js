function checkResponse(response) {
    if (response.indexOf('reauth') == 0) {
        var url = response.split('::')[1];
        alert('抱歉，阿里给予您的api调用次数已满，51网已为您更换接口，请重新授权，谢谢！');
        window.location = url;
    }
}
