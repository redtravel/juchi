$(function(){
	$(".left dt").toggle(
		function(){$(this).removeClass("navt_u").addClass("navt_d");$(this).nextAll("dd").show().css("display","block");},
		function(){$(this).removeClass("navt_d").addClass("navt_u");$(this).nextAll("dd").hide();}
	)
});
