<?php

class Free_user_preference extends DataMapper   {

    var $table = "free_user_preference";
    
    public function get_by_user_id($user_id = 0) {
        
        $user_id = (empty($user_id)) ? get_free_user_session('id') : $user_id;
        
        $obj_pref = $this->where('free_user_id', $user_id)->get();
        
        return ( empty($obj_pref->all) ) ? FALSE : $obj_pref;
        
    }
}
