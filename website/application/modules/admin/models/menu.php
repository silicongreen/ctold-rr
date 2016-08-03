<?php

class Menu extends DataMapper {
    
    public $my_errors = array();

    var $table = "menu";
    
    var $validation = array(
        'position' => array(
            'label' => 'Location',
            'rules' => array('trim')
        ),        
        'type' => array(
            'label' => 'Menu Types',
            'rules' => array('required', 'trim'),
        ),
        'title' => array(
            'label' => 'Menu Title',
            'rules' => array('required', 'trim', 'unique'),
        ),
        'ci_key' => array(
            'label' => 'Key',
            'rules' => array('trim', 'unique'),
        ),
        'permalink' => array(
            'label' => 'External Link',
            'rules' => array('trim')
        ),
        'link_type' => array(
            'label' => 'Open',
            'rules' => array('trim')
        ),
        'link_text' => array(
            'label' => 'Text',
            'rules' => array('trim')
        ),
        'icon_name' => array(
            'label' => 'Icon',
            'rules' => array('trim')
        ),
        'is_active' => array(
            'label' => 'Active',
            'rules' => array('trim')
        ),
        'twitter_name' => array(
            'label' => 'Twitter Name',
            'rules' => array('trim')
        ),
        'widget_id' => array(
            'label' => 'Widget ID',
            'rules' => array('numeric')
        ),
    );

    public function get_fields(){
        foreach($this->validation as $field_key => $field_name){
            $fields[$field_key] = $field_key;
        }
        return $fields;
    }
    
    /**
     * @param $s_ret_type data type of return. default object, set arr to get array
    */
    public function get_menus_for_dropdown($id = null, $fields = array(), $s_ret_type = 'obj'){
        $this->db->select('id, title');
        $q = $this->db->get('menu');
        $menus[null] = 'Please Select';
        foreach($q->result_array() as $row){
            $menus[$row['id']] = $row['title'];
        }
        return $menus;
        exit;
    }
    
    public function myValidation($ar_model){
        if(!isset($ar_model['id']) || empty($ar_model['id'])){
            $this->my_errors['id'][] = 'Menu is required.';
        }
        
        $int_menu_id = (isset($ar_model['id'])) ? (int)$ar_model['id'] : null;
        if($int_menu_id < 1 || !is_integer($int_menu_id)){
            $this->my_errors['id'][] = 'Invalid Menu format. Please select from the dropdown list.';
        }
        
        if(!isset($ar_model['twitter_name']) || empty($ar_model['twitter_name'])){
            $this->my_errors['twitter_name'][] = 'Twitter Name is required.';
        }
        
        if(!isset($ar_model['widget_id']) || empty($ar_model['widget_id'])){
            $this->my_errors['widget_id'][] = 'Widget ID is required.';
        }
        
        $int_widget_id = (isset($ar_model['widget_id'])) ? (int)$ar_model['widget_id'] : null;
        if($int_widget_id < 1 || !is_integer($int_widget_id)){
            $this->my_errors['widget_id'][] = 'Widget ID is numeric.';
        }
        
        return (!empty($this->my_errors)) ? false : true;
    }
    
    /**
     * @function get_menus will return menu records. if id (PK) is not set then it'll retrun all rows
     * else it'll return only the row that id matches to the param $id
     * @param $id string $id 
     * @param $fields array of fields to be selected
    */
    public function get_sorted_menus($id = null, $fields = array(), $parent_Id = null){
        $select = (empty($fields)) ? '*' : implode(',', $fields);
        $this->select($select);
        //$this->from($this->table_name());
        if(!empty($id)){
            $this->where('id',(int)$id);
        }
        
        if(!empty($parent_Id)){
            $this->where('parent_menu_id', (int)$parent_Id);
        }else{
            $this->where('parent_menu_id IS NULL');
        }        
        $this->where('type != ',(int)3);
        $this->where('is_active = ',(int)1);
        $this->where('position',(int)1);
        $this->order_by('priority','asc');
        $q = $this->get();
        return $q;
        //return $q->result_array();
        exit;
    }
    
    public function get_ci_key_by_category_id($i_category_id = 0){
        $this->db->cache_on();
        $this->db->select('*');
        $this->db->from("menu");
        $this->db->join('categories','categories.id = menu.category_id','left');        
        $this->db->where("category_id", $i_category_id);
        $this->db->group_by("category_id");
        
        $query = $this->db->get();
        return ( $query->num_rows() == 0 ) ? FALSE : $query->_fetch_object();
        exit;
    }
    
    public function get_category_type_ci_key_category_id($i_category_id){
        $this->db->cache_on();
        $this->db->select('menu.ci_key, categories.id AS category_id, categories.name AS category_name, categories.category_type_id');
        $this->db->from("menu");
        $this->db->join('categories','categories.id = menu.category_id','inner');
        $this->db->where("category_id", $i_category_id);
        $this->db->where("categories.status", 1);
        $this->db->limit(1);
        
        $query = $this->db->get();
        return ( $query->num_rows() == 0 ) ? FALSE : $query->_fetch_object();
        exit;
    }
   
}