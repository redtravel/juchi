<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script src="public/js/My97DatePicker/WdatePicker.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		cooment_type();
		load_pro();
	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.selected('tag_type', '请选择关联类型');
			validator.selected('tag_id', '请选择对应商品');
			validator.selected('comment_type', '请选择评论类型');
			validator.required('comment_content', '请填写留言内容');
			return validator.passed();
	}
	
	function cooment_type(){
		com=$(':input[name=comment_type]').val();
		if(com == '1'){
			$('input[name=height]').attr('disabled','disabled');
			$('input[name=weight]').attr('disabled','disabled');
			$('select[name=size_id]').attr('disabled','disabled');
			$('input[name=suitable]').attr('disabled','disabled');
			return false;			
		}
		$('input[name=height]').removeAttr('disabled','disabled');
		$('input[name=weight]').removeAttr('disabled','disabled');
		$('select[name=size_id]').removeAttr('disabled','disabled');
		$('input[name=suitable]').removeAttr('disabled','disabled');
	}
	
	function load_pro () {
		$('#pro_name').html('');
		var tag_id=$.trim($(':input[name=tag_id]').val());
		tag_id=parseInt(tag_id);
		var tag_type=$(':input[name=tag_type]').val()==2?2:1;
		if(isNaN(tag_id)||tag_id<1) return false;
		$('#pro_name').html('查询中...');
		$.ajax({
			url:'liuyan/load_product',
			data:{tag_id:tag_id,tag_type:tag_type,rnd:new Date().getTime()},
			dataType:'json',
			type:'POST',
			success:function(result){
				$('#pro_name').html('');
				if(result.msg) $('#pro_name').html(result.msg);
				if(result.err) return false;
				if(result.html) $('#pro_name').html(result.html);
			}
		});
	}

	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">留言管理 >> 新增</span> <a href="liuyan/index" class="return r">返回列表</a></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('liuyan/proc_add/',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
			  <td class="item_title">评论类型</td>
			  <td class="item_input">
			  <?php print form_dropdown('comment_type',$this->comment_type,'','id="comment_type" onchange="cooment_type();"'); ?></td>
		  </tr>
            
            <tr>
				<td width="96" class="item_title">关联类型:</td>
				<td class="item_input">
				<?php print form_dropdown('tag_type',$this->tag_type,$tag_type,'id="tag_type" onchange="load_pro()"'); ?>
	            </td>
			</tr>
			<tr>
				<td class="item_title">tagID:</td>
				<td class="item_input">
                <input name="tag_id" type="text" id="tag_id" value="<?php print $tag_id?$tag_id:'' ?>" size="8" onblur="load_pro();"  />
                <span id="pro_name"></span>
                </td>
			</tr>
            
			<tr>
			  <td class="item_title">评论内容:</td>
			  <td class="item_input"><textarea name="comment_content" cols="60" rows="5" id="comment_content"></textarea></td>
		  </tr>
		  <tr>
			  <td class="item_title">星级:</td>
			  <td class="item_input"><input name="grade" type="text" id="grade" value="" size="10"  /></td>
		  </tr>
			<tr>
			  <td class="item_title">身高(cm):</td>
			  <td class="item_input"><input name="height" type="text" id="height" value="" size="60"  /></td>
		  </tr>
			<tr>
			  <td class="item_title">体重(kg):</td>
			  <td class="item_input"><input name="weight" type="text" id="weight" value="" size="60"  /></td>
		  </tr>
			<tr>
			  <td class="item_title">尺码:</td>
			  <td class="item_input"><select name="size_id" id="size_id">
			    <option>--请选择--</option>
                <?php foreach($size_arr as $item):?>
			    <option value="<?php echo $item->size_id?>"><?php echo $item->size_name;?></option>
                <?php endforeach;?>
		      </select>
              </td>
		  </tr>
			<tr>
			  <td class="item_title">尺码感受:</td>
			  <td class="item_input">
			      <p>
			          <input type="radio" name="suitable" value="1" id="suitable" />
			          偏小
			          <input type="radio" checked="checked" name="suitable" value="2" id="suitable" />
			          正好
    		        <input type="radio" name="suitable" value="3" id="suitable" />
			        偏大
			</td>
		  </tr>
			<tr>
			  <td class="item_title">用户名:</td>
			  <td class="item_input"><input name="user_name" type="text" id="user_name" value="" size="60"  /></td>
		  </tr>
		  	<tr>
		  		<td class="item_title">评价时间:</td>
		  		<td>
                    <input type="text" value="" name="comment_date" id="comment_date" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})">
                </td>
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
<?php include(APPPATH.'views/common/footer.php');?>