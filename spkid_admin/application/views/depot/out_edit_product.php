<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[

		var depot_url = '/depotio/flash_product_out';
		var depot_page = '';
		var depot_name = '<?php print $depot_name; ?>'

		function depot_gotoPage(page)
		{
		    if (page != null) depot_page = page;
		    var depot_out_id = document.getElementById('depot_out_id').value;
		    var depot_page_size = document.getElementById('depot_pageSize').value;
			$.ajax({
	            url: '/depotio/flash_product_out',
	            data: {is_ajax:1,depot_out_id:depot_out_id,depot_page:depot_page,depot_page_size:depot_page_size, rnd : new Date().getTime()},
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

		listTable.url = '/depotio/edit_out_product';
		function search(){
			listTable.filter['provider_goods'] = $.trim($('input[type=text][name=provider_goods]').val());
			listTable.filter['brand_id'] = $.trim($('select[name=brand_id]').val());
			listTable.filter['provider_id'] = $.trim($('select[name=provider_id]').val());
			listTable.filter['provider_status'] = $.trim($('select[name=provider_status]').val());
			listTable.filter['batch_id'] = $.trim($('select[name=batch_id]').val());
			listTable.filter['cooperation_id'] = $.trim($('select[name=cooperation_id]').val());
			listTable.filter['provider_barcode'] = $.trim($('input[type=text][name=provider_barcode]').val());
			if(document.getElementById("with_not").checked)
			{
				var depot_out_id = document.getElementById("depot_out_id").value;
				listTable.filter['with_not'] = depot_out_id;
			} else
			{
				listTable.filter['with_not'] = 0;
			}
			listTable.filter['depot_id'] = document.getElementById('depot_depot_id').value;
			listTable.filter['depot_out_id'] = document.getElementById('depot_out_id').value;

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
			$("#goodsDiv tr:visible input:checkbox").each(function(){
				$(this).attr("checked",obj.checked);
			});
		}

		function del_sel_depot()
		{
			var depot_out_id = document.getElementById('depot_out_id').value;
			var depot_page_size = document.getElementById('depot_pageSize').value;
			var param_str = {
			            is_ajax:1,
			            depot_out_id : depot_out_id,
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
                					alert('无效的出库记录号depot_out_sub_id:'+tmp_id);
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
	            url: '/depotio/del_depot_out_product',
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

		function update_sel_depot()
		{
                    var depot_out_id = document.getElementById('depot_out_id').value;
                    var depot_page_size = document.getElementById('depot_pageSize').value;
                    var param_str = {
                                is_ajax:1,
                                depot_out_id : depot_out_id,
                                depot_page : depot_page,
                                depot_page_size : depot_page_size,
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
                                        alert('无效的出库记录号depot_out_sub_id:'+tmp_id);
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
                        url: '/depotio/update_depot_out_product',
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
			$('#edit_p_add').attr("disabled","disabled");
			var depot_out_id = document.getElementById('depot_out_id').value;
			var depot_page_size = document.getElementById('depot_pageSize').value;
			var param_str = {
			            is_ajax:1,
			            depot_out_id : depot_out_id,
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
	            url: '/depotio/add_product_out',
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
	                $('#edit_p_add').removeAttr("disabled");
	            }
	        });
		    return false;
		}

		function show_diff_only(dom) {
			if(dom.checked) {
				$("#tb_product_out tr").not(".head").not(".tr_bg_yellow").not("tr_bg_green").hide();
			} else {
				$("#tb_product_out tr").show();
			}
		}

		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">出库管理 &gt;&gt; 出库商品详细</span> &nbsp;单号：<?php print $depot_out_info->depot_out_code; ?><span class="r">[ <a href="/depotio/out">返回列表 </a>]</span></div>
		<div class="produce">
		
		<input type="checkbox" onclick="show_diff_only(this);">只显示差异
		
		<ul>
	         <li class="p_nosel conf_btn" onclick="location.href='/depotio/edit_out/<?php print $depot_out_info->depot_out_id; ?>'"><span>基础信息</span></li>
	         <li class="p_sel conf_btn" onclick="location.href='/depotio/edit_out_product/<?php print $depot_out_info->depot_out_id; ?>'"><span>出库商品</span></li>
	     </ul>

		<div class="pc base">
		<div id="goodsDiv">
			<?php include_once(APPPATH.'views/depot/product_out_lib.php'); ?>
		</div>
		<div class="search_row" style="margin-top:5px;">
			<form name="search" action="javascript:search(); ">
			<span>搜索要添加的商品:</span>
			商品款号或名称：<input type="text" class="textbox" name="provider_goods" value="" style="width:100px;" />
			条码：<input type="text" class="textbox" name="provider_barcode" value="" style="width:150px;" />
			品牌：<?php print form_dropdown('brand_id',$brand_list);?>
			供应商：<?php print $depot_out_info->provider_id?form_dropdown('provider_id',$provider_list,$depot_out_info->provider_id,' disabled="disabled"'): form_dropdown('provider_id',$provider_list);?>
			批次：<?php print $depot_out_info->batch_id?form_dropdown('batch_id',$batch_list,$depot_out_info->batch_id,' disabled="disabled"'): form_dropdown('batch_id',$batch_list);?>
			合作方式：<?php print form_dropdown('cooperation_id',$type_list);?>
			状态：<?php print form_dropdown('provider_status',$provider_status);?>
			<input type="checkbox" name="with_not" id="with_not" checked>过滤已有商品
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
					<!--<th>售价</th>-->
					<th>状态</th>
					<th>颜色</th>
					<th>尺码</th>
					<th>批次</th>
					<th>条码</th>
					<!-- <th>可出库数</th> -->
					<th width="320px;">仓库|储位|库存</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->product_sn; ?>&nbsp;|&nbsp;<?php print $row->product_name; ?>&nbsp;|&nbsp;<?php print $row->provider_productcode; ?></td>
					<td><?php print $row->brand_name; ?></td>
					<td><?php print $row->provider_name; ?></td>
					<!--<td><?php //print $row->shop_price; ?></td>-->
					<td><?php print $row->status_name; ?></td>
					<td><?php print $row->color_name.'['.$row->color_sn.']'; ?></td>
					<td><?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
					<td><?php print $row->batch_code; ?></td>
					<td><?php print $row->provider_barcode; ?></td>
					<!-- <td><?php print $row->total_can_out_num; ?></td> -->
					<td>
					<?php foreach($row->item as $row2): ?>
					<?php if($row2->can_out_num > 0): ?>
					<p><span style="display:-moz-inline-box; display:inline-block; width:260px;">
                                            <?php print $row2->depot_name; ?>&nbsp;|&nbsp;
                                                <?php print $row2->location_name;?>&nbsp;可出库数:<?php print $row2->can_out_num; ?>&nbsp;</span>
                                            <input type="text" size="4" id="product_num_<?php print $row2->transaction_id; ?>" value="<?php print $filter['depot_type']=='ck001'?$row2->can_out_num:0; ?>" />
                                        </p>
					<?php endif; ?>
					<?php endforeach; ?>
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="9" class="bottomTd"> </td>
				</tr>
			</table>
			<div>
			<input type="button" class="r" name="edit_p" id="edit_p_add" value="添加出库商品" onclick="insert_sel_product()" />
			</div>
			<div class="blank5"></div>
			<div class="page">
				<?php include(APPPATH.'views/common/page.php') ?>
			</div>
<?php endif; ?>
<?php if($full_page): ?>
		</div>

		</div></div>
	</div>
	<input type="hidden" name="depot_out_id" id="depot_out_id" value="<?php print $depot_out_info->depot_out_id; ?>" />
	<input type="hidden" name="depot_depot_id" id="depot_depot_id" value="<?php print $depot_out_info->depot_depot_id; ?>" />

<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>