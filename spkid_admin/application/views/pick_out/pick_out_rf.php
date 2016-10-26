<?php include APPPATH . 'views/common/rf_header.php'; ?>
<?php
    $describe = "";
    if($doc_type == 1){
	$describe = "出库";
    }else if($doc_type == 2){
	$describe = "调拨";
    }else if($doc_type == 3){
	$describe = "领用";
    }
    
?>
<style type="text/css">
    th,td{font-size:12px}
    #goodsInfo{width:220px;overflow:hidden;}
    .goodsTable{margin-bottom:10px;border:1px solid #fff;width:218px;overflow:hidden;}
    .goodsTable table{border-left:1px solid #00C920;}
    .goodsTable h1{font-size:14px;text-align:center;height:24px;line-height:24px;background-color:#CAFFD0;border:1px solid #00C920;}
    .goodsTable table th{background-color:#EEFFE5}
    .goodsTable table th,.goodsTable table td{border-bottom:1px solid #00C920;border-right:1px solid #00C920;line-height:24px;text-align:center;}
    .button{color:#fff;background-color:#000;padding:2px 20px;border:0;}
    .back{color:#fff;background-color:#000;width:60px;text-align:center;margin-bottom:5px;}
    .yichang{color:#fff;background-color:#f00000;padding:2px 10px;border:0;}
    #note1,#note2,note3{text-align:left}
    #note1,#note3{display:block;}
    #note2{color:red}
    .ge{font-size:12px;text-align:center;color: red;font-weight:bold;}
    .c{color:#f50;font-weight:bold}
</style>
<div class="main">
    <table class="form" id="def" cellpadding=0 cellspacing=0>
	<tr>
	    <td class="item_title"><?=$describe?>单号：</td>
	    <td class="item_input"><input type="text" class="ts" name="doc_code" value="" style="width:150px;ime-mode:disabled;" onkeydown="scan_doc_code(event);" /></td>
	</tr>
	<tr>
	    <td class="item_title">箱号：</td>
	    <td class="item_input">
		<input type="text" class="ts" name="box_code" value="" style="width:150px;ime-mode:disabled;" onkeydown="scan_box_code(event);" />
	    </td>
	</tr>
	<tr>
	    <td class="item_title">储位：</td>
	    <td class="item_input">
		<input type="text" class="ts" name="location_code" value="" style="width:105px;ime-mode:disabled;" onkeydown="scan_location_code(event);" />
		<span id="p_cell" class="ge">&nbsp;</span>
	    </td>
	</tr>
	<tr>
	    <td class="item_title">商品条码：</td>
	    <td class="item_input"><input type="text" class="ts" name="goods_barcode" value="" style="width:150px;ime-mode:disabled;" onkeydown="scan_goods_barcode(event);" /></td>
	</tr>
	<tr>
	    <td colspan="2"><span id="note2"></span><span id="note1"></span><span id="note3"></span></td>
	</tr>
	<tr>
	    <td colspan="2" align="center">
		<input type="button" class="am-btn am-btn-primary" name="sim" value="提交">&nbsp;
                <input type="button" class="am-btn am-btn-primary" name="reset" value="重置">
	    </td>
	</tr>
	<?php if(!empty($location_name)):?>
	<tr>
	    <td colspan="2" align="center">
		建议下架储位：<span id="locat" style="color:red"><?=$location_name?></span>
	    </td>
	</tr>
	<?php endif;?>
    </table>
    <div id="listDiv">
	<form id="scan_pick_form" name="scan_pick_from" action="/pick_out/finish" method="post">
	    <div id="goodsInfo" style="display:none">
		<div class="back">返回</div>
	    </div>
	    <input id="hid_doc_code" type="hidden" name="doc_code" value=""/>
	    <input id="hid_doc_type" type="hidden" name="doc_type" value="<?php echo $doc_type;?>"/>
	    <input id="hid_box_code" type="hidden" name="box_code" value=""/>
	    <input id="hid_location_code" type="hidden" name="location_code" value=""/>
	</form>
	<div class="blank5"></div>
    </div>
</div>
<script type="text/javascript">
    //<![CDATA[
    var data_list;
    var doc_type = <?php echo $doc_type;?>;
    var getID=function (ID) {return document.getElementById(ID)};
    var getCharCode=function(event){return typeof event.charCode == 'number'?event.charCode:event.keyCode};
    function note1(text) {
	getID('note1').innerHTML=text;
    }
    function note2(text) {
	getID('note2').innerHTML=text;
        if(text !== ""){
            alert(text);
        }
    }
    function note3(text) {
	getID('note3').innerHTML=text;
    }
    $(function () {
	$('input[name=sim]').click(function () {
            $('input[name=sim]').attr("disabled","disabled");
	    $('#scan_pick_form').submit();
	}); 
        $('input[name=reset]').click(function(){
            $("input[type=text][name=goods_barcode]").val('');
            $("#hid_location_code").val(''); 
            $("input[type=text][name=location_code]").val("");
            $("#hid_box_code").val('');
            $("input[type=text][name=box_code]").val('');
            note1("");note2("");note3("");
            alert("重置之后会清空箱号，请重新扫描箱号。");
            $("input[type=text][name=box_code]").focus();
        });

	//reload pick_depot_out
<?php if (!empty($doc_code)): ?>
    	$("input[type=text][name=doc_code]").val('<?= $doc_code ?>');
    <?php if ($finished): ?>
	note1("此单据已经完成拣货");
	$("input[type=text][name=doc_code]").focus();
    <?php else: ?>
	$("input[type=text][name=doc_code]").attr("disabled","disabled").css({'backgroundColor':'#bbb','backgroundImage':'none'});
	scan_depot_out();
        <?php if(!empty($box_code)):?>
        $("input[type=text][name=box_code]").val('<?=$box_code?>');
        $("#hid_box_code").val('<?=$box_code?>');
	check_box_code();
            <?php else:?>
	$("input[type=text][name=box_code]").focus();
    <?php endif; ?><?php endif; ?>
<?php else: ?>
    	$("input[type=text][name=doc_code]").focus();
<?php endif; ?>
    });

    function gen_hidden(name,value){
	return "<input type='hidden' name='"+name+"' value='" + value + "'/> ";
    }

    function scan_depot_out(){
	var v_scan_sn = $.trim($("input[type=text][name=doc_code]").val());
	$("#hid_doc_code").val(v_scan_sn);
	$.ajax({
	    url: '/pick_out/check_doc_code',
	    data: {doc_code:v_scan_sn,doc_type:doc_type,rnd : new Date().getTime()},
	    dataType: 'json',
	    type: 'POST',
	    success: function(data){
		if(data.err > 0){
		    note2(data.msg);
		    $("input[type=text][name=doc_code]").val("").focus();
		}else{
		    $("input[type=text][name=doc_code]").attr("disabled","disabled").css({'backgroundColor':'#bbb','backgroundImage':'none'});
		    $("input[type=text][name=box_code]").focus();
		    //var msg = "此单据需拣货<span class='c' flg='scan_num_d_p'>"+data.number+"</span>件商品，已经拣<span class='c' flg='scan_num_f_p'>"+data.finished_number+"</span>件";
		    //note1(msg);
		    if($("#locat")[0]){
			$("#locat").html(data.location_name);
		    }else{
			$("#def").append("<tr><td colspan='2' align='center'>建议下架储位：<span id='locat' style='color:red'>"+data.location_name+"</span></td></tr>");
		    }
		}
	    }
	});
    }
    //拣货单号扫描
    function scan_doc_code(e){
	e = e ? e : (window.event ? window.event : null);
	var v_scan_sn = $.trim($("input[type=text][name=doc_code]").val());
	if (e.keyCode != 13){ return false;}
	if (v_scan_sn == ''){  note2('请您扫描单据号！');    return false	}
	note2("");
	scan_depot_out();
    }

    function scan_box_code(e){
	e = e ? e : (window.event ? window.event : null);
	if (e.keyCode != 13){   return false;  }
	check_box_code();
    }
    
    function check_box_code(){
        var doc_code = $.trim($("input[type=text][name=doc_code]").val());
	if (doc_code == ''){   note2('请您扫描单据号！');return false}
	var v_scan_sn = $.trim($("input[type=text][name=box_code]").val());
	if (v_scan_sn == ''){   note2('请您扫描箱号！');return false}
	note2("");
        if(!check_is_box_code(v_scan_sn)){   note2('扫描箱号【'+v_scan_sn+'】不合法！');return false}
	$.post('/pick_out/check_box_code',{doc_code:doc_code,doc_type:doc_type,box_code:v_scan_sn,rnd : new Date().getTime()},function(data){
            data = jQuery.parseJSON(data);
	    if(data.err == 1){
		note2(data.msg);
		$("input[type=text][name=box_code]").val("").focus();
		return;
	    }
            note1("此箱已经装箱<span class='c'>"+data.number+"</span>件商品");
	    $("#hid_box_code").val(v_scan_sn);
	    $("input[type=text][name=location_code]").focus();
	});
    } 

    function scan_location_code(e){
	e = e ? e : (window.event ? window.event : null);
	if (e.keyCode != 13){   return false;  }
	var doc_code = $.trim($("input[type=text][name=doc_code]").val());
	if (doc_code == ''){   note2('请您扫描单据号！');return false}
	var box_code = $.trim($("input[type=text][name=box_code]").val());
	if (box_code == ''){   note2('请您扫描箱号！');return false}
	var v_scan_sn = $.trim($("input[type=text][name=location_code]").val());
	if (v_scan_sn == ''){   note2('请您扫描储位！');return false}
	note2("");
	$.post('/pick_out/check_location_code',
	{doc_code:doc_code,doc_type:doc_type,box_code:box_code,location_code:v_scan_sn,rnd : new Date().getTime()},
	function(data){
	    data = jQuery.parseJSON(data);
	    if(data.err == 1){
		note2(data.msg);
		$("input[type=text][name=location_code]").val("").focus();
		return;
	    }else{
		if(data.type == 1){
		    note2(data.msg);
		    $("input[type=text][name=location_code]").val("").focus();
		    return;
		}else{
		    note3("该储位需拣货<span class='c' flg='scan_num_d_l'>"+data.product_number+"</span>件商品，已拣<span class='c'  flg='scan_num_f_l'>"+data.scan_number+"</span>件");
		    $("input[type=text][name=goods_barcode]").val('').focus();
		    $("#hid_location_code").val(v_scan_sn);
		    var items = new Array();
		    data_list = data.list;
                    $.each(data_list,function(i,info){
			var hidden ="<div class='hidden_data_"+i+"' flag='prod_hidden'>";
			hidden += gen_hidden('barcode[]',info.provider_barcode);
			hidden += gen_hidden('product_num[]',parseInt(info.product_number) - parseInt(info.scan_number));
			hidden += gen_hidden('sub_id[]',info.sub_id);
			hidden += gen_hidden('scan_num[]',"0");
			hidden += gen_hidden('is_scan[]',"");
			hidden += "</div>";
			items.push(hidden);
		    });
		    $("div[flag='prod_hidden']").remove();
		    for(var j=0;j<items.length;j++){
                        var content = items[j];
                        $("#scan_pick_form").append(content);
		    }
		}
	    }
	});
    }
    var v_scan_num2 = 0;
    //扫描商品条形码
    function scan_goods_barcode(e){
	e = e ? e : (window.event ? window.event : null);
	var v_scan_sn = $.trim($("input[type=text][name=goods_barcode]").val());
	if (e.keyCode != 13){   return false;  }
	note2('');
	if (v_scan_sn == ''){   note2('请您扫描商品条形码！'); return false}
	var v_is_scan = document.getElementsByName("is_scan[]");
	var v_goods_barcode = document.getElementsByName("barcode[]");
	var v_product_num = document.getElementsByName("product_num[]");
	var v_scan_num = document.getElementsByName("scan_num[]");
	var v_flag = 0;
	for (var i = 0; i < v_is_scan.length; i++){
	    if (v_is_scan.item(i).value == "1"){continue;}
	    if (v_goods_barcode.item(i).value != v_scan_sn){continue;}
            if(parseInt(v_scan_num.item(i).value) >= parseInt(v_product_num.item(i).value)){continue;}
	    v_flag = 1;
	    var scan_num = parseInt(v_scan_num.item(i).value) + 1;
	    v_scan_num.item(i).value = scan_num;
	    if (scan_num == v_product_num.item(i).value){
		v_is_scan.item(i).value = "1";
		v_scan_num2 = parseInt(v_scan_num2) + 1;
	    }
	    break;
	}
	$("input[type=text][name=goods_barcode]").val('');
	if (v_flag == 0){   note2('该单据中没有此商品或此商品已经下架完成'); return false;}
	scan_fini_one();
	if (v_scan_num2 != v_is_scan.length){return false;}
	if (confirm("该单据中此储位对应的商品下架已完成，是否自动提交数据？")){document.scan_pick_from.submit();}
    }

    function scan_fini_one(){
	var lo_f_num = $("span[flg='scan_num_f_l']").html();
	var lo_d_num= $("span[flg='scan_num_d_l']").html();
	var now_lo_num = parseInt(lo_f_num)+1;
	$("span[flg='scan_num_f_l']").html(now_lo_num);
	if(now_lo_num == lo_d_num){
	    note2("该储位拣货完成");
	}
    }
    <?php if($doc_type==1):?>
    //var pattern = /(^XJ)-([a-zA-Z0-9]{0,})-([a-zA-Z0-9]{0,})$/; 
    <?php elseif($doc_type==2):?>
    //var pattern = /(^TCX)-([a-zA-Z0-9]{0,})-([a-zA-Z0-9]{0,})$/; 
    <?php elseif($doc_type==3):?>
    //var pattern = /(^LYX)-([a-zA-Z0-9]{0,})-([a-zA-Z0-9]{0,})$/; 
    <?php endif;?>
    var pattern = /([a-zA-Z0-9]{0,})-([a-zA-Z0-9]{0,})-([a-zA-Z0-9]{0,})$/;
    function check_is_box_code(str){
        if(str.indexOf("-") == -1){
            return false;
        }
       return pattern.test(str);
    }
    //]]>
</script>
<?php include(APPPATH . 'views/common/rf_footer.php'); ?>