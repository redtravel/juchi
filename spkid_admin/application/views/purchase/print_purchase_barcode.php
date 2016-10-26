<?php include(APPPATH.'views/common/header.php'); ?>
<style type="text/css" media="all">
	.notice{display:none;}
	.main_title{display:none;}
	.printer_title{display:none;}
</style>
<script type="text/javascript">
	$(function(){window.print();});
</script>
    
    <!-- 火狐浏览器模版 -->
    <?php if($browser=='Firefox'){ ?>
    <div class="main" align="center">
    <?php $i=0;foreach($list as $sub):$i+=1; ?>
    <?php for($i=1;$i<=$sub['sub_count'];$i++){ ?>
        <div style="width:275px; height:150px; padding-top:10px; margin-top:5px;margin-bottom:4px; text-align: center; border:0px solid #000;">
            <p style="font-size:16px; color: #000; height:22px; line-height:22px; font-family: 'Microsoft Yahei'; padding: 0; margin: 0; font-weight:bold;"><?php print $sub['index'].' '.$sub['product_name'].' '.$sub['color_name'].' '.$sub['size_name'];?></p>
            <p style="font-size:16px; color: #000; height:22px; line-height:22px; font-family: 'Microsoft Yahei'; padding: 0; margin: 0; font-weight:bold;"><?php print $sub['provider_productcode'];?></p>
            <div style="margin:5px auto;"><img src="index/cls_barcode/<?php print urlencode($sub['provider_barcode']);?>.html" /></div>
        crosoft Yaheii</div>
    <?php }?>
    <?php endforeach;?>
    </div>
    
    <!-- 谷歌浏览器模版 -->
    <?php }elseif($browser=='Chrome'){ ?>
    <div class="main" align="center">
    <?php $i=0;foreach($list as $sub):$i+=1; ?>
    <?php for($i=1;$i<=$sub['sub_count'];$i++){ ?>
        <p style="page-break-before: always;font-size:12px;font-weight:bold; color: #000; height:14px; line-height:14px; font-family: 'Microsoft Yahei'; padding: 0; margin: 0;font-weight:bold;"><?php print $sub['index'].' '.$sub['product_name'].' '.$sub['color_name'].' '.$sub['size_name'];?></p>
        <p style="font-size:12px;font-weight:bold; color: #000; height:14px; line-height:14px; font-family: 'Microsoft Yahei'; padding: 0; margin: 0;font-weight:bold;"><?php print $sub['provider_productcode'];?></p>
        <p><img src="index/cls_barcode/<?php print urlencode($sub['provider_barcode']);?>.html" /></p>
    <?php }?>
    <?php endforeach;?>
    </div>
    
    <!-- IE浏览器模版 -->
    <?php }elseif($browser=='IE'){ ?>
    <div class="main" align="center">
    <?php $i=0;foreach($list as $sub):$i+=1; ?>
    <?php for($i=1;$i<=$sub['sub_count'];$i++){ ?>
        <p><?php print $sub['index'].' '.$sub['product_name'].' '.$sub['color_name'].' '.$sub['size_name'];?></p>
        <p><?php print $sub['provider_productcode'];?></p>
        <p><img src="index/cls_barcode/<?php print urlencode($sub['provider_barcode']);?>.html" /></p>
    <?php }?>
    <?php endforeach;?>
    </div>
    <?php }?>

<?php include_once(APPPATH.'views/common/footer.php'); ?>
