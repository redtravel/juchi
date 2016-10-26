<?php
/**
 * 自助退货查看
 */
?>
<?php include APPPATH."views/common/header.php"; ?>
<script type="text/javascript" src="<?php print static_style_url('js/jcarousellite.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/lhgdialog/lhgdialog.min.js') ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/user.js'); ?>"></script>
<link rel="stylesheet" href="<?php print static_style_url('css/ucenter.css'); ?>" type="text/css" />

<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/common_new.css'); ?>" media="all" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/layoutFlow.css'); ?>" media="all" charset="utf-8" />
<script type="text/javascript" src="<?php print static_style_url('js/jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php print static_style_url('js/util.js?20131121083606'); ?>" ></script>
<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/order.css'); ?>" media="all" charset="utf-8" />
<link rel="stylesheet" type="text/css" href="<?php print static_style_url('css/orderCN.css'); ?>" media="all" charset="utf-8" />

<div id="content">
    <div class="now_pos">
            <a href="/">首 页</a>
            >
            <a href="/user">会员中心</a>
            >
            <a class="now">自助退货</a>
    </div>
    <div class="ucenter_left">
    <?php include APPPATH."views/user/left.php"; ?>
    </div>
<div class="ucenter_main">
<div class="goodstab" style="overflow:visible;float:left;">
    <div class="goodsReturnTop">
      <span class="f14b">申请编号：</span>
      <span class="f14"><?=$apply_id?></span>
      <span class="f14b">当前状态：</span>
      <span class="f14"><?=$apply_return_product[0]['apply_status']?></span>
      <span class="f14b">申请时间：</span>
      <span class="f14"><?=$apply_return_product[0]['apply_time']?></span>
      <?php if($apply_return_info['apply_status']==0&&$apply_return_info['provider_status']==0){?>
      <a href="/user/modify_apply_return_info/<?=$apply_id?>" class="see_order4" target="_blank" title="修改" hidefocus>修改</a>
      <a href="/user/cancel_apply_return/<?=$apply_id?>" 
            class="see_order4" target="_blank" title="取消申请" hidefocus>取消申请</a>
      <?php }?>
      <div class="goodsReturnPro"></div>
    </div>
    <div class="goodsReturnBox goodsReturnDetail" style="border-bottom:0;overflow:visible;">
      <div class="goodsReturnTable">
        <table width="100%" cellpadding="0" cellspacing="0">
          <tr>
            <th width="15%">商品图片</th>
            <th width="27%">商品信息</th>
            <th width="10%">退货数量</th>
            <th width="15%">退货理由</th>
            <th width="20%">上传图片</th>
            <th width="13%">状态</th>
          </tr>
          <?php foreach($apply_return_product as $product){?>
          <tr>
            <td colspan="6">
              <div class="goodsReturnTd" style="width:15%">
                <img src="<?=img_url($product['img_url'] .'.85x85.jpg')?>" width="63">
              </div>
              <div class="goodsReturnTd" style="width:27%">
                <div class="goodsInfo">
                  <div><?=$product['product_name']?></div>
                  <div><?=$product['product_price']?></div>
                  <div>款号：<?=$product['product_sn']?></div>
                  <div>
                    <span>颜色：<?=$product['color_name']?></span>
                    <span>尺码：<?=$product['size_name']?></span>
                  </div>
                </div>
              </div>
              <div class="goodsReturnTd" style="width:10%">
                <?=$product['product_number']?>
              </div>
              <div class="goodsReturnTd" style="width:15%">
                <div class="reason"><?=$product['return_reason_desc']?></div>
              </div>
              <div class="goodsReturnTd" style="width:20%;">
                <div class="imgPreview">
                  <?php foreach($product['imgs'] as $img){?>
                  <img class="prevImg" src="<?=img_url($img)?>" width="23" height="26">
                  <?php }
                    if(count($product['imgs'])==0){
                        echo '&nbsp;';
                    }
                   ?>
                </div>
                <div class="prevOrg"></div>
              </div>
              <div class="goodsReturnTd" style="width:12%;">
                <?=$product['apply_status']?>
              </div>
            </td>
          </tr>
          <?php }?>
        </table>
      </div>
    </div>
  </div>
  <div class="trackInfo">
    <dl>
      <dt>
        状态跟踪：
      </dt>
      <?php if($apply_return_info['apply_status']==4){?>
      <dd>
        <div class="trackT"><?=substr($apply_return_product[0]['apply_time'],0,10)?></div>
        <div class="trackText">
            订单已拒收
        </div>
      </dd>
      <?php }else{?>
      <dd>
        <div class="trackT"><?=substr($apply_return_product[0]['apply_time'],0,10)?></div>
        <div class="trackText">
          用户申请退货,寄出包裹
        </div>
      </dd>
      <?php if(isset($cancel_time)){?>
      <dd>
        <div class="trackT"><?=substr($cancel_time,0,10)?></div>
      <?php 
	    if(empty($apply_return_info['cancel_admin_id'])){
	?>
        	<div class="trackText">用户取消申请</div>
	    <?php }
	    else{
	    ?>
        	<div class="trackText">客服取消申请 取消原因: <?=$apply_return_info['cancel_reason']?></div>
      	    <?php 
	    }
	?>
      </dd>
	<?php 
       }?>
      <?php foreach($returned_product as $product){?>
      <dd>
        <div class="trackT"><?=$product['is_ok_date']?></div>
        <div class="trackText">
          退货处理成功&nbsp;&nbsp;客服审核,其中款号
          <span class="red"><?=$product['product_sn']?>&nbsp;<?=$product['color_name']?>&nbsp;<?=$product['size_name']?></span>
          已退货处理成功.
        </div>
      </dd>
      <?php }?>
      <?php if(!empty($is_ok_date)){?>
      <dd>
        <div class="trackT"><?=$is_ok_date?></div>
        <div class="trackText">
          完成退款
        </div>
      </dd>
      <?php }
      }?>
    </dl>
  </div>
<div>
<script type="text/javascript">
// 查看页面预览图
var PrevImg=FS('.prevImg');
for (var l=0;l<PrevImg.length;l++) {
  PrevImg[l].onmouseover=function () {
    var PrevOrg=this.parentNode.parentNode.children[1];
    PrevOrg.innerHTML='<img src='+this.src+'>'
  };
  PrevImg[l].onmouseout=function () {
    var PrevOrg=this.parentNode.parentNode.children[1];
    PrevOrg.innerHTML=''
  };
}
</script>
    </div>
</div>
</div>
