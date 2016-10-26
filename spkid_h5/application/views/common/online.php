<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<base href="<?php print site_url(); ?>"></base>
<link rel="stylesheet" href="<?php print static_style_url('css/common.css'); ?>" type="text/css" />
<script type="text/javascript" src="<?php print static_style_url('js/jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/lazyload.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/common.js'); ?>"></script>
<script type="text/javascript">
  var img_host = '<?php print img_url(""); ?>';
  var base_url = '<?php print base_url(); ?>';
</script>
<title>爱童网_<?php print $title ?></title>
<style type="text/css">
html,body{ height:100%;}
body{ overflow:hidden;}
</style>
<script type="text/javascript">
var winWidth = 0;
var winHeight = 0;
var rWidth=0;
var cur_rec = '<?php echo $cur_rec ?>';
var cur_reply = '<?php echo $cur_reply  ?>';
var cur_page = 0;
var total_page = 0;
var gtimerArr = '';
var stop_flag = 0;
function findDimensions() //函数：获取尺寸
						{
						//获取窗口宽度
						if (window.innerWidth)
						winWidth = window.innerWidth;
						else if ((document.body) && (document.body.clientWidth))
						winWidth = document.body.clientWidth;
						//获取窗口高度
						if (window.innerHeight)
						winHeight = window.innerHeight;
						else if ((document.body) && (document.body.clientHeight))
						winHeight = document.body.clientHeight;
						//通过深入Document内部对body进行检测，获取窗口大小
						if (document.documentElement  && document.documentElement.clientHeight && document.documentElement.clientWidth)
						{
						winHeight = document.documentElement.clientHeight;
						winWidth = document.documentElement.clientWidth;
						}

						rWidth=$(".ol_c_right").width();
						rWidth=rWidth+35;
						//ol_c_left
						rWidth_l=rWidth-10;
						winHeight_l=winHeight-39;
						winWidth_l=winWidth-rWidth_l;
						$('.ol_c_left').css({height:winHeight_l,width:winWidth_l});
						$('.ol_histext').css({height:winHeight_l});
						winHeight_l=winHeight_l-40;
						$('.mes_history_text').css({height:winHeight_l});
						
						//ol_c_top
						winHeight=winHeight-200;
						if(winHeight<275){winHeight=275}else{winHeight=winHeight};
						winWidth=winWidth-rWidth;
						if(winWidth<250){winWidth=250}else{winWidth=winWidth};
						$('.ol_c_top').css({height:winHeight,width:winWidth});
						
		
}
$(function(){
    findDimensions();
	$(".mes_btn").toggle(
			    function(){$(".ol_ad").hide();$(".ol_histext").show();$(this).addClass("mes_onsel");show_total_msg();},
				function(){$(".ol_ad").show();$(".ol_histext").hide();$(this).removeClass("mes_onsel");findDimensions()}
						 );
	var intervalId = window.setInterval("auto_getdate()", 3000);

	$.extend({
		/**
		 * 调用方法： var timerArr = $.blinkTitle.show();
		 *			$.blinkTitle.clear(timerArr);
		 */
		blinkTitle : {
			show : function() {	//有新消息时在title处闪烁提示
				var step=0, _title = document.title;

				var timer = setInterval(function() {
					step++;
					if (step==3) {step=1};
					if (step==1) {document.title='【　　　】'+_title};
					if (step==2) {document.title='【新消息】'+_title};
				}, 500);

				return [timer, _title];
			},

			/**
			 * @param timerArr[0], timer标记
			 * @param timerArr[1], 初始的title文本内容
			 */
			clear : function(timerArr) {	//去除闪烁提示，恢复初始title文本
				if(timerArr) {
					clearInterval(timerArr[0]);
					document.title = timerArr[1];
					gtimerArr = '';
				};
			}
		}
	});
})
window.onresize=findDimensions;

function submit_msg()
{
	var svalue = $("#inputbox").val();
	svalue = $.trim(svalue);
	if (svalue != '')
	{
		$.ajax({
				url:'/user/submit_msg',
				data:{is_ajax:true,value:svalue,cur_rec:cur_rec,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						var msg_content = $('#msg_main').html();
						msg_content += result.content;
						$('#msg_main').html(msg_content);
						cur_rec = result.rec_id;
						$("#inputbox").val('');
						document.getElementById('msg_main').scrollTop = document.getElementById('msg_main').scrollHeight;
					}else
					{
						alert(result.msg);
					}
				}
			});
	}
	return false;
}

function auto_getdate()
{
	if (stop_flag == 1) return false;
	stop_flag = 1;
	$.ajax({
				url:'/user/get_reply_msg',
				data:{is_ajax:true,cur_reply:cur_reply,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						var msg_content = $('#msg_main').html();
						msg_content += result.content;
						$('#msg_main').html(msg_content);
						cur_reply = result.rec_id;
						document.getElementById('msg_main').scrollTop = document.getElementById('msg_main').scrollHeight;
						if (gtimerArr == '')
						{
							gtimerArr = $.blinkTitle.show();
						}
						stop_flag = 0;
					}else
					{
						stop_flag = 0;
					}
				}
			});
	return false;
}

function show_total_msg()
{
	$.ajax({
				url:'/user/show_total_msg',
				data:{is_ajax:true,page:0,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						$('#total_msg').html(result.content);
						cur_page = result.cur_page;
						total_page = result.total_page;
						findDimensions();
					}else
					{

					}
				}
			});
	return false;
}

function goto_page(page,flag)
{
	if (flag == 1)
	{
		page = parseInt(cur_page) + parseInt(page);
	}
	if (flag == 2)
	{
		page = parseInt(cur_page) - parseInt(page);
	}
	if (page > total_page)
	{
		page = total_page;
	}
	if (page < 1)
	{
		page = 1;
	}
	$.ajax({
				url:'/user/show_total_msg',
				data:{is_ajax:true,page:page,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						$('#total_msg').html(result.content);
						cur_page = result.cur_page;
						total_page = result.total_page;
					}else
					{

					}
				}
			});
	return false;
}

function handle_enter(obj,event)
{
	svalue = parseInt(obj.value);
	if (isNaN(svalue))
	{
		return false;
	}


	if(event.keyCode==13)
	{
		goto_page(svalue,0);
	}
}

function submit_other(event)
{
	if(event.keyCode==13)
	{
		submit_msg();
		return false;
	}
}

function closeme(flag)
{
	$.ajax({
				url:'/user/msg_close',
				data:{is_ajax:true,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
					}else
					{
						alert(result.msg);
					}
				}
			});
	if (flag > 0)
	{
		window.close();
	}

}

window.onunload=function(){
   closeme(0);
}

window.onfocus=function(){
   $.blinkTitle.clear(gtimerArr);
}



</script>
</head>
<body>
<div id="online">
  <div class="ol_top">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="37" height="36"><img src="<?php print static_style_url('img/common/ol_tl.png'); ?>" width="37" height="36" /></td>
        <td width="400">爱童网在线客服系统（工作时间：9:00-21:00）</td>
        <td align="right" valign="middle"><a href="#" onclick="closeme(1);return false;"><img src="<?php print static_style_url('img/common/ol_overt.png'); ?>" /></a><img src="<?php print static_style_url('img/common/ol_tr.png'); ?>" width="5" height="36" /></td>
      </tr>
    </table>
  </div>
  <div class="ol_c">
    <div class="ol_c_left">
      <div class="ol_c_top" id="msg_main">
        <?php if (!empty($message_list)): ?>
        <?php foreach ($message_list as $item): ?>
        <p><span class="<?php echo $item['qora']==0?'ol_cus':'ol_kf'; ?>"><?php echo $item['qora']==0?((isset($user_info->user_name) && !empty($user_info->user_name))?$user_info->user_name:'客户'):'本站客服'; ?> (<?php echo date('H:i:s',strtotime($item['create_date'])) ?>) </span><span class="ol_t"><?php echo $item['content'] ?></span></p>
		<?php endforeach; ?>
        <?php elseif ($has_man): ?>
        <p><span class="ol_kf">本站客服 (<?php echo date('H:i:s') ?>) </span><span class="ol_t">欢迎来到爱童网，很高兴为您服务！</span></p>
        <?php endif; ?>
        <?php if (!$has_man): ?>
        <p><span class="ol_kf">本站客服 (<?php echo date('H:i:s') ?>) </span><span class="ol_t">现在是非工作时段哦,请留言并留下您的手机或者qq等联系方式，我们的客服将会在第一时间联系您。</span></p>
        <?php endif; ?>
      </div>
      <div class="ol_c_bottom">
         <div class="menubar">&nbsp;&nbsp;请在下方输入您要咨询的问题!<span class="mes_btn"></span></div>
         <div class="cl"></div>
         <div class="inputbox">
           <div class="fix"><textarea name="inputbox" id="inputbox" onkeydown="submit_other(event);"></textarea></div>
           <div class="enter"><a href="#" onclick="submit_msg();return false;"><img src="<?php print static_style_url('img/common/ol_enter.png'); ?>" width="51" height="24" /></a></div>
         </div>
      </div>

    </div>
    <div class="ol_c_right" id="ol_c_right" >
       <div class="ol_ad"><img src="<?php print static_style_url('img/common/online_ad.jpg'); ?>" width="133" height="265" /></div>
       <div class="ol_histext" id="total_msg" style="position:relative;">
       </div>
    </div>
  </div>
</div>
</body>
</html>
