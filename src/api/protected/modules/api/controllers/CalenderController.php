<?php

class CalenderController extends Controller
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('getAttendence', 'academic', 'getbatch', 'getbatchstudentattendence', 'approveLeave', 'addattendence',
                    'studentattendencereport', 'getstudentinfo'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionGetAttendence()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $start_date = Yii::app()->request->getPost('start_date');
            $end_date = Yii::app()->request->getPost('end_date');

            $yearly = false;
            if (!$start_date || !$end_date)
            {
                $start_date = date("Y-01-01");
                $end_date = date("Y-12-31");

                $yearly = true;
            }


            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && $start_date != "" && $end_date != "" &&
                    ( Yii::app()->user->isStudent ||
                    (Yii::app()->user->isParent && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') && Yii::app()->request->getPost('school')) ||
                    (Yii::app()->user->isTeacher && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id'))))
            {
                if (Yii::app()->user->isParent)
                {
                    $batch_id = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                    $school_id = Yii::app()->request->getPost('school');
                }
                else if (Yii::app()->user->isTeacher)
                {
                    $batch_id = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                    $school_id = Yii::app()->user->schoolId;
                }
                else
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                    $school_id = Yii::app()->user->schoolId;
                }
                $student = new Students();
                $stddata = $student->findByPk($student_id);

                $objbatch = new Batches();
                $batchData = $objbatch->findByPk($batch_id);
                if ($yearly)
                {
                    $start_date = date("Y-m-d", strtotime($batchData->start_date));
                    $end_date = date("Y-m-d", strtotime($batchData->end_date));
                    if ($end_date > date("Y-m-d"))
                    {
                        $end_date = date("Y-m-d");
                    }
                }


                $attendance = new Attendances();

                $holiday = new Events();
                $holiday_array = $holiday->getHolidayMonth($start_date, $end_date, $school_id);

                if (!$yearly)
                {
                    if ($start_date < date("Y-m-d", strtotime($batchData->start_date)))
                    {
                        $start_date = date("Y-m-d", strtotime($batchData->start_date));
                    }
                    if ($end_date > date("Y-m-d", strtotime($batchData->end_date)))
                    {
                        $end_date = date("Y-m-d", strtotime($batchData->end_date));
                    }
                }

                if ($end_date > date("Y-m-d"))
                {
                    $end_date = date("Y-m-d");
                }

                $leave = new ApplyLeaveStudents();
                $leave_array = $leave->getleaveStudentMonth($start_date, $end_date, $student_id);
                $weekend_array = $attendance->getWeekend(Yii::app()->user->schoolId,$batch_id);




                $holiday_count = 0;
                $holiday_array_for_count = array();
                foreach ($holiday_array as $value)
                {
                    $start_holiday = new DateTime($value['start_date']);
                    $end_holiday = new DateTime($value['end_date']);
                    $holiday_interval = DateInterval::createFromDateString('1 day');
                    $holiday_period = new DatePeriod($start_holiday, $holiday_interval, $end_holiday);

                    foreach ($holiday_period as $hdt)
                    {
                        $holiday_count++;
                        $holiday_array_for_count[] = $hdt->format("Y-m-d");
                    }
                    $holiday_count++;
                    $holiday_array_for_count[] = $end_holiday->format("Y-m-d");
                }

                $leave_count = 0;
                $leave_array_modified = array();
                $leave_array_date = array();
                foreach ($leave_array as $value)
                {
                    $start_holiday = new DateTime($value['start_date']);
                    $end_holiday = new DateTime($value['end_date']);
                    if ($value['end_date'] > date("Y-m-d"))
                    {
                        $end_holiday = new DateTime(date("Y-m-d"));
                        $value['end_date'] = date("Y-m-d");
                    }
                    $holiday_interval = DateInterval::createFromDateString('1 day');
                    $holiday_period = new DatePeriod($start_holiday, $holiday_interval, $end_holiday);



                    foreach ($holiday_period as $hdt)
                    {
                        if (in_array($hdt->format("Y-m-d"), $holiday_array_for_count))
                        {
                            continue;
                        }
                        if (in_array($hdt->format("w"), $weekend_array))
                        {
                            continue;
                        }
                        if (in_array($hdt->format("Y-m-d"), $leave_array_date))
                        {
                            continue;
                        }
                        $merge['title'] = $value['title'];
                        $merge['start_date'] = $hdt->format("Y-m-d");
                        $merge['end_date'] = $hdt->format("Y-m-d");
                        $leave_array_modified[] = $merge;
                        $leave_array_date[] = $hdt->format("Y-m-d");
                        $leave_count++;
                    }
                    
                    if (!in_array($end_holiday->format("Y-m-d"), $holiday_array_for_count) && !in_array($end_holiday->format("Y-m-d"), $leave_array_date) && !in_array($end_holiday->format("w"), $weekend_array))
                    {
                        $merge['title'] = $value['title'];
                        $merge['start_date'] = $end_holiday->format("Y-m-d");
                        $merge['end_date'] = $end_holiday->format("Y-m-d");
                        $leave_array_modified[] = $merge;
                        $leave_array_date[] = $end_holiday->format("Y-m-d");
                        $leave_count++;
                    }
                }





                if ($yearly)
                {
                    $begin = new DateTime(date("Y-m-d", strtotime($batchData->start_date)));
                    $end = new DateTime(date("Y-m-d", strtotime($batchData->end_date)));
                    if (date("Y-m-d", strtotime($batchData->end_date)) > date("Y-m-d"))
                    {
                        $end = new DateTime(date("Y-m-d"));
                    }
                }
                else
                {

                    $begin = new DateTime(date("Y-m-d", strtotime($start_date)));
                    $end = new DateTime(date("Y-m-d", strtotime($end_date)));
                }




                $i = 0;

                if ($yearly || $start_date <= $end_date)
                {
                    $interval = DateInterval::createFromDateString('1 day');
                    $period = new DatePeriod($begin, $interval, $end);


                    foreach ($period as $dt)
                    {

                        if (in_array($dt->format("Y-m-d"), $holiday_array_for_count))
                        {
                            continue;
                        }
                        if (in_array($dt->format("w"), $weekend_array))
                        {
                            continue;
                        }
                        $i++;
                    }
                    if (!in_array($end->format("Y-m-d"), $holiday_array_for_count) && !in_array($end->format("w"), $weekend_array))
                    {
                        $i++;
                    }
                }

                //$attendance_array = $attendance->getAbsentStudentMonth($start_date, $end_date, $student_id, $holiday_array_for_count, $weekend_array, $leave_array_modified);
                $attendance_array = $attendance->getAbsentStudentMonth($start_date, $end_date, $student_id, $holiday_array_for_count, $weekend_array);
                $absent_count = count($attendance_array['absent']);
                $late_count = count($attendance_array['late']);

                $start_date_main = Yii::app()->request->getPost('start_date');
                $end_date_main = Yii::app()->request->getPost('end_date');
                if (!$yearly && $start_date_main <= $end_date_main)
                {
                    $begin = new DateTime(date("Y-m-d", strtotime($start_date_main)));
                    $end = new DateTime(date("Y-m-d", strtotime($end_date_main)));
                    
                    $interval = DateInterval::createFromDateString('1 day');
                    $period = new DatePeriod($begin, $interval, $end);

                    foreach ($period as $dt)
                    {

                        if ($dt->format("Y-m-d") == date("Y-m-d"))
                        {
                            $text = "Today";
                        }
                        else
                        {
                            $text = date('jS F', strtotime($dt->format("Y-m-d")));
                        }


                        if (in_array($dt->format("Y-m-d"), $holiday_array_for_count))
                        {
                            $msg[$dt->format("Y-m-d")] = $text . " is Holiday";
                        }
                        elseif (in_array($dt->format("w"), $weekend_array))
                        {
                            $msg[$dt->format("Y-m-d")] = $text . " is Weekend";
                        }

                        if (!isset($msg[$dt->format("Y-m-d")]))
                        {
                            if ($leave_array_modified)
                            {
                                foreach ($leave_array_modified as $value)
                                {
                                    if ($value['start_date'] == $dt->format("Y-m-d"))
                                    {
                                        $msg[$dt->format("Y-m-d")] = $text . " " . $stddata->first_name . " is on leave";
                                        break;
                                    }
                                }
                            }

                            if (!isset($msg[$dt->format("Y-m-d")]))
                            {
                                if ($dt->format("Y-m-d") > date("Y-m-d"))
                                {
                                    $msg[$dt->format("Y-m-d")] = "No Status Found for " . date('jS F', strtotime($dt->format("Y-m-d")));
                                }
                                else
                                {
                                    foreach ($attendance_array['absent'] as $value)
                                    {
                                        if ($value['date'] == $dt->format("Y-m-d"))
                                        {
                                            $msg[$dt->format("Y-m-d")] = $text . " " . $stddata->first_name . " is Absent";
                                            break;
                                        }
                                    }
                                    if (!isset($msg[$dt->format("Y-m-d")]))
                                    {
                                        foreach ($attendance_array['late'] as $value)
                                        {
                                            if ($value['date'] == $dt->format("Y-m-d"))
                                            {
                                                $msg[$dt->format("Y-m-d")] = $text . " " . $stddata->first_name . " is Late";
                                                break;
                                            }
                                        }
                                    }
                                    if (!isset($msg[$dt->format("Y-m-d")]))
                                    {
                                        $msg[$dt->format("Y-m-d")] = $text . " " . $stddata->first_name . " is Present";
                                    }
                                }
                            }
                        }
                    }


                    if ($end->format("Y-m-d") == date("Y-m-d"))
                    {
                        $text = "Today";
                    }
                    else
                    {
                        $text = date('jS F', strtotime($end->format("Y-m-d")));
                    }


                    if (in_array($end->format("Y-m-d"), $holiday_array_for_count))
                    {
                        $msg[$end->format("Y-m-d")] = $text . " is Holiday";
                    }
                    elseif (in_array($end->format("w"), $weekend_array))
                    {
                        $msg[$end->format("Y-m-d")] = $text . " is Weekend";
                    }

                    if (!isset($msg[$end->format("Y-m-d")]))
                    {
                        if ($leave_array_modified)
                        {
                            foreach ($leave_array_modified as $value)
                            {
                                if ($value['start_date'] == $end->format("Y-m-d"))
                                {
                                    $msg[$end->format("Y-m-d")] = $text . " " . $stddata->first_name . " is on leave";
                                    break;
                                }
                            }
                        }

                        if (!isset($msg[$end->format("Y-m-d")]))
                        {
                            if ($end->format("Y-m-d") > date("Y-m-d"))
                            {
                                $msg[$end->format("Y-m-d")] = "No Status Found for " . date('jS F', strtotime($end->format("Y-m-d")));
                            }
                            else
                            {
                                foreach ($attendance_array['absent'] as $value)
                                {
                                    if ($value['date'] == $end->format("Y-m-d"))
                                    {
                                        $msg[$end->format("Y-m-d")] = $text . " " . $stddata->first_name . " is Absent";
                                        break;
                                    }
                                }
                                if (!isset($msg[$end->format("Y-m-d")]))
                                {
                                    foreach ($attendance_array['late'] as $value)
                                    {
                                        if ($value['date'] == $end->format("Y-m-d"))
                                        {
                                            $msg[$end->format("Y-m-d")] = $text . " " . $stddata->first_name . " is Late";
                                            break;
                                        }
                                    }
                                }
                                if (!isset($msg[$end->format("Y-m-d")]))
                                {
                                    $msg[$end->format("Y-m-d")] = $text . " " . $stddata->first_name . " is Present";
                                }
                            }
                        }
                    }


                   
                }
                
                $current_msg = "";
                $cur_day = "";
                if(isset($msg[date("Y-m-d")]))
                {
                    $current_msg = $msg[date("Y-m-d")];
                }



                if ($yearly)
                {
                    $response['data']['absent'] = $absent_count;
                    $response['data']['late'] = $late_count;
                    $response['data']['holiday'] = $holiday_count;
                    $response['data']['leave'] = $leave_count;
                    $response['data']['current_date'] = date("Y-m-d");
                    $response['data']['total'] = $i;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                }
                else
                {
                    $response['data'] = $attendance_array;
                    $response['data']['week_end'] = $weekend_array;
                    
                    $response['data']['holiday'] = $holiday_array;
                    $response['data']['leave'] = $leave_array_modified;
                    $response['data']['total'] = $i;
                    $response['data']['msg'] = $msg;
                    $response['data']['current_msg'] = $current_msg;
                    $response['data']['current_date'] = date("Y-m-d");
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                }
            }
            else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionAcademic()
    {

        $response = array();
        if ((Yii::app()->request->isPostRequest) && !empty($_POST))
        {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->request->getPost('school');

            $from_date = Yii::app()->request->getPost('from_date');
            $from_date = (!empty($from_date)) ? $from_date : date('Y-01-01');

            $origin = Yii::app()->request->getPost('origin');
            $origin = (!empty($origin)) ? $origin : 0;

            $to_date = Yii::app()->request->getPost('to_date');
            $to_date = (!empty($to_date)) ? $to_date : date('Y-12-31');

            $page_no = Yii::app()->request->getPost('page_number');
            $page_no = (!empty($page_no)) ? $page_no : 1;

            $page_size = Yii::app()->request->getPost('page_size');
            $page_size = (!empty($page_size)) ? $page_size : 10;

            $batch_id = Yii::app()->request->getPost('batch_id');
            $batch_id = (!empty($batch_id)) ? $batch_id : NULL;

            if (Yii::app()->user->isParent && empty($batch_id))
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->user_secret === $user_secret)
            {

                if (Yii::app()->user->isStudent)
                {
                    $batch_id = Yii::app()->user->batchId;
                }

                $events = new Events;
                $events = $events->getAcademicCalendar($school_id, $from_date, $to_date, $batch_id, $origin, $page_no, $page_size, false);

                if (!$events)
                {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "EVENTS_NOT_FOUND";
                }
                else
                {

                    $response['data']['events'] = $events;

                    $events_cnt = new Events;
                    $response['data']['total'] = $events_cnt->getAcademicCalendar($school_id, $from_date, $to_date, $batch_id, $origin, $page_no, $page_size, TRUE);

                    $has_next = false;
                    if ($response['data']['total'] > $page_no * $page_size)
                    {
                        $has_next = true;
                    }

                    $response['data']['has_next'] = $has_next;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EVENTS_FOUND";
                }
            }
            else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied";
            }
        }
        else
        {

            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    private function sendnotificationAttendence($student_id, $newattendence, $late)
    {
        $reminderrecipients = array();
        $studentobj = new Students();
        $studentdata = $studentobj->findByPk($student_id);
        $reminderrecipients[] = $studentdata->user_id;

        if ($late == 1)
            $message = $studentdata->first_name . " " . $studentdata->last_name . " is absent on (forenoon)" . $newattendence->month_date;
        else
            $message = $studentdata->first_name . " " . $studentdata->last_name . " is absent on " . $newattendence->month_date;

        if ($studentdata->immediate_contact_id)
        {
            $gr = new Guardians();
            $grdata = $gr->findByPk($studentdata->immediate_contact_id);
            if($grdata->user_id)
            {
                $reminderrecipients[] = $grdata->user_id;
            }
        }


        if ($reminderrecipients)
        {
            $notification_ids = array();
            foreach ($reminderrecipients as $value)
            {
                $reminder = new Reminders();
                $reminder->sender = Yii::app()->user->id;
                $reminder->recipient = $value;
                $reminder->subject = "Attendance Notice";
                $reminder->body = $message;
                $reminder->created_at = date("Y-m-d H:i:s");
                $reminder->rid = $newattendence->id;
                $reminder->rtype = 6;
                $reminder->updated_at = date("Y-m-d H:i:s");
                $reminder->school_id = Yii::app()->user->schoolId;
                $reminder->save();
                $notification_ids[] = $reminder->id;
            }
            $notification_id = implode(",", $notification_ids);
            $user_id = implode(",", $reminderrecipients);
            Settings::sendCurlNotification($user_id, $notification_id);
        }
    }

    private function sendnotificationleave($student_id, $status, $updateleave)
    {
        $reminderrecipients = array();
        $studentobj = new Students();
        $studentdata = $studentobj->findByPk($student_id);
        $reminderrecipients = array();

        if ($studentdata->immediate_contact_id)
        {
            $gr = new Guardians();
            $grdata = $gr->findByPk($studentdata->immediate_contact_id);
            if($grdata->user_id)
            {
                $reminderrecipients[] = $grdata->user_id;
            }
        }
        $approved_text = "Denied";
        if ($status == 1)
        {
            $approved_text = "Approved";
        }
        if ($reminderrecipients)
        {
            $notification_ids = array();

            $reminder = new Reminders();
            //delete reminder previous
            $reminderdata = $reminder->getReminder($updateleave->id, 10);

            if ($reminderdata)
            {
                foreach ($reminderdata as $rvalue)
                {
                    $rfordelete = $reminder->findByPk($rvalue->id);
                    $rfordelete->delete();
                }
            }
            //delete reminder previous

            foreach ($reminderrecipients as $value)
            {

                $reminder = new Reminders();
                $reminder->sender = Yii::app()->user->id;
                $reminder->recipient = $value;
                $reminder->subject = "Your Leave Aplication is " . $approved_text . " (" . $studentdata->first_name . ")";
                $reminder->body = "(" . $studentdata->first_name . ") leave application from " . $updateleave->start_date . " to " . $updateleave->end_date . " is " . $approved_text . "";
                $reminder->created_at = date("Y-m-d H:i:s");
                $reminder->rid = $updateleave->id;
                $reminder->rtype = 10;
                $reminder->updated_at = date("Y-m-d H:i:s");
                $reminder->school_id = Yii::app()->user->schoolId;
                $reminder->save();
                $notification_ids[] = $reminder->id;
            }
            $notification_id = implode(",", $notification_ids);
            $user_id = implode(",", $reminderrecipients);
            Settings::sendCurlNotification($user_id, $notification_id);
        }
    }

    public function actionapproveLeave()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $leave_id = Yii::app()->request->getPost('leave_id');
        $student_id = Yii::app()->request->getPost('student_id');
        $status = Yii::app()->request->getPost('status');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $leave_id && $student_id)
        {
            $leaveStudent = new ApplyLeaveStudents();
            $updateleave = $leaveStudent->findByPk($leave_id);
            if (isset($updateleave->student_id) && $updateleave->student_id == $student_id)
            {
                if (!$status)
                {
                    $status = 0;
                }
                $updateleave->viewed_by_teacher = 1;
                $updateleave->updated_at = date("Y-m-d H:i:s");
                $updateleave->approved = $status;
                $updateleave->approving_teacher = Yii::app()->user->id;
                
                $updateleave->save(false);

                //sending notification
                $reminderrecipients = array();
                $studentobj = new Students();
                $studentdata = $studentobj->findByPk($student_id);
                $reminderrecipients[] = $studentdata->user_id;
                

                $this->sendnotificationleave($student_id, $status, $updateleave);

                $check_date = $updateleave->start_date;
                $end_date = $updateleave->end_date;

                while ($check_date <= $end_date)
                {
                    $attendence = new Attendances();
                    $attendence_student = $attendence->getAttendenceStudent($student_id, $check_date);

                    if ($attendence_student)
                    {
                        $objatt = $attendence->findByPk($attendence_student->id);
                        
                        if(isset($updateleave->leave_subject) && $updateleave->leave_subject)
                        {
                            $objatt->reason = $updateleave->leave_subject;
                        } 
                        else
                        {
                            $objatt->reason = $updateleave->reason;
                        }
                        if ($status == 1)
                        {
                            $objatt->is_leave = 1;
                            $objatt->forenoon = 1;
                            $objatt->afternoon = 1;
                        }
                        else
                        {
                            $objatt->is_leave = 0;
                            $objatt->forenoon = 1;
                            $objatt->afternoon = 1;
                        }
                        $objatt->save();
                    }
                    else if ($status == 1)
                    {
                        $studentobj = new Students();
                        $studentdata = $studentobj->findByPk($student_id);
                        $attendence->afternoon = 1;
                        $attendence->forenoon = 1;
                        $attendence->month_date = $check_date;
                        $attendence->student_id = $student_id;
                        $attendence->batch_id = $studentdata->batch_id;
                        $attendence->created_at = date("Y-m-d H:i:s");
                        $attendence->updated_at = date("Y-m-d H:i:s");
                        $attendence->school_id = Yii::app()->user->schoolId;
                        if(isset($updateleave->leave_subject) && $updateleave->leave_subject)
                        {
                            $attendence->reason = $updateleave->leave_subject;
                        } 
                        else
                        {
                            $attendence->reason = $updateleave->reason;
                        }
                        
                        $attendence->is_leave = 1;
                        $attendence->save();
                    }
                    $check_date = date("Y-m-d", strtotime("+1 day", strtotime($check_date)));
                }


                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success";
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            }
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionAddAttendence()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $batch_id = Yii::app()->request->getPost('batch_id');
        $student_id = Yii::app()->request->getPost('student_id');
        $late = Yii::app()->request->getPost('late');
        $date = Yii::app()->request->getPost('date');
        //$reason = Yii::app()->request->getPost('reason');

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $batch_id && $student_id)
        {
            $student_ids = explode(",", $student_id);
            $lates = explode(",", $late);
            if (!$date)
            {
                $date = date("Y-m-d");
            }
            $attendence = new Attendances();
            $attendence_batch = $attendence->getAttendenceBatch($batch_id, $date);
            if ($attendence_batch)
                foreach ($attendence_batch as $value)
                {
                    $previous_attendence = $attendence->findbypk($value->id);
                    if ($previous_attendence)
                    {
                        $reminder = new Reminders();

                        $reminderdata = $reminder->getReminder($value->id);

                        if ($reminderdata)
                        {
                            foreach ($reminderdata as $rvalue)
                            {
                                $rfordelete = $reminder->findByPk($rvalue->id);
                                $rfordelete->delete();
                            }
                        }

                        $previous_attendence->delete();
                    }
                }

            foreach ($student_ids as $key => $student_id)
            {



                $late = (isset($lates[$key])) ? $lates[$key] : 0;

                $newattendence = new Attendances();

                $leaveStudent = new ApplyLeaveStudents();
                $leave_today = $leaveStudent->getallleaveStudentsDate($date);

                if (isset($leave_today['approved']) && in_array($student_id, $leave_today['approved']))
                {
                    $newattendence->is_leave = 1;
                }

                $newattendence->batch_id = $batch_id;
                $newattendence->student_id = $student_id;
                // $newattendence->reason = $reason;
                $newattendence->month_date = $date;
                $newattendence->created_at = date("Y-m-d H:i:s");
                $newattendence->updated_at = date("Y-m-d H:i:s");
                $newattendence->school_id = Yii::app()->user->schoolId;
                if ($late && $late == 1)
                {
                    $newattendence->forenoon = 1;
                    $newattendence->afternoon = 0;
                }
                else
                {
                    $newattendence->forenoon = 1;
                    $newattendence->afternoon = 1;
                }
                $newattendence->save();

                if(!isset($newattendence->is_leave) || $newattendence->is_leave!=1)
                {
                    $this->sendnotificationAttendence($student_id, $newattendence, $late);
                }
            }

            $response['status']['code'] = 200;
            $response['status']['msg'] = "Success";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionStudentAttendenceReport()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $batch_id = Yii::app()->request->getPost('batch_id');
        $date = Yii::app()->request->getPost('date');

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $batch_id)
        {
            if (!$date)
            {
                $date = date("Y-m-d");
            }
            $attendence = new Attendances();
            $bacthes = $attendence->getBatchStudentTodayAttendence($batch_id, $date);
            $total = count($bacthes);
            $student = array("present" => array(), "absent" => array(), "late" => array(), "leave" => array());
            $present = 0;
            $late = 0;
            $absent = 0;
            $leave = 0;

            foreach ($bacthes as $value)
            {
                if ($value['main_status'] == 1)
                {
                    $student['present'][$present]['student_name'] = $value['student_name'];
                    $student['present'][$present]['roll_no'] = $value['roll_no'];
                    $present++;
                }
                else if ($value['main_status'] == 0)
                {
                    $student['absent'][$absent]['student_name'] = $value['student_name'];
                    $student['absent'][$absent]['roll_no'] = $value['roll_no'];
                    $absent++;
                }
                else if ($value['main_status'] == 2)
                {
                    $student['late'][$late]['student_name'] = $value['student_name'];
                    $student['late'][$late]['roll_no'] = $value['roll_no'];
                    $late++;
                }
                else if ($value['main_status'] == 3)
                {
                    $student['leave'][$leave]['student_name'] = $value['student_name'];
                    $student['leave'][$leave]['roll_no'] = $value['roll_no'];
                    $leave++;
                }
            }
            $current_date = date("Y-m-d");

            $response['data']['total'] = $total;
            $response['data']['current_date'] = $current_date;
            $response['data']['student'] = $student;
            $response['data']['present'] = $present;
            $response['data']['late'] = $late;
            $response['data']['absent'] = $absent;
            $response['data']['leave'] = $leave;

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetBatchStudentAttendence()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $batch_id = Yii::app()->request->getPost('batch_id');
        $date = Yii::app()->request->getPost('date');

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $batch_id)
        {
            if (!$date)
            {
                $date = date("Y-m-d");
            }
            $attendence = new Attendances();
            $bacthes = $attendence->getBatchStudentTodayAttendence($batch_id, $date);
            $response['data']['batch_attendence'] = $bacthes;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetStudentInfo()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $student_id = Yii::app()->request->getPost('student_id');

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $student_id)
        {
            $studentsobj = new Students();
            $students = $studentsobj->getStudentById($student_id);

            $response['data']['student'] = $this->formatStudent($students);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "EVENTS_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    private function formatStudent($std)
    {
        $fullname = ($std->first_name) ? $std->first_name . " " : "";
        $fullname.= ($std->middle_name) ? $std->middle_name . " " : "";
        $fullname.= ($std->last_name) ? $std->last_name : "";
        $student['name'] = $fullname;
        $student['roll'] = $std->class_roll_no;
        $student['admission_no'] = $std->admission_no;
        $student['date_of_birth'] = $std->date_of_birth;
        $student['gender'] = $std->gender;
        $student['admission_no'] = $std->admission_no;
        $student['class'] = $std['batchDetails']['courseDetails']->course_name;
        $student['batch'] = $std['batchDetails']->name;
        $student['contact'] = "";
        
        if($std->address_line1)
        {
           $student['contact'] .=  $std->address_line1;
        }
        if($std->address_line2)
        {
            if($student['contact'])
            {
               $student['contact'] .=", "; 
            }
            $student['contact'] .=  $std->address_line2;
        }
        
        $student['phone'] = "";
        if($std->phone1)
        {
           $student['phone'] =  $std->phone1;
        }
        else if($std->phone2)
        {
           $student['phone'] =  $std->phone2; 
        }    
        
        
        
       
        $fullname = "";
        if (isset($std['guradianDetails']->first_name))
        {
            $fullname = ($std['guradianDetails']->first_name) ? $std['guradianDetails']->first_name . " " : "";
            $fullname.= ($std['guradianDetails']->last_name) ? $std['guradianDetails']->last_name : "";
        }
        $free = new Freeusers();


        $user_image = $free->getUserImage($std->user_id);
        $student['user_image'] = "";

        if (isset($user_image['profile_image']))
        {
            $student['user_image'] = $user_image['profile_image'];
        }

        $student['guradian'] = $fullname;
        return $student;
    }

    public function actionGetBatch()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
        {
            $emplyee_subject = new EmployeesSubjects();
            $bacthes = $emplyee_subject->getBatch(Yii::app()->user->profileId);
            $response['data']['batches'] = $bacthes;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "EVENTS_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

//    public function actionGetBatch() 
//    {
//       
//        $user_secret = Yii::app()->request->getPost('user_secret');
//        if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
//        {
//            $url_end = "api/batches";
//            $data = array("search[]"=>"");
//            $batches = Settings::getDataApi($data,$url_end);
//            $response['data'] = $batches;
//            $response['status']['code'] = 200;
//            $response['status']['msg'] = "EVENTS_FOUND";
//            
//        }
//        else
//        {
//            $response['status']['code'] = 400;
//            $response['status']['msg'] = "Bad Request";
//        }
//        echo CJSON::encode($response);
//        Yii::app()->end();
//        
//        
//    }
//    public function actionGetBatchStudent() 
//    {
//       
//        $user_secret = Yii::app()->request->getPost('user_secret');
//        
//        if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
//        {
//            $url_end = "api/batches";
//            $data = array("search[]"=>"");
//            $batches = Settings::getDataApi($data,$url_end);
//            $response['data'] = $batches;
//            $response['status']['code'] = 200;
//            $response['status']['msg'] = "EVENTS_FOUND";
//            
//        }
//        else
//        {
//            $response['status']['code'] = 400;
//            $response['status']['msg'] = "Bad Request";
//        }
//        echo CJSON::encode($response);
//        Yii::app()->end();
//        
//        
//    }
}
