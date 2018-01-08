<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


if (!defined('BASEPATH')){exit('No direct script access allowed');}
class test extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('table');
    }
    
    public function index()
    {
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl);
        if(isset($_POST['submit'])){
            $this->load->library('upload_files', $_FILES, 'validateFile');
            var_dump($validateFile);
            exit;
            $validateFile->max_allowed_size = 328049;
            $validateFile->file_field = 'test_file';
            var_dump($validateFile->validate_file());
            exit;
        }
        $this->render('admin/test/index',$data);       
    } 
}    
?>
