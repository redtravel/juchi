<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="../../../public/js/listtable.js"></script>
	<script type="text/javascript" src="../../../public/js/utils.js"></script>

	<script type="text/javascript">
	    $(function(){
        $('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});

    	});

		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'user_account_log/index/<?php echo $user_arr->user_id;?>';
		function search(){
			listTable.filter['change_code'] = $.trim($('select[name=change_code]').val());
			listTable.filter['start_time'] = $.trim($('input[name=start_time]').val());
			listTable.filter['end_time'] = $.trim($('input[name=end_time]').val());
			listTable.loadList();
		}
		
		//]]>
	</script>
	<div class="main">
        <div class="main_title"><span class="l">会员管理 >> 会员帐户变动明细列表</span><a href="user/index" class="return r">返回列表</a></div>
        <div class="blank5"></div>
        		<div class="button_row">
          <input type="button" <?php echo $dis == 2 ? 'disabled="disabled"' : '';?>  class="am-btn am-btn-primary" value="调节账户" onclick="javascript:location.href='/user_account_log/add/<?php echo $user_arr->user_id;?>'" />
    <strong>当前会员：</strong><?php echo $user_arr->user_name;?> <strong>账户金额：</strong>￥<?php echo $user_arr->user_money;?> 元 <strong>累计消费金额：</strong>￥<?php echo $user_arr->paid_money;?>元 <strong>消费积分：</strong><?php echo $user_arr->pay_points;?>  </div>
		<div class="blank5"></div>
    <div class="blank5"></div>
	  <div class="search_row">
			<form name="search" action="javascript:search(); ">
变动类型：
              <select name="change_code" id="change_code">
                <option value="">--请选择--</option>
                <?php foreach($all_kind as $item):?>
			    <option value="<?php echo $item->change_code?>"><?php echo $item->change_name?></option>
                <?php endforeach;?>
              </select>
			  变动时间：<input type="text" name="start_time" id="start_time" /><input type="text" name="end_time" id="end_time" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="157">账户变动时间</th>
				  <th width="129">变动类型</th>
				  <th width="142">账户金额</th>
				  <th width="143">销费积分</th>
				  <th width="448">变动原因</th>
			    </tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->create_date; ?></td>
			    	<td><?php print $row->change_name; ?></td>
			    	<td><?php print $row->user_money > 0 ? '+'.$row->user_money : $row->user_money; ?></td>
					<td><?php print $row->pay_points > 0 ? '+'.$row->pay_points : $row->pay_points; ?></td>
					<td><?php print $row->change_desc; ?></td>
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