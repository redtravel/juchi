<?php if($full_page): ?>
<?php include APPPATH . 'views/common/user_header.php'?>
<div class="personal-center-right">

                    <h1 class="page-title">我的订单</h1>
                    <ul class="order-status clearfix">
                    <li><a href="#" data-status="1" class="active">全部订单</a></li>
                    <li><a href="#" data-status="6">待付款<!--<i>11</i>--></a></li>
                    <li><a href="#" data-status="7">待发货<!--<i>11</i>--></a></li>
                    <li><a href="/user/my_liuyan">待评价<!--<i>11</i>--></a></li>
                    </ul>
                    
                    <ul class="my-order-list clearfix">
                        <li class="order-infor">订单信息</li>
                        <li class="unit-price">单价</li>
                        <li class="unit-price">数量</li>
                        <li class="total-order">订单总额</li>
                        <li class="order-statuss">
                                <a class="dingdanzt" href="javascript:void(0);">订单状态</a>
                                <ul class="dingdan-status" style="display:none;">
                                <li><a href="#" class="currt" data-status="1">全部订单</a></li>
                                <li><a href="#" data-status="7">待发货</a></li>
                                <li><a href="#" data-status="6">待付款</a></li>
                                <li><a href="#" data-status="4">已完成</a></li>
                                <li><a href="#" data-status="5">已取消</a></li>
                                </ul>
                        </li>
                        <li class="operation">操作</li>
                    </ul>
                 <?php if(empty($order_list)): ?>   
                 <div class="my-order-content" style="display:none;">
                         <p class="empty">还没有订单哦，赶紧去<a href="/">购物吧~</a></p>
                 </div>
                 <?php endif; ?>
                 <div id="listdiv">
                 <?php endif; ?>
                 <?php foreach($order_list as $order):?>
                 <table class="olist-table">
                      <thead class="serial-number">
                      <tr>
                        <th colspan="4" class="ordersn">订单编号：<?php echo $order->order_sn?><span>下单时间：<?php echo $order->format_create_date ?></span></th>
                        </tr>
                     </thead>
                     
                      <tr>
                        <td class="my-order-xx">
                           <?php foreach($order->order_goods as $k => $g): 
                               //if ($k) continue;
                               $url = ($g->genre_id == PRODUCT_COURSE_TYPE) ? '/product-'.$g->product_id.'.html' : '/pdetail-'.$g->product_id.'.html';
                           ?>
			   <div class="order-zb-xx">
                           <a href="<?=$url?>" target="_blank" class="my-order-of">
                              <div class="center-pic"><img src="<?php print img_url($g->img_url.".85x85.jpg"); ?>"></div>
                              <div class="center-title"><h3><?=$g->product_name?></h3><p><span>颜色：<?=$g->color_name?></span><span>规格：<?=$g->size_name?></span></p></div>
                           </a>
                           <div class="my-order-sprice">￥<?=$g->product_price?></div>
                           <div class="my-order-shuliang"><?=$g->product_num?></div>
			    </div>
                           <?php endforeach; ?>
                        </td>
                        
                        <td class="total">￥<?php echo $order->total_fee ?></td>
                        <td class="st">
                            <div><em style="color: #ff0000;" id="h_status_<?=$order->order_id?>"><?=$order->order_status_txt?></em></div>
                            <?php if(!$order->invalid && !$order->is_ok):?>
                            <div class="order-track order-cancel-<?=$order->order_id?>">                                
                                <a class="track-order" href="javascript:void(0);" data-id="<?=$order->order_id?>">订单跟踪</a>
                                <div class="track-panel" id="track-panel<?=$order->order_id?>" style=" display:none;">
                                     <div class="t-summary" >
                                          <div class="t-title">
                                               <span>订单编号：<?php echo $order->order_sn?></span>
                                               <!--
                                               <span class="fl-right">等待支付</span>
                                               -->
                                          </div>
                                         <div class="chuli2"><?=$order->shipping_name?> <?=$order->invoice_no?></div>
                                    </div>
                                    <!--
                                   <div class="t-detail">
                                        <div class="t-title">订单跟踪</div>
                                        <p class="t-current"><span class="t-time">2016-01-28 13:24:34</span><span>您提交了订单，请等待系统确认</span></p>
                                   </div>
                                    -->
                               </div>
                         </div>
                         <?php endif; ?>
                        </td>
                        <td class="operate">
                            <?php if ($order->can_pay): ?>
                            <div class="order-cancel-<?=$order->order_id?>"><button class="btn lijifukuai" onclick="window.location.href='/cart/success/<?php print $order->order_id ?>';">立即付款</button></div>
                            <?php endif; ?>
                            <?php 
			    if(!$order->invalid && $order->is_ok && empty($order->comment_id)): ?>
			    <!--
                            <button class="btn lijifukuai" style="background-color: #cccccc; color: #000000;" onclick="window.location.href='/user/my_liuyan';">去评价</button>
                            -->
			    <?php endif; ?>
                            <div><a href="/order/info/<?=$order->order_id?>">详情</a></div>
                            <?php if ( (empty($order->lock_admin) && $order->lock_admin == 0) && $order->order_status == 0 ): ?>
                            <div class="order_cancel order-cancel-<?=$order->order_id?>" style="cursor: pointer;" data-id="<?=$order->order_id?>">取消订单</div>
                            <!--
                            <div><a href="#cancel-order"  data-toggle="modal" data-container="body">取消订单</a></div>
                            -->
                            <?php endif ?>
                        </td>
                      </tr>
                      <?php if($order->can_pay): ?>
                      <tfoot class="chuli">
                          <tr><td colspan="4" class="tip"><p>请在<em><?=date("Y-m-d H:i:s", ORDER_INVALID_TIME+strtotime($order->create_date)) ?></em>前完成支付，逾期订单将被系统做超时关闭处理</p></td></tr>
                      </tfoot>
                    <?php endif; ?>
                    </table>
                    <?php endforeach?>
                     <?php if($full_page): ?> 
                    </div>
              </div>
                
          </div>    
     </div>
</div>

<!--取消订单-->
<div id="cancel-order" class="modal v-pov" tabindex="-1" role="dialog" aria-hidden="true">
    <input type="hidden" id="cancel_order_id" value=""/>
    <div class="modal-dialog">
        <div class="modal-content v-order-box">
            <div class="modal-header v-close v-cancel-order">
                <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">您确定要取消订单吗？订单取消后不能恢复。</h4>
            </div>
            <div class="modal-body">
                <div class="modify-dizhi form clearfix">
                    <ul>
                        <form class="address_info">
                            <li class="clearfix">
                                <label class="text-label"><i>*</i><span class="addr-title">取消原因：</span></label>
                                <div class="fl-left">
                                    <select id="invalid_note" class="v-order-select">
                                        <option value="不想买了" selected="selected">不想买了</option>
                                        <option value="缺货">缺货</option>
                                        <option value="拍错了">拍错了</option>
                                        <option value="订单信息有误">订单信息有误</option>
                                        <option value="付款遇到问题">付款遇到问题(余额不足、不知如何付款等)</option>
                                        <option value="重复下单">重复下单</option>
                                        <option value="其他原因">其他原因</option>
                                    </select>                              
                                </div>
                            </li>
                            <li>   
                                <input type="text" value="" id="invalid_note_other" placeholder="请填写具体原因，50字以内" class="v-order-input" style="display: none;">
                            </li>
                            <div class="operate">
                                <button class="btn btn-lg btn-blue cancel_order_btn" type="submit" data-dismiss="modal">确定</button>             
                                <button class="btn cancel" type="submit" data-dismiss="modal">取消</button>              
                            </div>
                        </form>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var order_status = '<?php echo $order_status ?>';
var order_page_count = '<?php echo $filter["page_count"] ?>';
var order_page = '<?php echo $filter["page"] ?>';

	 // dingdan-status样式下li 的a标签绑定点击事件
	$('.dingdan-status li a').bind("click",function(){
		//移除dingdan-status样式下所有a标签currt样式
		$('.dingdan-status li a').removeClass('currt');
		//当前点击a标签添加currt样式
		$(this).addClass('currt');
                order_status = $(this).attr('data-status');
                filter_result(order_status, 1);
	});
        
	$(".order-status li a").click(function(){
            var v_order_status = $(this).attr('data-status');
            if (v_order_status == undefined) return;
            order_status = v_order_status;
            $('.order-status li a').removeClass('active');
            $(this).addClass('active');
            
            filter_result(order_status, 1);
        }); 
        
	  //track-order这个样式所在的元素鼠标移上去事件
	/*$(".track-order").mouseover(function(){
            var v_id = $(this).attr('data-id');
	  	$("#track-panel"+v_id).show();//显示面板
	 });
	 
	 $(".track-order").mouseout(function(){
             var v_id = $(this).attr('data-id');
             $("#track-panel"+v_id).hide();//隐藏面板
	 });*/	
	 
	 $(".dingdanzt").mouseover(function(){
	  	$(".dingdan-status").show();
	  	$(this).addClass("test");
	 });
	  
	 $(".dingdanzt").mouseout(function(){
	 	$(".dingdan-status").hide();
	  	$(this).removeClass("test");
	 });
	  
	  $(".dingdan-status").mouseover(function(){
		$(".dingdan-status").show();
		$(".dingdanzt").addClass("test");
	  });
	  $(".dingdan-status").mouseout(function(){
		  $(".dingdan-status").hide();
		  $(".dingdanzt").removeClass("test");
	  });
          
$(document).on({
    mouseenter: function() { 
        var v_id = $(this).attr('data-id');
	$("#track-panel"+v_id).show();//显示面板
    }, 
    mouseleave: function() {
        var v_id = $(this).attr('data-id');
        $("#track-panel"+v_id).hide();//隐藏面板
    }
}, '.track-order');

function filter_result(status,page)
{
    if (status == 0)
    {
        status = order_status;
    }

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
//alert(order_status+' - '+order_page);
    $.ajax({
            url:'/user/order_list',
            data:{is_ajax:true,status:status,page:page,rnd:new Date().getTime()},
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
$("#invalid_note").change(function(){
    if($(this).val() == '其他原因'){
        $("#invalid_note_other").show();
    } else {
        $("#invalid_note_other").hide();
    }
});
//取消订单
$(document).on('click', '.order_cancel', function (e) {
    var _this = $(this);
    var v_data_id = _this.attr('data-id');
    $("#cancel_order_id").val(v_data_id);
    $("#cancel-order").modal('show');
    /*$.ajax({
            url:'/order/invalid/'+v_data_id,
            data:{},
            dataType:'json',
            type:'GET',
            success:function(result){
                    if(result.error==0){
                        $("#order-pay-"+v_data_id).remove();
                        _this.remove();
                    }
            }
    });*/
});

//取消订单
$(document).on('click', '.cancel_order_btn', function (e) {
    //var _this = $(this);
    var v_data_id = $("#cancel_order_id").val();
    var v_invalid_note = $("#invalid_note").val();
    if ($("#invalid_note_other").css('display') != 'none') {
        var v_invalid_note_other = $("#invalid_note_other").val();
        if (v_invalid_note_other.length > 0) 
            v_invalid_note = v_invalid_note +'-'+v_invalid_note_other;
    }
    
    if (v_data_id == ''){
        alert('参数错误！');
        return false;
    }
    
    $.ajax({
            url:'/order/invalid/'+v_data_id,
            data:{invalid_note: v_invalid_note, rnd: new Date().getTime()},
            dataType:'json',
            type:'GET',
            success:function(result){
                    if(result.error==0){
                        $(".order-cancel-"+v_data_id).remove();
                        $("#h_status_"+v_data_id).html('已作废');
                        //$("#order-pay-"+v_data_id).remove();
                        //_this.remove();
                    }
            }
    });
});

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
</script>           
<?php include APPPATH . 'views/common/footer.php'?>
<?php endif; ?>