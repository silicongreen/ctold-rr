<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class User_gk_answers extends CI_Model{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_user_gk_answers( $i_user_id, $offset = 0, $limit = 1 )
    {
//        var_dump($offset * $limit);
//        exit;
        $this->db->select('SQL_CALC_FOUND_ROWS date, GROUP_CONCAT(question) AS question, GROUP_CONCAT(user_answer) AS user_answer',false )
                ->from('user_gk_answers')
                ->where('user_id', $i_user_id, FALSE)
                ->offset( $offset * $limit )
                ->limit( $limit )
                ->order_by( 'date DESC' )
                ->group_by( "date" );
        $query = $this->db->get(); 
        //var_dump($query->num_rows() > 0);exit;
        $query_num_rows = $this->db->query('SELECT FOUND_ROWS() AS `Count`');
        $totaldata = $query_num_rows->row()->Count;
        
        return ( $query->num_rows() > 0 ) ? array("total" => $totaldata, "data" => $query->result()) : FALSE;
    }  
    
    
    
      
}
?>
