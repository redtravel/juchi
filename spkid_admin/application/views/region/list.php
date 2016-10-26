<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
<div class="main">
		<div class="main_title"><span class="l">订单管理 >> 地区列表</span> 
		<?php if($region_type == 1 || $region_type == 2 || $region_type == 3):?>
                <a href="region/index" class="return r">返回列表</a>
                <?php endif;?>
                <span class="r"><a href="<?php print base_url(); ?>region/add/<?php echo $region_type;?>/<?php echo $parent_id;?>" class="add">新增<?php if($region_type == 0){echo '一';}elseif($region_type == 1){echo '二';}elseif($region_type == 2){echo '三';}else{echo '四';}?>级地区</a></span></div>
                <div class="blank5"></div>
		<div id="listDiv">
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="2" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th><?php if($region_type == 0){echo '一';}elseif($region_type == 1){echo '二';}elseif($region_type == 2){echo '三';}else{echo '四';}?>级地区</th>
		        </tr>


			    <tr class="row">
			    	<td align="left">
                    <?php foreach($region as $item):?>
                    <div style="height:25px; width:350px; float:left">
					<?php echo $item->region_name?> 
                    <?php if($item->region_type == 0 || $item->region_type == 1 || $item->region_type == 2):?>
                    <a href="region/index/<?php print $item->region_type +1; ?>/<?php print $item->region_id; ?>" title="管理">管理</a>   
                    <?php endif;?>
                    <?php if($perms['region_edit'] == 1):?>
                    <a href="region/edit/<?php print $item->region_id; ?>/<?php echo $region_type;?>/<?php echo $parent_id;?>" title="编辑" class="edit"></a> 
			        <a href="region/delete/<?php print $item->region_id; ?>/<?php echo $region_type;?>/<?php echo $parent_id;?>" onclick="return confirm('确定删除？')" title="删除" class="del"></a>
                    <?php endif;?>
                    </div>
                    <?php endforeach;?>
                    </td>
				</tr>


			    <tr>
					<td colspan="2" class="bottomTd"> </td>
				</tr>
			</table>
	</div>
    <div class="blank5"></div>

<?php include_once(APPPATH.'views/common/footer.php'); ?>
