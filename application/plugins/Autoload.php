<?php
/**  
 * @author zqf 
 * Autoload.php 
 */  
class AutoloadPlugin extends Yaf_Plugin_Abstract {  
    //在路由之前触发，这个是7个事件中, 最早的一个. 但是一些全局自定的工作, 还是应该放在Bootstrap中去完成  
    var $config;
    var $fileload;
    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {  
        $this->config=Yaf_Application::app()->getConfig();
        $this->fileload=Yaf_Loader::getInstance();
        if(isset($this->config->application->autolibrary) && !empty($this->config->application->autolibrary)){
            $autoclass=explode(',',$this->config->application->autolibrary);
            foreach ($autoclass as $v) {
                if(is_dir(APPLICATION_PATH.'/'.$v)){
                    $this->getlist(APPLICATION_PATH.'/'.$v,'class');
                }else{
                    throw new Exception(APPLICATION_PATH.'/'.$v.'不是目录');
                }  
            }
        }
        $this->fileload->setLibraryPath(APPLICATION_PATH.'/library',true);
    }  
//路由结束之后触发，此时路由一定正确完成, 否则这个事件不会触发  
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {   
        if(isset($this->config->application->autofunction) && !empty($this->config->application->autofunction)){
            $autofunction=explode(',',$this->config->application->autofunction);
            foreach ($autofunction as $v) {
                if(is_dir(APPLICATION_PATH.'/'.$v)){
                    $this->getlist(APPLICATION_PATH.'/'.$v,'function');
                }else{
                    throw new Exception(APPLICATION_PATH.'/'.$v.'不是目录');
                }  
            }
        }
        $this->fileload->setLibraryPath(APPLICATION_PATH.'/library',true);
    }  
//分发循环开始之前被触发  
    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {  
       // echo "Plugin DispatchLoopStartup called <br/>\n";  
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
    private function getlist($dir_str,$type){
                try{
                    $handler = opendir($dir_str);
                    $this->fileload->setLibraryPath($dir_str,true);
                    while(($filename = readdir($handler)) !== false) 
                    {
                       
                            if($filename != "." && $filename != ".." && count(scandir($dir_str))>2)
                            {
                                if(is_dir($dir_str.'/'.$filename))
                                {
                                    $this->getlist($dir_str.'/'.$filename,$type);
                                }else{
                                    if(is_file($dir_str.'/'.$filename)){
                                        switch ($type) {
                                            case 'class':
                                                $fname=pathinfo($filename);
                                                $this->fileload->autoload($fname['filename']);
                                                break;
                                        
                                            case 'function':
                                                $this->fileload->import($dir_str.'/'.$filename);
                                                break;
                                        }
                                }else{
                                    throw new Exception($dir_str.'/'.$filename.'不是文件');
                                }


                            }



                        }

                    }
                    closedir($handler);
                }catch(Exception $e){
                    throw new Exception($e->getMessage());
                }
    }
}  

?>