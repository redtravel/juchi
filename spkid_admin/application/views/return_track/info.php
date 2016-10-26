<script type="text/javascript">
        function proc_recive(apply_id) {
            var invoice_no = $.trim($('input[type=text][name=invoice_no]').val());
            var url = 'return_track/proc_recive';
            
            $.post(url, {apply_id:apply_id}, function(result){
                result = $.parseJSON(result);
                if (result.error === 1) {
                    alert(result.result);
                } else {
                    alert('退货申请成功！');
                    window.location.href = 'return_track/recive/'+invoice_no;
                }
            });
        }
</script>

<div  id="order_return" style="background-color:#fff;">
        <table class="dataTable" width="100%" cellpadding="0" cellspacing="0" style=" margin-top:10;">
          <tr>
                <th colspan="4">基本信息<?php if ($apply_info['order_type'] == 1) :?><span style="color:red">【第三方直发】</span><?php endif;?></th>
          </tr>
          <tr>
                <td width="18%"><div align="right"><strong>申请单号：</strong></div></td>
                <td width="34%"><?php print $apply_info['apply_id']; ?></td>
                <td width="15%"><div align="right"><strong>关联订单号：</strong></div></td>
                <td><?php print $apply_info['order_sn']; ?> <a href="/order/info/<?php print $apply_info['order_id']; ?>" target="_blank">查看关联订单</a></td>
          </tr>
          <tr>
                <td width="18%"><div align="right"><strong>申请人姓名：</strong></div></td>
                <td width="34%"><?php print $apply_info['sent_user_name']; ?></td>
                <td width="15%"><div align="right"><strong>快递运单号：</strong></div></td>
                <td><?php print $apply_info['shipping_name']; ?>&nbsp;&nbsp;<?php print $apply_info['invoice_no']; ?></td>
          </tr>
          <tr>
                <td width="18%"><div align="right"><strong>申请人联系方式（手机/座机）：</strong></div></td>
                <td width="34%"><?php print $apply_info['mobile']; ?><?php if ($apply_info['tel']) :  print $apply_info['tel']; endif;?></td>
                <td width="15%"><div align="right"><strong>垫付运费：</strong></div></td>
                <td><?php print $apply_info['shipping_fee']; ?></td>
          </tr>
         </table>

        <table class="dataTable" width="100%" cellpadding="0" cellspacing="0">
          <tr>
                <th colspan="11" scope="col">商品信息</th>
            </tr>
          <tr>
                <td scope="col"><div align="center"><strong>商品名称 [ 品牌 ]</strong></div></td>
                <td scope="col"><div align="center"><strong>商品款号</strong></div></td>
                <td scope="col"><div align="center"><strong>供应商货号</strong></div></td>
                <td scope="col"><div align="center"><strong>价格</strong></div></td>
                <td scope="col"><div align="center"><strong>可退数量</strong></div></td>
                <td scope="col"><div align="center"><strong>申请退货数量</strong></div></td>
                <td scope="col"><div align="center"><strong>颜色尺码</strong></div></td>
                <td scope="col"><div align="center"><strong>退货原因</strong></div></td>
                <td scope="col"><div align="center"><strong>退货描述</strong></div></td>
                <td scope="col"><div align="center"><strong>供应商审核</strong></div></td>
          </tr>
          <?php foreach ($apply_product as $product): ?>
          <tr class="tr_product">
                <td><?php print $product['product_name']; ?> [ <?php print $product['brand_name']; ?> ]</td>
                <td align="center"><?php print $product['product_sn']; ?></td>
                <td align="center"><?php print $product['provider_productcode']; ?></td>
                <td align="center"><?php print $product['product_price']; ?> /F：<?php print $product['shop_price']; ?></td>
                <td align="center"><?php print $product['n_product_num']; ?><?php print $product['unit_name']; ?></td>
                <td align="center"><?php print $product['product_number']; ?><?php print $product['unit_name']; ?></td>
                <td align="center"><?php print $product['color_name']; ?>--<?php print $product['size_name']; ?></td>
                <td align="center"><?php print $product['reason']; ?></td>
                <td align="center"><?php print $product['description']; ?></td>
                <td align="center"><?php print $product['apply_provider_status']; ?></td>
          </tr>
          <?php if (isset($product['img_list'])) :?>
          <tr>
                <th scope="col"><strong>质量图片：</strong></th>
                <td colspan="9">
                <?php foreach ($product['img_list'] as $img): ?>
                <a href="<?php print $img; ?>" target="_blank" title="点击查看原图"><img width="50" height="55" src="<?php print $img; ?>" alt="质量图片"></a>
                <?php endforeach; ?>
                </td>
          </tr>
          <?php endif; ?>
          <?php endforeach; ?>
        </table>
    
        <table class="dataTable" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="10" height="40" style="text-align: center;">
                    <input type="button" class="am-btn am-btn-primary" value="确认退货" onclick="javascript:proc_recive(<?php print $apply_info['apply_id']; ?>);" />
                </td>
            </tr>
        </table>

</div>