<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class categories extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Datatables');
        $this->load->library('table');
        $this->load->library('image_lib');
        $this->form_validation->CI = & $this;
    }

    /**
     * Index function
     * @param None
     * @defination use for showing table header and setting table id and filtering for admin category
     * @author Fahim
     */
    public function index()
    {
        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1"  class="mytable">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('ID','Name', 'Status', 'Parent', 'Action');

        $obj_category = new Category();
        $obj_category->order_by('name');
        $obj_category->get();


        $select_categoryMenu[NULL] = "Select";
        foreach ($obj_category as $value)
        {
            $select_categoryMenu[$value->name] = $value->name;
        }
        
        $obj_catgory_type = $this->db->get_where('category_type', array('is_active' => 1))->result();
        
        $select_categoryType[NULL] = "Select";
        foreach ($obj_catgory_type as $value)
        {
            $select_categoryType[$value->type_name] = $value->type_name;
        }
        
        $data['categoryMenu'] = $select_categoryMenu;
        $data['categoryType'] = $select_categoryType;
        
        $data['datatableSortBy'] = 2;
        $data['datatableSortDirection'] = 'asc';
        
        $this->render('admin/category/index', $data);
    }

    /**
     * datatable function
     * @param none
     * @defination use for showing datatable of category with child tree callback function
     * @author Fahim
     */
    public function datatable()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->datatables->set_buttons("edit");
        
        $this->datatables->set_buttons("pdf","category_model");
        $this->datatables->set_buttons("photo","category_model");
       
        
        
        $this->datatables->set_buttons("change_status","ajax");
        $this->datatables->set_controller_name("categories");
        $this->datatables->set_primary_key("primary_id");
        $this->datatables->set_custom_string(2, array(1 => 'Active', 0 => 'Inactive'));
        $this->datatables->select('categories.id as primary_id,categories.name,categories.status,pre_category.name as parent')
                ->from('categories')->join("categories as pre_category", "pre_category.id=categories.parent_id", 'LEFT')
                ->where("categories.show", "1")
                ->join("category_type as pre_cat_type", "pre_cat_type.id=categories.category_type_id", 'LEFT');

        echo $this->datatables->generate();
    }

    /**
     * add function
     * @param none
     * @defination use for insert Category and child category as tree
     * @author Fahim
     */
    public function add()
    {
        $obj_category = new Category();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_category->$key = $value;
            }
            
            if(isset($_POST['inner_page_menu']) && !empty($_POST['inner_page_menu'])){
                $obj_category->inner_page_menu = 1;
            }else{
                $obj_category->inner_page_menu = 0;
            }
        }
        if (!$obj_category->parent_id)
            $obj_category->parent_id = NULL;

        $data['model'] = $obj_category;
        $data['parentCategory'] = $this->categoryList();
        
        $obj_catgory_type = $this->db->get_where('category_type', array('is_active' => 1))->result();
        
        
        foreach ($obj_catgory_type as $value)
        {
            $select_categoryType[$value->id] = $value->type_name;
        }
        
        $data['typeCategory'] = $select_categoryType;
        
        if (!$obj_category->save())
        {
            $this->render('admin/category/insert', $data);
        }
        else
        {
            garbage_collector();
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * edit function
     * @param none
     * @defination use for edit category
     * @author Fahim
     */
    public function edit($id)
    {

        $obj_category = new Category($id);
        if ($_POST)
        {
            $obj_category->icon = "";
            
            foreach ($this->input->post() as $key => $value)
            {
                $obj_category->$key = $value;
            }
            
            if(isset($_POST['inner_page_menu']) && !empty($_POST['inner_page_menu'])){
                $obj_category->inner_page_menu = 1;
            }else{
                $obj_category->inner_page_menu = 0;
            }
        }
        if (!$obj_category->parent_id)
            $obj_category->parent_id = NULL;

        $data['model'] = $obj_category;
        $data['parentCategory'] = $this->categoryList($id);
        
        $obj_catgory_type = $this->db->get_where('category_type', array('is_active' => 1))->result();
        
        $select_categoryType[NULL] = "Select";
        foreach ($obj_catgory_type as $value)
        {
            $select_categoryType[$value->id] = $value->type_name;
        }
        
        $data['typeCategory'] = $select_categoryType;
        
        
        if (!$obj_category->save() || !$_POST)
        {
            $this->render('admin/category/insert', $data);
        }
        else
        {
            $this->load->helper('string');
            garbage_collector_category($id);
          
            garbage_collector();
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * delete_recursive function
     * @param int $primary_id
     * @defination use for delete recursively category
     * @author Fahim
     */
    private function delete_recursive($primary_id)
    {
        $child_category = new Category();
        $child_category->where('parent_id', $primary_id)->get();
        if ($child_category->exists())
        {
            foreach ($child_category as $value)
            {
                $primary_id = $value->id;
                $this->delete_recursive($primary_id);
            }
        }
        
        $child_category->delete();
        garbage_collector();
    }

    /**
     * delete function
     * @param None
     * @defination use for delete category Ajax
     * @author Fahim
     */
    function change_status()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_category = new Category($this->input->post('primary_id'));
       
        if($obj_category->status)
        {
            $status = 0;
        }    
        else
        {
            $status = 1;
        }    
        
        $data  = array('status' =>$status);
        $where = "id = ".$this->input->post('primary_id');

        $str   = $this->db->update_string('tds_categories', $data, $where);
        $this->db->query($str);
        
        $data2  = array('is_active' =>$status);
        $where2 = "category_id = ".$this->input->post('primary_id');
        $str2   = $this->db->update_string('tds_menu', $data2, $where2);
        $this->db->query($str2);
        garbage_collector();
        
        echo 1;
    }
    
    public function pdf($category_id)
    {
        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1"  class="pdf_table">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Id','Issue Date', 'Action');

        $data['category_id'] = $category_id;
        
        
        $this->render('admin/category/pdf', $data);
    }
    
    public function datatable_pdf($category_id)
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->datatables->set_buttons("update_pdf");
        
        $this->datatables->set_buttons("del");
        
        $this->datatables->set_controller_name("categories");
        $this->datatables->set_primary_key("primary_id");

        $this->datatables->select('category_pdf.id as primary_id,category_pdf.issue_date')
               
                ->from('category_pdf')
                ->where("category_pdf.category_id",$category_id);
         
        echo $this->datatables->generate();
    }
    
    function del()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $obj_category_pdf = new Category_pdf($this->input->post('primary_id'));
        
        $obj_category = $this->get_category_type_and_ci_key_by_id((int)$obj_category_pdf->category_id);
        
        $ci_key = (isset($obj_category->ci_key)) ? $obj_category->ci_key : $obj_category->category_name;
        
        $cache_name = 'OBJ_INNER_PDF_'.strtoupper($ci_key).'_'.$obj_category_pdf->issue_date;
        if($this->cache->get($cache_name)){
            $this->cache->delete($cache_name);
        }
        
        $cache_name = 'HTML_INNER_PDF_'.strtoupper($ci_key).'_'.$obj_category_pdf->issue_date;
        if($this->cache->get($cache_name)){
            $this->cache->delete($cache_name);
        }
            
        $obj_category_pdf->delete();
        
        echo 1;
    }
    
    public function update_pdf($id)
    {
        $obj_category_pdf = new Category_pdf($id);
        if ($_POST)
        {
            $obj_category = $this->get_category_type_and_ci_key_by_id($obj_category_pdf->category_id);
            
            $ci_key = (isset($obj_category->ci_key)) ? $obj_category->ci_key : $obj_category->category_name;
            
            $cache_name = 'OBJ_INNER_PDF_'.strtoupper($ci_key).'_'.$obj_category_pdf->issue_date;
            if($this->cache->get($cache_name)){
                $this->cache->delete($cache_name);
            }
            
            $cache_name = 'HTML_INNER_PDF_'.strtoupper($ci_key).'_'.$obj_category_pdf->issue_date;
            if($this->cache->get($cache_name)){
                $this->cache->delete($cache_name);
            }
            
            foreach ($this->input->post() as $key => $value)
            {
                $obj_category_pdf->$key = $value;
            }   
        }
        
        $data['model'] = $obj_category_pdf;
        
        if (!$obj_category_pdf->save() || !($_POST))
        {
            $this->render('admin/category/insert_pdf', $data);
        }
        else
        {
            $cache_name = 'OBJ_INNER_PDF_'.strtoupper($ci_key).'_'.$obj_category_pdf->issue_date;
            if($this->cache->get($cache_name)){
                $this->cache->delete($cache_name);
            }
            
            $cache_name = 'HTML_INNER_PDF_'.strtoupper($ci_key).'_'.$obj_category_pdf->issue_date;
            if($this->cache->get($cache_name)){
                $this->cache->delete($cache_name);
            }
            
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    
    public function add_pdf($category_id)
    {
        $obj_category_pdf = new Category_pdf();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_category_pdf->$key = $value;
            }
            
            $obj_category_pdf->category_id = $category_id;
            
            $obj_category = $this->get_category_type_and_ci_key_by_id($category_id);
            
            $ci_key = (isset($obj_category->ci_key)) ? $obj_category->ci_key : $obj_category->category_name;
        }
      
        
        $data['model'] = $obj_category_pdf;
        
        if (!$obj_category_pdf->save())
        {
            $this->render('admin/category/insert_pdf', $data);
        }
        else
        {
            $cache_name = 'OBJ_INNER_PDF_'.strtoupper($ci_key).'_'.$obj_category_pdf->issue_date;
            if($this->cache->get($cache_name)){
                $this->cache->delete($cache_name);
            }
            
            $cache_name = 'HTML_INNER_PDF_'.strtoupper($ci_key).'_'.$obj_category_pdf->issue_date;
            if($this->cache->get($cache_name)){
                $this->cache->delete($cache_name);
            }
            
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    
    private function get_category_type_and_ci_key_by_id($category_id){
        
        $obj_menu = new Menu();
        $obj_menu = $obj_menu->get_category_type_ci_key_category_id($category_id);
        
        $obj_category = (($obj_menu) && !empty($obj_menu->category_id) && ($obj_menu->category_id > 0)) ? $obj_menu : FALSE;
        
        if( !($obj_category) || ((int)$obj_category->category_id <= 0) || empty($obj_category->category_id) ){
            $obj_category = new Category();
            $obj_category = $obj_category->get_category_and_ci_key_by_id($category_id);
        }
        
        return $obj_category;
        exit;
    }
    
    
    ////Cover Photo Section
    
    
    public function photo($category_id)
    {
        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1"  class="photo_table">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Id','Issue Date', 'Action');

        $data['category_id'] = $category_id;
        
        
        $this->render('admin/category/photo', $data);
    }
    
    public function datatable_photo($category_id)
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->datatables->set_buttons("update");
        
        $this->datatables->set_buttons("remove");
        
        $this->datatables->set_controller_name("categories");
        $this->datatables->set_primary_key("primary_id");

        $this->datatables->select('category_cover.id as primary_id,category_cover.issue_date')
               
                ->from('category_cover')
                ->where("category_cover.category_id",$category_id);
         
        echo $this->datatables->generate();
    }
    function remove()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_category_cover = new Category_cover($this->input->post('primary_id'));
        $obj_category_cover->delete();
        echo 1;
    }
    
    public function update($id)
    {
        $obj_category_cover = new Category_cover($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_category_cover->$key = $value;
            }
            
        }
      

        $data['model'] = $obj_category_cover;
        
        if (!$obj_category_cover->save() || !($_POST))
        {
            $this->render('admin/category/insert_cover', $data);
        }
        else
        {
            $this->cache->delete("BOTTOM_SLIDER");
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    } 
    public function add_images($category_id)
    {
        $obj_category_cover = new Category_cover();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_category_cover->$key = $value;
            }
            $obj_category_cover->category_id = $category_id;
        }
      

        $data['model'] = $obj_category_cover;
        
        if (!$obj_category_cover->save())
        {
            $this->render('admin/category/insert_cover', $data);
        }
        else
        {
            $this->cache->delete("BOTTOM_SLIDER");
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    
    private function get_category_menus($category_id, $position = 1)
    {

        $this->db->select("*");
        $this->db->from("menu");
        $this->db->where('category_id', (int) $category_id);
        $this->db->where('position', (int) $position);
        $this->db->where('type', 1);
        $this->db->where('is_active', 1);
        $menus = $this->db->get()->row();
        return $menus;
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
   
    public function assign_as_menu($category_id)
    {
        $obj_menu = $this->get_category_menus($category_id);
        
        $obj_parent_menu = $this->get_parent_menus();
        
        
        $obj_menu_footer = $this->get_category_menus($category_id,2);
        
        $obj_parent_menu_footer = $this->get_parent_menus(2);
        
       
        
        $data['menu'] = $obj_menu;    
        $data['parent_menu'] = $obj_parent_menu;  
        
        $data['menu_footer'] = $obj_menu_footer;    
        $data['parent_menu_footer'] = $obj_parent_menu_footer; 
        
        
        if ($_POST)
        {
            $obj_category = new Category($category_id);
            if($this->input->post('chk_header_menu') == 1)
            {
             
                if($this->input->post("menu_types") == 2)
                {
                    if(isset($obj_menu->id))
                    {
                        $this->db->where('parent_menu_id',$obj_menu->id);
                        $this->db->where('position', 1);
                        $this->db->delete("menu");
                    }
                    
                }    
                $insert = array();

                $insert['position'] = 1;
                $insert['type'] = 1;

                $insert['category_id'] = $category_id;

                if($this->input->post("title")=="")
                {
                    $insert['title']        = ucwords($obj_category->name); 
                    $insert['gallery_name'] = ucwords($obj_category->name);
                } 
                else
                {
                    $insert['title']        = $this->input->post("title");
                    $insert['gallery_name'] = $this->input->post("title");
                } 
                if(!isset($obj_menu->id))
                {
                    $insert['ci_key'] = sanitize(strtolower($obj_category->name));
                }
                
                if(isset($obj_menu->ci_key))
                {
                    $insert['ci_key'] = sanitize($obj_menu->ci_key);
                } 
                else if(isset($obj_menu_footer->ci_key))
                {
                    $insert['ci_key'] = sanitize($obj_menu_footer->ci_key);
                }  
                
                $insert['is_active']    = $this->input->post("is_active");
                if($this->input->post("news_num")!="" && $this->input->post("menu_types") == 1)
                {
                    $insert['news_num'] = $this->input->post("news_num");
                }
                else
                {
                   $insert['news_num'] = 0;
                }
                $insert['has_ad']       = $this->input->post("has_ad");

                if($this->input->post("menu_types") == 2)
                {
                    $insert['parent_menu_id'] = $this->input->post("parent_menu_id");
                }
                if(isset($obj_menu->id))
                {
                    $this->db->where('id', $obj_menu->id);
                    $this->db->update('menu', $insert); 
                }
                else
                {
                    $this->db->insert('menu', $insert); 
                    
                }
                    
                 
                   
                
                
            }    
            else
            {
                if(isset($obj_menu->id))
                {
                    $this->db->where('parent_menu_id',$obj_menu->id);
                    $this->db->where('position', 1);
                    $this->db->delete("menu");


                    $this->db->where('id',$obj_menu->id);
                    $this->db->where('position', 1);
                    $this->db->delete("menu");
                }
               
            }
            
            
            
            
            if($this->input->post('chk_footer_menu') == 1)
            {
                
                
                $insert_footer = array();

                $insert_footer['position'] = 2;
                $insert_footer['type'] = 1;
                
                $insert_footer['news_num'] = 0;

                $insert_footer['category_id'] = $category_id;

                if($this->input->post("title_footer")=="")
                {
                    $insert_footer['title']        = ucwords($obj_category->name); 
                    $insert_footer['gallery_name'] = ucwords($obj_category->name);
                } 
                else
                {
                    $insert_footer['title']        = $this->input->post("title_footer");
                    $insert_footer['gallery_name'] = $this->input->post("title_footer");
                } 
                if(!isset($obj_menu_footer->id))
                {
                    $insert_footer['ci_key'] = sanitize(strtolower($obj_category->name));
                }
                
                if(isset($obj_menu->ci_key))
                {
                    $insert_footer['ci_key'] = sanitize($obj_menu->ci_key);
                } 
                else if(isset($obj_menu_footer->ci_key))
                {
                    $insert_footer['ci_key'] = sanitize($obj_menu_footer->ci_key);
                } 
                
                $insert_footer['is_active']    = $this->input->post("is_active_footer");
               
                $insert_footer['parent_menu_id'] = $this->input->post("parent_menu_id_footer");
                
                if(isset($obj_menu_footer->id))
                {
                    $this->db->where('id', $obj_menu_footer->id);
                    $this->db->update('menu', $insert_footer); 
                }
                else
                {
                    $this->db->insert('menu', $insert_footer); 
                }
                    
                   
                   
                
                
            }    
            else
            {
                if(isset($obj_menu_footer->id))
                {
                    $this->db->where('parent_menu_id',$obj_menu_footer->id);
                    $this->db->where('position', 2);
                    $this->db->delete("menu");


                    $this->db->where('id',$obj_menu_footer->id);
                    $this->db->where('position', 2);
                    $this->db->delete("menu");
                }
                
            }
            
            
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
        
        
       
        $this->render('admin/category/header_menu', $data);
     
    }        

    private function categoryList($id = "")
    {
        $obj_category = new Category();
        $array = array('id !=' => $id,"show"=>1);

        $obj_category->order_by('parent_id');
        $obj_category->where($array)->get();




        $select_parentCategory[NULL] = "Select";


        foreach ($obj_category as $value)
        {
            $select_parentCategory[$value->id] = $value->name;
        }

        return $select_parentCategory;
    }
    
    public function sort_categories()
    {
        $obj_category = new Category();
        $obj_category->order_by('priority');
        $obj_category->where("category_type_id", "1");
        $obj_category->where("categories.show", "1");
        $obj_category->where("parent_id IS NULL");
        $data['categories'] = $obj_category->get();
        
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        $this->render('admin/category/sort', $data);
    }
    
    public function sub_categories()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->disable_layout = TRUE;
        $obj_category = new Category();
        $obj_category->order_by('priority');
        $data['categories'] = $obj_category->where('parent_id', $this->input->post('category_id'))->get();

        $s_subcategory_list = $this->render('admin/category/sub_categories', $data, TRUE);
        
        echo $s_subcategory_list;
        
    }
    
    public function save_priorities()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $ar_priority_sets = array();
        
        $s_category_data = $this->input->post("category_ids");
        
        $obj_category = new Category( );
        $ar_categories = explode(",", $s_category_data);
        $i = 1;
        foreach( $ar_categories as $category_id )
        {
            if ( stripos($category_id, "_") === FALSE )
            {
                $obj_category->where('id', $category_id);
                $obj_category->update("priority", $i);
                $i++;
            }
            else
            {
                $ar_cat_ids = explode("_", $category_id);
                $i_category_id = $ar_cat_ids[0];
                $i_parent_id = $ar_cat_ids[1];
                if ( !isset ($ar_priority_sets[$i_parent_id]) )
                {
                    $j = 1;
                    $ar_priority_sets[$i_parent_id] = $j;
                }
                else
                {
                    $j = $ar_priority_sets[$i_parent_id] + 1;
                    $ar_priority_sets[$i_parent_id] = $j;
                }
                $obj_category->where('id', $i_category_id);
                $obj_category->update("priority", $j);
            }
        }
        $cache_name = 'HOME_CATEGORY';
        $this->cache->delete($cache_name);
        
        $this->db->where('category_type_id', 1);
        $this->db->where('status', 1);        
        $this->db->where('parent_id IS NULL');
        $this->db->where('priority IS NOT NULL');
        $this->db->order_by("priority", "asc");
        $this->db->limit(11);
        $query = $this->db->get('categories');

        $obj_cat = $query->result();

        $this->cache->save($cache_name, $obj_cat, 86400 * 30 * 12);
        garbage_collector_category();
        garbage_collector();
    }

}

?>
