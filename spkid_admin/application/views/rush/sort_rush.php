<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>限抢排序-<?php echo $start_time; ?></title>
<script type="text/javascript" src="/public/js/jquery.js"></script>
<script type="text/javascript">
function isIE6() {
	var version = navigator.userAgent.indexOf('MSIE 6.0')>-1;
	return version;
}
</script>
<style type="text/css">
html,body{overflow:auto;}
body{font:12px/25px "宋体";}
*{margin:0;padding:0}
ul,li{list-style:none;}
.l{float:left;}
.r{float:right;}
.y_p{text-decoration:line-through;}
#icontent{width:1000px;margin:0 auto;}
#replace{}
#replace div{margin:5px;overflow:hidden;width:318px;height:394px;background:url(/public/style/img/replace_bg.png) no-repeat;}
.pro_c{overflow:hidden;display:block;float:left}
.pro_c li{width:330px;height:406px;float:left;display:block;overflow:hidden;}
.pro_c .box{position:relative;width:318px;height:394px;border:1px solid #dddddd;margin:5px;background-color:#fff;}
.pro_c li a{display:block;}
.pro_c li dl{margin-top:5px}
.pro_c li dd{padding-left:8px;height:24px;line-height:24px;padding-right:15px;overflow:hidden}
.pro_c li span,#inputNumber{position:absolute;top:10px;left:10px;font-family:arial;font-size:28px;font-weight:bold;}
#inputNumber{border:0;width:50px;top:6px;left:8px;text-align:center;}
.priceNum{font-size:28px;font-family:'微软雅黑';color:red}
.pro_c .sideRed{border:1px solid red}
.button{border-radius:5px;width:60px;height:60px;line-height:50px;background-color:#b2d7ff;display:inline-block;text-align:center;color:#FFF;border:none;cursor:pointer;position:fixed;right:50px;top:50%;_position:absolute;}
#Sq_mask{position:absolute;top:0;left:0;background-color:#000;opacity:0.01;filter:alpha(opacity=1);}
</style>
</head>
<body>
<div id="icontent">
	<div class="pro_c">
	    <ul id="product_squ">
		<?php foreach ($rush_list as $info):?>
		<li>
		    <div class="box"><a><img class="lazy" width="318" height="318" 
			    <?php if(isset($info->image_before_url) && !empty($info->image_before_url)): ?>src="/public/data/images/<?php echo $info->image_before_url; ?>"
			    <?php else:?>src="/public/style/img/no_img.png"<?php endif;?>
			     />
			    </a>
			    <dl>
				<dd>
				    <div class="l"><?php if(!empty($info->rush_index)) echo $info->rush_index; ?></div>
				    <div class="r" style="color:#666;"><font class="y_p"></font></div>
				</dd>
				<dd>
				    <div class="l"><?php if(!empty($info->rush_category))echo $info->rush_category; ?></div>
				    <div class="r" style="margin-top:5px;">(<?php if(!empty($info->rush_discount))echo $info->rush_discount; ?>折起)</div>
				    <div class="priceNum r">
					<?php 
				    if($info->status == 0)	{echo '未激活'; }
				    elseif($info->status == 1){echo '已激活'; }
				    elseif($info->status == 2){echo '停止'; }
				    elseif($info->status == 3){echo '结束'; }
				    else		{ echo "未知";}
				     ?></div>
				</dd>
			    </dl>
			    <span flag="sort_flg" rec_id="<?php echo $info->rush_id; ?>"></span>
		    </div>
		</li>
		<?php endforeach;?>
	    </ul>
	</div>
	<input id="btn" type="submit" class="am-btn am-btn-primary" onclick="sort_rush();" value="保存">
</div>
<script type="text/javascript" src="/public/js/creatSequence.js"></script>
<script type="text/javascript">
creatSequence({
	ID:'product_squ',
	speed:100,
	offset:100,
	tagsName:'span'
});
if (isIE6()) {
	window.onscroll = function () {
		document.getElementById('icontent').children[1].style.top=document.documentElement.scrollTop+document.documentElement.clientHeight/2+'px';
	}
}

function sort_rush(){
	$("#btn").attr("disabled",true).css('backgroundColor','#ccc');
	var rec_ids = new Array();
	$('span[flag=sort_flg]').each(function(){
		var rec_id = $(this).attr("rec_id");
		var val = $(this).html();
		rec_ids.push(rec_id + "_" + val);
	});
	if (rec_ids.length>0)
		rec_ids = rec_ids.join(',');
	else {
		alert('无法获取排列限抢信息');
		return false;
	}
	$.ajax({
		url:'/rush/proc_sort_rush',
		data:{rec_ids:rec_ids,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.msg) alert(result.msg);
			if(result.err!=0) return false;
			alert("操作完成！");
			window.location.reload();
		}
	});
}
$('.button').hover(function () {$(this).css('backgroundColor','#328df0')},function () {$(this).css('backgroundColor','#b2d7ff')});
</script>
</body>
</html>