<?php
include_once (APPPATH . "views/common/header.php");
?>
<link rel="stylesheet" type="text/css" href="<?php echo static_style_url('pc/css/pdetail.css?v=version')?>">
<link rel="stylesheet" type="text/css" href="<?php echo static_style_url('pc/css/tank.css?v=version')?>">
<link rel="stylesheet" type="text/css" href="<?php echo static_style_url('pc/css/signin.css?v=version')?>">
<script src="<?php echo static_style_url('pc/js/comm_tool.js?v=version')?>"></script>
<link href="<?php echo static_style_url('pc/css/bootstrap.css?v=version')?>" rel="stylesheet" type="text/css" media="all">
<script src="<?php echo static_style_url('pc/js/bootstrap.min.js?v=version')?>" type="text/javascript"></script>

<style>

.product-part {
	min-height:1024px;
}
	
.product-primary-image {
	vertical-align: top;
}

.product-title {
	font-size: 20px;
}

.product-info .product-price {
	color: black;
}

.product-btn-baoming span {
	color: white; font-family:Verdana; font-size:18px;
}

.product-btn-baoming span.course_baoming {
	border-left: 1px solid white;
	padding-left: 8px;
}

.product-info .product-btn {
	background: #f75555;
}

.product-info .product-btn.outofdate_course {
	background:gray;	
}

.product-info .product-group {
	font-size:1em;
	margin-left:0px;
}

.product-icon-bar .product-icon {
	float:none;
}
</style>

		<script>window.hostId = '3179';</script>
		<svg xmlns="http://www.w3.org/2000/svg" style="width:0;height:0;visibility:hidden;">   
		<symbol viewBox="0 0 1024 1024" id="icon-qq"><title>icon-qq</title> <path class="svgpath" data-index="path_0" d="M836.8 608 833.6 596.8 832 590.4 830.4 584 828.8 577.6 825.6 571.2 824 564.8 820.8 558.4 816 547.2 811.2 537.6 806.4 529.6 801.6 521.6 798.4 515.2 795.2 508.8 787.2 497.6 779.2 484.8 776 481.6 776 480 776 480 777.6 475.2 779.2 470.4 780.8 464 782.4 459.2 782.4 454.4 782.4 451.2 782.4 448 782.4 444.8 782.4 441.6 780.8 435.2 779.2 428.8 777.6 422.4 774.4 417.6 771.2 412.8 769.6 408 768 404.8 763.2 398.4 761.6 396.8 761.6 393.6 760 382.4 758.4 374.4 758.4 364.8 755.2 355.2 752 344 750.4 337.6 748.8 331.2 747.2 324.8 745.6 318.4 744 312 740.8 305.6 734.4 292.8 731.2 286.4 728 280 723.2 273.6 720 267.2 713.6 260.8 710.4 254.4 704 248 699.2 243.2 692.8 236.8 686.4 232 683.2 228.8 680 225.6 672 220.8 664 216 654.4 211.2 646.4 206.4 636.8 203.2 627.2 198.4 617.6 195.2 608 192 598.4 190.4 588.8 187.2 579.2 185.6 568 184 558.4 184 548.8 182.4 537.6 182.4 528 182.4 518.4 182.4 508.8 182.4 499.2 184 489.6 185.6 480 187.2 470.4 188.8 460.8 190.4 451.2 193.6 443.2 196.8 435.2 200 425.6 203.2 417.6 206.4 411.2 211.2 403.2 214.4 396.8 219.2 390.4 224 384 230.4 376 236.8 369.6 244.8 361.6 252.8 355.2 259.2 350.4 267.2 344 273.6 339.2 281.6 337.6 284.8 334.4 288 329.6 296 326.4 302.4 323.2 308.8 320 315.2 316.8 321.6 315.2 328 313.6 334.4 310.4 340.8 307.2 350.4 305.6 361.6 304 369.6 304 376 304 382.4 304 387.2 304 390.4 300.8 393.6 299.2 396.8 297.6 398.4 296 401.6 294.4 408 292.8 412.8 292.8 419.2 292.8 422.4 292.8 427.2 288 432 284.8 438.4 283.2 443.2 281.6 448 280 452.8 278.4 457.6 278.4 460.8 278.4 465.6 278.4 468.8 280 472 280 476.8 281.6 480 280 481.6 273.6 486.4 265.6 494.4 259.2 499.2 254.4 502.4 251.2 507.2 248 508.8 243.2 515.2 236.8 521.6 232 528 227.2 536 220.8 542.4 216 550.4 212.8 558.4 208 566.4 204.8 574.4 201.6 580.8 198.4 588.8 196.8 595.2 195.2 603.2 192 609.6 192 616 190.4 622.4 188.8 628.8 188.8 641.6 188.8 652.8 188.8 657.6 188.8 664 190.4 672 192 675.2 192 680 193.6 683.2 195.2 686.4 196.8 691.2 198.4 691.2 200 692.8 200 694.4 201.6 694.4 201.6 694.4 206.4 692.8 212.8 692.8 216 691.2 220.8 688 225.6 684.8 228.8 681.6 233.6 678.4 236.8 673.6 240 670.4 243.2 665.6 244.8 662.4 246.4 659.2 249.6 652.8 251.2 651.2 252.8 649.6 252.8 648 252.8 646.4 254.4 646.4 254.4 644.8 256 644.8 256 646.4 256 646.4 257.6 646.4 259.2 654.4 260.8 660.8 264 667.2 267.2 673.6 268.8 680 272 686.4 275.2 691.2 278.4 696 281.6 700.8 284.8 705.6 291.2 713.6 297.6 721.6 302.4 726.4 308.8 731.2 313.6 736 321.6 742.4 323.2 742.4 324.8 744 324.8 745.6 324.8 745.6 323.2 745.6 321.6 745.6 316.8 747.2 312 747.2 307.2 748.8 302.4 748.8 299.2 750.4 294.4 752 291.2 753.6 288 755.2 284.8 755.2 283.2 758.4 280 758.4 278.4 761.6 275.2 764.8 273.6 766.4 272 768 270.4 772.8 268.8 776 268.8 779.2 267.2 782.4 267.2 785.6 267.2 787.2 267.2 790.4 267.2 793.6 267.2 795.2 267.2 798.4 267.2 801.6 267.2 803.2 268.8 808 270.4 809.6 272 812.8 273.6 814.4 275.2 816 276.8 817.6 278.4 820.8 284.8 824 289.6 827.2 294.4 830.4 300.8 832 307.2 835.2 315.2 836.8 321.6 838.4 329.6 840 337.6 841.6 345.6 843.2 355.2 843.2 371.2 846.4 388.8 846.4 403.2 846.4 411.2 846.4 417.6 846.4 425.6 846.4 432 844.8 438.4 844.8 443.2 843.2 452.8 841.6 459.2 840 465.6 838.4 476.8 833.6 483.2 832 488 830.4 496 825.6 504 820.8 508.8 817.6 513.6 814.4 520 814.4 524.8 816 534.4 816 539.2 816 542.4 816 545.6 817.6 548.8 817.6 553.6 819.2 558.4 820.8 568 824 579.2 828.8 588.8 830.4 598.4 833.6 609.6 835.2 619.2 836.8 630.4 838.4 640 840 649.6 840 659.2 841.6 668.8 841.6 678.4 841.6 688 841.6 697.6 840 705.6 840 713.6 838.4 721.6 836.8 729.6 835.2 737.6 833.6 740.8 832 744 830.4 750.4 828.8 756.8 825.6 761.6 822.4 768 820.8 771.2 817.6 774.4 814.4 776 812.8 779.2 809.6 782.4 804.8 782.4 803.2 784 801.6 784 800 785.6 796.8 785.6 795.2 785.6 792 785.6 788.8 784 785.6 784 782.4 782.4 780.8 780.8 777.6 777.6 774.4 776 771.2 772.8 769.6 766.4 763.2 760 758.4 753.6 755.2 745.6 750.4 729.6 742.4 726.4 740.8 724.8 739.2 723.2 739.2 731.2 731.2 736 726.4 737.6 723.2 744 715.2 748.8 707.2 753.6 700.8 756.8 694.4 763.2 680 768 668.8 769.6 660.8 771.2 657.6 772.8 656 774.4 654.4 776 654.4 776 654.4 780.8 664 784 672 787.2 675.2 788.8 680 792 684.8 795.2 689.6 798.4 692.8 800 694.4 801.6 696 803.2 696 804.8 697.6 808 699.2 809.6 699.2 811.2 699.2 812.8 699.2 814.4 699.2 817.6 697.6 819.2 697.6 820.8 696 824 692.8 825.6 691.2 827.2 689.6 828.8 686.4 830.4 683.2 832 680 833.6 676.8 835.2 668.8 836.8 660.8 838.4 651.2 838.4 646.4 838.4 641.6 838.4 636.8 838.4 630.4 838.4 620.8Z"/> <path class="svgpath" data-index="path_1" d="M513.6 20.8c-272 0-494.4 222.4-494.4 494.4S241.6 1008 513.6 1008 1008 785.6 1008 513.6 785.6 20.8 513.6 20.8zM513.6 976C259.2 976 52.8 768 52.8 513.6s208-462.4 462.4-462.4S976 259.2 976 513.6 768 976 513.6 976z"/> </symbol><symbol viewBox="0 0 1024 1024" id="icon-sina"><title>icon-sina</title> <path class="svgpath" data-index="path_0" d="M692.8 505.6c-28.8-4.8-14.4-20.8-14.4-20.8s28.8-46.4-4.8-80c-41.6-41.6-144 4.8-144 4.8-38.4 12.8-28.8-4.8-22.4-35.2 0-35.2-12.8-94.4-115.2-59.2-104 35.2-192 160-192 160C136 556.8 144 620.8 144 620.8c16 140.8 164.8 179.2 281.6 188.8 121.6 9.6 286.4-41.6 337.6-148.8C811.2 553.6 721.6 512 692.8 505.6zM433.6 769.6c-121.6 6.4-219.2-54.4-219.2-136 0-81.6 97.6-145.6 219.2-152 121.6-4.8 219.2 44.8 219.2 124.8C652.8 688 555.2 763.2 433.6 769.6z"/> <path class="svgpath" data-index="path_1" d="M409.6 534.4c-121.6 14.4-107.2 128-107.2 128s-1.6 36.8 32 54.4c72 38.4 145.6 16 182.4-32C553.6 636.8 531.2 520 409.6 534.4zM379.2 694.4c-22.4 3.2-41.6-11.2-41.6-28.8 0-19.2 16-38.4 38.4-41.6 25.6-3.2 43.2 12.8 43.2 32C420.8 675.2 401.6 692.8 379.2 694.4zM451.2 633.6c-8 6.4-17.6 4.8-20.8-1.6-4.8-6.4-3.2-17.6 4.8-24 9.6-6.4 19.2-4.8 22.4 1.6C460.8 617.6 459.2 627.2 451.2 633.6z"/> <path class="svgpath" data-index="path_2" d="M752 456c9.6 0 17.6-8 19.2-16 0 0 0-1.6 0-1.6 14.4-134.4-110.4-110.4-110.4-110.4-11.2 0-19.2 9.6-19.2 20.8 0 11.2 9.6 19.2 19.2 19.2 89.6-19.2 70.4 70.4 70.4 70.4C731.2 448 740.8 456 752 456z"/> <path class="svgpath" data-index="path_3" d="M737.6 222.4c-43.2-9.6-88-1.6-99.2 1.6-1.6 0-1.6 1.6-3.2 1.6 0 0 0 0 0 0-12.8 3.2-20.8 14.4-20.8 28.8 0 16 12.8 28.8 28.8 28.8 0 0 16-1.6 25.6-6.4 11.2-4.8 99.2-3.2 144 72 24 54.4 11.2 91.2 9.6 96 0 0-6.4 14.4-6.4 28.8 0 16 12.8 25.6 28.8 25.6 12.8 0 24-1.6 27.2-24l0 0C920 315.2 814.4 241.6 737.6 222.4z"/> <path class="svgpath" data-index="path_4" d="M513.6 20.8c-272 0-494.4 222.4-494.4 494.4S241.6 1008 513.6 1008 1008 785.6 1008 513.6 785.6 20.8 513.6 20.8zM513.6 976C259.2 976 52.8 768 52.8 513.6s208-462.4 462.4-462.4S976 259.2 976 513.6 768 976 513.6 976z"/> </symbol>
		</svg>

		<div class="product-wrapper">
			<div class="product-overview clearfix">
				<div class="product-image fl-left">
					<div class="product-primary-image">
						<img src="<?php echo img_url(current($g_list)['default']->img_url)?>">
					</div>
				</div>
				<div class="product-info fl-left">
					<div class="product-title product-row"><?php echo isset($ititle) ? $ititle : ''?></div>

					

					<div class="product-price-box product-row clearfix">
						时间：
						<span style="border:1px solid rgb(23, 161, 229);padding: 2px 30px 6px 3px;">
						<img src="<?php echo static_style_url('pc/images/course_date.png?v=version')?>" alt="">
						<?php echo date("Y.m.d", strtotime($p -> package_name)); ?>
									<?php if (isset($product_desc_additional['desc_waterproof'])) echo '-' . date("Y.m.d", strtotime($product_desc_additional['desc_waterproof']))?></span>						
						
					</div>

					<div class="product-price-box product-row clearfix">
						<span>地点：<?if (isset($product_desc_additional['desc_crowd'])) echo $product_desc_additional['desc_crowd']?></span>
						
					</div>

					<div class="product-price-box product-row clearfix">
						<span>讲师：<?=$p -> subhead ?></span>						
					</div>
					
					<div class="product-price-box product-row clearfix">
						<span>报名：<?=$p -> ps_num ?>人
							<?php if($sub_list[$color_id]['sub_list'][0]->consign_num == -2):?>
								/人数不限制							
							<?php else:?>
								<?php echo '/' . $sub_list[$color_id]['sub_list'][0]->consign_num . '人'?>
							<?php endif; ?>

						</span>
						<span style="margin-left:5em">关注：<?php echo get_page_view('course', $p -> product_id); ?>人
							
						</span>
						
					</div>
					
					<div class="product-btn-box product-row">
						<div class="product-group">
							<div style="float:left;width:50%">
								<?php if(!$is_outofdate && !$is_exceed_num):?>							
									<button class="product-btn product-btn-baoming" type="button" style="width:200px">
										<span class="course_price">&#65509;<?php echo $p->product_price?></span>
										<span class="course_baoming">我要报名</span>
									</button>
								<?php else:?>
									<span >
										<p style="font-weight:bold">该课程已结束</p>
										<p style="font-size:0.8em;color:gray">您可以收藏该课程，等待老师新的开课通知</p>
									</span>
								<?php endif;?>							
							</div>
								
							<span class="product-icon-bar" style="position:relative; top:10px;">
								<span class="product-icon-box clearfix">
									<span class="product-icon like-icon" id="add_to_collect"></span>
								</span>
								<span style="position: relative;top: -12px;left:-13px; color:#909090;">关注课程</span>
							</span>
							<br style="clear:both">
						</div>
					</div>
				</div>
			</div>
			<div class="navbar-placeholder"></div>
			<div class="navbar-wrapper">
				<ul class="navbar clearfix">
					<li><a href="javascript:void(0);" data-tag="detail" class="active">课程简介</a></li>
					<li><a href="javascript:void(0);" data-tag="evaluation" class="">讲师介绍</a></li>
					<li><a href="javascript:void(0);" data-tag="guide" class="">课程评价</a></li>
					<li><a href="javascript:void(0);" id="keqianwenda" data-tag="comment" class="">课前问答</a></li>
				</ul>
			</div>
			<div class="product-content clearfix">
			<div class="product-part product-detail" style="display: block;">
				<div class="product-part-box">
					<?php echo $p -> product_desc; ?>
					<?php echo $p -> detail1; ?>
				</div>
			</div>
			
			<div class="product-part product-evaluation" style="display: none;">
				<div class="product-part-box">
					<?php echo $p -> detail2; ?>
				</div>
			</div>
			<div class="product-part product-guide" style="display: none;">
				<div class="product-part-box">
					<?php echo $p->detail4?>
				</div>
			</div>
			
			<div class="product-part product-comment grey-bg" style="display: none;">
				<div class="product-part-box">
					<div class="product-comment-selector">
						<a class="area_selector  discus_area active" href="javascript:void(0);" data-type="comment">讨论区 (<i class="comment-count">0</i>)</a>/<a class="area_selector pingjia_area" href="javascript:void(0);" data-type="goodscomment">购买评价 (<i class="goodscomment-count">0</i>)</a>
					</div>

					<div class="product-line"></div>
					<div class="product-comment grey-bg">
					<div class="area-1">
						<form class="ct-form" name="ct-form" data-hostid="3179" data-committype="" data-hosttype="2">
							<div class="clearfix liuyan-content">
								<textarea name="liuyan" aria-required="true" placeholder="同学，你怎么看?"></textarea>
							</div>
							<div class="ct-submit">
								<a type="" class="btn btn-primary btn-liuyan">提交</a>
							</div>
						</form>
						<ul class="ct-list" id="ct-list-full">

						</ul>
					</div>
					<div style="display:none" class="area-2">
						<ul class="ct-list" id="ct-list-full-comment">
						</ul>	
					</div>

					</div>
					
				</div>
			</div>
		</div>
		</div>

		<div class="backtop" style="display: block;"></div>

<!-- 留言开始 -->
<div id="give-message" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog give-message">
        <div class="modal-content">
            <div class="modal-header give-message-tit">
                <span>给我们留言</span>
                <button type="button" class="close give-message" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true"><img src="images/close.png"></span></button>
            </div>
          <div class="modal-body">
               <div class="give-contact">
                    <p>小悦悦要忙疯啦，有问题先留言，我们会尽快联系您哈！<span>客服电话：400-9905-920</span></p>
                    <div class="give-input">
                        <form action="/api/comment/add" method="POST " class="ct-form" name="ct-form" data-hostid="3179" data-committype="" data-hosttype="2">
                            <div class="clearfix"><textarea style="height: 90px;" name="comment" aria-required="true" placeholder="留言不能少于10个字"></textarea></div>
                            <span class="err_tip err_tips" id="address_err">不能为空</span>
                            <div class="clearfix" style="margin-top:10px;"><input name="mobile_num" type="text" placeholder="联系电话"></div>
                            <span class="err_tip err_tips" id="mobile_num_err">不能为空</span>
                       </form>
                    </div>
               </div>
          </div>
          
          <div class="modal-body v-button give-message-but">
              <a class="btn btn-lg btn-blue btn-give-message">留言</a>
              
          </div>
          
        </div>
      </div>
</div>
<!-- 留言结束 -->

<!--相关课程及分享开始-->
<div class="related_courses fixed" style="border-left:1px solid rgb(240, 240, 240);display:none">
	<div class="course_share" style=" background-color:#f6f6f6; height:100px;">
		<div style="border-bottom:solid 1px #f1f1f1; padding:7px 0 10px 0;">
			<div class="clearfix">
				<span style="float: left; padding-top: 1em; padding-left:10px; font-size:14px;">分享课程到：</span>
				<span style="float: right;">
					<div class="bdsharebuttonbox bdshare-button-style0-32" data-tag="share_1" data-bd-bind="1452912860952">

						<a class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>
						<a class="bds_sqq" data-cmd="sqq" title="分享到QQ好友"></a>
						<a class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
						<a class="bds_qzone" data-cmd="qzone" href="#" title="分享到QQ空间"></a>
					</div>		
				</span>

			</div>
		</div>
		
		<div style=" color:#909090; font-size:12px;  padding:10px 0 0 20px;">课程联系人联系方式：待定</div>
	</div>
	
	<div style="padding-left:20px;">
		<p style="margin-top:28px; font-size:14px; color:#333;">演示站课代表</p>
		<p style="color:#909090; font-size:12px;">联系方式只对报名学生可见</p>
		<p style="color:#909090; font-size:12px;">报名前对课程有疑问请点击
			<a href="javascript:void(0)" onclick="$('#keqianwenda').click();">课前问答</a>
		</p>
	</div>
	<style>
		.courses_rec ul {
			position:relative;
			width:250px;
		}

		.courses_rec ul li {
			height:8em;
			width:250px;
			margin-top:0.8em;
			margin-left:3px;
		}
		.course_info_block div {
			margin-top:4px;
		}
		
	</style>
	<div class="courses_rec">
		<p style=" margin-top:2em; font-size:14px; color:#333; padding-left:20px;">相关课程推荐</p>
		<ul>
			<?php foreach ($related_courses as $key => $value):?>
				
				<li>
					<a href="/product-<?php echo $value->product_id?>" style="text-decoration:none">
						<img style="float:left;" src="<?php echo img_url($value->img_url)?>" alt="<?=$value->product_name?>" width="100" height="100">
						<span class="course_info_block" style="float:right;margin-right:1em;width:50%">
							<div style="color:black;font-size:0.8em"><?php echo $value->product_name?></div>
							<div style="font-size:0.8em;color:#909090">开课：<?php echo $value->package_name?></div>
							<div style="font-size:0.8em;color:#909090">讲师：<?php echo $value->subhead?></div>
							<div style="font-size:0.8em;color:#909090">报名：<?php echo $value-> ps_num ?>人</div>
							<div style="font-size:0.8em; color:#f75555; font-family:Verdana;">&#65509;963</div>
						</span>
					</a>
				</li>

			<?php endforeach;?>
			
		</ul>
	</div>
</div>

<!--相关课程及分享结束-->

<!-- 登陆弹层开始 -->
	<div id="login-box" class="modal fade pop-box in" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
	                <h4 class="modal-title">登录</h4>
	            </div>
	            <div class="modal-body">
	            <div class="form-container">

	      <form name="loginForm" class="form-signin">
	        <p class="error"></p>
	        <input type="tel" name="username" class="form-control" placeholder="请输入手机号码">
	        <p class="error"></p>
	        <input type="password" id="password" name="password" class="form-control" placeholder="请输入密码" required="">
	        <div class="checkbox">
	          <label for="auto_login" class="grey-text"><input class="input-checkbox" type="checkbox" value="1" name="checkout" id="auto_login">下次自动登录</label> <a class="pull-right" href="/user/forgot">忘记密码</a>
	        </div>
	        <div class="btn-block clearfix">
	        <a class="btn btn-lg btn-blue btn-block" id="login_action" type="submit"><i class="fa fa-lock left"></i>登录</a>
	        <p class="pull-right grey-text">还没账号?<a href="/user/register">注册</a></p>
	        </div>


	        <div class="horizontal"><span>可以使用以下方式登录</span></div>
	        <div class="other">
	          <a href="/user/qq_login" class="qq"></a>                  
	          <a href="/user/weixin_login" class="weixin"></a>		  
	          <a href="/user/alipay_login" class="alipay"></a>                 
	          <a href="/user/xinlang_login" class="sina"></a>
	        </div>
	      </form>

	    </div>
	            </div>

	        </div>
	    </div>
	</div>  
<!-- 登陆弹层结束 -->

<script>
	$(function(){		
		$(".navbar li:first a").click();

		$('#add_to_collect').click(function(){
			var is_log_in = parseInt(user_id);
			if (!is_log_in) {
				$("#login-box").modal('show');
			} else {
				add_to_collect(tag_id, 3, $(this));	
			}				
		});

		$('#password').on('input propertychange', function(){
		    var username = $('input[name="username"]'), psw = $('#password');
		    if (0 < username.length && 0 < psw.length){
		        $('button.disabled').removeClass('disabled').removeAttr('disabled');        
		    }
		})
		var username = $('input[name="username"]');
		username.blur(function(){
		    if ('' == username.val()){
		        username.prev().text('请输入账号');
		        $('button.disabled').attr('disabled', 'disabled');
		    } else {
		        username.prev().text('');
		    }
		})
		$('#login_action').click(function(e){
		    e.preventDefault();
		    var psw = $('#password');    
		        
		    if ('' == psw.val()){
		        psw.prev().text('请输入密码');
		        $('button.disabled').attr('disabled', 'disabled');
		    } else if ('' != $('input[name="username"]').val()) {
		        $('button.disabled').removeClass('disabled').removeAttr('disabled');
		        //alert($(this).serialize());
		        $.ajax({url:'/user/proc_login', data:$(this).parents('form').serialize(), method:'POST', dataType:'json', success:function(data){
		            if (1 == data.error){
		                //alert(data.name);
		                $('input[name='+data.name+']').prev().text(data.message);
		            } else {
		                location.reload();
		            }
		        }
		        })
		    }
		    //alert(username.val()+' '+psw.val());
		    return false;
		})
	});
</script>


<script>window._bd_share_config = {
	"common": {
		"bdSnsKey": {},
		"bdText": "",
		"bdMini": "1",
		"bdMiniList": ["weixin", "sqq", "tsina", "qzone"],
		"bdPic": "<?php echo img_url(current($g_list)['default']->img_url)?>",
		"bdStyle": "0",
		"bdSize": "32",
	},
	"share": {}
};
with(document) 0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = 'http://bdimg.share.baidu.com/static/api/js/share.js?v=86835285.js?cdnversion=' + ~(-new Date() / 36e5)];</script>

		<script type="text/javascript" src="<?php echo static_style_url('pc/js/pdetail.js?v=version')?>"></script>
		
		
		<script type="text/javascript">
		function relayout() {
			var left = $('#keqianwenda').offset().left + 300;
			var top = $('.navbar-wrapper').offset().top;
			$('.related_courses').css({
				position:'absolute',
				left: left + 'px',
				top:top + 'px',
				zIndex:999,
				width:250 + 'px'
			}).show();
		}
		
		$(window).load(function(){
			relayout();
		});
		

		$(window).resize(function(){
			console.log('window resize');
			relayout();
		});

		var tag_id = product_id = '<?php echo $p -> product_id ? $p -> product_id : 0; ?>';
		var tag_type = 3; //代表课程
		var comment_type = 1; //代表咨询
		var tag_id = '<?php echo $p -> product_id; ?>';
		var user_id = '<?php echo $user_id; ?>';		

$(function() {
	setTimeout(function(){
		get_liuyan(3, tag_id, 1, $('#ct-list-full'));	
	}, 1000);	

	setTimeout(function(){
		get_liuyan(3, tag_id, 2, $('#ct-list-full'));		
	}, 1000);


	gototop = function() {
        var t = $(".navbar-placeholder"), a = $(".navbar-wrapper"), e = t.offset(), b = $(".related_courses");
        function fix_related_courses() {
        	$(".related_courses").css({
        		position:'fixed',
        		top:0,
        	})
        }

        function unfix_related_courses() {
        	$(".navbar-wrapper").removeClass("fixed")
        	relayout();
        }

        $(window).scroll(function() {
            var t = $(document).scrollTop();
            e.top > t && a.hasClass("fixed") ? unfix_related_courses() : e.top < t && !a.hasClass("fixed") && a.addClass("fixed") && fix_related_courses()
        }), $(".navbar li a").click(function() {
            var t = $(this), a = t.data("tag");
            return t.hasClass("disabled") ? !1 : ($(".navbar li a").removeClass("active"), t.addClass("active")
            )
        })
    }

//tab 注册
$('div.product-wrapper > div.navbar-wrapper > ul > li').click(function() {
	gototop();
	var index = $(this).index();
	$(this).siblings().find('a').removeClass('active');
	$(this).find('a').addClass('active');
	$('.product-part').hide();
	$('.product-part').eq(index).show();

});

var cur_sub_id = '<?=$sub_list[$color_id]['sub_list'][0]->sub_id?>';

$('.product-btn-baoming').click(function() {
	if (!checkLogin(false)) {
		$("#login-box").modal('show');
        return;            				
	};
	var sub_id = cur_sub_id;
	window.location.href = '/cart/checkout_course/' + sub_id;
});



$('.ct-form').find("textarea").focus(function() {
	"" === $(this).val() && ($(this).stop().animate({
		height: "90px"
	}), $(this).parent().next().show())
});
$('.ct-form').find("textarea").blur(function() {
	var t = $(this);
	setTimeout(function() {
		"" === t.val() && (t.stop().animate({
			height: "42px"
		}), t.parent().next().hide())
	}, 300)
});



$(".ct-list").delegate(".tb a", "click", function(t) {
	t.preventDefault(), t.stopPropagation();
	var i = $(this),
		n = i.closest("li");
	if (n.siblings().find(".ct-form textarea").val(""), n.siblings().find(".ct-form").slideUp("fast"), i.data("form"))
		i.data("form").slideToggle("fast");
	else {
		var a = $(".ct-form").first().clone(true).hide();
		a.find("textarea").css({
			height: "42px"
		}), a.find(".ct-submit").hide(), a.find("textarea").val(""), i.data("form", a), a.appendTo(n).slideDown("fast");
	}
})

$('.btn-liuyan').click(function(e) {
	//check user whether logined
	if (!checkLogin(false)) {
		$("#login-box").modal('show');
        return;            			
	};
	var at_comment_id = 0;
	var is_at = $(this).parents('form.ct-form').prev().find('.tb').length > 0;
	if (is_at) {
		at_comment_id = $(this).parents('form.ct-form').prev().find('.tb a').attr('at_comment_id');
	};

	var form = $(this).closest('form');
	var textarea = form.find('textarea');
	var content, liuyan, content = liuyan = textarea.val();
	var is_main_form = !!form.next('.ct-list').length;
	if (!liuyan) {
		alert('留言不能为空');
		return false;
	};

	$.ajax({
		url: '/liuyan/proc_zixun',
		type: 'POST',
		dataType: "json",
		data: {
		comment_type: 1, //代表咨询
			tag_type: tag_type, //1代表产品3代表课程
			tag_id: tag_id,
			comment_content: content,
			at_comment_id: at_comment_id
		},
		success: function(data, status, xhr) {
			if (data.err == 0) {
				get_liuyan(tag_type, tag_id, 1, $('#ct-list-full'));
			};
			alert(data.msg, (data.err == '0') ? '恭喜' : '抱歉');
			return false;
		},

		error: function(xhr, status) {
			//alert('数据请求错误');
			return false;
		}
	});
	textarea.stop().animate({
		height: "42px"
	}), textarea.parent().next().hide();
	textarea.val('');
	e.preventDefault();
	e.stopPropagation();

});

$(".ct-list").delegate(".tb a", "blur", function(t) {
	if ($(t.relatedTarget).is('button[type="submit"]') || $(t.relatedTarget).is('textarea')) {
		return;
	}
	var _this = $(this);

	setTimeout(function() {
		t.preventDefault();
		t.stopPropagation();
		var i = $(this),
			n = i.closest("li");
		if (n.siblings().find(".ct-form textarea").val(""), n.siblings().find(".ct-form").slideUp("fast"), i.data("form"))
			i.data("form").slideToggle("fast");

	}, 300);

});

//注册证件


});

$(function() {
	var comment_type = -1; //4代表询价，1代表留言或者客服
	var tagType = 3; //代表课程
	var name = '<?php echo $user_name; ?>';
	var mobile = '<?php echo $mobile; ?>';

	$('.product-btn-xunjia, .product-btn-kefu').click(function() {
		$('#give-message textarea[name="comment"]').val('');
		$('#give-message input[name="mobile_num"]').val('');

		$('#give-message').modal('show');
		if ($(this).hasClass('product-btn-xunjia')) {
			comment_type = 4;
		} else if ($(this).hasClass('product-btn-kefu')) {
			comment_type = 1;
		} else {
			comment_type = -1;
		}
		return;
	});

	$('a.btn-give-message').click(function() {
		var content = $('#give-message textarea[name="comment"]').val();

		$('#address_err').text('').hide();
		$('#mobile_num_err').text('').hide();
		if (content.length < 10) {
			//$('#give-message textarea[name="comment"]').focus();
			$('#address_err').text('留言不能少于10个字').show('slow');
			return false;
		};

		var mobile_num = $('#give-message input[name="mobile_num"]').val();

		var phone = /^1([38]\d|4[57]|5[0-35-9]|7[06-8]|8[89])\d{8}$/;
		if (!phone.test(mobile_num)) {
			$('#mobile_num_err').text('请填写正确的手机号码').show('slow');
			//$('#give-message input[name="mobile_num"]').focus();
			return false;
		}

		if (comment_type == -1) {
			$('#give-message').modal('hide');
			alert('数据错误');
			return false;
		};

		$.ajax({
			url: '/liuyan/proc_zixun',
			type: 'POST',
			dataType: "json",
			data: {
				comment_type: comment_type,
				tag_type: tag_type,
				tag_id: tag_id,
				comment_content: content,
				name: name,
				mobile: mobile
			},
			success: function(data, status, xhr) {

				alert(data.msg, (data.err == '0') ? '恭喜' : '抱歉');
				data.err == '0' && $('#give-message').modal('hide');
				//console.log(data);
			},

			error: function(xhr, status) {
				alert('数据请求错误');
			}
		});

	});

	//讨论区和评价区功能
	$('.area_selector').click(function(){
		$('.area_selector').removeClass('active');
		if($(this).hasClass('discus_area')) {
			$('.area-2').hide();
			$('.area-1').show();
			$(this).addClass('active');
		} 
		if($(this).hasClass('pingjia_area')) {
			$('.area-2').show();
			$('.area-1').hide();
			$(this).addClass('active');
		} 
	});

	
	});

	// fetch whether user add this product to collected box
	setTimeout(
	function(){
		$.ajax({
			url:'/product/is_product_collected',
			type:'POST',
			timeout:30000,
			data:{
				product_id:tag_id,
				product_type:3
			},
			success:function(data){
				if(data == 1) {
					$('#add_to_collect').addClass('active');			
				}
				console.log('data is: ' + data);
			},
			error:function(xhr, status, err) {
				console.log("DEBUG: status"+status+" \nError:"+err);
			},
			complete:function(xhr) {
				console.log(xhr);
			}
		});
	}
	,3000);

	</script>
	

<?php include APPPATH . 'views/common/footer.php'?>