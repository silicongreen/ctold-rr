<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Shift extends CI_Model {

    public $table_name = 'batches';
    private $_now;
    private $_school_id;

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

        if ($course_data = $this->getCourseData()) {
            $data = $this->preprocess($course_data, $param);
            $this->clear();

            if (isset($data['error']) && !empty($data['error'])) {
                return $data;
            }

            return ($this->db->insert_batch($this->table_name, $data)) ? (int) $this->db->insert_id() : FALSE;
        } else {
            return $data['error'] = 'No Classes found.';
        }
    }

    public function getCourseData() {
        $this->db->select('*');
        $this->db->where('school_id', $this->_school_id);
        $this->db->from('courses');
        $data = $this->db->get()->result_array();
        return (!empty($data)) ? $data : FALSE;
    }

    public function getWeekdaySetData() {
        $this->db->select('*');
        $this->db->where('school_id', $this->_school_id);
        $this->db->from('weekday_sets');
        $data = $this->db->get()->result_array();
        return (!empty($data)) ? $data[0] : FALSE;
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

    private function preprocess($course_data, $data, $mode = 'create') {

        $i = 0;
        $response = array();

        $week_day_set_data = $this->getWeekdaySetData();

        foreach ($course_data as $course) {
            foreach ($data as $key => $value) {

                $response[$i]['name'] = $value;
                $response[$i]['course_id'] = $course['id'];
                $response[$i]['weekday_set_id'] = $week_day_set_data['id'];
                $response[$i]['start_date'] = date('Y-m-d 00:00:00', strtotime($this->_now));
                $response[$i]['end_date'] = date("Y-m-d 00:00:00", strtotime(date("Y-m-d", strtotime($this->_now)) . " + 1 year"));

                $response[$i] = array_merge($response[$i], $this->before_save());
                $i++;
            }
        }
        
        return $response;
    }

    private function before_save() {
        $data = array(
            'created_at' => $this->_now,
            'updated_at' => $this->_now,
            'school_id' => $this->_school_id
        );

        return $data;
    }

}
