var Sq_getId=function(a){return document.getElementById(a)},Sq_bind=function(a,c,b){if(a.addEventListener){a.addEventListener(c,b,false)}else{if(a.attachEvent){a.attachEvent("on"+c,b)}else{a["on"+c]=b}}},Sq_bind=function(a,c,b){if(a.addEventListener){a.addEventListener(c,b,false)}else{if(a.attachEvent){a.attachEvent("on"+c,b)}else{a["on"+c]=b}}},Sq_unbind=function(a,c,b){if(a.removeEventListener){a.removeEventListener(c,b,false)}else{if(a.detachEvent){a.detachEvent("on"+c,b)}else{a["on"+c]=null}}},Sq_remove=function(a){if(a.parentNode){a.parentNode.removeChild(a)}},Sq_stop=function(a){a=a||window.event;a.stopPropagation&&(a.preventDefault(),a.stopPropagation())||(a.cancelBubble=true,a.returnValue=false)},Sq_offset=function(d){var b=d.offsetTop,c=d.offsetHeight,a=d.offsetLeft;while(d=d.offsetParent){b+=d.offsetTop;a+=d.offsetLeft}return{top:b,height:c,left:a}},isIE7=function(){var a=navigator.userAgent.indexOf("MSIE 7.0")>-1;return a},isIE6=function(){var a=navigator.userAgent.indexOf("MSIE 6.0")>-1;return a};function creatSequence(P){var q=P.ID,O=P.speed,L=P.offset,c=P.tagsName;if(O==null){O=20}if(O>50){O=50}L=L==null?100:L;var b,Y,s=document.body,f=document.documentElement,G,a=Sq_getId(q),v=0,n=a.children.length,I=1,u=a.offsetWidth,B=a.offsetHeight,E=a.children[0].offsetWidth,S=a.children[0].offsetHeight,k=Math.floor(a.offsetWidth/E),p,m,z,y,D,C,A,N,R,M,K,V,J,H,r,T=0,W=document.createElement("li");document.createAttribute("drag","");W.innerHTML="<div></div>";W.id="replace";var e=function(h){b=null;b=new Array();for(var l=0;l<h.length;l++){b.push(h[l])}return b},X=function(ab,aa,o){try{var l=a.children;var h=l.length;var t;r=h;for(var w=ab;w<aa;w++){t=w;l[t].getElementsByTagName(c)[0].innerHTML=parseInt(t+o)}}catch(Z){return}},g=function(l,i){Y=l.getElementsByTagName(c)[0].innerHTML;if(Y!=""){G=f.clientHeight;R=Sq_offset(l).left;M=Sq_offset(l).top;K=l.offsetWidth;V=l.offsetHeight;var i=i||window.event,h=s.scrollTop||f.scrollTop;z=i.clientX-R;y=i.clientY+h-M;T=Y;v=T;l.setAttribute("drag","on");if(document.all){l.setCapture()}Sq_stop(i)}},Q=function(x,w){if(x.getAttribute("drag")=="on"){var w=w||window.event,i=s.scrollTop||f.scrollTop;D=w.clientX-z;C=w.clientY+i-y;var t=i+G-V;if(C<i-L&&i>=0){s.scrollTop=i-O;f.scrollTop=i-O}if(C>i+G-V+L&&C<s.offsetHeight-V){s.scrollTop=i+O;f.scrollTop=i+O}x.style.position="absolute";x.style.left=D+"px";x.style.top=C+"px";x.style.zIndex=1000;J=w.clientX-z-R;H=w.clientY+i-y-M;var o,h=Math.ceil(b.length),Z=Math.ceil(Y/k),l=Math.ceil(h/k);o=Y-(Z-1)*k;if(J<-(K*o)+K){J=-(K*o)+K}if(J>K*(k-o)){J=K*(k-o)}if(H<-(V*Z)+V){H=-(V*Z)+V}if(H>V*(l-Z)){H=V*(l-Z)}r=Math.round(H/V)*k+Math.round(J/K)+parseInt(Y)-1;r=r>=Y?r+1:r;n=r;if(T!=r){if(Sq_getId("replace")!=null){Sq_remove(Sq_getId("replace"))}if(r<h){a.insertBefore(W,a.children[r])}else{a.appendChild(W)}}T=r}},j=function(i,h){if(Sq_getId("replace")!=null){i.getElementsByTagName(c)[0].innerHTML=n+1;a.replaceChild(i,Sq_getId("replace"));i.style.position="";if(v<n){X(parseInt(v)-1,parseInt(n),1)}if(v>n){X(parseInt(n),parseInt(v),1)}}i.setAttribute("drag","off");i.style.zIndex=900;if(document.all){i.releaseCapture()}},F=function(Z,x){var w=Z.parentNode;v=parseInt(w.getElementsByTagName("span")[0].innerHTML);if(Sq_getId("inputNumber")==null){var t=document.createElement("input"),l=document.createElement("div"),i=f.offsetWidth,h=f.offsetHeight;t.id="inputNumber";t.type="text";t.value=v;t.style.left=R+10+"px";t.style.top=M+10+"px";l.id="Sq_mask";l.style.width=i+"px";l.style.height=h+"px";a.parentNode.appendChild(l);a.parentNode.appendChild(t);var o=a.parentNode.getElementsByTagName("input")[0];w.getElementsByTagName("span")[0].style.display="none";Sq_getId("inputNumber").focus();Sq_getId("inputNumber").onblur=function(ab){var aa=ab;d(this,aa)}}},d=function(l,i){var h=l.parentNode;n=parseInt(l.value);n=n>a.children.length?a.children.length:n;n=isNaN(n)?v:n;a.children[v-1].getElementsByTagName("span")[0].innerHTML=n;a.children[v-1].getElementsByTagName("span")[0].style.display="block";Sq_remove(l);Sq_remove(Sq_getId("Sq_mask"));if(n<a.children.length){if(v<n){a.insertBefore(a.children[v-1],a.children[n]);X(v-1,n,1)}if(v>n){a.insertBefore(a.children[v-1],a.children[n-1]);X(n,v+1,1)}}else{if(n==a.children.length){a.children[v-1].getElementsByTagName("span")[0].innerHTML=n+1;a.appendChild(a.children[v-1]);X(v-1,n,1)}}};Sq_bind(window,"load",function(){X(v,n,I)});e(a.children);for(var U=0;U<b.length;U++){b[U].onmousedown=function(i){var h=i;g(this,h)};b[U].onmousemove=function(i){var h=i;Q(this,h)};b[U].onmouseout=function(i){var h=i;Q(this,h)};b[U].onmouseup=function(i){var h=i;j(this,h)};b[U].getElementsByTagName("span")[0].onclick=function(i){var h=i;F(this,h)}}};