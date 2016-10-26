<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
    <tr class="row">
        <th class="item_title" style="width:150px; text-align:center;">时间</th>
        <th class="item_title" style="width:200px; text-align:center;">订单状态</th>
        <th class="item_title" style="text-align:center;">内容</th>
        <th class="item_title" style="width:100px; text-align:center;">操作人</th>
    </tr>
    <?php if(!$order_action):?>
    <tr class="row">
        <td colspan=9>无记录</td>
    </tr>
    <?php endif; ?>
    <?php foreach($order_action as $action): ?>
    <tr class="row">
        <td><?php print $action->create_date; ?></td>
        <td><?php print implode('&nbsp;',format_order_status($action,TRUE)); ?></td>
        <td><?php print $action->action_note; ?></td>
        <td><?php print $action->admin_name; ?></td>
    </tr>
    <?php endforeach; ?>
</table>