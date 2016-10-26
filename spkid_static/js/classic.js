// img标签 onerror方法
//shopGroupImgOnError(this, '麦考林')
/*BG*/
/*.bg_o{position:relative}
.bg_o .bg{position:absolute;top:0;left:0;display:block;width:100%;height:100%;opacity:0.8;filter:alpha(opacity=80)}
.b_black .bg{background:#000;background:-moz-linear-gradient(top, #666666 0%, #000000 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,#666666), color-stop(100%,#000000));background:-webkit-linear-gradient(top, #666666 0%,#000000 100%);background:-o-linear-gradient(top, #666666 0%,#000000 100%);background:-ms-linear-gradient(top, #666666 0%,#000000 100%);filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#666666', endColorstr='#000000',GradientType=0);background:linear-gradient(top, #666666 0%,#000000 100%)}
.b_pink .bg{background:#ee074e;background:-moz-linear-gradient(top, #fb608f 0%, #ee074e 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,#fb608f), color-stop(100%,#ee074e));background:-webkit-linear-gradient(top, #fb608f 0%,#ee074e 100%);background:-o-linear-gradient(top, #fb608f 0%,#ee074e 100%);background:-ms-linear-gradient(top, #fb608f 0%,#ee074e 100%);filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#666666', endColorstr='#ee074e',GradientType=0);background:linear-gradient(top, #fb608f 0%,#ee074e 100%)}
.b_orange .bg{background:#ea6208;background:-moz-linear-gradient(top, #f39b61 0%, #ea6208 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,#f39b61), color-stop(100%,#ea6208));background:-webkit-linear-gradient(top, #f39b61 0%,#ea6208 100%);background:-o-linear-gradient(top, #f39b61 0%,#ea6208 100%);background:-ms-linear-gradient(top, #f39b61 0%,#ea6208 100%);filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#f39b61', endColorstr='#ea6208',GradientType=0);background:linear-gradient(top, #f39b61 0%,#ea6208 100%)}
.b_green .bg{background:#098609;background:-moz-linear-gradient(top, #5fc45f 0%, #098609 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,#5fc45f), color-stop(100%,#098609));background:-webkit-linear-gradient(top, #5fc45f 0%,#098609 100%);background:-o-linear-gradient(top, #5fc45f 0%,#098609 100%);background:-ms-linear-gradient(top, #5fc45f 0%,#098609 100%);filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#5fc45f', endColorstr='#098609',GradientType=0);background:linear-gradient(top, #5fc45f 0%,#098609 100%)}
.b_blue .bg{background:#113cbc;background:-moz-linear-gradient(top, #5e7edc 0%, #113cbc 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,#5e7edc), color-stop(100%,#113cbc));background:-webkit-linear-gradient(top, #5e7edc 0%,#113cbc 100%);background:-o-linear-gradient(top, #5e7edc 0%,#113cbc 100%);background:-ms-linear-gradient(top, #5e7edc 0%,#113cbc 100%);filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#5e7edc', endColorstr='#113cbc',GradientType=0);background:linear-gradient(top, #5e7edc 0%,#113cbc 100%)}
.bg_o .name{position:absolute;z-index:1;top:40%;left:0;display:block;width:100%;line-height:14px;font-weight:bold;color:#fff;text-align:center}
*/
function shopGroupImgOnError(obj, minishoptitle) {

		var li_class_name = ["bg_o b_black", "bg_o b_pink", "bg_o b_orange", "bg_o b_green", "bg_o b_blue"];
		var nickname = $(obj).parent().attr("title");
		$(obj).parent("span").addClass(li_class_name[Math.floor(Math.random() * li_class_name.length)]);
		$(obj).parent("span").append("<span class='bg'></span><span class='name'>" + minishoptitle + "</span>");
		$(obj).remove();
	}