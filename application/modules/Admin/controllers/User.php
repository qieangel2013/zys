<?php
/*
|---------------------------------------------------------------
|  Copyright (c) 2016
|---------------------------------------------------------------
| 文件名称：控制器用户操作类
| 功能 :用户信息操作
| 作者：qieangel2013
| 联系：qieangel2013@gmail.com
| 版本：V1.0
| 日期：2016/2/25 15:42 星期四
|---------------------------------------------------------------
*/
class UserController extends Yaf_Controller_Abstract {
    private $title;
    private $UserAdd_Url;
    private $UserDel_Url;
    private $UserUp_Url;
    private $UserInfo_Url;
    /**
     * 名称:  初始化一些配置文件
     */
	public function init() {
		$this->_req = $this->getRequest();
        $config = Yaf_Application::app()->getConfig();
        $this->title        = $config['web']['title'];
        $this->UserAdd_Url  = $config['user']['add'];
        $this->UserDel_Url  = $config['user']['del'];
        $this->UserUp_Url   = $config['user']['up'];
        $this->UserInfo_Url = $config['user']['userinfo'];
        ini_set("display_errors", "On");
        error_reporting(E_ALL | E_STRICT);
	}
    /**
     * 名称:  用户列表
     */
	public function indexAction() {
       // $_SESSION['zqf']='11111111';
        //$_SESSION['userinfo']=array('userid' =>1,'phone'=>18614064093);
       // print_r($_SESSION);
       // exit;
      $node[0]['text']="用户管理";

      $childd[0]['text']="员工列表";
      $childd[0]['url']="/user/login/";
      $childd[0]['args']['id']=88;
      $childd[0]['args']['userid']=85;
      $childd[1]['text']="添加员工";
      $childd[1]['url']="/user/menu/";
      $childd[1]['args']['id']=98;
      $childd[1]['args']['userid']=95;
      $child[0]['text']="用户中心";
      $child[0]['nodes']=$childd;
      unset($childd[0]['url']);
      unset($childd[0]['args']['id']);
      unset($childd[0]['args']['userid']);
      unset($childd[1]['url']);
      unset($childd[1]['args']['id']);
      unset($childd[1]['args']['userid']);
      $child[1]['text']="菜单管理";
      $childd[0]['text']="菜单维护";
      $childd[1]['text']="广告设置";
      $child[1]['nodes']=$childd;

      $node[0]['nodes']=$child;




      $node[1]['text']="财务系统";

      $child[0]['text']="财务管理";
      $childd[0]['text']="财务人员";
      $childd[1]['text']="财务提现";
      $child[0]['nodes']=$childd;

      $child[1]['text']="转账管理";
      $childd[0]['text']="删除转账";
      $childd[1]['text']="添加转账";
      $child[1]['nodes']=$childd;
      $node[1]['nodes']=$child;


      $node[2]['text']="订单系统";
      $childd[0]['text']="订单列表";
      $childd[1]['text']="订单删除";
      $child[0]['text']="订单管理";
      $child[0]['nodes']=$childd;

      $child[1]['text']="返修订单";
      $childd[0]['text']="同城订单";
      $childd[1]['text']="异地订单";
      $child[1]['nodes']=$childd;

      $node[2]['nodes']=$child;


      $node[3]['text']="角色系统";
      $childd[0]['text']="角色列表";
      $childd[1]['text']="添加角色";
      $child[0]['text']="角色管理";
      $child[0]['nodes']=$childd;

      $child[1]['text']="管理角色";
      $childd[0]['text']="普通角色";
      $childd[1]['text']="管理角色";
      $child[1]['nodes']=$childd;

      $node[3]['nodes']=$child;

      $node[4]['text']="CRM";
      $childd[0]['text']="添加用户";
      $childd[1]['text']="用户列表";
      $child[0]['text']="用户管理";
      $child[0]['nodes']=$childd;

      $child[1]['text']="权限管理";
      $childd[0]['text']="添加权限";
      $childd[1]['text']="权限列表";
      $child[1]['nodes']=$childd;

      $node[4]['nodes']=$child;

      $node[5]['text']="通用系统";
      $childd[0]['text']="应用设置";
      $childd[1]['text']="应用添加";
      $child[0]['text']="通用设置";
      $child[0]['nodes']=$childd;

      $child[1]['text']="网站配置";
      $childd[0]['text']="系统配置";
      $childd[1]['text']="基本配置";
      $child[1]['nodes']=$childd;

      $node[5]['nodes']=$child;



      $node[6]['text']="用户管理";

      $childd[0]['text']="员工列表";
      $childd[1]['text']="添加员工";
      $child[0]['text']="用户中心";
      $child[0]['nodes']=$childd;

      $child[1]['text']="菜单管理";
      $childd[0]['text']="菜单维护";
      $childd[1]['text']="广告设置";
      $child[1]['nodes']=$childd;

      $node[6]['nodes']=$child;




      $node[7]['text']="财务系统";

      $child[0]['text']="财务管理";
      $childd[0]['text']="财务人员";
      $childd[1]['text']="财务提现";
      $child[0]['nodes']=$childd;

      $child[1]['text']="转账管理";
      $childd[0]['text']="删除转账";
      $childd[1]['text']="添加转账";
      $child[1]['nodes']=$childd;
      $node[7]['nodes']=$child;


      $node[8]['text']="订单系统";
      $childd[0]['text']="订单列表";
      $childd[1]['text']="订单删除";
      $child[0]['text']="订单管理";
      $child[0]['nodes']=$childd;

      $child[1]['text']="返修订单";
      $childd[0]['text']="同城订单";
      $childd[1]['text']="异地订单";
      $child[1]['nodes']=$childd;

      $node[8]['nodes']=$child;


      $node[9]['text']="角色系统";
      $childd[0]['text']="角色列表";
      $childd[1]['text']="添加角色";
      $child[0]['text']="角色管理";
      $child[0]['nodes']=$childd;

      $child[1]['text']="管理角色";
      $childd[0]['text']="普通角色";
      $childd[1]['text']="管理角色";
      $child[1]['nodes']=$childd;

      $node[9]['nodes']=$child;

      $node[10]['text']="CRM";
      $childd[0]['text']="添加用户";
      $childd[1]['text']="用户列表";
      $child[0]['text']="用户管理";
      $child[0]['nodes']=$childd;

      $child[1]['text']="权限管理";
      $childd[0]['text']="添加权限";
      $childd[1]['text']="权限列表";
      $child[1]['nodes']=$childd;

      $node[10]['nodes']=$child;

      $node[11]['text']="通用系统";
      $childd[0]['text']="应用设置";
      $childd[1]['text']="应用添加";
      $child[0]['text']="通用设置";
      $child[0]['nodes']=$childd;

      $child[1]['text']="网站配置";
      $childd[0]['text']="系统配置";
      $childd[1]['text']="基本配置";
      $child[1]['nodes']=$childd;

      $node[11]['nodes']=$child;












      //print_r(json_encode($node));
     // exit;
      $this->getView()->assign("node", json_encode($node));
    	$this->getView()->assign("title", $this->title);
	}
    /**
     * 名称:  添加用户
     */
	public function addAction() {
		if(isset($_POST) && !empty($_POST)){
            $data['username']=htmlspecialchars_decode(trim($_POST['username']));
            $data['userpasswd']=htmlspecialchars_decode(trim($_POST['userpasswd']));
            $data['truename']=htmlspecialchars_decode(trim($_POST['truename']));
            $data['birthday']=trim($_POST['birthday']);
            $data['sex']=$_POST['sex'];
            $data['mobile']=trim($_POST['mobile']);
            $data['address']=htmlspecialchars_decode(trim($_POST['address']));
            $data['qq']=trim($_POST['qq']);
            $data['status']=$_POST['status'];
            $result=PostData($this->UserAdd_Url,$data);
            if('0000' == $result['retCode']){header("Location: /user/index");}
        }
        $this->getView()->assign("title", $this->title);
	}
    /**
     * 名称:  编辑用户
     */
	public function editAction() {
		if(isset($_POST) && !empty($_POST)){
            $data['id']=$_POST['id'];
            $data['username']=htmlspecialchars_decode(trim($_POST['username']));
            $data['truename']=htmlspecialchars_decode(trim($_POST['truename']));
            $data['birthday']=trim($_POST['birthday']);
            $data['sex']=$_POST['sex'];
            $data['mobile']=trim($_POST['mobile']);
            $data['address']=htmlspecialchars_decode(trim($_POST['address']));
            $data['qq']=trim($_POST['qq']);
            $data['status']=$_POST['status'];
            $result=PostData($this->UserUp_Url,$data);
            if('0000' == $result['retCode']){header("Location: /user/index");}
        }
        $userinfo = GetData($this->UserInfo_Url.'?id='.$_GET['id']);
        $this->getView()->assign("title", $this->title);
        $this->getView()->assign("userinfo", $userinfo);
	}
    /**
     * 名称:  删除用户
     */
	public function delAction() {
        Yaf_Dispatcher::getInstance()->autoRender(FALSE);
        $userinfo = PostData($this->UserDel_Url,array('ids' =>$_GET['id']));
        echo json_encode(array('status' =>1,'msg'=>"ok"));
	}
   public function loginAction() {
      
      }
       public function menuAction() {
      
      }
       public function tableAction() {
      
      }
      public function topAction() {
      
      }
    /**
     * 名称:  获取用户列表
     */
    
	 public function getuserlistAction() {
      Yaf_Dispatcher::getInstance()->autoRender(FALSE);
      if(isset($_POST) && !empty($_POST)){
            //$url = $_POST['url'];
            $_POST['page']=$_POST['curPage'];
            $_POST['rows']=$_POST['pageSize'];
            if(isset($_POST['username']) && !empty($_POST['username'])){
                  $url='http://192.168.102.2:8080/rest/userList.do?page='.$_POST['page'].'&rows='.$_POST['rows'].'&username='.$_POST['username'];
            }else{
                  $url='http://192.168.102.2:8080/rest/userList.do?page='.$_POST['page'].'&rows='.$_POST['rows'];
            }
            
            
            $userlist = '{"data":{"total":6,"curPage":1,"rows":[{"id":82,"username":"tes211","userpasswd":"4297f44b13955235245b2497399d7a93","truename":"afd","birthday":"2016-03-11 00:00:00","sex":0,"mobile":"123123","address":"adsf","qq":"123","registertime":"2016-03-10 14:00:19","status":0,"newuserpasswd":null,"paginationInfo":{"page":1,"rows":20,"totalPage":null,"totalRecord":null,"offset":0,"limit":20}},{"id":83,"username":"xxx","userpasswd":"912ec803b2ce49e4a541068d495ab570","truename":"123","birthday":"2016-03-11 00:00:00","sex":0,"mobile":"123123","address":"adsf","qq":"adsf","registertime":"2016-03-10 14:03:34","status":0,"newuserpasswd":null,"paginationInfo":{"page":1,"rows":20,"totalPage":null,"totalRecord":null,"offset":0,"limit":20}},{"id":74,"username":"asdads","userpasswd":null,"truename":"asa","birthday":"2016-03-16 00:00:00","sex":0,"mobile":"123456546","address":"wwe","qq":"345321434","registertime":null,"status":0,"newuserpasswd":null,"paginationInfo":{"page":1,"rows":20,"totalPage":null,"totalRecord":null,"offset":0,"limit":20}},{"id":79,"username":"test444","userpasswd":null,"truename":"111111","birthday":"2016-08-04 00:00:00","sex":0,"mobile":"18614064093","address":"11111111111111111","qq":"904208360","registertime":null,"status":1,"newuserpasswd":null,"paginationInfo":{"page":1,"rows":20,"totalPage":null,"totalRecord":null,"offset":0,"limit":20}},{"id":81,"username":"test22","userpasswd":"96e79218965eb72c92a549dd5a330112","truename":"111111","birthday":"2016-03-03 00:00:00","sex":1,"mobile":"18614064093","address":"11111111111111111","qq":"904208360","registertime":"2016-03-03 11:11:00","status":0,"newuserpasswd":null,"paginationInfo":{"page":1,"rows":20,"totalPage":null,"totalRecord":null,"offset":0,"limit":20}},{"id":80,"username":"test999","userpasswd":"96e79218965eb72c92a549dd5a330112","truename":"111111","birthday":"2016-03-01 00:00:00","sex":1,"mobile":"18614064093","address":"11111111111111111","qq":"904208360","registertime":"2016-03-01 17:46:37","status":0,"newuserpasswd":null,"paginationInfo":{"page":1,"rows":20,"totalPage":null,"totalRecord":null,"offset":0,"limit":20}}]},"retCode":"0000","retMsg":"\u64cd\u4f5c\u6210\u529f"}';
            //echo json_encode($userlist);
            //$userlist=array('data' => array('total' => 6, 'curPage' => 1, 'rows' =>array([0] =>array('id' => 82,'username' => 'tes211','userpasswd' => '4297f44b13955235245b2497399d7a93','truename' => 'afd', 'birthday'=> '2016-03-11 00:00:00' ,'sex' => 0,[mobile] => 123123 [address] => adsf [qq] => 123 [registertime] => 2016-03-10 14:00:19 [status] => 0 [newuserpasswd] => [paginationInfo] => Array ( [page] => 1 [rows] => 20 [totalPage] => [totalRecord] => [offset] => 0 [limit] => 20 ) ) [1] => Array ( [id] => 83 [username] => xxx [userpasswd] => 912ec803b2ce49e4a541068d495ab570 [truename] => 123 [birthday] => 2016-03-11 00:00:00 [sex] => 0 [mobile] => 123123 [address] => adsf [qq] => adsf [registertime] => 2016-03-10 14:03:34 [status] => 0 [newuserpasswd] => [paginationInfo] => Array ( [page] => 1 [rows] => 20 [totalPage] => [totalRecord] => [offset] => 0 [limit] => 20 ) ) [2] => Array ( [id] => 74 [username] => asdads [userpasswd] => [truename] => asa [birthday] => 2016-03-16 00:00:00 [sex] => 0 [mobile] => 123456546 [address] => wwe [qq] => 345321434 [registertime] => [status] => 0 [newuserpasswd] => [paginationInfo] => Array ( [page] => 1 [rows] => 20 [totalPage] => [totalRecord] => [offset] => 0 [limit] => 20 ) ) [3] => Array ( [id] => 79 [username] => test444 [userpasswd] => [truename] => 111111 [birthday] => 2016-08-04 00:00:00 [sex] => 0 [mobile] => 18614064093 [address] => 11111111111111111 [qq] => 904208360 [registertime] => [status] => 1 [newuserpasswd] => [paginationInfo] => Array ( [page] => 1 [rows] => 20 [totalPage] => [totalRecord] => [offset] => 0 [limit] => 20 ) ) [4] => Array ( [id] => 81 [username] => test22 [userpasswd] => 96e79218965eb72c92a549dd5a330112 [truename] => 111111 [birthday] => 2016-03-03 00:00:00 [sex] => 1 [mobile] => 18614064093 [address] => 11111111111111111 [qq] => 904208360 [registertime] => 2016-03-03 11:11:00 [status] => 0 [newuserpasswd] => [paginationInfo] => Array ( [page] => 1 [rows] => 20 [totalPage] => [totalRecord] => [offset] => 0 [limit] => 20 ) ) [5] => Array ( [id] => 80 [username] => test999 [userpasswd] => 96e79218965eb72c92a549dd5a330112 [truename] => 111111 [birthday] => 2016-03-01 00:00:00 [sex] => 1 [mobile] => 18614064093 [address] => 11111111111111111 [qq] => 904208360 [registertime] => 2016-03-01 17:46:37 [status] => 0 [newuserpasswd] => [paginationInfo] => Array ( [page] => 1 [rows] => 20 [totalPage] => [totalRecord] => [offset] => 0 [limit] => 20 ) ) ) ) [retCode] => 0000 [retMsg] => 操作成功 )
            $userlist=json_decode($userlist,true);
            // /exit;
            //$userlist = GetData($url,$_POST);
            //$data[0]=array('id' => 82,'username' => 'tes1','userpasswd' =>'4297f44b13955235245b2497399d7a93','truename' => 'afd', 'birthday' => '2016-03-11 00:00:00', 'sex]' => 0, 'mobile' => 123123, 'address' => 'adsf', 'qq' => 123 ,'registertime' => '2016-03-10 14:00:19' ,'status' => 0, 'newuserpasswd' =>'');
            //$data[1]=array('id' => 88,'username' => 'tes2','userpasswd' =>'4297f44b13955235245b2497399d7a93','truename' => 'afd', 'birthday' => '2016-03-11 00:00:00', 'sex]' => 0, 'mobile' => 123123, 'address' => 'adsf', 'qq' => 123 ,'registertime' => '2016-03-10 14:00:19' ,'status' => 0, 'newuserpasswd' =>'');
            //$userlist['data']['rows'][0]['child']=$data;
           // $userlist['data']['success']=true;
            //$userlist['data']['totalRows']=$userlist['data']['total'];
           // $userlist['data']['curPage']=1;
           //// $userlist['data']['data']=$userlist['data']['rows'];
           // unset($userlist['data']['rows']);
            //unset($userlist['data']['total']);
           // print_r($userlist);
            //exit;
            if('0001'==$userlist['retCode']){echo $userlist['retMsg'];exit;}
            //$userlist['data']['totalRows']=count($userlist['data']['rows']);
            //$userdata['success']=true;
            //$userdata['totalRows']=$userlist['data']['total'];
            //$userdata['curPage']=1;
            //$userdata['data']=;
            //unset($userlist);
            //$userlist['data']['rows']='';
           // $userlist['retCode']='1003';
            echo json_encode($userlist);
        }else{
            echo '{}';
        }
        
    }

     public function getuserlist1Action() {
      Yaf_Dispatcher::getInstance()->autoRender(FALSE);
      if(isset($_POST) && !empty($_POST)){
           //$_POST['page']=$_POST['curPage'];
            //$_POST['rows']=$_POST['pageSize'];
            //if(isset($_POST['username']) && !empty($_POST['username'])){
                //  $url='http://192.168.102.2:8080/rest/userList.do?page='.$_POST['page'].'&rows='.$_POST['rows'].'&username='.$_POST['username'];
           // }else{
               //   $url='http://192.168.102.2:8080/rest/userList.do?page='.$_POST['page'].'&rows='.$_POST['rows'];
           // }
            //$userlist = GetData($url);
           // $userlist['data']['success']=true;
            //$userlist['data']['totalRows']=$userlist['data']['total'];
           // $userlist['data']['curPage']=1;
           //// $userlist['data']['data']=$userlist['data']['rows'];
           // unset($userlist['data']['rows']);
            //unset($userlist['data']['total']);
           // print_r($userlist);
            //exit;
             //$url='http://192.168.102.2:8080/rest/userList.do?page=1&rows='.$_POST['rows'];
            // /exit;
            //$userlist = GetData($url);
            $userlist = '{"data":{"total":6,"curPage":1,"rows":[{"id":82,"username":"tes211","userpasswd":"4297f44b13955235245b2497399d7a93","truename":"afd","birthday":"2016-03-11 00:00:00","sex":0,"mobile":"123123","address":"adsf","qq":"123","registertime":"2016-03-10 14:00:19","status":0,"newuserpasswd":null,"paginationInfo":{"page":1,"rows":20,"totalPage":null,"totalRecord":null,"offset":0,"limit":20}},{"id":83,"username":"xxx","userpasswd":"912ec803b2ce49e4a541068d495ab570","truename":"123","birthday":"2016-03-11 00:00:00","sex":0,"mobile":"123123","address":"adsf","qq":"adsf","registertime":"2016-03-10 14:03:34","status":0,"newuserpasswd":null,"paginationInfo":{"page":1,"rows":20,"totalPage":null,"totalRecord":null,"offset":0,"limit":20}},{"id":74,"username":"asdads","userpasswd":null,"truename":"asa","birthday":"2016-03-16 00:00:00","sex":0,"mobile":"123456546","address":"wwe","qq":"345321434","registertime":null,"status":0,"newuserpasswd":null,"paginationInfo":{"page":1,"rows":20,"totalPage":null,"totalRecord":null,"offset":0,"limit":20}},{"id":79,"username":"test444","userpasswd":null,"truename":"111111","birthday":"2016-08-04 00:00:00","sex":0,"mobile":"18614064093","address":"11111111111111111","qq":"904208360","registertime":null,"status":1,"newuserpasswd":null,"paginationInfo":{"page":1,"rows":20,"totalPage":null,"totalRecord":null,"offset":0,"limit":20}},{"id":81,"username":"test22","userpasswd":"96e79218965eb72c92a549dd5a330112","truename":"111111","birthday":"2016-03-03 00:00:00","sex":1,"mobile":"18614064093","address":"11111111111111111","qq":"904208360","registertime":"2016-03-03 11:11:00","status":0,"newuserpasswd":null,"paginationInfo":{"page":1,"rows":20,"totalPage":null,"totalRecord":null,"offset":0,"limit":20}},{"id":80,"username":"test999","userpasswd":"96e79218965eb72c92a549dd5a330112","truename":"111111","birthday":"2016-03-01 00:00:00","sex":1,"mobile":"18614064093","address":"11111111111111111","qq":"904208360","registertime":"2016-03-01 17:46:37","status":0,"newuserpasswd":null,"paginationInfo":{"page":1,"rows":20,"totalPage":null,"totalRecord":null,"offset":0,"limit":20}}]},"retCode":"0000","retMsg":"\u64cd\u4f5c\u6210\u529f"}';
            //echo json_encode($userlist);
            //$userlist=array('data' => array('total' => 6, 'curPage' => 1, 'rows' =>array([0] =>array('id' => 82,'username' => 'tes211','userpasswd' => '4297f44b13955235245b2497399d7a93','truename' => 'afd', 'birthday'=> '2016-03-11 00:00:00' ,'sex' => 0,[mobile] => 123123 [address] => adsf [qq] => 123 [registertime] => 2016-03-10 14:00:19 [status] => 0 [newuserpasswd] => [paginationInfo] => Array ( [page] => 1 [rows] => 20 [totalPage] => [totalRecord] => [offset] => 0 [limit] => 20 ) ) [1] => Array ( [id] => 83 [username] => xxx [userpasswd] => 912ec803b2ce49e4a541068d495ab570 [truename] => 123 [birthday] => 2016-03-11 00:00:00 [sex] => 0 [mobile] => 123123 [address] => adsf [qq] => adsf [registertime] => 2016-03-10 14:03:34 [status] => 0 [newuserpasswd] => [paginationInfo] => Array ( [page] => 1 [rows] => 20 [totalPage] => [totalRecord] => [offset] => 0 [limit] => 20 ) ) [2] => Array ( [id] => 74 [username] => asdads [userpasswd] => [truename] => asa [birthday] => 2016-03-16 00:00:00 [sex] => 0 [mobile] => 123456546 [address] => wwe [qq] => 345321434 [registertime] => [status] => 0 [newuserpasswd] => [paginationInfo] => Array ( [page] => 1 [rows] => 20 [totalPage] => [totalRecord] => [offset] => 0 [limit] => 20 ) ) [3] => Array ( [id] => 79 [username] => test444 [userpasswd] => [truename] => 111111 [birthday] => 2016-08-04 00:00:00 [sex] => 0 [mobile] => 18614064093 [address] => 11111111111111111 [qq] => 904208360 [registertime] => [status] => 1 [newuserpasswd] => [paginationInfo] => Array ( [page] => 1 [rows] => 20 [totalPage] => [totalRecord] => [offset] => 0 [limit] => 20 ) ) [4] => Array ( [id] => 81 [username] => test22 [userpasswd] => 96e79218965eb72c92a549dd5a330112 [truename] => 111111 [birthday] => 2016-03-03 00:00:00 [sex] => 1 [mobile] => 18614064093 [address] => 11111111111111111 [qq] => 904208360 [registertime] => 2016-03-03 11:11:00 [status] => 0 [newuserpasswd] => [paginationInfo] => Array ( [page] => 1 [rows] => 20 [totalPage] => [totalRecord] => [offset] => 0 [limit] => 20 ) ) [5] => Array ( [id] => 80 [username] => test999 [userpasswd] => 96e79218965eb72c92a549dd5a330112 [truename] => 111111 [birthday] => 2016-03-01 00:00:00 [sex] => 1 [mobile] => 18614064093 [address] => 11111111111111111 [qq] => 904208360 [registertime] => 2016-03-01 17:46:37 [status] => 0 [newuserpasswd] => [paginationInfo] => Array ( [page] => 1 [rows] => 20 [totalPage] => [totalRecord] => [offset] => 0 [limit] => 20 ) ) ) ) [retCode] => 0000 [retMsg] => 操作成功 )
            $userlist=json_decode($userlist,true);
            //print_r($userlist);
            //exit;
            $datasss[0]=array('id' => 28,'username' => 'tes7444444444444444','userpasswd' =>'4297f44b13955235245b2497399d7a93','truename' => 'afd', 'birthday' => '2016-03-11 00:00:00', 'sex]' => 0, 'mobile' => 123123, 'address' => 'adsf', 'qq' => 123 ,'registertime' => '2016-03-10 14:00:19' ,'status' => 0, 'newuserpasswd' =>'');
            $datass[0]=array('id' => 80,'username' => 'tes544444444444444444','userpasswd' =>'4297f44b13955235245b2497399d7a93','truename' => 'afd', 'birthday' => '2016-03-11 00:00:00', 'sex]' => 0, 'mobile' => 123123, 'address' => 'adsf', 'qq' => 123 ,'registertime' => '2016-03-10 14:00:19' ,'status' => 0, 'newuserpasswd' =>'');
            $datass[1]=array('id' => 18,'username' => 'tes6444444444444444444','userpasswd' =>'4297f44b13955235245b2497399d7a93','truename' => 'afd', 'birthday' => '2016-03-11 00:00:00', 'sex]' => 0, 'mobile' => 123123, 'address' => 'adsf', 'qq' => 123 ,'registertime' => '2016-03-10 14:00:19' ,'status' => 0, 'newuserpasswd' =>'','child'=>$datasss);
            $data[0]=array('id' => 80,'username' => 'tes14444444444444','userpasswd' =>'4297f44b13955235245b2497399d7a93','truename' => 'afd', 'birthday' => '2016-03-11 00:00:00', 'sex]' => 0, 'mobile' => 123123, 'address' => 'adsf', 'qq' => 123 ,'registertime' => '2016-03-10 14:00:19' ,'status' => 0, 'newuserpasswd' =>'');
            $datas[0]=array('id' => 80,'username' => 'tes34444444444444','userpasswd' =>'4297f44b13955235245b2497399d7a93','truename' => 'afd', 'birthday' => '2016-03-11 00:00:00', 'sex]' => 0, 'mobile' => 123123, 'address' => 'adsf', 'qq' => 123 ,'registertime' => '2016-03-10 14:00:19' ,'status' => 0, 'newuserpasswd' =>'');
            $datas[1]=array('id' => 88,'username' => 'tes4444444444444444','userpasswd' =>'4297f44b13955235245b2497399d7a93','truename' => 'afd', 'birthday' => '2016-03-11 00:00:00', 'sex]' => 0, 'mobile' => 123123, 'address' => 'adsf', 'qq' => 123 ,'registertime' => '2016-03-10 14:00:19' ,'status' => 0, 'newuserpasswd' =>'','child'=>$datass);
            $data[1]=array('id' => 108,'username' => 'tes24444444444444','userpasswd' =>'4297f44b13955235245b2497399d7a93','truename' => 'afd', 'birthday' => '2016-03-11 00:00:00', 'sex]' => 0, 'mobile' => 123123, 'address' => 'adsf', 'qq' => 123 ,'registertime' => '2016-03-10 14:00:19' ,'status' => 0, 'newuserpasswd' =>'','child'=>$datas);
            $userlist['data']['rows'][0]['child']=$data;
            //$data[0]=array('id' => 90,'username' => 'tes3','userpasswd' =>'4297f44b13955235245b2497399d7a93','truename' => 'afd', 'birthday' => '2016-03-11 00:00:00', 'sex]' => 0, 'mobile' => 123123, 'address' => 'adsf', 'qq' => 123 ,'registertime' => '2016-03-10 14:00:19' ,'status' => 0, 'newuserpasswd' =>'');
           // $data[1]=array('id' => 98,'username' => 'tes4','userpasswd' =>'4297f44b13955235245b2497399d7a93','truename' => 'afd', 'birthday' => '2016-03-11 00:00:00', 'sex]' => 0, 'mobile' => 123123, 'address' => 'adsf', 'qq' => 123 ,'registertime' => '2016-03-10 14:00:19' ,'status' => 0, 'newuserpasswd' =>'');
            //$userlist['data']['rows'][2]['child']=$data;
            if('0001'==$userlist['retCode']){echo $userlist['retMsg'];exit;}
            //$userlist['data']['totalRows']=count($userlist['data']['rows']);
            //$userdata['success']=true;
            //$userdata['totalRows']=$userlist['data']['total'];
            //$userdata['curPage']=1;
            //$userdata['data']=;
            //unset($userlist);
            //$userlist['retCode']='1003';
            echo json_encode($userlist);
        }else{
            echo '{}';
        }
        
    }
    public function ajaxuploadAction() {
      Yaf_Dispatcher::getInstance()->autoRender(FALSE);
            
           echo json_encode($_POST);
           //print_r($_FILES);
           
        
    }
}
