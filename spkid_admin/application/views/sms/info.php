<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>

<script>

var sending = false;

$(function(){
	computerMobileTemplateLength();
});

// check form items' data
function checkValues(){
	if( Utils.$('mobiles').value =='' && Utils.$('excel').value == '' )
	{
		alert('请上传Excel或填写手机号');
		return false;
	}
	return true;
}

//短信模板长度计算
var computerMobileTemplateLength = function(){
    var len = 66-$("#mobile_template").val().length;
    if ( len < 0 ){
        $("#mobile_template").val($("#mobile_template").val().substr(0,66));
        alert('字数太长');
        return false;
    }else {
        $("#mobile_template_length").text(66-$("#mobile_template").val().length);
        return true;
    }
};

    function sendMobileCode(){
        if ( sending ) return ;
        var el = Utils.$('mobile_template');
        if( Utils.trim(el.value) =='')
        {
        alert('请填写短信内容');
        return ;
        }

        if( computerMobileTemplateLength ){
            sending = true;
            Utils.$('send_sms').disabled=true;
			var content_key = $("#mobile_template").val();
            $.ajax({
		            url: '/manual_sms/send',
		            data: {is_ajax:1,content:content_key, rnd : new Date().getTime()},
		            dataType: 'json',
		            type: 'POST',
		            success: function(result){
		            	if (result.error == 1)
		            	{
		            		alert('无效的手机号。');
		            	} else
		            	{
		            		alert('短信发送成功。');
		            	}
		            	sending=false;
				        Utils.$('send_sms').disabled=false;
		            }
		        });

        }
    }

    function local_alert( v ){
        alert(v);
    }

</script>

<div class="main">
  <div class="main_title"><span class="l">促销管理 >> 添加手机号</span>  <a href="manual_sms/sms_list" class="return r">短信发送记录列表</a></div>

  <div class="blank5"></div>
		<form name='fields_frm' method='post' action='manual_sms/add' enctype='multipart/form-data' onsubmit='return checkValues();'>
		<h3>添加手机号</h3>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr><td><b>方法一</b>上传Excel：</td>
			<td>
		        <input type="file" name='excel' id='excel' value='' style=''/>
		        <span>，需要<i><a href='<?php echo $mobile_template; ?>' target='_blank'>下载Excel模板</a></i></span>
		    </td></tr>
		    <tr><td><b>方法二&nbsp;</b>&nbsp;手工输入：
		            <br/>（每个手机号一行）</td>
		    <td>
		    <textarea name='mobiles' style='float:left' id='mobiles' cols=40 rows=10></textarea>
			</td></tr>
		</table>
		<input type='submit' name='submit' value='添加' class='am-btn am-btn-primary'>
		<input type='hidden' name='act' value='add'>
		</form>
		<br />
		<table class="form" cellpadding=0 cellspacing=0>
		    <tr><td><label>短信内容：</label></td>
		    <td><input type="text" id='mobile_template' name='mobile_template' value='' style='width:800px;' onkeyup='computerMobileTemplateLength()'/>
		        <span>剩余字数：</span><span id='mobile_template_length'></span>
		    </td></tr>
		    <tr><td>&nbsp;</td>
		    <td><input value='发送短信' name='send_sms' id='send_sms' type='am-btn am-btn-primary' onclick='sendMobileCode()'>
		    </td></tr>
		</table>
	</div>
<?php include(APPPATH.'views/common/footer.php');?>