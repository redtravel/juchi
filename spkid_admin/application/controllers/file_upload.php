<?php

class File_upload extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('file_upload_model');
    }
    public function index() {
        auth(array('file_upload_index'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms', get_friend_perm());
        $filter = $this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        $keys  = array("id","name","path","type","created");
        $filter = fill_filter( $filter, $keys, true );
        if( !empty($filter['search_keys']) ) $filter['sort_order'] = 'ASC';

        $filter = get_pager_param($filter);
        $data = $this->file_upload_model->list_f($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('file_upload/index', $data, TRUE);
            $data['error'] = 0;
            unset($data['index']);
            echo json_encode($data);
            return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('file_upload/index', $data);
    }

    public function scan(){
        $static = realpath(CREATE_HTML_PATH );

        $dir = $static. "/files/pdf" ;;

        $files = scandir($dir);

        $time = time();
        foreach($files as $index => $file){
            if ('.' == $file || '..' == $file)
                continue;
            //echo $file;
            $path = pathinfo($file);
            $ext = $path['extension'];
            $name = $path['filename'];
            //$name = iconv('GBK', 'utf-8', $name);
            $newName = $time.$index.rand(10, 200);
            $path = 'files/'.$ext.'-'.$newName.'.'.$ext;
            $newName = $static.'/'.$path;
            rename($dir.'/'.$file, $newName);
	    if( empty($name) ) $name = '点击修改名字';
            $data = array('name' => $name, 'path' => $path, 'type' => $ext, 'created' => date('Y-m-d H:i', $time));
            $this->file_upload_model->insert($data);
        }
        redirect('file_upload/index');
    }

    public function delete($pk_id) {
        auth('');
        $pk_id = intval($pk_id);
        $check = $this->file_upload_model->filter(array('id' => $pk_id));

        if (empty($check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        $this->file_upload_model->del(array('id' => $pk_id));
        sys_msg('操作成功', 2, array(array('href' => 'file_upload/index', 'text' => '返回列表页')));
    }

    public function editable() {
        if( ! auth('file_upload_editable'))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->file_upload_model->update( $data, $pk );
        die(json_encode(Array('success'=>true,'msg'=>'操作成功！', 'value'=>443)));
       
    }        

         /**
          * 将一维数组(key=>value)对应样子的，生成可以editable的select 数据源
          */
        function _to_js_json( $ary ){
            $tmp = array();
            foreach( $ary AS $key => $value )
                $tmp[] = '{value:"'.$key.'",text:"'.$value.'"}';
            $tmp = implode(',',$tmp);
            return '['.$tmp.'];';
        }

}

?>
