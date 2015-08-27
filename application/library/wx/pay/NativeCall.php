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
 * 请求商家获取商品信息接口
 */
class wx_pay_NativeCall extends wx_pay_server {

    /**
     * 生成接口参数xml
     */
    function createXml() {
        if ($this->returnParameters["return_code"] == "SUCCESS") {
            $this->returnParameters["appid"] = wx_pay_config::APPID; //公众账号ID
            $this->returnParameters["mch_id"] = wx_pay_config::MCHID; //商户号
            $this->returnParameters["nonce_str"] = $this->createNoncestr(); //随机字符串
            $this->returnParameters["sign"] = $this->getSign($this->returnParameters); //签名
        }
        return $this->arrayToXml($this->returnParameters);
    }

    /**
     * 获取product_id
     */
    function getProductId() {
        $product_id = $this->data["product_id"];
        return $product_id;
    }

}

?>