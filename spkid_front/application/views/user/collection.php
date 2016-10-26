<?php if($full_page): ?>
<?php include APPPATH."views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />
<script type="text/javascript">
var order_status = '<?php echo $order_status ?>';
var order_page_count = '<?php echo $filter["page_count"] ?>';
var order_page = '<?php echo $filter["page"] ?>';
function filter_result(status,page)
{
	if (status == 0)
	{
		status = order_status;
	}
	page_count = order_page_count;

	if (page == 0)
	{
		page = order_page;
	}
	if(page < 1)
	{
		page = 1;
	}
	if(page > page_count)
	{
		page = page_count;
	}

			$.ajax({
				url:'/user/collection',
				data:{is_ajax:true,status:status,page:page,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						$('#listdiv').html(result.content);
						order_status = result.order_status;
					}
				}
			});

		return false;
}

function del_collection(rec_id,status)
{
	if (rec_id > 0)
	{
		if (confirm('确定删除吗'))
		{
			$.ajax({
				url:'/user/collection_del',
				data:{is_ajax:true,rec_id:rec_id,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						filter_result(status,1);
					} else
					{
						alert(result.msg);
					}
				}
			});
		}
	}
	return false;
}
</script>
<div id="content">
	<div class="now_pos">
		<a href="/">首 页</a>
		>
		<a href="/user">会员中心</a>
		>
		<a class="now">我的收藏</a>
                <!-- come soon
		<a class="notice" href="/">全场满200减20!</a>
                -->
	</div>
	<div class="ucenter_left">
	<?php include APPPATH."views/user/left.php"; ?>
	</div>
	<div class="ucenter_main">
		<div class="switch_block" id="listdiv">
			<?php endif; ?>
			<!-- <div class="switch_block_title">
				<ul>
					<li <?php if ($order_status == 1): ?>class="sel"<?php else: ?>onclick="filter_result(1,1);return false;"<?php endif;?>>商品收藏</li>
					<li <?php if ($order_status == 2): ?>class="sel"<?php else: ?>onclick="filter_result(2,1);return false;"<?php endif;?>>礼包收藏</li>
				</ul>
			</div> -->
			<div class="switch_block_content collectionList">
				<table width="748" border="0" cellspacing="0" cellpadding="0">
					<?php if ($order_status == 1): ?>
					<tr>
						<th width="15%">图片</th>
						<th width="25%">品牌</th>
						<th width="23%">名称</th>
						<th width="13%">单价</th>
						<th width="24%">操作</th>
					</tr>
					<?php if (!empty($collection_list)):
					foreach ($collection_list as $collection): ?>
					<tr>
						<td>
							<a href="/<?php echo $collection->url ?>" target="_blank">
								<img src="<?php echo $collection->teeny_url ?>" width="85" height="85" />
							</a>
						</td>
						<td><?php echo $collection->brand_name ?></td>
						<td><?php echo $collection->product_name ?></td>
						<td class="red"><?php echo empty($collection->promote_price)? $collection->shop_price:$collection->promote_price; ?>元</td>
						<td>
							<a class="btn_g_52" href="/<?php echo $collection->url ?>" target="_blank">购买</a>
							<a class="btn_gray_52" href="javascript:void(0)" onclick="del_collection('<?php echo $collection->rec_id ?>',1);return false;">移除</a>
						</td>
					</tr>
					<?php endforeach; endif; ?>
					<tr>
						<td colspan="5">
							<div class="switch_block_page ablack">
							<?php include(APPPATH.'views/user/page.php') ?>
							</div>
						</td>
					</tr>
					<?php else: ?>
					<tr>
						<th width="15%">图片</th>
						<th width="25%">状态</th>
						<th width="23%">礼包名称</th>
						<th width="13%">礼包价格</th>
						<th width="24%">操作</th>
					</tr>
					<?php if (!empty($collection_list)):
					foreach ($collection_list as $collection): ?>
					<tr>
						<td>
							<a href="<?php echo $collection->url ?>" target="_blank">
								<img src="<?php echo $collection->teeny_url ?>"  width="85" height="85" />
							</a>
							</td>
						<td><?php echo $collection->package_status == 1? '热卖中':'已过期' ?></td>
						<td><?php echo $collection->package_name ?></td>
						<td class="cred_b">￥<?php echo $collection->formated_package_amount; ?></td>
						<td>
							<a class="btn_g_52" href="#this">购买</a>
							<a class="btn_gray_52" href="#" onclick="del_collection('<?php echo $collection->rec_id ?>',2);return false;">移除</a><
						</td>
					</tr>
					<?php endforeach; endif; ?>
					<tr>
						<td colspan="5">
							<div class="switch_block_page ablack">
							<?php include(APPPATH.'views/user/page.php') ?>
							</div>
						</td>
					</tr>
					<?php endif; ?>
				</table>
			</div>
			<?php if($full_page): ?>
		</div>
	</div>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>
<?php endif; ?>