<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>

	<script type="text/javascript">
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'friend/index';
		function search(){
			listTable.filter['link_name'] = $.trim($('input[name=link_name]').val());
			listTable.filter['link_url'] = $.trim($('input[name=link_url]').val());
			listTable.loadList();
		}
		//]]>
	</script>
<div class="main">
		<div class="main_title"><span class="l">订单管理 >> 配送区域</span> <a href="shipping/index" class="return r">返回列表</a><span class="r"><a href="shipping/add_shipping_area/<?php echo $shipping_id?>" class="add">新增</a></span></div>
        <div class="blank5"></div>
		<div id="listDiv">
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="42">ID</th>
                                  <th width="140">配送区域名称</th>
                                  <th width="800">所辖地区</th>
                                  <th width="88">货到付款</th>
                                  <th>首重运费</th>
                                  <th>续重运费</th>
				  <th width="100">操作</th>
				</tr>
				<?php foreach($all_shipping_area as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row['shipping_area_id']; ?></td>
					<td><?php print $row['shipping_area_name']; ?></td>
					<td>
                    
					<?php
						if(empty($row['area'])){echo '当前区域下没有任何关联地区!';}
						else{
							foreach($row['area'] as $item){
								echo $item.',';
							}
						}
					?>
                    </td>
					<!--<td><img src="public/images/<?php /*echo $row['is_cod'] == 1 ? 'yes' : 'no'*/?>.gif" /></td>样式显示 By Rock-->
                    <td><span class="<?php echo $row['is_cod'] == 1 ? 'yesForGif' : 'noForGif'?>"></span></td>
                    <td><?=$row['shipping_fee1']?></td>
                    <td><?=$row['shipping_fee2']?></td>
		    <td>
                    <?php if($perms['shipping_area_edit'] == 1):?>
                    <a href="shipping/edit_shipping_area/<?php print $row['shipping_area_id']; ?>/<?php echo $shipping_id?>" title="编辑" class="edit"></a>
                    <a onclick="return confirm('确定删除？')" href="shipping/delete_shipping_area/<?php print $row['shipping_area_id']; ?>/<?php echo $shipping_id?>" title="删除" class="del"></a>
                    <?php endif;?>
                    </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="7" class="bottomTd"> </td>
				</tr>
			</table>
<div class="blank5"></div>
</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
