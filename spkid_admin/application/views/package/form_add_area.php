<form name="form_add_area" action="javascript:add_area();">
	<table class="form area_edit"  cellpadding=0 cellspacing=0>
		<tr>
			<td colspan=4 class="topTd"></td>
		</tr>
		<tr class="area_type_tr_1 area_type_tr_2">
			<td class="item_title">区域类型</td>
			<td class="item_input">
				<select name="area_type" onchange="change_area_type();">
					<option value="1">商品区域</option>
					<option value="2">自定义区域</option>
				</select>
			</td>
		</tr>
		<tr class="area_type_tr_1 area_type_tr_2">
			<td class="item_title">区域名称</td>
			<td class="item_input">
				<input type="text" name="area_name" value="" class="textbox require" />
			</td>
		</tr>
		<?php if($package->package_type==3):?>
		<tr class="area_type_tr_1">
			<td class="item_title">最小购买数量</td>
			<td class="item_input">
				<input type="text" name="min_number" value="" class="textbox require" size="3" />
			</td>
		</tr>
		<?php endif;?>
		<tr class="area_type_tr_1 area_type_tr_2">
			<td class="item_title">排序</td>
			<td class="item_input">
				<input type="text" name="sort_order" value="" class="textbox" size="3" />
			</td>
		</tr>
		<tr class="area_type_tr_2 area_text_tr"  style="display:none;">
			<td class="item_title">自定义内容</td>
			<td class="item_input">
				<?php print $this->ckeditor->editor('area_text');?>
			</td>
		</tr>
		<tr>
			<td class="item_title"></td>
			<td class="item_input">
				<?php print form_submit('mysubmit','提交', 'class="am-btn am-btn-primary"');?>
				<?php print form_button('mycancel','取消', 'class="am-btn am-btn-primary" style="display:none" onclick="cancel_area_edit();"');?>
			</td>
		</tr>
		<tr>
			<td colspan=2 class="bottomTd"></td>
		</tr>
	</table>
</form>