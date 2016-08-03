<?php

//$to      = 'nurul.islam@teamworkbd.com';
//$subject = 'the lola and the popo';
//$message = 'lola popo vai vai ';
//$headers = 'From: webdev.nislam@gmail.com' . "\r\n" .
//           'Cc: huffas.abdhullah@teamworkbd.com' . "\r\n" .
//           'Bcc: fahim.mohammad@teamworkbd.com' . "\r\n" .
//           'Reply-To: webdev.nislam@gmail.com' . "\r\n" .
//           'X-Mailer: PHP/' . phpversion();
//
//mail($to, $subject, $message, $headers);


    $ar_email['sender_full_name'] = 'Nurul Islam';
    $ar_email['sender_email'] = 'webdev.nislam@gmail.com';
    $ar_email['to'] = 'nurul.islam@teamworkbd.com';
    $ar_email['cc_name'] = 'Huffas Abdhullah';
    $ar_email['cc_email'] = 'huffas.abdhullah@teamworkbd.com';
    $ar_email['bcc_name'] = 'Fahim Mohammad';
    $ar_email['bcc_email'] = 'fahim.mohammad@teamworkbd.com';

    $ar_email['subject'] = 'the lola and the popo';
    $ar_email['message'] = 'lola popo vai vai, amra tader bichar chai.';

    $headers = array();
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-type: text/plain; charset=utf-8";
    $headers[] = "From: " . $ar_email['sender_full_name'] . " <" . $ar_email['sender_email'] . ">";

    if (isset($ar_email['cc_email']) && !empty($ar_email['cc_email'])) {
        $cc_name = ( isset($ar_email['cc_name']) && !empty($ar_email['cc_name']) ) ? $ar_email['cc_name'] : $ar_email['cc_email'];
        $headers[] = "Cc: " . $cc_name . " <" . $ar_email['cc_email'] . ">";
    }

    if (isset($ar_email['bcc_email']) && !empty($ar_email['bcc_email'])) {
        $bcc_name = ( isset($ar_email['bcc_name']) && !empty($ar_email['bcc_name']) ) ? $ar_email['bcc_name'] : $ar_email['bcc_email'];
        $headers[] = "Bcc: " . $bcc_name . " <" . $ar_email['bcc_email'] . ">";
    }

    $headers[] = "Reply-To: " . $ar_email['sender_full_name'] . " <" . $ar_email['sender_email'] . ">";
    $headers[] = "Subject: {$ar_email['subject']}";
    $headers[] = "X-Mailer: PHP/" . phpversion();

    //        echo '<pre>';
    //        var_dump($headers);
    //        echo implode("\r\n", $headers);
    //        exit;

    return mail($ar_email['to'], $ar_email['subject'], $ar_email['message'], implode("\r\n", $headers));
?>
