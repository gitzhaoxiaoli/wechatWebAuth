# **wechatWebAuth**：**一个**微信公众号网页授权给**任何**域名下的url(可以是本地localhost)
把从微信网页授权接口中获取到的`授权code`以get参数的形式传递给**任何**域名下的url。

## 打赏捐赠
如果您觉得本程序不错，欢迎打赏我给予小小的支持，谢过！![谢谢][6]![卖萌][7]

![支付宝二维码][1]　　　　![微信二维码][2]


## 环境需求
* php >= 5.4.0

## 极速使用
1. 假设将网页授权回调域名设置为`www.test.com`；
2. 编辑`wechatWebAuth/getcode.php`，将变量`$appId`的值修改为自己的`微信公众号AppId`；
3. 将文件夹wechatWebAuth中的所有文件部署到`http://www.test.com/wechatWebAuth/`；
4. 将微信授权地址换成`"http://test.com/codetoany/getcode.php?redirect_uri=http://www.a.com/index.pgp?name=name1&scope=snsapi_userinfo"` 其中 `www.a.com` 为要获取授权的地址，顺利的话，页面将跳转到类似这样的url：`http://www.a.com/index.pgp?name=name1&code=0318PVx00bTFzB1JOny00YMRx008PVxS&state=STATE`,就可以继续后续操作了

## 攻略指南
网页授权接口回调地址redirect_uri中的get参数`scope`和`state`可以以get参数的形式传递给`wechatWebAuth/getcode.php`，程序会把它们再传递给接口；


## 郑重声明
* 本程序仅供学习研究使用，不得用于非法用途，否则后果自负；
* 对于由本程序导致的一切法律和安全问题，作者不承担任何责任；

[1]: <asset/image/qrcode_alipay_230_253.jpg> "支付宝扫一扫打赏"
[2]: <asset/image/qrcode_weixin_230_253.jpg> "微信扫一扫打赏"

<!--
简述（用于搜索）：
1、微信公众号平台网页授权接口中获取到的授权code传递给（即一个微信公众号网页授权给）任何其他多个回调域名下的url，解决了只能设置一个网页授权回调域名的问题，解决了redirect_uri参数错误的问题。
2、vue 本地开发，每次都要打包上传到服务器才能调试，解决了vue 本地开发不用上传到服务器，就可以在本地调试微信登录的问题。
-->
