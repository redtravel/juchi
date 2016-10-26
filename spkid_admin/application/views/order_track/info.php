<script type="text/javascript" src="public/js/lhgdialog/lhgdialog.js"></script>
<link type="text/css" href="/public/js/lhgdialog/skins/default.css" rel="stylesheet" />

<script type="text/javascript">
        
        function print_order(order_id) {
            window.open('order_track/print_order/' + order_id);
        }
        
        function proc_send(order_id, order_sn) {
            var url = 'order_track/proc_send/'+order_id+'/'+order_sn;
            
            $.post(url, {}, function(result){
                result = jQuery.parseJSON(result);
                alert(result.msg);
                if (result.error===0) {
                    window.location.href = 'order_track/send/'+order_sn;
                }
            });
        }
        
        function load_user_panel()
        {
            var dg = new $.dialog({ id:'thepanel',height:300,maxBtn:false, title:'',iconTitle:false,cover:true,html:$('#float_panel')[0] });
            dg.ShowDialog();
            return false;
	}
        
        function print_invoice() {
            var xml = '';
            var sn = '<?php print $format_order->sn; ?>';
            var code = '<?php print $format_order->shipping_code; ?>';
            var codAmount = '<?php print number_format($format_order->codAmount,2,'.',''); ?>';
            var cod = codAmount>0?1:0;
            var rcvPerson = '<?php print $format_order->consignee; ?>';
            var rcvAddress = '<?php print $format_order->address; ?>';
            var rcvMobile = '<?php print $format_order->mobile; ?>';
            var rcvTel = '<?php print $format_order->tel; ?>';
            var bestTime = '<?php print $format_order->best_time; ?>';
            var goods_num = <?php print $format_order->goods_num; ?>;
            var pick_cell = '';
            var city = '<?php print $format_order->city; ?>';
            var codAmount2 = g2b(codAmount);
            if (code == 'ems') {
                var custormPostNo = (cod == 1) ? '代收货款  上海治晨' : '非代收货款  上海治晨';
            } else if (code == 'ems-sh' || code == 'ems-hz') {
                var custormPostNo = (cod == 1) ? '代收货款' : '非代收货款';
            }
            if (xml == '') xml='<data express="public/express/'+code+'.xml">';
            xml+='<order><orderSn>'+sn+'</orderSn><code>'+code+'</code><isCod>'+cod+'</isCod><codAmount>￥'+codAmount+'</codAmount><codAmount2>'+codAmount2+'</codAmount2><custormPostNo>'+custormPostNo+'</custormPostNo><rcvPerson>'+rcvPerson+'</rcvPerson><rcvAddress>'+rcvAddress+'</rcvAddress>';

            if (code == 'yto') {
                if (rcvMobile != '') xml += '<rcvMobile>'+rcvMobile+'</rcvMobile>';
                if (rcvTel != '') xml += '<rcvTel>'+rcvTel+'</rcvTel>';
            }else{
                if (rcvMobile != '') {
                    xml += '<rcvMobile>'+rcvMobile+'</rcvMobile>';
                } else if (rcvTel != '') {
                    xml += '<rcvMobile>'+rcvTel+'</rcvMobile>';
                }
            }
            xml+='<bestTime>'+bestTime+'</bestTime><goodsnum>'+goods_num+'</goodsnum><orderCell>'+pick_cell+'</orderCell><lcity>'+city+'</lcity><city2>'+city+'</city2></order>';
            xml+='</data>';
            flexApp.doPrint(xml);
            return true;
        } // End of print_invoice

        // 将金额转换成大写
        function g2b(str) {
            var p = str.indexOf(".");
            if (p < 0) return '';
            var result = '';
            str = str.substring(0, p);
            var strl = str.length > 5 ? 4 : str.length-1;
            var v_unitArray = new Array('元', '拾', '佰', '仟', '万');
            for (var i = 0; i < str.length; i++) {
                if (str[i] == 0) result = result + str[i].replace(/0/g, '零') + v_unitArray[strl-i];
                if (str[i] == 1) result = result + str[i].replace(/1/g, '壹') + v_unitArray[strl-i];
                if (str[i] == 2) result = result + str[i].replace(/2/g, '贰') + v_unitArray[strl-i];
                if (str[i] == 3) result = result + str[i].replace(/3/g, '叁') + v_unitArray[strl-i];
                if (str[i] == 4) result = result + str[i].replace(/4/g, '肆') + v_unitArray[strl-i];
                if (str[i] == 5) result = result + str[i].replace(/5/g, '伍') + v_unitArray[strl-i];
                if (str[i] == 6) result = result + str[i].replace(/6/g, '陆') + v_unitArray[strl-i];
                if (str[i] == 7) result = result + str[i].replace(/7/g, '柒') + v_unitArray[strl-i];
                if (str[i] == 8) result = result + str[i].replace(/8/g, '捌') + v_unitArray[strl-i];
                if (str[i] == 9) result = result + str[i].replace(/9/g, '玖') + v_unitArray[strl-i];
            }
            return result;
        }
        
	//]]>
</script>
	<?php print form_open_multipart('model/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'), array('order_id'=>$order->order_id));?>
		<table style="border:1px solid #7F9DB9; width:100%;" cellpadding=0 cellspacing=0 bgcolor="#FFFFFF">
                        <tr>
                                <td colspan="4" class="item_title_1">基本信息</td>
			</tr>
			<tr>
                                <td class="item_title" width="100"><strong>系统单号</strong></td>
                                <td class="item_input">
                                    <?php print $order->order_sn; ?>
                                    【查看用户：<a href="#" onclick="load_user_panel();return false;"><?php print $user->user_name; ?></a>】
                                    <?php if (!$order->shipping_true) : ?> <font color="red" style="font-weight:bold;">【虚发】</font> <?php endif; ?>
                                </td>
                                <td class="item_title" width="100"><strong>天猫单号</strong></td>
                                <td class="item_input">
                                    <a target="_blank" href="order_track/edit/<?php print $order_track->order_sn; ?>"><?php print $order_track->track_order_sn; ?></a>
                                </td>
			</tr>
			<tr>
				<td class="item_title" width="100"><strong>订单流程</strong></td>
				<td class="item_input">
				【<?php print $order->source_name ?>】
				【<font color=red><?php print $order->pay_name ?></font>】
				【<?php print $order->shipping_name ?>】
				</td>
				<td class="item_title" width="100"><strong>订单状态</strong></td>
				<td class="item_input">
				<?php print implode('&nbsp;',format_order_status($order,TRUE)); ?>
				<?php if(isset($lock_admin)) print "<font color=red>当前被 {$lock_admin} 锁定</font>"; ?>
				<?php if ($order->shipping_status) print "【运单号:{$order->invoice_no} <a href='javascript:void(0);' onclick='edit_invoice_no()' class='edit'></a>】" ?>
				</td>
			</tr>

			<tr>
				<td class="item_title"><strong>配送地址</strong></td>
				<td class="item_input" colspan="3">
				<?php print '<strong>'.$order->consignee.'</strong>'; ?>
				<?php print $order->province_name ?>
				<?php print $order->city_name ?>
				<?php print $order->district_name ?>
				<?php print $order->address ?>
				<?php if($order->zipcode) print '&nbsp;&nbsp;邮编： '.$order->zipcode ?>
				&nbsp;&nbsp;电话：
				<?php if ($order->mobile) print '<i>'.$order->mobile.'</i>&nbsp;&nbsp' ?>
				<?php if ($order->tel) print '<i>'.$order->tel.'</i>' ?>
				<?php if($order->best_time) print "<font color=red>&nbsp;&nbsp;最佳送货时间：{$order->best_time}</font>" ?>
				<?php if ($perms['edit_order']): ?>
				<a class="edit" href="order/consignee/<?php print $order->order_id; ?>" title="编辑"></a>
				<?php endif ?>
				</td>
			</tr>
			<tr>
				<td class="item_title"><strong>运费</strong></td>
				<td class="item_input">
				<?php print $order->shipping_fee ?>
					<?php if ($order->shipping_fee>0): ?>
						<?php print form_button('btn_free_shipping_fee','免运费','onclick="free_shipping_fee();" '.($perms['edit_order']?'':'disabled')) ?>
					<?php else: ?>
						<?php print form_button('btn_reset_shipping_fee','重新计算运费','onclick="reset_shipping_fee();" '.($perms['edit_order']?'':'disabled')) ?>
					<?php endif ?>
					<?php print form_input('new_shipping_fee',$order->shipping_fee, 'size="3" '.($perms['edit_order']?'':'disabled')) ?>
					<?php print form_button('btn_update_shipping_fee','更新运费','onclick="update_shipping_fee();" '.($perms['edit_order']?'':'disabled')) ?>
				</td>
				<td class="item_title"><strong>实际运费</strong></td>
				<td class="item_input">
				<?php print $order->real_shipping_fee ?>
				<?php if ($order->shipping_status) print "<a href='javascript:void(0);' onclick='edit_real_shipping_fee()' class='edit'></a>" ?>
				</td>
				</td>
			</tr>

			<tr>
				<td class="item_title"><strong>使用余额</strong></td>
				<td class="item_input">
					<?php if ($order->order_amount>0): ?>
					<?php print form_input('balance_amount','','class="textbox" size="5" '.($perms['edit_order']?'':'disabled')); ?>
					<?php print form_button('balance_submit','支付','onclick="pay_balance();" '.($perms['edit_order']?'':'disabled')); ?>
					<?php else: ?>
					已全额支付。
					<?php endif ?>
					可用余额： <?php print fix_price($user->user_money); ?>
				</td>
				<td class="item_title">使用现金券</td>
				<td class="item_input">
					<?php if ($voucher_payment): ?>
						<?php printf('已使用现金券 %s , 抵用 %.2f 元', $voucher_payment->payment_account, $voucher_payment->payment_money) ?>
						<?php if ($perms['edit_order']): ?>
							<a href="javascript:remove_voucher()">[ 移除 ]</a>
						<?php endif ?>
					<?php else: ?>
						<select name="available_voucher" onchange="choice_voucher();" <?php if(!$perms['edit_order']) print 'disabled'; ?>>
							<option value="">可用现金券</option>
							<?php foreach ($voucher_list as $voucher): ?>
								<option value="<?php print $voucher->voucher_sn; ?>"><?php print $voucher->voucher_sn.' '.$voucher->voucher_name ?></option>
							<?php endforeach ?>
						</select>
						<?php print form_input('voucher_sn','','class="textbox" '.($perms['edit_order']?'':'disabled')); ?>
						<?php print form_button('voucher_submit','支付','onclick="pay_voucher();" '.($perms['edit_order']?'':'disabled')); ?>
					<?php endif ?>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="item_title_1">
					其它信息
					<?php if ($perms['edit_order']): ?>
					<a class="edit" href="order/other/<?php print $order->order_id; ?>" title="编辑"></a>
					<?php endif ?>
				</td>
			</tr>
			<tr>
				<td class="item_title"><strong>发票抬头</strong></td>
				<td class="item_input"><?php print $order->invoice_title?$order->invoice_title:'[ 不要发票 ]' ?></td>
				<td class="item_title"><strong>发票内容</strong></td>
				<td class="item_input"><?php print $order->invoice_content ?></td>
			</tr>
			<tr>
				<td class="item_title"><strong>客户留言</strong></td>
				<td class="item_input"><?php print $order->user_notice ?></td>
				<td class="item_title"><strong>客服回复</strong></td>
				<td class="item_input"><?php print $order->to_buyer ?></td>
			</tr>

			<tr>
				<td colspan="4" class="item_title_1">
					订单商品
					<?php if ($perms['edit_order']): ?>
					<a class="edit" href="order/product/<?php print $order->order_id; ?>" title="编辑"></a>
					<?php endif ?>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="item_input" style="padding:0;">
					<?php include ROOT_PATH.'application/views/order/order_product_info.php' ?>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="item_title_1" >
					支付记录
				</td>
			</tr>
			<tr>
				<td colspan="4" class="item_input" style="padding:0;">
					<?php include ROOT_PATH.'application/views/order/order_payment.php' ?>
				</td>
			</tr>
			
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
                        <tr>
                            <td height="40" colspan="4" align="center">
                                <input type="button" name="bt_print_invoice" value="控件加载中..." class="am-btn am-btn-primary" onclick="javascript:print_invoice();" disabled="disabled"/>
                                <div id="flashContent" style="display:none;">运单打印控件</div>
                                <script type="text/javascript">
                                        swfobject.embedSWF("public/js/autoPrinter-1.1.16.swf", "flashContent","0", "0", "10.0.0","",{bridgeName:"expressBridge"});
                                        var flexApp;
                                        var initCallback = function() {			
                                                flexApp = FABridge.expressBridge.root();
                                                $(':input[name=bt_print_invoice]').val('打印运单').attr('disabled',false);
                                        }
                                        FABridge.addInitializationCallback( "expressBridge", initCallback );
                                </script>
                                <input type="button" class="am-btn am-btn-primary" value="打印装箱单" onclick="javascript:print_order(<?php print $order->order_id; ?>);" />
                                <?php if (!$order->is_ok) : ?>
                                <input type="button" class="am-btn am-btn-primary" value="确认发货" onclick="javascript:proc_send(<?php print $order->order_id; ?>, '<?php print $order->order_sn; ?>');" />
                                <?php endif; ?>
                            </td>
			</tr>
		</table>
                
	<?php print form_close();?>

<div id="float_panel" style="display:none;">
<table border="0" width="100%">
<caption>
<strong> 购货人信息 </strong>
</caption>
<tr>
<td> 电子邮件 </td>
<td>
<a href="mailto:<?php print $user->email ?>"><?php print $user->email ?></a>
</td>
</tr>
<tr>
<td> 手机 </td>
<td> <?php print $user->mobile ?> </td>
</tr>
<tr>
<td> 账户余额 </td>
<td> ￥<?php print $user->user_money ?>元 </td>
</tr>
<tr>
<td> 消费积分 </td>
<td> <?php print $user->pay_points ?> </td>
</tr>
</table>
<table border="0" width="100%">
<caption>
<strong> 收货人：<?php print $order->consignee ?> </strong>
</caption>
<tr>
<td> 地址 </td>
<td> <?php print $order->province_name ?> <?php print $order->city_name ?> <?php print $order->district_name ?> <?php print $order->address ?> </td>
</tr>
<tr>
<td> 邮编 </td>
<td> <?php print $order->zipcode ?> </td>
</tr>
<tr>
<td> 电话 </td>
<td> <?php print $order->tel ?> </td>
</tr>
<tr>
<td> 手机 </td>
<td> <?php print $order->mobile ?> </td>
</tr>
</table>
</div>
<div id="h_shipping" style="display:none;">
<table style="width:100%;">
<tr>
<td height="30" align="right">运单号：</td><td><input type="text" name="invoice_no"></td>
</tr>
<tr>
<td height="30" align="right">发货类型：</td>
<td><!--<input type="radio" value="1" name="shipping_true" checked/>实发-->
<input type="radio" value="0" name="shipping_true" style="margin-left:10px;" checked/>虚发
</td>
</tr>
<tr>
<td height="30">&nbsp;</td>
<td><input type="button"  value="确定" name="sb_shipping"  class="am-btn am-btn-secondary" onclick="order_shipping();"></td>
</tr>
</table>
</div>
