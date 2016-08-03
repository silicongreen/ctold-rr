<?php

class Menus extends CI_Model 
{   
    
    public function __construct(){
        parent::__construct();
    }
    
    public function get_menu_by_ci_key( $s_key )
    {                
        $this->db->cache_on();
        $this->db->select('*');
        $this->db->from("menu");
        $this->db->join('categories','categories.id = menu.category_id','left');
        $this->db->where("ci_key", $s_key);
        
        $query = $this->db->get();
        return ( $query->num_rows() == 0 ) ? FALSE : $query->_fetch_object();
    }
    
    public function get_category_pdf_from_menu_by_ci_key($s_key){
        $this->db->cache_on();
        $this->db->select('categories.id AS category_id, categories.name AS category_name, categories.category_type_id');
        $this->db->from("menu");
        $this->db->join('categories','categories.id = menu.category_id','inner');
        $this->db->where("ci_key", $s_key);
        $this->db->where("categories.status", 1);
        $this->db->limit(1);
        
        $query = $this->db->get();
        return ( $query->num_rows() == 0 ) ? FALSE : $query->_fetch_object();
    }
    
    public function get_parent_menu_by_ci_key( $s_key )
    {
         
        $this->db->cache_on();
        $this->db->select('*');
        $this->db->from("menu");
        
        $this->db->where("ci_key", $s_key);
        
       
        $query = $this->db->get()->row();
        if(count($query)== 0 )
        {
            return false;
        }    
        else
        {
            
            $this->db->select('*');
            $this->db->from("menu");

            $this->db->where("id", $query->parent_menu_id);

            $query_parent = $this->db->get()->row();
             
            if(count($query_parent) == 0 )
            {
                return false;
            }
            else
            {
                return $query_parent->ci_key;
            }
        }
      
        
    }
    
    public function get_menu_by_id( $id )
    {
        $this->db->cache_on();
        $this->db->select('*');
        $this->db->from("menu");
        
        $this->db->where("id", $id);
        
        $query = $this->db->get();
        return ( $query->num_rows() == 0 ) ? FALSE : $query->_fetch_object();
    }
    
    public function get_menu_key_by_cat_id( $cat_id )
    {
        $this->db->cache_on();
        $this->db->select('ci_key');
        $this->db->from("menu");
        
        $this->db->where("category_id", $cat_id);
        
        $a_ci_key = $this->db->get()->row();
        
        if(count($a_ci_key)>0)
        {
            return $a_ci_key->ci_key;
        }    
        else
        {
            $this->db->select('name');
            $this->db->from("categories");
        
            $this->db->where("id", $cat_id);
            $a_ci_key = $this->db->get()->row();
            
            return sanitize($a_ci_key->name);
        }    
       
    }
    
}
