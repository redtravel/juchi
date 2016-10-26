<div class="page cached no-toolbar">
    <!--navbar start-->
    <div class="navbar menu">
        <div class="navbar-inner">
            <div class="left"><a class="link icon-only back" href="#"> <i class="icon back"></i></a></div>
            <div class="center"><?php echo $title; ?></div>
        </div>
    </div>
    <!--navbar end-->

    <!--account start-->
    <?php if(isset($voucher_all) || isset($voucher_ok)) { ?> 
	    <div class="page-content article-bg2">
		   	<div class="edu-fot">
	            <div class="guanzhu-hu">
	            	<!--buttons-row start-->
	              	<div class="buttons-row">
	                   	<a href="#col_1" class="tab-link active button button-secondary">可用</a>
	                   	<a href="#col_2" class="tab-link button button-secondary">所有</a>
	              	</div>
	           		<!--buttons-row start-->	           		
	             	<div class="tabs" style="color:#fff;">
	                   	<div id="col_1" class="tab active list-block guanzhu-tab" >		    
                   		   	<div class="order-details-rr row no-gutter">
						        <div class="col-33 hu-shijian">券号</div>
						        <div class="col-15 hu-shijian">金额</div>
						       	<div class="col-50 hu-shijian account-shoury">最小单价</div>
						  	</div> 
	                        <ul class="account-liebiao" >
	                        <?php if(!empty($voucher_ok)){ foreach ($voucher_ok as $v_ok) { ?>                     
	                          	<li>
	                          	   	<div class="juli-plick row no-gutter">
	                               	   	<div class="col-33 account-time"><?php echo $v_ok->voucher_sn;?></div>
									   	<div class="col-15 account-time"><?php echo $v_ok->voucher_amount;?></div>
									   	<div class="col-50 account-shoury"><?php echo $v_ok->min_order;?></div>
								  	</div>
	                            </li>
	                        <?php } }else{ echo '<li><div class="juli-plick row no-gutter">您还没有可用的现金券!</div></li>';} ?>        
	                        </ul>
	                  	</div>
	                  	<div id="col_2" class="tab list-block guanzhu-tab">
	                       	<div class="order-details-rr row no-gutter">
							    <div class="col-33 hu-shijian">券号</div>
							    <div class="col-15 hu-shijian">金额</div>
							    <div class="col-50 hu-shijian account-shoury">使用记录</div>
							</div>
	                        <ul class="account-liebiao">  
	                        <?php if(!empty($voucher_all)){ foreach ($voucher_all as $v_all) { ?>                         
	                          	<li>
	                          		<div class="juli-plick row no-gutter">
		                               	<div class="col-33 account-time"><?php echo $v_all->voucher_sn;?></div>
										<div class="col-15 account-time"><?php echo $v_all->voucher_amount;?></div>
										<div class="col-50 account-shoury">
											<?php if($v_all->used_number == 1 && $v_all->repeat_number == 1){ ?>
												<a href="<?php if($v_all->genre_id == 1){ echo '/order/info/'.$v_all->order_id;}elseif($v_all->genre_id == 2){echo '/order/course_info/'.$v_all->order_id;}?>"><?php echo $v_all->order_sn;?></a> 
											<?php }elseif($v_all->repeat_number > 1){ ?>
												可用<?php echo $v_all->repeat_number;?>次,已用<?php echo $v_all->used_number;?>次
<?php }elseif($v_all->voucher_status == 0 && $v_all->used_number == 0 && $v_all->repeat_number == 1){ echo '未使用';} else {
    echo '不可用';
}?>
										</div>
									</div>
	                            </li>
	                         <?php } }else{ echo '<li><div class="juli-plick row no-gutter">您还没有现金券!</div></li>';} ?>            
	                        </ul>
	                 	</div>
	          		</div>
		  		</div>
		  	</div>
	   	</div>
   	<?php }else{ ?>
	    <div class="page-content public-bg no-top2">
	        <div class="page-content-inner no-top">
			 	<div class="content-block">
		            <div class="v-tab-acc">
			            <div class="order-details-rr">
					        <div class="hu-account-con clearfix">
						      	<div class="hu-shijian">事件</div>
						      	<div class="zhichu-yb">
						          	<span class="hu-shouru">收入</span>
						          	<span class="hu-zhichu">支出</span>
						      	</div>
						 	</div>
					 
							<ul class="account-liebiao">
							    <?php if(isset($integral)) { foreach ($integral as $ing) { ?>
								 <li class="row no-gutter">
									 <div class="col-25 account-time"><?php echo date('Y-m-d',strtotime($ing->create_date));?></div>
									 <div class="col-45 account-time "><?php echo $ing->change_desc;?></div>
									 <div class="col-15 account-shoury"><?php if($ing->pay_points >= 0) echo $ing->pay_points;?></div>
									 <div class="col-15" style="text-align:right;"><?php if($ing->pay_points < 0) echo $ing->pay_points;?></div>
								 </li>
							    <?php } } ?>
							    
							    <?php if(isset($balance)) { foreach ($balance as $bal) { ?>
								 <li class="row no-gutter">
									 <div class="col-25 account-time"><?php echo date('Y-m-d',strtotime($bal->create_date));?></div>
									 <div class="col-45 account-time"><?php echo $bal->change_desc;?></div>
									 <div class="col-15 account-shoury"><?php if($bal->user_money >= 0) echo $bal->user_money;?></div>
									 <div class="col-15" style="text-align:right;"><?php if($bal->user_money < 0) echo $bal->user_money;?></div>
								 </li>
							    <?php } } ?>		 
							</ul>
							
				    	</div>
					</div>
		        </div>
	        </div>   
	    </div>
	<?php } ?>
	<script>
					            $$("ul li:last-child").addClass('v-acc-noline')
					        </script>
    <!--account end-->
</div> 
