function myscroll(direction){
	if (direction == 'left'){
		$("#pictures").find("ul:last").css('marginLeft', '-162px');
		$("#pictures").find("li:last").insertBefore($("#pictures").find("li:first"));

		$("#pictures").find("ul:first").animate({
			marginLeft:"0px"
			},
			1000
		);
	} else {
		$("#pictures").find("ul:first").animate({
			marginLeft:"-162px"
			},
			1000,
			function(){
				$(this).css({
					marginLeft:"0"
				}).find("li:first").appendTo(this);
			}
		);
	}
}

$(function(){
	//轮播图鼠标移入移出效果
	$('.scroll li').hover(function () {
		$(this).addClass('borderRed');
	},function () {
		$(this).removeClass('borderRed');
	});

	//轮播图左右箭头的鼠标移入移出效果
	$('.prev').hover(function () {
		$(this).removeClass('prev').addClass('prevHover');
	},function () {
		$(this).removeClass('prevHover').addClass('prev');
	});
	$('.next').hover(function () {
		$(this).removeClass('next').addClass('nextHover');
	},function () {
		$(this).removeClass('nextHover').addClass('next');
	});

	

	/*try {
		load_ad('#uc_first','index',0,0,'index_right');
	} catch(e){}*/
});