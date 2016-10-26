function jumpTo(element,time) {
	var dom = document.getElementById(element);
	dom.innerHTML=time;
	if (--time<0) {
		window.history.back(-1)
	}
	else {
		setTimeout("jumpTo('"+element+"',"+time+")",1000);
	}
}