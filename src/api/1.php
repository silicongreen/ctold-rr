<?php
 //,{CL~Sk~&m(u

echo phpinfo();
require 'phpmailer/PHPMailerAutoload.php';



//Create a new PHPMailer instance
$mail = new PHPMailer;
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 2;
//Ask for HTML-friendly debug output
$mail->Debugoutput = 'html';
//Set the hostname of the mail server
$mail->Host = "mail.champs21.com";
//Set the SMTP port number - likely to be 25, 465 or 587
$mail->Port = 25;
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication
$mail->Username = "noreplay@champs21.com";
//Password to use for SMTP authentication
$mail->Password = "Tc]#[9f}ae@!";
//Set who the message is to be sent from
$mail->setFrom('noreplay@champs21.com', 'Classtune');
//Set an alternative reply-to address
$mail->addReplyTo('noreplay@champs21.com', 'Classtune');
//Set who the message is to be sent to
$mail->addAddress('fahim.cse@gmail.com', 'Fahim');
//Set the subject line
$mail->Subject = 'PHPMailer SMTP test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML("Test mail");
//Replace the plain text body with one created manually
$mail->AltBody = 'Test mail';

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}

  
  
 ?>










