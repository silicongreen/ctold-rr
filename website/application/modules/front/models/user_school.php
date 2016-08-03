<?php

class User_school extends DataMapper {

    var $table = "user_school";
    
    public function get_user_school($user_id = 0, $school_id = 0) {
        
        if($user_id > 0){
            $this->db->where('user_id', $user_id);
        }
        
        if($school_id > 0){
            $this->db->where('school_id', $school_id);
        }
        
        $data = $this->db->get('user_school')->result();
        return (count($data) > 0) ? $data : FALSE;
    }
    
}
