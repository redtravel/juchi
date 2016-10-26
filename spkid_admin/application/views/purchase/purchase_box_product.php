<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
<!--    <script type="text/javascript" src="public/js/jquery.form.js" ></script> -->
    <script type="text/javascript">
	function p_updata(){
	    $("#up_form").ajaxSubmit(function(data){
		data = jQuery.parseJSON(data);
		if(data.err == 1){
		    $("input[name=p_num_"+data.target+"]").attr("class","p_edit p_err");
		    alert(data.msg);
		}else{
		    alert("修改完成");
		    $("input.p_err").attr("class","p_edit");
		}
	    });
	}
	
	function p_audit(){
	    $.post("/purchase_box/proc_box_prodcut_statistics/<?php echo $box->box_id; ?>",function(data){
		   data = jQuery.parseJSON(data);
		   if(data.err == 1){
		        alert(data.msg);
		   }else{
			alert("重新审核完成");
		    }
	    });
	}
    </script>
    <style type="text/css">
	.p_edit{width:30px}
	.p_err{backgroud:red}
    </style>
	<div class="main">
		<div class="main_title">
		<span class="l">仓库管理 &gt;&gt; <a href="purchase_box">收货箱列表</a>&gt;&gt;商品查看</span>
        <span style='float:right'><a href="purchase_box">返回列表</a></span>
		</div>
		<div class="blank5"></div>
        <div class="search_row">
            箱号:&nbsp;&nbsp;<?=$box->box_code?> 
        </div>
		<div class="blank5"></div>
		<div id="listDiv">
		<form id="up_form" action="/purchase_box/proc_box_prodcut_edit/<?php echo $box->box_id; ?>" method="POST">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th>商品款号</th>
				  <th>名称</th>
				  <th>品牌</th>
				  <th>颜色</th>
				  <th>规格</th>
				  <th>条形码</th>
				  <th>货号</th>
				  <th>收货数量</th>
				  <th>收件人</th>
				  <th>上架数量</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
					<td><?=$row->product_sn?></td>
					<td><?=$row->product_name?></td>
					<td><?=$row->brand_name?></td>
					<td><?=$row->color_name?></td>
					<td><?=$row->size_name?></td>
					<td><?=$row->provider_barcode?></td>
					<td><?=$row->provider_productcode?></td>
					<td><?php if($edit){ echo '<input class="p_edit" name="p_num_'.$row->box_sub_id.'" value="'.intval($row->product_number).'" />';} else{echo intval($row->product_number);}?></td>
					<td><?=$row->realname?></td>
					<td><?=intval($row->over_num)?></td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="7" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
<?php if($full_page): ?>
			</form>
			<?php if($edit):?>
			<div class="r">
			    <input  type="button"  class="am-btn am-btn-secondary" value="提交数量更改" onclick="p_updata();"/>
			    <input  type="button"  class="am-btn am-btn-secondary" value="检查是否完结" onclick="p_audit();"/>
			</div>
			<?php endif; ?>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
