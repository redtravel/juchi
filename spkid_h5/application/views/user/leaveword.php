<?php if($full_page): ?>
<?php include APPPATH."views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />
<script type="text/javascript">
var order_page_count = '<?php echo $filter["page_count"] ?>';
var order_page = '<?php echo $filter["page"] ?>';
function filter_result(status,page)
{
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
				url:'/user/leaveword',
				data:{is_ajax:true,page:page,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.error==0){
						$('#listdiv').html(result.content);
					}
				}
			});

		return false;
}
</script>
<div id="content">
	<div class="now_pos">
		<a href="/">首 页</a>
        >
        <a href="/user">会员中心</a>
        >
        <a class="now">点评与咨询</a>
	</div>
	<div class="ucenter_left">
	<?php include APPPATH."views/user/left.php"; ?>
	</div>
	<div class="ucenter_main">
		<div class="switch_block">
			<div class="switch_block_title">
				<ul>
					<li onclick="location.href='/user/liuyan'">我的点评</li>
					<li class="sel">我的咨询</li>
				</ul>
			</div>
			<?php endif; ?>
			<div class="switch_block_content leavewordList">
				<table width="738" border="0" cellspacing="0" cellpadding="0">
					<?php if (!empty($liuyan_list)):
					 foreach ($liuyan_list as $liuyan): ?>
					<tr>
						<td width="9%" align="center" valign="top">
							<a href="<?php echo $liuyan->url ?>" target="_blank">
								<img src="<?php echo $liuyan->teeny_url ?>" width="48" height="48" />
							</a>
						</td>
						<td width="91%">
							<div class="leavewordListC">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td>
											<?php echo $liuyan->brand_name ?>&nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $liuyan->product_name ?>&nbsp;&nbsp;&nbsp;&nbsp;
											<font class="red"><?php if(isset($liuyan->sale_price)) { ?><?php echo $liuyan->sale_price ?>元<?php } ?></font>
										</td>
									</tr>
									<tr>
										<td>
											<font class="bold">咨询内容:</font>
											<?php echo empty($liuyan->comment_title)?$liuyan->comment_content: $liuyan->comment_title."<br />".$liuyan->comment_content; ?>&nbsp;&nbsp;&nbsp;
											<font class="c99"><?php echo $liuyan->comment_date ?></font>
										</td>
									</tr>
									<?php if (!empty($liuyan->reply_content)): ?>
									<tr>
										<td>
											<font class="red bold">客服回复:</font>
											<font class="red"><?php echo $liuyan->reply_content; ?><br></font>
											<font class="line14 marginL65 red"><?php echo $liuyan->reply_date ?></font>
										</td>
									</tr>
									<?php else: ?>
									<tr>
										<td>暂未回复</td>
									</tr>
									<?php endif; ?>
								</table>
							</div>
						</td>
					</tr>
					<?php endforeach; endif; ?>
					<tr>
						<td colspan="5" class="bottomPage">
							<div class="switch_block_page ablack">
							<?php include(APPPATH.'views/user/page.php') ?>
							</div>
						</td>
					</tr>
				</table>
			</div>
			<?php if($full_page): ?>
		</div>
	</div>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>
<?php endif; ?>
