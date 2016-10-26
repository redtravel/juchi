<?php include APPPATH.'views/common/rf_header.php'; ?>
<div class="main">
  <div class="blank5"></div>
	<?php print form_open_multipart('purchasebox_scanning/add',array('name'=>'mainForm','id'=>'mainForm'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">
                                        
				</td>
				<td class="item_input">
					<input type="button" class="am-btn am-btn-secondary" value="取消本次扫描" name="myreset" id="myreset"/>
					<?php if(!empty($product_number) && !empty($shelve_num)) print $product_number."/".$shelve_num;?>
				</td>
			</tr>			
			<tr>
				<td class="item_title">上架扫描:</td>
				<td class="item_input">
					<input name="data_code" class="textbox" id="data_code" onkeypress="if(event.keyCode==13) {return false;}"/>
				</td>
			</tr>
			<tr>
				<td class="item_title">箱号:</td>
				<td class="item_input">
					<span id="b_code"></span>
				</td>
			</tr>
			<tr>
				<td class="item_title">SKU数量:</td>
				<td class="item_input">
					<span id="g_code"></span>
					<span id="g_num"></span>
				</td>
			</tr>
			<tr>
				<td class="item_title">储位:</td>
				<td class="item_input">
					<span id="d_code"></span>
				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<input type="button" class="am-btn am-btn-secondary" value="提交本次扫描" name="mysubmit" id="mysubmit" onclick="$('#mainForm').submit();"/>
				</td>				
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
		<input type="hidden" id="box_code" name="box_code" value="">
		<input type="hidden" id="goods_code" name="goods_code" value="">
		<input type="hidden" id="goods_num" name="goods_num" value="0">
		<input type="hidden" id="depot_code" name="depot_code" value="">
	<?php print form_close();?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td class="item_input" style="padding: 20px;">
					<?php if ($is_finished == 0):?>
					<span style="color: green">该箱号 <?php print $box_code;?> 已上架 <?php print $shelve_num;?> 件,还有 <?php print $unshelve_num;?> 件待上架。</span>
					<span style="color: red" id="data_msg"></span>
					<?php elseif ($is_finished == 1):?>
					<span style="color: green">本次扫描已完成。</span>
					<?php else :?>
					<span style="color: red" id="data_msg"></span>
					<?php endif;?>
					
				</td>
			</tr>
		</table>
</div>
<script type="text/javascript">
	//<![CDATA[
	$(document).ready(function(){
		$("#data_code").attr("disabled",false);
		$("#data_code").val('');
		$("#box_code").val('');
		$("#goods_code").val('');
		$("#goods_num").val('0');
		$("#depot_code").val('');
		$("#data_code").focus();
		//重新加载box
		<?php if(!empty($box_code)):?>
		var box_code = '<?php echo $box_code;?>';
		if(box_code !=""){
		    $("#box_code").val(box_code);
		    $("#b_code").html(box_code);
		}
	    <?php endif;?>
		//扫描箱号、商品条码、储位
		$("#data_code").keydown(function(event){
			$("#data_msg").html('');
			//检测扫描值是箱号或商品条码或储位
			if(event.which == 13){
				var data_code = $("#data_code").val();
				var box_code = $("#box_code").val();
				var goods_code = $("#goods_code").val();
				var goods_num = $("#goods_num").val();
				
				if(data_code == "" || data_code == null){
					$("#data_code").val('');
					$("#data_code").focus();
					$("#data_msg").html("扫描提示：扫描失败，扫描文本框不能为空，请重新扫描。");
				}else{
					if(box_code == "" || box_code == null){
					    $.post("/purchase_box/check_box/0000/"+data_code,
						{is_ajax:1,rnd : new Date().getTime()},
						function(data){
						    data = jQuery.parseJSON(data);
						    if(data.is_exists != true){
							alert('此箱号不存在');
							$("#box_code").val('');
							$("#b_code").html('');
						    }else if(data.is_close == true){
							alert('箱子所有商品已经上架');
							$("#box_code").val('');
							$("#b_code").html('');
						    }else{
							$("#box_code").val(data_code);
							$("#b_code").html(data_code);
						    }
						});
						$("#data_code").val('');
						$("#data_code").focus();
					}else{
						//是商品条码
						if(!check_is_location(data_code)){
							//判断扫描的是否是同一件商品
							if(goods_code != "" && goods_code != null && data_code != goods_code){
								$("#data_code").val('');
								$("#data_code").focus();
								$("#data_msg").html("扫描提示：商品扫描失败，扫描必须是同一件商品。");
								return false;
							}
							$("#goods_code").val(data_code);
							$("#goods_num").val(parseInt(goods_num)+parseInt(1));
							$("#g_code").html(data_code+"：");
							$("#g_num").html(parseInt(goods_num)+parseInt(1));
							$("#data_code").val('');
							$("#data_code").focus();
						}else{
							$("#depot_code").val(data_code);
							$("#d_code").html(data_code);
							$("#data_code").val("本次扫描已完成");
							$("#data_code").attr("disabled",true);
						}
					}
				}
			}	
		});
		
		$("#myreset").click(function(event){
			$("#data_msg").html('');
			$("#data_code").attr("disabled",false);
			$("#data_code").val('');
			//$("#box_code").val('');
			$("#goods_code").val('');
			$("#goods_num").val('0');
			$("#depot_code").val('');
			
			//$("#b_code").html('');
			$("#g_code").html('');
			$("#g_num").html('');
			$("#d_code").html('');
			$("#data_code").focus();
		});
			
	});
	var pattern = /(^[a-zA-Z0-9]{1,3})-(\d{1,3})-(\d{1,3})-(\d{1,3})-(\d{1,3})$/; 
	function check_is_location(str){
	    if(str.indexOf("-") == -1){
		return false;
	    }
	   return pattern.test(str);
	}
	//]]>
</script>

<?php include(APPPATH.'views/common/rf_footer.php');?>