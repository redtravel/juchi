<?php include APPPATH."views/mobile/header.php"; ?>
    <style>
        .page-content { padding-top:0px; }
        .navbar .center, .subnavbar .center { line-height: 56px; }
        .tuan-name { font-size: 15px; color: #232326; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; word-break: break-all; }
        .tuan-time { color:#F93; padding-top: 5px; display: block;}
        .tuan-product .pv-num { font-size: 14px; color: #848689; line-height: 16px; float: left;}
        .tuan-product .tr-num { font-size: 14px; color: #848689; line-height: 16px; float: right;} 
        .product-name-box { height: 40px;  margin-bottom: 0; overflow: hidden;line-height: 18px;}
        .search-list-half{  border-bottom: solid 1px #ddd; margin-top:10px;}
        .search-list-half dd{ border-bottom:none;}
        .tuan-product{ padding-top:11px;}
        body{ overflow: none; }
    </style>
    <div class="views">
        <div class="view view-main">
            <div class="pages">
                <div data-page="searchResult">
                    <div class="navbar">
                        <div class="navbar-inner">
                            <div class="left">
                                <a href="javascript:void(0)" class="link back history-back"><i class="icon icon-back"></i></a>
                            </div>
                            <div class="center c_name"> 课程团购 </div>
                        </div>
                    </div>
                    <div class="page-content  infinite-scroll" >
                        <div id="list">
                            <div class="order-details-rr">
                            <?php if (!empty($tuan_course)){ foreach($tuan_course as $product){ ?>
                                <a class="external" href="/tuan/yueyatuan/<?php echo $product->tuan_id;?>">
                                    <dl class="search-list-half clearfix">
                                        <dt><img src="<?php echo img_url($product->img_315_207);?>" alt="<?php echo $product->tuan_name;?>" /></dt>
                                        <dd>
                                            <div class="product-name-box">
                                                <div class="tuan-name"><?php echo $product->tuan_name;?></div>
                                            </div>
                                            <span class="tuan-time"><?php echo date('m-d H:i', strtotime($product->tuan_online_time)).'  ~  '.date('m-d H:i', strtotime($product->tuan_offline_time));?></span>
                                            <div class="tuan-product clearfix"><span class="pv-num">浏览量：<?php echo ($product->pv_num) ? $product->pv_num : 0 ;?></span>  <span class="tr-num">已报名：<?php echo ($product->tr_num) ? $product->tr_num : 0 ;?></span></div>
                                        </dd>
                                    </dl>
                                </a>
                            <?php } } else{ ?>
                                <div style="margin:2em auto; color:#333;">还没有团购活动哦！</div>
                            <?php }?>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include APPPATH."views/mobile/common/footer-js.php"; ?>
<?php include APPPATH."views/mobile/footer.php"; ?>

