<?php include APPPATH."views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/plist.css'); ?>" type="text/css" />
<script type="text/javascript">
function change_order (uri) {
  location.href = base_url+uri;
}
$(function(){
  //筛选区展开伸缩
  // $(".brand span:gt(9)").hide();
  // $("#key").toggle(function(){$(this).removeClass("open").addClass("close").text("收起");$(".brand span:gt(9)").show()},
  //          function(){$(this).removeClass("close").addClass("open").text("展开");$(".brand span:gt(9)").hide()})

  load_ad('div.plist_t_ad','nav-<?php print $nav->nav_id ?>',0,0,'banner');
});
</script>
<div id="content">
	<div class="plist_t_ad" id="plist_t_ad"></div>
	<div class="right">
		<div class="bread_line ablack">您现在的位置： <a href="index.html">首页</a>
		<?php print "&gt; {$nav->nav_name}"; ?>
		</div>
		<div class="screen">
			<h2 class="b">筛选条件</h2>
			<div class="sc_list">
				<div class="c_name">性别：</div>
				<span class="all"><a href="<?php print nav_link($param,array('sex'=>0,'page'=>0)); ?>">全部</a></span>
				<span>
					<a <?php if($param['sex']==1) print 'style="color:#DE5B03"' ?> href="<?php print nav_link($param,array('sex'=>1,'page'=>0)); ?>">男装</a>
				</span>
				<span>
					<a <?php if($param['sex']==2) print 'style="color:#DE5B03"' ?> href="<?php print nav_link($param,array('sex'=>2,'page'=>0)); ?>">女装</a>
				</span>
			</div>
			<?php if ($brand_filter): ?>
			<div class="sc_list brand">
				<div id="key" class="open" <?php if(TRUE||count($brand_filter)<=9) print 'style="display:none;"'; ?>>展开</div>
				<div class="c_name">品牌：</div>
				<span class="all"><a href="<?php print nav_link($param,array('brand_id'=>0,'page'=>0)); ?>">全部</a></span>
				<?php foreach ($brand_filter as $b): ?>
				<span><a <?php if($param['brand_id']==$b->brand_id) print 'style="color:#DE5B03"' ?> href="<?php print nav_link($param,array('brand_id'=>$b->brand_id,'page'=>0)); ?>"><?php print $b->brand_name ?></a></span>
				<?php endforeach ?>
			</div>
			<?php endif ?>
		</div>

		<div class="page_top">
			<div class="px l">排序方式：
				<select name="sort_order" onchange="change_order(this.value);">
					<option value="<?php print nav_link($param,array('sort'=>0,'page'=>0)); ?>">默认排列方式</option>
					<option <?php if($param['sort']==1) print 'selected' ?> value="<?php print nav_link($param,array('sort'=>1,'page'=>0)); ?>">按价格从低到高</option>
					<option <?php if($param['sort']==2) print 'selected' ?> value="<?php print nav_link($param,array('sort'=>2,'page'=>0)); ?>">按价格从高到低</option>
					<option <?php if($param['sort']==4) print 'selected' ?> value="<?php print nav_link($param,array('sort'=>4,'page'=>0)); ?>">按上架时间</option>
					<option <?php if($param['sort']==3) print 'selected' ?> value="<?php print nav_link($param,array('sort'=>3,'page'=>0)); ?>">按销量</option>
				</select>
			</div>
			<div class="page_area r">
				当前第<?php print $filter['page']+1 ?>页/共<?php print $filter['page_count'] ?>页
				<?php if ($filter['page']>0): ?>
				<a href="<?php print nav_link($param,array('page'=>$filter['page']-1)); ?>"> 上一页 </a>
				<?php endif ?>
				<?php if ($filter['page']<$filter['page_count']-1): ?>
				<a href="<?php print nav_link($param,array('page'=>$filter['page']+1)); ?>">下一页</a> 
				<?php endif ?>              
			</div>
		</div>
		<?php include APPPATH.'views/product/product_list_block.php'; ?> 
		<div class="page_d">
			<?php if ($filter['page_count']): ?>  

			当前第<?php print $filter['page']+1 ?>页/共<?php print $filter['page_count'] ?>页     
			<?php if ($filter['page']>0): ?>
			<a href="<?php print nav_link($param,array('page'=>$filter['page']-1)); ?>">上一页</a>
			<?php endif ?>

			<?php for($i=0;$i<$filter['page_count'];$i++): ?>
			<a <?php if($i==$filter['page']) print 'class="on_page"'; ?> href="<?php print nav_link($param,array('page'=>$i)); ?>"><?php print $i+1; ?></a>     
			<?php endfor;?>

			<?php if ($filter['page']<$filter['page_count']-1): ?>
			<a href="<?php print nav_link($param,array('page'=>$filter['page']+1)); ?>">下一页</a> 
			<?php endif ?>

			<?php endif ?>
		</div>
	</div>

	<div class="left">
		<?php foreach ($category_list as $cat): ?>
		<div class="mod">
			<h2 class="bg_y"><?php print $cat->category_name ?></h2>
			<ul class="nav_ul">
				<?php foreach ($cat->sub_category as $c): ?>
				<?php print "<li>"; ?>
				<?php print "<a href=\"category-{$c->category_id}.html\" target=\"_blank\">{$c->category_name}({$c->number})</a></li>"; ?>
				<?php endforeach ?>
			</ul>
		</div>
		<?php endforeach ?>
	</div>

	<div class="cl"></div>  
</div>
<?php include APPPATH.'views/common/footer.php'; ?>