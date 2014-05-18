<?php
return array(
    //'配置项'=>'配置值'
    'URL_MODEL' => 0,
    'app_id' => '1006478',
    'secret_id' => 'C7OMfhfK3C!T',
    'grant_type' => 'authorization_code',
    'need_refresh_token' => 'true',
    'host' => 'http://localhost',
    'redirect_uri' => 'Index/authBack',
    'open_url' => 'http://gw.open.1688.com/openapi',
    'title_suffix' => '- 面包西点',
    'max_try_api_times' => 10,
    'max_api_times_per_minute' => 30,

    //数据库配置
    'DB_TYPE' => 'mysql',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'ecm_',
    /* production
    'taobao_app_key' => '21336502',
    'taobao_secret_key' => '608b67adaba42db656de726b7cf2549f',
    'DB_HOST' => 'rdsqr7ne2m2ifjm.mysql.rds.aliyuncs.com',
    'DB_NAME' => 'test2',
    'DB_USER' => 'test2',
    'DB_PWD' => 'xiaoweng51wangpi',
    //*/
    //* dev
    'taobao_app_key' => '21610927',
    'taobao_secret_key' => 'e8c369cf5a2a20cb4c29e44726c863cd',
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'ecmall',
    'DB_USER' => 'root',
    'DB_PWD' => '57826502',
    //*/

    //开启日志
    'LOG_RECORD' => true,
    'LOG_LEVEL' => 'ERR',
);
?>