<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript">
		//<![CDATA[
		var index = <?echo $index ?>;
		    $(function(){
			 $(".enterQuery").bind('keydown', function(event){
				if(event.keyCode==13){
				    search();
				}
			    });
			  $(":input[type=radio][name=model]").click(function(event){
				var type = $(event.target).val();
				if(type == "scan_model"){
				    $("#scan_model").show();
				    $("#file_model").hide();
				}else{
				    $("#scan_model").hide();
				    $("#file_model").show();
				}
			      
			  });  
		    });
		   
		    function search(){
			var provider_barcode = $("#provider_barcode").val();
			if(provider_barcode == null || provider_barcode=="")
			    return false;
			var inputs="";
			$.ajax({
				url: '/product_api/batch_provider_barcode',
				data: {provider_barcode : provider_barcode, rnd : new Date().getTime()},
				dataType: 'json',
				type: 'POST',
				success: function(result){
				    index ++;
				    if(result != null)
				    {
					var style="";
					if(result.length == 1){
					    style="style='background:yellow'";
					}
					if(result.length > 1){
					    style="style='background:red'";
					}
					for(i = 0;i<result.length;i++){
					    var data = result[i];
					    inputs="";
					    inputs =append_td(inputs,index);
					    inputs =append_td(inputs,data.provider_barcode);
					    inputs =append_td(inputs,data.product_sn);
					    var provider_name ="";
					    var provider_code ="";
					    if(data.provider_name != null){
						provider_name = data.provider_name;
					    }
					    if(data.provider_code != null){
						provider_code = data.provider_code;
					    }
					    if(provider_name != "" && provider_code != "")
						inputs =append_td(inputs,provider_name+"["+provider_code+"]");
					    else
						inputs =append_td(inputs,"");
					    inputs =append_td(inputs,data.provider_productcode);
					    inputs =append_td(inputs,data.color_name);
					    inputs =append_td(inputs,data.size_name);
					    inputs =append_td(inputs,data.batch_code);
					    if(data.consign_type == 0){
						inputs =append_td(inputs,'非代销');
					    }else if(data.consign_type == 1){
						inputs =append_td(inputs,'固定代销价');
					    }else if(data.consign_type == 2){
						inputs =append_td(inputs,'浮动代销率');
					    }else{
						inputs =append_td(inputs,'非法类型');
					    }
					    inputs =append_td(inputs,data.consign_price);
					    inputs =append_td(inputs,data.cost_price);
					    inputs =append_td(inputs,data.consign_rate);
					    jQuery('<tr class="row" '+style+' >'+inputs+'</tr>').prependTo('#content_code');
					 }
				    }else{
					inputs =append_td(inputs,index);
					inputs =append_td(inputs,provider_barcode);
					inputs =append_td(inputs,"");
					inputs =append_td(inputs,"");
					inputs =append_td(inputs,"");
					inputs =append_td(inputs,"");
					inputs =append_td(inputs,"");
					inputs =append_td(inputs,"");
					inputs =append_td(inputs,"");
					inputs =append_td(inputs,"");
					inputs =append_td(inputs,"");
					inputs =append_td(inputs,"");
					jQuery('<tr class="row" >'+inputs+'</tr>').prependTo('#content_code');
				    }
				    $("#provider_barcode").val("");
				    $("#provider_barcode").focus();
				}
			     });
			}
			
			function append_td(content,val){
			    if(val == null){
				val = "";
			    }
			   return content+= "<td> "+val+" </td>";
			}
		//]]>
	</script>
	<div class="main">
		<div class="main_title">
		<span class="l">批次老货查询</span>
		</div>
		<div class="blank5"></div>
		<div class="search_row">
		    <label><input name="model" type="radio" value="scan_model" checked="true"/>扫描模式</label>
		    <label><input name="model" type="radio" value="file_model"/>批次导入模式</label>
		    <div id="scan_model">供应商条码：<input type="text" class="ts enterQuery" id="provider_barcode" style="width:220px;" /></div>
		    <div id="file_model" style="display: none">
			<form action="/product/batch_check_provider_barcode" method="post" enctype="multipart/form-data" >
			上传文件：<input type="file" name="data_file" />
			<input type="submit" class="am-btn am-btn-primary" value="提交确认"/>
			[<a href="public/import/_template/provider_barcode.xml">下载模板_右键另存</a>]
			</form>
		    </div>
		    <div>
		    温馨提示：扫描后查询老货条形码记录。<br />
		    【<span style='background:red'>红色</span>】表示存在多条对应老货记录，序号相同；
		    【<span style='background:yellow'>黄色</span>】表示存在一条老货记录；
		    【<span>白色</span>】表示可能不存在老货记录；
		    </div>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr class="row">
					<td colspan="12" class="topId"></td>
				</tr>
				<tr class="row">
				    <th> 序列号 </th>
				    <th> 条形码 </th>
				    <th> 商品款号 </th>
				    <th> 供应商 </th>
				    <th> 货号 </th>
				    <th> 颜色 </th>
				    <th> 尺码 </th>
				    <th> 批次号 </th>
				    <th> 销售类型 </th>
				    <th> 代销成本价 </th>
				    <th> 买断成本价 </th>
				    <th> 浮动代销率 </th>
				</tr>
				<tbody id="content_code">
				    <?php if($full_page):
					foreach($result_data as $result):
					$style ="";
					$count = $muti_data[$result["index"]];
					if($count == 1){
					    $style = "style='background:yellow'";
					}else if($count > 1){
					    $style = "style='background:red''";
					}
					?>
				    <tr class="row" <?echo $style ?>>
					<td><?echo $index +1 -$result["index"] ?></td>
					<td><?echo $result["provider_barcode"] ?></td>
					<td><?echo $result["product_sn"] ?></td>
					<td><?echo $result["provider"] ?></td>
					<td><?echo $result["provider_productcode"] ?></td>
					<td><?echo $result["color_name"] ?></td>
					<td><?echo $result["size_name"] ?></td>
					<td><?echo $result["batch_code"] ?></td>
					<td><?php if($result["consign_type"]==0)echo'非代销';
						elseif($result["consign_type"]==1)echo'固定代销价';
						elseif($result["consign_type"]==2)echo'浮动代销率'; 
						else echo'非法类型'?>
					</td>
					<td><?echo $result["consign_price"] ?></td>
					<td><?echo $result["cost_price"] ?></td>
					<td><?echo $result["consign_rate"] ?></td>
				    </tr>
				    <?php endforeach; endif;?>
				</tbody>
				<tr class="row">
					<td colspan="12" class="bottomTd"></td>
				</tr>
			</table>
			<div class="blank5"></div>
		</div>
		<div class="blank5"></div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
