function editSysArea(){
	var id=getId();
	var level=getLevel();
	var areaName=getName();
	var parentId=getParentId();
	var areaCode=getCode();
	var areaDesc=getDesc();
	var adminAreaId=getAdminAreaId();
	if(id==undefined){
		easyDialog.open({
			container:{
				content:"请选择要操作的记录！"
			},
			autoClose : 2000
		});
		return false;
	}
	$('#systemOutTree').remove();
	$.ajax({
		url:server_url+'sysAreaManager/initSysEditPage.do?ran='+Math.random(),
		type:'get',
		data: {id:id,level:level,parentId:parentId,adminAreaId:adminAreaId},
		success:function(data){
			var bb=transformTozTreeFormat(JSON.stringify(data.data.tree));
			var html='<div class="z_rightMidden">'
		    	+'<div class="z_title">首页>系统区域管理>编辑区域</div>'
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
						if(level==1||level==2||level==3){
							//只选中中国下
							html+='<span>中国</span>';
						}
						if(level==2||level==3){
							//选择省下
							html+='<em>></em><span>'+data.data.shengName+'</span>';
						}
						if(level==3){
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
		    			+'<li class="clearfix">'
		    			+'<span>关联行政区域</span>'
		    			+'</li>'
		    		+'</ul>'
		    		+'<input type="hidden" id="parentId" value="'+data.data.parentId+'"/>'
		    		+'<input type="hidden" id="id" value="'+data.data.id+'"/>'
		    		+'<input type="hidden" id="level" value="'+data.data.level+'"/>'
		    		+'</div>'
			    	+'<div class="z_zone"style="margin-left: 300px; margin-top: 300px;position: relative">'   	
			    	+'</div>'
		    		+'<a href="javascript:editSysSubmit();" class="z_sure" style="margin-top: 70px;position: absolute">确定</a>'
		    		+'<a href="javascript:returnSysList();" class="z_goback" style="margin-top: 70px;margin-left:485px;position: absolute">返回</a>'
		   +'</div>';
			$('#systemTreeEdit').html(html);
			//清空数据源*
			$.fn.tree.html_str="";
			$.fn.tree.init({
				nodes:(JSON.stringify(bb))
			});
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
};
function getDesc(){
	return $.fn.tree.obj_tu.areaDesc;
};
function getCode(){
	return $.fn.tree.obj_tu.areaCode;
};
function getAdminAreaId(){
	return $.fn.tree.obj_tu.adminAreaId;
}

function editSysSubmit(){
	 var id=$("#id").val();
	 var adminAreaId=getSelectedDate();
	 var areaName=$("#areaName").val();
	 var areaCode=$("#areaCode").val();
	 var areaDesc=$("#areaDesc").val();
	 var parentId=$("#parentId").val();
	 var username=localStorage.getItem("username");
	 var level=$("#level").val();
	 console.log("id="+id,"areaName"+areaName,"areaCode"+areaCode,"areaDesc"+areaDesc,"parentId"+parentId,"level"+level,"adminAreaId"+adminAreaId);
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
		url:server_url+'sysAreaManager/editSystemTree.do?ran='+Math.random(),
		type:'post',
		data: {id:id,parentId:parentId,adminAreaId:adminAreaId,level:level,areaCode:areaCode,areaDesc:areaDesc,areaName:areaName,username:username},
		success:function(data){
//			window.location="http://localhost:8080/TianlianSaasMgrWeb/showSysAreaPage.do";
			back2();
		}
	});
}
function returnSysList(){
//	window.location="http://localhost:8080/TianlianSaasMgrWeb/showSysAreaPage.do";
	back2();
}
function back2(){
	$(".g_rightMidden").load("showSysAreaPage.do?ran="+Math.random());
}
function getSelectedDate(){
	$.fn.tree.getSelect();
	//console.log('id:'+sd);
	return $.fn.tree.tmpid;
}
