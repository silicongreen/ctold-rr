<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        $this->load_view("index");
    }
	public function copyright() {        
		$this->load->view('layout/header');
        $this->load->view('copyright');
        $this->load->view('layout/footer_extra');
    }
	public function privacypolicy() {        
		$this->load->view('layout/header');
        $this->load->view('paivacypolicy');
        $this->load->view('layout/footer_extra');		
    }
	public function terms() {        	
		$this->load->view('layout/header');
        $this->load->view('terms');
        $this->load->view('layout/footer_extra');
    }

    //ajax function
    public function send_mail() {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            if (isset($_POST['name']) AND isset($_POST['email']) AND isset($_POST['subject']) AND isset($_POST['massage'])) {
                $to = 'info@classtune.com';
                $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $phone = filter_var($_POST['phone'], FILTER_SANITIZE_EMAIL);
                $user_type = filter_var($_POST['user_type'], FILTER_SANITIZE_EMAIL);
                $school_name = filter_var($_POST['school_name'], FILTER_SANITIZE_EMAIL);
                $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
                $message_content = filter_var($_POST['massage'], FILTER_SANITIZE_STRING);

                $subject = $subject. "(Classtune contact)";

                $message = "Name: " . $name . "<br/>";
                $message .= "User Type: " . $user_type . "<br/>";
                $message .= "School Name: " . $school_name . "<br/>";
                $message .= "Phone: " . $phone . "<br/>";
                $message .= "E-mail: " . $email . "<br/>";
                $message .= "Comment: " . $message_content . "<br/><br /><br />";

                $sent = $this->email($to, $email, $name, $subject, $message);
                if ($sent) {

                    $auto_name = "classtune.com";
                    $auto_subject = "Greetings from Classtune team";
                    $auto_message = "Dear " . $name . ",<br /><br />";
                    $auto_message .= "Greetings from Classtune team." . "<br /><br />";
                    $auto_message .= "Thank you very much for contacting with us. Our team will communicate with you within 48 hrs.  <br/><br />";
                    $auto_message .= "Your Contact Details: <br/><br />";
                    $auto_message .= "Name: " . $name . "<br/>";
                    $auto_message .= "E-mail: " . $email . "<br/>";
                    $auto_message .= "Comment: " . $message_content . "<br/><br /><br />";


                    $auto_message .= "Regards,<br/>";
                    $auto_message .= "Customer Service Team<br/>";
                    $auto_message .= "<img src='http://www.classtune.dev/images/logo/classtune.png'>";

                    $sent2 = $this->autoreply_email($email, $to, $auto_name, $auto_subject, $auto_message);
                    if ($sent2)
                    {
                        echo 'Message sent!Recently you will receive an email.';
                    }
                    else
                    {
                        echo 'Message sent!';
                    }    
                    
                } else {
                    echo "0";
                }
            } else {
                echo "1";
            }
            return;
        }
    }

    //PRIVATE FUNCTION
    private function load_view($view_name) {
        $this->load->view('layout/header');
        $this->load->view($view_name);
        $this->load->view('layout/footer');
    }

    private function email($to, $from_mail, $from_name, $subject, $message) {
        $header = array();
        $header[] = "MIME-Version: 1.0";
        $header[] = "From: {$from_name}<{$from_mail}>";
        /* Set message content type HTML */
        $header[] = "Content-type:text/html; charset=iso-8859-1";
        $header[] = "Content-Transfer-Encoding: 7bit";
        if (mail($to, $subject, $message, implode("\r\n", $header)))
            return true;
    }

    private function autoreply_email($to, $from_mail, $from_name, $subject, $message) {
        $header = array();
        $header[] = "MIME-Version: 1.0";
        $header[] = "From: {$from_name}<{$from_mail}>";
        /* Set message content type HTML */
        $header[] = "Content-type:text/html; charset=iso-8859-1";
        $header[] = "Content-Transfer-Encoding: 7bit";
        if (mail($to, $subject, $message, implode("\r\n", $header)))
            return true;
    }

}
