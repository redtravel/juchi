<?php if ($full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = '/depotio/transaction_log';
        function search() {
            listTable.filter['provider_barcode'] = $.trim($('#provider_barcode').val());
            listTable.filter['location_name'] = $.trim($('#location_name').val());
            listTable.filter['batch_code'] = $.trim($('#batch_code').val());
            var trans_status='';
            $("input[type=checkbox][name=trans_status]:checked").each(function(i,dom){
                trans_status += dom.value + ',';
            });
            listTable.filter['trans_status'] = $.trim(trans_status.substring(0,trans_status.length-1));
            listTable.loadList();
        }

        $(function(){
			var trans_status = '<?php echo $filter['trans_status']; ?>';
			if(!isEmpty(trans_status)) {
				var tmp = trans_status.split(",");
				$("input[type=checkbox][name=trans_status]").removeProp("checked");
				$("input[type=checkbox][name=trans_status]").each(function(i,dom){
					if(contains(tmp,dom.value)){
						$(dom).attr("checked","checked");
					}
				});
			}
			
        });

        function contains(arr, obj) {
            for (var i = 0; i < arr.length; i++) {
                if (arr[i] === obj) {
                    return true;
                }
            }
            return false;
        }
        isEmpty = function (str) {
        	return (typeof (str) === "undefined" || str === null || (str.length === 0));
        };
        //]]>
    </script>
    <div class="main">
        <div class="main_title">
            <span class="l">出入库详情</span>
        </div>

        <div class="blank5"></div>
        <div class="search_row">
		<form name="search" action="javascript:search(); ">
			条形码：<input type="text" name="provider_barcode" id="provider_barcode">
			储位：<input type="text" name="location_name" id="location_name">
			批次：<input type="text" name="batch_code" id="batch_code" value="<?=$filter['batch_code']?>">
			状态：
				<input type="checkbox" name="trans_status" value="1" checked="checked">待出
				<input type="checkbox" name="trans_status" value="2" checked="checked">已出
				<input type="checkbox" name="trans_status" value="3" checked="checked">待入
				<input type="checkbox" name="trans_status" value="4" checked="checked">已入
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
        </div>
        <div class="blank5"></div>

        <div id="listDiv">
        <?php endif; ?>

		<?php if (!empty($list)): ?>
		<table id="dataTable" class="dataTable">
			<tr>
				<td colspan="9" class="topTd"> </td>
			</tr>
			<tr class="row">
				<th>商品名称</th>
				<th>颜色</th>
				<th>规格</th>
				<th>条形码</th>
				<th>货号</th>
				<th>仓库</th>
				<th>储位</th>
				<th>批次</th>
				<th>状态</th>
				<th>数量</th>
				<th>创建时间</th>
				<th>关联单号</th>
			</tr>
			<?php foreach ($list as $row): ?>
			<tr class="row">
				<td align="center"><?=$row->product_name; ?> [<?=$row->product_sn;?>]</td>
				<td align="center"><?=$row->color_name; ?> [<?=$row->color_sn; ?>]</td>
				<td align="center"><?=$row->size_name; ?> [<?=$row->size_sn; ?>]</td>
				<td align="center"><?=$row->provider_barcode; ?></td>
				<td align="center"><?=$row->provider_productcode; ?></td>
				<td align="center"><?=$row->depot_name; ?></td>
				<td align="center"><?=$row->location_name; ?></td>
				<td align="center"><?=$row->batch_code; ?></td>
				<td align="center">
				<?php if($row->trans_status==1): ?>待出
				<?php elseif($row->trans_status==2):?>已出
				<?php elseif($row->trans_status==3):?>待入
				<?php elseif($row->trans_status==4):?>已入
				<?php endif; ?>
				</td>
				<td align="center"><?=abs($row->product_number); ?></td>
				<td align="center"><?=$row->create_date; ?></td>
				<td align="center">
				<?php if($row->trans_type==1): ?>
				<a href="depotio/edit_in/<?=$row->depot_in_id?>" target="_blank"><?=$row->trans_sn; ?></a>
				<?php elseif($row->trans_type==2): ?>
				<a href="depotio/edit_out/<?=$row->depot_out_id?>" target="_blank"><?=$row->trans_sn; ?></a>
				<?php elseif($row->trans_type==3): ?>
				<a href="order/info/<?=$row->order_id?>" target="_blank"><?=$row->trans_sn; ?></a>
				<?php elseif($row->trans_type==4): ?>
				<a href="order_return/edit/<?=$row->return_id?>" target="_blank"><?=$row->trans_sn; ?></a>
				<?php elseif($row->trans_type==6): ?>
				<a href="exchange/edit_exchange/<?=$row->exchange_id?>" target="_blank"><?=$row->trans_sn; ?></a>
				<?php else: ?>
				<?=$row->trans_sn; ?>
				<?php endif; ?>
				</td>
			</tr>
			<?php endforeach; ?>
			<tr>
				<td colspan="9" class="bottomTd"> </td>
			</tr>
		</table>

		<div class="blank5"></div>

		<div class="page">
			<?php include(APPPATH.'views/common/page.php') ?>
		</div>
        
        <?php endif; ?>

<?php if ($full_page): ?>
        </div>
    </div>
    <?php include_once(APPPATH . 'views/common/footer.php'); ?>
<?php endif; ?>