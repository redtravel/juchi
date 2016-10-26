<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
    <tr class="row">
        <th class="item_title" style="width:150px; text-align:center;">时间</th>
        <th class="item_title" style="width:200px; text-align:center;">类型</th>
        <th class="item_title" style="text-align:center;">内容</th>
        <th class="item_title" style="width:100px; text-align:center;">操作人</th>
    </tr>

    <?php foreach($order_advice as $advice): ?>
    <tr class="row">
        <td><?php print $advice->advice_date; ?></td>
        <td><span style="display:inline-block;width:15px;height:15px;background-color:<?php print $advice->type_color ?>;">&nbsp;</span>
        <?php print $advice->type_name; ?></td>
        <td><?php print $advice->advice_content; ?></td>
        <td><?php print $advice->admin_name; ?></td>
    </tr>
    <?php endforeach; ?>
</table>