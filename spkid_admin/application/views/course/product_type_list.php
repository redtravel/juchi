<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<div class="main">
		<div class="main_title">
		<span class="l">商品管理&gt;&gt; 商品前台分类</span>
		<?php if ($perm_add): ?>
		<span class="r">
			<a href="product_type/add" class="add">添加</a>
		</span>
		<?php endif; ?>
		</div>

		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:type_search(); ">
                关键字:<input type='text' name='key_word' id='key_word'>
			    <?php print form_dropdown('genre_id', array('0'=>'商品类型')+get_pair($all_genre,'id','name'));?>
			    <?php print form_dropdown('first_type', array('0'=>'一级分类'),'0','id="first_type"');?>
			    <?php print form_dropdown('second_type', array('0'=>'二级分类'),'0','id="second_type"');?>
			    <input type="submit" class="am-btn am-btn-primary" value="搜索" />
            </form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th>编码</th>
				  <th>名称</th>
			      <th>一级</th>
				  <th>二级</th>
                  <th>后台分类</th>
				  <th>商品大类</th>
				  <th>排序</th>
				  <th>启用</th>
				  <th>操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
                    <td><a href="javascript:listTable.filter['parent_id']=<?=$row->type_id?>;listTable.loadList();">
                        <?=$row->type_code?></a></td>
					<td><?=$row->type_name?></td>
					<td><?=empty($row->p1_type_name)?"/":$row->p1_type_name?></td>
					<td><?=empty($row->p2_type_name)?"/":$row->p2_type_name?></td>
                    <td><?=empty($row->category_name)?"/":$row->category_name?></td>
					<td><?=$row->name?></td>
					<td><?=$row->sort_order?></td>
					<td><span class="<?=$row->is_show_cat==1?"yesForGif":"noForGif"?>"></td>
					<td>
                        <?if($perm_edit):?>
                            <a class='edit' title="编辑" href="product_type/add/<?=$row->type_id?>"></a>
                        <?endif?>
                        <?if($perm_del):?>
                            <a class='del' title="删除" href="javascript:type_del(<?=$row->type_id?>)"></a>
                        <?endif?>
                    </td>
				</tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="7" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
			<div class="page">
				<?php include(APPPATH.'views/common/page.php') ?>
			</div>
<?php if($full_page): ?>
		</div>
	</div>
    <script>
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = '/product_type';

        function type_search()
        {
			listTable.filter['key_word'] = $.trim($('input[type=text][name=key_word]').val());
            listTable.filter['parent_id']="";
			listTable.filter['first_type_id'] =$('#first_type').val();
			listTable.filter['genre_id'] = $('select[name=genre_id]').val();
			listTable.filter['second_type_id'] =$('#second_type').val();
			listTable.loadList();
        }
        
        $(function(){
            $('select[name=genre_id]').change(function(){
                var first_type=$('select[name=first_type]');
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
            $('select[name=first_type]').change(function(){
                var second_type=$('select[name=second_type]');
                second_type[0].length=1;
                if($(this).val()!=0){
                    $.post('/product_type/get_second_type/'+$(this).val(),
                        function(data){
                            for(var i=0;i<data.length;i++){
                                //过滤3级分类
                                if(data[i].parent_id2>0){
                                    continue;
                                }
                                second_type.append("<option value='"+data[i].type_id+"'>"+data[i].type_name+"</option>");
                            }
                        },
                        'json');
                }
            });
        });
        function type_del(type_id){
            if(confirm('确认删除吗')){
                $.post('product_type/proc_del/'+type_id,function(data){
                        if(data.error==0){
                            alert('删除成功');
			                listTable.loadList();
                        }
                        else if(data.error==1){
                            alert('该分类下有商品,不能删除');
                        }
                        else if(data.error==2){
                            alert('有下一级分类,不能删除');
                        }
                     },'json');
            }
        }
    </script>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
