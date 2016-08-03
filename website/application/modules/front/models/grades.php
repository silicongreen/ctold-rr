<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FreeUsers
 *
 * @author ahuffas
 */
class Grades extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "grades";
    private $ar_fields = array('id', 'grade_name', 'status');

    public function get_fields() {
        foreach ($this->ar_fields as $field_key => $field_name) {
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }
    
    public function getActiveGrades() {
        
        $this->where('status', 1);
        $data = $this->get();
        
        return $data;
    }
    
}
