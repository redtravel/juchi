<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="../../../public/js/listtable.js"></script>
	<script type="text/javascript" src="../../../public/js/utils.js"></script>
	<div class="main">
		<div class="main_title"><span class="l">订单管理 >> 支付列表</span> <a href="<?php print base_url(); ?>payment/add" class="add r">新增</a></div>
        <div class="blank5"></div>
		<div id="listDiv">
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="8" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="50">ID</th>
				  <th width="110">支付编码</th>
			      <th width="125">支付名称</th>
			      <th>支付方式描述</th>
			      <th width="68">类型</th>
			      <th width="68">退还方式</th>
			      <th width="68">在线支付</th>
				  <th width="44">启用</th>
				  <th width="57">排序号</th>
				  <th width="93">操作</th>
				</tr>
                
				<?php foreach($list as $row): ?>
                <tr class="row">
			    	<td><?php print $row->pay_id; ?></td>
			    	<td><?php print $row->pay_code; ?></td>
					<td><?php print $row->pay_name; ?></td>
					<td><?php print $row->pay_desc; ?></td>
					<td><?php print $row->is_discount?'折扣':'支付'; ?></td>
					<td><?php print $this->back_type[$row->back_type] ?></td>
					<!--<td><img src="public/images/<?php /*echo $row->is_online == 1 ? 'yes' : 'no'*/?>.gif" /></td> 用样式修改 By Rock-->
					<!--<td><img src="public/images/<?php /*echo $row->enabled == 1 ? 'yes' : 'no'*/?>.gif" /></td> 用样式修改 By Rock-->
                    <td><span class="<?php echo $row->is_online == 1 ? 'yesForGif' : 'noForGif'?>"></span></td>
					<td><span class="<?php echo $row->enabled == 1 ? 'yesForGif' : 'noForGif'?>"></span></td>
					<td><?php print $row->sort_order; ?></td>
					<td>
                    <a class="edit" href="payment/edit/<?php print $row->pay_id; ?>" title="编辑" ></a>
                    <a id="del_<?php print $row->pay_id; ?>" href="javascript:void(0);" rel="payment/delete/<?php print $row->pay_id; ?>" title="删除" onclick="do_delete(this)" class="del"></a>
                    </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="8" class="bottomTd"> </td>
				</tr>
			</table>
<div class="blank5"></div>
</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<br />
