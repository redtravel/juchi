$(function(){
	$('#tuanTabUL li').click(function(){
		
		$(this).addClass('content-navbar__item--current');
		$(this).siblings().removeClass('content-navbar__item--current');
		var index=$('#tuanTabUL li').index($(this))+1;
		$('#tuanTabCon'+index).show();
		$('#tuanTabCon'+index).siblings().hide();
	});
	// common-fixed 675
	$(window).scroll(function(){
		if($(window).scrollTop()>675){
			$('#J-content-navbar').addClass('common-fixed');
			$('#J-nav-buy').show();
		}else{
			if($('#J-content-navbar').hasClass('common-fixed')){
				$('#J-content-navbar').removeClass('common-fixed');
			}
			if($('#J-nav-buy').css("display")=='block'){
				$('#J-nav-buy').hide();
			}
		}
	})
})

// 移上去的交互
$(".ulTuanRush li").hover(function(){
	$(this).css({borderBottom:'1px solid #f00'});
},function(){
	$(this).css({borderBottom:'1px solid #ddd'});
})