<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		var depot_url = '/exchange/flash_exchange_out';
		var depot_page = '';
		var depot_name = '<?php print $depot_name; ?>'

		function depot_gotoPage(page)
		{
		    if (page != null) depot_page = page;
		    var exchange_id = document.getElementById('exchange_id').value;
		    var depot_page_size = document.getElementById('depot_pageSize').value;
			$.ajax({
	            url: '/exchange/flash_exchange_out',
	            data: {is_ajax:1,exchange_id:exchange_id,depot_page:depot_page,depot_page_size:depot_page_size, rnd : new Date().getTime()},
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
	                	document.getElementById('goodsDiv').style.display = "block";
	                	depot_page = result.depot_filter.page;
	                }
	            }
	        });
		    return false;

		}

		function depot_gotoPageFirst()
		{
		    depot_gotoPage(1);
		}

		function depot_gotoPagePrev()
		{
			if (depot_page > 1)
			{
				depot_gotoPage(parseInt(depot_page) -1);
			}
		}

		function depot_gotoPageNext()
		{
			var tmp_num = 1;
			if (depot_page > 1)
			{
				tmp_num = depot_page;
			}
			depot_gotoPage(parseInt(tmp_num) +1);
		}

		function depot_gotoPageLast()
		{
			depot_gotoPage(9999);
		}

		depot_changePageSize = function(e)
		{
		    var evt = ((typeof e == "undefined") ? window.event : e);
		    if (evt.keyCode == 13)
		    {
		        depot_gotoPage(1);
		        return false;
		    };
		}

		listTable.url = '/exchange/edit_out_exchange';
		function search(){
			listTable.filter['provider_goods'] = $.trim($('input[type=text][name=provider_goods]').val());
			listTable.filter['brand_id'] = $.trim($('select[name=brand_id]').val());
			listTable.filter['provider_id'] = $.trim($('select[name=provider_id]').val());
			listTable.filter['provider_status'] = $.trim($('select[name=provider_status]').val());
			listTable.filter['cooperation_id'] = $.trim($('select[name=cooperation_id]').val());
			if(document.getElementById("with_not").checked)
			{
				var exchange_id = document.getElementById("exchange_id").value;
				listTable.filter['with_not'] = exchange_id;
			} else
			{
				listTable.filter['with_not'] = 0;
			}
			listTable.filter['depot_id'] = document.getElementById('source_depot_id').value;
			listTable.filter['exchange_id'] = document.getElementById('exchange_id').value;

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

		function check_all_sel_depot(obj)
		{
			var	all=document.getElementById("goodsDiv");
    		var checks=all.getElementsByTagName("input");

		    for(var i=0;i <checks.length;i++)
		    {
				if (checks[i].type == "checkbox")
		   		{
                	if(checks[i].id.length > 12)
                	{
                		if(checks[i].id.substr(0,12) == "depot_check_")
                		{
							document.getElementById(checks[i].id).checked=obj.checked;
                		}
                	}
                }
		    }
		}

		function del_sel_exchange()
		{
			var exchange_id = document.getElementById('exchange_id').value;
			var depot_page_size = document.getElementById('depot_pageSize').value;
			var param_str = {
			            is_ajax:1,
			            exchange_id : exchange_id,
			            depot_page_size : depot_page_size,
			            depot_page : depot_page,
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
                	if(checks[i].id.length > 12)
                	{
                		if(checks[i].id.substr(0,12) == "depot_check_")
                		{
                			if(document.getElementById(checks[i].id).checked)
                			{
                				tmp_id = checks[i].id.substr(12);
                				if(tmp_id > 0)
                				{
									param_str['checkp_'+tmp_id] = tmp_id;
									is_sel = 1;
                				}
                				else
                				{
                					alert('无效的调仓出库记录号exchange_sub_id:'+tmp_id);
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
	            url: '/exchange/del_exchange_out_product',
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

		function update_sel_exchange()
		{
			var exchange_id = document.getElementById('exchange_id').value;
			var depot_page_size = document.getElementById('depot_pageSize').value;
			var param_str = {
			            is_ajax:1,
			            exchange_id : exchange_id,
			            depot_page_size : depot_page_size,
			            depot_page : depot_page,
			            rnd : new Date().getTime()
			        };
			var tmp_id = "";
			var tmp_str = "";
			var	all=document.getElementById("goodsDiv");
    		var checks=all.getElementsByTagName("input");
			var is_sel = 0;
		    for(var i=0;i <checks.length;i++)
		    {
				if (checks[i].type == "text")
		   		{
                	if(checks[i].id.length > 10)
                	{
                		if(checks[i].id.substr(0,10) == "depot_num_")
                		{
                			//if(document.getElementById(checks[i].id).checked)
                			//{
                				tmp_id = checks[i].id.substr(10);
                				if(tmp_id > 0)
                				{
									tmp_str = checks[i].value;
									if(!isNaN(tmp_str) && tmp_str > 0)
									{
										param_str['checkp_'+tmp_id] = parseInt(tmp_str);
										is_sel = 1;
									}
									else
									{
										alert('无效的商品数量');
										checks[i].style.borderColor ="red";
										return false;
									}
                				}
                				else
                				{
                					alert('无效的出库记录号exchange_sub_id:'+tmp_id);
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
	            url: '/exchange/update_exchange_out_product',
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
			var exchange_id = document.getElementById('exchange_id').value;
			var depot_page_size = document.getElementById('depot_pageSize').value;
			var param_str = {
			            is_ajax:1,
			            exchange_id : exchange_id,
			            depot_page_size : depot_page_size,
			            rnd : new Date().getTime()
			        };
			var tmp_id = "";
			var tmp_str = "";
			var is_sel = 0;
			var	all=document.getElementById("listDiv");
    		var checks=all.getElementsByTagName("input");
		    for(var i=0;i <checks.length;i++)
		    {
				if (checks[i].type == "text")
		   		{
                	if(checks[i].id.length > 12)
                	{
                		if(checks[i].id.substr(0,12) == "product_num_")
                		{
                			//if(document.getElementById(checks[i].id).checked)
                			//{
                				tmp_id = checks[i].id.substr(12);
                				if(tmp_id > 0)
                				{
                					tmp_str = checks[i].value;
									if(!isNaN(tmp_str) && tmp_str > 0)
									{
										param_str['check_'+tmp_id] = parseInt(tmp_str);
										is_sel = 1;
									}
									else
									{
										if(tmp_str != 0)
										{
											alert('无效的商品数量');
											checks[i].style.borderColor ="red";
											return false;
										}
									}
                				}
                				else
                				{
                					alert('无效的商品sub_id:'+tmp_id);
                					return false;
                				}

                			//}
                		}
                	}
                }
		    }
		    if(is_sel == 0)
		    {
		    	alert('请先设置商品数量');
		    	return false;
		    }

			$.ajax({
	            url: '/exchange/add_exchange_product_out',
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


		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">调仓管理 &gt;&gt; 调仓出库商品详细</span> &nbsp;单号：<?php print $exchange_info->exchange_code; ?> <span class="r">[ <a href="/exchange/exchange_list">返回列表 </a>]</span></div>
		<div class="produce">
		<ul>
	         <li class="p_nosel conf_btn" onclick="location.href='/exchange/edit_exchange/<?php print $exchange_info->exchange_id; ?>'"><span>基础信息</span></li>
	         <li class="p_sel conf_btn" onclick="location.href='/exchange/edit_out_exchange/<?php print $exchange_info->exchange_id; ?>'"><span>出库商品</span></li>
	         <?php if ($exchange_info->out_audit_admin > 0): ?>
	         <li class="p_nosel conf_btn" onclick="location.href='/exchange/edit_in_exchange/<?php print $exchange_info->exchange_id; ?>'"><span>入库商品</span></li>
	         <?php endif; ?>
	     </ul>
	     <div class="blank5"></div>
	     <div class="pc base">
		<div id="goodsDiv">
			<?php include_once(APPPATH.'views/depot/exchange_out_lib.php'); ?>
		</div>
		<div class="blank5"></div>

		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			<span>搜索要添加的商品:</soan>
			商品款号或名称：<input type="text" class="ts" name="provider_goods" value="" style="width:100px;" />
			品牌：<?php print form_dropdown('brand_id',$brand_list);?>
			供应商：<?php print form_dropdown('provider_id',$provider_list);?>
			合作方式：<?php print form_dropdown('cooperation_id',$type_list);?>
			状态：<?php print form_dropdown('provider_status',$provider_status);?>
			<input type="checkbox" name="with_not" id="with_not" checked />过滤已有商品
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			<input type="button" id="toggle_product" class="am-btn am-btn-primary" value="隐藏" onclick="toggle_product_div()" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv" style="display:none;">
<?php endif; ?>
<?php if(!$full_page): ?>
<script type="text/javascript">
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
</script>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="9" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="product_number">商品款号|商品名称|供应商货号</th>
					<th>品牌</th>
					<th>供应商名称</th>
					<th>售价</th>
					<th>状态</th>
					<th>颜色</th>
					<th>尺码</th>
					<th>可出库数</th>
					<th width="320px;">仓库|储位|库存</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->product_sn; ?>&nbsp;|&nbsp;<?php print $row->product_name; ?>&nbsp;|&nbsp;<?php print $row->provider_productcode; ?></td>
					<td><?php print $row->brand_name; ?></td>
					<td><?php print $row->provider_name; ?></td>
					<td><?php print $row->shop_price; ?></td>
					<td><?php print $row->status_name; ?></td>
					<td><?php print $row->color_name.'['.$row->color_sn.']'; ?></td>
					<td><?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
					<td><?php print $row->total_can_out_num; ?></td>
					<td>
					<?php foreach($row->item as $row2): ?>
					<p><span style="display:-moz-inline-box; display:inline-block; width:260px;"><?php print $row2->depot_name; ?>&nbsp;|&nbsp;<?php print $row2->location_name;?>&nbsp;可出库数:<?php print $row2->can_out_num; ?>&nbsp;</span><input type="text" size="4" id="product_num_<?php print $row2->transaction_id; ?>" value="0" /></p>
					<?php endforeach; ?>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="9" class="bottomTd"> </td>
				</tr>
			</table>
			<div>
			<input type="button" class="r" name="edit_p" id="edit_p" value="添加出库商品" onclick="insert_sel_product()" />
			</div>
			<div class="blank5"></div>
			<div class="page">
				<?php include(APPPATH.'views/common/page.php') ?>
			</div>

<?php endif; ?>
<?php if($full_page): ?>
		</div></div>
		</div>
	</div>
	<input type="hidden" name="exchange_id" id="exchange_id" value="<?php print $exchange_info->exchange_id; ?>" />
	<input type="hidden" name="source_depot_id" id="source_depot_id" value="<?php print $exchange_info->source_depot_id; ?>" />

<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>