<?php

/*
 * menus Controller
 * Admin Menu management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class menus extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->form_validation->CI = & $this;
        $this->load->library('Datatables');
        $this->load->library('table');
    }

    /**
     * Index function
     * @param none
     * @defination use for showing table header and setting table id for menu
     * @author Mahamud Hasan <mahamud.hasan@teamworkbd.com>
     */
    public function index() {
        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Type','Title','Position','Parent', 'Gallery', 'Status', 'Actions');
        
        $obj_menu = $this->db->get_where('menu', array('parent_menu_id'=>NULL))->result();
        
        $select_menu[NULL] = "Select";
        foreach ($obj_menu as $value)
        {
            $select_menu[$value->title] = $value->title;
        }


        $data['select_menu'] = $select_menu;
        
        
        $this->render('admin/menus/index',$data);
    }

    /**
     * Datable function
     * @param none
     * @defination use for showing datatable for byline callback function
     * @author Fahim
     */
    function datatable() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_buttons("change_gallery_status","ajax");
        $this->datatables->set_controller_name("menus");

        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->set_custom_string(1, array(1=>'Category',2 => 'Text',3 => 'Icon', 5 => 'Only Text'));
        
        $this->datatables->set_custom_string(3, array(1 => 'Header', 2 => 'Footer'));
        
        $this->datatables->set_custom_string(5, array(1 => 'Yes', 0 => 'No'));
        
        $this->datatables->set_custom_string(6, array(1 => 'Active', 0 => 'Inactive'));

        $this->datatables->select('menu.id AS primary_id,menu.type, menu.title,menu.position,pre_menu.title as pr_menu_title, menu.show_gallery, menu.is_active')
                ->unset_column('primary_id')
                ->from("menu")
                ->join("menu as pre_menu", "menu.parent_menu_id=pre_menu.id", 'LEFT');

        echo $this->datatables->generate();
    }
    
    function change_gallery_status()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_menu = new Menu($this->input->post('primary_id'));
       
        if($obj_menu->show_gallery)
        {
            $show_gallery = 0;
        }    
        else
        {
            $show_gallery = 1;
        }    
        
        $data  = array('show_gallery' =>$show_gallery);
        $where = "ci_key = '".$obj_menu->ci_key."'";

        $str   = $this->db->update_string('tds_menu', $data, $where);
        $this->db->query($str); 
        
        if($obj_menu->category_id)
        {
            garbage_collector_category($obj_menu->category_id);
        }    
        
        echo 1;
    }
    
    
    private function get_parent_menus($position = 1)
    {

        $this->db->select("*");
        $this->db->from("menu");
        $this->db->where('parent_menu_id',NULL);
        $this->db->where('position', (int) $position);
        //$this->db->where('type', 1);
        $this->db->where('is_active', 1);
        $menus = $this->db->get()->result();
        
        $select_menu = array();


        foreach ($menus as $value)
        {
            $select_menu[$value->id] = $value->title;
        }

        return $select_menu;
        
    } 

    /**
     * Add function
     * @param none
     * @defination use for add menu for text and Icon menu
     * @author Mahamud Hasan <mahamud.hasan@teamworkbd.com>
     */
    public function add() {
        $this->load->config('tds');
        $data['ar_menu_types'] = $this->config->config['menu_types_for_crud'];
        
        
        
        $obj_parent_menu = $this->get_parent_menus();
        $obj_parent_menu_footer = $this->get_parent_menus(2);
           
        $data['parent_menu'] = $obj_parent_menu;  
          
        $data['parent_menu_footer'] = $obj_parent_menu_footer; 

        $obj_menu = new Menu();

        if (!empty($_FILES['icon_name']['name'])) {
            if ($_FILES['icon_name']['size'] <= 328049) {
                $name = explode(".", $_FILES['icon_name']['name']);
                $type = explode("/", $_FILES['icon_name']['type']);
                $destination = strtotime("now") . "-" . sanitize($name[0]) . "." . $type[1];
                $destination = "images/icons/menu/" . $destination;
                $destination = Image::upload($_FILES['icon_name']['tmp_name'], $destination);
                $_POST['icon_name'] = $destination;
            }
        }


        if ($_POST) {
            foreach ($_POST as $key => $value) {
                $obj_menu->$key = ($key == 'ci_key') ? sanitize($_POST['title'], '-') : $value;
            }
            
            if($this->input->post("menu_types") == 2 && $this->input->post("position")==1)
            {
                $obj_menu->parent_menu_id = $this->input->post("parent_menu_id_header");
            }
            else if($this->input->post("menu_types") == 2 && $this->input->post("position")==2)
            {
                $obj_menu->parent_menu_id = $this->input->post("parent_menu_id_footer");
            }
            else
            {
               $obj_menu->parent_menu_id = null; 
            }
            
            if($_POST['type']==2)
            {    
                $obj_menu->icon_name = NULL;
            }
            else if($_POST['type']==3)
            {    
              $obj_menu->link_text = NULL; 
              $obj_menu->parent_menu_id = null;
            }
            else 
            {
              $obj_menu->ci_key    = NULL;   
              $obj_menu->icon_name = NULL;   
              $obj_menu->link_text = NULL; 
              $obj_menu->permalink = NULL; 
              $obj_menu->parent_menu_id = null;
            }                
            
        }

        $data['model'] = $obj_menu;
        if (!$obj_menu->save()) {
            $this->render('admin/menus/insert', $data);
        } else {
            
            $html_header_cache = "common/HEADER_MENU";
            if($this->cache->file->get($html_header_cache))
            {
                $this->cache->file->delete($html_header_cache);
            }
            $html_footer_cache = "common/FOOTER_MENU";
            if($this->cache->file->get($html_footer_cache))
            {
                $this->cache->file->delete($html_footer_cache);
            }
            garbage_collector();
            
            
            
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * Edit function
     * @param none
     * @defination use for add menu for text and Icon menu
     * @author Mahamud Hasan <mahamud.hasan@teamworkbd.com>
     */
    public function edit($id) {
        $this->load->config('tds');
        $data['ar_menu_types'] = $this->config->config['menu_types_for_crud'];

        $obj_menu = new Menu($id);
        
        
        if($obj_menu->type==1)
        {
            redirect(base_url()."admin/categories/assign_as_menu/".$obj_menu->category_id);
        }    
        
        
        $obj_parent_menu = $this->get_parent_menus();
        $obj_parent_menu_footer = $this->get_parent_menus(2);
           
        $data['parent_menu'] = $obj_parent_menu;  
          
        $data['parent_menu_footer'] = $obj_parent_menu_footer; 
        
        if (!empty($_FILES['icon_name']['name'])) {
            if ($_FILES['icon_name']['size'] <= 328049) {
                $name = explode(".", $_FILES['icon_name']['name']);
                $type = explode("/", $_FILES['icon_name']['type']);
                $destination = strtotime("now") . "-" . sanitize($name[0]) . "." . $type[1];
                $destination = "images/icons/menu/" . $destination;
                $destination = Image::upload($_FILES['icon_name']['tmp_name'], $destination);
                $_POST['icon_name'] = $destination;
            }
        }
        
        
        if ($_POST) {
            
            $old_postion= $obj_menu->position;
            $obj_menu->image = "";
            foreach ($_POST as $key => $value) {
               
                 $obj_menu->$key = ($key == 'ci_key' && $value!="index") ? sanitize($_POST['title'], '-') : $value;
                
            }
            
            if($this->input->post("menu_types") == 2 && $this->input->post("position")==1)
            {
                $obj_menu->parent_menu_id = $this->input->post("parent_menu_id_header");
                $this->db->where('parent_menu_id',$obj_menu->id);
                $this->db->where('position', $old_postion);
                $this->db->delete("menu");
            }
            else if($this->input->post("menu_types") == 2 && $this->input->post("position")==2)
            {
                $obj_menu->parent_menu_id = $this->input->post("parent_menu_id_footer");
                $this->db->where('parent_menu_id',$obj_menu->id);
                $this->db->where('position', $old_postion);
                $this->db->delete("menu");
            }
            else
            {
               $obj_menu->parent_menu_id = null; 
            }
            
            
            if($_POST['type']==2)
            {    
                $obj_menu->icon_name = NULL;
            }
            else if($_POST['type']==3)
            {    
              $obj_menu->link_text = NULL; 
              $obj_menu->parent_menu_id = null;
            }
            else 
            {
              $obj_menu->ci_key    = NULL;   
              $obj_menu->icon_name = NULL;   
              $obj_menu->link_text = NULL; 
              $obj_menu->permalink = NULL; 
              $obj_menu->parent_menu_id = null;
            }    
            
            
            
        }

        $data['model'] = $obj_menu;
        if (!$obj_menu->save() || !$_POST) {
            $this->render('admin/menus/insert', $data);
        } else {
            $html_header_cache = "common/HEADER_MENU";
            if($this->cache->file->get($html_header_cache))
            {
                $this->cache->file->delete($html_header_cache);
            }
            $html_footer_cache = "common/FOOTER_MENU";
            if($this->cache->file->get($html_footer_cache))
            {
                $this->cache->file->delete($html_footer_cache);
            }
            garbage_collector();
            
           
            
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    
    public function sort_menus(){
        $obj_menu = new Menu();
        
        $data['menus'] = $obj_menu->get_sorted_menus(null, array('id','title','priority','parent_menu_id'));
        
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        
        $this->render('admin/menus/sort_menus', $data);
    }
    
    function get_submenus(){
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        
        $this->disable_layout = TRUE;
        $parent_menu_id =  $this->input->post('parent_menu_id');
        
        $obj_menu = new Menu();
        $data['sub_menus'] = $obj_menu->get_sorted_menus(null, array('id','title','priority','parent_menu_id'),$parent_menu_id);
        
        $data['parent_menu_id'] = $parent_menu_id;
        
        $s_post_list = $this->render('admin/menus/sub_menu_list', $data, TRUE);
    }
    
    function save_priority(){
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        
        $str_menu_ids =  $this->input->post('menu_ids');
        $ar_menu_ids = explode(',', $str_menu_ids);
        
        $i = 2;
        foreach($ar_menu_ids as $menu_id){
            $menu = new Menu($menu_id);
            $menu->priority = $i;
            $menu->save();
            $i++;
        }
        garbage_collector();
        $html_header_cache = "common/HEADER_MENU";
        $this->cache->file->delete($html_header_cache);
        
        $html_footer_cache = "common/FOOTER_MENU";
        $this->cache->file->delete($html_footer_cache);
        
    }

    /**
     * delete function
     * @param none
     * @defination use for delete a menu
     * @author Mahamud
     */
    function delete() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $obj_menu = new Menu($this->input->post('primary_id'));
        
        $this->db->where('parent_menu_id',$obj_menu->id);
        $this->db->where('position', $obj_menu->position);
        $this->db->delete("menu");
        
        
        $obj_menu->delete();
        garbage_collector();
        
        $html_header_cache = "common/HEADER_MENU";
        $this->cache->file->delete($html_header_cache);
        
        $html_footer_cache = "common/FOOTER_MENU";
        $this->cache->file->delete($html_footer_cache);
        
        
        echo 1;
    }

}

?>
