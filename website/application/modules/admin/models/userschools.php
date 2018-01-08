<?php

class userschools extends DataMapper {

    var $table = "user_created_school";

    public function get_user_submitted_school($school_id = 0) {
        
        if($school_id > 0) {
            $this->db->where('id', $school_id);
        }
        
        $this->db->group_by('freeuser_id');
        $this->db->from('user_created_school');
        
        $data = $this->db->get()->result();
        
        return (!empty($data)) ? $data : FALSE;
    }
    
}
