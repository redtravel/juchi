<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>
        <script type="text/javascript" src="public/js/swfobject.js"></script>
	<script type="text/javascript" src="public/js/FABridge.js"></script>
        
	<script type="text/javascript">
            function load_order_data(){
	        var order_sn = $.trim($('input[type=text][name=order_sn]').val());
	        if (order_sn == '') {
                    alert('请输入天猫运单号/系统订单号！');
                    return false;
                }
                
                var url = '/order_track/order_info/'+order_sn;
                $.post(url, {}, function(result){
                    $('#listDiv').html(result);
                });
                
	        return false;
	    }
        </script>
        
	<div class="main" id="order">
            <div class="main_title"><span class="l">天猫订单管理 &gt;&gt; 订单发货</span><span class="r">[ <a href="/order_track/index">返回列表 </a>]</span></div>
            <div class="produce">
                <div class="pc base">
                    <div class="search_row" style="text-align: center;">
                        天猫运单号/系统订单编号：<input type="text" class="tl" name="order_sn" value="<?php print empty($order_sn) ? '' : $order_sn; ?>" />
                        <input type="button" class="am-btn am-btn-primary" value="载入" onclick="load_order_data()" />
                    </div>
                    <div class="blank5"></div>

                    <div id="listDiv"></div>
                </div>
            </div>
	</div>
        
        <script type="text/javascript">
            <?php if(!empty($order_sn)): ?>
                load_order_data();
            <?php endif; ?>
                
            $('input[type=text][name=order_sn]').keydown(function(event) {
                if(event.which === 13) {
                    load_order_data();
                }
            });
        </script>
        
<?php include_once(APPPATH.'views/common/footer.php'); ?>
