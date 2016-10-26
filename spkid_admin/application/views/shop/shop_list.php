<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<div class="main">
		<div class="main_title">
		<span class="l">系统设置&gt;&gt; 店铺列表</span>
		<?php if (check_perm('shop_add')): ?>
		<span class="r">
			<a href="shop/add" class="add">添加</a>
		</span>
		<?php endif; ?>
		</div>

		<div class="blank5"></div>
		<!--<div class="search_row">
		</div>-->
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th>店铺名称</th>
				  <th>店铺id</th>
			      <th>单商品生成订单</th>
				  <th>支持货到付款</th>
				  <th>供应商发货</th>
				  <th>添加人</th>
				  <th>添加时间</th>
				  <th width="120px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
                    <td><?=$row->shop_name?></td>
					<td><?=$row->shop_sn?></td>
					<td><?=$row->single_order==1?"是":"否"?></td>
					<td><?=$row->is_cod==1?"是":"否"?></td>
					<td><?=$row->shop_shipping==1?"是":"否"?></td>
					<td><?=$row->admin_name?></td>
					<td><?=$row->create_date?></td>
					<td>
						<?php if($perm_edit):?>
                        <a class="edit" href="shop/add/<?php print $row->shop_id; ?>" title="编辑"></a>
						<?php endif;?>
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
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
