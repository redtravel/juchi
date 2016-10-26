<div class="view view-main" data-page="index">
    <div class="pages">
        <div data-page="index" class="page article-bg no-toolbar">
            <div class="yywtoolbar">
                <div class="yywtoolbar-inner row no-gutter">
                    <div class="col-100 v-acc-converted"><a class="link" href="/account/account_voucher/">积分兑换现金券</a></div>
                </div>
            </div>
            <!--navbar start-->
            <div class="navbar menu">
                <div class="navbar-inner">
                    <div class="left"><a class="link icon-only back" href="#"> <i class="icon back"></i></a></div>
                    <div class="center">账户管理</div>
                </div>
            </div>
            <!--navbar end-->

            <!--account start-->  
            <div class="page-content article-bg2 no-top2">
                <div class="page-content-inner no-top">
                <div class="content-block wrap">
                  <ul class="v_account">
                    <li>
                      <div class="account-ts">
                        <span class="account-jifen">我的积分</span>
                        <span class="account-jiage"><?php echo $user_info->pay_points;?></span>
                        <span class="account-ckxq"><a href="/account/account_content?type=integral">查看详情</a></span>
                      </div> 
                    </li>

                    <li>
                      <div class="account-ts">
                        <span class="account-jifen">我的现金券</span>
                        <span class="account-jiage"><?php echo $voucher_num;?> <span class="account-jiage2">张可用</span></span>
                        <span class="account-ckxq"><a href="/account/account_content?type=voucher">查看详情</a></span>
                      </div>
                    </li>

                    <li>
                      <div class="account-ts">
                        <span class="account-jifen">我的账户余额</span>
                        <span class="account-jiage">￥<?php echo $user_info->user_money;?></span>
                        <span class="account-ckxq"><a href="/account/account_content?type=balance">查看详情</a></span>
                      </div>
                    </li>
                  </ul>
		  </div>
                </div> 
            </div>
            <!--account end-->
        </div>
    </div>
</div>