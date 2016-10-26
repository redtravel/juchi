<!doctype html>
<html>
<head>

 <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>演示站电子商务管理系统</title>

  <!-- Set render engine for 360 browser -->
  <meta name="renderer" content="webkit">

  <!-- No Baidu Siteapp-->
  <meta http-equiv="Cache-Control" content="no-siteapp"/>

  <link rel="icon" type="image/png" href="public/assets/i/favicon.png">

  <!-- Add to homescreen for Chrome on Android -->
  <meta name="mobile-web-app-capable" content="yes">
  <link rel="icon" sizes="192x192" href="assets/i/app-icon72x72@2x.png">

  <!-- Add to homescreen for Safari on iOS -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="apple-mobile-web-app-title" content="Amaze UI"/>
  <link rel="apple-touch-icon-precomposed" href="public/assets/i/app-icon72x72@2x.png">

  <!-- Tile icon for Win8 (144x144 + tile color) -->
  <meta name="msapplication-TileImage" content="public/assets/i/app-icon72x72@2x.png">
  <meta name="msapplication-TileColor" content="#0e90d2">

	<base href="<?php print base_url(); ?>" target="_self" />
	<link rel="stylesheet" href="public/style/style.css?v=2" type="text/css" media="all" />
	<script type="text/javascript">var base_url='<?php print base_url(); ?>';</script>

<!-- jquery ui -->
<link type="text/css" href="public/js/jui-1.11.4/jquery-ui.css" rel="stylesheet" />
<link type="text/css" href="public/js/jui-1.11.4/jquery-ui.theme.css" rel="stylesheet" />
<!-- amazeui -->
  <link rel="stylesheet" href="public/assets/css/amazeui.min.css?v=1.5">
  <link rel="stylesheet" href="public/assets/css/app.css">

<script src="public/assets/js/jquery.min.js"></script>
<script src="public/js/jquery.form.js"></script>
<script src="public/assets/js/amazeui.min.js"></script>

<script type="text/javascript" src="public/js/jui-1.11.4/jquery-ui.min.js"></script>
<!-- jquery editable -->
<link type="text/css" href="public/js/juieditable/css/jqueryui-editable.css" rel="stylesheet" />
<script type="text/javascript" src="public/js/juieditable/js/jqueryui-editable.js"></script>

<script language="javascript" type="text/javascript">
var right_speed = 100;
var flag=1;
var cur_c = 0;
window.onload=function(){
    remindshow_common('box_left','/remind/query_advice',50);
    remindshow_common('box_right','/remind/query_order',right_speed);
}
// shangguannan 20130426
function remindshow_common(id,link_url,speed){
    var o=document.getElementById(id);
	var param_str = {is_ajax:1, rnd : new Date().getTime()};
	$.ajax({
		url: link_url,
		data: param_str,
		dataType: 'json',
		type: 'POST',
		success: function(result){
			if(result.msg) {alert(result.msg)};
            
			if(result.error == 0){
                for(var i = 0;i< result.detail.length;i++){
                    var MyLi = document.createElement("li");
                    o.appendChild(MyLi);
                    MyLi.innerHTML =  result.detail[i];
                }
                scrollup(id,30,0,speed);
			}
		}
	});
	return false;
}
///滚动主方法
///参数:id 滚动块对象
///参数:d 每次滚屏高度
///参数:c 当前已滚动高度
///参数:speed 每次滚屏的时间间隔(毫秒)
function scrollup(id,d,c,speed){
    var o=document.getElementById(id);
	if(d===c){
		var t=getFirstChild(o.firstChild).cloneNode(true);
		o.removeChild(getFirstChild(o.firstChild));
		o.appendChild(t);
		t.style.marginTop="0px";
        window.setTimeout(function(){scrollup(id,d,0,speed);},speed);
	}else{
		getFirstChild(o.firstChild).style.marginTop=-c+"px";
        if(flag===1 || id === 'box_left'){
            c+=1;
            window.setTimeout(function(){scrollup(id,d,c,speed);},speed);
        }else{
            cur_c = c;
        }
	}
}
//解决firefox下会将空格回车作为节点的问题
function getFirstChild(node){
  while (node.nodeType!==1)
  {
         node=node.nextSibling;
  }
  return node;
}
function link_to(type){
    var last_time = '<?php echo date('Y-m-d H:i:s',mktime(0,0,0,date("m"),date("d")-1,date("Y"))); ?>';
	if(type===1){
        $("#add_end").val(last_time);
        $("#order_status").val(0);
    }else if(type===2){
        //$("#pay_end").val(last_time);
        //$("#pay_status").val(1);
        //$("#shipping_status").val(0);
    }else if(type===3){//在线支付
        //$("#add_end").val(last_time);
        //$("#order_status").val(1);
        //$("#shipping_status").val(0);
    }else if(type===4){
       //$("#odd").val(1);
    }else if(type===5){//问题单
       $("#odd").val(1);
    }
    document.init_form.submit();
}
function click_advice(){
    document.advice_form.submit();
}
function mouseOver(){
    flag=0;
}
function mouseOut(){
    flag=1;
    scrollup('box_right',30,cur_c,100);
}
</script>

</head>
<body bgcolor="<?php print empty($print_bgcolor)?'#FAFEF0':$print_bgcolor; ?>">
    <?php if(!isset($full_src)): ?>
    <div id="notice" style="width:98%; height:28px; margin:0 auto 10px auto;">
        <div class="notice" style="float:left; width:49%;cursor: pointer;" onclick="click_advice();" title="点击查看更多">
            <ul id="box_left" style="white-space: nowrap;">
            </ul>
        </div>
        <div class="notice" style="float:right; width:49%;">
            <ul id="box_right" style="white-space: nowrap;">
            </ul>
        </div>
    </div>
    <form name="advice_form" action="/order_advice" method="POST">
    </form>
    <form name="init_form" action="/order" method="POST">
        <input type="hidden" name="add_end" id="add_end"/>
        <input type="hidden" name="odd" id="odd"/>
        <input type="hidden" name="pay_end" id="pay_end"/>
        <input type="hidden" name="pay_status" id="pay_status" value="-1"/>
        <input type="hidden" name="order_status" id="order_status" value="-1"/>
        <input type="hidden" name="shipping_status" id="shipping_status" value="-1"/>
    </form>
    <?php endif; ?>