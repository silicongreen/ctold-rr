<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('PHPMailer/class.phpmailer.php');

 class Mailer extends PHPMailer
 {
    public function __construct() 
    {
       parent::__construct();
    }
 }
?>
