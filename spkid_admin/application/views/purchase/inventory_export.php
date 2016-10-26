<<?php print $tag; ?>xml version="1.0"<?php print $tag; ?>>
<<?php print $tag; ?>mso-application progid="Excel.Sheet"<?php print $tag; ?>>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Created>2006-09-16T00:00:00Z</Created>
  <LastSaved>2006-09-16T00:00:00Z</LastSaved>
  <Version>15.00</Version>
 </DocumentProperties>
 <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
  <AllowPNG/>
  <RemovePersonalInformation/>
 </OfficeDocumentSettings>
 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
  <WindowHeight>9480</WindowHeight>
  <WindowWidth>21570</WindowWidth>
  <WindowTopX>0</WindowTopX>
  <WindowTopY>0</WindowTopY>
  <ProtectStructure>False</ProtectStructure>
  <ProtectWindows>False</ProtectWindows>
 </ExcelWorkbook>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="宋体" x:CharSet="134" ss:Size="11" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="s62">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
  </Style>
  <Style ss:ID="s67">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
   <Font ss:FontName="宋体" x:CharSet="134" ss:Size="11" ss:Color="#000000"/>
  </Style>
  <Style ss:ID="s68">
   <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
   <Font ss:FontName="宋体" x:CharSet="134" ss:Size="11" ss:Color="#333333"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Sheet1">
  <Table ss:ExpandedColumnCount="11" x:FullColumns="1"
   x:FullRows="1" ss:DefaultColumnWidth="54" ss:DefaultRowHeight="13.5">
   <Column ss:Index="2" ss:StyleID="s62" ss:AutoFitWidth="0" ss:Width="162.75"/>
   <Column ss:StyleID="s62" ss:AutoFitWidth="0" ss:Width="108.75"/>
   <Column ss:StyleID="s62" ss:AutoFitWidth="0" ss:Width="154.5"/>
   <Column ss:StyleID="s62" ss:AutoFitWidth="0" ss:Width="110.25"/>
   <Column ss:StyleID="s62" ss:AutoFitWidth="0" ss:Width="88.5"/>
   <Column ss:StyleID="s62" ss:AutoFitWidth="0" ss:Width="182.25"/>
   <Column ss:StyleID="s62" ss:AutoFitWidth="0" ss:Width="120"/>
   <Column ss:StyleID="s62" ss:AutoFitWidth="0" ss:Width="48"/>
   <Column ss:StyleID="s62" ss:AutoFitWidth="0"/>
   <Row ss:AutoFitHeight="0"/>
   <Row ss:AutoFitHeight="0" ss:Height="24">
    <Cell ss:Index="3" ss:MergeAcross="3"><Data ss:Type="String">所有商品余库</Data></Cell>
   </Row>
   <Row ss:Index="4" ss:AutoFitHeight="0" ss:Height="18">
    <Cell ss:Index="2"><Data ss:Type="String">名称</Data></Cell>
    <Cell><Data ss:Type="String">款号</Data></Cell>
    <Cell><Data ss:Type="String">条形码</Data></Cell>
    <Cell><Data ss:Type="String">品牌</Data></Cell>
    <Cell><Data ss:Type="String">后台分类</Data></Cell>
    <Cell><Data ss:Type="String">色码</Data></Cell>
    <Cell><Data ss:Type="String">批次</Data></Cell>
    <Cell><Data ss:Type="String">储位</Data></Cell>
    <Cell><Data ss:Type="String">可售</Data></Cell>
    <Cell><Data ss:Type="String">实库</Data></Cell>
   </Row>
<?php 
   foreach($list as $goods): 
?>

   <Row ss:AutoFitHeight="0" ss:Height="18">
    <Cell ss:Index="2" ss:StyleID="s68"><Data ss:Type="String"><?=$goods->product_name?></Data></Cell>
    <Cell ss:StyleID="s67"><Data ss:Type="String"><?=$goods->product_sn?></Data></Cell>
    <Cell ss:StyleID="s67"><Data ss:Type="String"><?=$goods->provider_barcode;?></Data></Cell>
    <Cell ss:StyleID="s67"><Data ss:Type="String"><?=$goods->brand_name?></Data></Cell>
    <Cell ss:StyleID="s67"><Data ss:Type="String"><?=$goods->category_name?></Data></Cell>
    <Cell ss:StyleID="s67"><Data ss:Type="String"><?=$goods->color_name.'-'.$goods->size_name; ?></Data></Cell>
    <Cell ss:StyleID="s67"><Data ss:Type="String"><?=$goods->batch_code?></Data></Cell>
    <Cell ss:StyleID="s67"><Data ss:Type="String"><?=$goods->location_name?></Data></Cell>
    <Cell><Data ss:Type="Number"><?=$goods->gl_num?></Data></Cell>
    <Cell><Data ss:Type="Number"><?=$goods->product_number?></Data></Cell>
   </Row>
<?php endforeach; ?>
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <Unsynced/>
   <Print>
    <ValidPrinterInfo/>
    <PaperSizeIndex>9</PaperSizeIndex>
    <HorizontalResolution>600</HorizontalResolution>
    <VerticalResolution>600</VerticalResolution>
   </Print>
   <Selected/>
   <Panes>
    <Pane>
     <Number>3</Number>
     <ActiveRow>13</ActiveRow>
     <ActiveCol>4</ActiveCol>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>
