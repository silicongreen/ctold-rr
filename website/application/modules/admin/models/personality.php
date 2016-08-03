<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Gallery
 *
 * @author ahuffas
 */
class Personality extends DataMapper  {
    
    //put your code here
    var $table = "personality";
    private $ar_fields = array('id', 'name', 'description', 'is_active');
    
    
    public function get_fields(){
        foreach($this->ar_fields as $field_key => $field_name){
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }
    
    public function get_all_personalities(){
        $this->select('id, name');
        $this->order_by('id', 'ASC');
        $personalities = $this->get();
        
        return $personalities;
    }
    
    public function personalities_filter_dropdown(){
        $personalities = $this->get_all_personalities();
        $ar_personalities[null] = 'Select';
        foreach($personalities as $key => $value){
            $ar_personalities[$value->name] = $value->name;
        }
        
        return $ar_personalities;
    }
    
    public function get_personality_by_id($int_id){
        $int_id = (int)$int_id;
        if(empty($int_id) || ($int_id < 1)){
            return false;
        }
        
        $this->select('*');
        $this->where('id', $int_id, false);
        $personality = $this->get();
        return (!empty($personality->id)) ? $personality : false;
        exit;
    }
    
    public function get_personality_by_name($str_name, $like = false){
        $this->select('*');
        ($like) ? $this->like('name', $str_name, 'after') : $this->where('name', $str_name);
        $this->order_by('name', 'ASC');
        $this->limit(10);
        $personality = $this->get();
        return (!empty($personality->id)) ? $personality : false;
        exit;
    }
    
}

?>
