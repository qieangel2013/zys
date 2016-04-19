function editAdminArea(){
	var id=getId();
	var level=getLevel();
	var areaName=getName();
	var areaCode=getCode();
	var areaDesc=getDesc();
	if(id==undefined){
		easyDialog.open({
			container:{
				content:"请选择要编辑的区域！"
			},
			autoClose : 2000
		});
		return false;
	}
	$('#administrtionOutTree').remove();
	$.ajax({
		url:server_url+'areaManager/editAdminTreeStep1.do?ran='+Math.random(),
		type:'get',
		data: {id:id,level:level},
		success:function(data){
			var html='<div class="z_rightMidden">'
		    	+'<div class="z_title">首页>行政区域管理>编辑区域</div>'
		    	+'<div class="l_system">'
		    		+'<ul>'
		    			+'<li class="clearfix">'
		    				+'<span>区域名称</span>'
		    				+'<input type="text" id="areaName" value="'+areaName+'"/>'
		    				+'<em>*</em>'+'<em id="tishi"></em>'
		    			+'</li>'
		    			+'<li class="clearfix">'
		    				+'<span>地区</span>'
		    				+'<div class="l_adress">';
		    				if(level==1||level==2||level==3||level==4){
		    					//只选中中国下
		    					html+='<span>中国</span>';
		    				}
		    				if(level==2||level==3||level==4){
		    					//选择省下
		    					html+='<em>></em><span>'+data.data.shengName+'</span>';
		    				}
		    				if(level==3||level==4){
		    					//选择市下
		    					html+='<em>></em><span>'+data.data.shiName+'</span>';
		    				}
		    					html+='</div>'
		    			+'</li>'
		    			+'<li class="clearfix">'
		    				+'<span>区域代码</span>'
		    				+'<input type="text" value="'+areaCode+'" id="areaCode"/>'
		    			+'</li>'
		    			+'<li class="clearfix">'
		    				+'<span>备注</span>'
		    				+'<textarea name="" id="areaDesc" cols="30" rows="10" >'+areaDesc+'</textarea>'
		    			+'</li>'
		    		+'</ul>'
		    		+'<a href="javascript:editSubmit();" class="z_sure">确定</a>'
		    		+'<a href="javascript:returnList2();" class="z_goback">返回</a>'
		    	+'</div>'
		   +'</div>';
			$('#administrtionTreeEdit').html(html);
		}
	});
}
function getId(){
	return $.fn.tree.obj_tu.areaId;
};
function getParentId(){
	return $.fn.tree.obj_tu.parentId;
};
function getLevel(){
	return $.fn.tree.obj_tu.level;
};
function getName(){
	return $.fn.tree.obj_tu.areaName;
}
function getDesc(){
	return $.fn.tree.obj_tu.areaDesc;
}
function getCode(){
	return $.fn.tree.obj_tu.areaCode;
}



function editSubmit(){
	 var id=getId();
	 var areaName=$("#areaName").val();
	 var areaCode=$("#areaCode").val();
	 var areaDesc=$("#areaDesc").val();
	 var username=localStorage.getItem("username");
	 var parentId=getParentId();
	 var level=getLevel();
	 var reg = /^[0-9]{6}$/;
	if(areaName==""){
		$("#tishi").append("<em>该值不能为空</em>");
		return false;
	}
	if(reg.test(areaCode) != true){
		easyDialog.open({
			container:{
				content:"区域代码为6位数字！"
			},
			autoClose : 2000
		});
		return false;
	}
	$.ajax({
		url:server_url+'areaManager/editAdminTreeStep2.do?ran='+Math.random(),
		type:'post',
		data: {id:id,parentId:parentId,level:level,areaCode:areaCode,areaDesc:areaDesc,areaName:areaName,username:username},
		success:function(data){
			back();
		}
	});
}
function returnList2(){
	back();
}
