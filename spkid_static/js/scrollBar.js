//滚动条及即将上线弹层显示
function InitScrollBar(parBox,txtContent,txtTrack,txtHandle,vHeight,upTarget,downTarget){
		//对象初始化
		var ObjScroll = new Object();
		ObjScroll.MaxScroll = vHeight;                                      //最大高度
		ObjScroll.ScrollBox = document.getElementById(parBox);              //内容框父层
		ObjScroll.ScrollContent = document.getElementById(txtContent);      //内容框
		ObjScroll.ScrollTrack = document.getElementById(txtTrack);			//滚动条框
		ObjScroll.ScrollHandle = document.getElementById(txtHandle);        //滚动条
		ObjScroll.upTarget=document.getElementById(upTarget);               //向上滚按钮
		ObjScroll.downTarget=document.getElementById(downTarget);           //向下滚按钮
		var stepForBar;                                                     //步长 
		 //对象不存在
		if(!(ObjScroll.ScrollBox && ObjScroll.ScrollContent && ObjScroll.ScrollTrack  && ObjScroll.ScrollHandle)){
			ObjScroll.ScrollTrack.style.display = "none";
			return;
		}
		//初始化元件高度、位置
		ObjScroll.ScrollAmount = ObjScroll.ScrollContent.offsetHeight/ObjScroll.MaxScroll;
		if(ObjScroll.ScrollAmount>1){
			
			ObjScroll.ScrollBox.scrollTop=0;
			ObjScroll.ScrollHandle.onmousedown = function(e){
				if (!e)
				e = window.event;
				beginDrag(e);
			}
			if(document.all){
				ObjScroll.ScrollContent.onmousewheel = function(){wheelScroll(event);}
			}else{
				ObjScroll.ScrollContent.addEventListener("DOMMouseScroll", wheelScroll, false);
			}
			ObjScroll.ScrollTrack.style.height = 240 + "px";
			ObjScroll.ScrollBox.style.height = ObjScroll.MaxScroll + "px";
			
			ObjScroll.ScrollTrack.style.display = 'block';
			
			ObjScroll.ScrollHandle.style.marginTop = "0px";
			ObjScroll.ScrollHandle.style.height = Math.floor(240/ObjScroll.ScrollAmount) + "px";
			ObjScroll.ScrollHandle.style.backgroundPosition='-1px '+ parseInt((parseInt(ObjScroll.ScrollHandle.style.height)-14)/2) +'px';
			ObjScroll.MaxLength =  ObjScroll.MaxScroll - Math.floor(ObjScroll.MaxScroll/ObjScroll.ScrollAmount);
			ObjScroll.MaxBarLength = 240 - Math.floor(240/ObjScroll.ScrollAmount);
			//点击向下箭头
			ObjScroll.downTarget.onclick=function(){
				downIconClick();
			};
			//点击向上箭头
			ObjScroll.upTarget.onclick=function(){
				upIconClick();
			};
			
		}else{
			ObjScroll.ScrollTrack.style.display = "none";
		}

		//向下箭头点击事件
		function downIconClick(){
			stepForBar=parseInt(70*(240 - parseInt(ObjScroll.ScrollHandle.style.height))/(ObjScroll.ScrollContent.offsetHeight -280));
			//240 - parseInt(ObjScroll.ScrollHandle.style.height) - parseInt(ObjScroll.ScrollHandle.style.marginTop) > stepForBar
			if(ObjScroll.ScrollContent.offsetHeight - ObjScroll.ScrollBox.scrollTop - 240 >= 70){
				ObjScroll.ScrollBox.scrollTop +=70;
				ObjScroll.ScrollHandle.style.marginTop = parseInt(ObjScroll.ScrollHandle.style.marginTop) + stepForBar + "px";
			}
			
		}
		//向上箭头点击事件
		function upIconClick(){
			stepForBar=parseInt(70*(240 - parseInt(ObjScroll.ScrollHandle.style.height))/(ObjScroll.ScrollContent.offsetHeight -280));
			if(ObjScroll.ScrollBox.scrollTop>0){
				ObjScroll.ScrollBox.scrollTop -=70;
				ObjScroll.ScrollHandle.style.marginTop = parseInt(ObjScroll.ScrollHandle.style.marginTop) - stepForBar + "px";
			}
		}

		//鼠标滚轮事件
		function wheelScroll(event){
			if (event&&event.preventDefault){ 
				event.preventDefault();
				event.stopPropagation();
			}else{ 
				 event.returnvalue=false;  
				 return false;     
			}
			var wAmount = event.wheelDelta;
			if(!wAmount) wAmount = -event.detail*40;    
			ObjScroll.ScrollBox.scrollTop -= wAmount/12;
			if(ObjScroll.ScrollBox.scrollTop == 0) {
				ObjScroll.ScrollHandle.style.marginTop = "0px";
			}else if(ObjScroll.ScrollBox.scrollTop == ObjScroll.ScrollContent.offsetHeight - 240){
				ObjScroll.ScrollHandle.style.marginTop = 240 +"px";
			}else{
				ObjScroll.ScrollHandle.style.marginTop = parseInt(ObjScroll.ScrollHandle.style.marginTop) - Math.floor(wAmount/(12*ObjScroll.ScrollAmount)) + "px";
			}
			if(parseInt(ObjScroll.ScrollHandle.style.marginTop) > (240 - parseInt(ObjScroll.ScrollHandle.style.height))) ObjScroll.ScrollHandle.style.marginTop = 240 - parseInt(ObjScroll.ScrollHandle.style.height)+"px";
		}
	 
		//鼠标释放
		function upHandler(e){
			if (!e) e = window.event;
			document.onmouseup = "";
			document.onmousemove = "";
			document.onmouseout = "";
			if(document.all) ObjScroll.ScrollHandle.releaseCapture();
		}
		//鼠标移开
		function outHandler(e){
			if (!e) e = window.event;       
			document.onmouseup = "";
			document.onmousemove = "";
			document.onmouseout = "";
			if(document.all) ObjScroll.ScrollHandle.releaseCapture();
		}
		//拖动滚动条事件
		function beginDrag(event) {
			var deltaY = event.clientY - parseInt(ObjScroll.ScrollHandle.style.marginTop);       
			document.onmousemove = moveHandler;
			document.onmouseup = upHandler;
			document.onmouseout = outHandler; 
			if(document.all) ObjScroll.ScrollHandle.setCapture();
		//鼠标拖动事件
		function moveHandler(e) {
			if (!e)
			e = window.event;
			ObjScroll.ScrollHandle.style.marginTop = (e.clientY - deltaY) + "px";
			if((e.clientY - deltaY)<0){
				ObjScroll.ScrollHandle.style.marginTop = 0 +"px";
			}else if((e.clientY - deltaY)>ObjScroll.MaxLength){
				ObjScroll.ScrollHandle.style.marginTop = ObjScroll.MaxBarLength +"px";
			}else{
				ObjScroll.ScrollBox.scrollTop = Math.floor((e.clientY - deltaY ) * ObjScroll.ScrollAmount);  
			}
		}
	}
}
