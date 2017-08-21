<?php

$school_array = array(2, 246);
$servername = "localhost";
$username = "champs21_champ";
$password = "1_84T~vADp2$";
$dbname_source = "champs21_school";

// Create connection
global $conn_source;
$conn_source = new mysqli($servername, $username, $password, $dbname_source);
// Check connection
if ($conn_source->connect_error)
{
    die("Connection failed: " . $conn_source->connect_error);
}
function send_smtp_mail($to,$name,$subject, $body)
{

    $mail = new PHPMailer;

    $mail->isSMTP();

    $mail->SMTPDebug = 0;

    $mail->Debugoutput = 'html';

    $mail->Host = "mail.classtune.com";

    $mail->Port = 25;

    $mail->SMTPAuth = true;

    $mail->Username = "no-replay@classtune.com";

    $mail->Password = "cHamps2125896321";

    $mail->setFrom('no-replay@classtune.com', 'Classtune');
//Set an alternative reply-to address
    $mail->addReplyTo('no-replay@classtune.com', 'Classtune');
//Set who the message is to be sent to
    $mail->addAddress($to,$name);
//Set the subject line
    $mail->Subject = $subject;
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
    $mail->msgHTML($body);
//Replace the plain text body with one created manually
    $mail->AltBody = $body;

//send the message, check for errors
    if (!$mail->send())
    {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else
    {
        echo "Message sent!";
    }
}

$sql_mail_user = "select first_name,last_name,email,school_id from users where is_principal = 1 and is_deleted=0 and school_id IN ('" . implode(",", $school_array) . "')";

$res_mail = $conn_source->query($sql_mail_user);
if ($res_mail->num_rows > 0)
{
    while ($principal = $res_mail->fetch_object())
    {
        
    }
}

