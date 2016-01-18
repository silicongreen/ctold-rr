<?php
/**
 *
 * This is the template that use for login ajax
 *
 * @package WordPress
 * @subpackage classtune
 * @since classtune 1.0
 */
/*
Template Name: mail_ajax
*/       
if (isset($_POST['name']) AND isset($_POST['email']) AND isset($_POST['subject']) AND isset($_POST['massage'])) {
    $to = 'info@champs21.com';
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $message_content = filter_var($_POST['massage'], FILTER_SANITIZE_STRING);

    $subject = $subject. "(Classtune contact)";

    $message = "Name: " . $name . "<br/>";
    $message .= "E-mail: " . $email . "<br/>";
    $message .= "Comment: " . $message_content . "<br/><br /><br />";

    $sent = send_email($to, $email, $name, $subject, $message);
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

        $sent2 = autoreply_email($email, $to, $auto_name, $auto_subject, $auto_message);
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
        
    


function send_email($to, $from_mail, $from_name, $subject, $message) {
     $header = array();
     $header[] = "MIME-Version: 1.0";
     $header[] = "From: {$from_name}<{$from_mail}>";
     /* Set message content type HTML */
     $header[] = "Content-type:text/html; charset=iso-8859-1";
     $header[] = "Content-Transfer-Encoding: 7bit";
     if (mail($to, $subject, $message, implode("\r\n", $header)))
         return true;
 }

 function autoreply_email($to, $from_mail, $from_name, $subject, $message) {
     $header = array();
     $header[] = "MIME-Version: 1.0";
     $header[] = "From: {$from_name}<{$from_mail}>";
     /* Set message content type HTML */
     $header[] = "Content-type:text/html; charset=iso-8859-1";
     $header[] = "Content-Transfer-Encoding: 7bit";
     if (mail($to, $subject, $message, implode("\r\n", $header)))
         return true;
 }

  



