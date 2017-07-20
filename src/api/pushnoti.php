<?php

$servername = "localhost";
$username = "champs21";
$password = "079366";
$dbname_source = "champs21_school";

// Create connection
$conn_source = new mysqli($servername, $username, $password, $dbname_source);
// Check connection
if ($conn_source->connect_error)
{
    die("Connection failed: " . $conn_source->connect_error);
}
$notification_ids = $argv[1];
$user_ids = $argv[2];

$user_id_array = explode("*", $user_ids);
$notification_id_array = explode("*", $notification_ids);
if (count($user_ids) > 0 && count($user_ids) == count($notification_ids))
{
    foreach ($user_id_array as $key => $value)
    {

        $notification_id = $notification_id_array[$key];
        $user_id = $value;
        $user_type = 0;
        $total_unread = 0;
        $notification = array();

        $sql = "SELECT * FROM reminders where id = '" . $notification_id . "'";
        $res2 = $conn_source->query($sql);
        if ($res2->num_rows > 0)
        {
            $notification = $res2->fetch_object();
        }

        $sql = "SELECT count(id) as total_unread FROM reminders where recipient = '" . $user_id . "' and is_deleted_by_sender=0 and is_deleted_by_recipient = 0 and is_read = 0";
        $res2 = $conn_source->query($sql);
        if ($res2->num_rows > 0)
        {
            $notification_count = $res2->fetch_object();
            $total_unread = $notification_count->total_unread;
        }

        $sql = "SELECT * FROM users where id = '" . $user_id . "'";
        $res2 = $conn_source->query($sql);
        if ($res2->num_rows > 0)
        {
            $user = $res2->fetch_object();
            if ($user->admin)
            {
                $user_type = 1;
            }
            if ($user->student)
            {
                $user_type = 3;
            }
            if ($user->employee)
            {
                $user_type = 2;
            }
            if ($user->parent)
            {
                $user_type = 4;
            }
        }

        $all_gcm_user = array();
        $sql = "SELECT tds_gcm_ids.gcm_id as gcmid FROM tds_user_gcm LEFT JOIN tds_gcm_ids on tds_gcm_ids.id = tds_user_gcm.gcm_id where tds_user_gcm.user_id = '" . $user_id . "'";
        $res2 = $conn_source->query($sql);
        if ($res2->num_rows > 0)
        {
            while ($gcm_ids = $res2->fetch_object())
            {
                $all_gcm_user[] = $gcm_ids->gcmid;
            }
        }
        if ($user_type && $total_unread && count($all_gcm_user) > 0 && count($notification) > 0)
        {
            // API access key from Google API's Console
            define('API_ACCESS_KEY', 'AIzaSyBrKEjz2fYKuBiNJwtKD09DtmRZKkEeFYk');
            $messege = $notification->body;
            $registrationIds = array($_GET['id']);
            // prep the bundle
            $msg = array
                (
                'message' => $messege,
                "key" => "paid",
                'total_unread' => $total_unread,
                "user_type" => $user_type,
                "subject" => $notification->subject,
                "rtype" => $notification->rtype,
                "rid" => $notification->rid,
                "batch_id" => $notification->batch_id,
                "student_id" => $notification->student_id
            );
            $fields = array
                (
                'registration_ids' => $all_gcm_user,
                'data' => $msg
            );

            $headers = array
                (
                'Authorization: key=' . API_ACCESS_KEY,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
            
        }
    }
    echo $notification_ids;
}
