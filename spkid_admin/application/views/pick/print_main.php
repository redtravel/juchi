<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/swfobject.js"></script>
	<script type="text/javascript" src="public/js/FABridge.js"></script>
	
	<script type="text/javascript">
		//<![CDATA[
		var type='';
                var blank_shipping = '<?=$blank_shipping?>';
		function search(){
			var sn=$.trim($('input[type=text][name=sn]').val());
                        $("#bt_print").hide();
			if(!sn){
				alert('请输入单号');
				return;
			}
			$.ajax({
				url:'pick/print_main_list',
				data:{sn:sn,rnd:new Date().getTime()},
				dataType:'json',
				type:'POST',
				success:function(result){
					if(result.msg) alert(result.msg);
					if(result.err) return;
					$('#listDiv').html(result.html);
					type=result.type;
                                        if (blank_shipping.indexOf(result.shipping_id) != -1){
                                            $("#bt_print").show();
                                        }                                       
				}
			});			
		}
                
                function blank_print(){
                    var ids = '';
                    $(':checkbox[name=sn]:checked').each(function(){
                        var td = $(this).parent('td');
                        var id = $(':hidden[name=id]',td).val();
                        ids = (ids == '') ? id : ids+'|'+id;
                    });

                    window.location.href='/pick/blank_print/'+ids;
                }
		
		function switch_check() {
			if($(':checkbox[name=check_all]:checked').length){
				$(':checkbox[name=sn]').attr('checked',true);
			}else{
				$(':checkbox[name=sn]').attr('checked',false);
			}
		}
		
		function print_sale() {
			var url=base_url+'pick/print_sale/';
			url+=type=='order'?'order/':'change/';
			var ids=new Array();
			$(':checkbox[name=sn]:checked').each(function(){
				var td = $(this).parent('td');
				var id = $(':hidden[name=id]',td).val();
				ids.push(id);				
			});
			if(!ids.length){
				alert('请选择要打印的订单或换货单');
				return false;
			}			
			ids=ids.join('-');
			url+=ids;
			window.open(url);
			return true;
		} // End of print_sale
		
		function print_invoice() {
                    if(!$(':checkbox[name=sn]:checked').length){
                            alert('请选择要打印的订单或换货单');
                            return false;
                    }
                    var xml='';
                    var myDate = new Date();
                    var v_curDate = (myDate.getMonth()+1)+'月'+myDate.getDate()+'日'+myDate.getHours()+'时';

			$(':checkbox[name=sn]:checked').each(function(){
					var td = $(this).parent('td');
					var sn = $(this).val();
					var code = $(':hidden[name=code]',td).val();//快递公司shipping_code
					var codAmount = $(':hidden[name=codAmount]',td).val();
					//var cod = codAmount>0?1:0;
					var cod = 1;
					var rcvPerson = $(':hidden[name=rcvPerson]',td).val();
					var rcvAddress = $(':hidden[name=rcvAddress]',td).val();
					var rcvMobile = $(':hidden[name=rcvMobile]',td).val();
					var rcvTel = $(':hidden[name=rcvTel]',td).val();
					var bestTime = $(':hidden[name=bestTime]',td).val();
					var goods_num = $(':hidden[name=goods_num]',td).val();
					var weight= $(':hidden[name=weight]',td).val();
					var pick_cell = $(':hidden[name=pick_cell]',td).val();
					var city = $(':hidden[name=city]',td).val();
					var codAmount2 = g2b(codAmount);
					if (code == 'ems') {
					var custormPostNo = (cod == 1) ? '代收货款  上海治晨' : '非代收货款  上海治晨';
					} else if (code == 'ems-sh' || code == 'ems-hz') {
					var custormPostNo = (cod == 1) ? '代收货款' : '非代收货款';
					}
					if (xml == '') xml='<data express="public/express/'+code+'.xml">';
					xml+='<order><orderSn>'+sn+'</orderSn><code>'+code+'</code><isCod>'+cod+'</isCod><codAmount>￥'+codAmount+'</codAmount><codAmount2>'+codAmount2+'</codAmount2><custormPostNo>'+custormPostNo+'</custormPostNo><rcvPerson>'+rcvPerson+'</rcvPerson><rcvAddress>'+rcvAddress+'</rcvAddress>';

					if (code == 'yto') {
						if (rcvMobile != '') xml += '<rcvMobile>'+rcvMobile+'</rcvMobile>';
						if (rcvTel != '') xml += '<rcvTel>'+rcvTel+'</rcvTel>';
					}else{
						if (rcvMobile != '') {
							xml += '<rcvMobile>'+rcvMobile+'</rcvMobile>';
						} else if (rcvTel != '') {
							xml += '<rcvMobile>'+rcvTel+'</rcvMobile>';
						}
					}
					xml+='<bestTime>'+bestTime+'</bestTime><weight>'+weight+'</weight><goodsnum>'+goods_num+'</goodsnum><orderCell>'+pick_cell+'</orderCell><lcity>'+city+'</lcity><city2>'+city+'</city2><pdate>'+v_curDate+'</pdate></order>';

			});
            xml+='</data>';
            flexApp.doPrint(xml);
            return true;
        } // End of print_invoice

        // 将金额转换成大写
        function g2b(str) {
            var p = str.indexOf(".");
            if (p < 0) return '';
            var result = '';
            str = str.substring(0, p);
            var strl = str.length > 5 ? 4 : str.length-1;
            var v_unitArray = new Array('元', '拾', '佰', '仟', '万');
            for (var i = 0; i < str.length; i++) {
                if (str[i] == 0) result = result + str[i].replace(/0/g, '零') + v_unitArray[strl-i];
                if (str[i] == 1) result = result + str[i].replace(/1/g, '壹') + v_unitArray[strl-i];
                if (str[i] == 2) result = result + str[i].replace(/2/g, '贰') + v_unitArray[strl-i];
                if (str[i] == 3) result = result + str[i].replace(/3/g, '叁') + v_unitArray[strl-i];
                if (str[i] == 4) result = result + str[i].replace(/4/g, '肆') + v_unitArray[strl-i];
                if (str[i] == 5) result = result + str[i].replace(/5/g, '伍') + v_unitArray[strl-i];
                if (str[i] == 6) result = result + str[i].replace(/6/g, '陆') + v_unitArray[strl-i];
                if (str[i] == 7) result = result + str[i].replace(/7/g, '柒') + v_unitArray[strl-i];
                if (str[i] == 8) result = result + str[i].replace(/8/g, '捌') + v_unitArray[strl-i];
                if (str[i] == 9) result = result + str[i].replace(/9/g, '玖') + v_unitArray[strl-i];
            }
            return result;
        }
		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">运单销售单打印</span><span class="r"><a href="pick" class="add">拣货单列表</a></span></div>
		<div class="blank5"></div>
		<div class="search_row">
			<form name="search" action="javascript:search(); ">
            单号(拣货单/订单/换货单)：<input type="text" class="ts" name="sn" value="<?php echo $pick_sn;?>" style="width:200px;" />
			<input type="submit" class="am-btn am-btn-primary" value="搜索" />
			</form>
		</div>
		<div class="blank5"></div>
		<div id="listDiv">

		</div>
		<div id="op">
			<!--<input type="button" name="bt_print_sale" value="打印销售单" onclick="print_sale();" />-->
			<input type="button" name="bt_print_invoice" value="控件加载中，请稍候..." onclick="print_invoice();" disabled />
                        <input type="button" name="bt_print" id="bt_print" style="display: none;" value="打印白面单" onclick="blank_print();" />
			<div id="flashContent" style="display:none;">运单打印控件</div>
			<script type="text/javascript">
				//swfobject.embedSWF("public/js/autoPrinter-1.1.10.swf", "flashContent","0", "0", "10.0.0","",{bridgeName:"expressBridge"});
				//swfobject.embedSWF("public/js/autoPrinter-1.1.13.swf", "flashContent","0", "0", "10.0.0","",{bridgeName:"expressBridge"});
				swfobject.embedSWF("public/js/autoPrinter-1.1.16.swf", "flashContent","0", "0", "10.0.0","",{bridgeName:"expressBridge"});
				//swfobject.embedSWF("public/js/autoPrinter-1.1.7.swf", "flashContent","0", "0", "10.0.0","",{bridgeName:"expressBridge"});
			</script>
		</div>
	</div>
	<script type="text/javascript">
            var flexApp;
            var initCallback = function() {			
                    flexApp = FABridge.expressBridge.root();
                    $(':input[name=bt_print_invoice]').val('打印运单').attr('disabled',false);
            }
            // register the callback to load reference to the Flex app
            FABridge.addInitializationCallback( "expressBridge", initCallback );
        <?php 
         if ($pick_sn):
        ?>
        search();
        <?php 
        endif;
        ?>
	</script>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
