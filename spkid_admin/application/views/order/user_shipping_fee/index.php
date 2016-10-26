<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript" src="public/js/toggle.js"></script>
    <script type="text/javascript">
        //	<![CDATA[
        listTable.url = 'order_user_shipping_fee/index';
        function search(){
            listTable.filter['return_sn'] = $.trim($('input[name=return_sn]').val());
            listTable.filter['order_sn'] = $.trim($('input[name=order_sn]').val());
            listTable.filter['finance_status'] = $('select[name=finance_status]').val();
            listTable.filter.page = '1';
            listTable.loadList();
        }
        //]]>
        function batch_check(){
            if(!confirm("确认财审吗？<br>财审成功后会为用户的账号中充值指定的金额,请谨慎操作!")){
                return;
            }
            var arr = new Array();
            var i = 0;
            $("input[name='finance_check']:checked").each(function(){
                arr[i]=this.value;
                i+=1;
            });
            url = "order_user_shipping_fee/batch_check";
            $.ajax({
                url : url,
                data : {return_ids : arr},
                dataType : 'json',
                type : 'POST',
                success : function(result){
                    if (result.msg) {alert(result.msg)};
                    if (result.err == 0) {
                        search();
                    }
                }
            });
        }
        
        
        function check(obj,id){
            if(!confirm("确认财审吗？<br>财审成功后会为用户的账号中充值指定的金额,请谨慎操作!")){
                return;
            }
            url = "order_user_shipping_fee/check/"+id;
            $.ajax({
                url : url,
                dataType : 'json',
                type : 'POST',
                success : function(result){
                    if (result.msg) {alert(result.msg)};
                    if (result.err == 0) {
                        $(obj).parent().parent().find('td=[name=admin_name]').html(result.admin_name);
                        $(obj).parent().parent().find('td=[name=finance_date]').html(result.finance_date);
                        $(obj).next("a").remove();
                        $(obj).remove();
                    }
                }
            });
        }
        function del(obj,id){
            if(!confirm("确定删除!")){
                return;
            }
            url = "order_user_shipping_fee/delete/"+id;
            $.ajax({
                url : url,
                dataType : 'json',
                type : 'POST',
                success : function(result){
                    if (result.msg) {alert(result.msg)};
                    if (result.err == 0) {
                        $(obj).parent().parent().remove();
                    }
                }
            });
        }
        function select_all(source_obj,type,obj_name){
            if(type === 1){//全部选择//取消选择
                if(source_obj.value === "全部选择"){
                    source_obj.value = "取消选择";
                    $("input[name='"+obj_name+"']").each(function(){
                        this.checked=true;
                    });
                }else{
                    source_obj.value = "全部选择";
                    $("input[name='"+obj_name+"']").each(function(){
                        this.checked=false;
                    });
                }
            }else if(type === -1){//反向选择
                $("input[name='"+obj_name+"']").each(function(){
                    if(this.checked===true){
                        this.checked=false;
                    }else{
                        this.checked=true;
                    }
                });
            }
        }
    </script>
    <div class="main">
        <div class="main_title"><span class="l">用户运费管理</span>
            <span class="r">
                <?php if($perm_check): ?>
                    <a class="icon_check" href="javascript:void(0);" onclick="batch_check();" title="批量财审">批量财审</a>
                <?php endif; ?>
            </span>
        </div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
                退货单号：
                <input type="text" name="return_sn" id="return_sn" />
                源订单号：
                <input type="text" name="order_sn" id="order_sn" />
                财审状态：
                <select name="finance_status">
                    <option value="0">全部</option>
                    <option value="1">已财审</option>
                    <option value="2">未财审</option>
                    <option value="3">退货单未财审</option>
                </select>
                <input type="submit" class="am-btn am-btn-primary" value="搜索" />
            </form>
        </div>
        <div class="blank5"></div>
            <div id="listDiv">
<?php endif; ?>
<?php if(!$full_page): ?>
                <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
                    <tr>
                        <td colspan="6" class="topTd"></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align:left;">
                            <input type="button" onclick="select_all(this,1,'finance_check');" value="全部选择"/>
                            <input type="button" onclick="select_all(this,-1,'finance_check');" value="反向选择"/>
                            <span style="color: red;">使用按钮实现全选和反选功能</span>
                        </td>
                    </tr>
                    <tr class="row">
                        <th>财审状态</th>
                        <th>退货单号</th>
                        <th>订单号</th>
                        <th>原订单地址</th>
                        <th>退货人</th>
                        <th>退货原因</th>
                        <th>退货单创建时间</th>
                        <th>快递公司</th>
                        <th>运费金额</th>
                        <th>财审人</th>
                        <th>财审时间</th>
                        <th width="120px;">操作</th>
                    </tr>
                    <?php foreach($list as $row): ?>
                    <tr class="row">
                        <td>
                            <!-- 有财审权限 -->
                            <?php if($perm_check): ?>
                                <?php if($row->finance_admin==0): ?>
                                    <?php if($row->return_finance_admin==0): ?>
                                        退货单未财审
                                    <?php endif; ?>
                                    <?php if($row->return_finance_admin!=0): ?>
                                        <input type="checkbox" name="finance_check" value="<?php echo $row->return_id; ?>" />
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if($row->finance_admin!=0): ?>
                                    已财审
                                <?php endif; ?>
                            <?php endif; ?>
                            <!-- 无财审权限 -->
                            <?php if(!$perm_check): ?>
                                <?php if($row->finance_admin==0): ?>
                                    未财审
                                <?php endif; ?>
                                <?php if($row->finance_admin!=0): ?>
                                    已财审
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php print $row->return_sn; ?></td>
                        <td><?php echo  $row->order_sn; ?></td>
                        <td><?php echo  $row->address; ?></td>
                        <td><?php echo  $row->consignee; ?></td>
                        <td><?php echo  $row->return_reason; ?></td>
                        <td><?php echo  $row->create_date; ?></td>
                        <td><?php echo  $row->shipping_name; ?></td>
                        <td><?php echo  $row->user_shipping_fee; ?></td>
                        <td name="admin_name"><?php echo  $row->admin_name; ?></td>
                        <td name="finance_date"><?php echo  $row->finance_date; ?></td>
                        <td>
                            <a class="edit" href="order_user_shipping_fee/edit/<?php echo  $row->return_id; ?>" title="<?php echo $row->finance_admin==0&&$perm_edit?'编辑':'查看'; ?>"></a>
                            <?php if($row->finance_admin==0&&$perm_delete): ?>
                                <a class="del" href="javascript:void(0);" onclick="del(this,<?php echo  $row->return_id; ?>)" title="删除"></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="6" class="bottomTd"> </td>
                    </tr>
                </table>
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