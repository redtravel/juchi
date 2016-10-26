<?php include APPPATH.'views/common/rf_header.php'; ?>
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
			<td class="item_title">拣货单号：</td>
			<td class="item_input"><input type="text" class="ts" name="pick_sn" value="" style="width:150px;ime-mode:disabled;" onkeydown="scan_pick_sn(event);" /></td>
		</tr>
		<tr>
			<td class="item_title">储位：</td>
			<td class="item_input">
			    <input type="text" class="ts" name="location_code" value="" style="width:105px;ime-mode:disabled;" onkeydown="scan_location_code(event);" />
			    格：<span id="p_cell" class="ge">&nbsp;</span>
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
				<input type="button" class="am-btn am-btn-primary" name="info" value="详情">
			</td>
		</tr>
	</table>
	<div id="listDiv">
		<form id="scan_pick_form" name="scan_pick" action="/pick/scan_pick_rf_finish" method="post">
			<div id="goodsInfo" style="display:none">
				<div class="back">返回</div>
			</div>
			<input id="hid_pick_sn" type="hidden" name="pick_sn" value=""/>
		</form>
		<div class="blank5"></div>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
var getID=function (ID) {return document.getElementById(ID)};
var getCharCode=function(event){return typeof event.charCode == 'number'?event.charCode:event.keyCode};
var num=0,obj=getID('goodsInfo'),len,len2;
var data_list;
function paixu() {
	var len2=0;
	for (var i=1;i<len+1;i++) {
		var number=i;
		var a=parseInt(obj.children[number].getAttribute('a')),
			b=parseInt(document.getElementsByName('scan_num[]')[number-1].value),
			s=parseInt(obj.children[number].getAttribute('s'));
		if ((a+b)!=s) {
			document.getElementsByName('yichang')[number-1].style.display='block';
			obj.children[number].style.borderColor='red';
			obj.children[number].className='goodsTable on';
			len2+=1;
		}else{
			document.getElementsByName('yichang')[number-1].style.display='none';
			obj.children[number].style.borderColor='#fff';
			obj.children[number].className='goodsTable off';
		}
	};
	for (var i=0;i<len2;i++) {
	    var number=i;
	    var node=$('#goodsInfo .on').eq(number);
	    $('#goodsInfo .off').eq(0).before(node);
	}
}
function paixu_savePos(saveNum) {
	var x=saveNum,num=0;
	var len3=$('#scan_pick_form .hidden_data').length;
	for (var i=0;i<len3;i++) {
		var number=i;
		var y=data_list[number].location_name;
		var a=parseInt(data_list[number].pick_num),
			b=parseInt(document.getElementsByName('scan_num[]')[number].value),
			s=parseInt(data_list[number].product_number);
		if (x==y){
			num+=(s-b-a);
		}
	}
	if(num==0){
		note3(" ");
		note2('该储位下没有需要拣货的商品！');
		$("input[type=text][name=location_code]").val("").focus();
	}else {
		note2(" ");
		note3("该储位共需检货<span class='c' flg='scan_num_d_l'>"+num+"</span>件商品，已检<span class='c'  flg='scan_num_f_l'>0</span>件");
		$("input[type=text][name=goods_barcode]").val("").focus();
	}
}
function paixu_pick() {
	var num=0;
	for (var i=0;i<len;i++) {
		var number=i;
		var a=parseInt(data_list[number].pick_num),
			b=0,
			s=parseInt(data_list[number].product_number);
		    num+=(s-b-a);
	}
	if(num==0){
		note1(" ");
		note2('该拣货单没有需要拣货的商品！');
		$("input[type=text][name=pick_sn]").val("").focus();
	}else {
		note2(" ");
		note1("该拣货单共需检货<span class='c' flg='scan_num_d_p'>"+num+"</span>件商品，已检<span class='c' flg='scan_num_f_p'>0</span>件");
		$("input[type=text][name=location_code]").val("").focus();
	}
}
function note1(text) {
	getID('note1').innerHTML=text;
}
function note2(text) {
	getID('note2').innerHTML=text;
}
function note3(text) {
	getID('note3').innerHTML=text;
}
$(function () {
	$('input[name=info]').click(function () {
		$('#def').hide();
		for (var i=0; i<len; i++) {
			var num=i;
			var b=parseInt(document.getElementsByName('scan_num[]')[num].value);
			var str = gen_product(
				data_list[num].sub_id,
				data_list[num].product_name,
				data_list[num].brand_name,
				data_list[num].provider_productcode,
				data_list[num].location_name,
				data_list[num].provider_barcode,
				data_list[num].pick_cell,
				data_list[num].product_number,
				data_list[num].pick_num,
				b,'');
			$("#goodsInfo").append(str);
		};
		$('#goodsInfo').show();
		paixu();
	});
	$('input[name=sim]').click(function () {
		$('#scan_pick_form').submit();
	});
	$('.back').click(function () {
		$('#goodsInfo').hide();
		$('#def').show();
		$("#goodsInfo .goodsTable").remove();
		if($("input[type=text][name=pick_sn]").val() ==""){
		    $("input[type=text][name=pick_sn]").focus();
		}else if($("input[type=text][name=location_code]").val() ==""){
		    $("input[type=text][name=location_code]").focus();
		}else{
		    $("input[type=text][name=goods_barcode]").focus();
		}
	});

	//reload pick_scan
	<?php if(!empty($pick_sn)):?>
	    $("input[type=text][name=pick_sn]").val('<?=$pick_sn?>');
	    <?php if($finished):?>
	    note1("此拣货单已经完成拣货");
	    $("input[type=text][name=pick_sn]").focus();
	    <?php else:?>
	    $("input[type=text][name=pick_sn]").attr("disabled","disabled").css({'backgroundColor':'#bbb','backgroundImage':'none'});
	    scan_pick();
	    $("input[type=text][name=location_code]").focus();
	    <?php endif;?>
	<?php else:?>
	$("input[type=text][name=pick_sn]").focus();
	<?php endif;?>
});

function gen_hidden(name,value){
	return "<input type='hidden' name='"+name+"' value='" + value + "'/> ";
}

function gen_product(index,product_name,brand_name,provider_productcode,location
					,provider_barcode,rel_no,number,pick_num,scan_num,hidden){
	var str = '<div class="goodsTable" s="'+number+'" a="'+pick_num+'" b="0" save="'+location+'">';
		str += '<h1>'+product_name+'</h1>';
		str += '<table id="goods_'+index+'" width="100%" cellpadding=0 cellspacing=0><tr><th rowspan="2">';
		str += hidden;
		str += '<input type="button" class="yichang" name="yichang" value="异常" style="display:none" onclick="markers('+index+');">';
		str += '</th><th>品牌</th><th>货号</th></tr>';
		str += '<tr><td>'+brand_name+'</td><td>'+provider_productcode+'</td></tr>';
		str += '<tr><th>储位</th><th>条形码</th><th>格子号</th></tr>';
		str += '<tr><td class="savePos">'+location+'</td><td>'+provider_barcode+'</td><td>'+rel_no+'</td></tr>';
		str += '<tr><th>应拣数量</th><th>已拣数量</th><th>本次数量</th></tr>';
		str += '<tr><td class="s_num">'+number+'</td><td class="a_num">'+pick_num+'</td><td class="b_num">'+scan_num+'</td></tr>';
		str += '</table></div>';
	return str;
}
function scan_pick(){
	var v_scan_sn = $.trim($("input[type=text][name=pick_sn]").val());
	$("#hid_pick_sn").val(v_scan_sn);
	$.ajax({
		url: '/pick/scan_pick_rf',
		data: {pick_sn:v_scan_sn,rnd : new Date().getTime()},
		dataType: 'json',
		type: 'POST',
		success: function(data){
			if(data.error > 0){
				note2(data.message);
				$("input[type=text][name=pick_sn]").val("").focus();
			}else{
				$("input[type=text][name=pick_sn]").attr("disabled","disabled").css({'backgroundColor':'#bbb','backgroundImage':'none'});
				$("input[type=text][name=location_code]").focus();
				var items = new Array();
				data_list = data.list;
				len=data_list.length;
				$(data.list).each(function(i,info){
					var hidden ="<div class='hidden_data'>";
					hidden += gen_hidden('barcode[]',info.provider_barcode);
					hidden += gen_hidden('product_num[]',info.product_number - info.pick_num);
					hidden += gen_hidden('sub_id[]',info.sub_id);
					hidden += gen_hidden('pick_cell[]',info.pick_cell);
					hidden += gen_hidden('rel_no[]',info.rel_no);
					hidden += gen_hidden('scan_num[]',"0");
					hidden += gen_hidden('is_scan[]',"");
					hidden += "<input id='is_unusual' type='hidden' name='is_unusual_"+info.sub_id+"' value='0'/> ";
					hidden += "</div>";
					items.push(hidden);
				});
				$(items).each(function(info,content){
					$("#scan_pick_form").append(content);
				});
				paixu_pick();
			}
		}
	});
}
//拣货单号扫描
function scan_pick_sn(e){
	e = e ? e : (window.event ? window.event : null);
	var v_scan_sn = $.trim($("input[type=text][name=pick_sn]").val());
	if (e.keyCode != 13)
	{
		return false;
	}
	if (v_scan_sn == '')
	{
		note2('请您扫描拣货单号！');
		return false
	}
	scan_pick();
}


function scan_location_code(e){
	e = e ? e : (window.event ? window.event : null);
	if (e.keyCode != 13){   return false;  }
	var v_scan_sn = $.trim($("input[type=text][name=location_code]").val());
	if (v_scan_sn == ''){   note2('请您扫描储位！');return false}
	paixu_savePos(v_scan_sn);
}
var v_scan_num2 = 0;
//扫描商品条形码
function scan_goods_barcode(e){
	e = e ? e : (window.event ? window.event : null);
	var v_scan_sn = $.trim($("input[type=text][name=goods_barcode]").val());
	if (e.keyCode != 13){   return false;  }
	getID("p_cell").innerHTML="";
	note2('');
	if (v_scan_sn == ''){   note2('请您扫描商品条形码！'); return false}
	var v_is_scan = document.getElementsByName("is_scan[]");
	var v_goods_barcode = document.getElementsByName("barcode[]");
	var v_product_num = document.getElementsByName("product_num[]");
	var v_scan_num = document.getElementsByName("scan_num[]");
	var v_pick_cell = document.getElementsByName("pick_cell[]");
	var v_subid = document.getElementsByName("sub_id[]");
	var v_flag = false;
	for (var i = 0; i < v_is_scan.length; i++){
		if (v_is_scan.item(i).checked == true){continue;}
		if (v_goods_barcode.item(i).value != v_scan_sn){continue;}
		v_flag = true;
		var scan_num = parseInt(v_scan_num.item(i).value) + 1;
		v_scan_num.item(i).value = scan_num;
		if (v_scan_num.item(i).value == v_product_num.item(i).value){
			v_is_scan.item(i).checked = true;
			document.getElementsByName('is_unusual_'+v_subid.item(i).value).item(0).disabled = true;
			v_scan_num2 = v_scan_num2 + 1;
		}
		// 显示订单格子号
		getID("p_cell").innerHTML=v_pick_cell.item(i).value;
		break;
	}
	$("input[type=text][name=goods_barcode]").val('');
	if (!v_flag){   note2('该拣货单中没有此商品'); return false;}
	scan_fini_one();
	if (v_scan_num2 != v_is_scan.length){return false;}
	if (confirm("拣货已完成，是否自动提交数据？")){document.scan_pick.submit();}
}

function markers(obj){
	$("input[name=is_unusual_"+obj+"]").val(1);
    $("#scan_pick_form").submit();
}

function scan_fini_one(){
    var lo_f_num = $("span[flg='scan_num_f_l']").html();
    var lo_d_num= $("span[flg='scan_num_d_l']").html();
    var now_lo_num = parseInt(lo_f_num)+1;
    $("span[flg='scan_num_f_l']").html(now_lo_num);
    if(now_lo_num == lo_d_num){
	note2("该储位拣货完成");
    }
    var p_f_num = $("span[flg='scan_num_f_p']").html();
    var p_d_num= $("span[flg='scan_num_d_p']").html();
    var now_f_num = parseInt(p_f_num)+1;
    $("span[flg='scan_num_f_p']").html(now_f_num);
    if(now_f_num == p_d_num){
	note2("该拣货单拣货完成");
    }
}
//]]>
</script>
<?php include(APPPATH.'views/common/rf_footer.php');?>