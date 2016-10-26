<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>
		<script type="text/javascript">
		//<![CDATA[
        $(document).click(function(){
            if ($("#h_pick_cell").css('display') == 'block') 
            {
                $("#h_pick_cell").hide();
            }
        });
        // 拣货单号扫描
        function scan_pick_sn(e)
        {
            e = e ? e : (window.event ? window.event : null);    
            var v_scan_sn = $.trim($("input[type=text][name=pick_sn]").val());
            if (e.keyCode != 13) 
            {   
                return false;
            }   
                                
            if (v_scan_sn == '') 
            {   
                alert('请您扫描拣货单号！');
                return false
            }
            
		    listTable.url = 'pick/scan_pick';
			listTable.filter['pick_sn'] = v_scan_sn;
			listTable.loadList();
        }

        var v_scan_num2 = 0;
        // 扫描商品条形码
        function scan_goods_barcode(e)
        {
        
            e = e ? e : (window.event ? window.event : null);    
            var v_scan_sn = $.trim($("input[type=text][name=goods_barcode]").val());
            if (e.keyCode != 13) 
            {   
                return false;
            }   
                                
            if (v_scan_sn == '') 
            {   
                alert('请您扫描商品条形码！');
                return false
            }
            $("#h_pick_cell").hide();
            var v_is_scan = document.getElementsByName("is_scan[]");
            var v_goods_barcode = document.getElementsByName("barcode[]");
            var v_product_num = document.getElementsByName("product_num[]");
            var v_scan_num = document.getElementsByName("scan_num[]");
            var v_pick_cell = document.getElementsByName("pick_cell[]");
            var v_subid = document.getElementsByName("sub_id[]");
            //var v_unusual = document.getElementsByName("is_unusual[]");
            //var v_scan_num2 = 0;
            var v_flag = false;
            for (var i = 0; i < v_is_scan.length; i++)
            {
                if (v_is_scan.item(i).checked == true)
                {
                    continue;
                }

                if (v_goods_barcode.item(i).value != v_scan_sn)
                {
                    continue;
                }
                v_flag = true;
                v_scan_num.item(i).value = parseInt(v_scan_num.item(i).value) + 1;
                if (v_scan_num.item(i).value == v_product_num.item(i).value)
                {
                    v_is_scan.item(i).checked = true;
                    document.getElementsByName('is_unusual_'+v_subid.item(i).value).item(0).disabled = true;
                    v_scan_num2 = v_scan_num2 + 1;
                    v_is_scan.item(i).parentNode.parentNode.style.background="#00ff66";
                    v_scan_num.item(i).style.background = '#00ff66';
                }
                // 显示订单格子号
                $("#h_pick_cell").text(v_pick_cell.item(i).value);
                var pos = $("input[type=text][name=goods_barcode]").position();
                $("#h_pick_cell").css({
                    position:'absolute', 
                    index:'999', 
                    left:pos.left+"px",
                    top:(pos.top+30)+"px"
                }).show();
                
                break;
            }
            
            $("input[type=text][name=goods_barcode]").val('');
            if (!v_flag)
            {
                alert('该拣货单中没有此商品');
                return false;
            }

            if (v_scan_num2 != v_is_scan.length)
            {
                return false;
            }

            $("#h_pick_cell").hide();
            if (confirm("拣货已完成，是否自动提交数据？"))
            {
                document.scan_pick.submit();
            }
        }
        
        function j_check_scan()
        {
            var v_is_scan = document.getElementsByName("is_scan[]");
            var v_subid = document.getElementsByName("sub_id[]");
            var v_flag = false;
            for (var i = 0; i < v_is_scan.length; i++ )
            {
                if (v_is_scan.item(i).checked)
                {
                    continue;
                }
                
                if (document.getElementsByName('is_unusual_'+v_subid.item(i).value).item(0).checked)
                {
                    continue;
                }
                v_flag = true;
            }
            
            if (v_flag)
            {
                alert("未拣完货，若要提交此次拣货单数据，请将未拣完货的商品，勾选为异常单");
                return false;
            }
            return true;
        }
        //]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">扫描拣货</span></div>
		<div class="blank5"></div>
		<div class="search_row">
			拣货单号：<input type="text" class="ts" name="pick_sn" value="" style="width:150px;ime-mode:disabled;" onkeydown="scan_pick_sn(event);" />
			商品条码：<input type="text" class="ts" name="goods_barcode" value="" style="width:150px;ime-mode:disabled;" onkeydown="scan_goods_barcode(event);" />
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
        <?php endif; ?>
        <?php if ($list) :?>
                <form name="scan_pick" action="pick/scan_pick_finish" method="post" onsubmit="return j_check_scan();">
                <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
                <input type="hidden" name="pick_sn" value="<?php print $pick_sn; ?>"/>
                <tr>
					<td colspan="9" class="topTd"> </td>
				</tr>
				 <tr class="row">
                    <th width="120px">商品名称</th>
					<th>SKU</th>
					<th>储位</th>
					<th>应拣数量</th>
                    <th>已拣数量</th>
                    <th>扫描数量</th>
                    <th>是否拣完</th>
                    <th>异常单</th>
                    <th>格子号</th>
				</tr>
                <?php foreach($list as $row): ?>
                    <input type="hidden" name="barcode[]" value="<?php print $row->provider_barcode; ?>"/>
                    <input type="hidden" name="product_num[]" value="<?php print $row->product_number-$row->pick_num; ?>"/>
                    <input type="hidden" name="sub_id[]" value="<?php print $row->sub_id; ?>"/>
                    <input type="hidden" name="pick_cell[]" value="<?php print $row->pick_cell; ?>"/>
                    <input type="hidden" name="rel_no[]" value="<?php print $row->rel_no; ?>"/>
                <tr class="row">
                    <td><?php print $row->product_name; ?><br><?php print $row->color_name." ".$row->size_name; ?></td>
                    <td><?php print $row->sku; ?></td>
                    <td><?php print $row->location_name; ?></td>
                    <td><?php print $row->product_number; ?></td>
                    <td><?php print $row->pick_num; ?></td>
					<td><input type="text" name="scan_num[]" value="0" style="border:0px;background-color:#ffffff;width:20px;"/></td>
                    <td><input type="checkbox" name="is_scan[]" disabled></td>
                    <td><input type="checkbox" name="is_unusual_<?php print $row->sub_id; ?>" value="1"/></td>
					<td><?php print $row->pick_cell; ?></td>
				</tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="9" style="text-align:right;" height="30"><input type="submit" name="scan_finish" class="am-btn am-btn-primary" value="拣货完成"></td>
                </tr>
				<tr>
					<td colspan="9" class="bottomTd"> </td>
                </tr>
            </table>
            </form>
           <div id="h_pick_cell" style="width:400px; height:360px;display:none; background-color:#cccccc; font-size:300px; line-height:360px; text-align:center; color:#000000;"></div>
<?php endif; ?>
			<div class="blank5"></div>
<?php if($full_page): ?>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
