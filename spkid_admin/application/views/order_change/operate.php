<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript">
		var require_note = '<?php print $require_note; ?>';
	    var operation = '<?php print $operation; ?>';

	    function check()
	    {
	        if(operation == 'shipped'){
	            var error = false;
	            $('input[type=hidden][name^="location_id"]').each(function(){
	                if($(this).val()==0){
	                    error = true;
	                }
	            });
	            if(error){
	                alert('-请选择储位!');return false;
	            }
	        }
	        if (require_note)
	        {
	        	var eles=document.forms['theForm'].elements;
			  	if(eles['action_note'].value==""){
			  		alert("请输入备注信息");
			  		return false;
			  	}
	        }
	        return true;
	    }

	    function showLoactionWin(obj,str)
		{
			var psval = document.getElementById("ps-"+str);
	        var pshval = document.getElementById("psh-"+str);
			var depot_store_id = obj.value;
	                if(depot_store_id==0){
	                	psval.value = '';
						pshval.value = '';
	                    return false;
	                }
			var loOBJ = new Object();
			var lonewWin = window.showModalDialog("/depotio/show_location_win/"+depot_store_id,loOBJ,"dialogHeight:450px;dialogWidth:200px;center:yes;help:no;status:no;resizable:no");
			if(loOBJ.pass){
	        	psval.value = loOBJ.packet_name;
	           	pshval.value = loOBJ.packet_id;
			} else
			{
				psval.value = '';
				pshval.value = '';
			}

		}

	</script>
	<div class="main">
		<div class="main_title"><span class="l">换货单管理 &gt;&gt; 编辑换货单</span> &nbsp;单号：<?php print $change['change_sn']; ?><span class="r">[ <a href="/order_change">返回列表 </a>]</span></div>
		<div class="produce">
			<form name="theForm" method="post" action="/order_change/operate_post" onsubmit="return check()">
				<div class="list-div">
				<table class="dataTable" cellpadding="3" cellspacing="1">
				  <tr>
				    <th width="120">操作备注：</th>
				    <td><textarea name="action_note" cols="60" rows="3"><?php print $action_note; ?></textarea>
				    <?php print ($require_note)?'必填项目':'' ?></td>
				  </tr>
				  <?php if ($operation == 'shipped'): ?>
				  <tr>
				      <th>选择储位</th>
				      <td>
				          <div class="list-div">
				          <table class="dataTable" cellpadding="3" cellspacing="1">
				              <tr>
				                  <td align="center"><strong>商品名称</strong></td>
				                  <td align="center"><strong>商品款号</strong></td>
				                  <td align="center"><strong>供应商货号</strong></td>
				                  <td align="center"><strong>颜色规格</strong></td>
				                  <td align="center"><strong>换货数</strong></td>
				                  <td align="center"><strong>实入库</strong></td>
				                  <td align="center"><strong>来源</strong></td>
				                  <td align="center"><strong>储位</strong></td>
				              </tr>
				              <?php if (!empty($change_product)): ?>
				              <?php foreach ($change_product as $product): ?>
				              <tr>
				                  <td class="first-cell"><?php print $product['product_name']; ?>[<?php print $product['brand_name']; ?>]</td>
				                  <td align="center"><?php print $product['product_sn']; ?></td>
				                  <td align="center"><?php print $product['provider_productcode']; ?></td>
				                  <td align="center"><?php print $product['src_color_name']; ?> -- <?php print $product['src_size_name']; ?></td>
				                  <td align="center"><?php print $product['change_num']; ?></td>
				                  <td align="center"><?php print $product['real_num']; ?></td>
				                  <td align="center"><?php print $product['out_depot']; ?></td>
				                  <td><input type="hidden" name="rec_id[]" value="<?php print $product['cp_id']; ?>">
				                      <div class ="trans_box">
				                          <select name="depot_id[]" onchange="showLoactionWin(this,'<?php print $product['cp_id']; ?>')">
				                              <option value="0">请选择仓库</option>
				                              <?php foreach ($depot_arr as $key=>$value): ?>
				                              <option value="<?php print $key; ?>"><?php print $value; ?></option>
				                              <?php endforeach; ?>
				                          </select>
				                          <input type="text" id="ps-<?php print $product['cp_id']; ?>" value="" readonly />
				                          <input type="hidden" name="location_id[]" id="psh-<?php print $product['cp_id']; ?>" value="" />
				                      </div>
				                  </td>
				              </tr>
				              <?php endforeach; ?>
				              <?php else: ?>
				              <tr><td colspan="8">该换货单没有需要入库的商品</td></tr>
				              <?php endif; ?>
				          </table>
				          </div>
				      </td>
				  </tr>
				  <?php endif; ?>
				  <tr>
				    <td colspan="2">
				      <div align="center">
				        <input type="submit" name="submit" value="提交" class="am-btn am-btn-primary" />
				        <input type="button" name="back" value="返回" class="am-btn am-btn-primary" onclick="history.back()" />
				        <input type="hidden" name="change_id" value="<?php print $change_id; ?>" />
				        <input type="hidden" name="operation" value="<?php print $operation; ?>" />
				        <input type="hidden" name="act" value="operate_post" />
				        </div></td>
				  </tr>
				</table>
				</div>
			</form>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
