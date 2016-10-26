<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript">
        $(function(){
            $('input[type=text][name=check_date_start]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
            $('input[type=text][name=check_date_end]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
    	});
		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'provider_fee';
		function search(){
            var start_date = $.trim($('input[type=text][name=check_date_start]').val());
            var end_date = $.trim($('input[type=text][name=check_date_end]').val());
            if(start_date!=""){
                //判断开始日期是否大于今天...
                if(end_date!=""&&start_date>end_date){
                    alert("开始时间不能大于结束时间!");
                    return;
                }
            }
			listTable.filter['category_id'] = $.trim($('select[name=category_id]').val());
			listTable.filter['provider_id'] = $.trim($('select[name=provider_id]').val());
			listTable.filter['check_status'] = $('select[name=check_status]').val();
			listTable.filter['check_date_start'] = $.trim($('input[type=text][name=check_date_start]').val());
			listTable.filter['check_date_end'] = $.trim($('input[type=text][name=check_date_end]').val());
			listTable.loadList();
		}
        function check(id){
            if(window.confirm("确定通过审核?")){
                $.ajax({
                    url: 'provider_fee/check/'+id,
                    dataType:'json',
                    type:'POST',
                    success:function(result){
                        if(result.err == 0){
                            document.getElementById('check_status_'+id).innerHTML=result.msg;
                            document.getElementById('check_admin_name_'+id).innerHTML=result.check_admin_name;
                            document.getElementById('check_date_'+id).innerHTML=result.check_date;
                            document.getElementById('delete_'+id).innerHTML="";
                        }else{
                            alert(result.msg);
                        }
                    }
                });
            }
        }
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">供应商费用明细</span><span class="r"><a href="provider_fee/add" class="add">新增</a></span></div>
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
                费用名目:
                <select name="category_id">
                    <option value="">费用名目</option>
                    <?php foreach($fee_category_list as $fee_category) print "<option value='{$fee_category->category_id}'>{$fee_category->category_name}</option>"?>
                </select>
                供应商:
                <select name="provider_id">
                    <option value="">供应商</option>
                    <?php foreach($provider_list as $provider) print "<option value='{$provider->provider_id}'>{$provider->provider_code}</option>"?>
                </select>
                审核状态：
                <select name="check_status">
                    <option value="0">全部</option>
                    <option value="1">已审核</option>
                    <option value="2">未审核</option>
                </select>
                创建日期：
                <input type="text" name="check_date_start" readonly id="check_date_start" />
                <input type="text" name="check_date_end" readonly id="check_date_end" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th>费用名目</th>
                    <th>金额</th>
                    <th>供应商编码</th>
                    <th>品牌</th>
                    <th>备注</th>
                    <th>审核状态</th>
                    <th>审核人</th>
                    <th>审核时间</th>
                    <th>添加人</th>
                    <th>添加时间</th>
                    <th>操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->category_name; ?></td>
					<td><?php print $row->detail_price?></td>
					<td><?php print $row->provider_code?></td>
					<td><?php print $row->brand_name?></td>
					<td><?php print $row->remark?></td>
					<td>
                        <span id="check_status_<?php print $row->id;?>">
                            <?php if($row->check_status==0):?>
                                <a href="javascript:;" onclick="check(<?php print $row->id;?>)" title="点击审核"><font color="red">未审核</font></a>
                            <?php endif ?>
                            <?php if($row->check_status==1):?>
                                已审核
                            <?php endif ?>
                        </span>
                    </td>
					<td>
                        <span id="check_admin_name_<?php print $row->id;?>">
                            <?php print $row->check_admin_name?>
                        </span>
                    </td>
					<td>
                        <span id="check_date_<?php print $row->id;?>">
                            <?php print $row->check_date?>
                        </span>
                    </td>
					<td><?php print $row->create_admin_name?></td>
					<td><?php print $row->create_date?></td>
					<td>
                        <?php if (empty($perm_view)): ?>
                            <a class="edit" href="provider_fee/edit/<?php print $row->id; ?>" title="编辑/查看"></a>
                        <?php endif ?>
                        <?php if (empty($row->check_admin)): ?>
                            <?php if ($perm_del&&empty($row->check_admin)): ?>
                                <span id="delete_<?php print $row->id;?>">
                                    <a class="del" href="javascript:void(0)" rel="provider_fee/delete/<?php print $row->id; ?>" title="删除" onclick="do_delete(this)"></a>
                                </span>
                            <?php endif ?>
                        <?php endif ?>
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