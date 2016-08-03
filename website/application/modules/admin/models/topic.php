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
class Topic extends DataMapper  {
    
    //put your code here
    var $table = "topic";
    private $ar_fields = array('id', 'topic');
    
    
    public function get_fields(){
        foreach($this->ar_fields as $field_key => $field_name){
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }
    
    public function get_topic_by_id($int_id){
        $int_id = (int)$int_id;
        if(empty($int_id) || ($int_id < 1)){
            return false;
        }
        
        $this->select('*');
        $this->where('id', $int_id, false);
        $topic = $this->get();
        return (!empty($topic->id)) ? $topic : false;
        exit;
    }
    
    public function get_topic_by_name($str_name, $like = false){
        $this->select('*');
        ($like) ? $this->like('topic', $str_name, 'after') : $this->where('topic', $str_name);
        $this->order_by('topic', 'ASC');
        $this->limit(10);
        $topic = $this->get();
        return (!empty($topic->id)) ? $topic : false;
        exit;
    }
    
}

?>
