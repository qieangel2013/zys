<?php
/**  
 * 插件类定义 
 * UserPlugin.php 
 */  
class UserPlugin extends Yaf_Plugin_Abstract {  
    //在路由之前触发，这个是7个事件中, 最早的一个. 但是一些全局自定的工作, 还是应该放在Bootstrap中去完成  
    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {  
        //echo "Plugin routerStartup called <br/>\n";  
    }  
//路由结束之后触发，此时路由一定正确完成, 否则这个事件不会触发  
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {  
        //echo "Plugin routerShutdown called <br/>\n";  
    }  
//分发循环开始之前被触发  
    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {  
        //echo "Plugin DispatchLoopStartup called <br/>\n";  
    }  
//分发之前触发    如果在一个请求处理过程中, 发生了forward, 则这个事件会被触发多次  
    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {  
        //echo "Plugin PreDispatch called <br/>\n";  
    }  
//分发结束之后触发，此时动作已经执行结束, 视图也已经渲染完成. 和preDispatch类似, 此事件也可能触发多次  
    public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {  
        //echo "Plugin postDispatch called <br/>\n";  
    }  
  //分发循环结束之后触发，此时表示所有的业务逻辑都已经运行完成, 但是响应还没有发送  
    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {  
        //echo "Plugin DispatchLoopShutdown called <br/>\n";  
    }  
    
    public function preResponse(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {  
        //echo "Plugin PreResponse called <br/>\n";  
    }  
}  

?>