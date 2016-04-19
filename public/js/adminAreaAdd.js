function addAdminArea(){
	var id=getId();
	var level=getLevel();
	if(id==undefined){
		easyDialog.open({
			container:{
				header:"错误提示！",
				content:"请选择要操作的记录！"
			},
			autoClose : 2000
		});
		return false;
	}
	if(level==4){
		easyDialog.open({
			container:{
				content:"该级区域不能添加！"
			},
			autoClose : 2000
		});
		return false;
	}
	$('#administrtionOutTree').remove();
	$.ajax({
		url:server_url+'areaManager/addAdminTreeStep1.do?ran='+Math.random(),
		type:'get',
		data: {id:id,level:level},
		success:function(data){
			var html='<div class="z_rightMidden">'
		    	+'<div class="z_title">首页>行政区域管理>添加区域</div>'
		    	+'<div class="l_system">'
		    		+'<ul>'
		    			+'<li class="clearfix">'
		    				+'<span>区域名称</span>'
		    				+'<input type="text" id="areaName" />'
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
		    				+'<input type="text" id="areaCode"/>'
		    			+'</li>'
		    			+'<li class="clearfix">'
		    				+'<span>备注</span>'
		    				+'<textarea name="" id="areaDesc" cols="30" rows="10" ></textarea>'
		    			+'</li>'
		    		+'</ul>'
		    		+'<a href="javascript:addSubmit();" class="z_sure">确定</a>'
		    		+'<a href="javascript:returnList();" class="z_goback">返回</a>'
		    	+'</div>'
		   +'</div>';
			$('#administrtionTreeAdd').html(html);
		
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

function addSubmit(){
	 var id=getId();
	 var areaName=$("#areaName").val();
	 var areaCode=$("#areaCode").val();
	 var areaDesc=$("#areaDesc").val();
	 var parentId=getParentId();
	 var username=localStorage.getItem("username");
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
		url:server_url+'areaManager/addAdminTreeStep2.do?ran='+Math.random(),
		type:'post',
		data: {id:id,parentId:parentId,level:level,areaCode:areaCode,areaDesc:areaDesc,areaName:areaName,username:username},
		success:function(data){
			back();
		}
	});
}
function returnList(){
	back();
}
function back(){
	$(".g_rightMidden").load("showAdminAreaPage.do?ran="+Math.random());
}
