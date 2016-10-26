<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        <?php if($filter['only_show_diff'] == 1): ?>
        listTable.url = 'inventory/detail/<?=$row->inventory_id;?>/1';
        <?php else: ?>
        listTable.url = 'inventory/detail/<?=$row->inventory_id;?>';
        <?php endif; ?>
        
        function search(){
            listTable.loadList();
        }
        
        //复盘储位
        function reset(location_id) {
            if (confirm('确定复盘?')) {
                var inventory_id = <?=$row->inventory_id;?>;
                window.location.href = "inventory/reset/"+location_id+"/"+inventory_id;
            }
        }
        
        //生成差异商品清单
        function generate_diff(inventory_id) {
            if (confirm('确定生成?')) {
                window.location.href = "inventory/generate_diff/"+inventory_id;
            }
        }
        //生成差异出入库单据
        function generate_invoice(inventory_id) {
            if (confirm('确定生成?')) {
                window.location.href = "inventory/generate_invoice/"+inventory_id;
            }
        }
        
        //只显示商品差异
        function only_show_diff() {
            var inventory_id = <?=$row->inventory_id;?>;
            var url = 'inventory/detail/'+inventory_id;
            if ($('#onlyShowDiff').is(':checked')) {
                url = 'inventory/detail/'+inventory_id+'/1';
            }
            listTable.url = url;
            window.location.href = url;
        }
        //]]>
    </script>
    
    <div class="main">
        <div class="main_title">
            <span class="l">盘点列表 >> 商品列表</span><a href="inventory/index" class="return r">返回列表</a>
        </div>
        <div class="blank5"></div>
        <form action="inventory/import" method="POST" enctype="multipart/form-data">
            <div class="search_row">
                盘点编号：<?=$row->inventory_sn; ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                所属仓库：<?=$depot_arr[$row->depot_id]; ?>
                <?php if ($row->shelf_from && $row->shelf_to): ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                货架范围：<?=$row->shelf_from; ?> 至 <?=$row->shelf_to; ?>
                <?php endif; ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                只显示差异商品<input type="checkbox" id="onlyShowDiff" name="onlyShowDiff" onclick="only_show_diff();" <?php if($filter['only_show_diff'] == 1): ?>checked="true"<?php endif; ?> />
                
                <?php if($row->status == 1): ?>
                <span class="r">
                    <input type="hidden" name="inventory_id" value="<?=$row->inventory_id;?>" />
                    <!--<input type="file" name="inventory_file" />-->
                    <!--<input type="submit" class="am-btn am-btn-primary" value="导入盘点清单" />-->
                    <input type="button" class="am-btn am-btn-primary" onclick="javascript:generate_diff(<?=$row->inventory_id;?>);" value="生成差异商品" />
                    <input type="button" class="button10" onclick="javascript:generate_invoice(<?=$row->inventory_id;?>);" value="生成差异出入库单据" />
                </span>
                <?php endif; ?>
            </div>
        </form>
        <div class="blank5"></div>
        <div id="listDiv">
<?php endif; ?>
            
         <!-- 展示排除的储位列表 -->
         <?php if (!empty($exclude_location_list)): ?>
            <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
                <tr class="row">
                    <td colspan="10" style="color:red;">
                        以下储位不在此次盘点范围之内
                    </td>
                </tr>
                <?php foreach($exclude_location_list as $key => $value): ?>
                    <?php if ($key % 10 == 0): ?>
                    <tr class="row">
                    <?php endif; ?>
                        <td><?=$value->location_name; ?></td>
                    <?php if ($key % 10 == 9 || $key == count($exclude_location_list) - 1): ?>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        
        <?php if (count($results) > 0): ?>
            <?php foreach($results as $key => $list): ?>
            <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
                <tr class="row">
                    <td colspan="11">
                        <span>储位编码：<?=$key; ?></span>
                        <?php if($row->status == 1): ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span><input class="am-btn am-btn-primary" value="复盘此储位" onclick="javascript:reset(<?=$list[0]->location_id;?>);" /></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr class="row">
                    <th width="60px">商品ID</th>
                    <th width="150px">商品名称</th>
                    <th width="150px">商品条码</th>
                    <th width="60px">颜色</th>
                    <th width="70px">颜色编码</th>
                    <th width="60px">规格</th>
                    <th width="70px">规格编码</th>
                    <th width="60px">库存数量</th>
                    <th width="60px">已盘数量</th>
                    <th width="90px">盘点人</th>
                    <th width="140px">盘点时间</th>
                </tr>
                
                <?php foreach($list as $p): ?>
                <tr class="row" style="background-color: 
                    <?php 
                        if($p->inventory_number > $p->product_number) echo '#F3FF9E';
                        else if ($p->inventory_number < $p->product_number) echo '#FF0000';
                    ?>
                    ;">
                    <td><?=$p->product_id; ?></td>
                    <td><?=$p->product_name; ?></td>
                    <td><?=$p->provider_barcode; ?></td>
                    <td><?=$p->color_name; ?></td>
                    <td><?=$p->color_sn; ?></td>
                    <td><?=$p->size_name; ?></td>
                    <td><?=$p->size_sn; ?></td>
                    <td><?=$p->inventory_number; ?></td>
                    <td><?=$p->product_number==null ? 0 : $p->product_number; ?></td>
                    <td><?php echo empty($p->update_admin) ? '':$admin_arr[$p->update_admin]->admin_name; ?></td>
                    <td><?php echo empty($p->update_date) ? '':$p->update_date; ?></td>
                </tr>
                <?php endforeach; ?>
                
            </table>
            <?php endforeach; ?>
            
            <div class="blank5"></div>
            <div class="page">
                <?php include(APPPATH.'views/common/page.php') ?>
            </div>
        <?php endif; ?>
            
<?php if($full_page): ?>
        </div>
    </div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>