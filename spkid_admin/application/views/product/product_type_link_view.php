<div id="type_link_title">
	商品名称：<?php echo $product_name; ?>
	商品款号：<?php echo $product_sn; ?>
	商品品牌：<?php echo $brand_name; ?>
	后台分类：<?php echo $category_name; ?>
</div>
<div>
<!--商品前台分类-->
	<div id="typelinkDiv" style="height:500px;overflow-y:scroll;">
		<input type="hidden" id="product_id" name="product_id" value="<?php echo $product_id ?>">
		<?php foreach($types as $sub_type):?>
		<table width="95%" align="center" cellpadding="0" cellspacing="0" class="classified" style="margin: 5px 0;">
				<tr>
					<th align="left" class="classified_title"><?php echo $sub_type->type_name ?></th>
				</tr>
				<tr>
					<td>
					    <?php foreach($sub_type->sub_items as $type):?>
                        <div class="classifiedElement" <?php if($type->checked) echo 'style="color:red;"';?>>
							<label><input name="type_ids" type="checkbox" value="<?php echo $type->type_id ?>" style="vertical-align: middle;" 
								<?php if($type->checked): ?>checked="checked"<?php endif; ?>>
								<?php echo $type->type_name ?>
							</label>
						</div>
					    <?php endforeach; ?>
					</td>
				</tr>
			</tr>
		</table>
	    <?php endforeach ?>	
	</div>
	<div class="blank5"></div>
</div>
<script type="text/javascript">
$(function () {
	$('.classified_title').click(function () {
		var trFirst = $(this).parent(),
			trNextAll = trFirst.nextAll('tr');
		if (trFirst.nextAll('tr:hidden').length>0) {trNextAll.show();}
		else {trNextAll.hide();}
	});
});
</script>
