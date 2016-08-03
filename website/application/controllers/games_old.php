<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class games_old extends CI_Controller {

    public function __construct() 
    {
        parent::__construct();
        
    }
    
    

    function index()
    {
        $ar_segmens = $this->uri->segment_array();
        //unset($ar_segmens[0]);
        //array_shift($ar_segmens);
        $str_segmant = implode("/", $ar_segmens);
       
        header("Location:" . base_url() . "games-old/" . $str_segmant);
         
    }
    
   
    
    
}
