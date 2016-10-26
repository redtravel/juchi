//返回顶部按钮
$(function () {
	var topBtn=$(".toTop_btn"),
		topdiv=$("#top_lay"),
		toTopNum=40;
	topBtn.click(function (){$("html, body").animate({scrollTop:0},100)});
	function toTop() {
		var fh=$('#footer').height(),
			ft=$('#footer').offset().top,
			st=$(document).scrollTop(),
			ww=$(window).width(),
			wh=$(window).height(),
			l=ww/2+520,
			bh=fh+ft,
			nowPos=st+wh,
			b=nowPos-ft+toTopNum+0,
			ieT=st+wh-247;
		topdiv.css('left',l);
		(st>0)?topdiv.show():topdiv.hide();
		if (window.XMLHttpRequest) {
			b=(wh+st>ft)?b:toTopNum+0;
			topdiv.css('bottom',b);
		} else {//IE6
			ieT=wh+st>ft?ieT-b+toTopNum+2:ieT;
			topdiv.css('top',ieT);
		}
	}
	$(window).bind("scroll",toTop);
	$(window).bind("resize",toTop);
	$(window).bind("load",toTop);
});