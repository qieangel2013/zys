#zys高性能服务框架
[![Build Status](https://img.shields.io/wercker/ci/wercker/docs.svg)](https://packagist.org/packages/qieangel2013/zys)
![Supported PHP versions: >=5.5](https://img.shields.io/badge/php-%3E%3D5.5-blue.svg)
![Supported SWOOLE versions: >=1.8.0](https://img.shields.io/badge/swoole-%3E%3D1.8.0-orange.svg)
![License](https://img.shields.io/badge/license-Apache%202-yellow.svg)
###核心特性
	1.基于swoole提供同步异步数据库连接池服务
	2.基于thrift提供rpc远程调用服务
	3.基于HTML5提供在线网络直播平台服务
	4.基于vmstat提供服务器硬件实时监控服务
	5.基于yac、yaconf提供共享数据、配置服务
	6.基于zqf提供高并发计数器、红包、二维码服务
	7.很好的支持网页版console的shell服务
	8.基于swoole提供分布式服务器通讯服务（开发中）
###服务启动
	需要php以cli模式运行/server/server.php
        php server.php start
        php server.php stop
        php server.php restart
###composer 安装
{
    "require": {
        "qieangel2013/zys": "0.1.1"
    }
}
###thrift的rpc远程调用
	本地访问http://localhost/index/rpc （返回0表示成功）
###数据库连接池使用方法
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
###数据库连接池多进程执行如下
![](https://github.com/qieangel2013/yaf/blob/master/public/images/multiprocess.png)
###swoole实现简单的视频直播（可以实时传音频、视频、聊天）
	录制视频页面 http://localhost/index/swoolelivecamera
	接受视频页面 http://localhost/index/swoolelive
![](https://github.com/qieangel2013/yaf/blob/master/public/images/testlive.png)
###vmstat服务器监控
	本地访问http://localhost/vmstat/
	执行如下：
![](https://github.com/qieangel2013/yaf/blob/master/public/images/vmstats.png)
###yac、yaconf提供共享数据、配置使用如下
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
###高并发计数器、红包、二维码使用如下
	需要安装php扩展zqf
	首先安装php扩展zqf.so
	phpize来安装
	然后在php文件调用
	dl('zqf.so');或者phpini里加载（[https://github.com/qieangel2013/zqf）
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
###网页版console的shell使用如下
	本地访问http://localhost/console
###交流使用
	zys框架交流群：337937322
### License

Apache License Version 2.0 see http://www.apache.org/licenses/LICENSE-2.0.html
###如果你对我的辛勤劳动给予肯定，请给我捐赠，你的捐赠是我最大的动力
![](https://github.com/qieangel2013/zys/blob/master/public/images/ali.png)
