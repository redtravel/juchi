<?php include APPPATH . 'views/common/header.php'?>
<link href="<?php echo static_style_url('pc/css/bootstrap.css?v=version')?>" rel="stylesheet" type="text/css">
<script src="<?php echo static_style_url('pc/js/jquery-1.11.3.js?v=version')?>" type="text/javascript"></script>
<script src="<?php echo static_style_url('pc/js/bootstrap.js?v=version')?>" type="text/javascript"></script>
<!--course-bar start-->
<div class="course-bar">
     <div class="all-exhibits">
     <div class="exhibits-map"><a href="/">首页</a>&gt;<a href="/brand/lists">全部展品</a>&gt;<?php echo $brand->brand_name?></div>
          <div class="exhibits-int clearfix">
              <div class="exhibits-js">
<img src="<?php echo img_url($brand->brand_logo)?>">
                   <p><?php echo $brand->brand_info?></p>
              </div>
              <div class="exhibits-pic"><?php if ('' != $brand->brand_banner):?><img src="<?php echo img_url($brand->brand_banner)?>"><?php endif;?></div>
          </div>
    </div>
    
    <div class="course-list">
         <div class="sorting-nr">
             <ul class="brand-tab clearfix">
             <li data-value="0" class="brand-currt">全部展品</li>
             <li data-value="1">品牌简介与故事</li>
             </ul>
             <div class="brand-list">
                   <ul class="all-goods-lb clearfix">
<?php foreach($product_list as $product):
if(1 == $product->price_show):?>
                         <li>
                         <a href="/pdetail-<?php echo $product->product_id?>.html">
                         <div class="all-goods-img"><img src="<?php echo img_url($product->img_url)?>"></div>
                         <p class="all-goods-mc"><?php echo $product->product_name?></p>
                         <div class="all-goods-js"><?php echo $product->size_name?></div>

                         <div class="all-goods-price"><input name="" type="button" class="show-xunjia" value="询价"></div>
                         </a>
                         </li>
<?php endif;endforeach?>                                                
                     </ul>
             </div>
              
             <div class="brand-list" style="display:none;">
                  <div class="brand-tit">品牌故事</div>
                   <div class="brand-gushi"><?php echo $brand->brand_story?></div>
             </div>
            
          
            <div class="single-brand-right"><a href="#give-message" data-toggle="modal" data-container="body">联系客服</a></div>
              
       </div>
     
    
    
    </div>

</div>

<!--course-bar end-->

<div id="give-message" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog give-message">
        <div class="modal-content">
            <div class="modal-header give-message-tit">
                <span>给我们留言</span>
                <button type="button" class="close give-message" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true"><img src="<?php echo static_style_url('pc/images/close.png')?>"></span></button>
            </div>
          <div class="modal-body">
               <div class="give-contact">
                    <p>小悦悦要忙疯啦，有问题先留言，我们会尽快联系您哈！<span>客服电话：400-9905-920</span></p>
                    <div class="give-input">
                        <form action="/api/comment/add" method="POST " class="ct-form" name="ct-form" data-hostid="3179" data-committype="" data-hosttype="2">
                            <div class="clearfix"><textarea style="height: 90px;" name="comment" aria-required="true" placeholder="留言不能少于10个字"></textarea></div>
                            <span class="err_tip err_tips">不能为空</span>
                            <div class="clearfix" style="margin-top:10px;"><input name="mobile" type="text" placeholder="联系电话"></div>
                            <span class="err_tip err_tips">不能为空</span>
                       </form>
                    </div>
               </div>
          </div>
          
          <div class="modal-body v-button give-message-but">
              <button class="btn btn-lg btn-blue message-btn" type="submit">留言</button>
          </div>
          
        </div>
      </div>
</div>





<div id="give-success" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog give-message">
        <div class="modal-content">
            <div class="modal-header give-message-tit">
                <span>给我们留言</span>
                <button type="button" class="close give-message" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true"></span></button>
            </div>
          <div class="modal-body">
               <div class="give-contact">
                    <p class="ly-success">留言成功！</p>
                    <p class="houer-lx">我们会在24小时内与您联系</p>
                    
               </div>
          </div>
          
          <div class="modal-body v-button give-message-but" style="margin-top:30px;">
              <button class="btn btn-lg" type="submit">留言</button>
              <a href="#" class="zaiciliuyan">再次留言</a>
              
          </div>
          
        </div>
      </div>
</div>

<script>
 $(".brand-tab li").bind("click", function () {
        $(".brand-tab li").removeClass("brand-currt");
        $(this).addClass("brand-currt");
        var i = $(this).attr("data-value");
        $(".brand-list").hide();
        $(".brand-list:eq(" + i + ")").show();
 });
var brand_id = <?php echo $brand->brand_id?>;
$('.message-btn').click(function(){
    
    var content = $('textarea[name=comment]').val();
    var mobile = $('input[name="mobile"]').val();
    if (content.length<10){
        //$('textarea[name=comment]').parent().next().show();
        $('.err_tip').first().show();
        return false;
    }
    $.ajax({url:'/brand/comment', data:{brand_id:brand_id, content:content, mobile:mobile}, dataType:'json', method:'POST', success:function(data){
        if (data.success){
            $('#give-message').modal('hide');
            $('#give-success').modal('show');
        }
    }
    })
})
var range = 50;
var page = 2;
$(document).on("scroll", function(){  
    var srollPos = $(window).scrollTop();
    var totalheight = parseFloat($(window).height()) + parseFloat(srollPos);
    if(($(document).height()-range) <= totalheight) {
        $.ajax({
        url:'/brand/index/'+brand_id,
            data:{page:page},            
            success:function(data){
                if(data && '' != data){
                    $('.all-goods-lb').append(data);
                    page++;
                }
                
            }
        })
    }
});
</script>

<?php include APPPATH . 'views/common/footer.php'?>
