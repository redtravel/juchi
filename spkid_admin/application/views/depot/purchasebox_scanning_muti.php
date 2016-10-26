<?php include APPPATH.'views/common/rf_header.php'; ?>
<style type="text/css">
#content{overflow-x:hidden}
#g_code{width:158px;display:block;overflow:hidden;word-break:break-all;}
.btn_submit,.btn_re{padding:5px 12px;border:0;}
.btn_submit{background-color:#FF582F;color:#fff;}
.btn_re{background-color:#FFFBA3}
</style>
<div class="main">
  <div class="blank5"></div>
	<?php print form_open_multipart('purchasebox_scanning/mu_add',array('name'=>'mainForm','id'=>'mainForm'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">
					<input type="button" class="btn_re" value="重置" name="myreset" id="myreset"/>
				</td>
				<td class="item_input">
					<input type="button" class="btn_submit" value="提交本次扫描" name="mysubmit" id="mysubmit" onclick="$('#mainForm').submit();"/>
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
				<td class="item_title">储位:</td>
				<td class="item_input">
					<span id="d_code"></span>
				</td>
			</tr>
			<tr>
				<td class="item_title" valign="top">SKU数量:</td>
				<td class="item_input">
					<span id="g_code" ></span>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
		<input type="hidden" id="box_code" name="box_code" value="">
		<input type="hidden" id="goods_code" name="goods_code" value="">
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
                                                    
							if(goods_code =="") {
								var content = data_code+":"+String(parseInt(1),10);
								$('#goods_code').attr('value',content);
								$("#g_code").html(content);
							}else{
								var string = $('#goods_code'),
									array1=[],
									array2=[],
									html=$("#g_code");
								toCon(string,array1,array2,data_code,html);
							}
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
			$("#box_code").val('');
			$("#goods_code").val('');
			$("#depot_code").val('');

			$("#b_code").html('');
			$("#g_code").html('');
			$("#d_code").html('');
			$("#data_code").focus();
		});
			
	});
	
	function toCon(str,arr1,arr2,data,html) {
		var val=str.attr('value'),
			l=val.length,
			re=/,/g,
			strFinal='';
		//拆分数组1
		arr1=val.split(',');
		var len=arr1.length,nowNum=null;
		//拆分数组2
		for(var i=0;i<len;i++){
			arr2.push(arr1[i].split(':'));
		}
		//搜索是否有相等值
		for(var i=0;i<len;i++){if(data==arr2[i][0]) nowNum=i}
		//根据是否存在相等值来判断
		if (nowNum==null) {
			for(var i=0;i<len;i++){
				strFinal += arr2[i][0]+':'+arr2[i][1]+',';
			}
			strFinal += data+':'+'1,';
		} else {
			arr2[nowNum][1]=parseInt(arr2[nowNum][1])+1;
			for(var i=0;i<len;i++){
				strFinal = strFinal+arr2[i][0]+':'+arr2[i][1]+',';
			}
		}
		strFinal=strFinal.slice(0,strFinal.length-1);
		strFinalBR=strFinal.replace(re,'<br>');
		str.attr('value',strFinal);
		html.html(strFinalBR);
	}
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