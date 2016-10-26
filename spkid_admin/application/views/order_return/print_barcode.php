<?php include(APPPATH.'views/common/header.php'); ?>
<style type="text/css" media="all">
    .notice{display:none;}
    .main_title{display:none;}
    .printer_title{font-size:16px;font-weight:bold;}
    .body{background-color:#FFF;}
    table.dingdan_nr td{padding:5px;}
</style>
<script type="text/javascript">
    $(function(){window.print();});
</script>
    <body>
        <div align="center">
            <p style="font-size:12px; color: #000; height:14px; line-height:14px; font-family: 'Microsoft Yahei'; font-weight:bold; padding: 0; margin: 0"><?php print $product_name." ".$color_name." ".$size_name;?></p>
            <p style="font-size:12px; color: #000; height:14px; line-height:14px; font-family: 'Microsoft Yahei'; font-weight:bold; padding: 0; margin: 0"><?php print $provider_productcode;?></p>
         <img src="index/cls_barcode/<?php print urlencode($barcode);?>.html" /><br>
         <!--<img src="<?php print BARCODE_URL.$barcode; ?>" />-->
        </div>
    </body>
</html>
