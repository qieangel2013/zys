function addSysArea(){
	var id=getId();
	var level=getLevel();
	var parentId=getParentId();
	if(id==undefined){
		easyDialog.open({
			container:{
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
	$('#systemOutTree').remove();
	$.ajax({
		url:server_url+'sysAreaManager/initSysAddPage.do?ran='+Math.random(),
		type:'get',
		data: {id:id,level:level,parentId:parentId},
		success:function(data){
			var bb=transformTozTreeFormat(JSON.stringify(data.data.tree));
			var html='<div class="z_rightMidden">'
		    	+'<div class="z_title">首页>系统区域管理>添加区域</div>'
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
		    	+'<a href="javascript:addSysSubmit();" class="z_sure"  style="margin-top: 70px;position: absolute">确定</a>'
		    	+'<a href="javascript:returnSysList2();" class="z_goback" style="margin-top: 70px;margin-left:485px;position: absolute">返回</a>'
		   +'</div>';
			$('#systemTreeAdd').html(html);
			$('.z_country .z_address1').remove();
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



function addSysSubmit(){
	 var id=$("#id").val();
	 var adminAreaId=getId();
	 var areaName=$("#areaName").val();
	 var areaCode=$("#areaCode").val();
	 var areaDesc=$("#areaDesc").val();
	 var parentId=$("#parentId").val();
	 var level=$("#level").val();
	 var username=localStorage.getItem("username");
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
		url:server_url+'sysAreaManager/addSystemTree.do?ran='+Math.random(),
		type:'post',
		data: {id:id,parentId:parentId,adminAreaId:adminAreaId,level:level,areaCode:areaCode,areaDesc:areaDesc,areaName:areaName,username:username},
		success:function(data){
//			window.location="http://localhost:8080/TianlianSaasMgrWeb/showSysAreaPage.do";
			back2();
		}
	});
}
function returnSysList2(){
//	window.location="http://localhost:8080/TianlianSaasMgrWeb/showSysAreaPage.do";
	back2();
}
function back2(){
	$(".g_rightMidden").load("showSysAreaPage.do?ran="+Math.random());
}
function transformTozTreeFormat(sNodes) {
	sNodes=$.parseJSON(sNodes);
	var i,l,
	key = "areaId",
	parentKey = "parentId",
	childKey = "nodes";
	if (!key || key=="" || !sNodes) return [];
	var r = [];
	var tmpMap = [];
	for (i=0, l=sNodes.length; i<l; i++) {
		tmpMap[sNodes[i][key]] = sNodes[i];
	}
	for (i=0, l=sNodes.length; i<l; i++) {
		if (tmpMap[sNodes[i][parentKey]] && sNodes[i][key] != sNodes[i][parentKey]) {
			if (!tmpMap[sNodes[i][parentKey]][childKey])
				tmpMap[sNodes[i][parentKey]][childKey] = [];
			tmpMap[sNodes[i][parentKey]][childKey].push(sNodes[i]);
		} else {
			r.push(sNodes[i]);
		}
	}
	return r;
}
