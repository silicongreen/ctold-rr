<?php

/**
 * Description of Assessment
 *
 * @author NIslam
 */
class Assessment_school_mark_temp extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "assessment_school_mark_temp";
    
    private $ar_fields = array(
        'id',
        'user_id',
        'assessment_id',
        'school_id',
        'mark',
        'created_date'
    );

    public function get_fields() {
        foreach ($this->ar_fields as $field_key => $field_name) {
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }

    function get_assesment_school_mark_by_id($id) {
        
        $this->db->select('*');
        $this->db->from("assessment_school_mark_temp");
        $this->db->where("assessment_school_mark_temp.id", $id);
        $obj_assessment = $this->db->get()->row();
        
        $data['data'] = $obj_assessment;

        return $data;
    }
    
    function find_assessment_school_mark($user_id = 0, $assessment_id = 0, $cur_level = 0, $school_id = 0, $ar_select = array()) {
        
        $select = (empty($ar_select)) ? '*' : implode(',', $ar_select);
        
        $this->db->select($select);
        if($user_id > 0) {
            $this->db->where("assessment_school_mark_temp.user_id", $user_id);
        }
        if($assessment_id > 0) {
            $this->db->where("assessment_school_mark_temp.assessment_id", $assessment_id);
        }
        
        if($cur_level > 0) {
            $this->db->where("assessment_school_mark_temp.level", $cur_level);
        }
        
        if($school_id > 0) {
            $this->db->where("assessment_school_mark_temp.school_id", $school_id);
        }
        
        $this->db->from("assessment_school_mark_temp");
        
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
