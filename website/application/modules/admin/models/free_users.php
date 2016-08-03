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
class Free_users extends DataMapper {

    public $my_errors = array();
    //put your code here
    var $table = "free_users";
    private $ar_fields = array('id', 'username', 'email', 'password', 'fb_profile_id', 'gl_profile_id', 'first_name', 'middle_name', 'last_name', 'tds_country_id', 'district', 'mobile_no', 'dob', 'medium', 'gender', 'grade_ids', 'user_type', 'user_type_text', 'salt', 'cover_image', 'profile_image', 'status');
    
    public function get_fields() {
        foreach ($this->ar_fields as $field_key => $field_name) {
            $fields[$field_name] = $field_name;
        }
        return $fields;
    }
    
    function get_free_user_by_id($id)
    {
        $attributes = $this->get_attributes();
        
        $ar_attributes[] = 'free_users.id';
        foreach ($attributes as $key => $value) {
            if($key != 'tds_country_id'){
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
    
    public function get_attributes(){
        
        return array(
//            'id' => 'ID',
            'email' => 'Email',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'tds_country_id' => 'Country',
            'district' => 'District',
            'mobile_no' => 'Mobile',
            'dob' => 'Date of Birth',
            'medium' => 'Study Medium',
            'gender' => 'Gender',
            'grade_ids' => 'Grades / Classes',
            'user_type' => 'User Type',
//            'cover_image' => 'Cover Image',
            'profile_image' => 'Profile Image',
            'status' => 'Status',
        );
        
    }

}
