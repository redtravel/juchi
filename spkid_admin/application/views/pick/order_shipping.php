<<?php print $tag; ?>xml version="1.0"<?php print $tag; ?>>
<<?php print $tag; ?>mso-application progid="Excel.Sheet"<?php print $tag; ?>>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Created>2006-09-16T00:00:00Z</Created>
  <LastSaved>2013-03-01T10:33:11Z</LastSaved>
  <Version>14.00</Version>
 </DocumentProperties>
 <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
  <AllowPNG/>
  <RemovePersonalInformation/>
 </OfficeDocumentSettings>
 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
  <WindowHeight>8010</WindowHeight>
  <WindowWidth>14805</WindowWidth>
  <WindowTopX>240</WindowTopX>
  <WindowTopY>105</WindowTopY>
  <ProtectStructure>False</ProtectStructure>
  <ProtectWindows>False</ProtectWindows>
 </ExcelWorkbook>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="宋体" x:CharSet="134" ss:Size="12" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="s16">
   <Font ss:FontName="宋体" x:CharSet="134" ss:Size="12" ss:Color="#000000"
    ss:Bold="1"/>
   <Interior ss:Color="#92CDDC" ss:Pattern="Solid"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Sheet1">
  <Table ss:ExpandedColumnCount="12" x:FullColumns="1"
   x:FullRows="1" ss:DefaultColumnWidth="54" ss:DefaultRowHeight="13.5">
   <Column ss:AutoFitWidth="0" ss:Width="104.25"/>
   <Column ss:AutoFitWidth="0" ss:Width="86.25"/>
   <Column ss:AutoFitWidth="0" ss:Width="62.25"/>
   <Column ss:AutoFitWidth="0" ss:Width="103.5"/>
   <Column ss:AutoFitWidth="0" ss:Width="81.75"/>
   <Column ss:AutoFitWidth="0" ss:Width="139.5"/>
   <Column ss:AutoFitWidth="0" ss:Width="237"/>
   <Column ss:AutoFitWidth="0" ss:Width="73.5"/>
   <Column ss:AutoFitWidth="0" ss:Width="79.5"/>
   <Column ss:AutoFitWidth="0" ss:Width="78.75"/>
   <Column ss:AutoFitWidth="0" ss:Width="84"/>
   <Column ss:AutoFitWidth="0" ss:Width="83"/>
   <Row>
    <Cell ss:StyleID="s16"><Data ss:Type="String">订单号</Data></Cell>
    <Cell ss:StyleID="s16"><Data ss:Type="String">运单号</Data></Cell>
    <Cell ss:StyleID="s16"><Data ss:Type="String">发货状态</Data></Cell>
    <Cell ss:StyleID="s16"><Data ss:Type="String">发货时间</Data></Cell>
    <Cell ss:StyleID="s16"><Data ss:Type="String">快递公司</Data></Cell>
    <Cell ss:StyleID="s16"><Data ss:Type="String">送货地址</Data></Cell>
    <Cell ss:StyleID="s16"><Data ss:Type="String">配送地址</Data></Cell>
    <Cell ss:StyleID="s16"><Data ss:Type="String">收货人</Data></Cell>
    <Cell ss:StyleID="s16"><Data ss:Type="String">包裹金额</Data></Cell>
    <Cell ss:StyleID="s16"><Data ss:Type="String">付款方式</Data></Cell>
    <Cell ss:StyleID="s16"><Data ss:Type="String">待收货款金额</Data></Cell>
    <Cell ss:StyleID="s16"><Data ss:Type="String">订单重量</Data></Cell>
   </Row>
<?php foreach ($list as $row) : ?>
   <Row>
   <Cell><Data ss:Type="String"><?php print $row->order_sn; ?></Data></Cell>
   <Cell><Data ss:Type="String"><?php print $row->invoice_no; ?></Data></Cell>
   <Cell><Data ss:Type="String"><?php print ($row->shipping_status) ? '已发货' : '未发货'; ?></Data></Cell>
   <Cell><Data ss:Type="String"><?php print $row->shipping_date; ?></Data></Cell>
   <Cell><Data ss:Type="String"><?php print $row->shipping_name; ?></Data></Cell>
   <Cell><Data ss:Type="String"><?php print $row->province." ".$row->city." ".$row->district; ?></Data></Cell>
   <Cell><Data ss:Type="String"><?php print $row->address; ?></Data></Cell>
   <Cell><Data ss:Type="String"><?php print $row->consignee; ?></Data></Cell>
   <Cell><Data ss:Type="Number"><?php print $row->order_amount; ?></Data></Cell>
   <Cell><Data ss:Type="String"><?php print $row->pay_name; ?></Data></Cell>
   <Cell><Data ss:Type="Number"><?php print $row->paid_money; ?></Data></Cell>
   <Cell><Data ss:Type="Number"><?php print $row->order_weight_unreal; ?></Data></Cell>
   </Row>
<?php endforeach; ?>
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <Print>
    <ValidPrinterInfo/>
    <PaperSizeIndex>9</PaperSizeIndex>
    <VerticalResolution>0</VerticalResolution>
   </Print>
   <Selected/>
   <Panes>
    <Pane>
     <Number>3</Number>
     <ActiveRow>1</ActiveRow>
     <ActiveCol>2</ActiveCol>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
 <Worksheet ss:Name="Sheet2">
  <Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1"
   x:FullRows="1" ss:DefaultColumnWidth="54" ss:DefaultRowHeight="13.5">
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
 <Worksheet ss:Name="Sheet3">
  <Table ss:ExpandedColumnCount="1" ss:ExpandedRowCount="1" x:FullColumns="1"
   x:FullRows="1" ss:DefaultColumnWidth="54" ss:DefaultRowHeight="13.5">
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>
