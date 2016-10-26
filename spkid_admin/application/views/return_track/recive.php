<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>
        
	<script type="text/javascript">
            
            function load_return_data(){
	        var invoice_no = $.trim($('input[type=text][name=invoice_no]').val());
	        if (invoice_no === '') {
                    alert('请输入退货申请运单号/系统订单号！');
                    return false;
                }
                
                var url = '/return_track/return_info/'+invoice_no;
                $.post(url, {}, function(result){
                    result = $.parseJSON(result);
                    if (result.error === 1) {
                        alert(result.result);
                    } else {
                        $('#listDiv').html(result.result);
                    }
                });
                
	        return false;
	    };
        </script>
        
	<div class="main">
            <div class="main_title"><span class="l">天猫退单管理 &gt;&gt; 退单收货</span><span class="r">[ <a href="/return_track/index">返回列表 </a>]</span></div>
            <div class="produce">
                <div class="pc base">
                    <div class="search_row" style="text-align: center;">
                        退货申请运单号/系统订单号：<input type="text" class="tl" name="invoice_no" value="<?php print empty($invoice_no) ? '' : $invoice_no; ?>" />
                        <input type="button" class="am-btn am-btn-primary" value="载入" onclick="load_return_data()" />
                    </div>
                    <div class="blank5"></div>

                    <div id="listDiv"></div>
                </div>
            </div>
	</div>
        
        <script type="text/javascript">
            <?php if(!empty($invoice_no)): ?>
                load_return_data();
            <?php endif; ?>
                
            $('input[type=text][name=invoice_no]').keydown(function(event) {
                if(event.which === 13) {
                    load_return_data();
                }
            });
        </script>
        
<?php include_once(APPPATH.'views/common/footer.php'); ?>
