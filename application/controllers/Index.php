<?php
/**
 * 默认的控制器
 * 当然, 默认的控制器, 动作, 模块都是可用通过配置修改的
 * 也可以通过$dispater->setDefault*Name来修改
 */
class IndexController extends Yaf_Controller_Abstract {
	public function init() {
		$this->_req = $this->getRequest();
        ini_set("display_errors", "On");
        error_reporting(E_ALL | E_STRICT);
	}
	public function indexAction() {
    	$where=array('id' =>37936);
   		//$user=new HbModel('hb_users');//直接实例化给表名就行了，其他跟操作thinkphp一样
		//$result = $user->where($where)->select();
		//echo $user->getlastsql();
		//print_r($result);
		alert("3");
		exit;
	}
    public function dbtestAction() {
        ini_set("display_errors", "On");
        error_reporting(E_ALL | E_STRICT);
        Yaf_Dispatcher::getInstance()->autoRender(FALSE);
        $dbclient=new mysql_dbclient;
        
        //print_r($data);
        for ($i=0; $i <100 ; $i++) { 
            $dbclient->query("INSERT INTO user(name) VALUES('$i')");
            //echo "INSERT INTO user(name) VALUES('$i')";
        }

        $data=$dbclient->query("select * from user");
        $dbclient->close();
        print_r($data);
        exit;
    }
	public function testAction() {
		$where=array('id' =>353);
   		$user=new HbModel('hb_goods');
		$result = $user->where($where)->select();
		print_r($result);
		exit;
	}
	public function diyAction() {
		Yaf_Dispatcher::getInstance()->autoRender(FALSE);
        $id=$this->_req->getQuery('id',1);
		if($this->_req->isPost()){
            $this->diy = new DiyModel();
                   $pre_svg='<?xml version="1.0" standalone="no" ?>'.trim($_POST['svg_val']);
                   $rdate = APPLICATION_PATH . '/public/Uploads/' . date("Ymd", time()); //文件名
                    if (!file_exists($rdate)) {
                        chmod(APPLICATION_PATH . '/public/Uploads/',0777);
                        mkdir($rdate); //创建目录
                    }
                    $savename = $this->create_unique();
                    $path = $rdate . '/' . $savename;
                        if(!$file_svg=fopen($path.'.svg','w+')){
                            echo "不能打开文件 $path.'.svg'";
                            exit;
                        }

                        if(fwrite($file_svg,$pre_svg) === FALSE){
                                echo "不能写入到文件 $path.'.svg'";
                                exit;
                        }

                        echo "已成功写入";
                        fclose($file_svg);
                    //$path= APPLICATION_PATH . '/public/Uploads/' . date("Ymd", time()) .'/m-1';
                    //添加图片转化
                    $im = new Imagick();
                    $im->setBackgroundColor(new ImagickPixel('transparent'));
                    $svg = file_get_contents($path.'.svg');
                    $im->readImageBlob($svg);
                    $im->setImageFormat("png");
                    $am = $im->writeImage($path . '.png');
                    $im->thumbnailImage(579, 660, true); /* 改变大小 */
                    $ams = $im->writeImage($path . '-t.png');
                    $im->clear();
                    $im->destroy();
                    //图片加水印
                    $waterpath = APPLICATION_PATH . '/public/source/source.png';
  
                    $im1 = new Imagick($waterpath);
                    $im2 = new Imagick($path . '.png');
                    $im2->thumbnailImage(600, 600, true);
                    $dw = new ImagickDraw();
                    $dw->setGravity(5);
                    $dw->setFillOpacity(0.1);
                    $dw->composite($im2->getImageCompose(), 0, 0, 50, 0, $im2);
                    $im1->drawImage($dw);

                    if (!$im1->writeImage($path . '-s.png')) {
                        echo '加水印失败';
                        exit;
                    }

                    $im1->clear();
                    $im2->clear();
                    $im1->destroy();
                    $im2->destroy();
                    //exit;
                    //删除相应的文件
                    //unlink($path.'.svg');
                    //unlink($path.'.png');
                    $filepath = '/Uploads/' . date("Ymd", time()) . '/' . $savename . '.png';
                    $data['origin_img'] = $filepath;
                    $data['diy_synthetic_img'] = '/Uploads/' . date("Ymd", time()) . '/' . $savename . '-s.png';
                    $data['diy_preview_img'] = '/Uploads/' . date("Ymd", time()) . '/' . $savename . '-s.png';
                    $data['user_id'] = 0;
                    $data['source'] = 3;
                    $data['created'] = date("Y-m-d H:i:s", time());
                    $datas['image'] = $data['diy_synthetic_img'];
                    $datas['user_id'] = 0;
                    $datas['source'] = 2;
                    $datas['state'] = 1;
                    $datas['createtime'] = date("Y-m-d H:i:s", time());
                    $datas['updatetime'] = date("Y-m-d H:i:s", time());
                    $diy_picture_id = $this->diy->adddiy($data);
                    //$datas['use'] = $tool;
                    //$datas['author'] = $userinfo['mobile'];
                    $this->userpicture = new UserpictureModel();
                    $datas['diy_picture_id'] = $diy_picture_id;
                    $this->userpicture->adduserpicture($datas);
                    $response_data['origin_img'] = '/Uploads/' . date("Ymd", time()) . '/' . $savename . '.png';
                    $response_data['diy_preview_img'] = '/Uploads/' . date("Ymd", time()) . '/' . $savename . '-s.png';
                    $response_data['diy_thumb_img'] = '/Uploads/' . date("Ymd", time()) . '/' . $savename . '-t.png';
                    $response_data['diy_picture_id'] = $diy_picture_id;
                    //$this->getView()->display("/index/buy.html",$response_data);
                    $this->_session->set('diypicture',$response_data);
                    $this->_redis = new phpredis();
                    $this->_redis->set($diy_picture_id,json_encode($response_data));
                    header("Location:/index/share?diy=".$diy_picture_id);
                }else{
                    switch ($id) {
                        case 1:
                            $this->getView()->display("index/diy_1.html");
                            break;
                        case 2:
                            $this->getView()->display("index/diy_2.html");
                            break;
                        case 3:
                            $this->getView()->display("index/diy_3.html");
                            break;
                        case 4:
                            $this->getView()->display("index/diy_4.html");
                            break;
                        case 5:
                            $this->getView()->display("index/diy_5.html");
                            break;
                        case 6:
                            $this->getView()->display("index/diy_6.html");
                            break;
                        case 7:
                            $this->getView()->display("index/diy_7.html");
                            break;
                    }
                    
                }
	}
	public function shareAction() {
        //微信分享
        $jssdk = new wx_share_wxshare("你的appId", "你的appSecret");
        $signPackage = $jssdk->GetSignPackage();
        $data['appId']=$signPackage['appId'];
        $data['nonceStr']=$signPackage['nonceStr'];
        $data['timestamp']=$signPackage['timestamp'];
        $data['signature']=$signPackage['signature'];
        $this->_redis = new phpredis();
        $this->_redis->set('token',json_encode($data));
        $diyid=$this->_req->getQuery('diy',0);
        $diypicture=$this->_session->get('diypicture');
        if(!$diypicture){
            $diydata=$this->_redis->get($diyid);
            $this->_session->set('diypicture',json_decode($diydata));
            $diypicture=json_decode($diydata);
        }
        //ini_set("display_errors", "On");
        //error_reporting(E_ALL | E_STRICT);
        //$data=file_get_contents(APPLICATION_PATH."/public/json/token.json");
        //$data=$this->_redis->get('token');
        $this->getView()->assign("token", json_encode($data));
        $this->getView()->assign("data", $diypicture);
        $this->getView()->display('index/share.html');
	}
	public function buyAction() {
        //微信分享
        $jssdk = new wx_share_wxshare("你的appId", "你的appSecret");
        $signPackage = $jssdk->GetSignPackage();;
        $data['appId']=$signPackage['appId'];
        $data['nonceStr']=$signPackage['nonceStr'];
        $data['timestamp']=$signPackage['timestamp'];
        $data['signature']=$signPackage['signature'];
        $this->_redis = new phpredis();
        $this->_redis->set('token',json_encode($data));
        $diypicture=$this->_session->get('diypicture');
        $userShare=$this->_req->getQuery('userShare',0);
        $this->getView()->assign("data", $diypicture);
        $this->getView()->assign("token", json_encode($data));
        $this->getView()->assign("userShare", $userShare);
        $this->getView()->display('index/buy.html');
	}
	//图片上传
	 public function uploadpicAction() {
        if (!empty($_FILES)) {
        	$config = Yaf_Application::app()->getConfig()->upload->config->toArray();
        	$ftpconfig = Yaf_Application::app()->getConfig()->ftp->config->toArray();
            $upload = new Upload($config, 'Ftp',$ftpconfig); 
            $info = $upload->upload();
            if (!$info) {// 上传错误提示错误信息
                echo $upload->getError();
            } else {// 上传成功
                if (!empty($info["UpLoadFile"]))
                    $pic = array("cate_pic" => $info["UpLoadFile"]['savepath'] . $info["UpLoadFile"]['savename']);
                print_r($pic);
            }
        }
        
    }
     private function create_unique() {
        $data = substr(date("ymdHis"), 2, 8) . mt_rand(100000, 999999);
        return $data;
    }
    public function swoolehttpAction(){
        Yaf_Dispatcher::getInstance()->autoRender(FALSE);
        $where=array('id' =>37936);
        $user=new HbModel('hb_users');//直接实例化给表名就行了，其他跟操作thinkphp一样
        $result = $user->where($where)->select();
        //echo $user->getlastsql();
        // echo json_encode( $result);
         echo json_encode($result);//返回结果{"id":37936}
    }
    public function swoolesocketAction(){
        Yaf_Dispatcher::getInstance()->autoRender(FALSE);
        $this->getView()->display("index/swoolesocket.html");
    }
}
