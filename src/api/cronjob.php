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

function send_notification($id = 0)
{
    global $conn_source;
    define('API_ACCESS_KEY', 'AIzaSyBrKEjz2fYKuBiNJwtKD09DtmRZKkEeFYk');
    if ($id)
    {
        $sql_notification = "select * from reminders where rid = " . $id . " and rtype=1";
    } else
    {
        $sql_notification = "select * from reminders where rtype=159";
    }
    $res2 = $conn_source->query($sql_notification);
    $notification_array = array();
    $user_array = array();

//    if ($res2->num_rows > 0)
//    {
        while ($notification = $res2->fetch_object())
        {

            if ($id == 0)
            {
                $update_birthday_notification = "Update reminders set rtype = 160 where id = $notification->id";
                $conn_source->query($update_birthday_notification);
            }

            $user_id = $notification->recipient;
            $total_unread = 0;
            $user_type = 0;
            $sql = "SELECT count(id) as total_unread FROM reminders where recipient = '" . $user_id . "' and is_deleted_by_sender=0 and is_deleted_by_recipient = 0 and is_read = 0";
            $res3 = $conn_source->query($sql);
            if ($res3->num_rows > 0)
            {
                $notification_count = $res3->fetch_object();
                $total_unread = $notification_count->total_unread;
            }




            $sql = "SELECT * FROM users where id = '" . $user_id . "'";
            $res4 = $conn_source->query($sql);
            if ($res4->num_rows > 0)
            {
                $user = $res4->fetch_object();
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
            $res5 = $conn_source->query($sql);
            if ($res5->num_rows > 0)
            {
                while ($gcm_ids = $res5->fetch_object())
                {
                    $all_gcm_user[] = $gcm_ids->gcmid;
                }
            }


            if ($user_type && count($all_gcm_user) > 0)
            {

                $messege = $notification->body;


                $msg = array
                    (
                    'message' => str_replace("New event description : ", "", trim($messege)),
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
    //}
}

//Birthday Notification

$sql_find_birthday = "select * from students where date_of_birth is not null and DATE_FORMAT(date_of_birth,'%m-%d') = '" . date('m-d') . "' and is_deleted=0 and school_id IN ('" . implode(",", $school_array) . "')";

$res_birthday = $conn_source->query($sql_find_birthday);
if ($res_birthday->num_rows > 0)
{
    while ($student = $res_birthday->fetch_object())
    {
        $admin_sql = "select id from users where username not like 'champs%' and username like '%admin' and school_id = $student->school_id and is_deleted = 0";
        $admin_res = $conn_source->query($admin_sql);

        $admin_sql2 = "select id from users where username not like 'champs%' and username not like '%admin' and school_id = $student->school_id  and is_deleted = 0 ";
        $admin_res2 = $conn_source->query($admin_sql2);


        if ($admin_res->num_rows > 0)
        {
            $admin_info = $admin_res->fetch_object();

            $student_batch = "select * from batches where id = " . $student->batch_id . " and is_deleted=0";
            $res_batch = $conn_source->query($student_batch);
            if ($res_batch->num_rows > 0)
            {
                $batch_info = $res_batch->fetch_object();
                $student_course = "select * from courses where id = " . $batch_info->course_id . " and is_deleted=0";
                $res_course = $conn_source->query($student_course);
                if ($res_course->num_rows > 0)
                {
                    $course_info = $res_course->fetch_object();

                    $birthday_msg_teacher = "It's " . $student->first_name . " " . $student->last_name . "'s (" . $batch_info->name . " " . $course_info->course_name . " " . $course_info->section_name . ") birthday today. Help him to celebrate";
                    $birthday_msg_student = "Wishing you a very happy birthday and many more to come. Hope it’s a good one.";
                    $birtday_msg_parent = "It's " . $student->first_name . " " . $student->last_name . "'s birthday today. Help him to celebrate";

                    $teacher_sql = "SELECT * FROM `batch_tutors` where batch_id = " . $student->batch_id;
                    $res_teacher = $conn_source->query($teacher_sql);

                    if ($res_teacher->num_rows > 0)
                    {
                        while ($batch_teacher = $res_teacher->fetch_object())
                        {
                            $teacher_info_sql = "SELECT * FROM `employees` where id = " . $batch_teacher->employee_id;
                            $res_teacher_info = $conn_source->query($teacher_info_sql);
                            if ($res_teacher_info->num_rows > 0)
                            {
                                $teacher_info = $res_teacher_info->fetch_object();
                                $reminder_insert = "insert into reminders (sender,recipient,subject,body,created_at,updated_at,school_id,rtype) values ($admin_info->id,$teacher_info->user_id,'" . $birthday_msg_teacher . "','" . $birthday_msg_teacher . "','" . date('Y-m-d H:i:s') . "','" . date('Y-m-d H:i:s') . "',$student->school_id,159)";
                                $conn_source->query($reminder_insert);
                            }
                        }
                    }

                    if ($admin_res2->num_rows > 0)
                    {
                        while ($admins = $admin_res2->fetch_object())
                        {
                            $reminder_insert = "insert into reminders (sender,recipient,subject,body,created_at,updated_at,school_id,rtype) values ($admin_info->id,$admins->id,'" . $birthday_msg_teacher . "','" . $birthday_msg_teacher . "','" . date('Y-m-d H:i:s') . "','" . date('Y-m-d H:i:s') . "',$student->school_id,159)";
                            $conn_source->query($reminder_insert);
                        }
                    }

                    $g_sql = "SELECT * FROM `guardian_students` where student_id = " . $student->id;
                    $res_guardian = $conn_source->query($g_sql);

                    if ($res_guardian->num_rows > 0)
                    {
                        while ($guardians = $res_guardian->fetch_object())
                        {
                            $guardian_info_sql = "SELECT * FROM `guardians` where id = " . $guardians->guardian_id;
                            $res_guardian_info = $conn_source->query($guardian_info_sql);
                            if ($res_guardian_info->num_rows > 0)
                            {
                                $guardian_info = $res_guardian_info->fetch_object();
                                $reminder_insert = "insert into reminders (sender,recipient,subject,body,created_at,updated_at,school_id,rtype) values ($admin_info->id,$guardian_info->user_id,'" . $birtday_msg_parent . "','" . $birtday_msg_parent . "','" . date('Y-m-d H:i:s') . "','" . date('Y-m-d H:i:s') . "',$student->school_id,159)";
                                $conn_source->query($reminder_insert);
                            }
                        }
                    }

                    $reminder_insert = "insert into reminders (sender,recipient,subject,body,created_at,updated_at,school_id,rtype) values ($admin_info->id,$student->user_id,'" . $birthday_msg_student . "','" . $birthday_msg_student . "','" . date('Y-m-d H:i:s') . "','" . date('Y-m-d H:i:s') . "',$student->school_id,159)";
                    $conn_source->query($reminder_insert);
                }
            }
        }
    }
}

send_notification();







$sql_find_event_today = "select id from events where DATE(start_date) = '" . date('Y-m-d') . "'";
$res = $conn_source->query($sql_find_event_today);
if ($res->num_rows > 0)
{

    while ($event = $res->fetch_object())
    {
        send_notification($event->id);
    }
}

