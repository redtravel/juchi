<<?php echo $xtag;?>xml version="1.0"<?php echo $xtag;?>>
<<?php echo $xtag;?>mso-application progid="Excel.Sheet"<?php echo $xtag;?>>
<Workbook
  xmlns:x="urn:schemas-microsoft-com:office:excel"
  xmlns="urn:schemas-microsoft-com:office:spreadsheet"
  xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">

<Styles>
 <Style ss:ID="Default" ss:Name="Normal">
  <Alignment ss:Vertical="Bottom"/>
  <Borders/>
  <Font/>
  <Interior/>
  <NumberFormat/>
  <Protection/>
 </Style>
 <Style ss:ID="s27">
  <Font x:Family="Swiss" ss:Color="#0000FF" ss:Bold="1"/>
 </Style>
   <Style ss:ID="s64">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
  </Style>
</Styles>

 <Worksheet ss:Name="Sheet1">
  <ss:Table>
   <Row>
    <Cell ss:MergeAcross="7" ss:StyleID="s64" colspan="8"><Data ss:Type="String"><?php echo $title;?></Data></Cell>
   </Row>
   <ss:Row>
   <?php foreach($table_heads as $th): ?>    
    <ss:Cell  ss:StyleID="s27"><Data ss:Type="String"><?php echo $th;?></Data></ss:Cell>
   <?php endforeach; ?>
   <?php if($th_old_money):?><ss:Cell  ss:StyleID="s27"><Data ss:Type="String"><?php echo $th_old_money;?></Data></ss:Cell><?php endif;?>
   </ss:Row>
   <?php foreach($accounts as $as):?>
   <ss:Row>
     <?php foreach($as as $a):?>
    <ss:Cell><Data ss:Type="String"><?php echo $a;?></Data></ss:Cell>
	<?php endforeach; ?>
	</ss:Row>
      <?php endforeach; ?>
   </ss:Table>
 </Worksheet>
</Workbook>