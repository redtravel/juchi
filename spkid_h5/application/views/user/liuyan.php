<?php if ($full_page): ?>
<?php include APPPATH . "views/common/header.php"; ?>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />
<script type="text/javascript">

$(function () {
    //确定浮层的位置
    $('.liuyanList .btn_g_78').click(function () {
        var t = $(this).offset().top+35;
        var l = $(this).offset().left-235;
        $('.myAdeMsgBox').css({'left':l,'top':t});
    });
});

var order_page_count = '<?php echo $filter["page_count"] ?>';
var order_page = '<?php echo $filter["page"] ?>';

function filter_result(status,page)
{
    page_count = order_page_count;

    if (page == 0)
    {
        page = order_page;
    }
    if(page < 1)
    {
        page = 1;
    }
    if(page > page_count)
    {
        page = page_count;
    }

    $.ajax({
        url:'/user/liuyan',
        data:{is_ajax:true,page:page,rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
            if(result.error==0){
                $('#listdiv').html(result.content);
            }
        }
    });

    return false;
}

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
            var mask='<div id="mask"></div>',
                h=$(document).height(),
                w=$(document).width();
            $('#float_panel_dianpin').html(result.content);
            $('#float_panel_dianpin').show();
            $('body').append(mask);
            $('#mask').css({'height':h,'width':w,'opacity':'0.01'});
            $('#mask').click(function () {
                $('#float_panel_dianpin').html('').hide();
                $(this).remove();
            });
            $('#float_panel_dianpin a').click(function () {
                post_dianping();
            });
        }
    });
}

function post_dianping()
{
    var parent=$('#float_panel_dianpin');
    var fl_text=$.trim($('textarea[name=fl_text]',parent).val());
    var product_id = $.trim($('input[type=hidden][name=product_id]',parent).val());
 
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


//点评检查
function check_dianping_word_length()
{
    var content=$.trim($(':input[name=fl_text]').val());
    content = content.replace(/字数限制5-200个汉字/g, '');
    var content_length=cnlength(content);
    $('#float_panel_dianpin .errorInfo').hide();
    if(content_length == 0 || content == ''){
        $('#float_panel_dianpin .errorInfo').html("评论内容不能为空").show();
        return false;
    }else if(content_length< 5){
        $('#float_panel_dianpin .errorInfo').html("评论内容至少为5个汉字").show();
        return false;
    }else if(content_length>200){
        $('#float_panel_dianpin .errorInfo').html("评论内容最多只能为200个汉字").show();
        return false;
    }
        
    return true;
}

</script>
<div id="content">
    <div class="now_pos">
        <a href="/">首 页</a>
        >
        <a href="/user">会员中心</a>
        >
        <a class="now">咨询与点评</a>
        <!-- come soon
        <a class="notice" href="/">全场满200减20!</a>
        -->
    </div>
    <div class="ucenter_left">
        <?php include APPPATH . "views/user/left.php"; ?>
    </div>
    <div class="ucenter_main">
        <div class="switch_block" id="listdiv">
            <div class="switch_block_title">
                <ul>
                    <li class="sel">我的点评</li>
                    <li onclick="location.href='/user/leaveword'">我的咨询</li>
                </ul>
            </div>
        <?php endif; ?>
        <div class="switch_block_content liuyanList">
            <table width="738" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <th width="20%">图片</th>
                    <th width="20%">品牌</th>
                    <th width="20%">名称</th>
                    <th width="16%">单价</th>
                    <th width="24%">操作</th>
                </tr>
                <?php
                if (!empty($liuyan_list)):
                    foreach ($liuyan_list as $liuyan):
                        ?>
                        <tr>
                            <td>
                                <a href="<?php echo $liuyan->url ?>" target="_blank">
                                    <img src="<?php echo $liuyan->teeny_url ?>" width="73" height="73">
                                </a>
                            </td>
                            <td><?php echo $liuyan->brand_name ?></td>
                            <td><?php echo $liuyan->product_name ?></td>
                            <td class="red"><?= $liuyan->sale_price ?>元</td>
                            <td>
                                <?php if (isset($liuyan->comment_date)): ?>
                                    <?php if ($liuyan->is_audit == 1): ?>获得<?= number_format($liuyan->pay_points, 0) ?>积分!<br>
                                        <font class="c99">评论时间：<?php echo isset($liuyan->comment_date) ? $liuyan->comment_date : '' ?><font>
                                    <?php else: ?>审核中...<?php endif; ?>
                                <?php else: ?><input id="loadbutton" type="button" onclick="load_dianping_panel('<?php echo $liuyan->product_id ?>')" class="btn_g_78 font14b" value="点评"><?php endif; ?>

                            </td>
                        </tr>
                        <?php
                    endforeach;
                endif;
                ?>
                        <?php if (isset($liuyan->comment_date)): ?>
                            <tr>
                                <td colspan="5" class="bottomPage">
                                    <div class="switch_block_page ablack">
                                        <?php include(APPPATH . 'views/user/page.php') ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
            </table>
        </div>
        <?php if ($full_page): ?>

        </div>

    </div>
</div>
<div id="float_panel_dianpin" class="myAdeMsgBox"></div>
<?php include APPPATH . 'views/common/footer.php'; ?>
<?php endif; ?>
