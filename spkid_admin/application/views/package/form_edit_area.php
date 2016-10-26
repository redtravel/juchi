<form name="form_edit_area" action="javascript:edit_area();">
	<table class="form area_edit"  cellpadding=0 cellspacing=0>
		<tr>
			<td colspan=4 class="topTd"></td>
		</tr>
		<tr>
			<td class="item_title">区域类型</td>
			<td class="item_input">
				自定义区域
			</td>
		</tr>
		<tr>
			<td class="item_title">区域名称</td>
			<td class="item_input">
				<?php print $area->area_name; ?>
			</td>
		</tr>
		<tr class="area_type_tr_2"  style="display:none;">
			<td class="item_title">自定义内容</td>
			<td class="item_input">
				<?php print $this->ckeditor->editor('area_text', $area->area_text);?>
			</td>
		</tr>
		<tr>
			<td class="item_title"></td>
			<td class="item_input">
				<?php print form_submit('mysubmit','提交', 'class="am-btn am-btn-primary"');?>
				<?php print form_button('mycancel','取消', 'class="am-btn am-btn-primary" onclick="cancel_area_edit();"');?>
			</td>
		</tr>
		<tr>
			<td colspan=2 class="bottomTd"></td>
		</tr>
	</table>
</form>