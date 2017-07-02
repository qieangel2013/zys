# zys高性能服务框架
[![Build Status](https://api.travis-ci.org/qieangel2013/zys.svg)](https://travis-ci.org/qieangel2013/zys)
[![Packagist](https://img.shields.io/badge/packagist-passing-ff69b4.svg)](https://packagist.org/packages/qieangel2013/zys)
![Supported PHP versions: >=5.5](https://img.shields.io/badge/php-%3E%3D5.5-blue.svg)
![License](https://img.shields.io/badge/license-Apache%202-yellow.svg)
### 核心特性
	1.基于swoole提供分布式服务器通讯服务
	2.基于thrift提供rpc远程调用服务
	3.基于HTML5提供在线网络直播平台服务
	4.基于swoole提供同步异步数据库连接池服务
	5.基于swoole提供异步任务服务器投递任务服务
	6.基于vmstat提供服务器硬件实时监控服务
	7.基于yac、yaconf提供共享数据、配置服务
	8.基于zqf提供高并发计数器、红包、二维码服务
	9.很好的支持网页版console的shell服务
	10.基于hprose提供rpc远程调用、推送等服务
	11.基于zqfHB的php扩展统计php脚本执行时间的服务
### Nginx 下配置
	location / {
        if (!-e $request_filename) {
           rewrite ^/(.*)$ /index.php?$1 last;
        }
    }
### 服务启动
	需要php以cli模式运行/server/server.php
        php server.php start
        php server.php stop
        php server.php restart
### composer 安装
	{
    		"require": {
        		"qieangel2013/zys": "2.0.0.1"
		}
	}
### 分布式服务器通讯服务
	建立多个服务器之间进行数据通信服务，服务自动连接在线服务器，支持热拔，启动服务后自动连接，无需人为干预
	注意事项：
		需要在conf/application.conf里配置端口和监听、日志等
		需要有一个redis服务器，并且分布式服务器都能连接redis
		web端可以直接调用服务
		使用如下
		//注意：type为sql、file，要是需要别的功能，自己定义
        	if($_FILES){
        	//数据同步
        	$sql = array('type'=>'sql','data'=>'show tables');
        	var_dump(distributed::getInstance()->query($sql));
        	//文件同步（不用安装rsync+inotify就可以实现文件同步，并且是触发式的占用很小的资源，调用sendfile零复制）
            	$dir_pre=MYPATH.'/public/uploads/';
            	if(!is_dir($dir_pre.date('Ymd'))){
                	mkdir($dir_pre.date('Ymd'),0777,true);
        	 }
            	if(is_uploaded_file($_FILES['file']['tmp_name'])){ 
                $upname=explode('.',$_FILES['file']['name']);
                $filename=uniqid().substr(time(),-4).'.'.$upname[1];
                if(move_uploaded_file($_FILES['file']['tmp_name'],$dir_pre.date('Ymd').'/'.$filename)){  
                    echo "Stored in: " . $dir_pre.date('Ymd').'/'.$filename; 
                    $fileinfo = array('type'=>'file','data'=>array('path' =>'/public/uploads/'.date('Ymd').'/'.$filename,'size'=>$_FILES['file']['size'],'ext'=>$upname[1]));
                    var_dump(distributed::getInstance()->queryfile($fileinfo));
                	}else{  
                    	echo 'Stored failed:file save error';  
                	}  
            	}else{
                echo 'Stored failed:no post ';  
            }
       	}
       		本地访问：http:/localhost/index/distributed/
       		架构图
![](https://github.com/qieangel2013/yaf/blob/master/public/images/jg.png)
       		执行结果如下
![](https://github.com/qieangel2013/yaf/blob/master/public/images/dis1.png)![](https://github.com/qieangel2013/yaf/blob/master/public/images/dis2.png)
### thrift的rpc远程调用
	本地访问http://localhost/index/rpc （返回0表示成功）
### 数据库连接池使用方法
	服务文件在/server/mysql/DbServer.php
	简单地封装文件在/application/library/mysql/dbclient.php
	配置在conf/application.ini中
	;数据库连接池配置
	DbServer.async=true   //配置是同步执行还是异步执行，默认不配置代表异步执行，同步执行设置为false
	DbServer.multiprocess=false //配置是否启用多进程，默认不配置代表单进程阻塞模式，多进程模式要设置为true
	DbServer.pool_num=20  //配置连接池mysql的数量
	DbServer.port=9501
	DbServer.logfile="/server/log/DbServer.log"
	DbServer.localip="192.168.2.13"
	使用方法：
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
        本地访问：http:/localhost/index/dbtest/
### 数据库连接池多进程执行如下
![](https://github.com/qieangel2013/yaf/blob/master/public/images/multiprocess.png)
### swoole实现简单的视频直播（可以实时传音频、视频、聊天）
	录制视频页面 http://localhost/index/swoolelivecamera
	接受视频页面 http://localhost/index/swoolelive
	采用vue+html5实现的聊天ui界面
http://www.jduxiu.com:88/live/index/index (演示地址)
![](https://github.com/qieangel2013/yaf/blob/master/public/images/testlive.png)
### vmstat服务器监控
	本地访问http://localhost/vmstat/
	执行如下：
![](https://github.com/qieangel2013/yaf/blob/master/public/images/vmstats.png)
### yac、yaconf提供共享数据、配置使用如下
	需要安装php扩展yac、yaconf
	//注意：需要安装yaconf扩展，并且yaconf.directory=/tmp/yaconf 必须在php.ini里设置，不能动态加载
        echo Yaconf::get("conf.zqf");
        //注意：需要安装yac扩展，用于存储共享变量，下面的实例作为高并发计数器
        $yac = new Yac();
        $count=$yac->get('zqf');
        if(!$count){
            $yac->set('zqf', 1);
        }else{
            $yac->set('zqf', $count+0.5);
        }
        echo $count;
### 高并发计数器、红包、二维码使用如下
	需要安装php扩展zqf
	首先安装php扩展zqf.so
	phpize来安装
	然后在php文件调用
	dl('zqf.so');或者phpini里加载（[项目地址](https://github.com/qieangel2013/zqf)）
	$obj=new zqf();
	$counter= $obj->autoadd(0,1,0);（声明只针对多线程）
	echo $counter;
	红包第一个参数是红包总额，第二个人参数红包数量，第三个参数默认代表拼手气红包，设置为1的话为普通红包
	拼手气红包
	$hongb= $obj->hongbao(10,8);或者$hongb= $obj->hongbao(10,8,0);返回数组为Array ( [0] => 1.33 [1] => 1.02 [2] => 1.28 [3] => 0.44 [4] => 1.37 [5] => 0.81 [6] => 1.81 [7] => 1.94 )
	普通红包，每个人数额一样设置第三个参数
	$hongb= $obj->hongbao(10,8,1);返回数组为Array ( [0] => 1.25 [1] => 1.25 [2] => 1.25 [3] => 1.25 [4] => 1.25 [5] => 1.25 [6] => 1.25 [7] => 1.25 )
	var_dump($hongb);
	$obj->savefile('https://www.baidu.com/s?wd=昌平香堂','./test.png',500);第一个参数是url，第二参数是保存路径，第三个参数是二维码长或者宽
	$obj->savefile('https://www.baidu.com/s?wd=昌平香堂','./test.png',500,1);第一个参数是url，第二参数是保存路径，第三个参数是二维码长或者宽，第四个参数是决定是否透明，默认是不透明的
### php脚本执行时间统计扩展
	wget https://github.com/redis/hiredis/archive/v0.13.3.tar.gz
	tar zxvf v0.13.3.tar.gz
	cd hiredis-0.13.3
	make
	make install
	如果出现libhiredis.so.0.13: cannot open shared object file: No such file or directory in Unknown on line 0
	vi /etc/ld.so.conf
	文件末尾添加  /usr/local/lib
	然后执行ldconfig
	./phpize（[项目地址](https://github.com/qieangel2013/zqfHB)）
	./configure --with-php-config=/usr/local/php/bin/php-config
	make
	make install
	add zqfHB.so to php.ini
	extension=zqfHB.so
	[zqfHB]
	zqfHB.slow_maxtime=10000(单位微妙1s=1000000us,改参数是页面加载超过这个时间会统计)
	zqfHB.type=1（1代表redis 2代表memcache，由于memcache性能处理有点低，暂时不开放）
	zqfHB.auth=123456(如果redis没有密码，此项不必配置，如果有密码，必须配置此项)
	zqfHB.host=192.168.102.163
	zqfHB.port=6379
	使用：
	把web里的所有文件复制到网站目录下或者放在其他目录下
	直接执行http://localhost/web/
	效果图：
![](https://github.com/qieangel2013/zqfHB/blob/master/images/img1.png)
![](https://github.com/qieangel2013/zqfHB/blob/master/images/img2.png)
### 网页版console的shell使用如下
	本地访问http://localhost/console/console.php
### hprose的使用如下
	echo hprose::getInstance()->getdata();
	本地访问：http://localhost/index/hprose
### 交流使用
	zys框架交流群：337937322
### License

Apache License Version 2.0 see http://www.apache.org/licenses/LICENSE-2.0.html
### 如果你对我的辛勤劳动给予肯定，请给我捐赠，你的捐赠是我最大的动力
![](https://github.com/qieangel2013/zys/blob/master/public/images/pay.png)
[项目捐赠列表](https://github.com/qieangel2013/zys/wiki/%E9%A1%B9%E7%9B%AE%E6%8D%90%E8%B5%A0)
