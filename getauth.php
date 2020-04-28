<?php
/**
 * @author: xiao
 */

require 'library/Authorize.php';

$appId             = 'your appId';
$authorize         = new xiao\weixin\library\Authorize($appId);
$redirectUrlConfig = [
    '1' => 'http://abc.com',
    '2' => 'http://www.baidu.com',
    '3' => 'http://localhost',
];

$authorize->authorizeCodeToUrl($redirectUrlConfig);
