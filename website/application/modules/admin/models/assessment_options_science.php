<?php

/**
 * Description of Assessment Option
 *
 * @author NIslam
 */
class Assessment_options_science extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "science_rocks_option";
    var $validation = array(
        'question_id' => array(
            'label' => 'Question',
            'rules' => array('required', 'trim'),
        ),
        'answer' => array(
            'label' => 'Answer',
            'rules' => array('required', 'trim'),
        ),
        'correct' => array(
            'label' => 'Correct',
            'rules' => array('number'),
        ),
    );
    
    public function get_assessment_option_by_id($id) {

        $this->db->select('*');
        $this->db->from("science_rocks_option");
        $this->db->where("science_rocks_option.id", $id);
        return $this->db->get()->row();
    }

    public function get_assessment_option_by_q_id($q_id) {

        $this->db->select('*');
        $this->db->from("science_rocks_option");
        $this->db->where("science_rocks_option.question_id", $q_id);
        return $this->db->get()->result();
    }

    public function del_assessment_option_by_q_id($q_id) {

        $this->db->delete('science_rocks_option', array('question_id' => $q_id));
        if ($this->db->_error_message()) {
            return false;
        } else if (!$this->db->affected_rows()) {
            return false;
        } else {
            return true;
        }
    }
    
}
