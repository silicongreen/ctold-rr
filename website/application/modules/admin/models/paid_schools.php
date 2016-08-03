<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Paid_schools
 *
 * @author NIslam
 */
class Paid_schools extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "paid_schools";
    private $ar_fields = array('id', 'school_name', 'school_id', 'school_code', 'redirect_url', 'client_id', 'client_secret', 'username', 'password', 'is_active');

    public function get_fields() {
        foreach ($this->ar_fields as $field_key => $field_name) {
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }

    function get_free_user_by_id($id) {
        $attributes = $this->get_attributes();

        $ar_attributes[] = 'free_users.id';
        foreach ($attributes as $key => $value) {
            if ($key != 'tds_country_id') {
                $ar_attributes[] = $key;
            }
        }

        $this->db->select(implode(',', $ar_attributes) . ', countries.name AS tds_country_id');
        $this->db->from("free_users")->join("countries as countries", "free_users.tds_country_id = countries.id", 'LEFT');
        $this->db->where("free_users.id", $id);
        $obj_users = $this->db->get()->row();

        $data['_attributes'] = $attributes;
        $data['data'] = $obj_users;

        return $data;
    }

    public function get_attributes() {

        return array(
            'school_name' => 'School Name',
            'school_id' => 'School ID',
            'school_code' => 'School Code',
            'redirect_url' => 'Redirect URL',
            'client_id' => 'Client ID',
            'client_secret' => 'Client Secret',
            'username' => 'Username',
            'password' => 'Password',
            'is_active' => 'Active',
        );
    }

}
