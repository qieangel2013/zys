(function ($) {
    $.fn.tree = {
    	html_str:'',
    	select_obj:{
    		co:{
    			areaName:'中国',
    			parentId:0,
    			areaId:1    			
    		},
    		pr:{
    			areaName:'',
    			parentId:0,
    			areaId:0  
    		},
    		ci:{
    			areaName:'',
    			parentId:0,
    			areaId:0  
    		},
    		di:{
    			areaName:'',
    			parentId:0,
    			areaId:0  
    		}
    	},
    	init:function(setting){
    		if(typeof(setting.nodes)=="string"){
    			setting.nodes=$.parseJSON(setting.nodes);
    		}
    		$.each(setting.nodes,function(i,v){
						if(v.parentId==0){
							$.fn.tree.html_str +='<div class="z_country z_address1"><ul><li><span></span><input type="checkbox" '+(v.isChecked=='true'?"checked":"")+' name="tl_sel" level="0" value='+JSON.stringify(v)+' onclick=\'javascript:$.fn.tree.itemclick('+JSON.stringify(v)+',0);\'/><p>'+v.areaName+'</p></li></ul></div>';
							if(v.nodes!='' && v.nodes!=undefined){
								$.fn.tree.html_str +='<div class="z_provice z_gone">';
								$.each(v.nodes,function(ii,vv){
									$.fn.tree.html_str +='<div class="z_country z_address2 "><ul class="z_dizhi2"><li><span></span><input type="checkbox" '+(vv.isChecked=='true'?'checked':'')+' name="tl_sel" level="1" value='+JSON.stringify(vv)+' onclick=\'javascript:$.fn.tree.itemclick('+JSON.stringify(vv)+',1);\'/><p>'+vv.areaName+'</p></li></ul>';
									if(vv.nodes!='' && vv.nodes!=undefined){
                                        $.fn.tree.html_str +='<div class="z_address3 z_gone">';
										$.each(vv.nodes,function(iii,vvv){
											$.fn.tree.html_str +='<div class="z_country"><ul><li><span></span><input type="checkbox" '+(vvv.isChecked=='true'?'checked':'')+' name="tl_sel" level="2" value='+JSON.stringify(vvv)+' onclick=\'javascript:$.fn.tree.itemclick('+JSON.stringify(vvv)+',2);\'/><p>'+vvv.areaName+'</p></li></ul>';
													if(vvv.nodes!='' && vvv.nodes!=undefined){
														$.fn.tree.html_str +='<div class="z_country z_address4 ">';
														$.each(vvv.nodes,function(ii,vvvv){
															$.fn.tree.html_str +='<div class="z_country"><ul><li><span></span><input type="checkbox" '+(vvvv.isChecked=='true'?'checked':'')+' name="tl_sel" level="3" value='+JSON.stringify(vvvv)+' onclick=\'javascript:$.fn.tree.itemclick('+JSON.stringify(vvvv)+',3);\'/><p>'+vvvv.areaName+'</p></li></ul></div>';	
														});
														$.fn.tree.html_str +='</div>';
													}
													$.fn.tree.html_str +='</div>';
											});
                                            $.fn.tree.html_str +='</div>';
										}
										$.fn.tree.html_str +='</div>';
							});
								$.fn.tree.html_str +='</div>';
							}
						}
					});
				$('.z_zone').html($.fn.tree.html_str);
				$.fn.tree.run();
    	},
    	run:function(){
    	$(".g_houLeft").hover(function(){
			$(".g_niazhanK").removeClass("g_none");
			$(this).css({"background":"#fff","border":"1px solid #ddd","width":"178px","height":"70px"});
			$(this).children("span").css({"color":"#222","border":"none","background":"url('images/g_huiXia.png') 154px 11px no-repeat"});
		},function(){
			$(".g_niazhanK").addClass("g_none");
			$(this).css({"background":"#1777d8","border":"none","width":"180px","height":"72px"});
			$(this).children("span").css({"color":"#fff","border-right":"1px solid #4592e0","background":"url('images/g_upDown.png') 154px 11px no-repeat"});
		})
		$(".g_huMing").hover(function(){
			$(".g_yuiChu").removeClass("g_none");
			$(this).css({"background":"#fff"});
			$(this).children("span").css({"color":"#222","background":"url('images/g_huiXia.png') 50px 11px no-repeat"});
		},function(){
			$(".g_yuiChu").addClass("g_none");
			$(this).css({"background":"#1777d8"});
			$(this).children("span").css({"color":"#fff","background":"url('images/g_upDown.png') 50px 11px no-repeat"});
		})
		$(".g_bifUlbao li").hover(function(){
			$(this).addClass("g_baiSd");
		},function(){
			$(this).removeClass("g_baiSd");
		})
		$(".g_smallUlbao li").hover(function(){
			$(this).css("background","#fff");
		},function(){
			$(this).css("background","#fff");
		})
		$(".g_nCaidan").hover(function(){
			$(".g_biaoGecon").removeClass("g_none");
			$(this).children("span").css({"color":"#333","background":"url('images/g_huiGong.png') 0px 2px no-repeat"});
		},function(){
			$(".g_biaoGecon").addClass("g_none");
			$(this).children("span").css("color","#fff");
			$(this).children("span").css({"color":"#fff","background":"url('images/g_Sheiji.png') 0px 2px no-repeat"});
		})
		$(".g_smallUlbao li a").hover(function(){
			$(this).css("background","#fff");
			$(this).addClass("g_Ahover");
		},function(){
			$(this).css("background","none");
			$(this).removeClass("g_Ahover");
		})
		$(".g_smallUlbao li a").click(function(){
			$(this).addClass("g_danQia").parent().siblings().children("a").removeClass("g_danQia");
		})
			$(".g_dowmXia li").hover(function(){
			$(this).css("background","#f5f5f5");
		},function(){
			$(this).css("background","none");
		})
		
        /*左侧点击效果*/
        $(".g_indent p").css("top","1px"); 
        $(".g_indent p").click(function(event) {
            var h = $(this).get(0).style.top;
            if(h =="1px"){
                $(this).removeClass('g_below').addClass('g_leftUp');
                //$(this).removeClass('g_leftUp').addClass('g_below');
                //$(this).parent().siblings().find('.g_dowmXia').slideUp(800).siblings('p').removeClass('g_below').addClass('g_leftUp');
                $(this).parent().siblings().find('.g_dowmXia').slideUp(800).siblings('p').removeClass('g_leftUp').addClass('g_below');
                $(this).css("top","2px");
                if($(this).siblings('.g_dowmXia').find('li').length>0){
                    $(this).siblings('.g_dowmXia').slideDown(800);
                }
                $(this).css("top","2px").parent().siblings().children('p').css("top","1px");  
            }
            else{
                $(this).removeClass('g_leftUp').addClass('g_below');
                //$(this).removeClass('g_below').addClass('g_leftUp');
                $(this).siblings('.g_dowmXia').slideUp(800);
                $(this).css("top","1px");         
            }
        });
			/*点击国家一级*/
		
		$(".z_address1 span").click(function(){
			var a=$(this).css("top");
			if(a=="1px")
			{
				$(this).addClass('z_minus');
				$(".z_provice").removeClass('z_gone');
				$(this).css("top","2px");
			}
			else
			{
				$(this).removeClass('z_minus');
				$(".z_provice,.z_address3,z_address4").addClass('z_gone');
				$(".z_dizhi2 span").removeClass('z_minus').css("top","3px");
				$(this).css("top","1px");
				$(".z_address3 span").removeClass('z_minus').css("top","5px");
			}	
		})
		/*点击省份二级*/
		$(".z_dizhi2 span").click(function(){
			var b=$(this).css("top");
			if(b=="3px")
			{
				$(this).addClass('z_minus');
				$(this).parent().parent().siblings(".z_address3").removeClass('z_gone').css("top","5px");
				$(this).css("top","4px");
				$(".z_address4").addClass('z_gone');
			}
			else
			{
				$(this).removeClass('z_minus');
				$(this).parent().parent().siblings(".z_address3").addClass('z_gone');
				$(this).parent().parent().siblings(".z_address3").find("span").removeClass('z_minus').css("top","5px");
				$(this).parent().parent().siblings(".z_address3").find('.z_address4').addClass('z_gone');
				$(this).css("top","3px");
			}	
		})
		/*点击省份三级*/
		$(".z_address3 span").click(function(){
			var c=$(this).css("top");
			if(c=="5px")
			{
				$(this).addClass('z_minus');
				$(this).parent().parent().siblings(".z_address4").removeClass('z_gone');
				$(this).css("top","6px");
			}
			else
			{
				$(this).removeClass('z_minus');
				$(this).parent().parent().siblings(".z_address4").addClass('z_gone');
				$(this).css("top","5px");
			}	
		})

		/*点击国家复选框*/
      $(".z_address1 input").click(function(event) {
         /*if(this.checked==true) 
         {
            for(var i=0;i<$(".z_provice input").length;i++)
            {
               $(".z_provice input")[i].checked=true;
            }

         }
         else
         {
            for(var i=0;i<$(".z_provice input").length;i++)
            {
               $(".z_provice input")[i].checked=false;
            }

         }*/
       });
      /*点击省份复选框*/
      $(".z_dizhi2 input").click(function(event) {
        /* if(this.checked==true) 
         {
            for(var j=0;j<$(this).parent().parent().siblings(".z_address3").find("input").length;j++)
            {
               $(this).parent().parent().siblings(".z_address3").find("input")[j].checked=true;
            }

         }
         else
         {
            for(var j=0;j<$(this).parent().parent().siblings(".z_address3").find("input").length;j++)
            {
               $(this).parent().parent().siblings(".z_address3").find("input")[j].checked=false;
            }

         }*/
       });
      /*点击市复选框*/
      $(".z_address3 .z_country input").click(function(event) {
         /*if(this.checked==true) 
         {
            for(var j=0;j<$(this).parent().parent().siblings(".z_address4").find("input").length;j++)
            {
               $(this).parent().parent().siblings(".z_address4").find("input")[j].checked=true;
            }

         }
         else
         {
            for(var j=0;j<$(this).parent().parent().siblings(".z_address4").find("input").length;j++)
            {
               $(this).parent().parent().siblings(".z_address4").find("input")[j].checked=false;
            }

         }*/
       });
    }, 
    itemclick:function(data,level){
    	switch(level){
    		case 0:
    			if(data.isChecked=='true'){
    				$.fn.tree.select_obj.co.areaName=data.areaName;
    				$.fn.tree.select_obj.co.parentId=data.parentId;
    				$.fn.tree.select_obj.co.areaId=data.areaId;
    			}
    			break;
    		case 1:
    			if(data.isChecked=='true'){
    				$.fn.tree.select_obj.pr.areaName=data.areaName;
    				$.fn.tree.select_obj.pr.parentId=data.parentId;
    				$.fn.tree.select_obj.pr.areaId=data.areaId;
    			}
    			break;
    		case 2:
    			if(data.isChecked=='true'){
    				$.fn.tree.select_obj.ci.areaName=data.areaName;
    				$.fn.tree.select_obj.ci.parentId=data.parentId;
    				$.fn.tree.select_obj.ci.areaId=data.areaId;
    			}
    			
    			break;
    		case 3:
    			if(data.isChecked=='true'){
    				$.fn.tree.select_obj.di.areaName=data.areaName;
    				$.fn.tree.select_obj.di.parentId=data.parentId;
    				$.fn.tree.select_obj.di.areaId=data.areaId;
    			}
    			
    			break;
    	}
    },
    getSelect:function(){
    	 $('input:checkbox[name=tl_sel]:checked').each(function(i,v){
    	 	var level=$(v).attr('level');
    	 	var data=$.parseJSON($(v).attr('value'));
    	 	switch(level){
    		case '0':
    				$.fn.tree.select_obj.co.areaName=data.areaName;
    				$.fn.tree.select_obj.co.parentId=data.parentId;
    				$.fn.tree.select_obj.co.areaId=data.areaId;
    			break;
    		case '1':
    				$.fn.tree.select_obj.pr.areaName=data.areaName;
    				$.fn.tree.select_obj.pr.parentId=data.parentId;
    				$.fn.tree.select_obj.pr.areaId=data.areaId;
    			break;
    		case '2':
    				$.fn.tree.select_obj.ci.areaName=data.areaName;
    				$.fn.tree.select_obj.ci.parentId=data.parentId;
    				$.fn.tree.select_obj.ci.areaId=data.areaId;
    			
    			break;
    		case '3':
    				$.fn.tree.select_obj.di.areaName=data.areaName;
    				$.fn.tree.select_obj.di.parentId=data.parentId;
    				$.fn.tree.select_obj.di.areaId=data.areaId;
    			
    			break;
    	}
    	 });
    	 return $.fn.tree.select_obj;
    }
    };
})(jQuery)