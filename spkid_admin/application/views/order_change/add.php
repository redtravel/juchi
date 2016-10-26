<?php include(APPPATH.'views/common/header.php'); ?>
	<script type="text/javascript" src="public/js/listtable.js"></script>
	<script type="text/javascript" src="public/js/utils.js"></script>
	<script type="text/javascript" src="public/js/region.js"></script>

	<style type="text/css">
        .dis{display:block;}
		.inline{display:inline;}
		.none{display:none;}
    </style>

	<script type="text/javascript">
		//<![CDATA[
		var alertflag = 0;
		var lastobj = null;

		$(function(){
            load_order_data();
	    });

	    function load_order_data(){
	        var order_sn = $.trim($('input[type=text][name=order_sn]').val());

	        if (order_sn == '') return false;
	        $.ajax({
	            url: '/order_change/get_order_data',
	            data: {order_sn:order_sn,rnd:new Date().getTime()},
	            dataType: 'json',
	            type: 'POST',
	            success:function(result){
	                if(result.msg) {alert(result.msg)};
	                if(result.error == 0)
	                {
	                	document.getElementById('listDiv').innerHTML = result.content;
	                	//$('#listDiv').html(result.content);
						//$('input[type=text][name=hope_time]').datepicker({dateFormat:'yy-mm-dd',changeMonth:true,changeYear:true,nextText:'',prevText:'',yearRange:'-60:+0'});
	                }
	            }
	        });
	        return false;
	    }

		function check_add(){
	        var eles=document.forms['productForm'].elements;
		   	var valid = false;
		   	for(i=0; i< eles.length; i++){
		            if(eles[i].type=="text" && eles[i].name.substr(0,4)=="num_" && eles[i].value >0 ){
		                valid = true;
		                break;
		            }
		   	}

		   	if(!valid){
		            alert("没有选择要换货的商品!");
		            return false;
		   	}
	    }


		function handleOnFucus(obj)
		{
			if(lastobj == obj.name)
			{
				alertflag = 0;
			}
			lastobj = null;
		}

		function checkmax(obj, aid_str, maxvalue, product_str, maxproduct)
		{
			if(alertflag == 1)
			{
				alertflag = 0;
				return false;
			}

			lastobj = obj.name;
			if(Utils.isInt(obj.value) == false || parseInt(obj.value) < 0)
			{
				alertflag = 1;
				alert("不是有效的换货数量!");
				obj.focus();
				return false;
			}

			lastobj = null;
		}

		function addchangeitem(obj)
		{
			var objvalue = $('#sel_'+obj).val();
			var numvalue = $('#sum_'+obj).val();

			if(objvalue == 0)
			{
				alert('请选择要更换的商品');
				return false;
			}
			if($('#tr_'+objvalue).attr('class')!='none')
			{
				alert('要更换的商品已经在编辑区，不能重复添加');
				return false;
			}
			$('#tr_'+objvalue).removeClass();
            $('#num_'+objvalue).val(numvalue);
		}

		function delchangeitem(obj)
		{
			$('#num_'+obj).val(0);
			$('#tr_'+obj).attr('class','none');
		}

		//]]>
	</script>
	<div class="main">
		<div class="main_title"><span class="l">换货单管理 &gt;&gt; 新增换货单</span><span class="r">[ <a href="/order_change">返回列表 </a>]</span></div>
		<div class="produce">
		<div class="pc base">
		<div class="search_row">
			订单编号：<input type="text" class="tl" name="order_sn" value="<?php print isset($order_info)?$order_info->order_sn:'' ?>" />
			<input type="button" class="am-btn am-btn-primary" value="载入" onclick="load_order_data()" />
		</div>
		<div class="blank5"></div>
		<div id="listDiv" style="background-color:#fff;">

		</div>
		</div></div>
	</div>
<?php include_once(APPPATH.'views/common/footer.php'); ?>
