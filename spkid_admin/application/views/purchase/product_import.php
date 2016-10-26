<?php include(APPPATH.'views/common/header.php'); ?>
	<div class="main">
                <div class="main_title"><span class="l">采购管理 &gt;&gt; 导入产品有效期</span> <span class="r">[ <a href="/purchase/index">返回列表 </a>]</span></div>

		<div class="search_row">
			<form name="upload" action="purchase/product_import" method="post" enctype="multipart/form-data" onsubmit="return confirm('上传新文件将覆盖旧文件，确定上传？')">			
			数据文件：<input type="file" name="data_file" value="" />
			<input type="submit" class="am-btn am-btn-primary" value="上传" />
                        <a target="_blank" style="color:red;margin-left:20px;" href="public/import/purchase/purchase_product.xml">模板下载（右键另存）</a>
			</form>
		</div>
		<div class="blank5"></div>
                <?php if(isset($purchase)): ?>
                <div id="listDiv">
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
                            <form name="product_form" action="purchase/product_import_proc" method="POST">
                                <input type="hidden" name="purchase_code" value="<?=$purchase->purchase_code?>">
                                <tr>
					<td colspan="3" style="text-align: left;padding-left:5px; font-weight: bold;height:30px;">采购单号：<?=$purchase->purchase_code?></td>
				</tr>
				<tr class="row">
					<th width="120px">商品款号</th>
					<th>过期日期</th>
					<th>系统提示</th>
				</tr>
                                <?php foreach($sub as $s): ?>
                                <?php if(isset($s['purchase_sub_id'])): ?>
                                <input type="hidden" name="purchase_sub_id[]" value="<?=$s['purchase_sub_id']?>">
                                <input type="hidden" name="exdate[]" value="<?=$s['exdate']?>">
                                <?php endif; ?>
				<tr class="row">
					<td><?=$s['product_sn']?></td>					
					<td><?=$s['exdate']?></td>
					<td><?=isset($s['err_msg']) ? $s['err_msg'] : '';?></td>
				</tr>
                                <?php endforeach; ?>
                                <tr>
					<td colspan="3" style="height:40px;"><input type="submit" class="am-btn am-btn-primary" value="确认提交" /></td>
				</tr>
				<tr>
					<td colspan="3" class="bottomTd"></td>
				</tr>
                            </form>
			</table>
			<div class="blank5"></div>
		</div>
                    <?php endif; ?>
		
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
