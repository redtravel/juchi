<?php include APPPATH."views/mobile/header.php"; ?>
<div class="view view-main" data-page="index">
    <div class="pages">
        <div data-page="index" class="page">
            <div class="navbar">
                <div class="navbar-inner">
                    <div class="center">个人中心</div>
                </div>
            </div>
<div class="toolbar tabbar-labels tabbar">
      <div class="toolbar-inner">
        <a href="#index" class="link"> <i class="icon tabbar-demo-icon-1"></i><span class="tabbar-label">商品展销</span></a>
        <a href="#course" class="link"><i class="icon tabbar-demo-icon-2"></i><span class="tabbar-label">教育培训</span></a>
        <a href="#article" class="link"><i class="icon tabbar-demo-icon-3"></i><span class="tabbar-label">文章视频</span></a>
        <a href="#" class="link login active"><i class="icon tabbar-demo-icon-4"></i><span class="tabbar-label">个人中心</span></a>        
      </div>
</div>
                <div class="page-content">
                <div class="content-block">
<h3><?php echo $user->user_name?></h3>
</div>
                    <div class="list-block">
                    <ul>
                        <li>
                            <a href="#" class="item-link item-content">
								<div class="item-inner">我的关注</div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item-link item-content">
								<div class="item-inner">购物车</div>
                            </a>
                        </li><li>
                            <a href="/user/order" class="item-link item-content">
								<div class="item-inner">我的订单</div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item-link item-content">
								<div class="item-inner">收货地址</div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item-link item-content">
								<div class="item-inner">我的课程</div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item-link item-content">
								<div class="item-inner">账户管理</div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item-link item-content">
								<div class="item-inner">设置</div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="item-link item-content">
								<div class="item-inner">礼品中心</div>
                            </a>
                        </li>
                      </ul>
                    </div>
                </div>
        </div>
    </div>
</div>
</div>

<script type="text/javascript" src="<?php echo static_style_url('mobile/js/framework7.min.js')?>"></script>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/yyw-app.js')?>"></script>
</body>
</html>
