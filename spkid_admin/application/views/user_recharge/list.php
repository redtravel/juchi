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
		listTable.url = 'user_recharge/index';
		function search(){
			listTable.filter['email'] = $.trim($('input[name=email]').val());
			listTable.filter['mobile'] = $.trim($('input[name=mobile]').val());
			listTable.filter['is_paid'] = $.trim($('select[name=is_paid]').val());
			listTable.filter['is_audit'] = $.trim($('select[name=is_audit]').val());
			listTable.filter['start_time'] = $.trim($('input[name=start_time]').val());
			listTable.filter['end_time'] = $.trim($('input[name=end_time]').val());
			listTable.loadList();
		}
		function audit(recharge_id){
			$.ajax({
			   type: "POST",
			   url: "user_recharge/audit",
			   data: "recharge_id="+recharge_id,
			   dataType: "JSON",
			   success: function(msg){
				 if(msg.msg != ''){
					alert('无操作权限');	 
					return false;	   
				 }
				 if(msg.che == 1){
					alert('记录不存在');	 
					return false;
				 }
				 if(msg.che == 2){
					alert('未支付');	 
					return false;
				 }
				 if(msg.che == 3){
					alert('已审核');	 
					return false;
				 }
				 if(msg.che == 4){
					alert('已删除');	 
					return false;
				 }
				 if(msg.che == 5){
					alert('审核成功');	 
					$('span#audit_'+recharge_id).html('');
					$('td#audit_admin_'+recharge_id).html(msg.audit_admin);
					$('td#audit_date_'+recharge_id).html(msg.audit_date);
					$('a#a_'+recharge_id).remove();
				 }
				 
			   }
			});
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">会员管理 >> 充值列表</span> <span class="r"><a href="user_recharge/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
	  <div class="search_row">
			<form name="search" action="javascript:search(); ">
            手机：
              <input type="text" name="mobile" id="mobile" />
            email：
              <input type="text" name="email" id="email" />
			  是否支付：
			    <select name="is_paid" id="is_paid">
			      <option value="">--请选择--</option>
			      <option value="2">是</option>
			      <option value="1">否</option>
		      </select>
              			  是否审核：
			    <select name="is_audit" id="is_audit">
			      <option value="">--请选择--</option>
			      <option value="2">是</option>
			      <option value="1">否</option>
		      </select>

			  支付时间：<input type="text" name="start_time" id="start_time" /><input type="text" name="end_time" id="end_time" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="14" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="42">ID</th>
			      <th width="68">用户名</th>
			      <th width="66">手机</th>
			      <th width="66">email</th>
			      <th width="66">金额</th>
			      <th width="58">支付</th>
				  <th width="194">支付时间</th>
				  <th width="96">支付方式</th>
				  <th width="70">审核人</th>
				  <th width="196">审核日期</th>
				  <th width="81">创建人</th>
				  <th width="162">创建日期</th>
				  <th width="137">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->recharge_id; ?></td>
					<td><?php print $row->user_name; ?></td>
					<td><?php print $row->mobile; ?></td>
				  <td><?php print $row->email; ?></td>
					<td><?php print $row->amount; ?></td>
					<!--<td><img src="public/images/<?php /*echo $row->is_paid == 0 ? 'no' : 'yes'*/?>.gif" /></td>样式显示 By Rock-->
                    <td><span class="<?php echo $row->is_paid == 0 ? 'noForGif' : 'yesForGif'?>"></span></td>
					<td><?php print $row->paid_date == '0000-00-00 00:00:00' ? '' : $row->paid_date; ?></td>
					<td><?php print $row->pay_name; ?></td>
					<td id="audit_admin_<?php print $row->recharge_id; ?>"><?php echo empty($row->audit_admin) ? '' : $all_admin[$row->audit_admin]->admin_name; ?></td>
					<td id="audit_date_<?php print $row->recharge_id; ?>"><?php print $row->audit_date == '0000-00-00 00:00:00' ? '' : $row->audit_date; ?></td>
					<td><?php echo empty($row->create_admin) ? '' : $all_admin[$row->create_admin]->admin_name; ?></td>
					<td><?php print $row->create_date; ?></td>
					<td>
                    <?php if($perms['user_recharge_del'] == 1):?>
                    <?php if($row->is_audit != 1):?>
                    <a id="a_<?php print $row->recharge_id; ?>" class="del" href="javascript:void(0);" rel="user_recharge/del/<?php print $row->recharge_id; ?>" title="删除" onclick="do_delete(this)"></a>
                    <?php endif;?>
                    <?php endif; if($perms['user_recharge_author'] == 1):?>
                    <span id="audit_<?php print $row->recharge_id; ?>"><?php echo $row->is_audit == 1 ? '' : '<a onclick="audit('.$row->recharge_id.')" style="cursor:pointer" title="审核">审核</a>'?>	</span>
                    <?php endif;?>
                    <a href="user_recharge/show/<?php echo $row->recharge_id;?>" title="查看">查看</a>
 <a class="change_discount" href="#<?php echo $row->user_id?>" >修改会员折扣</a>
                    </td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="14" class="bottomTd"> </td>
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
