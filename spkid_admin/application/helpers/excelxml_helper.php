<?php
/**
 * 处理excel xml公用函数 of excelxml_helper
 *
 * @author Carol
 * @date    2013-3-1
 */

    /**
     * 读取xml
     * @param string        $file       文件path
     * @param int||array    $key_size   要取的excel xml的列数||每列对应的key，不填默认index
     * @return array        array(data,msg)
     */
    function read_xml( $file , $key_size = 0  ){
	if (!file_exists($file)) {
		sys_msg('文件不存在', 1);
	}
        $content = file_get_contents($file);
	$content = preg_replace('/&.*;/','',$content);
	$dom = new SimpleXMLElement($content);
	$dom->registerXPathNamespace('c', 'urn:schemas-microsoft-com:office:spreadsheet');
	$rows = $dom->xpath('//c:Workbook//c:Worksheet//c:Table//c:Row');
        
        $xml_array = array();//xml array data
        $col_size = 0;
        if (is_array($key_size)) {
            $col_size = count($key_size);
        } else {
            $col_size = $key_size;
        }
        try {
            foreach ($rows as $key => $row) {
                $xml_row = array();
                foreach ($row as $k => $cell) {
                    if ( $col_size > 0 && $k > $col_size ){
                        continue;
                    }
                    $xml_row[] = trim(strval($cell->Data));
                }
                
                if (!isset($xml_row[0]) || empty($xml_row[0])) continue;
                if ( !empty($key_size) ) {
                    $xml_row = array_pad($xml_row, $col_size, '');
                    $xml_row = array_slice($xml_row, 0, $col_size);
                    if(is_array($key_size ) ){
                        $xml_row = array_combine($key_size,$xml_row);
                    }
                }
                $xml_array[] = $xml_row;
            }
        } catch (Exception $exc) {
            sys_msg("读取Excel Xml发生异常：".$exc->getTraceAsString(), 1);
            return;
        }
        if ( empty($xml_array)) {
            sys_msg("文件中无数据，请检查", 1);
            return;
        }else {
            return $xml_array;
        }
        
    }


?>
