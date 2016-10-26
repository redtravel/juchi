<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="theme-color" content="#2196f3">
    <title>演示站</title>
    <link rel="stylesheet" href="<?php echo static_style_url('mobile/css/framework7.material.css')?>">
    <link rel="stylesheet" href="<?php echo static_style_url('mobile/css/yyw-app.css')?>">
    

</head>
<div class="statusbar-overlay"></div>
<div class="views"> 
  <div class="view view-main">
    <div class="pages  navbar-through toolbar-through navbar-fixed">

      



      <div data-page="product_detail" class="page navbar-fixed">
        <div class="navbar">
                          <div class="navbar-inner">
                              <div class="right">
                                  <a href="list_view_2.html" class="link icon-only"><i class="icon icon-f7"></i></a>
                              </div>
                              <div class="center c_name">爱马仕</div>
                              <div class="right">
                                  <a href="list_view_2.html" class="link icon-only"><i class="icon icon-f7"></i></a>
                              </div>
                          </div>
                      </div>
        
        <div class="page-content">
          
         
          
        <?php echo preg_replace('#\s*(&nbsp)*\s*;#s', '', $p->product_desc_detail)?>

          
        
      </div>

     

     
          
        
      </div>
      </div>
    </div>
      
  </div>    

  
</div>
