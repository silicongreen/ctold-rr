<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Issue Date
 *
 * @author NIslam
 */
class Issue_date extends DataMapper
{
    var $table = "issue_date";
    
    private $ar_fields = array(
                        'id',
                        'issue_date',
                    );
    
    public function get_fields(){
        foreach($this->ar_fields as $field_key => $field_name){
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }
    
    public function found($str_date){
        $this->select("COUNT(id) AS cnt_id");
        $this->where('issue_date', $str_date);
        $res = $this->get();
        return ($res->cnt_id > 0) ? true : false;
        exit;
    }
}

?>
