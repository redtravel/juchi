<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript" src="public/js/My97DatePicker/WdatePicker.js"></script>
    <script type="text/javascript">
        $(function(){
            var arrs = 'create_date_start,create_date_end,confirm_date_start,confirm_date_end'.split(',');
            wdatepicker(arrs);
    	});
        function wdatepicker(arrs){
            for(var index in arrs){
                $('input[type=text][name='+arrs[index]+']').attr('onclick',"WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})");
                $('input[type=text][name='+arrs[index]+']').attr('readonly','readonly');
            }
        }
        function datepicker(arrs){
            for(var index in arrs){
                $('input[type=text][name='+arrs[index]+']').datetimepicker({timeFormat: 'hh:mm:ss',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
                $('input[type=text][name='+arrs[index]+']').attr('readonly','readonly');
            }
        }
        //	<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'depot_order/index';
        function search(){
            listTable.filter['create_date_start'] = $.trim($('input[name=create_date_start]').val());
            listTable.filter['create_date_end'] = $.trim($('input[name=create_date_end]').val());
            listTable.filter['order_sn'] = $.trim($('input[name=order_sn]').val());
            listTable.filter['order_status'] = $('select[name=order_status]').val();
            listTable.filter['confirm_date_start'] = $.trim($('input[name=confirm_date_start]').val());
            listTable.filter['confirm_date_end'] = $.trim($('input[name=confirm_date_end]').val());
            listTable.filter['shipping_id'] = $('select[name=shipping_id]').val();
            listTable.filter['shipping_status'] = $('select[name=shipping_status]').val();
            listTable.filter.page = '1';
            listTable.loadList();
        }
        //]]>
    </script>
    <div class="main">
        <div class="main_title"><span class="l">订单查询</span>
        </div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
                <label onclick="change();">下单日期：</label>
                <input type="text" name="create_date_start" id="create_date_start"/>
                <input type="text" name="create_date_end" id="create_date_end"/>
                订单号：
                <input type="text" name="order_sn"/>
                订单状态：
                <select name="order_status">
                    <option value=>全部</option>
                    <option value="-1">未确认</option><!--使用0会被后台认为empty用-1代替-->
                    <option value="1">已确认</option>
                    <option value="4">作废</option>
                    <!--<option value="5">拒收</option>-->
                </select>
                审核时间：
                <input type="text" name="confirm_date_start" id="confirm_date_start"/>
                <input type="text" name="confirm_date_end" id="confirm_date_end"/>
                配送方式：
                <?php print form_dropdown('shipping_id',array(''=>'配送方式')+get_pair($shipping_list,'shipping_id','shipping_name')); ?>
                配送状态：
                <select name="shipping_status">
                    <option value=>全部</option>
                    <option value="-1">未拣货</option>
                    <option value="1">已拣货</option>
                    <option value="2">已复核</option>
                    <option value="3">已发货</option>
                </select>
                <input type="submit" class="am-btn am-btn-primary" value="搜索" />
            </form>
        </div>
        <div class="blank5"></div>
            <div id="listDiv">
<?php endif; ?>
                <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
                    <tr>
                        <td colspan="10" class="topTd"></td>
                    </tr>
                    <tr class="row">
                        <th>下单时间</th><th>订单号</th><th>订单状态</th><th>审核时间</th><th>配送方式</th><th>配送状态</th>
                        <th>打印</th>
                    </tr>
                    <?php foreach($list as $row): ?>
                        <tr class="row">
                            <td><?php print $row->create_date ?></td>
                            <td><?php print $row->order_sn ?></td>
                            <td><?php print $row->order_status ?></td>
                            <td><?php print $row->confirm_date ?></td>
                            <td><?php print $shipping[$row->shipping_id] ?></td>
                            <td><?php print $row->shipping_status ?></td>
                            <td><a href="order_track/print_order/<?=$row->order_id?>" target="_blank" class="icon_xiang" title="打印包裹装箱单"></a></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="10" class="bottomTd"> </td>
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