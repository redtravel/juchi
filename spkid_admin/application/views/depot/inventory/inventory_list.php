<?php
    $inventory_type_ary = array(0 => '按货架范围盘点', 1 => '按指定储位盘点');
    $inventory_status_ary = array(0 => '未确认', 1 => '已确认', 2 => '已结束', 3 => '已终止', 4 => '已财审');
?>

<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.filter.sort_by = 'inventory_id';
        listTable.filter.sort_order = 'DESC';
        listTable.url = 'inventory/index';
        function search(){
            listTable.filter['inventory_sn'] = $('input[name=inventory_sn]').val();
            listTable.filter['start_date'] = $('input[name=start_date]').val();
            listTable.filter['end_date'] = $('input[name=end_date]').val();
            listTable.loadList();
        }

        $(function(){
            $(':input[name=start_date]').datetimepicker({showSecond:true,timeFormat:'HH:mm:ss'});
            $(':input[name=end_date]').datetimepicker({showSecond:true,timeFormat:'HH:mm:ss'});
        });
        //]]>
    </script>
    
    <div class="main">
        <div class="main_title">
            <span class="l">盘点列表</span>
            <span class="r">
                <!--<a target="_blank" href="inventory/scan_list" class="add">扫描盘点</a>-->
                <a href="inventory/add" class="add">新增</a>
            </span>
        </div>
        <!--
            TODO:
            1.关于退货后，导致的已知的储位商品错误，可以对储位的某些商品进行快速修复，直接生成盘点赢亏的出入库单据。
            2.关于货到付款的订单，由于储位商品错误，导致的缺货，订单不能正常走下去的情况，仓库通知客服，客服反客审作废，
              完结订单，仓库针对此储位的商品库存错误情况，直接生成盘点赢亏的出入库单据。
            3.关于款到发货，已财审的订单，由于储位商品错误，导致的缺货，订单不能正常走下去的情况，仓库通知客服，
              客服进行虚发虚退，订单完结后，仓库针对此储位的商品库存错误情况，直接生成盘点赢亏的出入库单据。
        -->
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
                盘点编号：<input type="text" class="ts" name="inventory_sn" style="width:140px;height:23px;" />
                盘点创建时间范围：<?php print form_input('start_date', '', 'class="textbox"');?> 
                至
                <?php print form_input('end_date', '', 'class="textbox"');?> 
                <input type="submit" class="am-btn am-btn-primary" value="搜索" />
            </form>
        </div>
        <div class="blank5"></div>
        <div id="listDiv">
<?php endif; ?>
            <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
                <tr>
                    <td colspan="8" class="topTd"> </td>
                </tr>
                <tr class="row">
                    <th width="40px">
                        <a href="javascript:listTable.sort('i.inventory_id', 'DESC'); ">
                            ID<?php echo ($filter['sort_by'] == 'i.inventory_id') ? $filter['sort_flag'] : '' ?>
                        </a>
                    </th>
                    <th width="120px">盘点编号</th>
                    <th width="90px">盘点仓库</th>
                    <th width="100px">盘点类型</th>
                    <th width="90px">盘点范围</th>
                    <th width="120px">
                        <a href="javascript:listTable.sort('i.create_date', 'ASC'); ">
                            创建时间<?php echo ($filter['sort_by'] == 'i.create_date') ? $filter['sort_flag'] : '' ?>
                        </a>
                    </th>
                    <th width="90px">创建人</th>
                    <th width="120px">生成差异时间</th>
                    <th width="60px">状态</th>
                    <th width="230px;">操作</th>
                </tr>
                <?php foreach($list as $row): ?>
                <tr class="row">
                    <td><?=$row->inventory_id; ?></td>
                    <td><?=$row->inventory_sn; ?></td>
                    <td><?=$depot_arr[$row->depot_id]; ?></td>
                    <td><?=$inventory_type_ary[$row->inventory_type]; ?></td>
                    <td>
                        <?php if($row->inventory_type == 0): ?>
                        <?=$row->shelf_from; ?>至<?=$row->shelf_to; ?>
                        <?php else: ?>
                        <?=$row->location_name; ?>
                        <?php endif; ?>
                    </td>
                    <td><?=$row->create_date; ?></td>
                    <td><?php echo empty($row->create_admin) ? '' : $admin_arr[$row->create_admin]->admin_name; ?></td>
                    <td><?=$row->diff_date; ?></td>
                    <td><?=$inventory_status_ary[$row->status]; ?></td>
                    <td>
                        <?php if($row->status == 0): ?>
                        <a href="inventory/edit/<?=$row->inventory_id; ?>">编辑</a>
                        | <a href="inventory/generate/<?=$row->inventory_id; ?>" onclick="return confirm('确定生成盘点清单?');">生成盘点清单</a>
                        | <a href="inventory/delete/<?php print $row->inventory_id; ?>" onclick="return confirm('确定删除?');">删除</a>
                        | <a href="inventory/check/<?=$row->inventory_id; ?>" onclick="return confirm('确定确认?');">确认</a>
                        | <a href="inventory/detail/<?=$row->inventory_id; ?>">商品详情</a>
                        <?php else: ?>
                            <a href="inventory/edit/<?=$row->inventory_id; ?>">查看</a>
                            | <a href="inventory/detail/<?=$row->inventory_id; ?>">商品详情</a>
                            <?php if($row->status == 2): ?>
                            | <a href="inventory/financial_check/<?=$row->inventory_id; ?>" onclick="return confirm('确定财审?');">财审</a>
                            <?php endif; ?>
                            <?php if($row->status <= 2): ?>
                            | <a href="inventory/stop/<?=$row->inventory_id; ?>" onclick="return confirm('确定终止?');">终止</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="8" class="bottomTd"> </td>
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