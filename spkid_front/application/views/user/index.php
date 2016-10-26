<?php include APPPATH . 'views/common/user_header.php'?>               
                <div class="personal-center-right">
                    <h1 class="order-details-bt">个人中心</h1>
                    <div class="my-center clearfix">
                        <div class="my-touxiang fl-left">
                            <img src="<?php echo static_style_url('mobile/touxiang/'.$advar.'?v=version')?>">
                            <div class="fl-right center-mingchen"><span><?php echo $user_name;?></span><a href="/user/profile.html">修改头像></a></div>
                        </div>
                        <div class="center-lsit fl-left">
                            <ul class="my-persona-lb">
                               	<li class="clearfix">
                                	<label>账户安全：</label>
                                	<div class="progress clearfix">
                                    	<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: <?php if($security == 0 ) {echo '50%';}elseif($security == 1 ){echo '100%';}?>">
                                   	 		<!-- <span class="sr-only">100% Complete (success)</span> -->
                                   	 	</div>
                                	</div>
                               	</li>
                               	<li class="clearfix"><label>绑定手机：</label><span><?php echo $mobile;?></span></li>
                               	<!-- <li class="clearfix"><label>绑定邮箱：</label><span><a href="#">绑定></a></span></li> -->                            
                            </ul>
                    	</div>
               		</div> 
	               	<ul class="personal-center-ico clearfix">
	                    <li>
	                      	<span class="count-icon count-icon1"></span>
	                      	<p class="rr-center">
	                        	<a class="rr-center-dd">待支付的订单：<em><?php echo $wait_pay;?></em></a>
	                        	<a href="/user/order_list" class="rr-center-see">查看待支付的订单 ></a>
	                      	</p>
	                    </li>	                    
	                    <li>
	                     	<span class="count-icon count-icon2"></span>
	                      	<p class="rr-center">
	                        	<a class="rr-center-dd">待发货的订单：<em><?php echo $await_goods;?></em></a>
	                        	<a href="/user/order_list" class="rr-center-see">查看待发货的订单 ></a>
	                      	</p>
	                    </li>	                    
	                    <li>
	                      	<span class="count-icon count-icon3"></span>
	                      	<p class="rr-center">
	                        	<a class="rr-center-dd">待评价的商品：<em><?php echo $evaluate_product;?></em></a>
	                        	<a href="/user/my_liuyan" class="rr-center-see">查看待评价的商品 ></a>
	                      	</p>
	                    </li>	                    
	                    <li>
	                      	<span class="count-icon count-icon4"></span>
	                      	<p class="rr-center">
	                        	<a class="rr-center-dd">喜欢的商品：<em><?php echo $like_product;?></em></a>
	                        	<a href="/collect/index.html" class="rr-center-see">查看喜欢的商品 ></a>
	                      	</p>
	                    </li>	                    	                    
	              	</ul>                    
            	</div>
	        </div>   
	    </div>
	</div>
<?php include APPPATH . 'views/common/footer.php'?>
