<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Defaults extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * @param array $param array('key' => 'value to the key filed', 'value' => 'value to the `value` filed')
     * @return int last inserted id or bool false
     * * */
    public function create($param) {
        $this->db->set_dbprefix('');
        return ($this->db->insert('defaults', $param)) ? (int) $this->db->insert_id() : FALSE;
    }

    /**
     * @param array $param array('name of the field as key' => 'value of the field as value')
     * @return assoc array data
     * * */
//    public function getData($param = array()) {
//        $data = $this->getRawData($param);
//        return ($data !== FALSE) ? $data : FALSE;
//    }

    /**
     * @param array $param array('name of the field as key' => 'value of the field as value')
     * @return assoc array data
     * */
    public function getData($param = array(), $b_all = FALSE) {
        $this->db->set_dbprefix('');
        $this->db->select('*');
        if (!empty($param)) {
            foreach ($param as $key => $value) {
                $this->db->where($key, $value);
            }
        }

        $this->db->order_by('value asc');
        $this->db->from('defaults');
        $data = $this->db->get()->result_array();

        if (!$b_all) {
            $data = $data[0];
        }

        return (!empty($data)) ? $data : FALSE;
    }

    /**
     * @param int $id
     * @return bool true or false
     * * */
    public function delete($id) {
        $this->db->set_dbprefix('');
        $this->db->where('id', $id);
        return $this->db->delete('defaults');
    }

    /**
     * @param int $id id of the record to be updated
     * @param array $param array('key' => 'value to the key filed', 'value' => 'value to the `value` filed')
     * @return bool true or false
     * * */
    public function update($id, $param) {
        $this->db->set_dbprefix('');
        $this->db->where('id', $id);
        return $this->db->update('defaults', $param);
    }

}
