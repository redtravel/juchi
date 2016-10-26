<?php include(APPPATH . 'views/common/header.php'); ?>
<div class="main">
<div class="am-panel am-panel-default">
  <div class="am-panel-hd">
    <h3 class="am-panel-title">生成文件列表</h3>
  </div>
  <ul class="am-list am-list-static">

<?php foreach ( $gen_files AS $file ) :?>
<li>
<?php if ( $gen ) : ?>
    <span class="am-badge am-badge-success">已生成</span> 
<?php endif; ?>
<?php if ( in_array( $file, $cov_files ) ): ?>
<span class="am-badge am-badge-warning">已覆盖</span>
<?php endif; ?>
<?php echo $file;?></li>
<?php endforeach; ?>
  </ul>
  <div class="am-panel-footer">若为linux服务器,所生成的文件所属者与运行web服务器者相同，权限为644.</div>
</div>
</div>
<?php include_once(APPPATH . 'views/common/footer.php'); ?>
