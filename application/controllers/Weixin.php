<?php

/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class WeixinController extends Yaf_Controller_Abstract {

    public function init() {
        date_default_timezone_set('PRC');
        $this->getView()->assign("title", "微信h5页面");
        //$this->weixin = new WxModel();
        $this->_req = $this->getRequest();
       //$this->_redis = new phpredis();
    }
    /**
     * JS_API支付demo
     * ====================================================
     * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
     * 成功调起支付需要三个步骤：
     * 步骤1：网页授权获取用户openid
     * 步骤2：使用统一支付接口，获取prepay_id
     * 步骤3：使用jsapi调起支付
     */
    public function wxpayAction() {
            $jsApi = new wx_pay_JsApi();
            $oid=123;//订单id
            $userid=456;//用户id
            $wx_openid='';//微信授权id
            if(empty($wx_openid)){
                if (!isset($_GET['code'])) {
                    $url = wx_pay_config::JS_API_CALL_URL;
                    $url = str_replace('%oid%', $oid, $url);
                    $url = str_replace('%uid%', $userid, $url);
                    $url = $jsApi->createOauthUrlForCode($url);
                    Header("Location: $url");
                } else {
                    $code = $_GET['code'];
                    $jsApi->setCode($code);
                    $openid = $jsApi->getOpenId();
                }            
            }else{
                $openid = $wx_openid;
            }
            $unifiedOrder = new wx_pay_UnifiedOrder();
            $unifiedOrder->setParameter("body", "test"); //商品描述
            $unifiedOrder->setParameter("out_trade_no", "1111111111"); //商户订单号 
            $unifiedOrder->setParameter("total_fee", "0101"); //总金额 $total
            $unifiedOrder->setParameter("notify_url", wx_pay_config::NOTIFY_URL); //通知地址 
            $unifiedOrder->setParameter("trade_type", "JSAPI"); //交易类型
            $unifiedOrder->setParameter("openid", $openid); //用户标识
            $prepay_id = $unifiedOrder->getPrepayId();
            $jsApi->setPrepayId($prepay_id);
            $jsApiParameters = $jsApi->getParameters();
            $this->getView()->display('weixin/weixinpay.html');
    }
    
    /**
     * 微信支付的回调地址
     */
    public function wxnotifyAction() {
        //使用通用通知接口
        $notify = new wx_pay_Notify();
        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];//$GLOBALS1->HTTP_RAW_POST_DATA;//$GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);
        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if ($notify->checkSign() == FALSE) {
            $notify->setReturnParameter("return_code", "FAIL"); //返回状态码
            $notify->setReturnParameter("return_msg", "签名失败"); //返回信息
        } else {
            $notify->setReturnParameter("return_code", "SUCCESS"); //设置返回码
        }
        $returnXml = $notify->returnXml();
        //echo $returnXml;
        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        if ($notify->checkSign() == TRUE) {
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $this->_redis->set('notify_3',"更新交易记录失败-通信出错：".json_encode($notify->data));
            } elseif ($notify->data["result_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $this->_redis->set('notify_4',"更新交易记录失败-业务出错：".json_encode($notify->data));
            } else {
                //此处应该更新一下订单状态，商户自行增删操作
                //$log_->log_result($log_name,"【支付成功】:\n".$xml."\n"); 
                $notify->data["method"]='notify';
                $r = $this->updatePayRecord($notify->data);
                if (is_array($r)) {
                    $r1 = $this->updatewaporder($r);
                    if ($r1 === 1) {
                        ///修改所有的都成功
                    } else {
                        $this->_redis->set("notify_2_".$r['order_id'],"更新订单信息失败：".json_encode($notify->data));
                    }
                }else{
                    $this->_redis->set("notify_1_".$r['order_id'],"更新交易记录失败：".json_encode($notify->data));
                }
            }
        }
    }

    
}
