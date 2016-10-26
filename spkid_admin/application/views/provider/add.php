<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
        //validator.required('provider_code', '请填写供应商代码');
        validator.required('provider_name', '请填写供应商名称');
        validator.required('provider_cooperation', '请选择供应商合作方式');
        validator.required('legal_provider', '请选择法人代表');
        validator.required('sales_name', '请选择销售员');
        validator.required('sales_mobile', '请选择销售员手机号');
		return validator.passed();
	}
	//]]>
</script>
<style>
	.v-aptitude {clear: both;}
	.v-aptitude p {margin-bottom: 3px;}
	.v-aptitude li{ float: left; margin-right: 5px; margin-bottom: 20px;}
	.v-aptitude img {width: 100px;height: 100px;}
</style>
<div class="main">
	<div class="main_title">
		<?php if(!empty($parent)){ ?>
			发货商管理 >> 新增 </span><a href="provider/scm_index/<?php print $parent->provider_id;?>" class="return r">返回列表</a>
		<?php }else{ ?>
			供应商管理 >> 新增 </span><a href="provider/index" class="return r">返回列表</a>
		<?php } ?>	
	</div>
	<div class="blank5"></div>
	<?php print form_open_multipart('provider/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<?php if(!empty($parent)){ ?>
			<tr>
				<td class="item_title">上级供应商:</td>
				<td class="item_input">
					<?php print $parent->provider_name;?>
					<input type="hidden" name="parent_id" value="<?php print $parent->provider_id;?>">
				</td>
			</tr>
			<?php } ?>
			<tr>
				<?php if(!empty($parent)){ ?>
				<td class="item_title">发货商代码:</td>
				<?php }else{ ?>
				<td class="item_title">供应商代码:</td>
				<?php } ?>	
				<td class="item_input"><?php print form_input(array('name'=> 'provider_code','class'=> 'textbox require wd280','placeholder'=>'留空可自动生成'));?></td>
			</tr>
			<tr>
				<?php if(!empty($parent)){ ?>
					<td class="item_title">发货商名称:</td>
				<?php }else{ ?>
					<td class="item_title">供应商名称:</td>
				<?php } ?>
				<td class="item_input"><?php print form_input(array('name'=> 'provider_name','class'=> 'textbox require wd280'));?></td>
			</tr>
			<tr>
				<?php if(!empty($parent)){ ?>
				<td class="item_title">发货商合作方式:</td>
				<?php }else{ ?>
				<td class="item_title">供应商合作方式:</td>
				<?php } ?>
				<td class="item_input"><?php print form_dropdown('provider_cooperation', get_pair($all_cooperation,'cooperation_id','cooperation_name'),2);?></td>
			</tr>
			<tr>
				<td class="item_title">公司名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'official_name','class'=> 'textbox wd280'));?></td>
			</tr>
			<?php if(empty($parent)){ ?>
			<tr>
				<td class="item_title">法人代表:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'legal_provider','class'=> 'textbox require wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">销售员:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'sales_name','class'=> 'textbox require wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">销售员手机号:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'sales_mobile','class'=> 'textbox require wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">资质图片:</td>
				<td class="item_input">
					<div class = "v-aptitude">
						<p>营业执照</p>
						<input type="file" id="aptitude_img1" name="aptitude_img1[]" multiple />
						<ul id="lists1"></ul>
					</div>
					<div class = "v-aptitude">
						<p>医疗器械生产或者经营许可证或者备案凭证</p>
						<input type="file" id="aptitude_img2" name="aptitude_img2[]" multiple />
						<ul id="lists2"></ul>
					</div>
					<div class = "v-aptitude">
						<p>医疗器械注册证或者备案凭证</p>
						<input type="file" id="aptitude_img3" name="aptitude_img3[]" multiple />
						<ul id="lists3"></ul>
					</div>
					<div class = "v-aptitude">
						<p>销售人员身份证复印件，加盖供货者公章的授权书原件。授权书应当载明授权销售的品种、地域、期限，注明销售人员的身份证号码</p>
						<input type="file" id="aptitude_img4" name="aptitude_img4[]" multiple />
						<ul id="lists4"></ul>
					</div>
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td class="item_title">开户银行:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'provider_bank','class'=> 'textbox wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">银行帐号:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'provider_account','class'=> 'textbox wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">纳税号:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'tax_no','class'=> 'textbox wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">状态:</td>
				<td class="item_input">
					<label><?php print form_radio(array('name'=>'is_use', 'value'=>0,'checked'=>TRUE)); ?>禁用</label>
					<label><?php print form_radio(array('name'=>'is_use', 'value'=>1)); ?>启用</label>
				</td>
			</tr>
            <tr>
				<td class="item_title">LOGO图:</td>
				<td class="item_input"><?php print form_upload(array('name'=> 'logo','class'=> 'wd280'));?></td>
			</tr>
            <tr>
				<td class="item_title">前台显示名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'display_name','class'=> 'textbox wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">退货地址:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'return_address','class'=> 'textbox wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">退货邮编:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'return_postcode','class'=> 'textbox wd280'));?></td>
			</tr>
			<tr>
				<td class="item_title">退货收货人:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'return_consignee','class'=> 'textbox wd280'));?></td>
			</tr>
            <tr>
				<td class="item_title">退货收货人手机:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'return_mobile','class'=> 'textbox wd280'));?></td>
			</tr>
            <tr>
				<td class="item_title">短信价格:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'sms_price','class'=> 'textbox wd280'));?></td>
			</tr>

			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<script type="text/javascript">
	function fileSelect(e) {
	    e = e || window.event;
	    var files = e.target.files;  //FileList Objects    
	    var output = [];

	    function funDisposePreviewHtml(file, e) {
	    	var html = "";
	    	html = '<li ><img id="uploadImage_'+file.index+'" class="upload_image" src="' + e.target.result + '" style="width:expression(this.width > '+ '100' +' ? '+'100'+'px : this.width)" /></li>';
	    	return html;
	    }

		var html = '', i = 0;

	    // 组织预览html
	    var funDealtPreviewHtml = function() {
	    	file = files[i];
	    	if (file) {
	    		var reader = new FileReader()
	    		reader.onload = function(e) {
	    			// 处理下配置参数和格式的html
	    			html += funDisposePreviewHtml(file, e);	    			
	    			i++;
	    			// 再接着调用此方法递归组成可以预览的html
	    			funDealtPreviewHtml();
	    		}
	    		reader.readAsDataURL(file);
	    	} else {
	    		// 走到这里说明文件html已经组织完毕，要把html添加到预览区
	    		$('#lists1').html(html);
	    	}
	    };
	    funDealtPreviewHtml();
	}
	if(window.File && window.FileList && window.FileReader && window.Blob) {
	    document.getElementById('aptitude_img1').addEventListener('change', fileSelect, false);
	} else {
	    document.write('您的浏览器不支持File Api');
	}

//
	function fileSelect2(e) {
	    e = e || window.event;
	    var files = e.target.files;  //FileList Objects    
	    var output = [];

	    function funDisposePreviewHtml(file, e) {
	    	var html = "";
	    	html = '<li ><img id="uploadImage_'+file.index+'" class="upload_image" src="' + e.target.result + '" style="width:expression(this.width > '+ '100' +' ? '+'100'+'px : this.width)" /></li>';
	    	return html;
	    }

		var html = '', i = 0;

	    // 组织预览html
	    var funDealtPreviewHtml = function() {
	    	file = files[i];
	    	if (file) {
	    		var reader = new FileReader()
	    		reader.onload = function(e) {
	    			// 处理下配置参数和格式的html
	    			html += funDisposePreviewHtml(file, e);	    			
	    			i++;
	    			// 再接着调用此方法递归组成可以预览的html
	    			funDealtPreviewHtml();
	    		}
	    		reader.readAsDataURL(file);
	    	} else {
	    		// 走到这里说明文件html已经组织完毕，要把html添加到预览区
	    		$('#lists2').html(html);
	    	}
	    };
	    funDealtPreviewHtml();
	}
	if(window.File && window.FileList && window.FileReader && window.Blob) {
	    document.getElementById('aptitude_img2').addEventListener('change', fileSelect2, false);
	} else {
	    document.write('您的浏览器不支持File Api');
	}

//
	function fileSelect3(e) {
	    e = e || window.event;
	    var files = e.target.files;  //FileList Objects    
	    var output = [];

	    function funDisposePreviewHtml(file, e) {
	    	var html = "";
	    	html = '<li ><img id="uploadImage_'+file.index+'" class="upload_image" src="' + e.target.result + '" style="width:expression(this.width > '+ '100' +' ? '+'100'+'px : this.width)" /></li>';
	    	return html;
	    }

		var html = '', i = 0;

	    // 组织预览html
	    var funDealtPreviewHtml = function() {
	    	file = files[i];
	    	if (file) {
	    		var reader = new FileReader()
	    		reader.onload = function(e) {
	    			// 处理下配置参数和格式的html
	    			html += funDisposePreviewHtml(file, e);	    			
	    			i++;
	    			// 再接着调用此方法递归组成可以预览的html
	    			funDealtPreviewHtml();
	    		}
	    		reader.readAsDataURL(file);
	    	} else {
	    		// 走到这里说明文件html已经组织完毕，要把html添加到预览区
	    		$('#lists3').html(html);
	    	}
	    };
	    funDealtPreviewHtml();
	}
	if(window.File && window.FileList && window.FileReader && window.Blob) {
	    document.getElementById('aptitude_img3').addEventListener('change', fileSelect3, false);
	} else {
	    document.write('您的浏览器不支持File Api');
	}

// 
    function fileSelect4(e) {
	    e = e || window.event;
	    var files = e.target.files;  //FileList Objects    
	    var output = [];

	    function funDisposePreviewHtml(file, e) {
	    	var html = "";
	    	html = '<li ><img id="uploadImage_'+file.index+'" class="upload_image" src="' + e.target.result + '" style="width:expression(this.width > '+ '100' +' ? '+'100'+'px : this.width)" /></li>';
	    	return html;
	    }

		var html = '', i = 0;

	    // 组织预览html
	    var funDealtPreviewHtml = function() {
	    	file = files[i];
	    	if (file) {
	    		var reader = new FileReader()
	    		reader.onload = function(e) {
	    			// 处理下配置参数和格式的html
	    			html += funDisposePreviewHtml(file, e);	    			
	    			i++;
	    			// 再接着调用此方法递归组成可以预览的html
	    			funDealtPreviewHtml();
	    		}
	    		reader.readAsDataURL(file);
	    	} else {
	    		// 走到这里说明文件html已经组织完毕，要把html添加到预览区
	    		$('#lists4').html(html);
	    	}
	    };
	    funDealtPreviewHtml();
	}
	if(window.File && window.FileList && window.FileReader && window.Blob) {
	    document.getElementById('aptitude_img4').addEventListener('change', fileSelect4, false);
	} else {
	    document.write('您的浏览器不支持File Api');
	}

</script> 
<?php include(APPPATH.'views/common/footer.php');?>
