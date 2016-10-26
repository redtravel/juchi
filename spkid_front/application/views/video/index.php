<?php include APPPATH . 'views/common/header.php'?>
<script src="<?php echo static_style_url('pc/js/jquery-1.11.3.js?v=version')?>" type="text/javascript"></script>
<!-- 图片轮播 -->
	<script type="text/javascript">
		// 轮播数据
		window.SLIDEDATA = [
			// tab不提供则隐藏
			{'marginleft':40, 'left':55},
			{'marginleft':55, 'left':173}
		];
	</script>
<div class="video-banner">

<div class="mainslideData" id="j-mainslide-data">

<?php foreach($focus_image as $item):?>
	    <p>
		<!--图片链接--><span><?php echo $item['href']?></span>
		<!--大banner图--><span><?php echo img_url($item['img_src'])?></span>
		<!--小缩略图--><span><?php echo img_url($item['small_img'])?></span>
		<!--浮动在大banner上的图（图片+文字）--><span><?php echo $item['title']?></span>
		<!--整个banner图的背景颜色--><span>#6b6a5f</span>
		</p>		
<?php endforeach;?>
   
 </div>
            
<div class="m-slidebox rel">
	 <div class="u-loading f-pa" id="j-mainslide-loading"><div class="loadingIcon"></div>
</div>

<div class="g-wrap f-pr" id="j-mainslide-imgs"></div>
<div class="g-container f-pr">
	<div class="slwrap f-pa" id="j-mainslide-items"></div>
	<div class="tabwrap f-pa" id="j-mainslide-tabs">
		 <div class="bg"></div>
			  <div class="con f-pa">
				   <div class="tabtop"></div>
				   <div class="tabs f-pa">
					    <ul class="tabsul clear f-f0">
					    <li class="j-tab tab f-fl tabpos">热门排行</li>
						<li class="j-tab tab f-fl">最新课程</li>
						<li class="j-tab tab f-fl">翻译进度</li>
						</ul>
						<div class="tabline f-pa j-tabline"></div>
					</div>
				    <div class="tabcon f-pr">
						<!-- 排行榜tab -->
						   <div class="tabbox f-pa j-mainslide-tabcon" style="display: block;">
                                <div class="subtabs">
								     <a class="j-subtab subtabpos">周排行</a>|<a class="j-subtab">月排行</a>
							   </div>
							   <div class="subtabcon j-mainslide-subtabcon" style="display: block;">
                                    <div class="listwrap">
                                         <ul class="list j-mainslide-weekrank clearfix">
<?php foreach($hot['week'] as $index => $h):?>
                                         <li>
                                         <a class="item" target="_blank" href="/video/detail/<?php echo $h['ID']?>">
<span class="num"><?php echo $h['views']?></span>
<i class="icon<?php if(3>$index) echo ' icon2';?>"><?php echo 1+$index?></i><span class="txt f-ib f-thide"><?php echo $h['post_title']?></span>
                                         </a>
                                         </li>
<?php endforeach;?>
                                         </ul>
                                    
                                    </div>
                               
                               </div>
<div class="subtabcon j-mainslide-subtabcon" style="display: none;">
                                    <div class="listwrap">
                                         <ul class="list j-mainslide-weekrank clearfix">
<?php foreach($hot['month'] as $index => $h):?>
                                         <li>
                                         <a class="item" target="_blank" href="/video/detail/<?php echo $h['ID']?>">
<span class="num"><?php echo $h['views']?></span><i class="icon<?php if(3>$index) echo ' icon2';?>"><?php echo 1+$index?></i><span class="txt f-ib f-thide"><?php echo $h['post_title']?></span>
                                         </a>
                                         </li>
<?php endforeach;?>
                                         </ul>
                                    
                                    </div>
                               </div>
							     
						   </div>
						<!-- 排行榜tab end -->
						<!-- 最新课程tab -->
						<div class="tabbox f-pa j-mainslide-tabcon">
							  <div class="listwrap">
								    <ul class="list">
<?php foreach($new as $n):?>
								    <li>
                                    <a href="/video/detail/<?php echo $n['ID']?>" target="_blank" class="item">
                                    <span class="ltxt f-ib f-thide"><span class="subtxt"></span><?php echo $n['post_title']?></span>
									</a>
                                    </li>
<?php endforeach;?>
                                   </ul>
							  </div>
						</div>
						<!-- 最新课程tab end -->
					</div>
				</div>
			</div>
		</div>
</div>    

</div>

<script src="<?php echo static_style_url('pc/js/rolling.js')?>" type="text/javascript"></script>
<div class="video-fenlei">
     <div class="category-labels clearfix">
     <p>分类标签:<?php foreach($categorys as $index => $name): ++$index?><a href="#cat<?php echo $index?>"><?php echo $name?></a><?php endforeach?></p>
          <span class="fbsp"><a href="/video/upload">发布视频</a></span>
    </div>
</div>

<div class="video-fication">
     <div class="video-list">
         
          <ul id="cat1" class="video-lb1 clearfix">
                <li>
                <div class="video-gongyong yazhou"><a href="#"><?php echo $categorys[0]?></a></div>
              </li>
<?php foreach(array_slice($video1, 0, 9) as $v):?>
             <li>
             <a href="/video/detail/<?php echo $v->ID?>">
              <div class="video-pic"><img src="<?php echo $v->cover?>"></div>
                 <div class="video-title"><?php echo $v->post_title?></div>
                 <div class="video-xinxi clearfix"><span class="video-zz"><?php echo $v->display_name?></span><span class="video-riqi"><?php echo current(explode(' ', $v->post_date))?></span></div>
                 <div class="video-tubiao clearfix"><span class="video-liul"><?php echo $v->views?></span><span class="video-jt"><?php echo $v->comment_count?></span></div>
              </a>
             </li>
<?php endforeach;?>
        </ul>
<?php if (9 < count($video1)):?>
         <div id="video-liebiao-1" class="video-liebiao" style="display:none;">
              <ul class="video-lb1 clearfix">
              <?php foreach(array_slice($video1, 10) as $v):?>
             <li>
             <a href="/video/detail/<?php echo $v->ID?>">
              <div class="video-pic"><img src="<?php echo $v->cover?>"></div>
                 <div class="video-title"><?php echo $v->post_title?></div>
                 <div class="video-xinxi clearfix"><span class="video-zz"><?php echo $v->display_name?></span><span class="video-riqi"><?php echo current(explode(' ', $v->post_date))?></span></div>
                 <div class="video-tubiao clearfix"><span class="video-liul"><?php echo $v->views?></span><span class="video-jt"><?php echo $v->comment_count?></span></div>
              </a>
             </li>
<?php endforeach;?>
             </ul>
         </div> 
        <div class="video-more" onclick="openShutManager(this,'video-liebiao-1',false,'隐藏更多','查看更多')"><a class="video-btn f-icon0 f-icon1" href="javascript:;">查看更多</a></div>
<?php endif;?>           
          
       <ul id="cat2" class="video-lb1 clearfix">
            <li><div class="video-gongyong zhongzhi"><a href="#"><?php echo $categorys[1]?></a></div></li>
<?php foreach(array_slice($video2, 0, 9) as $v):?>
             <li>
             <a href="/video/detail/<?php echo $v->ID?>">
              <div class="video-pic"><img src="<?php echo $v->cover?>"></div>
                 <div class="video-title"><?php echo $v->post_title?></div>
                 <div class="video-xinxi clearfix"><span class="video-zz"><?php echo $v->display_name?></span><span class="video-riqi"><?php echo current(explode(' ', $v->post_date))?></span></div>
                 <div class="video-tubiao clearfix"><span class="video-liul"><?php echo $v->views?></span><span class="video-jt"><?php echo $v->comment_count?></span></div>
              </a>
             </li>
<?php endforeach;?>
        </ul>
<?php if (9 < count($video2)):?>
<div id="video-liebiao-2" class="video-liebiao" style="display:none;">
              <ul class="video-lb1 clearfix">
              <?php foreach(array_slice($video2, 10) as $v):?>
             <li>
             <a href="/video/detail/<?php echo $v->ID?>">
              <div class="video-pic"><img src="<?php echo $v->cover?>"></div>
                 <div class="video-title"><?php echo $v->post_title?></div>
                 <div class="video-xinxi clearfix"><span class="video-zz"><?php echo $v->display_name?></span><span class="video-riqi"><?php echo current(explode(' ', $v->post_date))?></span></div>
                 <div class="video-tubiao clearfix"><span class="video-liul"><?php echo $v->views?></span><span class="video-jt"><?php echo $v->comment_count?></span></div>
              </a>
             </li>
<?php endforeach;?>
             </ul>
        </div>
      <div class="video-more" onclick="openShutManager(this,'video-liebiao-2',false,'隐藏更多','查看更多')"><a class="video-btn f-icon0 f-icon1" href="javascript:;">查看更多</a></div> 
<?php endif;?>      
      
      <ul id="cat3" class="video-lb1 clearfix">
            <li><div class="video-gongyong zhengji"><a href="#"><?php echo $categorys[2]?></a></div></li>
<?php foreach(array_slice($video3, 0, 9) as $v):?>
             <li>
             <a href="/video/detail/<?php echo $v->ID?>">
              <div class="video-pic"><img src="<?php echo $v->cover?>"></div>
                 <div class="video-title"><?php echo $v->post_title?></div>
                 <div class="video-xinxi clearfix"><span class="video-zz"><?php echo $v->display_name?></span><span class="video-riqi"><?php echo current(explode(' ', $v->post_date))?></span></div>
                 <div class="video-tubiao clearfix"><span class="video-liul"><?php echo $v->views?></span><span class="video-jt"><?php echo $v->comment_count?></span></div>
              </a>
             </li>
<?php endforeach;?>
    </ul>
<?php if (9 < count($video3)):?>
<div id="video-liebiao-3" class="video-liebiao" style="display:none;">
              <ul class="video-lb1 clearfix">
              <?php foreach(array_slice($video3, 10) as $v):?>
             <li>
             <a href="/video/detail/<?php echo $v->ID?>">
              <div class="video-pic"><img src="<?php echo $v->cover?>"></div>
                 <div class="video-title"><?php echo $v->post_title?></div>
                 <div class="video-xinxi clearfix"><span class="video-zz"><?php echo $v->display_name?></span><span class="video-riqi"><?php echo current(explode(' ', $v->post_date))?></span></div>
                 <div class="video-tubiao clearfix"><span class="video-liul"><?php echo $v->views?></span><span class="video-jt"><?php echo $v->comment_count?></span></div>
              </a>
             </li>
<?php endforeach;?>
             </ul>
         </div>
      <div class="video-more" onclick="openShutManager(this,'video-liebiao-3',false,'隐藏更多','查看更多')"><a class="video-btn f-icon0 f-icon1" href="javascript:;">查看更多</a></div> 
<?php endif;?>      
      
      <ul id="cat4" class="video-lb1 clearfix">
            <li><div class="video-gongyong qiatan"><a href="#"><?php echo $categorys[3]?></a></div></li>
<?php foreach(array_slice($video4, 0, 9) as $v):?>
             <li>
             <a href="/video/detail/<?php echo $v->ID?>">
              <div class="video-pic"><img src="<?php echo $v->cover?>"></div>
                 <div class="video-title"><?php echo $v->post_title?></div>
                 <div class="video-xinxi clearfix"><span class="video-zz"><?php echo $v->display_name?></span><span class="video-riqi"><?php echo current(explode(' ', $v->post_date))?></span></div>
                 <div class="video-tubiao clearfix"><span class="video-liul"><?php echo $v->views?></span><span class="video-jt"><?php echo $v->comment_count?></span></div>
              </a>
             </li>
<?php endforeach;?>
        </ul>
<?php if (9 < count($video4)):?>
<div id="video-liebiao-4" class="video-liebiao" style="display:none;">
              <ul class="video-lb1 clearfix">
              <?php foreach(array_slice($video4, 10) as $v):?>
             <li>
             <a href="/video/detail/<?php echo $v->ID?>">
              <div class="video-pic"><img src="<?php echo $v->cover?>"></div>
                 <div class="video-title"><?php echo $v->post_title?></div>
                 <div class="video-xinxi clearfix"><span class="video-zz"><?php echo $v->display_name?></span><span class="video-riqi"><?php echo current(explode(' ', $v->post_date))?></span></div>
                 <div class="video-tubiao clearfix"><span class="video-liul"><?php echo $v->views?></span><span class="video-jt"><?php echo $v->comment_count?></span></div>
              </a>
             </li>
<?php endforeach;?>
             </ul>
         </div>
      <div class="video-more" onclick="openShutManager(this,'video-liebiao-4',false,'隐藏更多','查看更多')"><a class="video-btn f-icon0 f-icon1" href="javascript:;">查看更多</a></div> 
<?php endif;?>      
      <ul id="cat5" class="video-lb1 clearfix">
            <li><div class="video-gongyong qiatan2"><a href="#"><?php echo $categorys[4]?></a></div></li>
<?php foreach(array_slice($video5, 0, 9) as $v):?>
             <li>
             <a href="/video/detail/<?php echo $v->ID?>">
              <div class="video-pic"><img src="<?php echo $v->cover?>"></div>
                 <div class="video-title"><?php echo $v->post_title?></div>
                 <div class="video-xinxi clearfix"><span class="video-zz"><?php echo $v->display_name?></span><span class="video-riqi"><?php echo current(explode(' ', $v->post_date))?></span></div>
                 <div class="video-tubiao clearfix"><span class="video-liul"><?php echo $v->views?></span><span class="video-jt"><?php echo $v->comment_count?></span></div>
              </a>
             </li>
<?php endforeach;?>
        </ul>
<?php if (9 < count($video5)):?>
      <div id="video-liebiao-5" class="video-liebiao" style="display:none;">
              <ul class="video-lb1 clearfix">
              <?php foreach(array_slice($video5, 10) as $v):?>
             <li>
             <a href="/video/detail/<?php echo $v->ID?>">
              <div class="video-pic"><img src="<?php echo $v->cover?>"></div>
                 <div class="video-title"><?php echo $v->post_title?></div>
                 <div class="video-xinxi clearfix"><span class="video-zz"><?php echo $v->display_name?></span><span class="video-riqi"><?php echo current(explode(' ', $v->post_date))?></span></div>
                 <div class="video-tubiao clearfix"><span class="video-liul"><?php echo $v->views?></span><span class="video-jt"><?php echo $v->comment_count?></span></div>
              </a>
             </li>
<?php endforeach;?>
             </ul>
         </div>
      <div class="video-more" onclick="openShutManager(this,'video-liebiao-5',false,'隐藏更多','查看更多')"><a class="video-btn f-icon0 f-icon1" href="javascript:;">查看更多</a></div>
<?php endif;?>     
     </div>



</div>

<script>
	function openShutManager(oSourceObj,oTargetObj,shutAble,oOpenTip,oShutTip){
	var sourceObj = typeof oSourceObj == "string" ? document.getElementById(oSourceObj) : oSourceObj;
	var targetObj = typeof oTargetObj == "string" ? document.getElementById(oTargetObj) : oTargetObj;
	var openTip = oOpenTip || "";
	var shutTip = oShutTip || "";
	
	// 控制上下箭头	
	$(sourceObj).find('a').toggleClass('f-icon1');
	
	
	if(targetObj.style.display!="none"){
	   if(shutAble) return;
	   targetObj.style.display="none";
	   if(openTip  &&  shutTip){
		$(sourceObj).find('a').text(shutTip); 
		
	   }
	} else {
	   targetObj.style.display="block";
	   if(openTip  &&  shutTip){
		   $(sourceObj).find('a').text(openTip); 
	   }
	}
	}
</script>

<?php include APPPATH . 'views/common/footer.php'?>
