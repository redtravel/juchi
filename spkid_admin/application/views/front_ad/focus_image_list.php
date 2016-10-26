<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
    <script src="public/js/My97DatePicker/WdatePicker.js"></script>
    <style type="text/css">
        .am-figure-zoomable::after{
            content: none;
        }
        .am-figure-default{
            margin:0; 
        }
        .am-figure-default img{
            border: none;
            margin: none;
        }
        .am-icon-file-image-o{
            margin-top: 10px;
        }
    </style>
	<div class="main">
		<div class="main_title">
		<span class="l">焦点图管理</span>
		<?php if (check_perm('focus_image_add')): ?>
		<span class="r">
			<a href="front_focus_image/add/<?php echo $search_type?>" class="add">添加</a>
		</span>
		<?php endif; ?>
		</div>

		<div class="blank5"></div>
		<div class="search_row">
<ul class="am-nav am-nav-pills">
            <?foreach($focus_type as $key=>$val):?>
     <li class="<?php if ($key==$cur_ft)echo 'am-active';?>">            <a href="front_focus_image/index/<?=$key?>"><?=$val?></a></li>
                &nbsp;&nbsp;&nbsp;&nbsp;
            <?endforeach?>
</ul>
		</div>
		<div class="blank5"></div>

       

		<div id="listDiv">
<?php endif; ?>
            <form method="post" action="front_focus_image/update_focus">
			<table id="dataTable" class="dataTable am-panel-group" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="7" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th>名称</th>
				  <th>播放顺序</th>
                    <th>缩略图</th>
			      <th>活动图片</th>
				  <th>开始时间</th>
				  <th>结束时间</th>
                  <th>操作</th>
				</tr>
				<?php foreach($list as $row): ?>
                <?php if($row->end_time >= date('Y-m-d H:i:s',time())){ ?>
			    <tr class="row">
                    <td>
                        <?=$row->focus_name?>
                        <a href="<?=$row->focus_url?>">查看</a>
                        <input type="hidden" name="focus_id[]" value="<?=$row->id?>">
                    </td>
					<td>
                        <input type="text" name="focus_order[]" value="<?=$row->focus_order?>" style="width:50px;">
                    </td>
                    <td>
                        <figure data-am-widget="figure" class="am am-figure am-figure-default "   data-am-figure="{  pureview: 'true' }">
                            <i class="am-icon-file-image-o"></i>
                            <img data-rel="<?=img_url($row->small_img)?>"/>
                        </figure>
                    </td>
					<td>
                        <figure data-am-widget="figure" class="am am-figure am-figure-default "   data-am-figure="{  pureview: 'true' }">
                            <i class="am-icon-file-image-o"></i>
                            <img data-rel="<?=img_url($row->focus_img)?>"/>
                        </figure>
                    </td>
					<td>
                        <input type="text" value="<?=$row->start_time?>" name="focus_start_time[]" id="fs_time_<?=$row->id?>" 
                            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'fe_time_<?=$row->id?>\')}'})">
                    </td>
					<td>
                        <input type="text" value="<?=$row->end_time?>" name="focus_end_time[]" id="fe_time_<?=$row->id?>" 
                            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'fs_time_<?=$row->id?>\')}'})">
                    </td>
                    <td>
                        <a class="edit" href="/front_focus_image/edit/<?=$row->id?>" title="编辑"></a>
                        <a href="/front_focus_image/delete/<?=$row->id?>">删除</a>
                    </td>
			    </tr>
                <?php } ?>
				<?php endforeach; ?>
                
                <tr>
                    <td colspan="6">
                    </td>
                </tr>
			    <tr>
					<td colspan="6" class="bottomTd"></td>
				</tr>

                <tr>
                    <td colspan="6"><a href="javascript:void(0);" onclick="return v_click_more();">点击查看已过期</a></td>
                </tr>
                    
                <?php foreach($list as $row): ?>
                <?php if($row->end_time < date('Y-m-d H:i:s',time())){ ?>
                <tr class="row v-focus-block" style="display:none;">
                    <td>
                        <?=$row->focus_name?>
                        <a href="<?=$row->focus_url?>">查看</a>
                        <input type="hidden" name="focus_id[]" value="<?=$row->id?>">
                    </td>
                    <td>
                        <input type="text" name="focus_order[]" value="<?=$row->focus_order?>" style="width:50px;">
                    </td>
                    <td>
                        <figure data-am-widget="figure" class="am am-figure am-figure-default "   data-am-figure="{  pureview: 'true' }">
                            <i class="am-icon-file-image-o"></i>
                            <img data-rel="<?=img_url($row->small_img)?>"/>
                        </figure>
                    </td>
					<td>
                        <figure data-am-widget="figure" class="am am-figure am-figure-default "   data-am-figure="{  pureview: 'true' }">
                            <i class="am-icon-file-image-o"></i>
                            <img data-rel="<?=img_url($row->focus_img)?>"/>
                        </figure>
                    </td>
                    <td>
                        <input type="text" value="<?=$row->start_time?>" name="focus_start_time[]" id="fs_time_<?=$row->id?>" 
                            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'fe_time_<?=$row->id?>\')}'})">
                    </td>
                    <td>
                        <input type="text" value="<?=$row->end_time?>" name="focus_end_time[]" id="fe_time_<?=$row->id?>" 
                            onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'fs_time_<?=$row->id?>\')}'})">
                    </td>
                    <td>
                        <a class="edit" href="/front_focus_image/edit/<?=$row->id?>" title="编辑"></a>
                        <a href="/front_focus_image/delete/<?=$row->id?>">删除</a>
                    </td>
                </tr>
                <?php } ?>
                <?php endforeach; ?>
			</table>
            
		    <?php if (check_perm('focus_image_edit')): ?>
            <div class="am-form-group">
            <input type="submit" id="mysubmit" class="am-btn am-btn-primary" value="更新数据" onclick="return do_submit()">
            </div>
            <?endif?>
			<div class="blank5"></div>
            <?php if (1==$search_type):?>
            
            <div class="am-form-group">
            <label class="am-radio-inline"><input type="radio" name="direction" value="h" checked>水平</label>
            <label class="am-radio-inline"><input type="radio" name="direction" value="v">垂直</label>         
            </div>
            <div class="am-form-group">
            <input type="text" id="btn_name" name="btn_name" placeholder="按钮名称">
            <input type="text" id="url" name="url" placeholder="跳转路径">&nbsp;&nbsp;注册页面(/user/signin/register-step1)
            </div>
            
            <div class="am-form-group am-form-icon">
<i class="am-icon-calendar"></i>
            <input type="text" id="end_time" class="am-form-field" placeholder="截止日期" data-am-datepicker readonly/>
            </div>
            <?endif?>
            <div class="am-form-group">
            <a href="#" id="update_focus_image_html" class="am-btn am-btn-primary">生成静态页</a>
            </div>           
            </div>
            </form>
            <div class="am-modal am-modal-alert" tabindex="-1" id="yyw-alert">
  <div class="am-modal-dialog">
    <div class="am-modal-bd">
    </div>
    <div class="am-modal-footer">
      <span class="am-modal-btn">确定</span>
    </div>
  </div>
</div>
		</div>
	</div>
    <script>
        $(function(){
            $('#update_focus_image_html').click(function(e){
                e.preventDefault();
                var url='front_focus_image/update_focus_image_html/<?php echo $search_type ?>';
                var btn_name=$('#btn_name');
                if (btn_name){
                    btn_value=btn_name.val();
                    var direction=$('input[name="direction"]:checked').val();
                    var expire_time=$('#end_time').val();
                    var msg = '', jump_url=$('#url').val();
                    if ('' == btn_value){
                        msg = '按钮名称必填!';
                    } else if('' == expire_time){
                        msg = '截止日期必填!';
                    } else if('' == jump_url){
                        msg = '跳转路径必填!';
                    }
                    if ('' != msg){
                        $('#yyw-alert').find('.am-modal-bd').html(msg);
                        $('#yyw-alert').modal();
                        return;
                    }
                    
                    $.ajax({
                        method:'POST',
                        url:url,
                        //dataType:'json',
                        data:{btn_name:btn_value,direction:direction,expire_time:expire_time,jump_url:jump_url},
                        success:function(data){
                            $('#yyw-alert').find('.am-modal-bd').html(data);
                            $('#yyw-alert').modal();
                        }
                    })
                }
                else {
                    $.get(url,null,function(data){
                            $('#yyw-alert').find('.am-modal-bd').html(data);
                            $('#yyw-alert').modal();
                        })
                }
            });
        });
        function do_submit(){
            var orders=document.getElementsByName('focus_order[]');
            if(orders.length>0){
                for(var i=0;i<orders.length;i++){
                    if(orders[i].value==""||isNaN(orders[i].value)){
                        alert('请输入正确的排序');
                        orders[i].focus();
                        return false;
                    }
                }
            }
            else{
                return false;
            }
            var start_time=document.getElementsByName('focus_start_time[]');
            for(var i=0;i<start_time.length;i++){
                if(start_time[i].value==""){
                    alert('请输入正确的开始时间');
                    start_time[i].focus();
                    return false;
                }
            }
            var end_time=document.getElementsByName('focus_end_time[]');
            for(var i=0;i<end_time.length;i++){
                if(end_time[i].value==""){
                    alert('请输入正确的结束时间');
                    end_time[i].focus();
                    return false;
                }
            }
            return true;
        }

        function v_click_more(){
            $(".v-focus-block").toggle();
        }
    </script>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
