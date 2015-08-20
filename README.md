yaf项目快速开发，集成了thinkphp的db操作类，upload操作，加入redis操作以及微信分享，随后会加入微信支付
===================================
###db操作类使用方法如下：
          $where=array('id' =>37936);
   		    $user=new HbModel('hb_users');//直接实例化给表名就行了，其他跟操作thinkphp一样
		      $result = $user->where($where)->select();
		      echo $user->getlastsql();
		      print_r($result);
	      	exit;
###redis操作使用方法如下：
          $this->_redis = new phpredis();
          $this->_redis->set('token',1);
###upload操作使用方法如下：
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
###微信分享操作使用方法如下：
            //微信分享
        $jssdk = new JSSDK("你的appId", "你的appSecret");
        $signPackage = $jssdk->GetSignPackage();;
        $data['appId']=$signPackage['appId'];
        $data['nonceStr']=$signPackage['nonceStr'];
        $data['timestamp']=$signPackage['timestamp'];
        $data['signature']=$signPackage['signature'];
        $this->getView()->assign("token", json_encode($data));
### 以上具体的使用方法，控制器里都有，随后会加入更多更能，有什么问题可以及时联系我 qieangel@hotmail.com
