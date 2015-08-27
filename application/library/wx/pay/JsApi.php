<?php

/**
 * 微信支付帮助库
 * ====================================================
 * 接口分三种类型：
 * 【请求型接口】--Wxpay_client_
 * 		统一支付接口类--UnifiedOrder
 * 		订单查询接口--OrderQuery
 * 		退款申请接口--Refund
 * 		退款查询接口--RefundQuery
 * 		对账单接口--DownloadBill
 * 		短链接转换接口--ShortUrl
 * 【响应型接口】--Wxpay_server_
 * 		通用通知接口--Notify
 * 		Native支付——请求商家获取商品信息接口--NativeCall
 * 【其他】
 * 		静态链接二维码--NativeLink
 * 		JSAPI支付--JsApi
 * =====================================================
 * 【CommonUtil】常用工具：
 * 		trimString()，设置参数时需要用到的字符处理函数
 * 		createNoncestr()，产生随机字符串，不长于32位
 * 		formatBizQueryParaMap(),格式化参数，签名过程需要用到
 * 		getSign(),生成签名
 * 		arrayToXml(),array转xml
 * 		xmlToArray(),xml转 array
 * 		postXmlCurl(),以post方式提交xml到对应的接口url
 * 		postXmlSSLCurl(),使用证书，以post方式提交xml到对应的接口url
 */


/**
 * JSAPI支付——H5网页端调起支付接口
 */
class wx_pay_JsApi extends wx_pay_pub {

    var $code; //code码，用以获取openid
    var $openid; //用户的openid
    var $parameters; //jsapi参数，格式为json
    var $prepay_id; //使用统一支付接口得到的预支付id
    var $curl_timeout; //curl超时时间

    function __construct() {
        //设置curl超时时间
        $this->curl_timeout = wx_pay_config::CURL_TIMEOUT;
    }

    /**
     *  作用：生成可以获得code的url
     */
    function createOauthUrlForCode($redirectUrl) {
        $urlObj["appid"] = wx_pay_config::APPID;
        $urlObj["redirect_uri"] = urlencode("$redirectUrl");
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->formatBizQueryParaMap($urlObj, false);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }

    /**
     *  作用：生成可以获得openid的url
     */
    function createOauthUrlForOpenid() {
        $urlObj["appid"] = wx_pay_config::APPID;
        $urlObj["secret"] = wx_pay_config::APPSECRET;
        $urlObj["code"] = $this->code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->formatBizQueryParaMap($urlObj, false);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }

    /**
     *  作用：通过curl向微信提交code，以获取openid
     */
    function getOpenid() {
        $url = $this->createOauthUrlForOpenid();
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res, true);
        //var_dump($data);
        $this->openid = $data['openid'];
        return $this->openid;
    }

    /**
     *  作用：设置prepay_id
     */
    function setPrepayId($prepayId) {
        $this->prepay_id = $prepayId;
    }

    /**
     *  作用：设置code
     */
    function setCode($code_) {
        $this->code = $code_;
    }

    /**
     *  作用：设置jsapi的参数
     */
    public function getParameters() {
        $jsApiObj["appId"] = wx_pay_config::APPID;
        $timeStamp = time();
        $jsApiObj["timeStamp"] = "$timeStamp";
        $jsApiObj["nonceStr"] = $this->createNoncestr();
        $jsApiObj["package"] = "prepay_id=$this->prepay_id";
        $jsApiObj["signType"] = "MD5";
        $jsApiObj["paySign"] = $this->getSign($jsApiObj);
        $this->parameters = json_encode($jsApiObj);

        return $this->parameters;
    }

}

?>
