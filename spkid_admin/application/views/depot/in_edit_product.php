<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript">
		//<![CDATA[

		var depot_url = '/depotio/flash_product_in';
		var depot_page = '';
		var depot_name = '<?php print $depot_name; ?>'

		listTable.url = '/depotio/edit_in_product';

		function depot_gotoPage(page)
		{
		    if (page != null) depot_page = page;
		    var depot_in_id = document.getElementById('depot_in_id').value;
		    var depot_page_size = document.getElementById('depot_pageSize').value;
			$.ajax({
	            url: '/depotio/flash_product_in',
	            data: {is_ajax:1,depot_in_id:depot_in_id,depot_page:depot_page,depot_page_size:depot_page_size, rnd : new Date().getTime()},
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


		function search(){
			listTable.filter['provider_barcode'] = $.trim($('input[type=text][name=provider_barcode]').val());
			listTable.filter['provider_goods'] = $.trim($('input[type=text][name=provider_goods]').val());
			listTable.filter['brand_id'] = $.trim($('select[name=brand_id]').val());
			listTable.filter['provider_id'] = $.trim($('select[name=provider_id]').val());
			listTable.filter['provider_status'] = $.trim($('select[name=provider_status]').val());
			listTable.filter['cooperation_id'] = $.trim($('select[name=cooperation_id]').val());
			listTable.filter['depot_in_id'] = document.getElementById('depot_in_id').value;
			if(document.getElementById("with_not").checked)
			{
				listTable.filter['with_not'] = document.getElementById('depot_in_id').value;
			} else
			{
				listTable.filter['with_not'] = 0;
			}

			document.getElementById('listDiv').style.display = "block";
			document.getElementById('toggle_product').value = "隐藏";
			listTable.loadList();
		}

		function show_packet_ban(sub_id,order_sn,depot_depot_id)
		{
			var depot_in_id = document.getElementById('depot_in_id').value;
			$.ajax({
	            url: '/depotio/loaction_input_pre',
	            data: {sub_id:sub_id,order_sn:order_sn,depot_depot_id:depot_depot_id,depot_in_id:depot_in_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	if(result.valuable > 0)
	                	{
	                		document.getElementById('location_code').disabled = false;
	                		document.getElementById('in_num').disabled = false;
	                		document.getElementById('insert_single').disabled = false;
	                	} else
	                	{
	                		document.getElementById('location_code').disabled = true;
	                		document.getElementById('in_num').disabled = true;
	                		document.getElementById('insert_single').disabled = true;
	                	}
						format_packet_ban();
	                	document.getElementById('pre_sub_id').value = sub_id;
	                	document.getElementById('max_num').value = result.max_num;
						document.getElementById('info_tip1').innerHTML = result.content1;
						document.getElementById('info_tip2').innerHTML = result.content2;
	                	document.getElementById('loactionDiv').style.display = "block";
	                	document.getElementById('addResultDiv').style.display = "none";
	                }
	            }
	        });
		    return false;


		}

		function format_packet_ban ()
		{
			document.getElementById('pre_sub_id').value = '';
			document.getElementById('max_num').value = '';
			document.getElementById('info_tip1').innerHTML = '';
			document.getElementById('info_tip2').innerHTML = '';
			document.getElementById('in_num').value = ''
			document.getElementById('location_code').value = ''
		}

		function showLoactionWin(obj,depot_id)
		{
			/*var loOBJ = new Object();
			var lonewWin = window.showModalDialog("/depotio/show_location_win/"+depot_id,loOBJ,"dialogHeight:450px;dialogWidth:200px;center:yes;help:no;status:no;resizable:no");
			if(loOBJ.pass){
				obj.value = loOBJ.packet_name;
			}*/

                    window.open("/depotio/show_location_win/"+depot_id+"/"+obj.id,'newwindow',"height=500,width=300,toolbar=no,titlebar=no,location=no,menubar=no,resizable=no,z-look=yes");
		}

		function insert_sel_product(sub_id,index)
		{
			var depot_in_id = document.getElementById('depot_in_id').value;
			var location_code = document.getElementById('prodlocation_'+sub_id+'_'+index).value;
			var in_num = document.getElementById('prodinnum_'+sub_id+'_'+index).value;

			if(depot_in_id > 0 && location_code != '' && in_num > 0 && sub_id > 0 && in_num == parseInt(in_num))
			{
				in_num = parseInt(in_num);
			}
			else
			{
				alert('无效的参数');
                return false;
			}

			$.ajax({
	            url: '/depotio/add_product_in_simple',
	            data: {is_ajax:1,depot_in_id:depot_in_id,sub_id:sub_id,location_code:location_code,in_num:in_num,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	var depot_in_sub_id = result.depot_in_sub_id;
	                	if (document.getElementById('product_p_'+depot_in_sub_id))
	                	{
							document.getElementById('depot_num_'+depot_in_sub_id).value = parseInt(document.getElementById('depot_num_'+depot_in_sub_id).value) + parseInt(in_num);
	                		del_prod_p(sub_id+'_'+index);
	                	} else
	                	{
	                		document.getElementById('prodp_'+sub_id+'_'+index).innerHTML = depot_name+'--'+location_code+':<input style="margin-left:6px;margin-right:6px;" type="text" size="4" id="depot_num_'+depot_in_sub_id+'" value="'+in_num+'" /><a href="#" onclick="del_sel_product(\''+depot_in_sub_id+'\');return false;" title="删除">删除</a>';
	                		document.getElementById('prodp_'+sub_id+'_'+index).id = 'product_p_'+depot_in_sub_id;
	                	}

	                } else
	                {
	                	if(result.sub_id && result.sub_id > 0)
	                	{
	                		document.getElementById('prodlocation_'+result.sub_id+'_'+index).style.borderColor ="red";
				         }
	                }
	            }
	        });
		    return false;
		}

		function flash_product_div()
		{
			var depot_in_id = document.getElementById('depot_in_id').value;
			$.ajax({
	            url: '/depotio/flash_product_in',
	            data: {is_ajax:1,depot_in_id:depot_in_id,rnd : new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success: function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	document.getElementById('goodsDiv').innerHTML = result.content;
	                	document.getElementById('goodsDiv').style.display = "block";
	                }
	            }
	        });
		    return false;
		}

		function del_sel_product(depot_in_sub_id)
		{
			if(confirm("确认删除该记录吗？") == false)
			{
				return false;
			}
			var depot_in_id = document.getElementById('depot_in_id').value;
			var depot_page_size = document.getElementById('depot_pageSize').value;

			$.ajax({
	            url: '/depotio/del_depot_in_product',
	            data: {is_ajax:1,depot_in_id:depot_in_id,depot_in_sub_id:depot_in_sub_id,depot_page_size:depot_page_size,depot_page:depot_page,rnd : new Date().getTime()},
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

	                }
	            }
	        });
		    return false;
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
                	if(checks[i].id.length > 15)
                	{
                		if(checks[i].id.substr(0,15) == "depot_check_")
                		{
							document.getElementById(checks[i].id).checked=obj.checked;
                		}
                	}
                }
		    }
		}

		function add_product_in()
		{
                    $('#add_p').attr('disabled','disabled');
                    var depot_in_id = document.getElementById('depot_in_id').value;
                    var depot_page_size = document.getElementById('depot_pageSize').value;
                    var param_str = {
                                is_ajax:1,
                                depot_in_id : depot_in_id,
                                depot_page_size : depot_page_size,
                                rnd : new Date().getTime()
                    };
                    var tmp_id = "";
                    var tmp_str = "";
                    var	all=document.getElementById("listDiv");
                    var checks=all.getElementsByTagName("input");
                    var is_sel = 0;
		    for(var i=0;i <checks.length;i++)
		    {
                        if (checks[i].type == "text")
                        {
                            if(checks[i].id.length > 9)
                            {
                                if(checks[i].id.substr(0,9) == "location_")
                                {
                                    tmp_id = checks[i].id.substr(9);
                                    tmp_arr = tmp_id.split('_');
                                    tmp_sub_id = tmp_arr[0];
                                    if(tmp_sub_id > 0)
                                    {
                                        tmp_location = checks[i].value;
                                        tmp_num = document.getElementById("innum_"+tmp_id).value;
                                        tmp_batchid = document.getElementById("batchid_"+tmp_sub_id).value;
                                        if(tmp_num === '' || (tmp_num == parseInt(tmp_num) && tmp_num <= 0)) {
                                            continue;
                                        }
                                        
                                        if(tmp_num == parseInt(tmp_num) && tmp_num > 0 && tmp_location != '' && tmp_batchid != '')
                                        {
                                            if (param_str['checkp__'+tmp_sub_id+'__'+tmp_location+'__'+tmp_batchid])
                                            {
                                                    param_str['checkp__'+tmp_sub_id+'__'+tmp_location+'__'+tmp_batchid] = parseInt(param_str['checkp__'+tmp_sub_id+'__'+tmp_location+'__'+tmp_batchid])+parseInt(tmp_num);
                                            } else
                                            {
                                                    param_str['checkp__'+tmp_sub_id+'__'+tmp_location+'__'+tmp_batchid] = parseInt(tmp_num);
                                            }

                                            is_sel = 1;
                                        }
                                        else
                                        {
                                            if(tmp_location != '' || tmp_num != '')
                                            {
                                                    alert('无效的储位或者商品数量');
                                                    checks[i].style.borderColor ="red";
                                                    $('#add_p').removeAttr('disabled');
                                                    return false;
                                            }
                                        }
                                    }
                                    else
                                    {
                                            alert('无效的入库记录号depot_in_sub_id'+tmp_sub_id);
                                            $('#add_p').removeAttr('disabled');
                                            return false;
                                    }
                                }
                            }
                        }
		    }
		    if(is_sel == 0)
		    {
		    	alert('没有可更新的商品');
		    	$('#add_p').removeAttr('disabled');
		    	return false;
		    }

		    $.ajax({
	            url: '/depotio/add_product_in',
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
                        else
	                {
                            if(result.sub_id && result.sub_id > 0)
                            {
                                for(var l=0;l <checks.length;l++)
                                {
                                    if (checks[l].type == "text")
                                    {
                                        if(checks[l].id.length > 9)
                                        {
                                            if(checks[l].id.substr(0,9) == "location_")
                                            {
                                                tmp_id = checks[l].id.substr(9);
                                                tmp_arr = tmp_id.split('_');
                                                tmp_sub_id = tmp_arr[0];
                                                if(tmp_sub_id == result.sub_id && checks[l].value == result.subvalue)
                                                {
                                                    checks[l].style.borderColor ="red";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
	                }
	                $('#add_p').removeAttr('disabled');
	            }});
		    return false;
	        }



		function update_product_in()
		{
			var depot_in_id = document.getElementById('depot_in_id').value;
			var depot_page_size = document.getElementById('depot_pageSize').value;
			var param_str = {
			            is_ajax:1,
			            depot_in_id : depot_in_id,
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
                					alert('无效的入库记录号depot_in_sub_id'+tmp_str);
                					return false;
                				}

                			//}
                		}
                	}
                }
		    }
		    var samenamenum = '';
		    for(var i=0;i <checks.length;i++)
		    {
				if (checks[i].type == "text")
		   		{
                	if(checks[i].id.length > 13)
                	{
                		if(checks[i].id.substr(0,13) == "prodlocation_")
                		{
                				tmp_id = checks[i].id.substr(13);
								samenamenum	= document.getElementById('prodinnum_'+tmp_id).value;
								if(samenamenum == parseInt(samenamenum) && samenamenum > 0 && checks[i].value != '')
								{

								} else
								{
									if(samenamenum == '' && checks[i].value == '')
									{

									} else
									{
										alert('您有未处理完的操作，请先提交或者删除该项');
										checks[i].style.borderColor ="red";
										return false;
									}
								}

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
	            url: '/depotio/update_depot_in_product',
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

		function add_p(sub_id)
		{
			var num = document.getElementById('addnum_'+sub_id).value;
			document.getElementById('addnum_'+sub_id).value = parseInt(num)+1;

			var newcontent = document.createElement('p');
			newcontent.style.marginTop = "3px";
			newcontent.id = 'p_'+sub_id+'_'+num;
			newcontent.innerHTML ='储位:<input type="text" class="textbox" name="location_'+sub_id+'_'+num+'" id="location_'+sub_id+'_'+num+'" value="" ondblclick="showLoactionWin(this,\''+<?php print $depot_in_info->depot_depot_id; ?>+'\');" style="width:100px;" />&nbsp;&nbsp;数量:<input type="text" class="textbox" name="innum_'+sub_id+'_'+num+'" id="innum_'+sub_id+'_'+num+'" value="" style="width:50px;" />&nbsp;<a href="#" onclick="del_p(\''+sub_id+'_'+num+'\');return false;" value="delete" /><span class="deleteForGif"></span></a>';
			document.getElementById('div_'+sub_id).appendChild(newcontent);
            //<img src="<?php /*echo $imagedomain*/ ?>/delete.gif" border="0" /> By Rock
			 
			//tmpstr = document.getElementById('div_'+sub_id).innerHTML + tmpstr;
			//document.getElementById('div_'+sub_id).innerHTML += tmpstr;
		}

		function add_prod_p(sub_id)
		{
			var num = document.getElementById('addprodnum_'+sub_id).value;
			document.getElementById('addprodnum_'+sub_id).value = parseInt(num)+1;

			var newcontent = document.createElement('p');
			newcontent.style.marginTop = "3px";
			newcontent.id = 'prodp_'+sub_id+'_'+num;
			newcontent.innerHTML ='储位:<input type="text" class="textbox" name="prodlocation_'+sub_id+'_'+num+'" id="prodlocation_'+sub_id+'_'+num+'" value="" ondblclick="showLoactionWin(this,\''+<?php print $depot_in_info->depot_depot_id; ?>+'\');" style="width:75px;" />&nbsp;&nbsp;数量:<input type="text" class="textbox" name="prodinnum_'+sub_id+'_'+num+'" id="prodinnum_'+sub_id+'_'+num+'" value="" style="width:35px;" />&nbsp;<a href="#" onclick="insert_sel_product(\''+sub_id+'\',\''+num+'\');return false;" value="add" /><span class="yesForGif"></span></a>&nbsp;<a href="#" onclick="del_prod_p(\''+sub_id+'_'+num+'\');return false;" value="delete" /><span class="deleteForGif"></span></a>';
			document.getElementById('proddiv_'+sub_id).appendChild(newcontent);
		}
        //<!--<img src="--><?php /*echo $imagedomain  */ ?><!--/yes.gif" border="0" />改为样式<span class="yesForGif"></span> By Rock-->
		//<img src="<?php /*echo $imagedomain*/ ?>/delete.gif" border="0" /> By Rock
		function del_p(str)
		{
			var div = document.getElementById("p_"+str);
			div.parentNode.removeChild(div);
		}

		function del_prod_p(str)
		{
			var div = document.getElementById("prodp_"+str);
			div.parentNode.removeChild(div);
		}

		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">入库管理 &gt;&gt; 入库商品详细</span> &nbsp;单号：<?php print $depot_in_info->depot_in_code; ?><span class="r">[ <a href="/depotio/in">返回列表 </a>]</span></div>
		<div class="produce">
		<ul>
	         <li class="p_nosel conf_btn" onclick="location.href='/depotio/edit_in/<?php print $depot_in_info->depot_in_id; ?>'"><span>基础信息</span></li>
	         <li class="p_sel conf_btn" onclick="location.href='/depotio/edit_in_product/<?php print $depot_in_info->depot_in_id; ?>'"><span>入库商品</span></li>
	     </ul>

		<div class="pc base">
		<div id="goodsDiv">
			<?php include_once(APPPATH.'views/depot/product_in_lib.php'); ?>
		</div>
		<div class="search_row" style="margin-top:5px;">
			<form name="search" action="javascript:search(); ">
			<span>搜索要添加的商品:</span>
			<?php if ($depot_type == 'rk004'): ?>
			条形码：<input type="text" name="provider_barcode" style="width:150px;" />
			<?php else: ?>
			商品款号或名称：<input type="text" class="ts" name="provider_goods" value="" style="width:100px;" />
			品牌：<?php print form_dropdown('brand_id',$brand_list);?>
			供应商：<?php print form_dropdown('provider_id',$provider_list);?>
			合作方式：<?php print form_dropdown('cooperation_id',$type_list);?>
			状态：<?php print form_dropdown('provider_status',$provider_status);?>
			<?php endif; ?>
			<input type="checkbox" name="with_not" id="with_not" checked >过滤已有商品
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
					<td colspan="10" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="280px">商品款号|商品名称|供应商货号</th>
					<th>品牌</th>
					<th>供应商名称</th>
					<th>售价</th>
					<th>状态</th>
					<th>颜色|尺码</th>
					<th>包装方式</th>
                                        <th>重量</th>
					<th>批次</th>
					<th>最大入库数</th>
					<th width="300px;">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
				<tr class="row">
					<td><?php print $row->product_sn; ?>&nbsp;|&nbsp;<?php print $row->product_name; ?>&nbsp;|&nbsp;<?php print $row->provider_productcode; ?></td>
					<td><?php print $row->brand_name; ?></td>
                    <td><?php print $row->provider_name; ?></td>
					<td><?php print $row->shop_price; ?></td>
					<td><?php print $row->status_name; ?></td>
					<td><?php print $row->color_name.'['.$row->color_sn.']'; ?>| <?php print $row->size_name.'['.$row->size_sn.']'; ?></td>
                                        
                    <td data-pk="<?php print $row->product_id;?>" title ="点击可修改" data-name="pack_method" class="editable" data-type="textarea" data-value="<?php print $row->pack_method; ?>"><?php print $row->pack_method; ?> </td>
                    <td data-pk="<?php print $row->product_id;?>" title ="点击可修改" data-name="product_weight" class="editable" data-type="textarea" data-value="<?php print $row->product_weight; ?>"><?php print $row->product_weight; ?> </td>
					
                    <td>
						<?php print $row->batch_code; ?><?php if($row->is_reckoned==1): ?><br>(已结算)<?php endif; ?>
                                            <input type="hidden" name="batchid_<?php print $row->sub_id; ?>" id="batchid_<?php print $row->sub_id; ?>" value="<?php print $row->batch_id; ?>" />
					</td>
					<td><?php print ($row->max_num === 'big')?'不限':$row->max_num; ?></td>
					<td>
					<div id="div_<?php print $row->sub_id; ?>">
					<p id="p_<?php print $row->sub_id; ?>_<?=$row->batch_id;?>">
					储位:<input type="text" class="textbox" name="location_<?=$row->sub_id;?>_<?=$row->batch_id; ?>" id="location_<?=$row->sub_id;?>_<?=$row->batch_id;?>" value="<?php print $row->location_name;?>" ondblclick="showLoactionWin(this,'<?php print $depot_in_info->depot_depot_id; ?>');" style="width:100px;" />&nbsp;&nbsp;
					数量:<input type="text" class="textbox" name="innum_<?=$row->sub_id;?>_<?=$row->batch_id; ?>" id="innum_<?=$row->sub_id; ?>_<?=$row->batch_id; ?>" value="<?php print $filter['type']==4?$row->need_in_num:'' ?>" style="width:50px;" />&nbsp;<a href="#" onclick="add_p('<?php print $row->sub_id ?>');return false;" value="add" /><!--<img src="<?php /*echo $imagedomain*/ ?>/add.gif" border="0" />用样式显示 By Rock--><span class="addForGif"></span></a>
					</p>
					</div>
					<input type="hidden" name="addnum_<?php print $row->sub_id; ?>" id="addnum_<?php print $row->sub_id; ?>" value="1" />
					</td>
				</tr>
				<?php endforeach; ?>
				<tr>
					<td colspan="10" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
			<div>
			<input type="button" class="r" name="add_p" id="add_p" value="添加到入库单" onclick="add_product_in()" <?php echo !empty($list)?'':'disabled' ?> />
			</div>
			<div class="page">
				<?php include(APPPATH.'views/common/page.php') ?>
			</div>

<?php endif; ?>
<?php if($full_page): ?>

<script>
// jquery editable 
function _editable(){
$('.editable').editable({ url: '/quick_edit/pack_method', emptytext:'点击可修改',
        success: function(response, newValue) {
            if(!response.success) return response.msg;
            if( response.value != newValue ) return '操作失败';
        }
    });
}
//listTable.func = _editable; // 分页加载后调用的函数名
_editable();
</script>
		</div>
		</div></div>
	</div>
	<input type="hidden" name="depot_in_id" id="depot_in_id" value="<?php print $depot_in_info->depot_in_id; ?>" />
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
