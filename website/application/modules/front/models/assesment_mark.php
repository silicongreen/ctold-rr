<?php

/**
 * Description of Assessment
 *
 * @author NIslam
 */
class Assesment_mark extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "assesment_mark";
    
    private $ar_fields = array(
        'id',
        'user_id',
        'assessment_id',
        'mark',
        'created_date'
    );

    public function get_fields() {
        foreach ($this->ar_fields as $field_key => $field_name) {
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }

    function get_assesment_mark_by_id($id) {
        
        $this->db->select('*');
        $this->db->from("assesment_mark");
        $this->db->where("assesment_mark.id", $id);
        $obj_assessment = $this->db->get()->row();
        
        $data['data'] = $obj_assessment;

        return $data;
    }
    
    function find_assessment_mark($user_id = 0, $assessment_id = 0, $cur_level = 0, $ar_select = array()) {
        
        $select = (empty($ar_select)) ? '*' : implode(',', $ar_select);
        
        $this->db->select($select);
        if($user_id > 0) {
            $this->db->where("assesment_mark.user_id", $user_id);
        }
        if($assessment_id > 0) {
            $this->db->where("assesment_mark.assessment_id", $assessment_id);
        }
        
        if($cur_level > 0) {
            $this->db->where("assesment_mark.level", $cur_level);
        }
        
        $this->db->from("assesment_mark");
        
        $obj_assessment_mark = $this->db->get()->row();
        
        return (!empty($obj_assessment_mark)) ? $obj_assessment_mark : false;
        
    }
    
    function find_assessment_mark_all($user_id = 0, $assessment_id = 0, $cur_level = 0, $ar_select = array()) {
        
        $select = (empty($ar_select)) ? '*' : implode(',', $ar_select);
        
        $this->db->select($select);
        if($user_id > 0) {
            $this->db->where("assesment_mark.user_id", $user_id);
        }
        if($assessment_id > 0) {
            $this->db->where("assesment_mark.assessment_id", $assessment_id);
        }
        
        if($cur_level > 0) {
            $this->db->where("assesment_mark.level", $cur_level);
        }
        
        $this->db->from("assesment_mark");
        
        $obj_assessment_mark = $this->db->get()->result();
        
        return (!empty($obj_assessment_mark)) ? $obj_assessment_mark : FALSE;
        
    }
    
    function find_user_assessment_total_mark($user_id = 0, $assessment_id = 0) {
        
        $this->db->select('SUM(mark) AS mark');
        if($user_id > 0) {
            $this->db->where("assesment_mark.user_id", $user_id);
        }
        if($assessment_id > 0) {
            $this->db->where("assesment_mark.assessment_id", $assessment_id);
        }
        
        $this->db->from("assesment_mark");
        
        $obj_assessment_mark = $this->db->get()->row();
        
        return (!empty($obj_assessment_mark)) ? $obj_assessment_mark : false;
        
    }

    public function get_attributes() {

        return array(
            'user_id' => 'User',
            'assessment_id' => 'Assessment',
            'mark' => 'Mark'
        );
    }

}
