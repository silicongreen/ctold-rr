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
class WhatsOn extends DataMapper  {
    public $my_errors = array();
    
    //put your code here
    var $table = "whats_on";
    private $ar_fields = array('id', 'channel_id', 'program_type', 'program_details', 'category_id', 'show_date', 'is_active');
    
    public function get_fields(){
        foreach($this->ar_fields as $field_key => $field_name){
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }
    
    public function myValidation($ar_model){
        if(!isset($ar_model['channel_id'])){
            $this->my_errors['channel_id'][] = 'Channel Name is required.';
        }
        
        $int_ch_id = (isset($ar_model['channel_id'])) ? (int)$ar_model['channel_id'] : null;
        if($int_ch_id < 1){
            $this->my_errors['channel_id'][] = 'Invalid Channel Name format.';
        }
        
        if(!isset($ar_model['category_id'])){
            $this->my_errors['category_id'][] = 'Category is required.';
        }
        
        $int_cat_id =  (isset($ar_model['category_id'])) ? (int)$ar_model['category_id'] : null;
        if($int_cat_id < 1){
            $this->my_errors['category_id'][] = 'Invalid Category format.';
        }
        
        $s_date = strtotime($ar_model['show_date']);
        if(!is_integer($s_date) || strlen($s_date) < 4){
            $this->my_errors['show_date'][] = 'Invalid Show Date format.';
        }
        
        return (!empty($this->my_errors)) ? false : true;
    }
    
    public function get_events($category_id, $ar_issue_date){
        
        $sql = "SELECT
                	w.id,
                    w.category_id AS category_id,
                    ch.`name` AS channel_id,
                	w.program_details AS content,
                	c.`name` AS category_name,
                	w.show_date
                FROM
                	`tds_whats_on` AS w
                INNER JOIN `tds_channels` AS ch ON w.channel_id = ch.id
                INNER JOIN `tds_categories` AS c ON w.category_id = c.id
                WHERE w.category_id = '". $category_id ."' AND w.is_active = 1 AND w.show_date = '". date('Y-m-d', strtotime($ar_issue_date['s_issue_date'])) ."'";
        
        $obj_whats_on = $this->query($sql);
        
        return (sizeof($obj_whats_on->all) > 0) ? $obj_whats_on : false;
    }
    
}

?>
