<?php

$to      = 'nurul.islam@teamworkbd.com';
$subject = 'the lola and the popo';
$message = 'lola popo vai vai ';
$headers = 'From: webdev.nislam@gmail.com' . "\r\n" .
           'Cc: huffas.abdhullah@teamworkbd.com' . "\r\n" .
           'Bcc: fahim.mohammad@teamworkbd.com' . "\r\n" .
           'Reply-To: webdev.nislam@gmail.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);
?>
