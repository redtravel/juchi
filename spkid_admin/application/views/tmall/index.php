<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <base href="<?php echo base_url(); ?>">
        <title>天猫商品抓取</title>
        <link rel="stylesheet" type="text/css" href="public/js/easyui/themes/default/easyui.css">
        <link rel="stylesheet" type="text/css" href="public/js/easyui/themes/icon.css">
        <link rel="stylesheet" type="text/css" href="public/js/easyui/style.css">
        <script type="text/javascript" src="public/js/easyui/jquery.min.js"></script>
        <script type="text/javascript" src="public/js/easyui/jquery.easyui.min.js"></script>
        <script type="text/javascript" src="public/js/easyui/locale/easyui-lang-zh_CN.js"></script>   
    </head>
    <body>
        <table id="dg" data-options="fit:true" title="天猫商品抓取" class="easyui-datagrid" style="height:250px"
               url="tmall/item_list"
               toolbar="#toolbar" pagination="true" pageSize="30"
               rownumbers="true" fitColumns="true" singleSelect="true">
            <thead>
                <tr>
                    <th field="num_iid" width="20" formatter="formatNumIid">天猫ID</th>
                    <th field="title" width="50" formatter="formatTitle">商品名称</th>
                    <th field="nick" width="20">卖家昵称</th>
                    <th field="price" width="20" formatter="formatPrice">价格</th>
                    <th field="sync_status" width="10" formatter="formatSyncStatus">同步状态</th>
                    <th field="check_status" width="10" formatter="formatCheckStatus">入库状态</th>
                </tr>
            </thead>
        </table>
        <div id="toolbar">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newJob()">新建</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="destroyItem()">移除</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="editItem()">编辑</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-reload" plain="true" onclick="syncItem()">同步</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-redo" plain="true" onclick="reloadGallery()">图片重载</a>

            <select name="search_sync_status"  panelHeight="auto" style="width:100px">
                <option value="">同步状态</option>
                <option value="t">已同步</option>
                <option value="f">未同步</option>
            </select>
            <select name="search_check_status"  panelHeight="auto" style="width:100px">
                <option value="">入库状态</option>
                <option value="t">已入库</option>
                <option value="f">未入库</option>
            </select>
            <?php print form_dropdown('provider_id', array('供应商')+get_pair($all_provider,'provider_id','provider_name'));?>
            <a href="javascript:doSearch();" class="easyui-linkbutton" iconCls="icon-search">检索</a>
        </div>

        <div id="dlg" class="easyui-dialog" style="width:500px;height:380px;padding:10px 20px"
             closed="true" buttons="#dlg-buttons">
            <div class="ftitle">店铺ID和商品ID列表任选其一</div>
            <form id="fm" method="post" novalidate>
                <div class="fitem">
                    <label>店铺三级域名:</label>
                    <input name="shop">
                </div>
                <div class="fitem">
                    如店铺域名是http://fivepeas.tmall.com/，则输入fivepeas
                </div>
                <div class="fitem">
                    <label style="vertical-align: top;">商品ID列表:<br/>（每行一个）</label>
                    <textarea name="goods" rows="8" cols="30"></textarea>
                </div>
                <div class="fitem">
                    <label style="vertical-align: top;">供应商</label>
                    <?php print form_dropdown('provider_id', get_pair($all_provider,'provider_id','provider_name'));?>
                </div>
            </form>
        </div>
        <div id="dlg-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveJob()">保存</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')">取消</a>
        </div>
        
        <div id="item_dlg" class="easyui-dialog" style="width:880px;height:580px;padding:10px 20px"
             closed="true" buttons="#edit_dlg-buttons" data-options="modal:true">
            
        </div>
        <div id="edit_dlg-buttons">
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveItem(false)">保存</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="saveItem(true)">入库</a>
            <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#item_dlg').dialog('close')">取消</a>
        </div>
        <script type="text/javascript">
            var url;
            var submitting = false;
            var gallery_reloading = false;
            function newJob() {
                $('#dlg').dialog('open').dialog('setTitle', '新建任务');
                $('#fm').form('clear');
                url = 'tmall/insert';
                submitting=false;
            }
            function saveJob() {
                $('#fm').form('submit', {
                    url: url,
                    onSubmit: function() {
                        if(submitting==true){
                            $.messager.show({
                                title: 'Error',
                                msg: "请不要重复提交"
                            });
                            return false;
                        }
                        if ($.trim($('input[name=shop]').val()) == '' && $.trim($('textarea[name=goods]').val()) == '') {
                            alert('请填写采集条件');
                            return false;
                        }
                        submitting = true;
                        $.messager.show({
                            title: '提醒',
                            msg: '系统正在努力抓取数据，所需时间较长(大约30S)，请耐心等待,不要关闭弹窗'
                        });
                    },
                    success: function(result) {
                        var result = eval('(' + result + ')');
                        if (result.msg) {
                            $.messager.show({
                                title: 'Error',
                                msg: result.msg
                            });
                        } else {
                            $('#dlg').dialog('close');        // close the dialog
                            $('#dg').datagrid('reload');    // reload the user data
                        }
                    }
                });
            }
            function destroyItem() {
                var row = $('#dg').datagrid('getSelected');
                if (!row) {
                    $.messager.show({
                        title: 'Error',
                        msg: '请选择要删除的记录'
                    });
                    return false;
                }

                $.messager.confirm('Confirm', '确认删除此项目?', function(r) {
                    if (!r)
                        return false;
                    $.post('tmall/delete', {num_iid: row.num_iid}, function(result) {
                        if (result.msg) {
                            $.messager.show({// show error message
                                title: 'Error',
                                msg: result.msg
                            });
                        }
                        if(!result.err){
                            $('#dg').datagrid('reload');    // reload the user data
                        }
                    }, 'json');
                });

            }
            
            function editItem()
            {
                var row = $('#dg').datagrid('getSelected');
                if (!row) {
                    $.messager.show({
                        title: 'Error',
                        msg: '请选择要编辑的记录'
                    });
                    return false;
                }
                if (typeof CKEDITOR != 'undefined' && CKEDITOR.instances['desc']) {
                    CKEDITOR.instances['desc'].destroy();
                }
                $('#item_dlg').dialog({'href':'tmall/edit/'+row.num_iid,title:'编辑天猫商品信息'}).dialog('open');
                url='tmall/save/'+row.num_iid;
                submitting=false;
            }
            
            /**
             * 保存信息
             */
            function saveItem(ruku)
            {
                $('#item_fm').form('submit', {
                    url: url+(ruku?'/1':''),
                    onSubmit: function() {
                        if(submitting==true){
                            $.messager.show({
                                title: 'Error',
                                msg: "请不要重复提交"
                            });
                            return false;
                        }
                        submitting=true;
                        $(':hidden[name=sku_del]').val(sku_del.join('|'));
                        var tmp_alias = new Array();
                        for(i in sku_alias){
                            if(i.substr(0,6)=='color_' && i.substr(6)==sku_alias[i]) continue;
                            if(i.substr(0,5)=='size_' && i.substr(5)==sku_alias[i]) continue;
                            tmp_alias.push(i+'$$$'+sku_alias[i]);
                        }
                        $(':hidden[name=sku_alias]').val(tmp_alias.join('|||'));
                    },
                    success: function(result) {
                        submitting = false;
                        var result = eval('(' + result + ')');
                        if (result.msg) {
                            $.messager.show({
                                title: 'Error',
                                msg: result.msg
                            });
                        } else {
                            $('#item_dlg').dialog('close');        // close the dialog
                            $('#dg').datagrid('reload');    // reload the user data
                        }
                    }
                });
            }
            
            /**
             * 同步按钮
             */
            function syncItem()
            {
                var row = $('#dg').datagrid('getSelected');
                if (!row) {
                    $.messager.show({
                        title: 'Error',
                        msg: '请选择要编辑的记录'
                    });
                    return false;
                }
                window.open('http://detail.tmall.com/item.htm?id='+row.num_iid);
            }
            
            /**
             * 图片重载
             * @returns {undefined}
             */
            function reloadGallery()
            {
                if(gallery_reloading){
                    $.messager.show({
                        title: 'Error',
                        msg: '相同的进程正在进行，请稍候操作...'
                    });
                    return false;
                }
                var row = $('#dg').datagrid('getSelected');
                if (!row) {
                    $.messager.show({
                        title: 'Error',
                        msg: '请选择要编辑的记录'
                    });
                    return false;
                }
                $.messager.confirm('Confirm', '确认重载图片?', function(r) {
                    if (!r)
                        return false;
                    gallery_reloading = true;
                    $.post('tmall/reload_gallery', {num_iid: row.num_iid}, function(result) {
                        gallery_reloading = false;
                        if (result.msg) {
                            $.messager.show({// show error message
                                title: 'Error',
                                msg: result.msg
                            });
                        }
                        if(!result.err){
                            $('#dg').datagrid('reload');    // reload the user data
                        }
                    }, 'json');
                });
                
            }
            
            function doSearch()
            {
                $('#dg').datagrid({queryParams:{
                        sync_status:$('select[name=search_sync_status]').val(),
                        check_status:$('select[name=search_check_status]').val(),
                        provider_id:$('select[name=provider_id]').val(),
                }});
            }

            function formatNumIid(val)
            {
                return '<a href="http://detail.tmall.com/item.htm?&id='+val+'" target="_blank">'+val+'</a>';
            }
            function formatSyncStatus(val, row) {
                switch (val) {
                    case '0':
                        return '<a href="http://detail.tmall.com/item.htm?id='+row.num_iid+'" target="_blank">待同步</a>';
                    case '1':
                        return '已同步';
                }
            }
            function formatCheckStatus(val,row) {
                switch (val) {
                    case '0':
                        return '待入库';
                    case '1':
                        return '<a href="product/edit/'+row.product_id+'" target="_blank">已入库</a>';
                }
            }
            function formatPrice(val, row)
            {
                if(row.tmall_price){
                  return '猫：<span style="color:red;">'+row.tmall_price + '</span> 柜：<span style="color:blue;">'+row.reserve_price+'</span> Ｍ：<span style="color:green;">'+row.shop_price+'</span>';  
                }
                return '';
            }
            function formatTitle(val, row)
            {
                if(row.sync_status==0 || row.check_status==1 || row.has_sku){
                    return val;
                }
                return '<span style="color:red; text-decoration:line-through">'+val+'</span>';
            }
        </script>
    </body>
</html>