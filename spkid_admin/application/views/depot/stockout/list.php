<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.filter.sort_by = 'create_date';
        listTable.filter.sort_order = 'DESC';
        listTable.url = 'stockout/index';
        function search(){
            listTable.filter['stockout_sn'] = $('input[name=stockout_sn]').val();
            listTable.filter['trans_sn'] = $('input[name=trans_sn]').val();
            listTable.loadList();
        }
        //]]>
    </script>
    
    <div class="main">
        <div class="main_title">
            <span class="l">缺货登记列表</span>
        </div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
                流水号：<input type="text" class="ts" name="stockout_sn" style="width:140px;height:23px;" />
                订单/退货单编号：<input type="text" class="ts" name="trans_sn" style="width:140px;height:23px;" />
                <input type="submit" class="am-btn am-btn-primary" value="搜索" />
            </form>
        </div>
        <div class="blank5"></div>
        <div id="listDiv">
<?php endif; ?>
            <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
                <tr>
                    <td colspan="13" class="topTd"> </td>
                </tr>
                <tr class="row">
                    <th width="100px">
                        <a href="javascript:listTable.sort('stockout_sn', 'ASC'); ">
                        流水号<?php echo ($filter['sort_by'] == 'stockout_sn') ? $filter['sort_flag'] : '' ?>
                        </a>
                    </th>
                    <th width="80px">仓库名称</th>
                    <th width="80px">订单号</th>
                    <th width="80px">出库单号</th>
                    <th width="80px">储位名称</th>
                    <th width="80px">商品名称</th>
                    <th width="80px">颜色</th>
                    <th width="80px">尺码</th>
                    <th width="80px">库存数量</th>
                    <th width="80px">登记数量</th>
                    <th width="80px">批次</th>
                    <th width="120px">
                        <a href="javascript:listTable.sort('create_date', 'ASC'); ">
                            创建时间<?php echo ($filter['sort_by'] == 'create_date') ? $filter['sort_flag'] : '' ?>
                        </a>
                    </th>
                    <th width="80px">创建人</th>
                    <th width="120px">操作</th>
                </tr>
                <?php foreach($list as $row): ?>
                <tr class="row">
                    <td><?=$row->stockout_sn; ?></td>
                    <td><?=$row->depot_name; ?></td>
                    <td><?=$row->trans_sn; ?></td>
                    <td><?=$row->depot_sn; ?></td>
                    <td><?=$row->location_name; ?></td>
                    <td><?=$row->product_name; ?></td>
                    <td><?=$row->color_name; ?></td>
                    <td><?=$row->size_name; ?></td>
                    <td><?=$row->num; ?></td>
                    <td><?=$row->num; ?></td>
                    <td><?=$row->batch_code; ?></td>
                    <td><?=$row->create_date; ?></td>
                    <td><?php echo empty($row->create_admin) ? '' : $admin_arr[$row->create_admin]->admin_name; ?></td>
                    <td><a href="stockout/generate_invoice/<?=$row->id; ?>" onclick="return confirm('确定生成盘点出入库单据?');">生成盘点出入库单据</a></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="13" class="bottomTd"> </td>
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