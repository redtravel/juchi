function alert_msg(str, callback)
{
    var dvMsg = document.createElement("div");
    dvMsg.style.position = "absolute";
    dvMsg.setAttribute('id', 'msg');
    dvMsg.style.width = "400px";
    dvMsg.style.height = "100px";
    dvMsg.style.top = "40%";
    dvMsg.style.left = "30%";
    dvMsg.style.background = "white";
    dvMsg.style.border = "1px solid #6699dd";
    dvMsg.style.zIndex = "1112";
    document.body.appendChild(dvMsg);
    var title = document.createElement("div");
    title.style.position = "absolute";
    title.setAttribute('id', 'title');
    title.style.width = "400px";
    title.style.height = "20px";
    title.style.top = "0";
    title.style.filter = "progid:DXImageTransform.Microsoft.Alpha(style=1,opacity=100,finishOpacity=100%)";
    title.style.zIndex = "1113";
    title.innerHTML = "<font size='2'>提示</font>";
    title.style.background = "#6699ff";
    var imgErr = document.createElement("img");
    imgErr.src = "public/style/img/error.gif";
    imgErr.style.marginLeft = "15px";
    imgErr.style.marginTop = "30px";
    imgErr.style.height = "30px";
    imgErr.style.position = "absolute";
    var imgClo = document.createElement("img");
    imgClo.src = "public/images/no.gif";
    imgClo.style.marginLeft = "378px";
    imgClo.style.marginTop = "0px";
    imgClo.style.position = "absolute";
    imgClo.style.zIndex = "1115";
    imgClo.style.cursor = "hand";
    imgClo.onclick = function()
    {
        document.body.removeChild(newMask);
        document.body.removeChild(dvMsg);
        callback();
    }
    var btn = document.createElement("input");
    btn.id = "ok";
    btn.type = "button";
    btn.value = "确   定";
    btn.style.marginTop = "75px";
    btn.style.marginLeft = "43%";
    btn.style.position = "absolute";
    btn.style.border = "1px solid #6699ff";
    btn.style.background = "lightblue";
    btn.onclick = function()
    {
        document.body.removeChild(newMask);
        document.body.removeChild(dvMsg);
        callback();
    }
    btn.onkeydown = function()
    {
        if (event.keyCode == "13")
        {
            return false;
        }
        if (event.keyCode == "27")
        {
            document.body.removeChild(newMask);
            document.body.removeChild(dvMsg);
            callback();
        }
    }
    var msg = document.createElement("div");
    msg.style.marginTop = "30px";
    msg.style.marginLeft = "18%";
    msg.style.position = "absolute";
    msg.style.width = "300px";
    msg.innerHTML = str;
    msg.style.wordWrap = "break-word";
    document.getElementById('msg').appendChild(msg);
    document.getElementById('msg').appendChild(btn);
    document.getElementById('msg').appendChild(imgErr);
    document.getElementById('msg').appendChild(imgClo);
    document.getElementById('msg').appendChild(title);
    document.getElementById('ok').focus();

    var newMaskID = "bg";  //遮罩层id  
    var newMaskWidth = document.body.scrollWidth;//遮罩层宽度  
    var newMaskHeight = document.body.scrollHeight;//遮罩层高度      
    //mask遮罩层    
    var newMask = document.createElement("div");//创建遮罩层  
    newMask.id = newMaskID;//设置遮罩层id  
    newMask.style.position = "absolute";//遮罩层位置  
    newMask.style.zIndex = "1";//遮罩层zIndex  
    newMask.style.width = newMaskWidth + "px";//设置遮罩层宽度  
    newMask.style.height = newMaskHeight + "px";//设置遮罩层高度  
    newMask.style.top = "0px";//设置遮罩层于上边距离  
    newMask.style.left = "0px";//设置遮罩层左边距离  
    newMask.style.background = "red";//#33393C//遮罩层背景色  
    newMask.style.filter = "alpha(opacity=40)";//遮罩层透明度IE  
    newMask.style.opacity = "0.40";//遮罩层透明度FF  
    document.body.appendChild(newMask);//遮罩层添加到DOM中 

}