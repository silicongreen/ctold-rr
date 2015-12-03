<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Error_logs extends CI_Model {

    public $table_name = 'error_logs';
    private $_now;

    /**
     * @param array $param array('key' => 'value to the key filed', 'value' => 'value to the `value` filed')
     * @return int last inserted id or bool false
     * * */
    public function record($param) {
        $data = $this->preprocess($param);
        return ($this->db->insert($this->table_name, $data)) ? (int) $this->db->insert_id() : FALSE;
    }

    /**
     * @param int $id
     * @return assoc array data
     * * */
    public function getData($id) {
        $data = $this->getRawData($id);
        return ($data !== FALSE) ? json_decode($data['value'], true) : FALSE;
    }

    /**
     * @param int $id
     * @return assoc array but the value as json string
     * * */
    public function getRawData($id) {
        $this->db->select('*');
        $this->db->where('id', $id);
        $this->db->from('tmp_data', $id);
        $data = $this->db->get()->result_array();
        return (!empty($data)) ? $data[0] : FALSE;
    }

    private function preprocess($data, $mode = 'create') {

        $message = '';
        if( isset($data['message']) && !empty($data['message']) ) {
            $message = ( !is_string($data['message']) ) ? json_encode($data['message']) : $data['message'];
        }
        
        $code = '';
        if( isset($data['code']) && !empty($data['code']) ) {
            $code = ( is_array($data['code']) || is_object($data['code']) ) ? json_encode($data['code']) : $data['code'];
        }

        
        $response['emsg'] = $message;
        $response['ecode'] = $code;
        $response['etype'] = $data['type'];

        return $response;
    }

}
