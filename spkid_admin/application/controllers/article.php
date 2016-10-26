<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class Article extends CI_Controller
{
    public function __construct ()
    {
            parent::__construct();
            $this->admin_id = $this->session->userdata('admin_id');
            if ( ! $this->admin_id )
            {
                    redirect('index/login');
            }
            $this->load->model('article_model');
    }

    public function cat_index ()
    {
        auth(array('art_cat_view','art_cat_edit'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_art_perm());
        $this->load->helper('category');
        $all_cat = $this->article_model->all_cat();
        $all_cat = category_tree($all_cat , 0 , 'cat_id' , 'parent_id');
        $all_cat = category_flatten($all_cat, '---- ');
        $this->load->vars('list', $all_cat);
        $this->load->view('article/cat_list');
    }

    public function cat_add ()
    {
        auth('art_cat_edit');
        $all_cat = $this->article_model->all_cat();
        $this->load->vars('all_cat',$all_cat);
        $this->load->view('article/cat_add');
    }


    public function proc_cat_add(){
        auth('art_cat_edit');
        $data['parent_id']  = $this->input->post('parent_id');
        $data['cat_name']  = $this->input->post('cat_name');
        $data['keywords']  = $this->input->post('keywords');
        $data['cat_desc']  = $this->input->post('cat_desc');
        $data['sort_order']  = $this->input->post('sort_order');
        $data['is_use']  = $this->input->post('is_use');
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cat_name', '分类名称', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        if ($data['parent_id'] != 0) {
                $cat = $this->article_model->cat_filter(array('cat_id'=>$data['parent_id']));
                if (!$cat) {
                        sys_msg('父分类不存在', 1);
                }
        }
        $cat_id = $this->article_model->cat_insert($data);
        sys_msg('操作成功', 0, array(array('text'=>'返回列表','href'=>base_url().'article/cat_index')));
    }

    
    public function cat_del($cat_id)
    {
        auth('art_cat_edit');
        $cat_id = intval($cat_id);
        $test = $this->input->post('test');
        $check = $this->article_model->cat_filter(array('cat_id' => $cat_id));
        $parent_check = $this->article_model->cat_filter(array('parent_id' => $cat_id));
        $article_check = $this->article_model->article_filter(array('cat_id' => $cat_id));
        if(!empty ($article_check)){
            sys_msg('无法删除',1);
            return;
        }
        if(!empty ($parent_check)){
            sys_msg('无法删除',1);
            return;
        }
        if(empty ($check)){
            sys_msg('记录不存在',1);
            return;
        }
        if($test) sys_msg('');
        $this->article_model->cat_del(array('cat_id' => $cat_id));
         sys_msg('操作成功', 0, array(array('text'=>'返回列表','href'=>base_url().'article/cat_index')));
    }



    public function cat_edit($cat_id){
        auth(array('art_cat_view','art_cat_edit'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_art_perm());
        $cat_id = intval($cat_id);
        $check = $this->article_model->cat_filter(array('cat_id' => $cat_id));
        if(empty ($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $all_cat = $this->article_model->all_cat();
        $this->load->vars('all_cat',$all_cat);
        $this->load->vars('check',$check);
        $this->load->view('article/cat_edit');
    }

    public function proc_cat_edit($cat_id){
        auth('art_cat_edit');
        $cat_id = intval($cat_id);
        $check = $this->article_model->cat_filter(array('cat_id' => $cat_id));
        if(empty ($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $data['parent_id']  = $this->input->post('parent_id');
        $data['cat_name']  = $this->input->post('cat_name');
        $data['keywords']  = $this->input->post('keywords');
        $data['cat_desc']  = $this->input->post('cat_desc');
        $data['sort_order']  = $this->input->post('sort_order');
        $data['is_use']  = $this->input->post('is_use');
        $data['create_admin'] = $this->admin_id;
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cat_name', '分类名称', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        if ($data['parent_id'] != 0) {
                $cat = $this->article_model->cat_filter(array('cat_id'=>$data['parent_id']));
                if (!$cat) {
                        sys_msg('父分类不存在', 1);
                }
        }
        $cat_id = $this->article_model->cat_update($data , $cat_id);
        sys_msg('操作成功', 0, array(array('text'=>'返回列表','href'=>base_url().'article/cat_index')));
    }

    function article_index(){
        auth(array('art_view','art_edit'));
        $this->load->helper('perms_helper');
        $this->load->helper('category');
        $this->load->vars('perms' , get_art_perm());
        $filter = $this->uri->uri_to_assoc(3);
        $cat_id = $this->input->post("cat_id");
        if(!empty($cat_id)) $filter['cat_id'] = $cat_id;
        $author = $this->input->post("author");
        if(!empty($author)) $filter['author'] = $author;
        $is_use = $this->input->post("is_use");
        if(!empty($is_use)) $filter['is_use'] = $is_use;
        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;

        $filter = get_pager_param($filter);
        $data = $this->article_model->article_list($filter);

        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('article/article_list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }
        $data['full_page'] = TRUE;
        $this->load->vars('all_cat',category_flatten(category_tree($this->article_model->all_cat(),0,'cat_id','parent_id'),'-- '));
        $this->load->view('article/article_list', $data);
    }

    function article_add()
    {
        auth('art_edit');
        $this->load->model('admin_model');
        $author = $this->admin_model->filter(array('admin_id'=>$this->admin_id));
        $this->load->helper('category');
        $all_cat = $this->article_model->all_cat();
        $all_cat = category_tree($all_cat , 0 , 'cat_id' , 'parent_id');
        $all_cat = category_flatten($all_cat, '---- ');
        $this->load->vars('all_cat',$all_cat);
        $this->load->vars('admin_name',$author->admin_name);
        $this->load->library('ckeditor');
        $this->load->view('article/article_add');
    }

    function proc_article_add()
    {
        auth('art_edit');
        $data['cat_id'] = $this->input->post('cat_id');
        $data['title'] = $this->input->post('title');
        $data['title_color'] = $this->input->post('title_color');
        $data['title_size'] = $this->input->post('title_size');
        $data['content'] = $this->input->post('content');
        $data['author'] = $this->input->post('author');
        $data['keywords'] = $this->input->post('keywords');
        $data['sort_order'] = $this->input->post('sort_order');
        $data['url'] = $this->input->post('url');
        $data['source'] = $this->input->post('source');
        $data['is_use'] = $this->input->post('is_use');
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cat_id', '分类', 'required');
        $this->form_validation->set_rules('title', '文章标题', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $cat = $this->article_model->cat_filter(array('cat_id'=>$data['cat_id']));
        if (!$cat) {
                sys_msg('分类不存在', 1);
        }
        $article_id = $this->article_model->article_insert($data);
        sys_msg('操作成功', 0, array(array('text'=>'返回列表','href'=>base_url().'article/article_index')));
    }

    function article_edit($article_id){
        auth(array('art_view','art_edit'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_art_perm());
        $article_id = intval($article_id);
        $check = $this->article_model->article_filter(array('article_id' => $article_id));
        if(empty ($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->helper('category');
        $all_cat = $this->article_model->all_cat();
        $all_cat = category_tree($all_cat , 0 , 'cat_id' , 'parent_id');
        $all_cat = category_flatten($all_cat, '---- ');
        $this->load->vars('all_cat',$all_cat);
        $this->load->library('ckeditor');
        $this->load->vars('check',$check);
        $this->load->view('article/article_edit');
    }
    
    function proc_article_edit($article_id)
    {
        auth('art_edit');
        $article_id = intval($article_id);
        $check = $this->article_model->article_filter(array('article_id' => $article_id));
        if(empty ($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $data['cat_id'] = $this->input->post('cat_id');
        $data['title'] = $this->input->post('title');
        $data['title_color'] = $this->input->post('title_color');
        $data['title_size'] = $this->input->post('title_size');
        $data['content'] = $this->input->post('content');
        $data['author'] = $this->input->post('author');
        $data['keywords'] = $this->input->post('keywords');
        $data['sort_order'] = $this->input->post('sort_order');
        $data['url'] = $this->input->post('url');
        $data['source'] = $this->input->post('source');
        $data['is_use'] = $this->input->post('is_use');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('cat_id', '分类', 'required');
        $this->form_validation->set_rules('title', '文章标题', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $cat = $this->article_model->cat_filter(array('cat_id'=>$data['cat_id']));
        if (!$cat) {
                sys_msg('分类不存在', 1);
        }
        $this->article_model->article_update($data,$article_id);
        sys_msg('操作成功', 0, array(array('text'=>'返回列表','href'=>base_url().'article/article_index')));
    }

    function article_del($article_id)
    {
        auth('art_edit');
        $article_id = intval($article_id);
        $test = $this->input->post('test');
        $check = $this->article_model->article_filter(array('article_id' => $article_id));
        if(empty ($check)){
            sys_msg('记录不存在',1);
            return;
        }
        if($test) sys_msg('');
        $this->article_model->article_del(array('article_id' => $article_id));
        sys_msg('操作成功', 0, array(array('text'=>'返回列表','href'=>base_url().'article/article_index')));
    }
    
    public function edit_cat_field()
    {
            auth('art_cat_edit');
            switch (trim($this->input->post('field'))) {
                    case 'sort_order':
                            $val = intval($this->input->post('val'));
                            break;
                    
                    default:
                            $val = NULL;
                            break;
            }
            print json_encode(proc_edit('article_model','cat_id', array('sort_order'), $val,'cat_filter','cat_update'));
    }
    public function edit_field()
    {
            auth('art_edit');
            switch (trim($this->input->post('field'))) {
                case 'sort_order':
                        $val = intval($this->input->post('val'));
                        break;
                
                default:
                        $val = NULL;
                        break;
            }
            print json_encode(proc_edit('article_model','article_id', array('sort_order'), $val,'article_filter','article_update'));
    }

}
