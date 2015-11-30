<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Activation extends CI_Controller {

    public function index() {
        $this->load_view('activate');
    }
    private function load_view($view_name, $data = array()) {
        $this->load->view('layout/header');
        $this->load->view($view_name, $data);
        $this->load->view('layout/footer_other');
    }

}
