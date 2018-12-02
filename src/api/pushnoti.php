<?php

//require 'phpmailer/PHPMailerAutoload.php';
//$servername = "localhost";
//$username = "champs21_school";
//$password = "u[QXL=OF%D,F";
//$dbname_source = "champs21_school";
//
//// Create connection
//$conn_source = new mysqli($servername, $username, $password, $dbname_source);
//// Check connection
//if ($conn_source->connect_error)
//{
//    die("Connection failed: " . $conn_source->connect_error);
//}
//
//function send_smtp_mail($to,$name,$subject, $body)
//{
//
//    $mail = new PHPMailer;
//
//    $mail->isSMTP();
//
//    $mail->SMTPDebug = 0;
//
//    $mail->Debugoutput = 'html';
//
//    $mail->Host = "mail.classtune.com";
//
//    $mail->Port = 25;
//
//    $mail->SMTPAuth = true;
//
//    $mail->Username = "no-replay@classtune.com";
//
//    $mail->Password = "cHamps2125896321";
//
//    $mail->setFrom('no-replay@classtune.com', 'Classtune');
////Set an alternative reply-to address
//    $mail->addReplyTo('no-replay@classtune.com', 'Classtune');
////Set who the message is to be sent to
//    $mail->addAddress($to,$name);
////Set the subject line
//    $mail->Subject = $subject;
////Read an HTML message body from an external file, convert referenced images to embedded,
////convert HTML into a basic plain-text alternative body
//    $mail->msgHTML($body);
////Replace the plain text body with one created manually
//    $mail->AltBody = $body;
//
////send the message, check for errors
//    if (!$mail->send())
//    {
//        echo "Mailer Error: " . $mail->ErrorInfo;
//    } else
//    {
//        echo "Message sent!";
//    }
//}
//
//$notification_ids = $argv[1];
//$user_ids = $argv[2];
//
//$user_id_array = explode("*", $user_ids);
//$notification_id_array = explode("*", $notification_ids);
//if (count($user_ids) > 0 && count($user_ids) == count($notification_ids))
//{
//    foreach ($user_id_array as $key => $value)
//    {
//
//        $notification_id = $notification_id_array[$key];
//        $user_id = $value;
//        $user_type = 0;
//        $total_unread = 0;
//        $notification = array();
//
//        $sql = "SELECT * FROM reminders where id = '" . $notification_id . "'";
//        $res2 = $conn_source->query($sql);
//        if ($res2->num_rows > 0)
//        {
//            $notification = $res2->fetch_object();
//        }
//
//        $sql = "SELECT count(id) as total_unread FROM reminders where recipient = '" . $user_id . "' and is_deleted_by_sender=0 and is_deleted_by_recipient = 0 and is_read = 0";
//        $res2 = $conn_source->query($sql);
//        if ($res2->num_rows > 0)
//        {
//            $notification_count = $res2->fetch_object();
//            $total_unread = $notification_count->total_unread;
//        }
//
//        $sql = "SELECT * FROM users where id = '" . $user_id . "'";
//        $res2 = $conn_source->query($sql);
//        if ($res2->num_rows > 0)
//        {
//            $user = $res2->fetch_object();
//            if ($user->admin)
//            {
//                $user_type = 1;
//            }
//            if ($user->student)
//            {
//                $user_type = 3;
//            }
//            if ($user->employee)
//            {
//                $user_type = 2;
//            }
//            if ($user->parent)
//            {
//                $user_type = 4;
//            }
//        }
//
//        $all_gcm_user = array();
//        $all_fcm_user = array();
//        $sql = "SELECT tds_gcm_ids.gcm_id as gcmid,tds_gcm_ids.fcm_converted as fcm_converted FROM tds_user_gcm LEFT JOIN tds_gcm_ids on tds_gcm_ids.id = tds_user_gcm.gcm_id where tds_user_gcm.user_id = '" . $user_id . "'";
//        $res2 = $conn_source->query($sql);
//        if ($res2->num_rows > 0)
//        {
//            while ($gcm_ids = $res2->fetch_object())
//            {
//                if($gcm_ids->fcm_converted == 0)
//                {
//                    $all_gcm_user[] = $gcm_ids->gcmid;
//                }
//                else
//                {
//                    $all_fcm_user[] = $gcm_ids->gcmid;
//                }    
//            }
//        }
//        if(count($notification) > 0 && $user->email_alert == 1)
//        {
//            send_smtp_mail($user->email, $user->first_name." ".$user->last_name, $notification->subject, $notification->body);
//        }    
//        
//        if ($user_type && $total_unread && count($all_fcm_user) > 0 && count($notification) > 0)
//        {
//            // API access key from Google API's Console
//            define('API_ACCESS_KEY', 'AAAA9xu5n9A:APA91bFWbXcRyqgByR1vpvMibChz8tZxvA9g1AcMdGAOOvsqEeIXg8LqkdMbWUQtPEUlCW0TjxADE15fdWIBRWEd1_UGKgq4BXLdNRcZB3hgw0CVD-crjADNU8u4uq1TBIp7FKCWGqbFQlnxccZFDdvmOPddPFcTcw');
//            
//            $messege = $notification->body;
//            $registrationIds = array($_GET['id']);
//            // prep the bundle
//            $msg = array
//                (
//                'message' => $messege,
//                "key" => "paid",
//                'total_unread' => $total_unread,
//                "user_type" => $user_type,
//                "subject" => $notification->subject,
//                "rtype" => $notification->rtype,
//                "rid" => $notification->rid,
//                "batch_id" => $notification->batch_id,
//                "student_id" => $notification->student_id
//            );
//            $fields = array
//                (
//                'registration_ids' => $all_fcm_user,
//                'data' => $msg
//            );
//
//            $headers = array
//                (
//                'Authorization: key=' . API_ACCESS_KEY,
//                'Content-Type: application/json'
//            );
//
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
//            curl_setopt($ch, CURLOPT_POST, true);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
//            $result = curl_exec($ch);
//            curl_close($ch);
//        }
//        
//        if ($user_type && $total_unread && count($all_gcm_user) > 0 && count($notification) > 0)
//        {
//            // API access key from Google API's Console
//            define('API_ACCESS_KEY', 'AIzaSyBrKEjz2fYKuBiNJwtKD09DtmRZKkEeFYk');
//            
//            $messege = $notification->body;
//            $registrationIds = array($_GET['id']);
//            // prep the bundle
//            $msg = array
//                (
//                'message' => $messege,
//                "key" => "paid",
//                'total_unread' => $total_unread,
//                "user_type" => $user_type,
//                "subject" => $notification->subject,
//                "rtype" => $notification->rtype,
//                "rid" => $notification->rid,
//                "batch_id" => $notification->batch_id,
//                "student_id" => $notification->student_id
//            );
//            $fields = array
//                (
//                'registration_ids' => $all_gcm_user,
//                'data' => $msg
//            );
//
//            $headers = array
//                (
//                'Authorization: key=' . API_ACCESS_KEY,
//                'Content-Type: application/json'
//            );
//
//            $ch = curl_init();
//            curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
//            curl_setopt($ch, CURLOPT_POST, true);
//            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
//            $result = curl_exec($ch);
//            curl_close($ch);
//        }
//    }
//    echo $notification_ids;
//}
