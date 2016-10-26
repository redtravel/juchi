<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
    $(function(){
    });

	function check_form(){
		var validator = new Validator('mainForm');
//			validator.required('type_code', '请填写编码');
			validator.required('type_name', '请填写名称');
			validator.required('sort_order', '请填写排序');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title">
        <span class="l">商品前台分类>> <?=$type_id==0?"新增":"修改"?> </span>
        <a href="/product_type" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open('product_type/proc_add/'.$type_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
                <td class="item_title">
                    商品所属大类
                </td>
                <td class="item_input">
                    <?php
                    	if(isset($row->genre_id)){
                    		print form_dropdown('genre_id', get_pair($all_genre,'id','name'),array($row->genre_id),'data-am-selected');
                    	}else{
                    		print form_dropdown('genre_id', get_pair($all_genre,'id','name'),array(),'data-am-selected');
                    	}
                    	
                    ?>
                </td>
            </tr>
			<tr>
				<td class="item_title">上级分类:</td>
				<td class="item_input">
				<select name='first_type' id='first_type'>
				    <option value='0'>一级分类</option>
				    <?
					foreach($first_type as $type):
					    echo "<option value='".$type->type_id."'".
					    ($type->type_id==@$row->parent_id?"selected":"")
					    .">".$type->type_name."</option>";
					endforeach
				    ?>
				</select>
				<select name='second_type' id='second_type'>
				    <option value='0'>二级分类</option>
				    <?
					if(isset($second_type))
					foreach($second_type as $type):
					    echo "<option value='".$type->type_id."'".
					    ($type->type_id==@$row->parent_id2?"selected":"")
					    .">".$type->type_name."</option>";
					endforeach
				    ?>
				</select>
			    </td>
			</tr>
                        <tr>
                            <td class="item_title"> 对应后台分类 </td>
                            <td class="item_input">
<?php print form_product_category('category_id', $all_category, empty($row)?0:$row->category_id, '', array('0'=>'不对应后台分类'));?>
                            </td>
                        </tr>
			<?php if($type_id != 0 ): ?>
			<tr>
				<td class="item_title">编码:</td>
				<td class="item_input">
				    <?php print form_input(array('name'=> 'type_code','value'=>@$row->type_code,'class'=> 'textbox require',"disabled"=>"disabled"));?>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<td class="item_title">名称:</td>
				<td class="item_input">
                    <?php print form_input(array('name'=> 'type_name',
                            'value'=>@$row->type_name,'class'=> 'textbox require'
                    ));?>
				</td>
			</tr>
            <tr>
				<td class="item_title">是否前台显示:</td>
				<td class="item_input">
                    <label><?php print form_radio(array('name'=>'is_show_cat', 'value'=>1,
                                'checked'=>($type_id==0||@$row->is_show_cat==1)?true:false)); ?>是</label>
					<label><?php print form_radio(array('name'=>'is_show_cat', 'value'=>0,
                                'checked'=>($type_id==0||@$row->is_show_cat==1)?false:true)); ?>否</label>
				</td>
			</tr>
            <tr>
				<td class="item_title">排序:</td>
				<td class="item_input">
                    <?php print form_input(array('name'=> 'sort_order',
                            'value'=>@$row->sort_order,'class'=> 'textbox require'
                    ));?>
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
<script>
    $(function(){
       $('select[name=genre_id]').change(function(){
           var first_type=$('#first_type');
           first_type[0].length=1;
           if($(this).val()!=0){
               $.post('/product_type/get_first_type/'+$(this).val(),
                   function(data){
                       for(var i=0;i<data.length;i++){
                           //过滤3级分类
                           if(data[i].parent_id2>0){
                               continue;
                           }
                           first_type.append("<option value='"+data[i].type_id+"'>"+data[i].type_name+"</option>");
                       }
                   },
                   'json');
           }
       });
        $('#first_type').change(function(){
            var second_type=$('#second_type');
            second_type[0].length=1;
            if($(this).val()!=0){
                $.post('/product_type/get_second_type/'+$(this).val(),
                    function(data){
                        for(var i=0;i<data.length;i++){
                            second_type.append("<option value='"+data[i].type_id+"'>"+data[i].type_name+"</option>");
                        }
                    },
                    'json');
                }
            });
        });
</script>
<?php include(APPPATH.'views/common/footer.php');?>
