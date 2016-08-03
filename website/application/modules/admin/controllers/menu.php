<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
class menu extends MX_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->form_validation->CI =& $this;
        $this->load->library('Datatables');
        $this->load->library('table');
    }
    
    public function index(){
        //set table id in table open tag
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl);
        $this->table->set_heading('Type','Title', 'Status', 'Position', 'Footer', 'Parent','Actions');
        
        # load tds config.
        $this->load->config('tds');
        $data['menu_types'] = $this->config->config['menu_types'];
        
        $this->render('admin/menu/index',$data);
    }
    
    public function datatable(){
        if(!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        # load tds config.
        $this->load->config('tds');
        
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_controller_name("menu");
        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->set_custom_string(1, $this->config->config['menu_types']);
        $this->datatables->set_custom_string(3, array(1 => 'Active', 0 => 'Inactive'));
        $this->datatables->set_custom_string(4, $this->config->config['menu_position']);
        
        $this->datatables->select('menu.id AS primary_id, menu.type, menu.title, menu.is_active, menu.position, menu.footer_group, pre_menu.title AS parent_name')
                ->unset_column('primary_id')
                ->from("menu")
                ->where("menu.parent_menu_id IS NULL")
                ->or_where("menu.parent_menu_id = ''")
                ->join("menu as pre_menu", "menu.parent_menu_id=pre_menu.id", 'LEFT');
        ;
        
        echo $this->datatables->generate();
    }
    
    public function add(){
        $this->load->model('menus','model');
        $this->load->library('form_validation');
        
        $this->load->config('tds');
        $positions = $this->config->config['menu_position'];
        
        /*foreach($positions as $positions_key => $positions_name){
            if(!empty($positions_key)){
                $ar_position[$positions_key] = $positions_name;
            }
        }*/
        
        $data['fields'] = $this->model->get_fields();
        $data['model'] = 'menus';
        //$data['ar_position'] = $ar_position;
        $data['ar_menu_types'] = $this->config->config['menu_types'];
        $data['ar_footer_groups'] = $this->config->config['footer_group'];
        
        if(isset($_POST['menus'])){
            $_POST['menus']['created'] = date('Y-m-d H:i:s');
            $_POST['menus']['updated'] = $_POST['menus']['created'];
            
            # set sub categories value
            $cnt_sub_cats = 0;
            if(isset($_POST['menus']['sub_categories'])){
               $sub_cats = $_POST['menus']['sub_categories'];
               $cnt_sub_cats = sizeof($sub_cats);
               unset($_POST['menus']['sub_categories']);
            }
            # set sub categories value
            
            # start date
            if(empty($_POST['menus']['startdate'])){
               unset($_POST['menus']['startdate']); 
            }
            
            # expire date
            if(empty($_POST['menus']['expired'])){
               unset($_POST['menus']['expired']); 
            }
            
            # Generate CI_key
            # load string helper.
            $this->load->helper('string');
            $ci_key = (isset($_POST['menus']['ci_key']) && !empty($_POST['menus']['ci_key'])) ? $_POST['menus']['ci_key'] : $_POST['menus']['title'];
            $_POST['menus']['ci_key'] = sanitize($ci_key);
            # Generate CI_key
            
            # set validation rules.
            $this->form_validation->set_rules($this->model->rules());
            if($this->form_validation->run() === true){
                # file uploader and set file name
                $file_name = NULL;
                if(isset($_FILES) && !empty($_FILES['menus']['name']['icon_name'])){
                    $this->load->library('upload_files', $_FILES, 'validateFile');
                    $this->validateFile->max_allowed_size = 328049;
                    $this->validateFile->set_allowed_dimension(19, 17, true);
                    $file_name = $this->validateFile->upload_file('menu_icons');
                }
                $_POST['menus']['icon_name'] = $file_name;
                # file uploader and set file name
                
                # insert parent menu.
                $parent_id = $this->model->save_model($_POST['menus']);
                
                # if needed then insert sub-menus.
                if($cnt_sub_cats > 0){
                    foreach($sub_cats as $sub_cat_id){
                        $sub_menu = $this->prepare_sub_cats_for_insert($sub_cat_id, $parent_id, $_POST['menus']['type']);
                        $this->model->save_model($sub_menu['menus']);
                    }
                }
                # if needed then insert sub-menus.
                
                echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
                exit;
            }
        }
        
        $this->render('admin/menu/_form',$data);
    }
    
    public function edit($id){
        $this->load->model('menus','model');
        
        # load config file.
        $this->load->config('tds');
        
        # position array
        $positions = $this->config->config['menu_position'];
        
        # prepare position array
        foreach($positions as $positions_key => $positions_name){
            if(!empty($positions_key)){
                $ar_position[$positions_key] = $positions_name;
            }
        }
        
        # get menu row by id.
        $results = $this->model->get_menus($id);
        foreach($results as $result){
            $data['values'] = $result;
        }
        
        # update process
        if(isset($_POST['menus'])){
            # set sub categories value
            $cnt_sub_cats = 0;
            if(isset($_POST['menus']['sub_categories'])){
               $sub_cats = $_POST['menus']['sub_categories'];
               $cnt_sub_cats = sizeof($sub_cats);
               unset($_POST['menus']['sub_categories']);
            }
            # set sub categories value
            
            # start date
            if(empty($_POST['menus']['startdate'])){
               unset($_POST['menus']['startdate']); 
            }
            
            # expire date
            if(empty($_POST['menus']['expired'])){
               unset($_POST['menus']['expired']); 
            }
            
            # unset create date
            unset($_POST['menus']['created']);
            
            # set update date
            $_POST['menus']['updated'] = date('Y-m-d H:i:s');
            
            # Generate CI_key
            # load string helper.
            $this->load->helper('string');
            $ci_key = (isset($_POST['menus']['ci_key']) && !empty($_POST['menus']['ci_key'])) ? $_POST['menus']['ci_key'] : $_POST['menus']['title'];
            $_POST['menus']['ci_key'] = sanitize($ci_key);
            # Generate CI_key
        
            # set validation rules.
            $this->form_validation->set_rules($this->model->rules());
            if($this->form_validation->run() === true){
                # file uploader and set file name
                if(isset($_FILES) && !empty($_FILES['menus']['name']['icon_name'])){
                    # if file uploaded
                    $this->load->library('upload_files', $_FILES, 'validateFile');
                    $this->validateFile->max_allowed_size = 328049;
                    $this->validateFile->set_allowed_dimension(19, 17, true);
                    $_POST['menus']['icon_name'] = $this->validateFile->upload_file('menu_icons', $data['values']['icon_name']);
                }else{
                    # if no file uploaded
                    $_POST['menus']['icon_name'] = $data['values']['icon_name'];
                }
                # file uploader and set file name
                
                # delete sub menus
                $this->model->delete_sub_cat((int)$_POST['menus']['id']);
                
                # update parent menu.
                $parent_id = $this->model->save_model($_POST['menus']);
                
                # if needed then update sub-menus.
                if($cnt_sub_cats > 0){
                    foreach($sub_cats as $sub_cat_id){
                        $sub_menu = $this->prepare_sub_cats_for_insert($sub_cat_id, $parent_id, $_POST['menus']['type']);
                        $this->model->save_model($sub_menu['menus']);
                    }
                }
                # if needed then update sub-menus.
            }
        }
        # update process
        
        $data['fields'] = $this->model->get_fields();
        $data['model'] = 'menus';
        $data['ar_position'] = $ar_position;
        $data['ar_menu_types'] = $this->config->config['menu_types'];
        $data['ar_footer_groups'] = $this->config->config['footer_group'];
        
        $this->render('admin/menu/_form',$data);
    }
    
    public function categoryListDrop()
    {
        if(!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        if(isset($_POST['menus_id']) && !isset($_POST['edit'])){
            $this->load->model('menus','model');
            $cat_id = $this->model->get_menus((int)$_POST['menus_id'], array('category_id'));
            $cat_id = $cat_id[0]['category_id'];
        }
        
        $obj_category = new Category();
        $obj_category->select('id, name');

        $where = "parent_id IS NULL OR parent_id = ''";
        
        if(isset($_POST['id']) && !empty($_POST['id'])){
            $where = "parent_id = '".$_POST['id']."'";
            
            $obj_category->where($where);
            $obj_category->order_by('id','asc');
            
            $obj_category->get();
            
            $res_count = $obj_category->result_count();
            if(empty($res_count)){
                echo "No sub categories found.";
                exit;
            }
            
            if(isset($_POST['edit']) && $_POST['edit'] == 'true'){
                $this->load->model('menus','model');
                $ar_sub_menus = $this->model->get_sub_menus($_POST['row_id'], array('category_id'));
                $ar_sub_menus = $this->model->prepare_sub_cat_ar($ar_sub_menus);
            }
            
            $sub_cat_list = '<table class="static"><thead><tr><th>Sub Categories</th><th>Select';
            $sub_cat_list .= '</th><th>Priority</th></tr></thead><tbody>';
            
            foreach ($obj_category as $result){
                $sub_cat_list .= '<tr>';
                    $sub_cat_list .= '<td>';
                        $sub_cat_list .= '<label for="menus_sub_categories_'.$result->id.'">'.$result->name.'</label>';
                    $sub_cat_list .= '</td>';
                    
                    $sub_cat_list .= '<td>';
                        $sub_cat_list .= '<input type="checkbox" id="menus_sub_categories_'.$result->id.'" class="chk_menus_sub_categories" name="menus[sub_categories][]" value="'.$result->id.'"';
                        $sub_cat_list .= (!empty($ar_sub_menus) && in_array($result->id, $ar_sub_menus)) ? 'checked="checked"' : '';;
                        $sub_cat_list .= '/>';
                    $sub_cat_list .= '</td>';

                    $sub_cat_list .= '<td></td>';
                $sub_cat_list .= '</tr>';                
            }
            $sub_cat_list .= '</tbody></table>';
            echo $sub_cat_list;
            exit;
        }
        
        $obj_category->where($where);
        $obj_category->order_by('id','asc');
        
        $obj_category->get();
        
        $cat_drop = "";
        $cat_drop = '<option value="">Select</option>';
        foreach ($obj_category as $value)
        {
            $cat_drop .= '<option value="'.$value->id.'"';
            $cat_drop .= (!empty($cat_id) && $value->id == $cat_id) ? 'selected="selected"' : '';
            $cat_drop .= '>'.$value->name.'</option>';
        }
        
        echo $cat_drop;
        exit;
    }
    
    public function newsList(){
        if(!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        if(isset($_POST) && !empty($_POST['cat_ids'])){
            $sql = "SELECT
                    tds_post_category.id,
                    tds_post_category.post_id,
                    tds_post_category.category_id,
                    tds_post.headline
                    FROM
                    tds_post_category
                    INNER JOIN tds_post ON tds_post_category.post_id = tds_post.id";
            
            if($_POST['cat_ids'] !== 'news'){
                $sql .= " WHERE category_id IN (".$_POST['cat_ids'].")";
            }
            
            $query = $this->db->query($sql);
            $results = $query->result();
            
            if(empty($results)){
                echo 'No news found.';
                exit;
            }
            
            $news_list = '<table class="static"><thead><tr><th>Headline</th><th>Select</th><th>Priority</th></tr></thead><tbody>';
            foreach($results as $result){
                $news_list .= '<tr>';
                    $news_list .= '<td>';
                        $news_list .= '<label for="menus_news_id_'.$result->post_id.'">'.$result->headline.'</label>';
                    $news_list .= '</td>';
                    
                    $news_list .= '<td>';
                        $news_list .= '<input type="checkbox" id="menus_news_id_'.$result->post_id.'" class="chk_menus_news_list" name="menus[news_id]" value="'.$result->post_id.'" />';
                    $news_list .= '</td>';

                    $news_list .= '<td></td>';
                $news_list .= '</tr>';
            }
            $news_list .= '</tbody></table>';
            echo $news_list;
            exit;
        }else{
            echo 'Please select sub categorie(s).';
            exit;
        }
    }
    
    public function __position_validation(){
        if(!is_integer($_POST['menus']['position']) && ($_POST['menus']['position'] > 2 || $_POST['menus']['position'] < 1)){
            return false;
        }
        return true;
    }
    
    public function __menu_types_validation(){
        if(!is_integer($_POST['menus']['type']) && ($_POST['menus']['type'] > 4 || $_POST['menus']['type'] < 1)){
            return false;
        }
        return true;
    }
    
    public function __icon_name_validation(){
        if((int)($_POST['menus']['type'] == 3) && (empty($_FILES['menus']['name']['icon_name']))){
            return false;
        }
        return true;
    }
    
    public function __news_num_validation(){
        $cnt_news_ids = sizeof($_POST['menus']['news_id']);
        if((int)($_POST['menus']['type'] == 1 || (int)$_POST['menus']['type'] == 4) && (!is_integer($_POST['news_num']) && ($cnt_news_ids > (int)$_POST['news_num']))){
            return false;
        }
        return true;
    }
    
    public function __priority_validation(){
        $cnt_news_ids = (isset($_POST['menus']['news_id'])) ? sizeof($_POST['menus']['news_id']) : 0;
        $cnt_priorities = (isset($_POST['menus']['priority'])) ? sizeof($_POST['menus']['priority']) : 0;
        
        if((int)($_POST['menus']['type'] == 1 || (int)$_POST['menus']['type'] == 4)){
            if((int)$_POST['menus']['type'] == 4){
                if($cnt_news_ids > 1){
                    return false;
                }
            }else{
                if($cnt_news_ids != $cnt_priorities){
                    return false;
                }
            }
        }
        return true;
    }
    
    public function __expired_validation(){
        if(isset($_POST['menus']['startdate'], $_POST['menus']['expired']) && (!empty($_POST['menus']['startdate']) && !empty($_POST['menus']['expired']))){
            $started = strtotime($_POST['menus']['startdate']);
            $expired = strtotime($_POST['menus']['expired']);
            if($started > $expired){
                return false;
            }
        }
        return true;
    }
    
    private function prepare_sub_cats_for_insert($id, $parent_id = NULL, $menu_type = NULL){
        #load string helper.
        $this->load->helper('string');
        
        $this->db->select('name, priority');
        $this->db->from('categories');
        $this->db->where('id', $id);
        $query = $this->db->get();
        
        $result = $query->result();
        $result = $result[0];
        
        unset($_POST['menus']['id']);
        $_POST['menus']['type'] = (int)$menu_type;
        $_POST['menus']['title'] = $result->name;
        $_POST['menus']['news_num'] = '2';
        $_POST['menus']['priority'] = $result->priority;
        $_POST['menus']['category_id'] = (int)$id;
        $_POST['menus']['parent_menu_id'] = $parent_id;
        $_POST['menus']['ci_key'] = sanitize($result->name);
        
        return $_POST;
    }
}
?>