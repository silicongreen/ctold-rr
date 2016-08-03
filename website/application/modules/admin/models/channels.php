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
class Channels extends DataMapper  {
    
    //put your code here
    var $table = "channels";
    private $ar_fields = array('id', 'name', 'logo', 'is_active');
    
    public function get_fields(){
        foreach($this->ar_fields as $field_key => $field_name){
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }
    
    public function get_channel_by_id($int_id){
        $int_id = (int)$int_id;
        if(empty($int_id) || ($int_id < 1)){
            return false;
        }
        
        $this->select('*');
        $this->where('id', $int_id, false);
        $channel = $this->get();
        return (!empty($channel->id)) ? $channel : false;
        exit;
    }
    
    public function get_channel_by_name($str_name, $like = false){
        $this->select('*');
        ($like) ? $this->like('name', $str_name, 'after') : $this->where('name', $str_name);
        $this->order_by('name', 'ASC');
        $this->limit(10);
        $channel = $this->get();
        return (!empty($channel->id)) ? $channel : false;
        exit;
    }
    
}

?>
