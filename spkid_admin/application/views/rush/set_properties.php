<div class="blank5"></div>
<table class="form" cellpadding=0 cellspacing=0>
	<tr>
		<td colspan=4 class="topTd"><input type="hidden" name="rush_id" value="<?php echo $rush->rush_id ?>" /></td>
	</tr>
	<tr>
		<td class="item_title">限抢名称:</td>
		<td class="item_input"><input name="rush_index" type="text" class="textbox" value="<?php echo $rush->rush_index;?>" disabled="true"/></td>
		<td class="item_title">限抢品牌:</td>
		<td class="item_input"><input name="rush_brand" type="text" class="textbox" value="<?php echo $rush->rush_brand;?>" disabled="true"/></td>
	</tr>
	<tr>
		<td class="item_title">限抢分类:</td>
		<td class="item_input"><input name="rush_category" type="text" class="textbox" value="<?php echo $rush->rush_category;?>" disabled="true"/></td>
		<td class="item_title">限抢折扣:</td>
		<td class="item_input"><input name="rush_discount" type="text" class="textbox" value="<?php echo $rush->rush_discount;?>" disabled="true"/></td>
	</tr>
	<tr>
		<td class="item_title">限抢标签:</td>
		<td class="item_input"><input name="rush_tag" type="text" class="textbox" value="<?php echo $rush->rush_tag;?>"/></td>
		<td class="item_title">限抢描述:</td>
		<td class="item_input"><input name="desc" type="text" class="textbox" value="<?php echo $rush->desc;?>"/></td>
	</tr>
	<tr>
		<td class="item_title">限抢简介:</td>
		<td class="item_input" colspan="3"><input name="rush_prompt" type="text" class="textbox" style="width:350px" value="<?php echo $rush->rush_prompt;?>"/></td>
	</tr>
	<tr>
		<td colspan=4 class="bottomTd"></td>
	</tr>
</table>
<br />
<div>
    温馨提示：<span style="color:red">（1个汉字=2个字符，1个字母=1个字符）</span><br />
    &nbsp&nbsp&nbsp【限抢标签】最多只能输入12个字符。
    <!--&nbsp&nbsp&nbsp【限抢名称】最多只能输入26个字符。-->
    &nbsp&nbsp&nbsp【限抢描述】最多只能输入30个字符。<br />
<!--    &nbsp&nbsp&nbsp【限抢品牌】最多只能输入16个字符。
    &nbsp&nbsp&nbsp【限抢分类】最多只能输入20个字符。<br />
    &nbsp&nbsp&nbsp【限抢折扣】最多只能输入4个字符。-->
    &nbsp&nbsp&nbsp【限抢简介】最多只能输入30个字符。<br />
    <span style="color:red">【限抢名称、限抢品牌、限抢分类、限抢折扣】 仅做展示，请至编辑页面修改</span>
</div>