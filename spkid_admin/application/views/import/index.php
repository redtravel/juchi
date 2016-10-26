<?php include(APPPATH.'views/common/header.php'); ?>
	<div class="main">
		<div class="main_title">批量导入 <a href="import/history" class="return r" target="_blank">历次导入列表</a></div>
		<div class="search_row">
			<form name="upload" action="import/upload" method="post" enctype="multipart/form-data" onsubmit="return confirm('上传新文件将覆盖旧文件，确定上传？')">
			类型
			<select name="data_type">
				<option value="product">新货主要信息导入</option>
				<option value="color_size">新货颜色规格导入</option>
				<option value="product_sub">新货次要信息导入</option>
				<option value="product_cost">商品成本价</option>
				<option value="product_price">商品售价修改</option>
				<option value="purchase">采购单</option>
				<option value="consign">虚库导入</option>
                                <option value="provider_barcode">修改条形码导入</option>
			</select>
			数据文件：<input type="file" name="data_file" value="" />
			<input type="submit" class="am-btn am-btn-primary" value="上传" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="6" class="topTd"> </td>
				</tr>
				<tr class="row">
					<th width="120px">项目</th>
					<th>描述</th>
					<th width="120px;">操作</th>
				</tr>
				<tr class="row">
					<td>新货主要信息导入</td>
					
					<td>
						将要导入的数据按照模板格式整理好后，(Excel XML 表格， UTF-8编码)格式， 并上传到 <font color="red">public/import/product/</font>目录下。<br/>注:第一行默认为标题,将不执行导入操作，
导入完成后点击右边的"查看结果" ，显示商品的导入状态。
						[<a href="public/import/_template/product.xml">下载模板_右键另存</a>]
					</td>
					<td>
						<a href="import/product" onclick="if(confirm('确定执行该操作？')) $(this).hide(); else return false;">执行导入</a>
						<a href="import/product_result">查看结果</a>
					</td>
				</tr>
				<tr class="row">
					<td>新货颜色规格导入</td>
					
					<td>
						将要导入的数据按照模板格式整理好后，(Excel XML 表格， UTF-8编码)格式， 并上传到 <font color="red">public/import/color_size/</font>目录下。<br/>注:第一行默认为标题,将不执行导入操作，
导入完成后点击右边的"查看结果" ，显示商品的导入状态。
						[<a href="public/import/_template/color_size.xml">下载模板_右键另存</a>]
					</td>
					<td>
						<a href="import/color_size" onclick="if(confirm('确定执行该操作？')) $(this).hide(); else return false;">执行导入</a>
						<a href="import/color_size_result">查看结果</a>
					</td>
				</tr>
				<tr class="row">
					<td>新货次要信息导入</td>
					
					<td>
						将要导入的数据按照模板格式整理好后，(Excel XML 表格， UTF-8编码)格式， 并上传到 <font color="red">public/import/product_sub/</font>目录下。<br/>注:第一行默认为标题,将不执行导入操作，
导入完成后点击右边的"查看结果" ，显示商品的导入状态。
						[<a href="public/import/_template/product_sub.xml">下载模板_右键另存</a>]
					</td>
					<td>
						<a href="import/product_sub" onclick="if(confirm('确定执行该操作？')) $(this).hide(); else return false;">执行导入</a>
						<a href="import/product_sub_result">查看结果</a>
					</td>
				</tr>
				<tr class="row">
					<td>图片导入</td>
					
					<td>
						把图片和模特文件按照 "款号_颜色编码/图片文件" 的目录结构上传到目录 <font color="red">public/import/gallery/</font>目录下。
						<br/>注1：默认图命名为1.扩展名,色片命名为2.扩展名，局部图任意命名，导入完成后点击右边的"查看结果" ，显示商品的导入状态。 
						<br/>注2：模特文件命名："model"+模特编码.txt(文件的内容为空)，如model321.txt。
					</td>
					<td>
						<a href="import/gallery" onclick="if(confirm('确定执行该操作？')) $(this).hide(); else return false;">执行导入</a>
						<a href="import/gallery_result">查看结果</a>
					</td>
				</tr>
                                <tr class="row">
					<td>商品成本价导入</td>
					
					<td>
						把商品成本价文件，(Excel XML 表格， UTF-8编码)格式，上传到目录 <font color="red">public/import/pro_cost/</font>目录下。<br/>注：如果供应商合作方式为代销，则需填写 代销价或者代销率（二选一）；如果供应商合作方式为买断，则需填写成本价。
                                                [<a href="public/import/_template/product_cost.xml">下载模板_右键另存</a>]
					</td>
					<td>
						<a href="import/product_cost" onclick="if(confirm('确定执行该操作？')) $(this).hide(); else return false;">执行导入</a>
						<a href="import/product_cost_result">查看结果</a>
					</td>
				</tr>
                                <tr class="row">
					<td>商品售价修改导入</td>
					
					<td>
						把修改商品售价文件，(Excel XML 表格， UTF-8编码)格式，上传到目录 <font color="red">public/import/pro_price/</font>目录下。
                                                [<a href="public/import/_template/product_price.xml">下载模板_右键另存</a>]
					</td>
					<td>
						<a href="import/product_price" onclick="if(confirm('确定执行该操作？')) $(this).hide(); else return false;">执行导入</a>
						<a href="import/product_price_result">查看结果</a>
					</td>
				</tr>
                                <tr class="row">
					<td>采购单导入</td>
					<td>
						把采购单文件，(Excel XML 表格， UTF-8编码)格式，上传到目录 <font color="red">public/import/purchase/</font>目录下。<br/>注：采购单模版中只能更改数量。[模版下载地址： 商品管理->商品列表->导出采购单模版]
					</td>
					<td>
						<a href="import/purchase" onclick="if(confirm('确定执行该操作？')) $(this).hide(); else return false;">执行导入</a>
						<a href="purchase">查看结果</a>
					</td>
				</tr>
                                <tr class="row">
					<td>虚库导入</td>
					<td>
					    将要导入的数据按照模板格式整理好后，(Excel XML 表格， UTF-8编码)格式， 并上传到 <font color="red">public/import/consign/</font>目录下。<br/>注:第一行默认为标题,将不执行导入操作，
导入完成后点击右边的"查看结果" ，显示商品虚库的导入状态。
						[<a href="public/import/_template/consign.xml">下载模板_右键另存</a>]
					</td>
					<td>
						<a href="import/consign" onclick="if(confirm('确定执行该操作？')) $(this).hide(); else return false;">执行导入</a>
						<a href="import/consign_result">查看结果</a>
					</td>
				</tr>
                                <tr class="row">
					<td>修改条形码导入</td>
					<td>
					    将要导入的数据按照模板格式整理好后，(Excel XML 表格， UTF-8编码)格式， 并上传到 <font color="red">public/import/provider_barcode/</font>目录下。<br/>注:第一行默认为标题,将不执行导入操作，
导入完成后点击右边的"查看结果" ，显示商品条形码的导入状态。
						[<a href="public/import/_template/provider_barcode_update.xml">下载模板_右键另存</a>]
					</td>
					<td>
						<a href="import/provider_barcode" onclick="if(confirm('确定执行该操作？')) $(this).hide(); else return false;">执行导入</a>
						<a href="import/provider_barcode_result">查看结果</a>
					</td>
				</tr>
				<tr>
					<td colspan="6" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
