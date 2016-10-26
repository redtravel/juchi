<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="theme-color" content="#2196f3">
    <meta property="qc:admins" content="1606616551551171676375" />
    <meta http-equiv="x-dns-prefetch-control" content="on" />
    <link rel="dns-prefetch" href="http://img.yueyawang.com" />
    <link rel="dns-prefetch" href="http://img1.yueyawang.com" /> 
    <link rel="dns-prefetch" href="http://static.yueyawang.com" />
    <title><?php echo isset($title) ? $title : SITE_NAME_MOBILE?></title>
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
