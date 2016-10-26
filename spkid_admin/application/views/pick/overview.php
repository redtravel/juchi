<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript">
	//<![CDATA[
	/**
	 * pick
	 */
        /*
	function pick(obj) {
		if (!confirm('确定要新建拣货单？')) {
			return false;
		}
		obj=$(obj);
		obj.attr('disabled',true);
		var info=obj.attr('rel').split('_');
		var type=info[0];
		var shipping_id=info[1];
		$.ajax({
			url:'pick/add',
			data:{type:type,shipping_id:shipping_id,rnd:new Date().getTime()},
			dataType:'json',
			type:'POST',
			success:function(result){
				obj.attr('disabled',false)
				if(result.msg) alert(result.msg);
				if(result.err) return false;
				if(confirm('立即操作该拣货单?')) {
					location.href=base_url+'pick/scan_shipping_list/'+result.pick_sn;
				}else{
					location.href=location.href;
				}
			}
		});		
	}
        */
        
	function assign_sub() {
		$('input[name=bt_assign_sub]').attr('disabled',true);
		$.ajax({
			url:'pick/assign_sub',
			data:{rnd:new Date().getTime()},
			dataType:'json',
			type:'POST',
			success:function(result){
				$('input[name=bt_assign_sub]').attr('disabled',false);
				if(result.msg) alert(result.msg);
				if(result.err) return false;
				location.href=location.href;				
			}
		});		
	}
        
        function search_admin () {
            var admin_name = $.trim($(':input[name=admin_name]').val());
            if(!admin_name){
                    alert('请填写搜索关键字');
                    return false;
            }
            var sel = $(':input[name=admin_id]');
            $.ajax({
                    url:'pick/search_admin',
                    data:{admin_name:admin_name,rnd:new Date().getTime()},
                    dataType:'json',
                    type:'POST',
                    success:function(result){
                            if (result.msg) {alert(result.msg)};
                            if (result.err) {return false};
                            sel[0].options.length=0;
                            for(i in result.admin_list){
                                    var admin = result.admin_list[i];
                                    var val = admin.admin_name;
                                    if(admin.realname) val += ' [ 真实姓名:'+admin.realname+' ]';
                                    if(admin.admin_email) val += ' [ 邮箱:'+admin.admin_email+' ]';
                                    sel[0].options.add(new Option(val,admin.admin_id));
                            }
                    }
            });
        }
	
        function checksubmit(){
            var pick_shipping = $('input[name="pick_shipping"]:checked').val();
            var hand_type = $('input[name="hand_type"]:checked').val();
            if(pick_shipping == '' || pick_shipping == null || pick_shipping == undefined){
                alert("请选择快递公司！");
                return false;
            }
            if(hand_type == '' || hand_type == null || hand_type == undefined){
                alert("请选择拣货类型！");
                return false;
            }
            if(hand_type == 1){
                var admin_id = $('select[name="admin_id"]').val();
                if(admin_id == '' || admin_id == null || admin_id == undefined){
                    alert("请选择操作管理员！");
                    return false;
                }
            }
            return true;
        }
        
	//]]>
</script>
	<div class="main">
		<div class="main_title"><span class="l">待拣汇总</span><span class="r"><a href="pick" class="add">拣货单列表</a></span></div>
		<div class="blank5"></div>
		<div class="search_row">
			<input type="button" name="bt_refresh" class="am-btn am-btn-primary" onclick="location.href=location.href;" value="刷新"/>
			<input type="button" name="bt_assign_sub" class="am-btn am-btn-primary" onclick="assign_sub();" value="分配虚库"/>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">
                    <form name="add_pick" action="pick/add" method="post" onsubmit="return checksubmit();">
			<table class="form" cellpadding=0 cellspacing=0>
				<tr>
					<td colspan="6" class="topTd"> </td>
				</tr>
				<tr>
					<td class="item_title" width="150">订单不代收款</td>
					<td class="item_input">
						<?php foreach($order_status as $r):?>
                                                <input type="radio" name="pick_shipping" value="<?php print "order_{$r->shipping_id}" ?>" /><?php print "{$r->shipping_name}【<span style='color:red'>{$r->pick_num}</span>】";?>&nbsp;&nbsp;
						<!-- <input type="button" name="btn_pick" rel="<?php //print "order_{$r->shipping_id}" ?>" onclick="pick(this)" value="<?php //print "{$r->shipping_name}【{$r->pick_num}】";?>" /> -->
						<?php endforeach;?>
					</td>
				</tr>
				<tr>
					<td class="item_title">订单代收款</td>
					<td class="item_input">
						<?php foreach($ordercod_status as $r):?>
                                                <input type="radio" name="pick_shipping" value="<?php print "ordercod_{$r->shipping_id}" ?>" /><?php print "{$r->shipping_name}【<span style='color:red'>{$r->pick_num}</span>】";?>&nbsp;&nbsp;
						<!-- <input type="button" name="btn_pick" rel="<?php //print "ordercod_{$r->shipping_id}" ?>" onclick="pick(this)" value="<?php //print "{$r->shipping_name}【{$r->pick_num}】";?>" /> -->
						<?php endforeach;?>
					</td>
				</tr>
                                <!-- <tr>
					<td class="item_title">换货单</td>
					<td class="item_input">
						<?php //foreach($change_status as $r):?>
						<input type="button" name="btn_pick" rel="<?php //print "change_{$r->shipping_id}" ?>" onclick="pick(this)" value="<?php //print "{$r->shipping_name}【{$r->pick_num}】";?>" />
						<?php //endforeach;?>
					</td>
				</tr> -->
                                <tr>
                                    <td class="item_title" width="200">拣货类型:</td>
                                    <td class="item_input">
                                        <input type="radio" name="hand_type" value="0" onclick="$('#hand').hide();"/>扫描&nbsp;&nbsp;
                                        <input type="radio" name="hand_type" value="1" onclick="$('#hand').show();"/>手动
                                    </td>
                                </tr>
                                <tr id="hand" style="display: none">
                                        <td class="item_title">拣货操作人:</td>
                                        <td class="item_input">
                                                <?php print form_input('admin_name') ?>
                                                <?php print form_button('admin_search','搜索','onclick="search_admin();"') ?>
                                                <?php print form_dropdown('admin_id', array()); ?>
                                                &nbsp;&nbsp;（按管理员昵称或真实姓名或Email搜索）
                                        </td>
                                </tr>
                                <tr>
                                        <td class="item_title"></td>
                                        <td class="item_input">
                                                <?php print form_submit('mysubmit','提交','class="am-btn am-btn-primary"') ?>
                                        </td>
                                </tr>
				<tr>
					<td colspan="6" class="bottomTd"> </td>
				</tr>
			</table>
			<div class="blank5"></div>
                    </form>
		</div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>