<?php
/**  
 * @author zqf 
 * Autoload.php 
 */  
class AutoloadPlugin extends Yaf\Plugin_Abstract {  
	//在路由之前触发，这个是7个事件中, 最早的一个. 但是一些全局自定的工作, 还是应该放在Bootstrap中去完成  
	var $config;
	var $fileload;
	public function routerStartup(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {  
		$this->config=Yaf\Application::app()->getConfig();
		$this->fileload=Yaf\Loader::getInstance();
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
	}  
//路由结束之后触发，此时路由一定正确完成, 否则这个事件不会触发  
	public function routerShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response) {
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
	private function getlist($dir_str,$type)
	{
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
					}
					else
					{
						if(is_file($dir_str.'/'.$filename))
						{
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
		}catch(\Exception $e){
			throw new Exception($e->getMessage());
		}
	}
}  

?>