var gridObj;
var tl_obj = $.parseJSON(node);
$.each(tl_obj,function(i,v){
  if(i>tl_config.menu_num && 0==tl_config.node_dropdown){
      $('.g_bifUlbao').append('<li class="g_nCaidan"><span>菜单列表</span><div class="g_biaoGecon g_none"><ul class="g_smallUlbao"></ul><div></li>');
      $('.g_smallUlbao').append("<li><a href='#' onclick='javascript:getTree("+JSON.stringify(v.nodes)+");'>"+v.text+"</a></li>");
      tl_config.node_dropdown=1;
  }else{
    if(0==tl_config.node_dropdown){
      $('.g_bifUlbao').append("<li><a href='#' onclick='javascript:getTree("+JSON.stringify(v.nodes)+",this);'>"+v.text+"</a></li>");
    }else{
      $('.g_smallUlbao').append("<li><a href='#' onclick='javascript:getTree("+JSON.stringify(v.nodes)+");'>"+v.text+"</a></li>");
    }
  }
  
})
//树形点击事件
function itemOnclick(obj,dataobj){  
    console.log(obj);
    console.log(dataobj);
    if(dataobj.url!=undefined && dataobj.url!=''){
      $('.g_rightMidden').html('');
      //$(".g_rightMidden").load(dataobj.url);
      if(dataobj.args!=undefined && dataobj.args!=''){
          var args='';
          for (var key in dataobj.args) 
          {
              args +='&'+key+'='+dataobj.args[key];
          }
          var tl_str=dataobj.url;
          if(tl_str.indexOf("?")==-1){
            args=args.substr(1,args.length);
            $(".g_rightMidden").load(dataobj.url+"?"+args);
            console.log(dataobj.url+"?"+args);
          }else{
            $(".g_rightMidden").load(dataobj.url+args);
            console.log(dataobj.url+args);
          }
      }
      //$.get(dataobj.url,function(data){ $(".g_rightMidden").html(data);});
    }
}  
function getTree(data,obj) {
    $('.g_leftAll').html('');
    $.each(data,function(i,v){
      var node_str='<div class="g_indent"><p class="g_below">'+v.text+'</p><div class="g_dowmXia"><ul>';
      $.each(v.nodes,function(ii,vv){
        node_str += "<li onclick='itemOnclick(this,"+JSON.stringify(vv)+")'><a href='javascript:void(0);'>"+vv.text+"</a></li>";
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
    $(".g_dowmXia li").hover(function(){
      $(this).css("background","#f5f5f5");
    },function(){
      $(this).css("background","none");
    })
   $('.g_bifUlbao li').removeClass("g_qianBlue");
   $(obj).parent().attr('class','g_qianBlue');
}

$(function () {
        gridObj = $.fn.bsgrid.init('g_tabYeh', {
           /* dataType: 'json',
            ajaxType: 'post',
            url: '/user/getuserlist',
            otherParames:{url:'http://192.168.102.211:8080/TianlianDemoMgrWebSev/rest/userList.do',rows:10, pages:1},*/
            autoLoad: true,
            pageSizeSelect: false,
            stripeRows: true,
            pageSize: 10,
            displayBlankRows: false,
            isProcessLockScreen:true,
            rowSelectedColor:false
        });
        //var userdatas=[];
        var grid_datas=$.parseJSON(grid_data);
            /*userdatas.success=true;
            userdatas.totalRows=grid_datas.data.total;
            userdatas.curPage=1;
            userdatas.data=grid_datas.data.rows;
            //console.log(userdatas);*/
            gridObj.loadGridData('json',grid_datas);
      /*$.ajax({
        type:"POST",
        url:"/user/getuserlist",
        data: {url:'http://192.168.102.211:8080/TianlianDemoMgrWebSev/rest/userList.do',rows:10, pages:1},
        dataType: "json",
        success:function (userdata){  
          var userdatas=[];
          userdatas.success=true;
          userdatas.totalRows=4;
          userdatas.curPage=1;
          userdatas.data=userdata.data.rows;
          gridObj.loadGridData('json',userdatas);
          $("#searchTable").tableDnD({
           onDrop:function(table,row){
           //console.log(row);
            //console.log(table.tBodies[0].rows);
            var rows = table.tBodies[0].rows;
            var debugStr = "Row dropped was "+row.id+". New order: ";
            for (var i=0; i<rows.length; i++) {
                debugStr += rows[i].id+" ";
            }
             console.log(debugStr);
           },
           onDragStart: function(table, row) {
             //console.log(row);
          }
        });
        }
    });*/
    $('.g_bifUlbao li:first >a').trigger("click");
    $('.g_bifUlbao li:first').attr('class','g_qianBlue');
    //头部
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
    });
    function operate(record, rowIndex, colIndex, options) {
        return '<a href="javascript:EditUser(' + gridObj.getRecordIndexValue(record, 'id') + ');">编辑</a><a href="javascript:DelUser(' + gridObj.getRecordIndexValue(record, 'id') + ');">删除</a>';
    }
    function AddUser () {
       window.location.href='/user/add';
    }
    function EditUser (id) {
       window.location.href='/user/edit?id='+id;
    }
    function DelUser(id){
        $.get("/user/del?id="+id, function(result){
            //console.log(result);
           window.location.reload();
        });
    }
    function sex_operate(record, rowIndex, colIndex, options) {
        if(1==gridObj.getRecordIndexValue(record, 'sex')){
            return "男";
        }else{
            return "女";
        }
    }
//获取checkbox选中
function getCheckedIds() {
        // values are array
        alert(gridObj.getCheckedValues('id'));
}
