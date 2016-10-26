<?php if(!isset($full_src)): ?>
<hr data-am-widget="divider" style="" class="am-divider am-divider-dashed" />
<footer data-am-widget="footer" class="am-footer am-footer-default">
  <div class="am-footer-miscs ">
    <p>当前版本：1.0</p><br/>
  </div>
</footer>


<div data-am-widget="gotop" class="am-gotop am-gotop-fixed">
  <a href="#top" title="回到顶部"><span class="am-gotop-title">回到顶部</span><i class="am-gotop-icon am-icon-chevron-up"></i>
  </a>
</div>
 <?php endif; ?>
	</body>

</html>
<script type="text/javascript">
$(document).on('change', 'input[type=file]', function(e){
    var file_size = this.files[0].size;
    var size = file_size/1024/1024;
    if (size > 1){
        alert("上传的文件大小不能超过1M");
    }
});
</script>
<script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?2b7ada9e30cf3ff4319ef3cdeb47391c";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
