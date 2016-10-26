<?php include(APPPATH.'views/common/header.php'); ?>
<script type="text/javascript" src="../../../public/js/listtable.js"></script>
<script type="text/javascript" src="../../../public/js/utils.js"></script>

<script type="text/javascript">
    $(function(){  
        $('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
        $('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
        $('input[type=text][name=r_start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
        $('input[type=text][name=r_end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
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
    <div class="main_title"><span class="l">报表管理 >> 采购单明细表</span> </div>
    <div class="blank5"></div>
	  <div class="search_row">
		<form method="post" action="report/purchase_main_detail_report" name="theForm"  onsubmit = "return checkForm()">                   
                    
                    <?php print form_dropdown('provider_id', array(''=>'--供应商--')+get_pair($provider_list,'provider_id','provider_name'),array($provider_id), 'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
                    <?php print form_dropdown('brand_id', array(''=>'--品牌--')+get_pair($brand_list,'brand_id','brand_name'),array($brand_id), 'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
                    <?php print form_dropdown('admin_name', array(''=>'--制单员--')+get_pair($all_admin,'admin_id','realname'),array($admin_name), 'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
                    &nbsp;
                    <span style="color: #FF0000;font: 12px verdana;">*</span>采购时间：<input type="text" name="start_time" id="start_time" value="<?php echo $start_time;?>" /><input type="text" name="end_time" id="end_time" value="<?php echo $end_time;?>" />
                    批次号 <input type="text" name="batch_code" value="<?php echo $batch_code;?>" size="15" />
                    采购单号 <input type="text" name="purchase_code" value="<?php echo $purchase_code;?>" size="15" />
                    
                    品名 <input type="text" name="product_name" value="<?php echo $product_name;?>" size="15" />
                    医械级别 <input type="text" name="medical1" value="<?php echo $medical1;?>" size="15" />
                    款号 <input type="text" name="product_sn" value="<?php echo $product_sn;?>" size="15" />
                    税率 <input type="text" name="product_cess" value="<?php echo $product_cess;?>" size="15" />
                    入库时间 <input type="text" name="r_start_time" id="r_start_time" value="<?php echo $r_start_time;?>" /><input type="text" name="r_end_time" id="r_end_time" value="<?php echo $r_end_time;?>" />                                                            
                    
		    <input type="submit" name="search" class="am-btn am-btn-primary" value="搜索" />
                    <input type="submit" name="export" class="am-btn am-btn-primary" value="导出" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
			<?php if (isset($list) && !empty($list)): ?>
			<table cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
                                    <td colspan="16" class="topTd"> </td>
				</tr>
				<tr class="row">
				    <th>供应商（编号）</th>
                                    <th>单号</th>
                                    <th>采购时间</th>
                                    <th>批次号</th>                                   
                                    <th>品牌</th>
                                    <th>品名</th>
                                    <th>规格</th>
                                    <th>医械级别</th>
                                    <th>款号</th>
                                    <th>税率</th>                                   
                                    <th>含税采购单价</th>
                                    <th>含税采购金额</th>
                                    <th>采购数量</th>
                                    <th>实际收货数量</th>
                                    <th>入库时间</th>
                                    <th>制单员</th>
				</tr>

				<?php foreach ($list as $k =>$rs): ?>
				<tr>
                                    <td><?=$rs->provider_code?></td>
                                    <td><?=$rs->purchase_code?></td>
                                    <td><?=$rs->create_date?></td>
                                    <td><?=$rs->batch_code?></td>                                    
                                    <td><?=$rs->brand_name?></td>
                                    <td><?=$rs->product_name?></td>
                                    <td><?=$rs->size_name?></td>
                                    <td><?=$medical_arr[$rs->medical1]?></td>
                                    <td><?=$rs->product_sn?></td>
                                    <td><?=$rs->product_cess?></td>                                   
                                    <td><?=$rs->consign_price?></td>
                                    <td><?=$rs->amount?></td>
                                    <td><?=$rs->product_number?></td>
                                    <td><?=$rs->product_finished_number?></td>
                                    <td><?=$rs->depot_in_date?></td>
                                    <td><?=$rs->realname?></td>                                
                                </tr>
				<?php endforeach; ?>
                                <tr>
                                    <td colspan="16" class="bottomTd"> </td>
				</tr>
			</table>
                        <?php endif; ?>
                <div class="blank5"></div>
	  </div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
