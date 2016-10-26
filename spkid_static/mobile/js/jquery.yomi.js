(function($){
$.fn.daojishi=function(){
	var data="";
	var _DOM=null;
	var TIMER;
	createdom =function(dom){
		_DOM=dom;
		data=$(dom).attr("data");
		data = data.replace(/-/g,"/");
		data = Math.round((new Date(data)).getTime()/1000);		
		$(_DOM).append("<div class='daojishi-jl'>剩余<span class='countdown_day'></span>天<span class='djs-bg countdown_hour'></span>时<span class='djs-bg countdown_min'></span>分<span class='djs-bg countdown_sec'></span>秒</div>")
		reflash();

	};
	reflash=function(){
		var	range  	= data-Math.round((new Date()).getTime()/1000),
					secday = 86400, sechour = 3600,
					days 	= parseInt(range/secday),
					hours	= parseInt((range%secday)/sechour),
					min		= parseInt(((range%secday)%sechour)/60),
					sec		= ((range%secday)%sechour)%60;
		$(_DOM).find(".countdown_day").html(nol(days));
		$(_DOM).find(".countdown_hour").html(nol(hours));
		$(_DOM).find(".countdown_min").html(nol(min));
		$(_DOM).find(".countdown_sec").html(nol(sec));

	};
	TIMER = setInterval( reflash,1000 );
	nol = function(h){
					return h>9?h:'0'+h;
	}
	return this.each(function(){
		var $box = $(this);
		createdom($box);
	});
}
})(jQuery);
$(function(){
	$(".daojishi").each(function(){
		$(this).daojishi();
	});	
});