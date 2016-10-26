<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript" src="public/js/toggle.js"></script>
    <script type="text/javascript">
        $(function(){
            $('input[type=text][name=create_date_start]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
            $('input[type=text][name=create_date_end]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
    	});
        //	<![CDATA[
        listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
        listTable.filter.page = '<?php echo $filter['page']; ?>';
        listTable.url = 'purchase_batch';
        function search(){
            //listTable.filter['batch_name'] = $.trim($('input[name=batch_name]').val());
            listTable.filter['batch_code'] = $.trim($('input[name=batch_code]').val());
            listTable.filter['provider_id'] = $.trim($('select[name=provider_id]').val());
            listTable.filter['brand_id'] = $.trim($('select[name=brand_id]').val());
            //listTable.filter['batch_status'] = $.trim($('select[name=batch_status]').val());
            //listTable.filter['plan_arrive_date'] = $.trim($('input[name=plan_arrive_date]').val());
            listTable.filter['create_admin'] = $.trim($('input[name=create_admin]').val());
            listTable.filter['create_date_start'] = $.trim($('input[name=create_date_start]').val());
            listTable.filter['create_date_end'] = $.trim($('input[name=create_date_end]').val());
            listTable.filter.page = '1';
            listTable.loadList();
        }
        //]]>
    </script>
    <div class="main">
        <div class="main_title"><span class="l">批次管理列表</span><span class="r"><a href="purchase_batch/add" class="add">新增</a></span>
        </div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
                <!--批次名称：
                <input type="text" name="batch_name" id="batch_name"/>-->
                批次号：
                <input type="text" name="batch_code" id="batch_code" />
                供应商<select name="provider_id" data-am-selected="{searchBox: 1,maxHeight: 300}">
                    <option value="">供应商</option>
                    <?php foreach($provider_list as $provider) print "<option value='{$provider->provider_id}'>{$provider->provider_code}-{$provider->provider_name}</option>"?>
                </select>
                品牌<?php print form_dropdown('brand_id',get_pair($brand_list,'brand_id','brand_name', array(''=>'品牌')),'','data-am-selected="{searchBox: 1,maxHeight: 300}"'); ?>
                <!--批次状态：
                <select name="batch_status">
                    <option value=>全部</option>
                    <option value="1">开启</option>
                    <option value="0">关闭</option>
                </select>-->
                创建日期：
                <input type="text" name="create_date_start" id="create_date_start" />
                <input type="text" name="create_date_end" id="create_date_end" />
                创建人：
                <input type="text" name="create_admin" id="create_admin" />
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
                        <th>批次名称</th>
                        <th>批次号</th>
                        <th>供应商编码(合作方式)</th>
                        <th>品牌</th>
                        <th>预计收货数量</th>
                        <th>预计收货时间</th>
                        <th>批次类型</th>
                        <th>销售类型</th>
                        <!-- <th>批次状态</th> -->
                        <th>创建人</th>
                        <th>创建时间</th>
                        <th>是否结算</th>
                        <th>结算时间</th>
                        <th width="120px;">操作</th>
                    </tr>
                    <?php foreach($list as $row): ?>
                    <tr class="row">
                        <td><?php print $row->batch_name; ?></td>
                        <td><?php echo  $row->batch_code; ?></td>
                        <td><?php echo  $providers[$row->provider_id]->provider_code.'('.$cooperation[$providers[$row->provider_id]->provider_cooperation].')'; ?></td>
                        <td><?php echo  $row->brand_name; ?></td>
                        <td><?php echo  $row->plan_num; ?></td>
                        <td><?php echo  $row->plan_arrive_date; ?></td>
                        <td><?php echo  $batch_type[$row->batch_type]; ?></td>
                        <td><?php if ($row->is_consign ==1):?>虚库销售<?php else: ?>实库销售<?php endif; ?></td>
                        <!-- 
                        <td>
                            <span class="<?php if($row->batch_status == 1) print 'yes'; else print 'no'; ?>ForGif" style="cursor:pointer;" onclick="toggle(this,'purchase_batch/toggle',<?php echo  $row->batch_id; ?>,'batch_status','请确认操作?');">
                            </span>
                        </td>
                         -->
                        <td><?php echo  $row->realname; ?></td>
                        <td><?php echo  $row->create_date; ?></td>
                        <td>
                        <?php if($row->is_reckoned == 1): ?> <span class="yesForGif"></span>
                        <?php else:?> <span class="noForGif"></span>
                        <?php endif;?>
                        </td>
                        <td><?php echo  $row->reckon_date; ?></td>
                        <td>
                             <?php 
                                if($perm_edit&&$row->batch_status==1) 
                                    print '<a class="edit" href="purchase_batch/edit/'.$row->batch_id.'" title="编辑"></a>';
                                else 
                                    print '<a class="edit" href="purchase_batch/view/'.$row->batch_id.'" title="查看"></a>';
                             ?>
                            <?php if($perm_delete):?>
                                <a class="del" href="javascript:void(0)" rel="purchase_batch/delete/<?php print $row->batch_id; ?>" title="删除" onclick="do_delete(this)"></a>
                            <?php endif;?>
                            <?php if($perm_edit&&$row->batch_status==1):?>
                                <a class="add_batch" href="purchase/add/<?php print $row->provider_id; ?>/<?php print $row->batch_id; ?>" title="添加采购单"></a>
                            <?php endif;?>
                            
                            <?php if($perm_lock && $row->batch_status==1 && empty($row->lock_admin)):?>
                                <a href="purchase_batch/lock/<?php print $row->batch_id; ?>" >锁定</a>
                            <?php endif;?>
                            <?php if($perm_lock && $row->batch_status==1 && $row->lock_admin == $admin_id):?>
                                <a href="purchase_batch/unlock/<?php print $row->batch_id; ?>" >解锁</a>
                            <?php endif;?>
                            <?php if($perm_reckon && $row->batch_status==1 && $row->is_reckoned==0):?>
                                <a href="purchase_batch/reckon/<?php print $row->batch_id; ?>" >设置已结算</a>
                            <?php endif;?>
                        </td>
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