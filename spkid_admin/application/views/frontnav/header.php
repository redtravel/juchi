<div id="head" style="width:100%; background:repeat-x #FFF url(<?php print img_url('img/common/bn_bg.png'); ?>);">
  <div class="head">
    <div class="logo l"><a hidefocus="true" href="<?php print FRONT_HOST;?>" target="_blank"><img src="<?php print img_url('img/common/logo.jpg'); ?>" width="360" height="114" alt="logo" /></a></div>
    <div class="h_r r">
      <div class="h_r_t l ablack"><script type="text/javascript" src="user_api/login_status_js"></script> <a href="user/order">我的订单</a> | <a href="user">会员中心</a> | <a href="help-7.html" target="_blank">帮助中心</a> | <a href="javascript:void(0)" onclick="addBookmark('爱童网--品牌童装折扣特卖会','<?php print FRONT_HOST;?>')">收藏本站</a></div>
      <div class="top_tel"></div>
      <div class="r_online">
          <a class="onlineservice" target="_blank" href="javascript:void(0);" onclick="window.open('<?php print FRONT_HOST;?>/user/show_online', 'onlinewindow', 'height=450, width=600, top=200, left=400, toolbar=no, menubar=no, scrollbars=yes, resizable=no,location=no, status=no');return false;"></a>
          <a class="qqservice" target="_blank" href="user/qq_answer"></a>
          <a class="servicetime"></a>
      </div>
    </div>
    <div class="nav">
        <?php foreach ($all_nav as $nav): ?>
        <?php if ($nav->category_list||$nav->brand_list||$nav->nav_ad_img)://最外层判断 ?>
        <div class="list_sec" rel="<?php print $nav->nav_id ?>">
        <?php if ($nav->category_list||$nav->brand_list)://品类层判断开始 ?>
        <div class="list_sec_left">
        <?php if ($nav->category_list)://分类层判断开始 ?>
        <div class="class_list">
          <span class="class_list_t">分类</span><span><a href="category-<?php print $nav->category_ids; ?>.html">[全部]</a></span>
          <?php foreach ($nav->category_list as $cat): ?>
            <span><a href="category-<?php print $cat->category_id; ?>.html"><?php print $cat->category_name; ?></a></span>
          <?php endforeach; ?>
        </div>
        <?php endif//分类层判断结束 ?>
        <?php if ($nav->brand_list)://品牌层判断开始 ?>
          <div class="class_list">
          <span class="class_list_t">品牌</span><span><a href="category-<?php print $nav->category_ids; ?>.html">[全部]</a></span>
          <?php foreach ($nav->brand_list as $brand): ?>
            <span><a href="<?php print "brand-{$brand->brand_id}-{$nav->category_ids}.html"; ?>.html"><?php print $brand->brand_name ?></a></span>
          <?php endforeach ?>
          </div>
        <?php endif;//品牌层判断结束 ?>
        </div>
        <?php endif//品类层判断结束 ?>
        <?php if ($nav->nav_ad_img): ?>
          <div class="list_sec_right"><a href="<?php print $nav->nav_ad_url?$nav->nav_ad_url:$nav->nav_url; ?>"><img src="<?php print img_url('data/nav_ad/'.$nav->nav_ad_img); ?>" width="225" height="120" /></a></div>
          </div>
        <?php endif ?>

        <?php endif;//最外层判断结束 ?>
        <?php endforeach ?>
      <ul>
        <?php foreach ($all_nav as $nav): ?>
          <?php print "<li rel=\"{$nav->nav_id}\"><span><a href=\"{$nav->nav_url}\">{$nav->nav_name}</a></span></li>"; ?>
        <?php endforeach ?>
      </ul>
    </div>
    <div class="car" id="car" style="position:relative;">
      <div class="car_t l"><a href="cart">购物车 <span class="fred b" id="top_cart_num">0</span>&nbsp;件</a></div>
      <div class="car_b l"><a href="cart">去结算</a></div>      
      <div class="car_pro">
        <div class="car_pro_c"></div>
        <div class="car_pro_d"></div>
      </div>
    </div>
    
    <div class="key">
      <div class="key_t"><font class="fgreen b">热门搜索： </font>
      <?php foreach ($all_hotword as $hotword): ?>
        <a href="<?php print $hotword->hotword_url; ?>" target="_blank"><?php print $hotword->hotword_name; ?></a>&nbsp;
      <?php endforeach ?>
      </div>
      <div class="search">
        <form action="product/search.html" method="get" onsubmit="return check_kw_search();">
        <div class="search_c"><input name="kw" type="text" class="text" value="请输入关键字..." /></div>
        <div class="search_b">
          <input type="submit" name="btn_keyword" class="btn" value=" " />
        </div>
        </form>
      </div>
    </div>
    <div><a class="women" target="_blank" href="#"></a></div>
    <div class="cl"></div>
  </div>
  <div class="cl"></div>
</div>