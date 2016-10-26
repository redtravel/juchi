<?php
//可添加 可编辑 可搜索 为列表列 快速编辑 
class Generate_code extends CI_Controller {

    // 每一列的默认操作选项
    var $ary_op_names = Array(
        'add'=>Array ('type'=>'add','name'=>'可添加','defaults'=>Array(' checked=true'),'pk_disable'=>true),
        'edit'=>Array ('type'=>'edit','name'=>'可编辑','defaults'=>Array(' checked=true'),'pk_disable'=>true),
        'search'=>Array ('type'=>'search','name'=>'可搜索','defaults'=>Array(' checked=false')),
        'index'=>Array ('type'=>'index','name'=>'为列表列','defaults'=>Array(' checked=true')),
        'editable'=>Array ('type'=>'editable','name'=>'快速编辑','defaults'=>Array(' checked=false'),'pk_disable'=>true),
    );
    var $ary_ops = Array();

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('generate_code_model');

        $this->ary_ops = array_keys( $this->ary_op_names );
    }

    public function index() {
        auth(array('generate_op'));
        $data = Array();
        $data['top_menus'] = $this->generate_code_model->get_menu( 0 );
        // 菜单权限分类
        //
        $this->load->view('generate_code/index', $data);
    }
    public function result(){
        $data = array('cov_files'=> array(), 'gen_files'=>array('走开','走开','走开','走开','走开','走开',));
        $a = $this->input->cookie('generate_files');
        $data['gen'] = false;
        if( !empty($a) ) {
            $a = unserialize($a);
            if( is_array($a) ) $data['gen_files'] = array_map(array($this,'get_rid_of_root'), $a);
            $data['gen'] = true;
        }

        $a = $this->input->cookie('generate_files_covered');
        if( !empty($a) ) {
            $a = unserialize($a);
            if( is_array($a) ) $data['cov_files'] = array_map(array($this,'get_rid_of_root'), $a);
        }

        $this->load->view('generate_code/result', $data);
    }
    private function get_rid_of_root($str){
        return str_replace(ROOT_PATH,'',$str);
    }
    /**
     * 生成代码
     */
    public function do_proc(){
        $data = array(); 

        $data = fill_filter( $data, array_merge(
            Array('table','code','name','parent_id','using_fields','pk', 'row_deletable','gen_model','gen_controller')
            ,$this->ary_ops
        ));

        // 有多少字段是需要操作的。
        // 默认所有字段

        if( !empty( $data['using_fields']) ){
            $data['fields_source'] = $data['fields_name'] = array();
            foreach( $data['using_fields'] AS $field ){
                $data['fields_name'][$field] = $this->input->post( $field.'_label' );
                $tmp = $this->input->post( $field.'_source_settings' );
                if( !empty($tmp) ) $data['fields_source'][$field] = $tmp;
            }
        }

        $data['fields'] = $this->generate_code_model->desc( $data['table'] );
        $data['fields'] = index_array( $data['fields'], 'name' );
        $data['templates'] = $this->_get_target_templates($data['code']);
        $data['code2'] = ucfirst( $data['code'] );

        // 有几个操作项 , 顺便创建目录
        $this->_mkdir( $data );
        // check FILES' existance, return exist files
        $data['exists_target_files'] = $this->target_file_exist( $data['templates'] );

        // add permission, return boolean
        $result = $this->_add_permission( $data );

        // for write file
        $this->load->helper('file');

        // generate add template
        if( isset($data['add']) ) $this->gen_add_template($data, 'add', $data['templates']['template_add']);
        else unset($data['templates']['template_add']);

        // generate edit template
        if( isset($data['edit']) ) $this->gen_add_template($data, 'edit', $data['templates']['template_edit']);
        else unset($data['templates']['template_edit']);

        // generate index template
        if( isset($data['index']) ) $this->gen_index_template($data, 'index', $data['templates']['template_index']);
        else unset($data['templates']['template_index']);

        // generate model
        if( isset($data['gen_model']) ) $this->gen_model_template( $data, $data['templates']['template_model'] );
        else unset($data['templates']['template_model']);

        // generate controller
        if( isset($data['gen_controller']) ) $this->gen_controller_template( $data, $data['templates']['template_controller'] );
        else unset($data['templates']['template_controller']);

        $this->input->set_cookie('generate_files', serialize($data['templates']), 20, SSO_COOKIE_DOMAIN);
        $this->input->set_cookie('generate_files_covered', serialize($data['exists_target_files']), 20, SSO_COOKIE_DOMAIN);
        redirect( 'generate_code/result' );

    }
    private function gen_controller_template( $data, $file ){
        $search_field_str = '';
        if( !empty($data['search']) ) $search_field_str = '"'.implode('","', $data['search']).'"';

        // field's data source from PARAM SETTINGS
        // TODO, field source for editable
        $fields_source = '';
        if( !empty($data['fields_source']) ){
            while( list($field, $code) = each($data['fields_source']) ){
                if( !empty($code) )
                    if( function_exists($code) )
                        $tmp = $code.'()';
                    else '$GLOBALS["'.$code.'"]';
                $fields_source .= <<<EOD

        \$data['fields_source']['{$field}'] = {$tmp};
        \$data['fields_source_data']['{$field}'] = \$this->_to_js_json({$tmp});
EOD;

            }
        }


        $content_model_index = <<<EOD
    public function index() {
        auth(array('{$data['permissions']['index']}'));
        \$this->load->helper('perms_helper');
        \$this->load->vars('perms', get_friend_perm());
        \$filter = \$this->uri->uri_to_assoc(3);

        // 关键字search_keys是搜索用的字段
        \$keys  = array({$search_field_str});
        \$filter = fill_filter( \$filter, \$keys, true );
        if( !empty(\$filter['search_keys']) ) \$filter['sort_order'] = 'ASC';

        \$filter = get_pager_param(\$filter);
        \$data = \$this->{$data['code']}_model->list_f(\$filter);
        if (\$this->input->post('is_ajax')) {
            \$data['full_page'] = FALSE;
            \$data['content'] = \$this->load->view('{$data['code']}/index', \$data, TRUE);
            \$data['error'] = 0;
            unset(\$data['index']);
            echo json_encode(\$data);
            return;
        }
{$fields_source}
        \$data['full_page'] = TRUE;
        \$this->load->view('{$data['code']}/index', \$data);
    }
EOD;
        $content_model_add='';
        if( isset($data['add'])){
        $adds = $data['add']; $add_str='';
        foreach( $adds AS $field ){
            $add_str .= <<<EOD

        \$data['{$field}'] = \$this->input->post('{$field}');
        # \$this->form_validation->set_rules('{$field}', '{$field}', 'trim|required');
EOD;

        }

        $content_model_add = <<<EOD
    public function add() {
        auth('{$data['permissions']['add']}');
        \$data = array();
{$fields_source}
        \$this->load->view('{$data['code']}/add',\$data);
    }

    public function proc_add() {
        auth('{$data['permissions']['edit']}');
        #\$this->load->library('form_validation');
{$add_str}

        #if (!\$this->form_validation->run()) {
        #    sys_msg(validation_errors(), 1);
        #}
        \$pk_id = \$this->{$data['code']}_model->insert(\$data);
        sys_msg('操作成功', 2, array(array('href' => '{$data['code']}/index', 'text' => '返回列表页')));
    }
EOD;
    }
        $content_model_edit='';
        if( isset($data['edit'])){
        $edits = $data['edit']; $edit_str = '';
        foreach( $edits AS $field ){
            $edit_str .= <<<EOD

        \$data['{$field}'] = \$this->input->post('{$field}');
        #\$this->form_validation->set_rules('{$field}', '{$field}', 'trim|required');
EOD;
        }

        $content_model_edit= <<<EOD
    public function edit(\$pk_id) {
        auth('{$data['permissions']['edit']}');
        \$data = array();
        \$pk_id = intval(\$pk_id);
        \$check = \$this->{$data['code']}_model->filter(array('{$data['pk']}' => \$pk_id));
        if (empty(\$check)) {
            sys_msg('记录不存在', 1);
            return;
        }
{$fields_source}
        \$this->load->vars('row', \$check);
        \$this->load->view('{$data['code']}/edit',\$data);
    }

    public function proc_edit(\$pk_id) {
        auth('{$data['permissions']['edit']}');
        \$this->load->library('form_validation');
$edit_str
        if (!\$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        \$this->{$data['code']}_model->update(\$data, \$pk_id);
        sys_msg('操作成功', 2, array(array('href' => '{$data['code']}/index', 'text' => '返回列表页')));
    }
EOD;
    }
        $content_model_delete= <<<EOD
    public function delete(\$pk_id) {
        auth('{$data['permissions']['delete']}');
        \$pk_id = intval(\$pk_id);
        \$check = \$this->{$data['code']}_model->filter(array('{$data['pk']}' => \$pk_id));

        if (empty(\$check)) {
            sys_msg('记录不存在', 1);
            return;
        }
        \$this->{$data['code']}_model->del(array('{$data['pk']}' => \$pk_id));
        sys_msg('操作成功', 2, array(array('href' => '{$data['code']}/index', 'text' => '返回列表页')));
    }
EOD;
        $content_model_editable= <<<EOD
    public function editable() {
        if( ! auth('{$data['permissions']['editable']}'))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        \$pk = \$this->input->post( 'pk' );
        \$name = \$this->input->post( 'name' );
        \$value = \$this->input->post( 'value' );
        \$data[\$name] = \$value;
        \$result = \$this->{$data['code']}_model->update( \$data, \$pk );
        die(json_encode(Array('success'=>true,'msg'=>'操作成功！', 'value'=>443)));
       
    }        
EOD;

        $content = <<<EOD
<?php

class {$data['code2']} extends CI_Controller {

    public function __construct() {
        parent::__construct();
        \$this->admin_id = \$this->session->userdata('admin_id');
        if (!\$this->admin_id) {
            redirect('index/login');
        }
        \$this->load->model('{$data['code']}_model');
    }
{$content_model_index}

{$content_model_add}

{$content_model_edit}

{$content_model_delete}

{$content_model_editable}

         /**
          * 将一维数组(key=>value)对应样子的，生成可以editable的select 数据源
          */
        function _to_js_json( \$ary ){
            \$tmp = array();
            foreach( \$ary AS \$key => \$value )
                \$tmp[] = '{value:"'.\$key.'",text:"'.\$value.'"}';
            \$tmp = implode(',',\$tmp);
            return '['.\$tmp.'];';
        }

}

?>
EOD;
        $this->_write_file( $file, $content );
    }
    // generate model template
    private function gen_model_template( $data, $file ){

        $content = <<<EOD
<?php

class {$data['code2']}_model extends CI_Model {

    public function list_f(\$filter) {
        \$from = " FROM {$data['table']}";
        \$where = " WHERE 1 ";

        \$param = generate_where_by_filter( \$filter, USE_SQL_OR );
        if( !empty(\$param) ) \$where .= "AND ".array_pop( \$param );

        \$filter['sort_by'] = empty(\$filter['sort_by']) ? '{$data['pk']}' : trim(\$filter['sort_by']);
        \$filter['sort_order'] = empty(\$filter['sort_order']) ? 'DESC' : trim(\$filter['sort_order']);

        \$sql = "SELECT COUNT(*) AS ct " . \$from . \$where;
        \$query = \$this->db_r->query(\$sql, \$param);
        \$row = \$query->row();
        \$query->free_result();
        \$filter['record_count'] = (int) \$row->ct;
        \$filter = page_and_size(\$filter);
        if (\$filter['record_count'] <= 0) {
            return array('list' => array(), 'filter' => \$filter);
        }
        \$sql = "SELECT * "
                . \$from . \$where . " ORDER BY " . \$filter['sort_by'] . " " . \$filter['sort_order']
                . " LIMIT " . (\$filter['page'] - 1) * \$filter['page_size'] . ", " . \$filter['page_size'];
        \$query = \$this->db_r->query(\$sql, \$param);
        \$list = \$query->result();
        \$query->free_result();
        return array('list' => \$list, 'filter' => \$filter);
    }

    public function insert(\$data) {
        \$this->db->insert('{$data['table']}', \$data);
        return \$this->db->insert_id();
    }

    public function filter(\$filter) {
        \$query = \$this->db_r->get_where('{$data['table']}', \$filter, 1);
        return \$query->row();
    }

    public function update(\$data, \$model_id) {
        \$this->db->update('{$data['table']}', \$data, array('{$data['pk']}' => \$model_id));
    }

    public function del(\$data) {
        \$this->db->delete('{$data['table']}', \$data);
    }

}

?>
EOD;
        $this->_write_file( $file, $content );
    }
    // 添加权限:没有则加上,有则更新
    private function _add_permission( &$data ){
        $this->load->model( 'action_model' );
        $data['permissions'] = Array();

        // 功能权限是否存在
        $perm = Array( 'action_code'=>$data['code'].'_manage', 'action_name'=>$data['name'], 
                'parent_id'=>$data['parent_id'],'menu_name'=> $data['name'],
                'url'=>'/'.$data['code'].'/index'
            );
        $action = $this->action_model->filter( array('action_code'=>$perm['action_code'] ));
        if( empty($action) ){
            $parent_id = $this->action_model->insert($perm);
        }else {
            $parent_id = $action->action_id;
            unset( $perm['action_code'] ); // OR cause sql error: duplicate entry for key 'unique_parent_action'
            $this->action_model->update( $perm, $action->action_id );
        }


        //看看有多少个子权限
        $sub_perms= Array();
        foreach( $this->ary_ops AS $op ){
            if( isset($data[$op]) ){
                array_push( $sub_perms, Array('parent_id'=>$parent_id,'action_code'=>$data['code'].'_'.$op, 
                    'action_name'=> $this->ary_op_names[$op]['name']) );
                $data['permissions'][$op] = $data['code'].'_'.$op;
            }
        }
        // 删除权限
        if( isset($data['row_deletable']) ){
                array_push( $sub_perms, Array('parent_id'=>$parent_id,'action_code'=>$data['code'].'_delete', 
                    'action_name'=> '可删除列') );
                $data['permissions']['delete'] = $data['code'].'_delete';
        }
    
        // 挨个检查子功能权限是否存在；没有则加上,有则更新
        foreach( $sub_perms AS $sub_perm ){
            $action = $this->action_model->filter( array('action_code'=>$sub_perm['action_code']) );
            if( empty($action) ){
                $this->action_model->insert( $sub_perm );
            }else $this->action_model->update( $sub_perm, $action->action_id );
        }
        return true;
    }
    // mkdir 
    private function _mkdir( &$data ){
        foreach( $this->ary_ops AS $key ){
            if ( empty($data[$key]) ){ 
                unset( $this->ary_ops[$key] );
                if ( isset( $data['templates']['template_'.$key] ) ) unset($data['templates']['template_'.$key]);
            }
            // 顺便创建目录
            if( isset( $data['templates']['template_'.$key] ) ) {
                $path_parts = pathinfo($data['templates']['template_'.$key]);
                if( !is_dir( $path_parts['dirname'] ) )
                    mkdir( $path_parts['dirname'] ) ||
                        sys_msg('创建目录失败，无法再目标位置创建目录：'.$path_parts['dirname'], 1);
            }
        }
    }
    // 检查要生成的文件是否存在
    public function target_file_exist($templates=Array()){
        if( empty($templates) ){
            $code = $this->input->post( 'code' );
            $templates = $this->_get_target_templates($code);
        }
        $exist_templates = Array();
        foreach( $templates AS $template_file ){
           file_exists( $template_file ) && array_push( $exist_templates, $template_file );
        }
        return empty($exist_templates)?FALSE:$exist_templates;
    }
    // 获得文件名
    private function _get_target_templates($code){
        return Array(
            'template_add'  => ROOT_PATH.APPPATH.'views/'.$code.'/add.php',
            'template_edit' => ROOT_PATH.APPPATH.'views/'.$code.'/edit.php',
            'template_index' => ROOT_PATH.APPPATH.'views/'.$code.'/index.php',
            'template_controller' => ROOT_PATH.APPPATH.'controllers/'.$code.'.php',
            'template_model' => ROOT_PATH.APPPATH.'models/'.$code.'_model.php',
        );
    }
    // 下一步
    public function step(){
        auth(array('generate_op'));
        $data = Array();
        $data['table'] = $this->input->post( 'table' ); // get table name
        $data['code'] = $this->input->post( 'code' ); // get code 
        $data['name'] = $this->input->post( 'name' ); // get name
        $data['parent_id'] = $this->input->post( 'parent_id' ); // get parent_id

        $data['top_menus'] = $this->generate_code_model->get_menu( 0 );
        $data['op_names'] = $this->ary_op_names;

        $data['fields'] = $this->generate_code_model->desc( $data['table'] );

        if( empty($data['fields']) ) {
            $data = Array( 'success'=> false, 'msg'=>'解析表失败' );
            sys_msg($data['msg'], 1);
}
        $this->load->view('generate_code/choose', $data);
    }
    public function check_table(){
        auth(array('generate_op'));
        $table = $this->input->post( 'table' ); // get table name

        $data = $this->generate_code_model->table_exists( $table );
        if( empty($data) ) $data = Array( 'success'=> false, 'msg'=>'解析表失败' );
        $data = Array( 'success'=> true, 'msg'=>'成功' );

        die(json_encode($data));
    }

    private function _get_input( $field_desc, $field, $t, $data ){
        $value = $select_ary = '';
        if( $t == 'edit' ) $value = '<?=$row->'.$field.';?>';
        if( $t == 'search' ) $select_ary = "array('0'=>'请选择')+";

        // 如果是下拉列表
        if( isset($data['fields_source'][$field]) && !empty($data['fields_source'][$field]) ){
            $str='<?php print form_dropdown("'.$field.'",'.$select_ary.'$fields_source["'.$field.'"],array("'.$value.'"),"data-am-selected");?>';
        }else{
            $str = <<<EOD
<input name="{$field_desc->name}" class="textbox require" id="{$field_desc->name}" value="{$value}" type="text"/>
EOD;
        }
        return $str;
    }
    private function _search_data( $data, $fields ){
        $result = Array('js'=>'','input'=>'');
        if( empty($fields) ) return $result;
        foreach( $fields AS $field ){
            $t = 'input';
            if( isset($data['fields_source'][$field]) && !empty($data['fields_source'][$field]) ) $t = 'select';
            $result['js'].= "listTable.filter['".$field."'] = $.trim($('".$t."[name=".$field."]').val());\n";
            $result['input'] .= $data['fields_name'][$field].'&nbsp;'.$this->_get_input( $data['fields'][$field], $field, 'search',$data )."\n";
        }
        return $result;
    }
    private function _index_data( $data, $fields ){
        $result = Array('title'=>'','rows'=>'');
        if( empty($fields) ) return $result;

        foreach( $fields AS $field ){
            $editable_date_str = '';
            $editable_class_postfix = '';

            // 如果可编辑字段是日期
            if( $data['fields'][$field]->type =='datetime')
                $editable_date_str = ' data-viewformat="yyyy-mm-dd" data-type="date"';

            if( $data['fields'][$field]->type =='text')
                $editable_date_str = ' data-type="textarea"';

            if( isset($data['fields_source'][$field]) ){
                $editable_date_str = ' data-type="select"';
                $editable_class_postfix = '_select_'.$field;
            }

            $result['title'] .= '<th width="100">'.$data['fields_name'][$field]."</th>\n";

            $result['rows'] .= '<td><span' . (in_array($field, $data['editable'])? $editable_date_str.' data-pk="<?php print $row->'.$data["pk"].
                '; ?>" data-name="'.$field.'" class="editable'.$editable_class_postfix.'" data-title="'.
                $data['fields_name'][$field].'" data-value="<?php print $row->'.
                $field.'; ?>"':'') . '><?php if(!empty($fields_source)&&isset($fields_source["'.$field.
                '"])&&isset($fields_source["'.$field.'"]["$row->'.$field.'"]))echo $fields_source["'.$field.
                '"]["$row->'.$field.'"] ;else echo $row->'.$field.'; ?></span></td>'."\n";

        }
        return $result;

    }
    // 生成是select的可编辑 js
    private function  _get_editable_select_js( $data ){
        $str = '';
        if( !empty($data['fields_source']) ){
            foreach( $data['fields_source'] AS $field=> $param_name ){
                $str .= 'var '.$field.'_ds = <?php echo $fields_source_data["'.$field.'"];?>';
$str .= <<<EOD

$('.editable_select_{$field}').editable({ 
    url: '/{$data['code']}/editable',
    source: {$field}_ds,
    success: function(response, newValue) {
        if(!response.success) return response.msg;
        if( response.value != newValue  ) return '操作失败';
    }
}); 
EOD;

            }
        }
        return $str;

    }


    // index template
    private function gen_index_template($data, $t='index', $file){
        // 搜索
        $search_fields = $data['search'];
        $search_data = $this->_search_data( $data, $search_fields );

        $datepicker_js = $this->_get_datepicker_js( $data, $search_fields ); // special datetime mark
        // 列表列
        $index_fields = $data['index'];
        $index_data = $this->_index_data( $data, $index_fields );
        // 可编辑字段
        $editable_fields = $data['editable'];
        // 可编辑字段是select
        $editable_select_str = $this->_get_editable_select_js( $data );
        // 列表行是否可以删除
        $row_delete_str =<<<EOD
                        <a class="del" href="javascript:void(0);" rel="{$data['code']}/delete/<?php print \$row->{$data['pk']}; ?>" title="删除" onclick="do_delete(this)"></a>
EOD;
        $row_delete_str = empty($data['row_deletable'])?'':$row_delete_str;

        $content = <<<EOD
<?php if (\$full_page): ?>
    <?php include(APPPATH . 'views/common/header.php'); ?>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/listtable.js"></script>

    <script type="text/javascript">
        //<![CDATA[
        listTable.filter.page_count = '<?php echo \$filter['page_count']; ?>';
        listTable.filter.page = '<?php echo \$filter['page']; ?>';
        listTable.url = '{$data['code']}/index';
        function search(){
            {$search_data['js']}
            listTable.loadList();
        }
        //]]>
    </script>
$datepicker_js
    <div class="main">
        <div class="main_title">
            <span class="l">{$data['name']}列表</span><span class="r"><a href="{$data['code']}/add" class="add">新增</a></span></div>
        <div class="blank5"></div>
        <div class="search_row">
            <form name="search" action="javascript:search(); ">
            {$search_data['input']}
                <input type="submit" class="am-btn am-btn-primary" value="搜索" />
            </form>
        </div>
        <div class="blank5"></div>
        <div id="listDiv">
        <?php endif; ?>
        <table width="1172" cellpadding=0 cellspacing=0 class="dataTable" id="dataTable">
            <tr>
                <td colspan="9" class="topTd"> </td>
            </tr>
            <tr class="row">
        {$index_data['title']}
                <th width="77">操作</th>
            </tr>
            <?php foreach (\$list as \$row): ?>
                <tr class="row">

        {$index_data['rows']}
                    <td>
                        <a href="{$data['code']}/edit/<?php print \$row->{$data['pk']}; ?>" title="编辑" class="edit"></a>
        {$row_delete_str}
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="9" class="bottomTd"> </td>
            </tr>
        </table>
        <div class="blank5"></div>
        <div class="page">
            <?php include(APPPATH . 'views/common/page.php') ?>
        </div>
<script>
// jquery editable 
function _editable(){
{$editable_select_str}

$('.editable').editable({ url: '/{$data['code']}/editable', emptytext:'',
        success: function(response, newValue) {
            if(!response.success) return response.msg;
            if( response.value != newValue ) return '操作失败';
        }
    });
}
listTable.func = _editable; // 分页加载后调用的函数名
_editable();
</script>

        <?php if (\$full_page): ?>
        </div>
    </div>
    <?php include_once(APPPATH . 'views/common/footer.php'); ?>
<?php endif; ?>
EOD;
        $this->_write_file( $file, $content );
    }
    private function _get_datepicker_js( $data, $fields ){
        $datepicker_js = ''; 
        foreach ( $fields AS $field ){
            if( $data['fields'][$field]->type =='datetime' ){
                $datepicker_js .=<<<EOD
    $('input[type=text][name={$field}]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});

EOD;


            }
        }
        // for datepicker
        if( !empty($datepicker_js) ){
            $datepicker_js = <<<EOD
<script type="text/javascript">
//<![CDATA[
$(function(){
{$datepicker_js}
});
//]]>
</script>
EOD;
        }
        return $datepicker_js;
    }
    private function gen_add_template($data, $t='add', $file){
        $content = '';
        // special field like date field
        $datepicker_js = $this->_get_datepicker_js( $data, $data[$t]); // special datetime mark

        // form url postfix for EDIT
        $form_url_postfix = '';
        if( $t == 'edit' ) $form_url_postfix = '/<?php echo $row->'.$data['pk'].'?>';

        // all input 
        foreach ( $data[$t] AS $field ){
            $input = $this->_get_input( $data['fields'][$field], $field, $t, $data );
            $content .=<<<EOD
        <tr>
            <td class="item_title">{$data['fields_name'][$field]}:</td>
            <td class="item_input">
{$input}
            </td>
        </tr>

EOD;
        }

        $content = <<<EOD
<?php include(APPPATH . 'views/common/header.php'); ?>
{$datepicker_js}
<div class="main">
    <div class="main_title"><span class="l"><span class="l">{$data['name']}管理 >> 增加/编辑</span></span> <span class="r"><a href="{$data['code']}/index" class="return r">返回列表</a></span></div>
    <div class="blank5"></div>
    <?php print form_open_multipart('{$data['code']}/proc_{$t}{$form_url_postfix}', array('name' => 'mainForm', 'onsubmit' => 'return check_form()')); ?>
    <table class="form" cellpadding=0 cellspacing=0>
        <tr>
            <td colspan=2 class="topTd"></td>
        </tr>
{$content}

        <tr>
            <td class="item_title"></td>
            <td class="item_input">
                <?php print form_submit(array('name' => 'mysubmit', 'class' => 'am-btn am-btn-primary', 'value' => '提交')); ?>
            </td>
        </tr>
        <tr>
            <td colspan=2 class="bottomTd"></td>
        </tr>
    </table>
    <?php print form_close(); ?>
</div>
<?php include(APPPATH . 'views/common/footer.php'); ?>
EOD;

        $this->_write_file( $file, $content );
    }
    // return boolean, whether create successfully.
    private function _write_file ( $file, $content ){
        return write_file( $file, $content );
    }

}

?>
