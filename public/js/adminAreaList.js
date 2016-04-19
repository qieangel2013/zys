$(function(){
		$.ajax({
			url:server_url+'areaManager/initAdminAreaTree.do?ran='+Math.random(),
			type:'get',
			success:function(data){
				var aa=transformTozTreeFormat(JSON.stringify(data.data));
				console.log(aa);
				console.log(JSON.stringify(aa));
				var html='<div class="z_rightMidden">'
		    	+'<div class="z_title">首页> 行政区域管理</div>'
		    	+'<div class="z_manamged z_myjob">'
		    	+'<input type="button" value="添加区域" id="addAdminArea" class="z_addpost"><input type="button" value="编辑" id="editAdminArea" class="z_delpost">'
			    +'</div>'
			    +'<div class="z_zone" style="margin-left:100px;position: absolute;">'   	
		    +'</div>';
				$('#administrtionOutTree').html(html);
				$("#addAdminArea").click(function(){addAdminArea();});
				$("#editAdminArea").click(function(){editAdminArea();});
				$.fn.tree.init({
					nodes:(JSON.stringify(aa))
				});
			}
		});
});



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