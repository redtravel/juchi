<?php include(APPPATH.'views/common/header.php'); ?>
<style type="text/css" media="all">

	.body{background-color:#FFF;}
        body{
            font-size: 12px;
        }
        table.dingdan_nr td{padding:4px;};

</style>
<script type="text/javascript">
	$(function(){window.print();});
</script>

		<?php $i=0;
                foreach($list as $row):$i+=1;
                ?>
			<div align="center" class="full_width" id="printer_order" style="page-break-after:always">
                            
                            <table width="100%" cellspacing="0" cellpadding="5" border="1" class="dingdan_nr" style="font-size: 12px;">
                                <tbody>
                                    <tr>
                                        <td colspan="2" height="68" align="right" valign="bottom">
                                            <?=$row->sn?><br><?=$row->pick_cell?>
                                        </td>
                                        <td colspan="2" valign="bottom">
                                            <img src="index/barcode/<?php print $row->mailno; ?>.html">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            寄件： 上海欧思蔚奥医疗器材有限公司<br>
                                            电话： 400-9905-920
                                        </td>
                                        <td>
                                            原寄地：<br>上海市
                                        </td>
                                            
                                    </tr>
                                    <tr>
                                        <td colspan="3" rowspan="2" valign="top">
                                            收件：<?php print $row->consignee; ?>
                                            <?php print $row->mobile; ?> <br>
                                            <?php print $row->address; ?>
                                        </td>
                                        <td>
                                            目的站：<br><?php if (!empty($row->dist_code)){
					    $dist_arr = explode("-", $row->dist_code); 
					    echo $dist_arr[1];
					    } ?>
                                        </td>                                           
                                    </tr>
                                    <tr>                                        
                                        <td width="16%">
                                            运输方式：<br><?=$row->transportType?>
                                        </td>                                           
                                    </tr>                              
                                        <tr>
                                            <td width="10%" align="center" height="10">件数</td>
                                            <td width="20%" align="center">计费重量</td>
                                            <td width="20%" align="center">运费</td>
                                            <td width="20%" align="center">费用合计</td>
                                        </tr>
                                        <tr>
                                            <td width="10%" align="center" height="10"><?php print $row->goods_num; ?></td>
                                            <td width="20%" align="center">&nbsp;</td>
                                            <td width="20%" align="center">&nbsp;</td>
                                            <td align="center">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">付款方式：月结 </td>
					    <td style="border-left:1px #ffffff solid;">已验视</td>
                                            <td>收件员：</td>
                                        </tr>
                                        
                                        <tr>
                                            <td width="10%" colspan="2" rowspan="2">保价金额：<?=$row->insuranceValue?>￥<br>
                                                代收货款：<?php print $row->codAmount; ?>￥<br>
                                                代收账号：<?php if($row->codAmount > 0): ?>98060154740008605<?php else: ?><br>&nbsp;<?php endif; ?></td>                                            
                                            <td width="20%">收方签名：<br><span style="margin-left:25px;">月</span><span style="margin-left:15px;">日</span></td>
                                            <td width="10%" valign="top">派件员：</td>
                                        </tr>
                                        <tr>                                                                                       
                                            <td width="20%" colspan="2">备注栏 ：</td>
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="4" height="20"></td>
                                        </tr>
                                    
                                    <tr>
                                        <td colspan="4" valign="bottom" align="right">
                                            <img src="index/barcode/<?php print $row->mailno; ?>.html">                                       
                                            件数:<?php print $row->goods_num; ?>
                                        </td>
                                    </tr>                                  
                                    <tr>
                                        <td colspan="4" height="68" valign="top">
                                            收件：<?php print $row->consignee; ?> <?php print $row->mobile; ?> <br><?php print $row->address; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" valign="top">
                                            寄   件：上海欧思蔚奥医疗器材有限公司<br>400-9905-920<br>
                                            上海市 上海市 浦东新区 周浦镇 建韵路618号3幢楼305室
                                        </td>
                                        <td rowspan="2" valign="top">
                                            <img src="public/images/dbkd.jpg">
                                        </td>                                           
                                    </tr>
                                    <tr>                                        
                                        <td colspan="3">
                                            货物名称：医疗器械
                                        </td>                                           
                                    </tr>
                                </tbody>
				</table>
                            
			</div>
                
		<?php endforeach;?>		
<?php include_once(APPPATH.'views/common/footer.php'); ?>
