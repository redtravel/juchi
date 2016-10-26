<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/lhgdialog/lhgdialog.js"></script>

	<script type="text/javascript">
	    $(function(){
		$('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		$("#sort_rush").hide();
	    });

		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'rush/index';
		function search(){
			listTable.filter['query_rush_id'] = $.trim($('input[name=query_rush_id]').val());
			listTable.filter['rush_index'] = $.trim($('input[name=rush_index]').val());
			// listTable.filter['nav_id'] = $.trim($('select[name=nav_id]').val());
			listTable.filter['status'] = $.trim($('select[name=status]').val());
			listTable.filter['start_time'] = $.trim($('input[name=start_time]').val());
			listTable.filter['end_time'] = $.trim($('input[name=end_time]').val());
			listTable.loadList();
			var start_time = $.trim($('input[name=start_time]').val());
			if(start_time != null && start_time != ""){
			    $("#sort_rush").show();
			}else{
			    $("#sort_rush").hide();
			}
		}
		function redirect(url){
		    if (!/*@cc_on!@*/0) {            
			window.open(url,'_blank');        
		    } else {            
			var a = document.createElement('a');            
			a.href = url;            
			a.target = '_blank';            
			document.body.appendChild(a);            
			a.click();        
		    }
		}
		function sort_rush(){
		    var start_time = $.trim($('input[name=start_time]').val());
		    if(start_time == null || start_time == ""){
			alert("请输入限抢开始时间");
			return ;
		    }
		    redirect("rush/sort_rush_view/" + start_time);
		}
		
		function act(rush_id){
			if(!confirm('确定操作！'))return;
			$.ajax({
			   type: "POST",
			   url: "rush/act",
			   dataType: "JSON",
			   data: "rush_id="+rush_id,
			   success: function(msg){
				   if(msg.msg != ''){
						alert('无权限');
						return false;
					}
				   if(msg.type == 1){
						alert('记录不存在');
						return false;
				   }
				   if(msg.type == 2){
				   		if(msg.status == 0){
							$('td#td_html_'+rush_id).html('未激活');
							$('span#span_html_'+rush_id).html('已激活');
						}
						else if(msg.status == 1){
							$('td#td_html_'+rush_id).html('已激活');
							$('span#span_html_'+rush_id).html('停止');
							$('a#del_'+rush_id).remove();
						}
						else if(msg.status == 2){
							$('td#td_html_'+rush_id).html('停止');
							$('span#span_html_'+rush_id).remove();
						}
				   }
				   if(msg.type == 3){
					   alert('抢购已经结束');
					   return false;
				   }
			   }
			});
		}
		
		function set_par(rush_id){
		    $.ajax({
			   type: "POST",
			   url: "rush/set_properties",
			   dataType: "JSON",
			   data: {rush_id : rush_id,rnd : new Date().getTime()},
			   success: function(result){
			       if(result.error == 0)
				{
				    var dialog = new $.dialog({ id:'thepanel',height:350,width:500,maxBtn:false, title:'设置',iconTitle:false,cover:true,html: result.content});
				    dialog.ShowDialog();
				    $("input[type=text][name=rush_tag]").focus();
				    dialog.addBtn('ok','确定',function(){
						set_properties(function(){search();dialog.cancel();});
					    },'left');
				}else{
				    alert(result.msg);
				}
			   }});
		}
		function set_properties(callback){
		    var rush_id = $.trim($('input[type=hidden][name=rush_id]').val());
		    var rush_tag = $.trim($('input[type=text][name=rush_tag]').val());
		    var rush_index = $.trim($('input[type=text][name=rush_index]').val());
		    var desc =  $.trim($('input[type=text][name=desc]').val());
		    var rush_brand = $.trim($('input[type=text][name=rush_brand]').val());
		    var rush_category = $.trim($('input[type=text][name=rush_category]').val());
		    var rush_discount = $.trim($('input[type=text][name=rush_discount]').val());
		    var rush_prompt = $.trim($('input[type=text][name=rush_prompt]').val());
		   $.ajax({
			    url: 'rush/proc_set_properties',
			    data: {rush_id : rush_id ,
				rush_tag : rush_tag, 
				rush_index : rush_index, 
				desc : desc, 
				rush_brand : rush_brand, 
				rush_category : rush_category, 
				rush_discount : rush_discount, 
				rush_prompt : rush_prompt,
				rnd : new Date().getTime()},
			    dataType: 'json',
			    type: 'POST',
			    success: function(data){
				if(data.err != 0)
				    alert(data.msg);
				else
				    callback();
			    }
		   });
		}
		function onsale(rush_id,sale){
		    $.ajax({
			    url: '/rush/sale',
			    data: {rush_id : rush_id ,
				sale : sale, 
				rnd : new Date().getTime()},
			    dataType: 'json',
			    type: 'POST',
			    success: function(data){
				alert(data.msg);
			    }
		   });
		}
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">促销管理 >> 限时抢购列表</span> <span class="r"><a href="rush/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
	<div class="search_row">
	<form name="search" action="javascript:search(); ">
	    限抢ID:<input type="text" name="query_rush_id" id="query_rush_id" />
	    名称：<input type="text" name="rush_index" id="rush_index" />
             <!--  对应导航分类：
              <span class="item_input">
              <?php print form_dropdown('nav_id',array(''=>'--请选择--')+get_pair($all_nav,'nav_id','nav_name')); ?>
              </span>  -->
	      状态：<select name="status" id="status">
			<option value="">--请选择--</option>
			<option value="1">未激活</option>
			<option value="2">已激活</option>
			<option value="3">停止</option>
		    </select>
	    开始日期：<input type="text" name="start_time" id="start_time" />
              <!--结束时间：<input type="text" name="end_time" id="end_time" />-->
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			<?php if ($perm_edit): ?><a id="sort_rush" class="set" href="javascript:void(0);" onclick="sort_rush();" title="排序">排序</a><?php endif; ?>
		</form>
</div>
		<div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
				<tr>
					<td colspan="12" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="50">ID</th>
				  <th>名称</th>
				  <th>可售数量</th>
				  <th>可售金额</th>
				  <th width="100">现金券ID</th>
				  <th width="120">开始时间</th>
				  <th width="120">结束时间</th>
				  <th width="80">状态</th>
				  <th width="50">排序号</th>
				  <th width="50">创建人</th>
				  <th width="120">创建日期</th>
				  <th width="80">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td align="center"><?php print $row->rush_id; ?></td>
			    	<td align="left"><?php print $row->rush_index; ?></td>
			    	<td><?php print $row->sale_number; ?></td>
			    	<td><?php print $row->sale_amount; ?></td>
			    	<td><?php print $row->campaign_id;?></td> 
			    	<td><?php print $row->start_date; ?></td>
					<td><?php print $row->end_date; ?></td>
					<td id="td_html_<?php print $row->rush_id; ?>"><?php print $this->status_list[$row->status]; ?><?php if($row->end_date<$this->time) print ' 已过期' ?></td>
					<td><?php print $row->sort_order; ?></td>
					<td><?php echo empty($row->create_admin) ? '' : $admin_arr[$row->create_admin]->admin_name; ?></td>
					<td><?php print $row->create_date; ?></td>
					<td>
					<?php if ($perm_edit): ?>
						<a class="edit" href="rush/edit/<?php print $row->rush_id; ?>" title="编辑"></a>
						<a class="set" href="rush/sort_view/<?php print $row->rush_id; ?>" target="_blank" title="排序">排序</a>
					<?php endif ?>
					<?php if ($perm_set): ?>
						<a class="set" href="javascript:void(0);" onclick="set_par('<?php print $row->rush_id; ?>')" title="设置">设置</a>
					<?php endif ?>
					<?php if ($row->end_date>=$this->time): ?>
					    <?php if($perm_edit&&$row->status == 0):?>
						<a id="del_<?php print $row->rush_id; ?>" class="del" href="javascript:void(0);" rel="rush/del/<?php print $row->rush_id; ?>" title="删除" onclick="do_delete(this)"></a>
					    <?php endif;?>
                        <?php if($row->status == 1):?><a href="<?=FRONT_HOST?>/rush-<?php print $row->rush_id; ?>.html?is_preview=1" target="_blank">预览</a><?php endif;?>
					    <?php if($perm_audit):?>
						<span style="cursor:pointer" id='span_html_<?php print $row->rush_id; ?>' onclick="return act(<?php print $row->rush_id; ?>);">
						<?php if($row->status == 1){echo '停止';}elseif($row->status == 0){echo '激活';}?>
						</span>
						<?php if($row->status == 1):?>
						<span style="cursor:pointer" onclick="onsale(<?php print $row->rush_id; ?>,1);">上架</span>
						<span style="cursor:pointer" onclick="onsale(<?php print $row->rush_id; ?>,0);">下架</span>
						<?php endif;?>
					    <?php endif;?>
					<?php endif ?>
					</td>
			    </tr>
				<?php endforeach; ?>
			    <tr>
					<td colspan="12" class="bottomTd"> </td>
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
