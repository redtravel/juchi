<?php if($full_page): ?>
<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="../../../public/js/listtable.js"></script>
	<script type="text/javascript" src="../../../public/js/utils.js"></script>

	<script type="text/javascript">
	    $(function(){
        $('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});

    	});

		//<![CDATA[
		listTable.filter.page_count = '<?php echo $filter['page_count']; ?>';
		listTable.filter.page = '<?php echo $filter['page']; ?>';
		listTable.url = 'liuyan/index';
		function search(){
			listTable.filter['tag_id'] = $.trim($('input[name=tag_id]').val());
			listTable.filter['tag_type'] = $.trim($('select[name=tag_type]').val());
			listTable.filter['comment_type'] = $.trim($('select[name=comment_type]').val());
			listTable.filter['is_audit'] = $.trim($('select[name=is_audit]').val());
			listTable.filter['is_reply'] = $.trim($('select[name=is_reply]').val());
			listTable.filter['is_del'] = $.trim($('select[name=is_del]').val());
			listTable.filter['start_time'] = $.trim($('input[name=start_time]').val());
			listTable.filter['end_time'] = $.trim($('input[name=end_time]').val());
			listTable.loadList();
		}
		
		function audit(comment_id){
			if(!confirm('确认审核！'))return;
			$.ajax({
			   type: "POST",
			   url: "liuyan/audit",
			   dataType:'json',
			   data: {comment_id:comment_id,rnd:new Date().getTime()},
			   success: function(result){
			   	if(result.msg) alert(result.msg);
			   	if(result.err!=0) return false;
				$('span#au_'+comment_id).remove();
				//$('td#tdau_'+comment_id).html('<img src="public/images/yes.gif" />'); 改样式 By Rock
				$('td#tdau_'+comment_id).html('<span class="yesForGif"></span>');
				$('a#del_'+comment_id).remove();				 				 
			   }
			});
		}
		function add_comment () {
			var tag_id = $.trim($('input[name=tag_id]').val());
			tag_id=parseInt(tag_id);
			if(isNaN(tag_id)) tag_id=0;
			var tag_type = $.trim($('select[name=tag_type]').val());
			tag_type=tag_type==2?2:1;
			location.href=base_url+'liuyan/add.html?tag_id='+tag_id+'&tag_type='+tag_type;
		}

		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">留言管理 &gt;&gt; 留言列表</span> <a href="javascript:void(0)" onclick="add_comment();" class="add r">新增</a></div>
		<div class="blank5"></div>
    <div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
			  商品/礼包ID：
              <input type="text" name="tag_id" id="tag_id" size="6" value="<?php print $filter['tag_id']?$filter['tag_id']:'';?>"/>
              <?php print form_dropdown('tag_type',array('--关联类型--')+$this->tag_type,$filter['tag_type']); ?>
              <?php print form_dropdown('comment_type',array('--留言类型--')+$this->comment_type,$filter['comment_type']); ?>
              <?php print form_dropdown('is_reply',array(-1=>'--是否回复--',0=>'未回复',1=>'已回复'),$filter['is_reply']); ?>
              <?php print form_dropdown('is_del',array(-1=>'--是否删除--',0=>'未删除',1=>'已删除'),$filter['is_del']); ?>
              留言时间：<input type="text" name="start_time" id="start_time" /><input type="text" name="end_time" id="end_time" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
                <div class="blank5"></div>

		  

		<div class="blank5"></div>
        <div class="blank5"></div>
		<div id="listDiv">
<?php endif; ?>
			<table id="dataTable" class="dataTable" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="14" class="topTd"> </td>
				</tr>
				<tr class="row">
				  <th width="42"><a href="javascript:listTable.sort('l.comment_id', 'DES'); ">ID<?php echo ($filter['sort_by'] == 'l.comment_id') ? $filter['sort_flag'] : '' ?></a></th>
			      <th width="50">类型</th>
			      <th width="50">类型</th>
			      <th width="200">商品</th>
			      <th>摘要</th>
			      <th width="50">星级</th>		      		      
				  <th width="52">用户名</th>
				  <th width="44">审核</th>
				  <th width="44">回复</th>
				  <th width="43">删除</th>
				  <th width="147">创建时间</th>
			      <th width="154">操作</th>
				</tr>
				<?php foreach($list as $row): ?>
			    <tr class="row">
			    	<td><?php print $row->comment_id; ?></td>
					<td><?php switch ($row->tag_type) {
						case 1:
							echo "商品";
							break;
						case 2:
							echo "礼包";
							break;
						case 3:
							echo "课程";
							break;
						case 4:
							echo "意见反馈";
							break;
						default:
							echo "unkown";
							break;
					} ?></td>
					<td><?php //print $row->comment_type==1?'咨询':'评价';
						switch ($row->comment_type) {
							case 1:
								echo '咨询';
								break;
							case 2:
								echo '评价';
								break;
							case 3:
								echo '测评';
								break;
							case 4:
								echo '询价';
								break;
							default:
								# code...
								break;
						}
					?></td>
					<td style="text-align:left">
					<a href="<?php switch ($row->tag_type) {
						case 1:
							echo front_url('pdetail-'.$row->tag_id.'.html');
							break;
						
						case 2:
							echo front_url('package-'.$row->tag_id.'.html');
							break;
						case 3:
							echo front_url('product-'.$row->tag_id.'.html');
							break;

						default:
							# code...
							break;
					} ?>" target="_blank">
					<?php switch ($row->tag_type) {
						case 1:
							echo "[{$row->product_sn}] {$row->product_name}";
							break;
						
						case 2:
							echo $row->package_name;
							break;

						case 3:
							echo "[{$row->product_sn}] {$row->product_name}";
							break;
						default:
							# code...
							break;
					} ?>
					</a>
					</td>
					<td style="text-align:left;"><?php print htmlspecialchars($row->comment_content); ?></td>
					<td>
					<?php
						if($row->tag_type == 4){
							switch ($row->grade) {
								case 0:
									echo '非常不满意';
									break;
								case 1:
									echo '不满意';
									break;
								case 2:
									echo '一般';
									break;
								case 3:
									echo '满意';
									break;
								case 4:
									echo '非常满意';
									break;
							}
						}else{ print $row->grade."星"; } ?>
					</td>
					<td>&nbsp;<?php print $row->create_admin? '<span style="color:red">*</span>':'';?> <?php print $row->user_name ? $row->user_name:'匿名'; ?></td>
					<td id="tdau_<?php print $row->comment_id; ?>">&nbsp;<?php if($row->is_audit == 0){ echo '<span class="noForGif"></span>' /*'<img src="public/images/no.gif" />'改为图片样式byRock*/;}
						else{echo '<span class="yesForGif"></span>'/*'<img src="public/images/yes.gif" />'改为图片样式byRock*/;} ?></td>
					<td>&nbsp;<?php if(empty($row->reply_admin_id)){ echo '<span class="noForGif"></span>' /*'<img src="public/images/no.gif" />'改为图片样式byRock*/;}
						else{echo /*'<img src="public/images/yes.gif" />'改为图片样式byRock*/ '<span class="yesForGif"></span>';} ?></td>
					<!--<td>&nbsp;<img src="public/images/--><?php /*echo $row->is_del == 0 ? 'no' : 'yes'*/?><!--.gif" /></td>改为图片样式byRock-->
                    <td>&nbsp;<span class="<?php echo $row->is_del == 0 ? 'noForGif' : 'yesForGif' ?> "/></span></td>
					<td>&nbsp;<?php print $row->comment_date; ?></td>
					<td> 
						<?php if($row->tag_type != 4) { ?>          	
							<a href="liuyan/replay/<?php print $row->comment_id; ?>" title="编辑" class="edit"></a>   
							       
	                        <?php if($perm_edit && !$row->is_del && !$row->is_audit):?>
	                        <a id="del_<?php print $row->comment_id; ?>" href="javascript:void(0);" rel="liuyan/del/<?php print $row->comment_id; ?>" title="删除" onclick="do_delete(this)" class="del"></a>
	                        <?php endif;?>
	                        
	                        <?php if($perm_aurep && !$row->is_audit && !$row->is_del):?>
	                        <span id="au_<?php print $row->comment_id; ?>" onclick="return audit(<?php print $row->comment_id; ?>)" style="cursor:pointer">审核</span>
	                        <?php endif;?>
                        <?php } ?>  
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