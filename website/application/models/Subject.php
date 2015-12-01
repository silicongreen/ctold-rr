<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Subject extends CI_Model {

    public $table_name = 'subjects';
    private $_now;
    private $_school_id;
    private $_num_zeros_for_subject_code = array(
        '0' => '00000',
        '1' => '0000',
        '2' => '000',
        '3' => '00',
        '4' => '0',
    );

    public function __construct() {
        parent::__construct();
        $this->db->set_dbprefix('');
        $this->_now = date('Y-m-d H:i:s');
    }

    public function init($school_id = 0) {
        $this->_school_id = $school_id;
    }

    /**
     * @param array $param array('key' => 'value to the key filed', 'value' => 'value to the `value` filed')
     * @return int last inserted id or bool false
     * */
    public function create($param) {
        $data = $this->preprocess($param);
        $this->clear();

        if (isset($data['error']) && !empty($data['error'])) {
            return $data;
        }
        
        return ($this->db->insert_batch($this->table_name, $data)) ? (int) $this->db->insert_id() : FALSE;
    }

    /**
     * @param int $id
     * @return assoc array data
     * */
    public function getDataById($id) {
        $this->db->select('*');
        $this->db->where('id', $id);
        $this->db->from($this->table_name);
        $data = $this->db->get()->result_array();
        return (!empty($data)) ? $data[0] : FALSE;
    }

    public function getCourses() {
        $this->db->select('*');
        $this->db->where('school_id', $this->_school_id);
        $this->db->where('is_deleted', 0);
        $this->db->from('courses');
        $this->db->group_by('course_name');
        $data = $this->db->get()->result_array();
        return (!empty($data)) ? $data : FALSE;
    }

    public function getBatches($course_id = 0, $course_name = '') {
        $this->db->select('*, courses.id as course_id, batches.id as batch_id');
        $this->db->where('courses.school_id', $this->_school_id);
        $this->db->where('courses.is_deleted', 0);
        $this->db->from('courses');

        if ($course_id > 0) {
            $this->db->where('courses.id', $course_id);
        }

        if (!empty($course_name)) {
            $this->db->where('courses.course_name', $course_name);
        }

        $this->db->join('batches', 'batches.course_id = courses.id', 'inner');

        $data = $this->db->get()->result_array();
        return (!empty($data)) ? $data : FALSE;
    }

    public function formatForDropdown($param) {

        $response = array();
        
        $response[0] = 'Select';
        foreach ($param as $row) {
            $response[$row['id']] = $row['course_name'];
        }
        return $response;
    }

    /**
     * @param int $id
     * @return bool true or false
     * */
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table_name);
    }

    /**
     * @param delete all records from table except System Admin
     * @return nothing
     * */
    public function clear() {
        $this->db->where('school_id', $this->_school_id);
        return $this->db->delete($this->table_name);
    }

    /**
     * @param int $id id of the record to be updated
     * @param array $param array('key' => 'value to the key filed', 'value' => 'value to the `value` filed')
     * @return bool true or false
     * */
    public function update($id, $param) {
        $this->db->where('id', $id);
        return $this->db->update($this->table_name, $param);
    }

    private function preprocess($data, $mode = 'create') {

        $i = 0;
        $response = array();
        $ar_subject_names_classes = array();

        foreach ($data as $value) {
            $ar_subject_class = explode('==', $value);
            $ar_subject_names_classes[$ar_subject_class[1]][] = $ar_subject_class[0];
        }

        foreach ($ar_subject_names_classes as $course_name => $ar_subject_name) {

            $ar_course_batch = $this->getBatches(0, $course_name);

            foreach ($ar_course_batch as $course_batch) {
                
                $j = 1;
                $subject_codes = array();
                foreach ($ar_subject_name as $subject_name) {

                    $response[$i]['name'] = $subject_name;

                    $subject_code = substr($subject_name, 0, 3);
                    $subject_code = $subject_code . $this->_num_zeros_for_subject_code[strlen($subject_code)] . $j;
                    $subject_code = strtoupper($subject_code);
                    
                    $response[$i]['code'] = $subject_code;
                    $response[$i]['batch_id'] = $course_batch['batch_id'];
                    $response[$i] = array_merge($response[$i], $this->before_save());
                    $j++;
                    $i++;
                }
            }
        }
        
        return $response;
    }

    private function before_save() {
        $data = array(
            'created_at' => $this->_now,
            'updated_at' => $this->_now,
            'max_weekly_classes' => 5,
            'credit_hours' => 100.00,
            'school_id' => $this->_school_id
        );

        return $data;
    }

}
