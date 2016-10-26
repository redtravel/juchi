<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>限抢商品排序-<?php echo $rush->rush_index; ?></title>
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
#replace div{margin:5px;overflow:hidden;width:85px;height:130px;background:url(/public/style/img/replace_bg_85x130.png) no-repeat;}
.pro_c{overflow:hidden;display:block;float:left}
.pro_c li{width:97px;height:145px;float:left;display:block;overflow:hidden;}
.pro_c .box{position:relative;width:85px;height:130px;border:1px solid #dddddd;margin:5px;background-color:#fff;}
.pro_c li a{display:block;}
.pro_c li dl{margin-top:2px}
.pro_c li dd{padding-left:2px;height:14px;line-height:14px;clear:both;overflow:hidden}
.pro_c li span,#inputNumber{position:absolute;top:0;left:3px;font-family:arial;font-size:16px;font-weight:bold;}
#inputNumber{border:0;width:34px;top:6px;left:8px;text-align:center;}
.priceNum{font-size:28px;font-family:'微软雅黑';color:red}
.pro_c .sideRed{border:1px solid red}
.button{border-radius:5px;width:60px;height:60px;line-height:50px;background-color:#b2d7ff;display:inline-block;text-align:center;color:#FFF;border:none;cursor:pointer;position:fixed;right:50px;top:50%;_position:absolute;}
.button2{border-radius:5px;width:60px;height:30px;line-height:30px;background-color:#b2d7ff;display:inline-block;text-align:center;color:#FFF;border:none;cursor:pointer;position:fixed;right:50px;top:100px;_position:absolute;}
#Sq_mask{position:absolute;top:0;left:0;background-color:#000;opacity:0.01;filter:alpha(opacity=1);}
</style>
</head>
<body>
<div id="icontent">
	<div class="pro_c">
	    <ul id="product_squ">
		<?php foreach ($link_product as $info):?>
		<li>
		    <div class="box"><a><img class="lazy" width="85" height="85" 
			    <?php if(isset($info->gallery) && !empty($info->gallery->img_85_85)): ?>src="/public/data/images/<?php echo $info->gallery->img_85_85; ?>"
			    <?php else:?>src="/public/style/img/no_img.png"<?php endif;?>
			     />
			    </a>
			    <dl>
				<dd title="<?php echo $info->product_name; ?>"><?php echo $info->product_name; ?></dd>
				<dd title="<?php echo $info->category_name; ?>"><?php echo $info->category_name; ?></dd>
				<!--<dd title="大衣">大衣</dd>-->
			    </dl>
			    <span flag="sort_flg" rec_id="<?php echo $info->rec_id; ?>"></span>
		    </div>
		</li>
		<?php endforeach;?>
	    </ul>
	</div>
	<a class="button2" href="/rush/sort_view/<?php echo $rush->rush_id; ?>">切换</a>
	<input id="btn" type="submit" class="am-btn am-btn-primary" onclick="sort_rush_product();" value="保存">
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

function sort_rush_product(){
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
		alert('无法获取排列商品信息');
		return false;
	}
	$.ajax({
		url:'/rush/proc_sort',
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
$('.button2').hover(function () {$(this).css('backgroundColor','#328df0')},function () {$(this).css('backgroundColor','#b2d7ff')});
</script>
</body>
</html>