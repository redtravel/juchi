<?php
    /**
     * 导出excel
     * $file_name可为空
     * $arr_data 结构为array(arr_title,arr_data,arr_title,arr_data...)
     */
    function export_excel_by_array($file_name,$arr_data=array())
    {
        if(empty($arr_data)) return;
        $file_name=empty($file_name)?"default":$file_name;
        $CI = &get_instance();
        $CI->load->library('PHPExcel');
        $CI->load->library('PHPExcel/IOFactory');
        $objPHPExcel =new PHPExcel(); 
        $objPHPExcel->getProperties()->setTitle("export")->setDescription("none");
        $objPHPExcel->setActiveSheetIndex(0);
        $row=1;//行索引
        //多个数组 
        foreach($arr_data as $item_arr)
        {
            $col = 0;
            $add_row_flag=false;
            //data 
            foreach($item_arr as $data)
            {
                //不是array 则不进行循环
                if(!is_array($data))
                {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data);
                    $col++;
                    $add_row_flag=true;
                    continue;
                }
                $col=0;
                foreach ($data as $val)
                {
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $val);
                    $col++;
                }
                $row++;
            }
            if($add_row_flag) 
            {
                $row++;
            }
        }
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        //发送标题强制用户下载文件
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$file_name.'.xls"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }

    /**
     * 导出xml
     * @param $file_name可为空
     * @param $arr_data 结构为array(arr_title,arr_data,arr_title,arr_data...)
     * @param $return_xml true:将xml以string形式返回 
     */
    function export_excel_xml($file_name,$arr_data,$return_xml=false)
    {
        if(empty($arr_data)) return;
        $file_name=empty($file_name)?"default":$file_name;
        $CI = &get_instance();
        //计算总行数
        $row_count=0;
        foreach($arr_data as $arr)
        {
            foreach($arr as $item)
            {
                if(is_array($item))
                {
                    $row_count+=count($arr); 
                }
                else{
                    $row_count+=1;
                }
                break;
            }
        }
        $data['row_count']=$row_count;
        $data['arr_data']=$arr_data;
        $xml=$CI->load->view('xml_template/xml_template',$data,true);
        if($return_xml===true)
        {
            return $xml;
        }
        header('Content-Disposition: attachment;filename="'.$file_name.'.xml"');
        echo $xml;
    }

    /**
     *  读取excel $filename 路径文件名 $encode 返回数据的编码 默认为utf8
     * @param type $filename
     * @param type $encode
     * @return type 
     */
    function read_excel_to_array($filepath) {
        $CI = &get_instance();
        $CI->load->library('PHPExcel');
        $CI->load->library('PHPExcel/IOFactory');

        $PHPExcel = new PHPExcel();
    //读取2007格式的Excel
        $PHPReader = new PHPExcel_Reader_Excel2007();
    //为了从上向下兼容，先判断是否为2007的，再判断是否为非2007的
        if (!$PHPReader->canRead($filepath)) {
            //非2007格式的Excel
            $PHPReader = new PHPExcel_Reader_Excel5();
            //判断是否为正确的Excel文件
            if (!$PHPReader->canRead($filepath)) {
                return array("error" => "1", "msg" => "no excel");
                exit();
            }
        }
    //filepath Excel文件路径
        $PHPExcel = $PHPReader->load($filepath);
    //转换为数组方便读取
        $arr = $PHPExcel->getSheet(0)->toArray();
        return array("error" => "0", "res" => $arr);
}

