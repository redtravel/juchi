<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>

<div class="main">
	<div class="main_title"><span class="l">供应商管理 >> 扣点历史 </span><a href="provider/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
        
        <div id="listDiv">
	    <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
		    <tr>
			    <td colspan="4" class="topTd"> </td>
		    </tr>
		    <tr class="row">
                            <th width="80px">编号</th>
                            <th>扣点</th>
                            <th>失效时间</th>
                            <th>更新人</th>
                    </tr>
                    <?php foreach($histories as $index => $row): ?>
                    <tr class="row">
                            <td><?php print $index + 1; ?></td>
                            <td><?php print $row->commission; ?></td>
                            <td><?php print $row->end_time; ?></td>
                            <td><?php print $all_admin[$row->update_admin]->admin_name; ?></td>
                    </tr>
                    <?php endforeach; ?>
		    <tr>
			    <th colspan="4" class="bottomTd"></th>
		    </tr>
	    </table>
	    <div class="blank5"></div>
	</div>
        
</div>
<?php include(APPPATH.'views/common/footer.php');?>
