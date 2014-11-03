<?php

class Category extends DataMapper {

    var $table = "categories";
    
    var $validation = array(
        'name' => array(
            'label' => 'name',
            'rules' => array('required', 'trim', 'unique_pair' => 'parent_id'),
        )
    );

    public function get_all_categories(){
        $this->select('id, name');
        $this->order_by('id', 'ASC');
        $categories = $this->get();
        
        return $categories;
    }
    
    public function categories_filter_dropdown(){
        $categories = $this->get_all_categories();
        $ar_categories[null] = 'Select';
        foreach($categories as $key => $value){
            $ar_categories[$value->name] = $value->name;
        }
        
        return $ar_categories;
    }
    
    public function get_category_name_by_id($id = 0){
        $this->select('id AS category_id, category_type_id, name');
        $this->where('id', $id);
        $this->order_by('category_id', 'desc');
        $this->limit(1);
        $obj_category = $this->get();
        return (count($obj_category->all) > 0 ) ? $obj_category : FALSE;
        exit;
    }
    
    public function get_category_and_ci_key_by_id($i_category_id){
        $this->db->select('categories.id AS category_id, categories.name AS category_name, categories.category_type_id');
        $this->db->from("categories");
        $this->db->where("id", $i_category_id);
        $this->db->where("status", 1);
        
        $query = $this->db->get();
        return ( $query->num_rows() == 0 ) ? FALSE : $query->_fetch_object();
    }
   
}
