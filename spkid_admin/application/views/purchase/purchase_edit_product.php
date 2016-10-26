<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript">
		//<![CDATA[
		listTable.url = '/purchase/edit_product';
		function search(){
			listTable.filter['provider_goods'] = $.trim($('input[type=text][name=provider_goods]').val());
			listTable.filter['brand_id'] = $.trim($('select[name=brand_id]').val());
			listTable.filter['provider_id'] = $.trim($('select[name=provider_id]').val());
			listTable.filter['provider_status'] = $.trim($('select[name=provider_status]').val());
			listTable.filter['purchase_type'] = $.trim($('select[name=purchase_type]').val());
			listTable.filter['purchase_id'] = $.trim($('#purchase_id').val());
			if(document.getElementById("with_not").checked)
			{
				var purchase_id = document.getElementById("purchase_id").value;
				listTable.filter['with_not'] = purchase_id;
			} else
			{
				listTable.filter['with_not'] = 0;
			}

			document.getElementById('listDiv').style.display = "block";
			document.getElementById('toggle_product').value = "隐藏";
			listTable.loadList();
		}

		function toggle_product_div()
		{
			if (document.getElementById('listDiv').style.display == "none")
			{
				document.getElementById('listDiv').style.display = "block";
				document.getElementById('toggle_product').value = "隐藏";
			} else
			{
				document.getElementById('listDiv').style.display = "none";
				document.getElementById('toggle_product').value = "显示";
			}
		}

		function check_all_sel_product(obj)
		{
			var	all=document.getElementById("listDiv");
    		var checks=all.getElementsByTagName("input");

		    for(var i=0;i <checks.length;i++)
		    {
				if (checks[i].type == "checkbox")
		   		{
                	if(checks[i].id.length > 14)
                	{
                		if(checks[i].id.substr(0,14) == "product_check_")
                		{
							document.getElementById(checks[i].id).checked=obj.checked;
                		}
                	}
                }
		    }
		}

		function check_all_sel_purchase(obj)
		{
			var	all=document.getElementById("goodsDiv");
    		var checks=all.getElementsByTagName("input");

		    for(var i=0;i <checks.length;i++)
		    {
				if (checks[i].type == "checkbox")
		   		{
                	if(checks[i].id.length > 15)
                	{
                		if(checks[i].id.substr(0,15) == "purchase_check_")
                		{
							document.getElementById(checks[i].id).checked=obj.checked;
                		}
                	}
                }
		    }
		}

		function del_sel_purchase()
		{
			var purchase_id = document.getElementById('purchase_id').value;
			var param_str = {
			            is_ajax:1,
			            purchase_id : purchase_id,
			            rnd : new Date().getTime()
			        };
			var tmp_id = "";
			var tmp_str = "";
			var	all=document.getElementById("goodsDiv");
    		var checks=all.getElementsByTagName("input");
			var is_sel = 0;
		    for(var i=0;i <checks.length;i++)
		    {
				if (checks[i].type == "checkbox")
		   		{
                	if(checks[i].id.length > 15)
                	{
                		if(checks[i].id.substr(0,15) == "purchase_check_")
                		{
                			if(document.getElementById(checks[i].id).checked)
                			{
                				tmp_id = checks[i].id.substr(15);
                				if(tmp_id > 0)
                				{
									param_str['checkp_'+tmp_id] = tmp_id;
									is_sel = 1;
                				}
                				else
                				{
                					alert('无效的调仓记录号purchase_sub_id');
                					return false;
                				}

                			}
                		}
                	}
                }
		    }
		    if(is_sel == 0)
		    {
		    	alert('请先勾选商品');
		    	return false;
		    }

			$.ajax({
	            url: '/purchase/del_purchase_product',
	            data: param_str,
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(result.row_num > 0)
	                	{
							document.getElementById('goodsDiv').disabled = false;
	                	}
	                	else
	                	{
	                		document.getElementById('goodsDiv').disabled = true;
	                	}
	                	alert("操作已完成");
	                	document.getElementById('goodsDiv').innerHTML = result.content;
	                }
	            }
	        });
		    return false;
		}

		function update_sel_purchase()
		{
			var purchase_id = document.getElementById('purchase_id').value;
			var param_str = {
			            is_ajax:1,
			            purchase_id : purchase_id,
			            rnd : new Date().getTime()
			        };
			var tmp_id = "";
			var tmp_str = "";
			var	all=document.getElementById("goodsDiv");
    		var checks=all.getElementsByTagName("input");
			var is_sel = 0;
		    for(var i=0;i <checks.length;i++)
		    {
				if (checks[i].type == "checkbox")
		   		{
                	if(checks[i].id.length > 15)
                	{
                		if(checks[i].id.substr(0,15) == "purchase_check_")
                		{
                			//if(document.getElementById(checks[i].id).checked)
                			//{
                				tmp_id = checks[i].id.substr(15);
                				if(tmp_id > 0)
                				{
									tmp_str = document.getElementById('purchase_num_'+tmp_id).value;
									if(!isNaN(tmp_str) && tmp_str > 0)
									{
										param_str['checkp_'+tmp_id] = parseInt(tmp_str);
										is_sel = 1;
									}
									else
									{
										alert('无效的商品数量');
										document.getElementById('purchase_num_'+tmp_id).style.borderColor ="red";
										return false;
									}
                				}
                				else
                				{
                					alert('无效的调仓记录号purchase_sub_id');
                					return false;
                				}

                			//}
                		}
                	}
                }
		    }
		    if(is_sel == 0)
		    {
		    	alert('没有可更新的商品');
		    	return false;
		    }

			$.ajax({
	            url: '/purchase/update_purchase_product',
	            data: param_str,
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(result.row_num > 0)
	                	{
							document.getElementById('goodsDiv').disabled = false;
	                	}
	                	else
	                	{
	                		document.getElementById('goodsDiv').disabled = true;
	                	}
	                	alert("操作已完成");
	                	document.getElementById('goodsDiv').innerHTML = result.content;
	                }
	            }
	        });
		    return false;
		}

		function insert_sel_product()
		{
			var purchase_id = document.getElementById('purchase_id').value;
                        var purchase_type = $("[name=purchase_type]").val();
			var param_str = {
			            is_ajax:1,
			            purchase_id : purchase_id,
                                    purchase_type : purchase_type,
			            rnd : new Date().getTime()
			        };
			var tmp_id = "";
			var tmp_str = "";
			var is_sel = 0;
			var	all=document.getElementById("listDiv");
    		var checks=all.getElementsByTagName("input");
		    for(var i=0;i <checks.length;i++)
		    {
				if (checks[i].type == "checkbox")
		   		{
                	if(checks[i].id.length > 14)
                	{
                		if(checks[i].id.substr(0,14) == "product_check_")
                		{
                			if(document.getElementById(checks[i].id).checked)
                			{
                				tmp_id = checks[i].id.substr(14);
                				if(tmp_id > 0)
                				{
                					tmp_str = document.getElementById('product_num_'+tmp_id).value;
									if( !isNaN(tmp_str) && tmp_str > 0 ) 
									{
										param_str['check_'+tmp_id] = parseInt(tmp_str);
										is_sel = 1;
									}
									else
									{
										alert('无效的商品数量/价格');
										document.getElementById('product_num_'+tmp_id).style.borderColor ="red";
										return false;
									}   
                				}
                				else
                				{
                					alert('无效的商品sub_id');
                					return false;
                				}

                			}
                		}
                	}
                }
		    }
		    if(is_sel == 0)
		    {
		    	alert('请先勾选商品');
		    	return false;
		    }
			$.ajax({
	            url: '/purchase/add_product_simple',
	            data: param_str,
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(result.row_num > 0)
	                	{
							document.getElementById('goodsDiv').disabled = false;
	                	}
	                	else
	                	{
	                		document.getElementById('goodsDiv').disabled = true;
	                	}
	                	document.getElementById('goodsDiv').innerHTML = result.content;
	                } else
	                {
	                	if(result.sub_id && result.sub_id > 0)
	                	{
	                		document.getElementById('product_num_'+result.sub_id).style.borderColor ="red";
	                	}
	                }
	            }
	        });
		    return false;
		}

		function insert_all()
		{
			if(confirm("确定添加搜选出的全部商品？"))
			{
				var provider_goods = listTable.filter['provider_goods'];
				var brand_id = listTable.filter['brand_id'];
				var provider_id = listTable.filter['provider_id'];
				var provider_status = listTable.filter['provider_status'];
				var purchase_type = listTable.filter['purchase_type'];
				var purchase_id = document.getElementById('purchase_id').value;

				$.ajax({
		            url: '/purchase/add_product_all',
		            data: {is_ajax:1,provider_goods:provider_goods,brand_id:brand_id,provider_id:provider_id,purchase_type:purchase_type,
		            		provider_status:provider_status,purchase_id:purchase_id,rnd : new Date().getTime()},
		            dataType: 'json',
		            type: 'POST',
		            success: function(result){
		                if(result.msg) {alert(result.msg)};
		                if(result.error == 0)
		                {
		                	if(result.row_num > 0)
		                	{
								document.getElementById('goodsDiv').disabled = false;
		                	}
		                	else
		                	{
		                		document.getElementById('goodsDiv').disabled = true;
		                	}
		                	document.getElementById('goodsDiv').innerHTML = result.content;
		                } else
		                {
		                	if(result.sub_id && result.sub_id > 0)
		                	{
		                		document.getElementById('product_num_'+result.sub_id).style.borderColor ="red";
		                	}
		                }
		            }
		        });
			}

		    return false;
		}
		function redirect(url){
		    if (!/*@cc_on!@*/0) {            
			window.open(url,'_blank');        
		    } else {            
			var a = document.createElement('a');            
			a.href = url;            
			a.target = '_blank';            
			document.body.appendChild(a);            
			a.click();        
		    }
		}
		$(function(){
		      $("#print_provider_barcode").click(function(){
			  redirect("purchase_box/pruchase_provider_barcode/<?php print $purchase_info->purchase_code; ?>");
		      });
                      $("#print_provider_barcode_scaned").click(function(){
			  redirect("purchase_box/pruchase_provider_barcode_scaned/<?php print $purchase_info->purchase_code; ?>");
		      });
		});
		//]]>
	</script>

<div class="main">
    <div class="main_title">
        <span class="l">采购管理 &gt;&gt; 采购单商品 &nbsp;单号：<?php print $purchase_info->purchase_code; ?></span>
        <span class="r">
            [ <a href="/purchase/index">返回列表 </a>]
        </span>
    </div>
  <div class="produce">
    <ul>
      <li class="p_nosel conf_btn" onclick="location.href='/purchase/edit/<?php print $purchase_info->purchase_id; ?>'"><span>基础信息</span></li>
      <li class="p_sel conf_btn" onclick="location.href='/purchase/edit_product/<?php print $purchase_info->purchase_id; ?>'"><span>采购单商品</span></li>
    </ul>
    <div class="pc base">
      <div id="goodsDiv">
        <table class="dataTable" cellpadding=0 cellspacing=0 >
          <tr>
            <td colspan="10" class="topTd"></td>
          </tr>
          <tr class="row">
            <th width="150px"><input type="checkbox" id="purchase_all_check" onclick="check_all_sel_purchase(this)" />
              商品款号</th>
            <th>商品名称</th>
            <th>供应商货号</th>
	    <th>条码</th>
            <th>品牌</th>
            <th>供应商名称</th>
            <th>颜色</th>
            <th>尺码</th>
            <th>过期日期</th>
            <th>生产批号</th>
            <th width="60px;">预采购数量</th>
          </tr>
          <?php foreach($goods_list as $row): ?>
          <tr class="row">
            <td><input type="checkbox" id="purchase_check_<?php print $row->purchase_sub_id; ?>" />
              <?php print $row->product_sn; ?></td>
            <td><?php print $row->b_product_name; ?></td>
            <td><?php print $row->provider_productcode; ?></td>
	    <td><?php print $row->provider_barcode; ?></td>
            <td><?php print $row->brand_name; ?></td>
            <td><?php print $row->provider_name; ?></td>
            <td><?php print $row->color_name.'['.$row->color_sn.']'; ?></td>
            <td><?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
            <td><?php print ($row->expire_date == '0000-00-00' || $row->expire_date == '0000-00-00 00:00:00' || $row->expire_date == '')?'无':$row->expire_date; ?></td>
            <td><?php print $row->production_batch; ?></td>
            <td><input type="text" size="4" id="purchase_num_<?php print $row->purchase_sub_id; ?>" value="<?php print $row->product_number; ?>" /></td>
          </tr>
          <?php endforeach; ?>
          <tr>
            <td colspan="10" class="bottomTd"></td>
          </tr>
        </table>
        <div class="purchase_btn">
<input type="button" class="r" name="del_p" id="del_p" value="删除勾选商品" onclick="del_sel_purchase()" <?php echo !empty($goods_list)?'':'disabled' ?> />
<input type="button" class="r" name="edit_p" id="edit_p" value="提交数量更改" onclick="update_sel_purchase()" <?php echo !empty($goods_list)?'':'disabled' ?> />
        </div>

      </div>
      <div class="search_row">
      <form name="search" action="javascript:search();">
      搜索要添加的商品: 
      商品款号或名称：<input type="text" class="ts" name="provider_goods" value="" style="width:100px;" />
      品牌：<?php print $purchase_info->purchase_brand?form_dropdown('brand_id',$brand_list,$purchase_info->purchase_brand,' disabled="disabled"') : form_dropdown('brand_id',$brand_list);?> 
      供应商：<?php print $purchase_info->purchase_provider?form_dropdown('provider_id',$provider_list,$purchase_info->purchase_provider,' disabled="disabled"'): form_dropdown('provider_id',$provider_list);?> 
      合作方式：<?php print form_dropdown('purchase_type',$type_list,$purchase_info->purchase_type,' disabled="disabled"');?> 
      状态：<?php print form_dropdown('provider_status',$provider_status);?><input type="checkbox" name="with_not" id="with_not" checked >
      过滤已有商品<input type="submit" class="am-btn am-btn-primary" value="搜索" /><input type="button" id="toggle_product" class="am-btn am-btn-primary" value="隐藏" onclick="toggle_product_div()" /></form>
</div>
<div id="listDiv" style="display:none;">
        <?php endif; ?>
        <?php if(!$full_page): ?>
        <script type="text/javascript">
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
</script>
        <table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
          <tr>
            <td colspan="11" class="topTd"></td>
          </tr>
          <tr class="row">
            <th width="150px"><input type="checkbox" id="product_all_check" onclick="check_all_sel_product(this)" />
              商品款号</th>
            <th>商品名称</th>
            <th>供应商货号</th>
	    <th>条码</th>
            <th>品牌</th>
            <th>供应商名称</th>
            <th>颜色</th>
            <th>尺码</th>
            <th>状态</th>
            <th>库存</th>
            <th width="40px;">数量</th>
          </tr>
          <?php foreach($list as $row): ?>
          <tr class="row">
            <td><input type="checkbox" id="product_check_<?php print $row->sub_id; ?>" />
              <?php print $row->product_sn; ?></td>
            <td><?php print $row->product_name; ?></td>
            <td><?php print $row->provider_productcode; ?></td>
	    <td><?php print $row->provider_barcode; ?></td>
            <td><?php print $row->brand_name; ?></td>
            <td><?php print $row->provider_name; ?></td>
            <td><?php print $row->color_name.'['.$row->color_sn.']'; ?></td>
            <td><?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
            <td><?php print $row->status_name; ?></td>
            <td><?php print $row->gl_num; ?></td>
            <td><input type="text" size="4" id="product_num_<?php print $row->sub_id; ?>" value="1" /></td>
          </tr>
          <?php endforeach; ?>
          <tr>
            <td colspan="11" class="bottomTd"></td>
          </tr>
        </table>
        <div>
          <input class="r" type="button" name="edit_p" id="edit_p" value="添加勾选商品" onclick="insert_sel_product()" />
          <input class="r" type="button" name="edit_p" id="edit_p" value="添加全部商品" onclick="insert_all()" />
        </div>
        <div class="page">
          <?php include(APPPATH.'views/common/page.php') ?>
        </div>
        <?php endif; ?>
        <?php if($full_page): ?>
      </div>
    </div>
  </div>
</div>
<input type="hidden" name="purchase_id" id="purchase_id" value="<?php print $purchase_info->purchase_id; ?>" />
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
