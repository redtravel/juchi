
 <div class="uleft_c">
   <dl class="ucblock">
	 <dt>我的订单</dt>
	 <?php if(isset($left_sel) && $left_sel == 11): ?><dd class="sel">订单管理</dd><?php else: ?><dd><a href="/user/order">订单管理</a></dd><?php endif; ?>
     <?php if(isset($left_sel) && $left_sel == 16): ?><dd class="sel">自助退货</dd><?php else: ?><dd><a href="/user/apply_return_list">自助退货</a></dd><?php endif; ?>
	 <?php if(isset($left_sel) && $left_sel == 21): ?><dd class="sel">我的收藏</dd><?php else: ?><dd><a href="/user/collection">我的收藏</a></dd><?php endif; ?>
	 <?php if(isset($left_sel) && ($left_sel == 31 || $left_sel == 32)): ?><dd class="sel">咨询与点评</dd><?php else: ?><dd><a href="/user/liuyan">点评与咨询</a></dd><?php endif; ?>
   </dl>
   <dl class="ucblock">
	 <dt>我的账户</dt>
	 <?php if(isset($left_sel) && $left_sel == 13): ?><dd class="sel">账户查询</dd><?php else: ?><dd><a href="/user/account">账户查询</a></dd><?php endif; ?>
	 <?php if(isset($left_sel) && $left_sel == 42): ?><dd class="sel">个人资料</dd><?php else: ?><dd><a href="/user/profile">基本资料</a></dd><?php endif; ?>
	 <?php if(empty($user->union_sina) && empty($user->union_qq) && empty($user->union_zhifubao)): ?><?php if(isset($left_sel) && $left_sel == 43): ?><dd class="sel">修改密码</dd><?php else: ?><dd><a href="/user/password">修改密码</a></dd><?php endif; ?><?php endif; ?>
	 <?php if(isset($left_sel) && $left_sel == 12): ?><dd class="sel">地址管理</dd><?php else: ?><dd><a href="/user/address">地址管理</a></dd><?php endif; ?>


	 </dl>
   <dl class="ucblock">
	 <dt>积分及礼券</dt>
	 <?php if(isset($left_sel) && $left_sel == 14): ?><dd class="sel">我的现金劵</dd><?php else: ?><dd><a href="/user/token">我的现金劵</a></dd><?php endif; ?>
	 <?php if(isset($left_sel) && $left_sel == 41): ?><dd class="sel">积分查询</dd><?php else: ?><dd><a href="/user/points">积分查询</a></dd><?php endif; ?>
     <?php if(isset($left_sel) && $left_sel == 15): ?><dd class="sel">积分兑换</dd><?php else: ?><dd><a href="/user/exchange_voucher">积分兑换</a></dd><?php endif; ?>	 	 
   </dl>
   <dl class="ucblock">
	 <dt>会员服务</dt>
	 <dd><a href="/help-2.html">常见问题</a></dd>
	 <dd><a href="/help-47.html">帮助中心</a></dd>
   <br/>
   </dl>
 </div>
 <script type="text/javascript">
    $(function(){
        if($('dd').hasClass('sel')){
            $('dd[class=sel]')[0].parentNode.children[0].style.background='#faa';
        }
    });
 </script>