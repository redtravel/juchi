<?php include(APPPATH.'views/common/header.php'); ?>
<script type="text/javascript" src="../../../public/js/listtable.js"></script>
<script type="text/javascript" src="../../../public/js/utils.js"></script>

<script type="text/javascript">
    $(function(){  
        $('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
        $('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
    });

    function checkForm(){                   
        var eles = document.forms['theForm'];
        if (eles['start_time'].value=='' || eles['end_time'].value==''){
                alert('错误：请输入报表期间!');
                return false;
        }
        if(eles['end_time'].value < eles['start_time'].value ){
                alert('错误：期间的结束时间早于或等于开始时间!');
                return false;
        }
    }
</script>
	<div class="main">
    <div class="main_title"><span class="l">报表管理 >> 供应商汇总表（采购已入库）</span> </div>
    <div class="blank5"></div>
	  <div class="search_row">
		<form method="post" action="report/purchase_main_report" name="theForm"  onsubmit = "return checkForm()">	
                    
                    <select name="provider_id" data-am-selected="{searchBox: 1,maxHeight: 300}">
			<option value="">--供应商--</option>
			<?php foreach($provider_list as $item) print "<option value='{$item->provider_id}'>{$item->provider_name}</option>";?>
                    </select>
                    
                    &nbsp;
                    <span style="color: #FF0000;font: 12px verdana;">*</span>采购时间：<input type="text" name="start_time" id="start_time" value="<?php echo $start_time;?>" /><input type="text" name="end_time" id="end_time" value="<?php echo $end_time;?>" />
                    批次号 <input type="text" name="batch_code" value="<?php echo $batch_code;?>" size="15" />
                    采购单号 <input type="text" name="purchase_code" value="<?php echo $purchase_code;?>" size="15" />
                    <?php print form_dropdown('admin_name', array(''=>'制单员')+get_pair($all_admin,'realname','realname'),array($admin_name), 'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
                    
		    <input type="submit" name="search" class="am-btn am-btn-primary" value="搜索" />
                    <input type="submit" name="export" class="am-btn am-btn-primary" value="导出" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
			<?php if (isset($list) && !empty($list)): ?>
			<table cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
                                    <td colspan="8" class="topTd"> </td>
				</tr>
				<tr class="row">
				    <th>供应商（编号）</th>
                                    <th>单号</th>
                                    <th>采购时间</th>
                                    <th>批次号</th>
                                    <th>含税采购金额</th>
                                    <th>采购数量</th>
                                    <th>实际收货数量</th>
                                    <th>制单员</th>
				</tr>

				<?php foreach ($list as $k =>$rs): ?>
				<tr>
                                    <td><?=$rs->provider_code?></td>
                                    <td><?=$rs->purchase_code?></td>
                                    <td><?=$rs->create_date?></td>
                                    <td><?=$rs->batch_code?></td>
                                    <td><?=$rs->purchase_amount?></td>
                                    <td><?=$rs->purchase_number?></td>
                                    <td><?=$rs->purchase_finished_number?></td>
                                    <td><?=$rs->realname?></td>                                
                                </tr>
				<?php endforeach; ?>
                                <tr>
                                    <td colspan="8" class="bottomTd"> </td>
				</tr>
			</table>
                        <?php endif; ?>
                <div class="blank5"></div>
	  </div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
