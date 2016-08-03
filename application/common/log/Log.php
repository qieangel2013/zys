<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * 日志处理类
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 */
class Log {

    // 日志级别 从上到下，由低到高
    const EMERG     = 'EMERG';  // 严重错误: 导致系统崩溃无法使用
    const ALERT     = 'ALERT';  // 警戒性错误: 必须被立即修改的错误
    const CRIT      = 'CRIT';  // 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
    const ERR       = 'ERR';  // 一般错误: 一般性错误
    const WARN      = 'WARN';  // 警告性错误: 需要发出警告的错误
    const NOTICE    = 'NOTIC';  // 通知: 程序可以运行但是还不够完美的错误
    const INFO      = 'INFO';  // 信息: 程序输出信息
    const DEBUG     = 'DEBUG';  // 调试: 调试信息
    const SQL       = 'SQL';  // SQL：SQL语句 注意只在调试模式开启时有效

    // 日志记录方式
    const SYSTEM    = 0;
    const MAIL      = 1;
    const FILE      = 3;
    const SAPI      = 4;

    // 日志信息
    static $log     =  array();

    // 日期格式
    static $format  =  '[ c ]';
    public static $contents = array();
    /**
     * 记录日志 并且会过滤未经设置的级别
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param boolean $record  是否强制记录
     * @return void
     */
    static function record($message,$level=self::ERR,$record=false) {
        if($record || false !== strpos("EMERG,ALERT,CRIT,ERR,WARN,NOTIC,INFO,DEBUG,SQL",$level)) {
            self::$log[] =   "{$level}: {$message}\r\n";
        }
    }

    /**
     * 日志保存
     * @static
     * @access public
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @param string $extra 额外参数
     * @return void
     */
    static function save($type='3',$destination='',$extra='') {
        $config_obj=Yaf_Registry::get("config");
        $log_config=$config_obj->log->toArray();
        if(empty(self::$log)) return ;
        $type = $type?$type:'3';
        if(self::FILE == $type) { // 文件方式记录日志信息
            if(empty($destination)){
                if($log_config['record']){
                    if(!is_dir($log_config['dir'])){
                        mkdir($log_config['dir'],0777,true);
                    }
                    if(!is_dir($log_config['dir'].'/'.date('Ymd'))){
                        mkdir($log_config['dir'].'/'.date('Ymd'),0777,true);
                    }
                     $destination = $log_config['dir'].'/'.date('Ymd').'/'.date('y_m_d').'.log';
                }else{
                     if(!is_dir(MYPATH.'/logs/')){
                        mkdir(MYPATH.'/logs/',0777,true);
                    }
                    if(!is_dir(MYPATH.'/logs'.'/'.date('Ymd'))){
                        mkdir(MYPATH.'/logs'.'/'.date('Ymd'),0777,true);
                    }
                    $destination = MYPATH.'/logs/'.date('Ymd').'/'.date('y_m_d').'.log';
                }
                
            }
            //检测日志文件大小，超过配置大小则备份日志文件重新生成
            if(is_file($destination) && floor('2097152') <= filesize($destination) )
                  rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
        }else{
            $destination   =   $destination?$destination:'mubiao';
            $extra   =  $extra?$extra:'额外信息';
        }
        $now = date(self::$format);
        error_log($now.' '.get_client_ip().' '.$_SERVER['REQUEST_URI']."\r\n".implode('',self::$log)."\r\n", $type,$destination ,$extra);
         if($log_config['record']){
            self::append($destination,$now.' '.get_client_ip().' '.$_SERVER['REQUEST_URI']."\r\n".implode('',self::$log)."\r\n",$type);
         }
        // 保存后清空日志缓存
        self::$log = array();
        //clearstatcache();
    }

    /**
     * 日志直接写入
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @param string $extra 额外参数
     * @return void
     */
    static function write($message,$level=self::ERR,$type=3,$destination='',$extra='') {
        $config_obj=Yaf_Registry::get("config");
        $log_config=$config_obj->log->toArray();
        $now = date(self::$format);
        $type = $type?$type:3;
        if(self::FILE == $type) { // 文件方式记录日志
            if(empty($destination)){
                if($log_config['record']){
                    if(!is_dir($log_config['dir'])){
                        mkdir($log_config['dir'],0777,true);
                    }
                    if(!is_dir($log_config['dir'].'/'.date('Ymd'))){
                        mkdir($log_config['dir'].'/'.date('Ymd'),0777,true);
                    }
                     $destination = $log_config['dir'].'/'.date('Ymd').'/'.date('y_m_d').'.log';
                }else{
                     if(!is_dir(MYPATH.'/logs/')){
                        mkdir(MYPATH.'/logs/',0777,true);
                    }
                    if(!is_dir(MYPATH.'/logs'.'/'.date('Ymd'))){
                        mkdir(MYPATH.'/logs'.'/'.date('Ymd'),0777,true);
                    }
                    $destination = MYPATH.'/logs/'.date('Ymd').'/'.date('y_m_d').'.log';
                }
            }
            //检测日志文件大小，超过配置大小则备份日志文件重新生成
            if(is_file($destination) && floor('2097152') <= filesize($destination) )
                  rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
        }else{
            $destination   =   $destination?$destination:'mubiao';
            $extra   =  $extra?$extra:'额外信息';
        }
        error_log("{$now} {$level}: {$message}\r\n", $type,$destination,$extra );
        if($log_config['record']){
            self::append($destination,"{$now} {$level}: {$message}\r\n",$type);
         }
        //clearstatcache();
    }
    static function read($filename,$type=''){
        return self::get($filename,'content',$type);
    }

    /**
     * 文件写入
     * @access public
     * @param string $filename  文件名
     * @param string $content  文件内容
     * @return boolean         
     */
    static function put($filename,$content,$type=''){
        $dir         =  dirname($filename);
        if(!is_dir($dir)){
            mkdir($dir,0777,true);
        }
        if(false === file_put_contents($filename,$content)){
            throw new Exception("写入失败".':'.$filename);
        }else{
            self::$contents[$filename]=$content;
            return true;
        }
    }

    /**
     * 文件追加写入
     * @access public
     * @param string $filename  文件名
     * @param string $content  追加的文件内容
     * @return boolean        
     */
    static function append($filename,$content,$type=''){
        if(is_file($filename)){
            $content =  self::read($filename,$type).$content;
        }
        return self::put($filename,$content,$type);
    }

    /**
     * 加载文件
     * @access public
     * @param string $filename  文件名
     * @param array $vars  传入变量
     * @return void        
     */
    static function load($_filename,$vars=null){
        if(!is_null($vars)){
            extract($vars, EXTR_OVERWRITE);
        }
        include $_filename;
    }

    /**
     * 文件是否存在
     * @access public
     * @param string $filename  文件名
     * @return boolean     
     */
    static function has($filename,$type=''){
        return is_file($filename);
    }

    /**
     * 文件删除
     * @access public
     * @param string $filename  文件名
     * @return boolean     
     */
    static function unlink($filename,$type=''){
        unset(self::$contents[$filename]);
        return is_file($filename) ? unlink($filename) : false; 
    }

    /**
     * 读取文件信息
     * @access public
     * @param string $filename  文件名
     * @param string $name  信息名 mtime或者content
     * @return boolean     
     */
    static function get($filename,$name,$type=''){
        if(!isset(self::$contents[$filename])){
            if(!is_file($filename)) return false;
           self::$contents[$filename]=file_get_contents($filename);
        }
        $content=self::$contents[$filename];
        $info   =   array(
            'mtime'     =>  filemtime($filename),
            'content'   =>  $content
        );
        return $info[$name];
    }
}