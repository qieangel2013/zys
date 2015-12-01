<?php
class swoole_task{
	public static function demcode($data){
		$task=new swoole_task();
		$redis_con= new phpredis();
    		$YMD=$data['Ymd'];
		$numall=(int)($data['prenum']+$data['appendnum']);
		$url="http://www.smesauz.com/index/showdemcode/?pi=";
		if($data['is_pay']){
      if(isset($data['explodenum']) && !empty($data['explodenum'])){
        for ($i=$data['prenum']+1; $i <= $numall; $i++) { 
        $result=array('pi' =>$task->encrypt($data['goods_id'].'_'.$data['id'].'_'.$i,true),'token'=>md5($data['goods_id'].'_'.$data['id'].'_'.$i),'num'=>0);
        $url .=$task->encrypt($data['goods_id'].'_'.$data['id'].'_'.$i,true).'&token='.md5($data['goods_id'].'_'.$data['id'].'_'.$i);
        $task->twodemcode($data['goods_id'],$data['id'],$i,$url,$YMD,$data['explodenum'],$data['explodecount']);
        $redis_con->hashSet($data['goods_id'].'_'.$data['id'],array($i =>json_encode($result)));
        $url="http://www.smesauz.com/index/showdemcode/?pi=";
      }
    
    //$YMD=date('Ymd',time()); 
    $path_pre="/www/www/smes";
    $path = "/public/twodecode/";  
    //if(is_readable($path_pre.$path.$YMD.'/'.$data['goods_id'].'/'.$data['id'])){  
            $cmd="cd ".$path_pre.$path.$YMD.'/'.$data['goods_id'].'/'.$data['id'].'/'.$data['explodecount']."/;tar -jcvf ".$path_pre.$path.$YMD.'/'.$data['goods_id'].'/'.$data['id'].'/'.$data['explodecount'].".tar.bz2  ./;rsync --delete-before -a -H --progress --stats /www/blank/ ./;cd ".$path_pre.$path.";chown -R www.www *";  
            //echo $cmd;
            //echo "<br/>";
            $result=exec($cmd); 
            //print_r($result);
            //exit;
        //}
    if($data['explodenum']==$data['explodecount']){
        $cmd="cd ".$path_pre.$path.$YMD.'/'.$data['goods_id'].'/'.$data['id']."/;zip -r ".$path_pre.$path.$YMD.'/'.$data['goods_id'].'/'.$data['id'].".zip  ./*.bz2;cd ".$path_pre.$path.";chown -R www.www *";  
            //echo $cmd;
            //echo "<br/>";
            $result=exec($cmd); 
        //连接数据库
        $con = mysql_connect("192.168.1.20","root","root");
        mysql_query("set names 'utf8'");//编码转化
        $database='smes';
        if(!$con)
        {
            die('Could not connect: ' . mysql_error());
        }else{
            $db_selecct=mysql_select_db($database,$con);//选择数据库
            if(!$db_selecct)
            {
                die("could not to the database</br>".mysql_error());    
            }
           $query="UPDATE smes_order SET downurl='".$path.$YMD.'/'.$data['goods_id'].'/'.$data['id'].".zip"."',is_down=1 WHERE id=".$data['id'];
           $result=mysql_query($query);//执行查询
       }
        mysql_close($con);
    }
    }
      }else{
			for ($i=$data['prenum']+1; $i <= $numall; $i++) { 
				$result=array('pi' =>$task->encrypt($data['goods_id'].'_'.$data['id'].'_'.$i,true),'token'=>md5($data['goods_id'].'_'.$data['id'].'_'.$i),'num'=>0);
				$url .=$task->encrypt($data['goods_id'].'_'.$data['id'].'_'.$i,true).'&token='.md5($data['goods_id'].'_'.$data['id'].'_'.$i);
				$task->twodemcode($data['goods_id'],$data['id'],$i,$url,$YMD);
				$redis_con->hashSet($data['goods_id'].'_'.$data['id'],array($i =>json_encode($result)));
				$url="http://www.smesauz.com/index/showdemcode/?pi=";
			}
		
		//$YMD=date('Ymd',time()); 
		//$path_pre="/www/www/smes";
    $path_pre="/var/www/smes";
		$path = "/public/twodecode/";  
		//if(is_readable($path_pre.$path.$YMD.'/'.$data['goods_id'].'/'.$data['id'])){  
            $cmd="cd ".$path_pre.$path.$YMD.'/'.$data['goods_id'].'/'.$data['id']."/;zip -r ".$path_pre.$path.$YMD.'/'.$data['goods_id'].'/'.$data['id'].".zip  ./;ls | xargs -n 10 rm -rf ;cd ".$path_pre.$path.";chown -R www.www *";  
            //echo $cmd;
            //echo "<br/>";
            $result=exec($cmd); 
            //print_r($result);
            //exit;
        //}
        
        //连接数据库
        $con = mysql_connect("192.168.1.20","root","root");
        mysql_query("set names 'utf8'");//编码转化
        $database='smes';
        if(!$con)
        {
            die('Could not connect: ' . mysql_error());
        }else{
            $db_selecct=mysql_select_db($database,$con);//选择数据库
            if(!$db_selecct)
            {
                die("could not to the database</br>".mysql_error());    
            }
           $query="UPDATE smes_order SET downurl='".$path.$YMD.'/'.$data['goods_id'].'/'.$data['id'].".zip"."',is_down=1 WHERE id=".$data['id'];
           $result=mysql_query($query);//执行查询
       }
        mysql_close($con);
    }
        }
        }
	}
	public static function savefd($fd){
		//$where=array('id' =>37936);
		$redis_con=new phpredis();
	if($redis_con->listSize('fd')){
		$redis_con->listPush('fd',$fd,0,1);
	}else{
		$redis_con->listPush('fd',$fd);
	}
		//file_put_contents( __DIR__ .'/log.txt' , $fd);
	}
	public static function getfd(){
		$redis_con=new phpredis();
		$result=$redis_con->listGet('fd',0,-1);
		 //$m = file_get_contents( __DIR__ .'/log.txt');
		//echo $m;
		echo json_encode($result);
	}
	public static function removefd($fd){
		$redis_con=new phpredis();
		$redis_con->listRemove('fd',$fd);
	}
	private function encrypt($str,$toBase64=false,$key="www.smesauz.com20380201"){
        $r = md5($key);
        $c=0;
        $v = "";
        $len = strlen($str);
        $l = strlen($r);
        for ($i=0;$i<$len;$i++){
         if ($c== $l) $c=0;
         $v.= substr($r,$c,1) .
             (substr($str,$i,1) ^ substr($r,$c,1));
         $c++;
        }
        if($toBase64) {
            return base64_encode($this->ed($v,$key));
        }else {
            return $this->ed($v,$key);
        }

    }
private function ed($str,$key="www.smesauz.com20380201") {
      $r = md5($key);
      $c=0;
      $v = "";
      $len = strlen($str);
      $l = strlen($r);
      for ($i=0;$i<$len;$i++) {
         if ($c==$l) $c=0;
         $v.= substr($str,$i,1) ^ substr($r,$c,1);
         $c++;
      }
      return $v;
   }
 private function twodemcode($goods_id,$order_id,$pathcode,$url,$ymd,$explodenum=0,$explodecount=0){
    //$yafins=Yaf_Loader::getInstance();
    //$yafins->import(APPLICATION_PATH.'/phpqrcode/phpqrcode.php');
    //$host=$_SERVER["HTTP_HOST"];    
    $data =$url;  
        // 纠错级别：L、M、Q、H  
    $level = 'L';  
        // 点的大小：1到10,用于手机端4就可以了  
    $size = 10;  
        // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false  
    $path = "/www/www/smes/public/twodecode/";  
    $YMD=$ymd; 
        if(!file_exists($path))   
        {   
            mkdir($path, 0700);  
        }  
        if(!file_exists($path.$YMD)){
            mkdir($path.$YMD, 0700); 
        }
        if(!file_exists($path.$YMD.'/'.$goods_id)){
            mkdir($path.$YMD.'/'.$goods_id, 0700);   
        }
        if(!file_exists($path.$YMD.'/'.$goods_id.'/'.$order_id)){
            mkdir($path.$YMD.'/'.$goods_id.'/'.$order_id, 0700); 
        }
        if($explodecount){
          if(!file_exists($path.$YMD.'/'.$goods_id.'/'.$order_id.'/'.$explodecount)){
            mkdir($path.$YMD.'/'.$goods_id.'/'.$order_id.'/'.$explodecount, 0700); 
        }
        }
        // 生成的文件名  
        if($explodecount){
          $fileName = $path.$YMD.'/'.$goods_id.'/'.$order_id.'/'.$explodecount.'/'.$pathcode.'.png'; 
        }else{
          $fileName = $path.$YMD.'/'.$goods_id.'/'.$order_id.'/'.$pathcode.'.png'; 
        }
        $obj=new zqf();
        $obj->savefile($data,$fileName,500); //需要安装zqf扩展生成二维码
        //echo $fileName.'<br/>';
        //ob_end_clean();//清空缓冲区  
        //QRcode::png($data,$fileName, $level, $size); 
       // QRcode::png($data,$fileName, $level, $size); 
}

}
