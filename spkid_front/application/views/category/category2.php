<?php include APPPATH."views/common/header.php"; ?>
<!--sorting start-->
<div class="sorting">
     <div class="sorting-nr"><span>排序：</span>
         <a href="javascript:void(0);" class="filter zx" data-type="11">最热</a>
         <a href="javascript:void(0);" class="filter" data-type="10">最新</a>
         <a href="javascript:void(0);" class="filter" data-type="4">价格</a>
         <i class="sort-on"></i></div>
</div> 
<!--sorting end-->
    
<!--sorting-pic start-->
<div class="sorting-pic">
     <div class="sorting-lb">
        <ul class="sortinglist clearfix">
	<?php foreach( $front_types AS $i=>$type ):?>
            <li><a href="javascript:void(0);" class="r<?=($i+1)?> <?php if ($type[0]=='-1')echo ' rr';?>" <?php if ( $type[0]>0):?>data-id="<?=$type[0]?>"<?php endif;?>><?=$type[1]?></a></li>
	<?php endforeach;?>
        </ul>     
    </div>
</div>
<!--sorting-pic end-->
<div class="wrap-mian wrap-min2">
<!--all-goods-start-->
     <div class="all-goods">
          <ul class="all-goods-lb clearfix">
              ﻿<?php include APPPATH."views/category/item.php"; ?>
          </ul>     
     </div>
<!--all-goods-end-->
</div>  
<script type="text/javascript">
var v_type_id = 0;
var v_sort_by = 11;
var v_page = 1;
//选择分类
$(".sortinglist a").click(function(){
    $(".sortinglist a").removeClass('active');
    $(this).addClass('active');
    v_type_id = $(this).attr("data-id");
    if (v_type_id == undefined) return;
    v_sort_by = $(".sorting-nr .zx").attr("data-type");
    v_page = 1;
    product_list(v_type_id, v_sort_by, v_page);
});
//点击排序
$(".sorting-nr a").click(function(){
    $(".sorting-nr a").removeClass('zx');   
    var v_sort = $(this).attr("data-type");
    $(this).addClass('zx');
    v_page = 1;
    if (v_sort == 4 || v_sort == 5) {
        if (v_sort == 4) {
            $(".sorting-nr i").removeClass('sort-down');
            $(".sorting-nr i").addClass('sort-on');
            v_sort_by = v_sort;
            $(this).attr("data-type", 5);
            product_list(v_type_id, v_sort_by, v_page);
        } else {
                $(".sorting-nr i").removeClass('sort-on');
                $(".sorting-nr i").addClass('sort-down');
                v_sort_by = v_sort;
                $(this).attr("data-type", 4);
                product_list(v_type_id, v_sort_by, v_page);          
        }
    } else {
        $(".sorting-nr i").removeClass('sort-down');
        $(".sorting-nr i").addClass('sort-on');
	$(".sorting-nr a:eq(2)").attr("data-type", 4);
        v_sort_by = v_sort;
        product_list(v_type_id, v_sort_by, v_page);
    }
});
//滚动加载
var range = 300;
$(document).bind("scroll", function(){  
    var srollPos = $(window).scrollTop();
    var totalheight = parseFloat($(window).height()) + parseFloat(srollPos);  
    if(($(document).height()-range) <= totalheight) {
        v_page++;
        product_list(v_type_id, v_sort_by, v_page);//调用
    }
});

function product_list(p_typeid, p_sort, p_page){
    $.ajax({
            url:'/category-'+p_typeid+'-0-0-0-'+p_sort+'-'+p_page+'.html',
            data:{},
            dataType:'json',
            type:'post',
            success:function(result){
                if(result.err == 0){
                    if (p_page == 1){
                        $(".all-goods-lb").html(result.content);
                    } else {                       
                        $(".all-goods-lb").append(result.content);
                    }
                    if (result.content.length <= 0) v_page--;
                }               
            }
    });
}
</script>
    
<?php include APPPATH.'views/common/footer.php'; ?>