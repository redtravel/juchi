<?php include APPPATH . 'views/common/header.php'?>
<script src="<?php echo static_style_url('pc/js/jquery-1.11.3.js?v=version')?>" type="text/javascript"></script>
<!--course-bar start-->
<div class="course-bar">
     <div class="all-exhibits clearfix">
          <a href="/brand/lists/0"><div class="exhibits-qb">全部</div></a>
          <ul class="exhibits-name">
<?php foreach($flags as $f):
if ($fid == $f['flag_id']):
    $area = $f['flag_name'];
?>
<li class="exhibits-current"><a href="/brand/lists/<?php echo $f['flag_id']?>"><?php echo $f['flag_name']?>品牌</a></li>
<?php else:?>
<li><a href="/brand/lists/<?php echo $f['flag_id']?>"><?php echo $f['flag_name']?>品牌</a></li>
<?php endif;endforeach?>
          </ul>
    </div>
     
     
     <div class="course-list">
          <div class="course-container">
          <div class="exhibits-map">首页&nbsp;&gt;&nbsp;<?php if(0  == $fid){$area = '全部';} echo $area;?>品牌</div>
          <div class="pro-categories">
<?php foreach($categorys as $c):?>
<a href="#"><?php echo $c['name'];?></a>
<?php endforeach?>
        </div>
<?php foreach($categorys as $pid => $c):
unset($c['name']);?>
<div class="pro-level">
<a href="<?php echo $pid;?>">全部</a>
<?php foreach($c as $child):
?>
<a href="<?php echo $child['id'];?>"><?php echo $child['name'];?></a>
<?php endforeach?>
</div>
<?php endforeach?>                
         </div>
         
         <div class="exhibits-cation">
                    <h2 class="exhibits-biaoti clearfix"><span></span></h2>
                    
                    <ul class="ified-content clearfix"></ul>
                    
                    <div class="exhibits-help"><a href="/about_us/team_work"><div>+</div>申请合作</a></div>
                        
           </div>
         
          
     
    </div>
</div>

<script>
$('.pro-categories>a').on('click', function(e){
    e.preventDefault();
    $('.pro-categories>a.pro-current').removeClass('pro-current');
    $('.pro-level.active').removeClass('active');
    $(this).addClass('pro-current');
    var index = $(this).index();
    $('.pro-level').eq(index).addClass('active');
    $('.pro-level').eq(index).children().first().trigger('click');
})
$('.pro-level>a').on('click', function(e){
    e.preventDefault();
    $('.pro-level>a.pro-current').removeClass('pro-current');
    $(this).addClass('pro-current');
    $('.exhibits-biaoti>span').html($('.pro-categories>.pro-current').html()+$('.active>.pro-current').html()+'类');
    var cid = $(this).attr('href');
    get_brand_list(cid);
})
$('.pro-categories>a').first().trigger('click')

function get_brand_list(cid){
    $.getJSON('/brand/ajax_brand_list', {fid:<?php echo $fid?>, cid:cid}, function(data){
        var list = '';
        for( var i in data ){
            var item = data[i];            
            //console.log(item);
            list += '<li><div class="ified-pic"><a href="/brand/index/'+item['brand_id']+'"><img src="'+item['brand_logo']+'"></a></div>';
            list += '<div class="ified-nr"><p class="flags clearfix"><img src="'+item['flag_url']+'"><span class="guoji-mc">'+item['brand_name']+'</span></p>';
            list += '<p class="flags-ction"><a href="/brand/index/'+item['brand_id']+'">'+item['brand_info']+'</a></p></div></li>';
        }
        $('.ified-content').html(list);
    })
}
</script>
<!--course-bar end-->
<?php include APPPATH . 'views/common/footer.php'?>
