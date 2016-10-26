<?php if($full_page): ?>
<?php include(APPPATH . 'views/common/header.php'); ?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/cluetip.js"></script>
<script type="text/javascript" src="public/js/lhgdialog/lhgdialog.js"></script>
<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
<script type="text/javascript">
    //<![CDATA[
	listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
	listTable.filter.page = '<?php echo $filter['page']; ?>';
	listTable.url = 'product_type_link/index';
	function search(){ 
		listTable.filter['product_sn'] = $.trim($('input[type=text][name=product_sn]').val());
		listTable.filter['product_name'] = $.trim($('input[type=text][name=product_name]').val());
		listTable.filter['category_id'] = $.trim($('select[name=category_id]').val());
		listTable.filter['first_type'] = $.trim($('select[name=first_type]').val());
		listTable.filter['second_type'] = $.trim($('select[name=second_type]').val());
		listTable.filter['three_type'] = $.trim($('select[name=three_type]').val());
		listTable.filter['brand_id'] = $.trim($('select[name=brand_id]').val());
		listTable.filter['product_sex'] = $.trim($('select[name=product_sex]').val());
		listTable.filter['skip_set'] = $.trim($('input[type=checkbox][name=skip_set]:checked').val());
		listTable.loadList();
	}
	
	function show_product_type(product_id){
		$.ajax({
		    url: 'product_type_link/pre_set_type',
		    data: {product_id:product_id, rnd : new Date().getTime()},
		    dataType: 'json',
		    type: 'POST',
		    success: function(result){
			if(result.error == 0)
			{
			    var content = result.content;
			    var dg = new $.dialog({ id:'thepanel',height:450,width:700,maxBtn:false, title:'设置前台分类',iconTitle:false,cover:true,html: content});
			    dg.ShowDialog();
			    dg.addBtn('od','确定',function(){
				set_type(function(){search();dg.cancel();});
			    },'left');
			}
		    }
		 });
	}
	
	function set_type(callback){
	    var product_id = $("#product_id").val();
	    var chk_value =[];    
	    $('input[name="type_ids"]:checked').each(function(){
	     chk_value.push($(this).val());    
	    });
	   $.ajax({
		    url: 'product_type_link/set_type',
		    data: {product_id : product_id ,type_ids : chk_value, rnd : new Date().getTime()},
		    dataType: 'json',
		    type: 'POST',
		    success: function(){
			callback();
		    }
	   });
	}
	$(function(){
            $('#first_type').change(function(){
                var second_type=$('#second_type');
		var three_type=$('#three_type');
                second_type[0].length=1;
		        three_type[0].length=1;
                if($(this).val()!=0){
                    $.post('/product_type/get_second_type/'+$(this).val(),
                        function(data){
                            for(var i=0;i<data.length;i++){
                                //过滤3级分类
                                if(data[i].parent_id2>0){
                                    continue;
                                }
                                var option="<option value="+data[i].type_id+">"+data[i].type_name+"</option>";
                                second_type.append(option);
				three_type[0].length=1;
                            }
                        },
                        'json');
                }
            });
	    $('#second_type').change(function(){
                var three_type=$('#three_type');
                three_type[0].length=1;
                if($(this).val()!=0){
                    $.post('/product_type/get_three_type/'+$(this).val(),
                        function(data){
                            for(var i=0;i<data.length;i++){
                                var option="<option value="+data[i].type_id+">"+data[i].type_name+"</option>";
                                three_type.append(option);
                            }
                        },
                        'json');
                }
            });
        });
    //]]>
</script>
<div class="main">
    <div class="main_title">
	<span class="l">商品前台分类关联</span>
    </div>

    <div class="blank5"></div>
    <div class="search_row">
	<form name="search" action="javascript:search(); ">
	<select name="category_id">
		<option value="">后台分类</option>
		<?php foreach($all_category as $category) print "<option value='{$category->category_id}'>{$category->level_space}{$category->category_name}</option>"?>
	</select>
	 <select name='first_type' id='first_type'>
	    <option value='0'>一级分类</option>
	    <?
		foreach($first_type as $type):
		    echo "<option value=".$type->type_id.">".$type->type_name."</option>";
		endforeach
	    ?>
	</select>
	<select name='second_type' id='second_type'>
	    <option value='0'>二级分类</option>
	</select>
	<select name='three_type' id='three_type'>
	    <option value='0'>三级分类</option>
	</select>
	<select name="product_sex">
		<option value="">性别</option>
		<option value="1">男</option>
		<option value="2">女</option>
		<option value="3">男女</option>
	</select>
	<?php print form_dropdown('brand_id',get_pair($all_brand,'brand_id','brand_name', array(''=>'品牌'))); ?>
	款号：<input type="text" class="ts" name="product_sn" />
	名称：<input type="text" class="ts" name="product_name" />
	<label><?php print form_checkbox('skip_set', 1, FALSE)?>过滤掉已设置</label>
	<input type="submit" class="am-btn am-btn-primary" value="搜索" />
	</form>
    </div>
    <div class="blank5"></div>
<?php endif; ?>
    <div id="listDiv">
	<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
	    <thead>
		<tr class="row">
		    <td colspan="10" class="topId"></td>
		</tr>
		<tr class="row">
		    <th> 商品名称 </th>
		    <th> 商品款号 </th>
		    <th> 商品品牌 </th>
		    <th> 商品性别 </th>
		    <th> 商品图 </th>
		    <th> 后台分类 </th>
		    <th> 前台分类 </th>
		</tr>
	    </thead>
	    <tbody id="content_code">
		<?php foreach($list as $row): ?>
		<tr class="row">
			<td><?php print $row->product_name; ?></td>
			<td><?php print $row->product_sn; ?></td>
			<td><?php print $row->brand_name; ?></td>
			<td><?php print $row->product_sex==1?'男':($row->product_sex==2?'女':($row->product_sex==3?'男女':'')); ?></td>
			<td><?php if(!empty($row->gallery)): ?>
			    <img src="public/data/images/<?php print $row->gallery; ?>" ><?php endif; ?> 
			</td>
			<td><?php print $row->category_name; ?></td>
			<td style="word-wrap:break-word;word-break:break-all;text-align:left"  width="300">[<a id="" href="javascript:show_product_type(<?php print $row->product_id; ?>);">设置</a>]<?php if(!empty($row->product_type)): echo "<span>".$row->product_type."</span>"; endif; ?></td>
		</tr>
		<?php endforeach; ?>
	    </tbody>
	    <tfoot>
		<tr class="row">
		    <td colspan="10" class="bottomTd"></td>
		</tr></tfoot>
	</table>
	<div class="blank5"></div>
	<div class="page">
	<?php include(APPPATH.'views/common/page.php') ?> </div>
    </div>
    <div class="blank5"></div>
 <?php if($full_page): ?>   
</div>
<?php include_once(APPPATH . 'views/common/footer.php'); ?>
<?php endif; ?>
