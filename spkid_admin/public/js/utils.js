/**
 * 工具类Utils和浏览器判断类Browser
 */

var Browser = new Object();

Browser.isMozilla = (typeof document.implementation != 'undefined') && (typeof document.implementation.createDocument != 'undefined') && (typeof HTMLDocument != 'undefined');
Browser.isIE = window.ActiveXObject ? true : false;
Browser.isFirefox = (navigator.userAgent.toLowerCase().indexOf("firefox") != - 1);
Browser.isSafari = (navigator.userAgent.toLowerCase().indexOf("safari") != - 1);
Browser.isOpera = (navigator.userAgent.toLowerCase().indexOf("opera") != - 1);

var Utils = new Object();

Utils.htmlEncode = function(text)
{
    return text.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

Utils.trim = function( text )
{
    if (typeof(text) == "string")
    {
        return text.replace(/^\s*|\s*$/g, "");
    }
    else
    {
        return text;
    }
}

Utils.isEmpty = function( val )
{
    switch (typeof(val))
    {
        case 'string':
            return Utils.trim(val).length == 0 ? true : false;
            break;
        case 'number':
            return val == 0;
            break;
        case 'object':
            return val == null;
            break;
        case 'array':
            return val.length == 0;
            break;
        default:
            return true;
    }
}

Utils.isNumber = function(val)
{
    var reg = /^[\d|\.|,]+$/;
    return reg.test(val);
}

Utils.isInt = function(val)
{
    if (val == "")
    {
        return false;
    }
    var reg = /\D+/;
    return !reg.test(val);
}

Utils.isEmail = function( email )
{
    var reg1 = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/;

    return reg1.test( email );
}

Utils.isTel = function ( tel )
{
    var reg = /^[\d|\-|\s|\_]+$/; //只允许使用数字-空格等

    return reg.test( tel );
}

Utils.fixEvent = function(e)
{
    var evt = (typeof e == "undefined") ? window.event : e;
    return evt;
}

Utils.srcElement = function(e)
{
    if (typeof e == "undefined") e = window.event;
    var src = document.all ? e.srcElement : e.target;

    return src;
}

Utils.isTime = function(val)
{
    var reg = /^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}$/;

    return reg.test(val);
}

Utils.x = function(e)
{ //当前鼠标X坐标
    return Browser.isIE?event.x + document.documentElement.scrollLeft - 2:e.pageX;
}

Utils.y = function(e)
{ //当前鼠标Y坐标
    return Browser.isIE?event.y + document.documentElement.scrollTop - 2:e.pageY;
}

Utils.request = function(url, item)
{
    var sValue=url.match(new RegExp("[\?\&]"+item+"=([^\&]*)(\&?)","i"));
    return sValue?sValue[1]:sValue;
}

Utils.$ = function(name)
{
    return document.getElementById(name);
}

function rowindex(tr)
{
    if (Browser.isIE)
    {
        return tr.rowIndex;
    }
    else
    {
        table = tr.parentNode.parentNode;
        for (i = 0; i < table.rows.length; i ++ )
        {
            if (table.rows[i] == tr)
            {
                return i;
            }
        }
    }
}

document.getCookie = function(sName)
{
    // cookies are separated by semicolons
    var aCookie = document.cookie.split("; ");
    for (var i=0; i < aCookie.length; i++)
    {
        // a name/value pair (a crumb) is separated by an equal sign
        var aCrumb = aCookie[i].split("=");
        if (sName == aCrumb[0])
            return decodeURIComponent(aCrumb[1]);
    }

    // a cookie with the requested name does not exist
    return null;
}

document.setCookie = function(sName, sValue, sExpires)
{
    var sCookie = sName + "=" + encodeURIComponent(sValue);
    if (sExpires != null)
    {
        sCookie += "; expires=" + sExpires;
    }

    document.cookie = sCookie;
}

document.removeCookie = function(sName,sValue)
{
    document.cookie = sName + "=; expires=Fri, 31 Dec 1999 23:59:59 GMT;";
}

function getPosition(o)
{
    var t = o.offsetTop;
    var l = o.offsetLeft;
    while(o = o.offsetParent)
    {
        t += o.offsetTop;
        l += o.offsetLeft;
    }
    var pos = {
        top:t,
        left:l
    };
    return pos;
}

function cleanWhitespace(element)
{
    var element = element;
    for (var i = 0; i < element.childNodes.length; i++) {
        var node = element.childNodes[i];
        if (node.nodeType == 3 && !/\S/.test(node.nodeValue))
            element.removeChild(node);
    }
}

function setCurrent(v)
{
    v = parseFloat(v);
    if(isNaN(v)) return 'invalid value';
    v = Math.round(v*100)/100 + '';
    v=v.replace(/^(\d*)$/,"$1.");
    v=(v+"00").replace(/(\d*\.\d\d)\d*/,"$1");
    v=v.replace(".",",");
    var re=/(\d)(\d{3},)/;
    while(re.test(v))
        v=v.replace(re,"$1,$2");
    v=v.replace(/,(\d\d)$/,".$1");
    return v.replace(/^\./,"0.")
}

function setPercent(v)
{
    v = parseFloat(v);
    if(isNaN(v)) return 'invalid value';
    v = Math.round(v*10000)/100 + '';
    v=v.replace(/^(\d*)$/,"$1.");
    v=(v+"00").replace(/(\d*\.\d\d)\d*/,"$1");
    return v+'%';
}

function do_delete (obj) {
    if(!confirm('确定执行该操作吗?')) return false;
    var obj = $(obj);
    var url = $('base').attr('href')+obj.attr('rel');
    //$.ajax({
    //    url:url,
    //    data:{test:true,rnd:new Date().getTime()},
    //    dataType:'json',
    //    type:'POST',
    //    success:function (result) {
    //        if(result.msg){alert(result.msg)}
    //        if(result.err){return false}
    //        if(!confirm('确定执行该操作吗?')) return false;
            $.ajax({
                url:url,
                data:{rnd:new Date().getTime()},
                dataType:'json',
                type:'POST',
                success:function (result) {
                    if(result.msg && result.err){alert(result.msg)}
                    if(result.err){return false}
                    obj.parents('tr.row').remove();
                }
            });
    //    }
    //});
}


function redirect(url){
    if (!/*@cc_on!@*/0) {       
	window.open(url,'_blank');        
    } else {
	var a = document.createElement('a');
	a.href = url;
	a.target = '_blank';
	document.body.appendChild(a);
	a.click();
    }
}