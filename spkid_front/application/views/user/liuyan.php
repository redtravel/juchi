<?php if($full_page): ?>
<?php include APPPATH . 'views/common/user_header.php'?>
               
<div class="personal-center-right">
                    <h1 class="page-title">我的评价</h1>
                    <ul class="order-status order-status2 clearfix">
                    <li><a href="#" class="active" data-type="1">待评价商品</a></li>
                    <li><a href="#" data-type="2">待评价课程</a></li>
                    <li><a href="#" data-type="3">最近三个月已评价</a></li>
                    </ul>
                    
                    <div class="my-evaluation-tit clearfix">
                         <div class="fl-left">商品信息</div>
                         <div class="fl-right">评价状态</div>
                    </div>
                    
                    <ul class="my-evaluation-lsit clearfix" id="listdiv">
                    <?php endif; ?>
                    <?php foreach ($liuyan_list as $liuyan): ?>
                    <li>
                    <div class="my-evalution-nr clearfix">
                         <div class="fl-left pingjia-left">
                              <a href="<?php print $liuyan->url ?>" class="evalution-xx">
                                 <img src="<?php print $liuyan->tiny_url ?>">
                                 <div class="pingjia-bt"><?php echo $liuyan->product_name ?><p>购买时间：<?php echo $liuyan->order_create_date; ?></p></div>
                              </a>
                        </div>
                        <div class="fl-right pingjia-right">
                            <?php if(empty($liuyan->comment_content)): ?>
                             <a href="#" class="click-pingjia" onclick="load_dianping_panel('<?php echo $liuyan->product_id ?>')">点击评价</a>                            
                             <p>评价审核通过后您将有机会获得演示站积分（最多不超过<?=$user->comment_point?>个）</p>
                             <?php else: ?>
                             <span title="<?=$liuyan->comment_content?>"><?php echo mask_str($liuyan->comment_content, 40, 0); ?></span>
                             <?php endif; ?>
                        </div>
                   </div>
                   </li>
                   <?php endforeach?>
                   <?php if($full_page): ?>
                    </ul>
              </div>
        </div>    
    </div>
</div>

<!-- 弹层开始 -->
<div id="forgot-box" class="modal v-pov " tabindex="-1" role="dialog" aria-hidden="true">
    <input type="hidden" id="product_id" value="">
      <div class="modal-dialog eva-pop">
        <div class="modal-content">
          <div class="modal-header v-close">
              <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
              <ul class="pingjia-pop clearfix">
              <!--
              <li>
              <label><i>*</i>评价：</label>
              <div class="pingjia-start">
                   <a class="s_star1"></a>
                   <a class="s_star2"></a>
                   <a class="s_star3"></a>
                   <a class="s_star4"></a>
                   <a class="s_star5"></a>
              </div>             
              </li>
              -->
              <li class=" clearfix">
               <label><i>*</i>心得：</label>
               <textarea class="goods-comment-content active" name="fl_text" id="J_commentContent">商品是否给力？快分享您的购买心得吧~</textarea>
              </li>
              </ul>
              
          </div>
          <div class="modal-body v-button pingjia-button">
              <button onclick="post_dianping();" class="btn btn-lg btn-red" type="submit">发布评价</button>
              
          </div>
        </div>
      </div>
</div>
<!-- 弹层结束 -->
<script>
var order_status = '<?php echo $filter["data_type"] ?>';
var order_page_count = '<?php echo $filter["page_count"] ?>';
var order_page = '<?php echo $filter["page"] ?>';
$('.order-status li a').bind("click",function(){
    //移除dingdan-status样式下所有a标签currt样式
    $('.order-status li a').removeClass('active');
    //当前点击a标签添加currt样式
    $(this).addClass('active');
    order_status = $(this).attr('data-type');
    filter_result(order_status, 1);
});

$('#forgot-box textarea').focus(function () {
    if ($(this).val()=='商品是否给力？快分享您的购买心得吧~') {
        $(this).val('');
    };
});

$('#forgot-box textarea').blur(function () {
    if ($(this).val()=='') {
        $(this).val('商品是否给力？快分享您的购买心得吧~');
    };
});
        
function filter_result(status,page)
{
    if (page == 0)
    {
        page = order_page;
    }
    if(page < 1)
    {
        page = 1;
    }
    if(page > order_page_count)
    {
        page = order_page_count;
        return false;
    }
    order_page = page;
    order_status = status;

    $.ajax({
            url:'/user/my_liuyan',
            data:{is_ajax:true,data_type:status,page:page,rnd:new Date().getTime()},
            dataType:'json',
            type:'POST',
            success:function(result){
                    if(result.error==0){
                        if (order_page == 1){
                            order_page_count = result.page_count;
                            $('#listdiv').html(result.content);
                        } else {
                            $('#listdiv').append(result.content);
                        }

                        //order_status = result.order_status;
                        //if (result.content.length <= 0) order_page--;
                    }
            }
    });
}
//滚动加载
var range = 300;
$(document).bind("scroll", function(){
    var srollPos = $(window).scrollTop();
    var totalheight = parseFloat($(window).height()) + parseFloat(srollPos);  
    if(($(document).height()-range) <= totalheight) {
        order_page++;
        //alert(order_page);
        filter_result(order_status, order_page);//调用
    }
});

function load_dianping_panel(product_id)
{
    $.ajax({
        url:'/user/load_dianping_panel',
        data:{is_ajax:true,product_id:product_id,rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
            if(result.error!=0){
                alert(result.msg);
                return false;
            }
            $("#product_id").val(product_id);
            $('#forgot-box').modal('show');
        }
    });
}
//以中文字算长度
function cnlength(str){return Math.ceil(str.replace(/[^\x00-\xff]/g, "**").length/2)}
//点评检查
function check_dianping_word_length()
{
    var content=$.trim($('textarea[name=fl_text]').val());
    content = content.replace(/商品是否给力？快分享您的购买心得吧~/g, '');
    var content_length=cnlength(content);

    if(content_length == 0 || content == ''){
        alert('评论内容不能为空');
        return false;
    }else if(content_length< 5){
        alert('评论内容至少为5个汉字');
        return false;
    }else if(content_length>200){
        alert('评论内容最多只能为200个汉字');
        return false;
    }
        
    return true;
}

function post_dianping()
{
    var parent=$('#forgot-box');
    var fl_text=$.trim($('textarea[name=fl_text]',parent).val());
    var product_id = $.trim($('#product_id',parent).val());
 
    if (!check_dianping_word_length()) {
        return false;
    }
    
    $.ajax({
        url:'/user/post_dianping',
        data:{is_ajax:true,product_id:product_id,comment_content:fl_text,rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
            if(result.error!=0){
                alert(result.msg);
                return false;
            }
            alert(result.msg);
            location.reload();
        }
    });
}
</script>
<?php include_once(APPPATH . "views/common/footer.php");?>
<?php endif; ?>