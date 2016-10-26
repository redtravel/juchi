<?php include(APPPATH.'views/common/header.php'); ?>
<script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>
<div class="main">
		<div class="main_title"><span class="l">会员管理 >> 等级列表</span> <span class="r"><a href="userrank/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
		<div id="listDiv">
			<table width="1170"  cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="13" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="55">ID</th>
			      <th width="124">名称</th>
			      <th width="98">最少</th>
			      <th width="82">最多</th>
				  <th width="84">注册积分</th>
				  <th width="104">购买积分倍数</th>
				  <th width="92">评论积分</th>
				  <th width="93">完善积分</th>
				  <th width="97">邀请积分</th>
				  <th width="111">首次下单积分</th>
				  <th width="134">时间</th>
				  <th width="94">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td><?php echo $row->rank_id;?></td>
					<td><?php echo $row->rank_name;?></td>
					<td><?php echo $row->min_points;?></td>
					<td><?php echo $row->max_points;?></td>
					<td>
						<span data-pk="<?php print $row->rank_id; ?>" data-name="regist_point" class="editable" data-title="注册积分" data-value="<?php print $row->regist_point; ?>">
							<?php echo $row->regist_point;?>
						</span>

					</td>
					<td><?php echo $row->buying_point_rate;?></td>
					<td><?php echo $row->comment_point;?></td>
					<td>
						<span data-pk="<?php print $row->rank_id; ?>" data-name="profile_point" class="editable" data-title="完善积分" data-value="<?php print $row->profile_point; ?>">
							<?php echo $row->profile_point;?>
						</span>
					</td>
					<td><?php echo $row->invite_point;?></td>
					<td><?php echo $row->friendby_point;?></td>
					<td><?php echo $row->create_date;?></td>
					<td>&nbsp;
                    <?php if(!in_array($row->rank_id,$dis_arr)):?>
                    <?php if($perms['rank_edit'] == 1):?>
                    <a href="userrank/edit/<?php echo $row->rank_id;?>" title="编辑" class="edit"></a> 
                    <?php endif;if($perms['rank_edit'] == 1):?>
                    <a class="del" href="javascript:void(0);" rel="userrank/del/<?php print $row->rank_id; ?>" title="删除" onclick="do_delete(this)"></a>
                    <?php endif;?>
                    <?php endif;?>
                    </td>
				</tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="13" class="bottomTd"> </td>
				</tr>
			</table>
    <div class="blank5"></div>
</div>
	</div>
	<script>
// jquery editable 
function _editable(){


$('.editable').editable({ url: '/userrank/editable', emptytext:'',
        success: function(response, newValue) {
            if(!response.success) return response.msg;
            if( response.value != newValue ) return '操作失败';
        }
    });
}
listTable.func = _editable; // 分页加载后调用的函数名
_editable();
</script>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
