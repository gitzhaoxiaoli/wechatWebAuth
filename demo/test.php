<?php

$get          = $_GET;
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&scope=snsapi_userinfo';
$url          = "http://微信填写的域名/codetoany/getauth.php?redirect_uri=" . $redirect_uri;

if (empty($get['code'])) {
    header("Location: " . $url);
}

$res = getLoginAccessToken($get['code']);
pe($res);

function pe($value)
{
    echo "<pre>";
    print_r($value);
    echo "</pre>";
    exit;

}

function getLoginAccessToken($code, $isGetUserInfo = true)
{
    $AppID     = "your AppID";
    $AppSecret = "your AppSecret";
    $apiUrl    = "https://api.weixin.qq.com/sns/oauth2/access_token?AppID={$AppID}&secret={$AppSecret}&code={$code}&grant_type=authorization_code";
    $res       = httpRequest($apiUrl);
    // pe($res);
    if (!$res) {
        return ['5001', 'oauth2接口错误'];
    }
    $res = json_decode($res, true);
    if (!$res) {
        return ['5001', 'oauth2接口:json解析失败'];
    }
    if (!empty($res['errcode']) && in_array($res['errcode'], [40029, 40163, 42003])) {
        $errcodeToName = [
            40029 => '无效的 oauth_code',
            40163 => 'oauth_code已使用',
            42003 => 'oauth_code 超时',
        ];
        return ['7201', $errcodeToName[$res['errcode']] ?? $res['errmsg']];
    }
    // $res['scope'] = 'snsapi_userinfo';

    //判断是否获取用户信息
    if ($isGetUserInfo && $res['scope'] == 'snsapi_userinfo') {
        $userInfo = getUserInfo($res['access_token'], $res['openid']);
        if (!$userInfo) {
            return $userInfo;
        }
        $res['userInfo'] = $userInfo;
    }
    return $res;
}

/**
 * 拉取用户信息
 */
function getUserInfo($access_token, $openid)
{
    $apiUrl = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
    $res    = httpRequest($apiUrl);
    if (!$res) {
        return ['5001', 'userinfo接口:接口错误'];
    }
    $res = json_decode($res, true);
    if (!$res) {
        return ['5001', 'userinfo接口:json解析失败'];
    }
    if (!empty($res['errcode'])) {
        return ['7202', "{$res['errcode']}:{$res['errmsg']}"];
    }
    return $res;
}

function httpRequest($url, $method = 'GET', $postData = [], $headers = [])
{
    // 创建一个cURL资源
    $ch = curl_init();
    // 设置URL和相应的选项
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (stripos($url, 'https') === 0) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不用https验证
    }
    if ($method == 'POST') {
        //设置post
        curl_setopt($ch, CURLOPT_POST, 1);
        //设置post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // 抓取URL并返回
    $data = curl_exec($ch);
    if (!$data) {
//        return curl_strerror(curl_errno($ch));
        return false;
    }
    // 关闭cURL资源，并且释放系统资源
    curl_close($ch);
    return $data;
}
