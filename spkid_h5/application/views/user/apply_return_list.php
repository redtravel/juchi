<?php
/**
 * 自助退货列表
 */
?>
<?php if($full_page): ?>
<?php include APPPATH."views/common/header.php"; ?>
<script type="text/javascript" src="<?php print static_style_url('js/jcarousellite.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/lhgdialog/lhgdialog.min.js') ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/user.js'); ?>"></script>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />

<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/common_new.css'); ?>" media="all" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/layoutFlow.css'); ?>" media="all" charset="utf-8" />
<script type="text/javascript" src="<?php print static_style_url('js/jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/util.js?20131121083606'); ?>" ></script>
<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/order.css'); ?>" media="all" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/orderCN.css'); ?>" media="all" charset="utf-8" />
<script type="text/javascript">
var apply_status = '<?php echo $apply_status ?>';
var return_page_count = '<?php echo $filter["page_count"] ?>';
var return_page = '<?php echo $filter["page"] ?>';
function filter_result(status,page)
{
	if (status == 0)
	{
		status = apply_status;
	}
        page_count = return_page_count;

	if (page == 0)
	{
		page = return_page;
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
				url:'/user/apply_return_list',
				data:{is_ajax:true,status:status,page:page,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
                                        if(result.error==0){
						$('#listDiv').html(result.content);
                                                apply_status = result.apply_status;
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
            <a class="now">自助退货</a>
    </div>
    <div class="ucenter_left">
    <?php include APPPATH."views/user/left.php"; ?>
    </div>
<div class="ucenter_main">
<div id="listDiv" class="switch_block">
    <?php endif; ?>
          <div class="switch_block_title">
                <ul>
                        <li <?php if ($apply_status == 1): ?>class="sel"<?php else: ?>onclick="filter_result(1,1);return false;"<?php endif;?>>所有申请单</li>
                        <li <?php if ($apply_status == 2): ?>class="sel"<?php else: ?>onclick="filter_result(2,1);return false;"<?php endif;?>>待处理</li>
                        <li <?php if ($apply_status == 3): ?>class="sel"<?php else: ?>onclick="filter_result(3,1);return false;"<?php endif;?>>处理中</li>
                        <li <?php if ($apply_status == 4): ?>class="sel"<?php else: ?>onclick="filter_result(4,1);return false;"<?php endif;?>>已处理</li>
                        <li <?php if ($apply_status == 5): ?>class="sel"<?php else: ?>onclick="filter_result(5,1);return false;"<?php endif;?>>已取消</li>
                </ul>
          </div>
          <div class="switch_block_content">
            <table width="100%" class="order_table">
              <thead>
                <tr>
                  <th width="12%">退货申请编号</th>
                  <th width="14%">订单号</th>
                  <th width="12%">快递名称</th>
                  <th width="16%">退包运单号</th>
                  <th width="14%">状态</th>
                  <th width="36%">操作</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach($list as $item):?>
                <tr>
                  <td><?=$item['apply_id']?></td>
                  <td><?=$item['order_sn']?></td>
                  <td><?=$item['shipping_name']?></td>
                  <td><?=$item['invoice_no']?></td>
                  <td><?=$item['apply_status']?></td>
                  <td class="cooptd">
                    <a href="/user/apply_return_view/<?=$item['apply_id']?>" class="see_order" target="_blank" title="查看" hidefocus>查看</a>
                    <?php if($item['can_modify']){?>
                    <a href="/user/modify_apply_return_info/<?=$item['apply_id']?>" class="see_order" target="_blank" title="修改" hidefocus>修改</a>
                    <?php }?>
                    <?php if($item['can_cancel']){?>
                    <a href="/user/cancel_apply_return/<?=$item['apply_id']?>" class="see_order4" target="_blank" title="取消申请" hidefocus
                        onclick="javascript:if(!confirm('确认取消退货申请吗？')) return false;"
                        >取消申请</a>
                    <?php }?>
                  </td>
                </tr>
              <?php endforeach;?>
              <?php if(empty($list)){?>
                  <tr>
                      <td colspan="6">暂时没有退货申请单</td>
                  </tr>
              <?php }?>
              <tr>
                    <td colspan="6">
                            <div class="switch_block_page ablack">
                            <?php include(APPPATH.'views/user/page.php') ?>
                            </div>
                    </td>
               </tr>
              </tbody>
            </table>
          </div>
    <?php if($full_page): ?>
</div>     
</div>
</div>
<?php include APPPATH.'views/common/footer.php'; ?>
<?php endif; ?>