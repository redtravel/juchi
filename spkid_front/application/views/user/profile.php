<?php include APPPATH."views/common/user_header.php"; ?>
<style>
    canvas {
        border: solid thin #ccc;
        cursor: pointer;
    }

    #canvasContainer {
        position: relative;
    }

    #picker {
        position: absolute;
        border: solid thin #ccc;
        cursor: move;
        overflow: hidden;
        z-index: 2;
    }

    #resize {
        width: 0;
        height: 0;
        border-bottom: 15px solid rgba(200,200,200,0.8);
        border-left: 15px solid transparent;
        right: 0;
        bottom: 0;
        position: absolute;
        cursor: se-resize;
        z-index: 3;
    }

    .a-upload {
        padding: 4px 10px;
        height: 20px;
        line-height: 20px;
        position: relative;
        cursor: pointer;
        color: #888;
        background: #fafafa;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
        display: inline-block;
        *display: inline;
        *zoom: 1
    }

    .a-upload  input {
        position: absolute;
        font-size: 100px;
        right: 0;
        top: 0;
        opacity: 0;
        filter: alpha(opacity=0);
        cursor: pointer;
    }

    .a-upload:hover {
        color: #444;
        background: #eee;
        border-color: #ccc;
        text-decoration: none;
    }

    
    select {
        border: solid 1px #c7c8cc;      
       
        padding-right: 14px;
        background: url("<?php echo static_style_url('pc/images/arrows.png');?>") no-repeat scroll right center transparent;
        font-size: 16px;
        width: 268px;
        outline: medium none;
        height: 26px;
        line-height: 26px; padding:1px 8px;
        box-shadow: 2px 3px 3px #e7e7e7 inset;
        
        
        height: 30px;
        font-size: 16px;
        vertical-align: middle;

    }
    select option p {
        padding:4px 4px;
    }
    select option:nth-child(even) { background-color:#f5f5f5;}

    select::-ms-expand { display: none; }
    .save_info {
        display:inline-block;
        background-color: rgb(0,174,249);
        color: white;
        width: 6em;
        border: none;
        margin-top: 40px;
        margin-left: 97px;
        text-align:center;
    }
	
	option {
    font-weight: normal;
    display: block;
    padding: 0px 0px 0px;
    white-space: pre;
    min-height: 1.2em;
}

    ul.choose-picture li img.selected {
        border:1px solid rgba(255, 0, 0, 0.5);
    }
    
    .pass-portrait-filebtn {
        -webkit-appearance: button;
        cursor: pointer;
    }
    

</style>
    <div class="personal-center-right">
                   
    <h1 class="order-details-bt">个人信息</h1>
         <div class="personal-rr">
              <ul class="personal-bt clearfix">
              <li data-value="0" class="current">基本资料</li>
              <li data-value="1">头像照片</li>
              </ul>
              
             <div class="personal-info-tab"> 
                  <ul class="personal-list">
                  <form class="user_info">
                      <li><label>昵　　称：</label><input name="user_name" type="text" class="mod-cus-input -mod-cus-red" value="<?php echo $user->user_name?>" ><!-- <em class="personal-empt">不能为空</em> --></li>
                      <li><label>姓　　名：</label><input name="real_name" type="text" class="mod-cus-input" value="<?php echo $user->real_name?>" ></li>
                      <li><label>职　　务：</label><input name="company_position" type="text" class="mod-cus-input" value="<?php echo $user->company_position?>" ></li>
                      <li><label>单位性质：</label><select name="company_type"><?php foreach ($values as $key => $value): ?><option value="<?php echo $value;?>"><p><?php echo $company_type[$key];?></p></option><?php endforeach;?></select></li>
                      <li><label>单位名称：</label><input name="company_name" type="text" class="mod-cus-input" value="<?php echo $user->company_name?>"></li>
                      <a class="save_info">保存</a>
                    </form>
                  </ul>
            </div>
            
            <div class="personal-info-tab" style="display:none;">
                 <div class="profile-pic clearfix">
                      <div class="profile-left">
                          <div class="touxiang-jl">
                                <p class="pass-portrain-commonp">方法一：选择本地照片，上传编辑自己的头像</p>
                                <div class="pass-portrait-openimg clearfix">
                                    <form id="uploadForm">
                                          <a href="javascript:;" class="a-upload">
                                            <input type="file" id="up_file" value="" class="pass-portrait-filebtn" name="user_advar">选择文件
                                          </a>
                                          <div id="canvasContainer">
                                              <canvas id="container"></canvas>
                                              <div id="picker">
                                                  <div id="resize"></div>
                                              </div>
                                          </div>
                                          
                                          <span class="pass-portrait-msg">支持jpg、jpeg、gif、png、bmp格式的图片</span>
                                          
                                    </form>
                                 </div>
                                <p class="pass-portrain-commonp">方法二：选择演示站为您推荐的头像</p>
                                <ul class="choose-picture clearfix">
                                   <li><img class="selected" src="<?php echo static_url('mobile/touxiang/img1.jpg')?>" ></li>
                                   <li><img src="<?php echo static_url('mobile/touxiang/img2.jpg')?>" ></li>
                                   <li><img src="<?php echo static_url('mobile/touxiang/img3.jpg')?>" ></li>
                                   <li><img src="<?php echo static_url('mobile/touxiang/img4.jpg')?>" ></li>
                                   <li><img src="<?php echo static_url('mobile/touxiang/img5.jpg')?>" ></li>
                                   <li><img src="<?php echo static_url('mobile/touxiang/img6.jpg')?>" ></li>
                                   <li><img src="<?php echo static_url('mobile/touxiang/img7.jpg')?>" ></li>
                                   <li><img src="<?php echo static_url('mobile/touxiang/img8.jpg')?>" ></li>

                               </ul>
                               <div class="pass-portrait-save "><input id="submit_upload" type="button" class="pass-portrait-savebtn" value="保存头像"></div> 
                           </div>
                   </div>

                   <div class="profile-right">
                         <p class="image-preview">头像预览</p>
                         <p class="big-head">

                          <canvas id="res1" class="big-head-img"></canvas>

                          <span>100*100</span>
                          </p>
                         <p class="big-head2">
                          <canvas id="res2" class="big-head-img2"></canvas>                         
                         <span>50*50</span>
                         </p>
                         <p class="big-head3">
                          <canvas id="res3" class="big-head-img3"></canvas>
                         <span>20*20</span>
                         </p>
                         <!--
                         <div class="user-picture">
                              <p>使用过头像</p>
                               <ul class="choose-picture  choose-picture2 clearfix">
                               <li><img src="images/profile-pictures.png"></li>
                               <li><img src="images/profile-pictures.png"></li>
                               <li><img src="images/profile-pictures.png"></li>
                               <li><img src="images/profile-pictures.png"></li>
                               <li><img src="images/profile-pictures.png"></li>
                               <li><img src="images/profile-pictures.png"></li>
                              </ul>
                         </div>
                         -->
                   </div>
              </div> 
         </div>
        </div>
    </div>
</div>
</div>
</div>


<script>
  var advar_type = 0; // 0表示用户没有进行任何选择,1表示用户选择上传，2表示用户选择用提供图片
  function fileChange(target){  
    advar_type = 1;
    //检测上传文件的类型 
    var imgName = document.all.up_file.value;
    var ext,idx;   
    if (imgName == ''){  
       document.all.submit_upload.disabled=true; 
       alert("请选择需要上传的文件!");  
       return; 
    } else {   
        idx = imgName.lastIndexOf(".");   
        if (idx != -1){   
            ext = imgName.substr(idx+1).toUpperCase();   
            ext = ext.toLowerCase( ); 
           // alert("ext="+ext);
            if (ext != 'jpg' && ext != 'png' && ext != 'jpeg' && ext != 'gif' && ext != 'bmp'){
              document.all.submit_upload.disabled=true;   
              alert("只能上传.jpg  .png  .jpeg  .gif .bmp类型的文件!"); 
              return;  
            }   
        } else {  
          document.all.submit_upload.disabled=true; 
          alert("只能上传.jpg  .png  .jpeg  .gif .bmp类型的文件!"); 
          return;
        }   
    }
    
    //检测上传文件的大小        
    var isIE = /msie/i.test(navigator.userAgent) && !window.opera;  
    var fileSize = 0;           
    if (isIE && !target.files){       
        var filePath = target.value;       
        var fileSystem = new ActiveXObject("Scripting.FileSystemObject");          
        var file = fileSystem.GetFile (filePath);       
        fileSize = file.Size;      
    } else {      
        fileSize = target.files[0].size;       
    }     

    var size = fileSize / 1024*1024;   

    if(size>(1024* 1024 * 5)){    
      document.all.submit_upload.disabled=true;
          alert("文件大小不能超过5MB");   
          return;
      }else{
      document.all.submit_upload.disabled=false;
    }
  }

 $(".personal-bt li").bind("click", function () {

        $(".personal-bt li").removeClass("current");
        $(this).addClass("current");
        var i = $(this).attr("data-value");
        $(".personal-info-tab").hide();
        $(".personal-info-tab:eq(" + i + ")").show();
    });

 $(function(){
    var is_ok = true;

    $('.user_info input').click(function(){
        if (!is_ok) {
            $(this).removeClass('mod-cus-red');
            $(this).siblings('.personal-empt').remove();    
            is_ok = true;
        };        
    });

    $('.save_info').click(function(){
        $('.mod-cus-red').removeClass('mod-cus-red');
        $('.personal-empt').remove();
        $('.user_info input').each(function(){
            if (!$(this).val()) {
                is_ok = false;
                $(this).addClass('mod-cus-red');
                $(this).after('<em class="personal-empt">不能为空</em>');
            };
        });
        
        if (is_ok) {
            $.ajax({
                type:'POST',
                url:'/user/edit',
                data:$('.user_info').serialize(),
                dataType:'json',
                success:function(data, textStatus){
                    if (data.msg) {
                        alert(data.msg);
                    }
                },
                error:function(xhr, textStatus, errorThrown) {

                },
                complete:function(){
                    is_ok = true;
                }

            });
        } else {
            return false;
        }
    });

    function update_advar_preview(id, img_src, resolution) {
      var c=document.getElementById(id);
      var ctx=c.getContext("2d");
      ctx.clearRect(0, 0, resolution, resolution);
      var img=new Image();
      img.onload = function(){
        ctx.drawImage(img, 0, 0, resolution, resolution);
      };
      img.src = img_src;
    }

    $('.choose-picture li img').click(function(){
        advar_type = 2;
        $('.choose-picture li img.selected').removeClass('selected');
        $(this).addClass('selected');
        update_advar_preview('res1', $(this).attr('src'), 100);
        update_advar_preview('res2', $(this).attr('src'), 50);
        update_advar_preview('res3', $(this).attr('src'), 30);       
        
    });

    $('.pass-portrait-savebtn').click(function(){
      if (advar_type == 2) {
        //保存头像
        var advar = $('.choose-picture li img.selected').attr('src');
        advar_ = advar.split('/').pop();
        $.post('/user/save_advar', {advar:advar_}, function(data){
          alert('更新成功');        
        });
        return;  
      } else if(advar_type == 1) {
        
        var fileServer = '/user/profile_upload';
        var imgData = $("#res1")[0].toDataURL("png");
        imgData = imgData.replace(/^data:image\/(png|jpg);base64,/, "");
        

        var blobBin = atob(imgData);
        var array = [];
        for (var i = 0; i < blobBin.length; i++) {
            array.push(blobBin.charCodeAt(i));
        }
        var blob = new Blob([new Uint8Array(array)], { type: 'image/png' });
        var file = new File([blob], "user_advar.png", { type: 'image/png' });
        var formdata = new FormData();
        formdata.append("user_advar", file);
        $.ajax({
            type: 'POST',
            url: fileServer,
            data: formdata,
            processData: false,
            contentType: false,
            dataType:'json',
            success: function (msg) {
                console.log(msg);
                alert('更新成功');
            }
        });
        return;
      } else {
        alert('未选择图片');
        return;
      }
      

        
    });
 });
</script>

<script>
    $(function () {
        var canvas = document.getElementById("container"),
            context = canvas.getContext("2d"),
            //文件服务器地址
            fileServer = null,
            //适配环境，随时修改事件名称
            eventName = { down: "mousedown", move: "mousemove", up: "mouseup", click: "click" };
        //////////canvas尺寸配置
        var canvasConfig = {
            //容器canvas尺寸
            width: 400,
            height: 300,
            //原图放大/缩小
            zoom: 1,
            //图片对象
            img: null,
            //图片完整显示在canvas容器内的尺寸
            size: null,
            //图片绘制偏移，为了原图不移出框外，这个只能是负值or 0
            offset: { x: 0, y: 0 },
            //当前应用的滤镜
            filter: null
        }
        canvas.width = canvasConfig.width;
        canvas.height = canvasConfig.height;
        ///////////设置选择工具配置
        var config = {
            //图片选择框当前大小、最大大小、最小大小
            pickerSize: 100,
            minSize: 50,
            maxSize: 200,
            x: canvas.width / 2 - 100 / 2,
            y: canvas.height / 2 - 100 / 2
        }
        /////////////结果canvas配置
        var resCanvas = [$("#res1")[0].getContext("2d"), $("#res2")[0].getContext("2d"), $("#res3")[0].getContext("2d")];
        //结果canvas尺寸配置
        var resSize = [100, 50, 32]
        resSize.forEach(function (size, i) {
            $("#res" + (i + 1))[0].width = size;
            $("#res" + (i + 1))[0].height = size;
        });
        //////// 滤镜配置
        var filters = [];
        filters.push({
            name: "灰度", func: function (pixelData) {
                //r、g、b、a
                //灰度滤镜公式： gray=r*0.3+g*0.59+b*0.11
                var gray;
                for (var i = 0; i < canvasConfig.width * canvasConfig.height; i++) {
                    gray = pixelData[4 * i + 0] * 0.3 + pixelData[4 * i + 1] * 0.59 + pixelData[4 * i + 2] * 0.11;
                    pixelData[4 * i + 0] = gray;
                    pixelData[4 * i + 1] = gray;
                    pixelData[4 * i + 2] = gray;
                }
            }
        });
        filters.push({
            name: "黑白", func: function (pixelData) {
                //r、g、b、a
                //黑白滤镜公式： 0 or 255
                var gray;
                for (var i = 0; i < canvasConfig.width * canvasConfig.height; i++) {
                    gray = pixelData[4 * i + 0] * 0.3 + pixelData[4 * i + 1] * 0.59 + pixelData[4 * i + 2] * 0.11;
                    if (gray > 255 / 2) {
                        gray = 255;
                    }
                    else {
                        gray = 0;
                    }
                    pixelData[4 * i + 0] = gray;
                    pixelData[4 * i + 1] = gray;
                    pixelData[4 * i + 2] = gray;
                }
            }
        });
        filters.push({
            name: "反色", func: function (pixelData) {
                for (var i = 0; i < canvasConfig.width * canvasConfig.height; i++) {
                    pixelData[i * 4 + 0] = 255 - pixelData[i * 4 + 0];
                    pixelData[i * 4 + 1] = 255 - pixelData[i * 4 + 1];
                    pixelData[i * 4 + 2] = 255 - pixelData[i * 4 + 2];
                }
            }
        });
        filters.push({ name: "无", func: null });
        // 添加滤镜按钮
        filters.forEach(function (filter) {
            var button = $("<button>" + filter.name + "</button>");
            button.on(eventName.click, function () {
                canvasConfig.filter = filter.func;
                //重绘
                draw(context, canvasConfig.img, canvasConfig.size);
            })
            $("#filters").append(button);
        });
        //下载生成的图片(只下载第一张)
        $("#download").on(eventName.click, function () {

            //将mime-type改为image/octet-stream，强制让浏览器直接download
            var _fixType = function (type) {
                type = type.toLowerCase().replace(/jpg/i, 'jpeg');
                var r = type.match(/png|jpeg|bmp|gif/)[0];
                return 'image/' + r;
            };
            var saveFile = function (data, filename) {
                var save_link = document.createElementNS('http://www.w3.org/1999/xhtml', 'a');
                save_link.href = data;
                save_link.download = filename;
                var event = document.createEvent('MouseEvents');
                event.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                save_link.dispatchEvent(event);
            };
            var imgData = $("#res1")[0].toDataURL("png");
            imgData = imgData.replace(_fixType("png"), 'image/octet-stream');//base64
            saveFile(imgData, "头像created on" + new Date().getTime() + "." + "png");
        });
        //上传图片
        $("#upload").on(eventName.click, function () {
            var imgData = $("#res1")[0].toDataURL("png");
            imgData = imgData.replace(/^data:image\/(png|jpg);base64,/, "");
            if (!fileServer) {
                alert("请配置文件服务器地址");
                return;
            }

            var blobBin = atob(imgData);
            var array = [];
            for (var i = 0; i < blobBin.length; i++) {
                array.push(blobBin.charCodeAt(i));
            }
            var blob = new Blob([new Uint8Array(array)], { type: 'image/png' });
            var file = new File([blob], "userlogo.png", { type: 'image/png' });
            var formdata = new FormData();
            formdata.append("userlogo", file);
            $.ajax({
                type: 'POST',
                url: fileServer,
                data: formdata,
                processData: false,
                contentType: false,
                success: function (msg) {
                    $("#uploadres").text(JSON.stringify(msg));
                }
            });
        });
        //绑定选择图片事件
        $("#up_file").change(function () {
            advar_type = 1;
            var file = this.files[0],
                URL = (window.webkitURL || window.URL),
                url = URL.createObjectURL(file),
                img = new Image();
            img.src = url;
            img.onload = function () {
                canvasConfig.img = img;
                canvasConfig.size = getFixedSize(img, canvas);
                draw(context, img, canvasConfig.size);
                setPicker();
            }

        });
        //移动选择框
        //绑定鼠标在选择工具上按下的事件
        $("#picker").on(eventName.down, function (e) {
            e.stopPropagation();
            var start = { x: e.clientX, y: e.clientY, initX: config.x, initY: config.y };
            $("#canvasContainer").on(eventName.move, function (e) {
                // 将x、y限制在框内
                config.x = Math.min(Math.max(start.initX + e.clientX - start.x, 0), canvasConfig.width - config.pickerSize);
                config.y = Math.min(Math.max(start.initY + e.clientY - start.y, 0), canvasConfig.height - config.pickerSize);
                setPicker();
            })
        });
        //原图移动事件
        $("#container").on(eventName.down, function (e) {
            e.stopPropagation();
            var start = { x: e.clientX, y: e.clientY, initX: canvasConfig.offset.x, initY: canvasConfig.offset.y };
            var size = canvasConfig.size;
            $("#canvasContainer").on(eventName.move, function (e) {
                // 将x、y限制在框内
                // 坐标<0  当图片大于容器  坐标>容器-图片   否则不能移动
                canvasConfig.offset.x = Math.max(Math.min(start.initX + e.clientX - start.x, 0), Math.min(canvasConfig.width - size.width * canvasConfig.zoom, 0));
                canvasConfig.offset.y = Math.max(Math.min(start.initY + e.clientY - start.y, 0), Math.min(canvasConfig.height - size.height * canvasConfig.zoom, 0));
                //重绘蒙版
                draw(context, canvasConfig.img, canvasConfig.size);
            })
        });
        //改变选择框大小事件
        $("#resize").on(eventName.down, function (e) {
            e.stopPropagation();
            var start = { x: e.clientX, init: config.pickerSize };
            $("#canvasContainer").on(eventName.move, function (e) {
                config.pickerSize = Math.min(Math.max(start.init + e.clientX - start.x, config.minSize), config.maxSize);
                $("#picker").css({ width: config.pickerSize, height: config.pickerSize });
                draw(context, canvasConfig.img, canvasConfig.size);
            })
        });
        $(document).on(eventName.up, function (e) {
            $("#canvasContainer").unbind(eventName.move);
        })
        //原图放大、缩小
        $("#bigger").on(eventName.click, function () {
            canvasConfig.zoom = Math.min(3, canvasConfig.zoom + 0.1);
            //重绘蒙版
            draw(context, canvasConfig.img, canvasConfig.size);
        })
        $("#smaller").on(eventName.click, function () {
            canvasConfig.zoom = Math.max(0.4, canvasConfig.zoom - 0.1);
            //重绘蒙版
            draw(context, canvasConfig.img, canvasConfig.size);
        })

        // 定位选择工具
        function setPicker() {
            $("#picker").css({
                width: config.pickerSize + "px", height: config.pickerSize + "px",
                top: config.y, left: config.x
            });
            //重绘蒙版
            draw(context, canvasConfig.img, canvasConfig.size);
        }
        //绘制canvas中的图片和蒙版
        function draw(context, img, size) {
            var pickerSize = config.pickerSize,
                zoom = canvasConfig.zoom,
                offset = canvasConfig.offset;
            context.clearRect(0, 0, canvas.width, canvas.height);
            context.drawImage(img, 0, 0, img.width, img.height, offset.x, offset.y, size.width * zoom, size.height * zoom);
            //绘制挖洞后的蒙版
            context.save();
            context.beginPath();
            pathRect(context, config.x, config.y, pickerSize, pickerSize);
            context.rect(0, 0, canvas.width, canvas.height);
            context.closePath();
            context.fillStyle = "rgba(255,255,255,0.9)";
            context.fill();
            context.restore();
            //绘制结果
            var imageData = context.getImageData(config.x, config.y, pickerSize, pickerSize)
            resCanvas.forEach(function (resContext, i) {
                resContext.clearRect(0, 0, resSize[i], resSize[i]);
                resContext.drawImage(canvas, config.x, config.y, pickerSize, pickerSize, 0, 0, resSize[i], resSize[i]);
                //添加滤镜效果
                if (canvasConfig.filter) {
                    var imageData = resContext.getImageData(0, 0, resSize[i], resSize[i]);
                    var temp = resContext.getImageData(0, 0, resSize[i], resSize[i]);// 有的滤镜实现需要temp数据
                    canvasConfig.filter(imageData.data, temp);
                    resContext.putImageData(imageData, 0, 0, 0, 0, resSize[i], resSize[i]);
                }
            });
        }
        //逆时针用路径自己来绘制矩形，这样可以控制方向，以便挖洞
        // 起点x，起点y，宽度，高度
        function pathRect(context, x, y, width, height) {
            context.moveTo(x, y);
            context.lineTo(x, y + height);
            context.lineTo(x + width, y + height);
            context.lineTo(x + width, y);
            context.lineTo(x, y);
        }
        // 根据图片和canvas的尺寸，确定图片显示在canvas中的尺寸
        function getFixedSize(img, canvas) {
            var cancasRate = canvas.width / canvas.height,
                imgRate = img.width / img.height, width = img.width, height = img.height;
            if (cancasRate >= imgRate && img.height > canvas.height) {
                height = canvas.height;
                width = imgRate * height;
            }
            else if (cancasRate < imgRate && img.width > canvas.width) {
                width = canvas.width;
                height = width / imgRate;
            }
            return { width: width, height: height };
        }
    });
</script>

<?php include APPPATH . "views/common/footer.php";?>