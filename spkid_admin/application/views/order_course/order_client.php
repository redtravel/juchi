<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
    <tr class="row">
        <th class="item_title">姓名</th>
        <th class="item_title">手机号</th>
        <th class="item_title">邮箱</th>
        <th class="item_title">详细地址</th>
        <th class="item_title">单位</th>
        <th class="item_title">留言</th>
    </tr>
    <?php foreach($order_client as $client): ?>
    <tr class="row">
        <td><?php print $client->name; ?></td>
        <td><?php print $client->mobile_phone; ?></td>
        <td><?php print $client->field_1; ?></td>
        <td><?php print $client->field_2; ?></td>
        <td><?php print $client->field_3; ?></td>
        <td><?php print $client->field_4; ?></td>
    </tr>
    <?php endforeach; ?>
</table>