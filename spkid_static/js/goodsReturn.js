function focusInput(obj){
	if(obj){
		var text=obj.val();
		if(text!=null){
			obj.focus(function(){
				if(obj.val()==text){
					obj.val('');
				}
			});
			obj.blur(function(){
				if(obj.val()=='' || obj.val()==text){
					obj.val(text);
				}
			});
		}
	}
}
$(function(){
	// 得到焦点和失去焦点的交互
	var expId=$('#expressNumber');
	focusInput(expId);
	var userNameInp=$('#userNameInp');
	focusInput(userNameInp);
	var userPhone=$('#userPhone');
	focusInput(userPhone);
	var userTel=$('#userTel');
	focusInput(userTel);	
	var expressMoney=$('#expressMoney');
	focusInput(expressMoney);
	// 上传图片及其相关操作
	$('.checkBox').click(function(){
		if(this.checked==true){
			
			$(this).parent().siblings('.problemHide').show();
			focusInput($(this));
		}else{
			$(this).parent().siblings('.problemHide').hide();
		}
	})
	var text='请您详细的说明您有问题的商品，这样方面我们能够更加直接迅速的处理您的退货';
	//$("input[type='text']").bind({
	$('.problemInt').bind({
		focus:function(){
			//text=$(this).val();
			if(text!=null){
				if($(this).val()==text){
					$(this).val('');
				}
			}
		},
		blur:function(){
			if($(this).val()=='' || $(this).val()==text){
				$(this).val(text);
			}
		}
	})
})

// 上传图片判断
$('.imgUploadBtn').click(function() {
    var upload_flag=false,file_input="";
    var uploaded=this.parentNode.children[9].getAttribute('value');
    if(uploaded>=5){
        alert('您已上传5张图片');                                                                                                                                                
        return;
    }
    for(var j=0;j<5;j++){
        var file_input=this.parentNode.children[j+3];
        if(file_input.value==""){
            upload_flag=true;
            break;
        }
        file_input="";
    }
    if(upload_flag){
        file_input.click();
    }else{
        alert('您已上传5张图片');
    }

})

function previewImage(file) {
    var file_input_name=file.id;
	var div = file.parentNode.children[9],
		n = parseInt(div.getAttribute('value')),
		tex = div.innerHTML;
	if (n < 5) {
		tex += '<div class="imgBlock"><img width="23" height="26" onmouseover="orgImgShow(this,this.src)" onmouseout="orgImgHide(this)">'+
                '<div onclick="removeImg(this)" id="div-'+file_input_name+'">删除</div></div>';
		if (file.files && file.files[0]) {
			div.innerHTML = tex;
			var img = div.children[n].children[0];
			var reader = new FileReader();
			reader.onload = function(evt) {
				img.src = evt.target.result;
			}
			reader.readAsDataURL(file.files[0]);
		} else {
			var sFilter = 'filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="';
			file.select();
			var src = document.selection.createRange().text;
			div.innerHTML = tex;
			var img = div.children[n].children[0];
			img.src = src;
		}
		div.setAttribute('value', ++n);
	} else {alert('最多只能传5张图')}
}
// 删除当前图片
function removeImg(img) {
    var file_id=img.id.substring(4);
	var div = img.parentNode.parentNode.parentNode.children[9];
	var n = parseInt(div.getAttribute('value'));
	//alert(div.outerHTML);
	img.parentNode.remove();
	div.setAttribute('value', --n);
    //同时将input file value置为空
    $('#'+file_id).val("");
}
// 显示/隐藏图片预览
function orgImgShow(img, src) {
	var div = img.parentNode.parentNode.parentNode.children[10];
	div.style.display = "block";
	div.innerHTML = '<img>';
	var image = div.children[0];
	image.style.display = "block";
	image.src = src;
}
function orgImgHide(img) {
	var div = img.parentNode.parentNode.parentNode.children[10];
	div.style.display = "none";
	div.innerHTML = "";
}


var chx = $('.checkBox'),
	add = $('.addBtn'),
	min = $('.minBtn'),
	divNum = $('.divSelectNum'),
	chxL = chx.length;
for (var i = 0; i < chxL; i++) {
	// 数量加减
	add[i].onclick = function() {
        var max_num=this.parentNode.getAttribute('max_num');
        var input_num=this.parentNode.children[1];
		var v = parseInt(input_num.value);
        if(v<max_num){
		    input_num.value= (++v);
        }
	}
	min[i].onclick = function() {
        var input_num=this.parentNode.children[1];
		var v = parseInt(input_num.value);
		if (v > 1) input_num.value= (--v);
	}
}

// 输入内容交互
var yzInput = $('.yanzheng');
for (var i = 0; i < yzInput.length; i++) {
	yzInput[i].onfocus = function() {
		var valN = this.getAttribute('val');
		if (this.value == valN) {
			this.value = ''
		}
	};
	yzInput[i].onblur = function() {
		var valN = this.getAttribute('val');
		if (this.value == '') {
			this.value = valN
		}
	};
}
function shipping_change(obj){
	if(obj.value=='-1'){
		$('#expressName').show();
	}else{
		$('#expressName').hide();
	}

}
// 提交判断
var enNumReg = /^[A-Za-z0-9]+$/,
	mobileReg = /^1[3|5|8]\d{9}$/,
	telRegExp = /(^(\d{3,4}-?)?\d{7,8})$/;
function enterBtn(){
    // 验证是否选择了商品
    var chk_goods=document.getElementsByName('chk_goods[]');
    var select_flag=false;
    for(var i=0;i<chk_goods.length;i++){
        if(chk_goods[i].checked){
            select_flag=true;
            break;
        }
    }
    if(!select_flag){
        alert('请先选择商品');
        return false;
    }
	if($("#sel_shipping").val()=="0"){
		$('#msg1').html('请选择快递公司!');
		return false;
	}
	// 验证输入
	if ($('#expressName').attr('display') == 'block' && $('#expressName').val() == '输入快递名称') {
		$('#msg1').html('快递不能为空!');
		return false;
	}
	if ($('#expressNumber').val() == '输入快递运单号') {
		$('#msg1').html('运单号不能为空!');
		return false;
	}
	if ($('#expressMoney').val() == '输入运费') {
		$('#msg1').html('运费不能为空!');
		return false;
	}
    if(isNaN($('#expressMoney').val())){
		$('#msg1').html('请输入正确的运费!');
		return false;
    }
    //运费金额验证
    var sp_address=['江苏','浙江','上海'];
    if(sp_address.indexOf(user_province) >= 0){
        if($('#expressMoney').val()>10){
		    $('#msg1').html('江浙沪最高运费为10元!');
		    return false;
        }
    }
    if($('#expressMoney').val()>15){
	    $('#msg1').html('最高运费为15元!');
	    return false;
    }
	if (!enNumReg.test($("#expressNumber").val())) {
		$('#msg1').html('请填写正确的运单号!');
		return false;
	}
	//不允许输入订单号到运单号上
	if($("#expressNumber").val()==order_sn){
	    $('#msg1').html('亲，貌似运单号写成了订单号哦!');
		return false;
	}
	$('#msg1').html('');
	if ($('#userNameInp').val() == '输入联系人姓名') {
		$('#msg2').html('联系人姓名不能为空!');
		return false;
	}
	 $('#msg2').html('');
	// 验证手机/电话输入
	if ($('#userPhone').val() == '输入手机号码' && $('#userTel').val() == '输入电话号码') {
		$('#msg3').html('手机电话至少填一项!');
		$('#msg4').html('手机电话至少填一项!');
		return false;
	}
	if ($('#userPhone').val() != '输入手机号码' && $('#userPhone').val()!=''){ 
        if(!mobileReg.test($('#userPhone').val())) {
            $('#msg3').html('请填写正确的手机号码!');
            $('#msg4').html('');
            return false;
        }
	}
	 $('#msg3').html('');
	if ($('#userTel').val() != '输入电话号码'&& $('#userTel').val() !=''){ 
        if(!telRegExp.test($('#userTel').val()) || $('#userTel').val().length < 7 || $('#userTel').val().length > 13) {
            $('#msg4').html('请填写正确的电话号码!');
            $('#msg3').html('');
            return false;
        }
	}
	 $('#msg4').html('');
    /*
	if (uPhone.value != '输入手机号码' && uTel.value != '输入电话号码') {
		if (!mobileReg.test(uPhone.value)) {
			msg3.innerHTML = '请填写正确的手机号码!';
		} else {
			msg3.innerHTML = '';
		}
		if (!telRegExp.test(uTel.value)) {
			msg4.innerHTML = '请填写正确的电话号码!';
		} else {
			msg4.innerHTML = '';
		}
		return false;
	}
    */
    if ($('#userPhone').val() == '输入手机号码'){
        $('#userPhone').val()="";
    }
    if($('#userTel').val() == '输入电话号码'){
        $('#userTel').val("");
    }
    $('#form_apply_return').submit();
}
