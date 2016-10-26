<table class="dataTable" cellpadding=0 cellspacing=0 rel="3" style="margin-top:0">
    <tr class="row">
        <th class="item_title" style="width:150px; text-align:center;">支付时间</th>
        <th class="item_title" style="width:100px; text-align:center;">支付方式</th>
        <th class="item_title" style="width:100px; text-align:center;">支付金额</th>
        <th class="item_title" style="text-align:center;">支付帐号</th>
        <th class="item_title" style="width:100px; text-align:center;">交易号</th>
        <th class="item_title" style="width:250px; text-align:center;">备注</th>
        <th class="item_title" style="width:100px; text-align:center;">操作人</th>
        <th class="item_title" style="width:100px; text-align:center;">操作</th>
    </tr>
    <?php foreach($order_payment as $payment): ?>
    <tr class="row">
        <td><?php print $payment->payment_date; ?></td>
        <td><?php print $payment->pay_name; ?></td>
        <td><?php print $payment->payment_money; ?></td>
        <td><?php print $payment->payment_account; ?></td>
        <td><?php print $payment->trade_no; ?></td>
        <td><?php print $payment->payment_remark; ?></td>
        <td><?php print $payment->admin_name; ?></td>
        <td>
            <?php if ($perms['edit_pay'] && $payment->payment_admin!=-1 && !in_array($payment->pay_id, array(PAY_ID_VOUCHER,PAY_ID_BALANCE)) ): ?>
                <a href="javascript:remove_payment(<?php print $payment->payment_id; ?>)">删除</a>
            <?php endif ?>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php if ($perms['edit_pay']): ?>
        <td></td>
        <td>
            <?php print form_dropdown('payment_pay_id',get_pair($available_pay,'pay_id','pay_name'),$order->pay_id); ?>
        </td>
        <td><?php print form_input('payment_payment_money','','class="textbox"') ?></td>
        <td><?php print form_input('payment_payment_account','','class="textbox"') ?></td>
        <td><?php print form_input('payment_trade_no','','class="textbox"') ?></td>
        <td><?php print form_input('payment_payment_remark','','class="textbox"') ?></td>
        <td></td>
        <td>
            <?php print form_button('op_payment','支付','onclick="javascript:add_payment();"') ?>
        </td>
    <?php endif ?>
</table>