<?php
/*
 * yield 异步启动服务
 * @author qieangel2013
 */
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);
// 检查扩展
if(!extension_loaded('yaf'))
{
    exit("Please install yaf extension.\n");
}
if(!extension_loaded('redis'))
{
    exit("Please install redis extension.\n");
}
if(!extension_loaded('swoole'))
{
    exit("Please install swoole extension.\n");
}
//检查是否为cli模式
if(php_sapi_name() !== 'cli'){
    exit("Please use php cli mode.\n");
}
$cmd="/usr/local/php/bin/php";//php的绝对路径
function syncServer()
{
    echo (yield ['rpc']) ."\n";
    echo (yield ['mysqlpool']) ."\n";
    echo (yield ['vmstat']) ."\n";
    echo (yield ['swoolelive']) ."\n";
    echo (yield ['task']) ."\n";
    echo (yield ['distributed']) ."\n";
}
//异步调用器
function asyncCaller(Generator $gen)
{
    global $cmd;
    $r = $gen->current();
    if (isset($r)) {
        switch ($r[0]) {
            case 'rpc':
                foreach(glob(__DIR__.'/rpc/*.php') as $start_file)
                {
                    exec($cmd.' '.$start_file);
                }
                echo "rpc SERVEICE START ...\n";//thrift 的rpc远程调用服务
                $gen->send('rpc SERVEICE SUCCESS！');
                asyncCaller($gen);
                break;
            case 'mysqlpool':
                 foreach(glob(__DIR__.'/mysql/*.php') as $start_file)
                {
                    exec($cmd.' '.$start_file);
                }
                echo "mysqlpool SERVEICE START ...\n";//数据库连接池服务
                $gen->send('mysqlpool SERVEICE SUCCESS！');
                asyncCaller($gen);
                break;
            case 'vmstat':
                 foreach(glob(__DIR__.'/swoole/Vm*.php') as $start_file)
                {
                    exec($cmd.' '.$start_file);
                }
                echo "vmstat SERVEICE START ...\n";//硬件监控服务
                $gen->send('vmstat SERVEICE SUCCESS！');
                asyncCaller($gen);
                break;
            case 'swoolelive':
                 foreach(glob(__DIR__.'/swoole/SwooleLiveServer.php') as $start_file)
                {
                    exec($cmd.' '.$start_file);
                }
                echo "swoolelive SERVEICE START ...\n";//网络直播服务
                $gen->send('swoolelive SERVEICE SUCCESS!');
                asyncCaller($gen);
                break;
             case 'task':
                 foreach(glob(__DIR__.'/swoole/Task*.php') as $start_file)
                {
                    exec($cmd.' '.$start_file);
                }
                echo "task SERVEICE START ...\n";//任务服务器服务
                $gen->send('task SERVEICE SUCCESS!');
                asyncCaller($gen);
                break;
            case 'distributed':
                 foreach(glob(__DIR__.'/distributed/*.php') as $start_file)
                {
                    exec($cmd.' '.$start_file);
                }
                echo "distributed SERVEICE START ...\n";//分布式服务器通讯服务
                $gen->send('distributed SERVEICE SUCCESS!');
                asyncCaller($gen);
                break;
            default:
                $gen->send('no method');
                asyncCaller($gen);
                break;
        }
    }
}
$ser_ser=$argv;
if(!isset($ser_ser[1])){
     exit("No argv.\n");
 }else{
switch ($ser_ser[1]) {
    case 'start':
        asyncCaller(syncServer());
        break;
    case 'stop':
        exec("ps -ef | grep -E '".$cmd."|vmstat' |grep -v 'grep'| awk '{print $2}'|xargs kill -9 > /dev/null 2>&1 &");
        echo "Kill all process success.\n"; 
        break;
     case 'restart':
        exec("ps -ef | grep -E '".$cmd."|vmstat' |grep -v 'grep'| awk '{print $2}'|xargs kill -9 > /dev/null 2>&1 &");
        echo "Kill all process success.\n"; 
        asyncCaller(syncServer());
        break;
    default:
        exit("Not support this argv.\n");
        break;
    }
 }
?>
