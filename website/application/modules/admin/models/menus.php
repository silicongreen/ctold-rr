<?php

class Menus extends CI_Model {   
    
    private $ar_menu_types = array();
    private $ar_fields = array(
                        'id',
                        'type',
                        'title',
                        'icon_name',
                        'is_active',
                        'position',
                        'footer_group',
                        'ci_key',
                        'link_type',
                        'link_text',
                        'permalink',
                        'category_id',
                        'news_id',
                        'news_num',
                        'priority',
                        'created',
                        'updated',
                        'startdate',
                        'expired',
                        'parent_menu_id',
                    );
    
    public function __construct($ar_params = null){
        if(!empty($ar_params)){
            $this->set_menu_types($ar_params);
        }
        parent::__construct();
    }
    
    /*public function initialize($config = array()){
        if(is_array($config) && !empty($config)){
            if(array_key_exists('menu_type',$config)){
                $this->set_menu_types($config['menu_type']);
            }
        }
    }*/
    
    protected function set_menu_types($menu_types){
        $this->ar_menu_types = $menu_types;
    }
    
    public function get_menu_types($type_index = null){
        return (empty($type_index)) ? $this->ar_menu_types : $this->ar_menu_types[$type_index];
    }
    
    /**
     * @function get_menus will return menu records. if id (PK) is not set then it'll retrun all rows
     * else it'll return only the row that id matches to the param $id
     * @param $id string $id 
     * @param $fields array of fields to be selected
    */
    public function get_menus($id = null, $fields = array()){
        $select = (empty($fields)) ? '*' : implode(',', $fields);
        $this->db->select($select);
        $this->db->from($this->table_name());
        if(!empty($id)){
            $this->db->where('id',(int)$id);
        }
        $q = $this->db->get();
        return $q->result_array();
        exit;
    }
    
    public function get_fields(){
        foreach($this->ar_fields as $field_key => $field_name){
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }
    
    public function get_sub_menus($id = null, $fields = array()){
        $select = (empty($fields)) ? '*' : implode(',', $fields);
        $this->db->select($select);
        $this->db->from($this->table_name());
        if(!empty($id)){
            $this->db->where('parent_menu_id',(int)$id);
        }
        $q = $this->db->get();
        return $q->result_array();
        exit;
    }
    
    public function prepare_sub_cat_ar($arrs){
        $n_arr = array();
        foreach($arrs as $arr){
            array_push($n_arr, $arr['category_id']);
        }
        return $n_arr;
        exit;
    } 
    
    public function table_name(){
        return 'menu';
    }
    
    public function file_fields(){
        return array(
            'icon_name',
        );
    }
   
    public function rules(){
        $rules = array(
                  array(
                     'field'   => 'menus[position]',
                     'label'   => 'Location',
                     'rules'   => 'required|integer|callback___position_validation'
                  ),
                  array(
                     'field'   => 'menus[type]',
                     'label'   => 'Menu Types',
                     'rules'   => 'required|integer|callback___menu_types_validation'
                  ),
                  array(
                     'field'   => 'menus[title]',
                     'label'   => 'Menu Title',
                     'rules'   => 'required'
                  ),
                  array(
                     'field'   => 'menus[icon_name]',
                     'label'   => 'Expire Date',
                     'rules'   => (isset($_FILES['menus']) && !empty($_FILES['menus']['name']['icon_name'])) ? 'callback___icon_name_validation' : ''
                  ),
                  array(
                     'field'   => 'menus[news_num]',
                     'label'   => 'Number of News',
                     'rules'   => 'integer|callback___news_num_validation'
                  ),
                  array(
                     'field'   => 'menus[priority][]',
                     'label'   => 'Priority',
                     'rules'   => 'integer|callback___priority_validation'
                  ),
                  array(
                     'field'   => 'menus[news_id]',
                     'label'   => 'News',
                     'rules'   => ((int)$_POST['menus']['type'] == 4) ? 'required|integer' : ''
                  ),
                  array(
                     'field'   => 'menus[news_num]',
                     'label'   => 'News Number',
                     'rules'   => 'max_length[5]'
                  ),
                  array(
                     'field'   => 'menus[expired]',
                     'label'   => 'Expire Date',
                     'rules'   => 'callback___expired_validation'
                  ),
                  array(
                     'field'   => 'menus[category_id]',
                     'label'   => 'Category',
                     'rules'   => (isset($_POST['menus']['category_id']) && !empty($_POST['menus']['category_id'])) ? 'integer' : ''
                  ),
                  array(
                     'field'   => 'menus[ci_key]',
                     'label'   => 'CI Key',
                     'rules'   => 'is_unique[menu.ci_key]'
                  ),
            );
        return $rules;
    }
    
    /**
     * @function save_model saves model to the DB
     * @param $model is $key => $value assocciative array
     * if $model['id'] exists and $model['id'] is not null the it'll be update operation
     * else insert operation.
    */
    public function save_model($model){
        if(isset($model['id']) && !empty($model['id'])){
            if(!is_integer((int)$model['id'])){
                return false;
                exit;
            }else{
                $this->db->where('id',(int)$model['id']);
                return ($this->db->update($this->table_name(), $model)) ? (int)$model['id'] : false;
                exit;
            }
        }else{
            if($this->db->insert($this->table_name(), $model)){
                $parent_id = $this->db->insert_id();
                return (int)$parent_id;
                exit;
            }else{
                return false;
                exit;
            }
        }
    }
    
    public function delete_sub_cat($id){
        # delete submenus
        $this->db->where('parent_menu_id', $id);
        $this->db->delete('menu');
        # delete submenus
        return true;
    }
}
