<?php include APPPATH.'views/common/rf_header.php'; ?>
    <div id="listDiv">
        <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0 >
            <tr class="row">
                <th>请选择类型</th>
            </tr>
            <tr class="row">
                <td>
                    <a href="/return_onshelf/select/0">商品移储</a>
                </td>
            </tr>
            <tr class="row">
                <td>
                    <a href="/return_onshelf/select/1">退货上架</a>
                </td>
            </tr>
        </table>
    </div>
<?php include_once(APPPATH.'views/common/rf_footer.php'); ?>