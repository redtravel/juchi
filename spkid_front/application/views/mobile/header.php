<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="theme-color" content="#2196f3">
    <title><?php echo isset($title) ? $title : '演示站'?></title>
    <meta name="Keywords" content="<?php echo isset($keywords) ? $keywords : '';?>">
    <meta name="Description" content="<?php echo isset($description) ? $description : '';?>">
	
    <?php include APPPATH."views/mobile/common/header-css.php"; ?>
    <script>
    var static_host='<?php echo static_style_url('')?>';
    </script>

</head>
<body>
<div class="statusbar-overlay"></div>
<div class="panel-overlay"></div>
