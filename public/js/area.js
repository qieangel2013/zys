/*select框id分别为provinceId,cityId,countyId即可*/
$(document).ready(function(){
	queryProvince();
	$("#provinceId").change(queryCity);
	$("#cityId").change(queryCounty);
});
function queryProvince(){
	$("#provinceId").empty();
	var html="<option value='' selected='selected'>"+"省/直辖市"+"</option>";
	$(html).appendTo("#provinceId");
	$.ajax({
		type:"get",
		url:server_url+"areaManager/getProvince.do",
		async:false,
		success:function(data){
			for(var i=0;i<data.data.length;++i){
				var html="<option value="+data.data[i].areaId+">"+data.data[i].areaName+"</option>";
				$(html).appendTo("#provinceId");
			}
		}
	});
	queryCity();
}

function queryCity(){
	$.ajax({
		type:"get",
		url:server_url+"areaManager/getByParent.do",
		data:{
			parentId:$("#provinceId").val()
		},
		async:false,
		success:function(data){
			$("#cityId").empty();
			if(data.data!=null){
				var html="<option value='' selected='selected'>"+"市/区"+"</option>";
				$(html).appendTo("#cityId");
				for(var i=0;i<data.data.length;++i){
					var html="<option value="+data.data[i].areaId+">"+data.data[i].areaName+"</option>";
					$(html).appendTo("#cityId");
				};
			}else{
				var html="<option value='' selected='selected'>"+"市/区"+"</option>";
				$(html).appendTo("#cityId");
			}
		}
	});
	queryCounty();
}

function queryCounty(){
	$.ajax({
		type:"get",
		url:server_url+"areaManager/getByParent.do",
		data:{
			parentId:$("#cityId").val()
		},
		async:false,
		success:function(data){
			$("#countyId").empty();
			if(data.data!=null){
				var html="<option value='' selected='selected'>"+"县"+"</option>";
				$(html).appendTo("#countyId");
				for(var i=0;i<data.data.length;++i){
					var html="<option value="+data.data[i].areaId+">"+data.data[i].areaName+"</option>";
					$(html).appendTo("#countyId");
				}
			}else{
				var html="<option value='' selected='selected'>"+"县"+"</option>";
				$(html).appendTo("#countyId");
			}
		}
	});
}