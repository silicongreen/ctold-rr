<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Activity_logs extends CI_Model {
    
    private $table_name = 'activity_logs';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function record($controller, $action, $user_agent = NULL)
    {
        $user_info = get_free_user_session();
        $user_id = (free_user_logged_in()) ? $user_info['id'] : 0;
        $school_id = (free_user_logged_in() && !empty($user_info['paid_school_id']) ) ? $user_info['paid_school_id'] : 0;
        $school_name = (free_user_logged_in() && empty($user_info['paid_school_id']) && !empty($user_info['school_name']) ) ? $user_info['school_name'] : NULL;
        $cur_time = date('Y-m-d H:i:s', time());
        
        $data = array(
            'user_id' => $user_id,
            'school_id' => $school_id,
            'controller' => $controller,
            'action' => $action,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => ($user_agent) ? $user_agent : $_SERVER['HTTP_USER_AGENT'],
            'created_at' => $cur_time,
            'updated_at' => $cur_time,
            'free_site' => 1,
            'school_name' => $school_name
        );

        $this->db->dbprefix = '';
        $this->db->insert($this->table_name(), $data);
        $this->db->dbprefix = 'tds_';
        return true;
    }
    
    public function table_name() {
        return $this->table_name;
    }
      
}
?>
