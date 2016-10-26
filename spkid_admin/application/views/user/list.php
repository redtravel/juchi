<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/lhgdialog/lhgdialog.js"></script>

<script type="text/javascript">
    $(function(){
    $('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
    $('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
    $('.change_discount').click(function(e){
        e.preventDefault();
        var uid = $(this).attr('href').substring(1);
        $.dialog({ id:'panel',height:150,width:300,maxBtn:false, lock:true, title:'修改会员折扣',iconTitle:false,cover:true,content: 'url:user/ajax_get_user_discount/'+uid,cache:false,ok:function(){
            var value = this.content.document.getElementById('discount').value;
            $.post('user/ajax_change_discount', {uid:uid, discount:value}, function(msg){
                if (1 == msg){
                    $.dialog({content:'修改成功!'});
                } else if(0 == msg){
                    $.dialog({content:'修改失败!'});
                } else{
                    $.dialog.alert(msg);
                }
                
            })
        }
        });
    });
    });
    //<![CDATA[
    listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
    listTable.filter.page = '<?php echo $filter['page']; ?>';
    listTable.url = 'user/index';
    function search(){
		listTable.filter['user_type'] = $.trim($('select[name=user_type]').val());
        listTable.filter['mobile'] = $.trim($('input[name=mobile]').val());
        listTable.filter['user_name'] = $.trim($('#user_name').val());
	    listTable.filter['email'] = $.trim($('input[name=email]').val());
		listTable.filter['is_use'] = $.trim($('select[name=is_use]').val());
		listTable.filter['email_validated'] = $.trim($('select[name=email_validated]').val());
		listTable.filter['mobile_checked'] = $.trim($('select[name=mobile_checked]').val());
		listTable.filter['start_time'] = $.trim($('input[name=start_time]').val());
        listTable.filter['end_time'] = $.trim($('input[name=end_time]').val());
        listTable.loadList();
    }
    //]]>
</script>
	<div class="main">
		<div class="main_title"><span class="l">会员管理 >> 会员列表</span><span class="r"><a href="user/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
	  <div class="search_row">
			<form name="search" action="javascript:search(); ">
			  手机号：
			    <input name="mobile" type="text" id="mobile" size="15" />
            用户名：
			    <input name="user_name" type="text" id="user_name" size="15" />
              用户类型：
<select name="user_type" id="user_type">
	    <option value="">--请选择--</option>
			    <option value="2">普通会员</option>
			    <option value="3">代销商</option>
			  </select>
Email：
<input name="email" type="text" id="email" size="15" />

Email是否验证：
<select name="email_validated" id="email_validated">
	    <option value="" selected="selected">--请选择--</option>
			    <option value="1">否</option>
			    <option value="2">是</option>
			  </select>
              手机验证：
<select name="mobile_checked" id="mobile_checked">
	    <option value="">--请选择--</option>
			    <option value="1">否</option>
			    <option value="2">是</option>
			  </select>
              是否停用：
<select name="is_use" id="is_use">
	    <option value="">--请选择--</option>
			    <option value="1">否</option>
			    <option value="2">是</option>
			  </select>			  创建时间：
			  <input name="start_time" type="text" id="start_time" size="15" />
			  <input name="end_time" type="text" id="end_time" size="15" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1170"  cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="15" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="42">ID</th>
				  <th width="77">用户等级</th>
			      <th width="76">用户名</th>
			      <th width="57">类型</th>
				  <th width="147">email</th>
				  <th width="132">手机</th>
			      <th width="74">账户金额</th>
				  <th width="97">累计销费</th>
				  <th width="83">消费积分</th>
				  <th width="140">创建时间</th>
				  <th width="37">可用</th>
				  <th width="117">操作</th>
				</tr>
		<?php foreach($list as $item):?>	
	      <tr class="row">
			    	<td><?php echo $item->user_id?></td>
			    	<td><?php echo $item->rank_name?></td>
					<td><?php echo $item->user_name?></td>
					<td><?php echo $item->user_type == 0 ? '普通会员' : '代销商';?></td>
			    	<!--<td><img title="<?php /*echo $item->email_validated == 0 ? '未验证' : '已验证'*/?>" src="public/images/<?php /*echo $item->email_validated == 0 ? 'no' : 'yes'*/?>.gif" /> <?php /*echo $item->email*/?></td>  用样式显示byRock-->
                    <td><span title="<?php echo $item->email_validated == 0 ? '未验证' : '已验证'?>" class="<?php echo $item->email_validated == 0 ? 'noForGif' : 'yesForGif'?>"></span> <?php echo $item->email?></td>
					<!--<td><img title="<?php /*echo $item->mobile_checked == 0 ? '未验证' : '已验证'*/?>" src="public/images/<?php /*echo $item->mobile_checked == 0 ? 'no' : 'yes'*/?>.gif" /> <?php /*echo $item->mobile*/?></td> 用样式显示 ByRock-->
                    <td><span title="<?php echo $item->mobile_checked == 0 ? '未验证' : '已验证'?>" class="<?php echo $item->mobile_checked == 0 ? 'noForGif' : 'yesForGif'?>" ></span> <?php echo $item->mobile?></td>
					<td><?php echo $item->user_money?></td>
					<td><?php echo $item->paid_money?></td>
					<td><?php echo $item->pay_points?></td>
					<td><?php echo $item->create_date?></td>
					<!--<td><img src="public/images/<?php /*echo $item->is_use == 1 ? 'no' : 'yes'*/?>.gif" /></td> 用样式显示 By Rock-->
                    <td><span class="<?php echo $item->is_use == 1 ? 'noForGif' : 'yesForGif'?>"></span></td>
					<td>
                    
                    <a class="edit" href="user/edit/<?php echo $item->user_id?>" title="编辑"></a>
                    <?php if($perms['user_edit'] == 1):?>  
                    <a  href="user/<?php echo $item->is_use == 1 ? 'able' : 'disable';?>/<?php echo $item->user_id?>" onclick="return confirm('确定<?php echo $item->is_use == 1 ? '启用' : '停用';?>？')"><?php echo $item->is_use == 1 ? '启用' : '停用';?></a>
                    <?php 
					endif;
					if($perms['uaccount_l_edit'] == 1 || $perms['uaccount_l_view'] == 1):
					?>  
                    <a  href="user_account_log/index/<?php echo $item->user_id;?>" title="明细">明细</a>
                    <?php endif;?> 
<?php if($perm_change_discount):?>
 <a class="change_discount" href="#<?php echo $item->user_id?>" >修改折扣</a>
<?php endif;?> 
                    </td>
				</tr>
		<?php endforeach;?>
			    <tr>
					<td colspan="15" class="bottomTd"></td>
				</tr>
			</table>
  <div class="blank5"></div>
			<div class="page">
				<?php include(APPPATH.'views/common/page.php') ?>
			</div>
<?php if($full_page): ?>
	  </div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
<?php endif; ?>
