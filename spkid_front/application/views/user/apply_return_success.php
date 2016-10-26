<?php
/**
 * 自助退货提交返回页
 */
?>
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
<div class="goodstab" style="overflow:visible;float:left;margin-bottom:0">
    <div class="goodsReturnTop">
        <span class="f14b">自助退货流程：</span>
        <div class="goodsReturnPro" style="margin-top:15px;"></div>
    </div>
</div>
<div class="goodsReturnComp">
    恭喜你，您提交的退货申请成功，之后可以到<a href="/user/apply_return_list">自助退货</a>中查询处理状态
    <p style="color:red;"><?=$msg?></p>
</div>
</div>
</div>
