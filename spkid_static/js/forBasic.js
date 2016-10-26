// JavaScript Document
//新建iframe
	newIframe =function(id){
		var newFrame=document.createElement("iframe");
		var newFrameHeight=$('body').height();
		newFrame.src="";
		newFrame.scrolling='no';
		newFrame.scrollborder='0';
		newFrame.style.width='0px';
		newFrame.style.height='0px';
		newFrame.style.zIndex='99990';
		newFrame.style.background='#fff';
		newFrame.style.opacity='0.8';
		newFrame.style.top='0';
		newFrame.style.left='0';
		newFrame.id='frame'+id;
		document.body.appendChild(newFrame);
		var newDiv=document.createElement('div');
		newDiv.className='maskDiv';
		newDiv.style.width='100%';
		newDiv.style.height=newFrameHeight+'px';
		newDiv.style.background='#000';
		//newDiv.style.filter='alpha(opacity=60)';
		//newDiv.style.opacity='0.6';
		newDiv.style.position='absolute';
		newDiv.style.top='0';
		newDiv.style.left='0';
		newDiv.style.zIndex='99991';
		newDiv.id='div'+id;
		document.body.appendChild(newDiv);
	}
	delIframe=function(id){
		if(id){
			var tagId=document.getElementById('frame'+id);
			document.body.removeChild(tagId);
			tagId=document.getElementById('div'+id);
			document.body.removeChild(tagId);
		}
	}
	//加入收藏
	function addFavorite(){
		if(document.all){
			window.external.addFavorite('http://www.mammytree.com','演示站');
		}else if(window.sidebar){
			window.sidebar.addPanel('演示站','http://www.mammytree.com','');
		}else{
			alert('您的浏览器不支持加入收藏，请用 ctrl+d 收藏演示站');
		}
	}
	
	//日期时间full值转化时间戳
	function dateUTC(fullDate){
		if(fullDate){
			var str=fullDate.replace(/:/g,'-');
				str=str.replace(/ /g,'-');
			var arr=str.split('-');
			var timeTag=Date.UTC(arr[0],arr[1]-1,arr[2],arr[3]-8,arr[4],arr[5]);
			//console.log(timeTag);
			return timeTag;
			
		}
	}
	/*倒计时
	 *广告倒计时
	 *
	 */
	
	function timeOff(varOff){
		var i=setInterval(function countDown(){
			//计算差值
			var now=new Date();
			//varOff = 1366950385858;
			//endTime=Date(varOff);
			//varOff=now.getTime(endTime);
			if(varOff <= now.getTime()){
				return false;
			}else{
				varSub=Math.floor((varOff - now.getTime())/1000);
				varHour=Math.floor(varSub/3600);
				varMinute=Math.floor((varSub%3600)/60);
				varSecond=varSub%3600%60;
				$('#banTHour').text(varHour);
				$('#banTMin').text(varMinute);
				$('#banTSec').text(varSecond);
				if(varHour <0 || varMinute <0 || varSecond <0){
					clearInterval(i);
					$("#banerTimeOff").html("时间已到期！");
				}
			}
		},1000);
	}
	
	//最后一天倒计时
	function lastDayOff(){
		var now=new Date(),
			endTime=new Date();
			endTime.setUTCDate(now.getUTCDate()+1);
			endTime.setHours(0);
			endTime.setMinutes(0);
			endTime.setSeconds(0);
		var varOff=endTime.valueOf();
		var j=setInterval(function(){
			if(varOff <= now.getTime()){
				clearInterval(j);
				return false;
			}else{
				varSub=Math.floor((varOff - now.getTime())/1000);
				varHour=Math.floor(varSub/3600);
				varMinute=Math.floor((varSub%3600)/60);
				$("#iHours").text(varHour);
				$("#iMiniutes").text(varMinute);
			}
			
		},6000);
	}
		
/**
 * 读取本地cookie
 * @param {type} c_name
 * @returns {String}
 */
function getCookie(c_name) {
    if (document.cookie.length > 0) {
        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1)
                c_end = document.cookie.length;
            return unescape(document.cookie.substring(c_start, c_end))
        }
    }
    return "";
}

/**
 * 格式化数字
 * @param {type} number
 * @param {type} dec
 * @returns {String}
 */
function number_format(number, dec) {
    number = parseFloat(number);
    orig = number;
    if (orig < 1)
        number += 1;
    dec = parseInt(dec);
    number = Math.round(number * Math.pow(10, dec));
    number += '';
    l = number.length;
    int_part = number.substr(0, l - dec);
    if (orig < 1)
        int_part = parseInt(int_part) - 1;
    return int_part + '.' + number.substr(l - dec, dec);

}	
	
	
	