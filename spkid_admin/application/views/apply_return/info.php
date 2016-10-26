
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript">
            function check_suggestion_form(){
                var eles=document.forms['suggest_form'].elements;
                if(eles['suggest_content'].value==""){
                        alert("请填写意见内容！");
                        return false;
                }
          }
	</script>
	<div class="main">
		<div class="main_title"><span class="l">申请退货单管理 &gt;&gt; 申请退货单详情</span><span class="r">[ <a href="/apply_return">返回列表 </a>]</span></div>
		<div class="produce" id="order_return" style="background-color:#fff;">
			<div>
			<table class="dataTable" width="100%" cellpadding="0" cellspacing="0" style=" margin-top:0;">
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
			</div>

			<div class="list-div">
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
                            <a href="<?php print "public/data/images/".$img; ?>" target="_blank" title="点击查看原图"><img width="50" height="55" src="<?php print "public/data/images/".$img; ?>" alt="质量图片"></a>
                            <?php endforeach; ?>
                            </td>
                          </tr>
                          <?php endif; ?>
			  <?php endforeach; ?>
			</table>
			</div>

			<form name="suggestion_form" action="/apply_return/suggest/<?php print $apply_info['apply_id']; ?>" method="POST" onsubmit="return check_suggestion_form()">
			<div class="list-div" style="margin-bottom: 5px;" >
			<table class="dataTable" width="100%" cellpadding="0" cellspacing="0">
			  <tr>
			    <th colspan="5">意见信息</th>
			  </tr>
			  <tr>
			    <td><div align="right"><strong>签写意见</strong></div></td>
                            <td colspan="4">
                            <select name="suggest_type">
                            <option value="0">客服意见</option>
                            <option value="3">其他意见</option>
                            </select><br/>
                            <textarea name="suggest_content" cols="80" rows="3"></textarea>
                            <br/>
                            <input type="submit" name="suggestion" value="发布意见" onclick="return check_suggestion_form()"/>
			  </td>
			  </tr>
                          
                          <?php if ($apply_suggest) :?>
                          <tr>
                            <th>意见类型</th>
                            <th>创建人</th>
                            <th>意见内容</th>   
                            <th>创建时间</th>    
                          </tr>
                          <?php foreach ($apply_suggest as $suggest): ?>
                          <tr>
                            <td><div align="center"><?php print $suggest['suggest_type_name']; ?></div></td>
                            <td><div align="center"><?php print $suggest['user_name']; ?></div></td>
                            <td><div align="center"><?php print $suggest['suggest_content']; ?></div></td>
                            <td><div align="center"><?php print $suggest['create_date']; ?></div></td>
                          </tr>
                          <?php endforeach; ?>
                          <?php endif; ?>
			</table>
			</div>
			</form>
	</div>

<?php include_once(APPPATH.'views/common/footer.php'); ?>
