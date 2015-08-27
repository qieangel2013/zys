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
 * 请求型接口的基类
 */
class wx_pay_pay extends wx_pay_pub {

    var $parameters; //请求参数，类型为关联数组
    public $response; //微信返回的响应
    public $result; //返回参数，类型为关联数组
    var $url; //接口链接
    var $curl_timeout; //curl超时时间

    /**
     *  作用：设置请求参数
     */

    function setParameter($parameter, $parameterValue) {
        $this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }

    /**
     *  作用：设置标配的请求参数，生成签名，生成接口参数xml
     */
    function createXml() {
        $this->parameters["appid"] = wx_pay_config::APPID; //公众账号ID
        $this->parameters["mch_id"] = wx_pay_config::MCHID; //商户号
        $this->parameters["nonce_str"] = $this->createNoncestr(); //随机字符串
        $this->parameters["sign"] = $this->getSign($this->parameters); //签名
        return $this->arrayToXml($this->parameters);
    }

    /**
     *  作用：post请求xml
     */
    function postXml() {
        $xml = $this->createXml();
        $this->response = $this->postXmlCurl($xml, $this->url, $this->curl_timeout);
        //$this->printErr('请求参数=', $this->xmlToArray($this->response));
        return $this->response;
    }

    /**
     *  作用：使用证书post请求xml
     */
    function postXmlSSL() {
        $xml = $this->createXml();
        $this->response = $this->postXmlSSLCurl($xml, $this->url, $this->curl_timeout);
        return $this->response;
    }

    /**
     *  作用：获取结果，默认不使用证书
     */
    function getResult() {
        $this->postXml();
        $this->result = $this->xmlToArray($this->response);
        return $this->result;
    }

}
?>