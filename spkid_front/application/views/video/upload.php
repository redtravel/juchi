<?php include APPPATH . 'views/common/header.php'?>
<script src="<?php echo static_style_url('pc/js/jquery-1.11.3.js?v=version')?>" type="text/javascript"></script>
<div class="wrap-mian wrap-min2 video-upload">
     <div class="play-con">
          <p class="shipin-titi">视频&gt;发布视频</p>
          <form class="video-upload-box" id="uploadForm">
                <h3>上传视频</h3>
                <ul>
                <li>
                <h4>标题：<span class="tip">请输入2~16位有效字符</span></h4>
                <input type="text" name="title" class="input-check" id="upTitle" placeholder="2-16位有效字符">
                </li>
                <li>
                <h4>请输入视频链接：<span class="tip">请输入内容</span></h4>
                <textarea name="content" class="describtion input-check" placeholder="暂时只支持腾讯视频哦"></textarea>
                </li>
                <li>
                <h4>请输入描述文字：<span class="search-tip"></span></h4>
                <textarea name="desc" class="describtion input-check" placeholder="限60字以内，请勿与标题相同"></textarea>
                </li>
                <li>
                
                <div class="video-cover clearfix">
                     <!--<div class="video-fengmian">
<a href="javascript:;"><b>+</b><span>点击上传视频封面</span></a>
</div>--><label>上传视频封面</label><input type="file" name="cover">
                     <div class="upload-tip">封面的颜值决定您的点击率哦<br/>支持格式：jpg、gif、png;  文件尺寸10m以内</div>
                    
                </div>                
                </li>
                
                <a class="submit-button" href="#">提交</a>
                </ul>
          
          </form>
     </div>
<div class="play-con" style="display:none">
          <p class="shipin-titi">视频&gt;发布视频</p>
          <div class="video-upload-box2">
                <h3>上传视频</h3>
                <span class="zhif-hu video-fbcg">发布成功!</span>
                <div class="video-back">点击返回<a href="/video.html">视频列表</a></div>
          </div>
       
</div>
</div>
<script>
$(function(){

    $('.submit-button').click(function(e){
        e.preventDefault();
        var title = $.trim($('input[name=title]').val());
        var content = $.trim($('textarea[name=content]').val());
        if (title.length > 16 || title.length < 2){
            $('input[name=title]').prev().children('.tip').show();
            return false;
        } else if ('' == content){
            $('textarea[name=content]').prev().children('.tip').show();
            return false;
        }
        var data = new FormData($('#uploadForm')[0]);
        /*$.each($('input[name=cover]')[0].files, function(i, file) {
            data.append('cover'+i, file);
        });*/
        $.ajax({url:'/video/proc_upload', data:data, cache:false, method:'POST', dataType:'json', contentType:false, processData: false, success:function(data){
            if (data.uploaded){
                $('.play-con').first().hide();
                $('.play-con').last().show();
            }
        }
        })
        //$('#cover').trigger('click');
    })
})

</script>
<?php include APPPATH . 'views/common/footer.php'?>
