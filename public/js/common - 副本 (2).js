//var server_url='http://localhost:8080/TianlianSaasMgrWebSer/';
var server_url='http://192.168.102.207:8086/TianlianSaasMgrWebSer/';
var token;
var tl_config={
      init_url:'',
      his_url:'',
      last_url:'',
      init_setting:{},
      menu_num:6,
      menu_max:1,
      init: function (settings) {
    	  $.ajaxSetup({cache:true});
          var node_dropdown=0;
          if(typeof(settings.menu_node)!='object'){
              var tl_obj = $.parseJSON(settings.menu_node);
          }else{
              var tl_obj = settings.menu_node;
          }
          if(settings.menu_num==undefined || settings.menu_num=='')
          {
        	  settings.menu_num=tl_config.menu_num;
          }
          if(settings.int_menu_url!=undefined && settings.int_menu_url!=''){
              tl_config.init_url=settings.int_menu_url;
          }
          if(tl_obj=='' || tl_obj==null){
//              alert('菜单数据：'+settings.menu_node+'不能为空!');
//              return false;
          }else{
          var m_num=1;
          tl_config.init_setting=settings;
          $.each(tl_obj.data,function(i,v){
            if(i>settings.menu_num && 0==node_dropdown){
              $('.g_bifUlbao').append('<li class="g_nCaidan"><span>菜单列表</span><div class="g_biaoGecon g_none"><ul class="g_smallUlbao"></ul><div></li>');
              $('.g_smallUlbao').append("<li><a href='#' onclick='javascript:tl_config.getTree("+JSON.stringify(v.child)+");'>"+v.menuName+"</a></li>");
              m_num++;
              node_dropdown=1;
            }else{
              if(0==node_dropdown){
                $('.g_bifUlbao').append("<li><a href='#' onclick='javascript:tl_config.getTree("+JSON.stringify(v.child)+",this);'>"+v.menuName+"</a></li>");
            }else{
                if(m_num<=tl_config.menu_max){
                $('.g_smallUlbao').append("<li><a href='#' onclick='javascript:tl_config.getTree("+JSON.stringify(v.child)+");'>"+v.menuName+"</a></li>");
                }
                m_num++;
                //console.log(m_num);
            }
          }
  
          });
          $('.g_bifUlbao li:first >a').trigger("click");
          $('.g_bifUlbao li:first').attr('class','g_qianBlue');
          }
           //头部
          $(".g_houLeft").hover(function(){
          $(".g_niazhanK").removeClass("g_none");
          $(this).css({"background":"#fff","border":"1px solid #ddd","width":"178px","height":"70px"});
          $(this).children("span").css({"color":"#222","border":"none","background":"url('../images/g_huiXia.png') 154px 11px no-repeat"});
           },function(){
            $(".g_niazhanK").addClass("g_none");
            $(this).css({"background":"#1777d8","border":"none","width":"180px","height":"72px"});
            $(this).children("span").css({"color":"#fff","border-right":"1px solid #4592e0","background":"url('../images/g_upDown.png') 154px 11px no-repeat"});
          })
          $(".g_huMing").hover(function(){
          $(".g_yuiChu").removeClass("g_none");
          $(this).css({"background":"#fff"});
          $(this).children("span").css({"color":"#222","background":"url('../images/g_huiXia.png') 50px 11px no-repeat"});
          },function(){
          $(".g_yuiChu").addClass("g_none");
          $(this).css({"background":"#1777d8"});
          $(this).children("span").css({"color":"#fff","background":"url('../images/g_upDown.png') 50px 11px no-repeat"});
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
          $(this).children("span").css({"color":"#333","background":"url('../images/g_huiGong.png') 0px 2px no-repeat"});
        },function(){
          $(".g_biaoGecon").addClass("g_none");
          $(this).children("span").css("color","#fff");
         $(this).children("span").css({"color":"#fff","background":"url('../images/g_Sheiji.png') 0px 2px no-repeat"});
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
     if(tl_config.init_url!=''){
         $('.g_rightMidden').html('');
         tl_config.his_url = tl_config.init_url;
         tl_config.loadhtml(tl_config.init_url);
     }
      },
      itemOnclick:function(obj,dataobj){ 
    	  $('.g_leftAll>div>div>ul').children('li').each(function(){
    	      $(this).removeClass('tl_back');
    	    });
    	     $(obj).attr('class','tl_back');
    	     $('.g_leftAll>div>div>ul').children('li').each(function(){
    	       if($(this).attr('class')=='tl_back' || $(this).attr('class')=='tl_back'){
    	        $(this).css("background", "#f5f5f5");
    	      }else{
    	        $(this).css("background", "none");
    	      }
    	    });

              if(dataobj.menuUrl!=undefined && dataobj.menuUrl!='' && dataobj.menuUrl!=null){
                $('.g_rightMidden').html('');
                if(dataobj.args!=undefined && dataobj.args!='' && dataobj.args!=null){
                  var args='';
                  for (var key in dataobj.args) 
                  {
                      args +='&'+key+'='+dataobj.args[key];
                  }
                var tl_str=dataobj.menuUrl;
                if(tl_str.indexOf("?")==-1){
                    args=args.substr(1,args.length);
                    tl_config.his_url = dataobj.menuUrl+"?"+args;
                    tl_config.loadhtml(dataobj.menuUrl+"?"+args);
                }else{
                    tl_config.his_url = dataobj.menuUrl+args;
                    tl_config.loadhtml(dataobj.menuUrl+args);
                }
              }else{
                    tl_config.his_url = dataobj.menuUrl;
                    tl_config.loadhtml(dataobj.menuUrl);
              }
           }          
      },
      getTree:function(data,obj){  
            $('.g_leftAll').html('');
            $.each(data,function(i,v){
            var node_str='<div class="g_indent"><p class="g_below">'+v.menuName+'</p><div class="g_dowmXia"><ul>';
            $.each(v.child,function(ii,vv){
              node_str += "<li onclick='tl_config.itemOnclick(this,"+JSON.stringify(vv)+")'><a href='javascript:void(0);'>"+vv.menuName+"</a></li>";
            })
            node_str +='</ul></div></div>';
            $('.g_leftAll').append(node_str);
            });

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
            $(".g_dowmXia li").hover(function() {
                if($(this).attr('class')==undefined || $(this).attr('class')==''){
                 $(this).css("background", "#f5f5f5");
               }
               
             }, function() {
               if($(this).attr('class')==undefined || $(this).attr('class')==''){
                 $(this).css("background", "none");
               }
               
             })

        $('.g_bifUlbao li').removeClass("g_qianBlue");
        $(obj).parent().attr('class','g_qianBlue');
        $(".g_rightMidden").html('');
      },
      loadToWorkArea:function(url){
        if(url!=''){
          $('.g_rightMidden').html('');
          tl_config.his_url = url;
          tl_config.loadhtml(url);
        }
      },
      reloadMenu:function(node){
          $('.g_bifUlbao').html('');
          $('.g_leftAll').html('');
          $('.g_rightMidden').html('');
          tl_config.init_setting.menu_node=node;
          tl_config.init(tl_config.init_setting);
      },
       loadhtml:function(url){
          $.get(url, function(result){
                    $(".g_rightMidden").html(result);
                });
      },
      listen:function(){
         var ajaxBack = $.ajax;
         $.ajax = function(setting){
         var cb = setting.success;
         setting.success = function(){
        	 var data=arguments[0];
             if(data.retCode=='1003'){
           	  localStorage.clear();
	 			  //window.location.href="login.do";
	 			  return;
             }
            if($.isFunction(cb)){cb.apply(setting.context, arguments);}
            }
          ajaxBack(setting);

        }
        tl_config.refresh();
      },
      refresh:function(){
        document.onkeydown = function (e) {
        e = e || window.event;
        if ((e.ctrlKey&&e.keyCode==82) || 
            e.keyCode == 116) {
            e.returnValue = false
            if (e.preventDefault) e.preventDefault();
            else e.keyCode = 0;
            tl_config.loadToWorkArea(tl_config.his_url);
            return false;
        }
    }
    window.onbeforeunload = function (e) {
        return (e || window.event).returnValue = '确认要离开当页面？！';
    }
      }
}
tl_config.listen();