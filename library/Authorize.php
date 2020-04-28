<?php
/**
 * 微信网页授权类
 *
 * @author: James.xiao
 */

namespace xiao\weixin\library;

class Authorize
{
    private $_isFromWeixinGetParamName  = '__is_from_weixin_lion__';
    private $_isFromWeixinGetParamValue = 'yes';

    public $appId;
    public $isHttps = false;

    public function __construct($appId)
    {
        $this->appId   = $appId;
        $this->isHttps = $this->is_https();
    }

    public function authorizeCodeToUrl(array $authorizeUrlConfig = [])
    {

        $finalAuthorizeUrl = '';
        if (!empty($_GET[$this->_isFromWeixinGetParamName]) && $_GET[$this->_isFromWeixinGetParamName] == $this->_isFromWeixinGetParamValue) {
            $filterGetParamName = $this->_isFromWeixinGetParamName;
            $forceGetParamName  = ['code', 'state'];
            $newGetParam        = [];
            foreach ($_GET as $k => $v) {
                if (in_array($k, $forceGetParamName) || $k != $filterGetParamName) {
                    $newGetParam[$k] = $v;
                }
            }
            if ($newGetParam) {
                $finalAuthorizeUrl = $newGetParam['redirect_uri'];
                unset($newGetParam['redirect_uri']);
                $finalAuthorizeUrl .= (strpos($finalAuthorizeUrl, '?') === false ? '?' : '&') . http_build_query($newGetParam);
            }
        } else {
            $pattern = "/redirect_uri=(.*?)&/";
            preg_match($pattern, $_SERVER['REQUEST_URI'], $matchs);

            $tempArray = parse_url($matchs[1]);
            $currUrl   = $tempArray['scheme'] . '://' . $tempArray['host'];
            if (!in_array($currUrl, $authorizeUrlConfig)) {
                echo ("NO AUTH");
                return;
            }
            $apiGetParamState = empty($_GET['state']) ? 'STATE' : $_GET['state'];
            unset($_GET['state']);
            $_GET[$this->_isFromWeixinGetParamName] = $this->_isFromWeixinGetParamValue;
            $apiGetParamRedirectUrl                 = explode('?', $_SERVER['REQUEST_URI']);
            $apiGetParamRedirectUrl                 = 'http' . ($this->isHttps ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $apiGetParamRedirectUrl[0] . '?' . urldecode(http_build_query($_GET));
            $apiGetParam['appid']                   = $this->appId;
            $apiGetParam['redirect_uri']            = urlencode($apiGetParamRedirectUrl);
            $apiGetParam['response_type']           = 'code';
            $apiGetParam['scope']                   = empty($_GET['scope']) || !in_array($_GET['scope'], ['snsapi_base', 'snsapi_userinfo']) ? 'snsapi_base' : $_GET['scope'];
            $apiGetParam['state']                   = "{$apiGetParamState}#wechat_redirect";
            $finalAuthorizeUrl                      = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . urldecode(http_build_query($apiGetParam));

        }
        header("Location: {$finalAuthorizeUrl}");
    }

    /**
     * 判断是否为https
     * @return bool 是https返回true;否则返回false
     */
    private function is_https()
    {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        } else {
            return false;
        }
    }
}
