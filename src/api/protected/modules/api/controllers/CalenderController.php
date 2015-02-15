<?php

class CalenderController extends Controller {

    /**
     * @return array action filters
     */
    public function filters() {
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
    public function accessRules() {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('getAttendence', 'academic','getbatch','getbatchstudentattendence','approveLeave','addattendence',
                    'studentattendencereport','getstudentinfo'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionGetAttendence() {
        if (isset($_POST) && !empty($_POST)) {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $start_date = Yii::app()->request->getPost('start_date');
            $end_date = Yii::app()->request->getPost('end_date');
            
            $yearly = false;
            if (!$start_date || !$end_date) {
                $start_date = date("Y-01-01");
                $end_date = date("Y-12-31");
                   
                $yearly = true;
            }
            
            
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && $start_date != "" && $end_date != "" &&
                    ( Yii::app()->user->isStudent ||
                    (Yii::app()->user->isParent  && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') && Yii::app()->request->getPost('school')) ||
                    (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id')))) 
                {
                if (Yii::app()->user->isParent) {
                    $batch_id = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                    $school_id = Yii::app()->request->getPost('school');
                } else if(Yii::app()->user->isTeacher) {
                    $batch_id = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                    $school_id = Yii::app()->user->schoolId;
                }
                else {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                    $school_id = Yii::app()->user->schoolId;
                }
                $objbatch = new Batches();
                $batchData = $objbatch->findByPk($batch_id);
                if ($yearly) 
                {
                    $start_date = date("Y-m-d",strtotime($batchData->start_date));
                    $end_date = date("Y-m-d",strtotime($batchData->end_date));
                    if($end_date>date("Y-m-d"))
                    {
                        $end_date = date("Y-m-d");
                    }
                }

                $attendance = new Attendances();
                $attendance_array = $attendance->getAbsentStudentMonth($start_date, $end_date, $student_id);
                $holiday = new Events();
                $holiday_array = $holiday->getHolidayMonth($start_date, $end_date, $school_id);
                $leave = new ApplyLeaveStudents();

                $leave_array = $leave->getleaveStudentMonth($start_date, $end_date, $student_id);
                $weekend_array = $attendance->getWeekend(Yii::app()->user->schoolId);

                if ($yearly) {

                    $late_count = count($attendance_array['absent']);
                    $absent_count = count($attendance_array['late']);

                    $holiday_count = 0;
                    $holiday_array_for_count = array();
                    foreach ($holiday_array as $value) {
                        $start_holiday = new DateTime($value['start_date']);
                        $end_holiday = new DateTime($value['end_date']);
                        $holiday_interval = DateInterval::createFromDateString('1 day');
                        $holiday_period = new DatePeriod($start_holiday, $holiday_interval, $end_holiday);
                        $holiday_count++;
                        $holiday_array_for_count[] = $value['start_date'];
                        foreach ($holiday_period as $hdt) {
                            $holiday_count++;
                            $holiday_array_for_count[] = $hdt->format("Y-m-d");
                        }
                    }
                    $leave_count = 0;
                    foreach ($leave_array as $value) {
                        $start_holiday = new DateTime($value['start_date']);
                        $end_holiday = new DateTime($value['end_date']);
                        if($value['end_date']>date("Y-m-d"))
                        {
                            $end_holiday = new DateTime(date("Y-m-d"));
                        }
                        $holiday_interval = DateInterval::createFromDateString('1 day');
                        $holiday_period = new DatePeriod($start_holiday, $holiday_interval, $end_holiday);
                        foreach ($holiday_period as $hdt) {
                            if (in_array($hdt->format("Y-m-d"), $holiday_array_for_count)) 
                            {
                                continue;
                            }
                            if (in_array($hdt->format("w"), $weekend_array)) {
                                continue;
                            }
                            $leave_count++;
                        }
                    }

                    
                            
                    $begin = new DateTime(date("Y-m-d",strtotime($batchData->start_date)));
                    $end = new DateTime(date("Y-m-d",strtotime($batchData->end_date)));
                    
                    if(date("Y-m-d",strtotime($batchData->end_date))>date("Y-m-d"))
                    {
                        $end = new DateTime(date("Y-m-d"));
                    }    

                    $interval = DateInterval::createFromDateString('1 day');
                    $period = new DatePeriod($begin, $interval, $end);
                    $i = 0;

                    
                    foreach ($period as $dt) {

                        if (in_array($dt->format("Y-m-d"), $holiday_array_for_count)) {
                            continue;
                        }
                        if (in_array($dt->format("w"), $weekend_array)) {
                            continue;
                        }
                        $i++;
                    }
                }

                if ($yearly) {
                    $response['data']['absent'] = $absent_count;
                    $response['data']['late'] = $late_count;
                    $response['data']['holiday'] = $holiday_count;
                    $response['data']['leave'] = $leave_count;
                    $response['data']['current_date'] = date("Y-m-d");
                    $response['data']['total'] = $i;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                } else {
                    $response['data'] = $attendance_array;
                    $response['data']['holiday'] = $holiday_array;
                    $response['data']['leave'] = $leave_array;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                }
            } else {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionAcademic() {

        $response = array();
        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

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

            if (Yii::app()->user->isParent && empty($batch_id)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->user_secret === $user_secret) {

                if (Yii::app()->user->isStudent) {
                    $batch_id = Yii::app()->user->batchId;
                }

                $events = new Events;
                $events = $events->getAcademicCalendar($school_id, $from_date, $to_date, $batch_id, $origin, $page_no, $page_size, false);

                if (!$events) {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "EVENTS_NOT_FOUND";
                } else {
                    
                    $response['data']['events'] = $events;

                    $events_cnt = new Events;
                    $response['data']['total'] = $events_cnt->getAcademicCalendar($school_id, $from_date, $to_date, $batch_id, $origin, $page_no, $page_size, TRUE);

                    $has_next = false;
                    if ($response['data']['total'] > $page_no * $page_size) {
                        $has_next = true;
                    }

                    $response['data']['has_next'] = $has_next;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EVENTS_FOUND";
                }
            } else {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied";
            }
        } else {

            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    private function sendnotificationAttendence($student_id, $newattendence,$late)
    {
        $reminderrecipients = array();
        $studentobj = new Students();
        $studentdata = $studentobj->findByPk($student_id);
        $reminderrecipients[] = $studentdata->user_id;
        
        if ($late==1)
          $message = $studentdata->first_name." ".$studentdata->last_name." is absent on (forenoon)".$newattendence->month_date;
        else
          $message = $studentdata->first_name." ".$studentdata->last_name." is absent on ".$newattendence->month_date;
        
        if($studentdata->immediate_contact_id)
        {
            $reminderrecipients[] = $studentdata->immediate_contact_id;
        }
        
        
        if($reminderrecipients)
        {
            $notifiation_ids = array();
            foreach($reminderrecipients as $value)
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
                $notifiation_ids[] = $reminder->id;
                
            }  
            $notifiation_id = implode(",", $notifiation_ids);
            $user_id = implode(",", $reminderrecipients);
            Settings::sendCurlNotification($user_id, $notification_id);
        }
    }
    
    
    private function sendnotificationleave($student_id,$status,$updateleave)
    {
        $reminderrecipients = array();
        $studentobj = new Students();
        $studentdata = $studentobj->findByPk($student_id);
        $reminderrecipients = array();

        if($studentdata->immediate_contact_id)
        {
            $reminderrecipients[] = $studentdata->immediate_contact_id;
        }
        $approved_text = "Denied";
        if($status==1)
        {
            $approved_text = "Approved";
        } 
        if($reminderrecipients)
        {
            $notifiation_ids = array();
            
            $reminder = new Reminders(); 
            //delete reminder previous
            $reminderdata = $reminder->getReminder($updateleave->id,10);
                    
            if($reminderdata)
            {
                foreach($reminderdata as $rvalue)
                {
                    $rfordelete = $reminder->findByPk($rvalue->id);
                    $rfordelete->delete();
                }
            }
            //delete reminder previous
            
            foreach($reminderrecipients as $value)
            {
                   
                $reminder = new Reminders(); 
                $reminder->sender = Yii::app()->user->id;
                $reminder->recipient = $value;
                $reminder->subject = "Your Leave Aplication is ".$approved_text." (".$studentdata->first_name.")";
                $reminder->body = "(".$studentdata->first_name.") leave application from ".$updateleave->start_date." to ".$updateleave->end_date." is ".$approved_text."";
                $reminder->created_at = date("Y-m-d H:i:s");
                $reminder->rid = $updateleave->id;
                $reminder->rtype = 10;
                $reminder->updated_at = date("Y-m-d H:i:s");
                $reminder->school_id = Yii::app()->user->schoolId;
                $reminder->save();
                $notifiation_ids[] = $reminder->id;
            } 
            $notifiation_id = implode(",", $notifiation_ids);
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
         if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $leave_id && $student_id)
         {
             $leaveStudent = new ApplyLeaveStudents();
             $updateleave = $leaveStudent->findByPk($leave_id);
             if(isset($updateleave->student_id) && $updateleave->student_id==$student_id)
             {
                 if(!$status)
                 {
                     $status = 1;
                 }
                 $updateleave->approved = $status;
                 $updateleave->approving_teacher = Yii::app()->user->profileId;
                 
                 //sending notification
                 $reminderrecipients = array();
                 $studentobj = new Students();
                 $studentdata = $studentobj->findByPk($student_id);
                 $reminderrecipients[] = $studentdata->user_id;
                 $updateleave->save(false);
                 
                 $this->sendnotificationleave($student_id, $status, $updateleave);
                 
                 $check_date = $updateleave->start_date;
                 $end_date = $updateleave->end_date;
                 
                 while ($check_date <= $end_date) 
                 { 
                    $attendence = new Attendances();
                    $attendence_student = $attendence->getAttendenceStudent($student_id, $check_date);
                    
                    if($attendence_student)
                    {
                        $objatt = $attendence->findByPk($attendence_student->id);
                        if($status==1)
                        {
                            $objatt->is_leave = 1;
                        }
                        else
                        {
                            $objatt->is_leave = 0;
                        }    
                        $objatt->save();
                    }  
                    else if($status==1)
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
                        $attendence->reason = $updateleave->reason;
                        $attendence->is_leave = 1;
                        $attendence->save();
                        
                    }
                    $check_date = date ("Y-m-d", strtotime ("+1 day", strtotime($check_date)));      
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
        
        if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $batch_id && $student_id)
        {
            $student_ids = explode(",", $student_id);
            $lates = explode(",", $late);
            if(!$date)
            {
                $date = date("Y-m-d");
            }
            $attendence = new Attendances();
            $attendence_batch = $attendence->getAttendenceBatch($batch_id, $date);
            if($attendence_batch)
            foreach($attendence_batch as $value)
            {
                $previous_attendence = $attendence->findbypk($value->id);
                if($previous_attendence)
                {
                    $reminder = new Reminders();

                    $reminderdata = $reminder->getReminder($value->id);
                    
                    if($reminderdata)
                    {
                        foreach($reminderdata as $rvalue)
                        {
                            $rfordelete = $reminder->findByPk($rvalue->id);
                            $rfordelete->delete();
                        }
                    }    

                    $previous_attendence->delete();
                }
            }    
            
            foreach($student_ids as $key=>$student_id)
            {

            
           
                $late = (isset($lates[$key]))?$lates[$key]:0;
            
                $newattendence = new Attendances();
                
                $leaveStudent = new ApplyLeaveStudents();
                $leave_today = $leaveStudent->getallleaveStudentsDate($date);
                
                if(isset($leave_today['approved']) && in_array($student_id, $leave_today['approved']))
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
                if($late && $late==1)
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
                
                $this->sendnotificationAttendence($student_id, $newattendence,$late);
               
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
        
        if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $batch_id )
        {
            if(!$date)
            {
                $date = date("Y-m-d");
            }
            $attendence = new Attendances();
            $bacthes = $attendence->getBatchStudentTodayAttendence($batch_id,$date);
            $total = count($bacthes);
            $student = array("present"=>array(),"absent"=>array(),"late"=>array(),"leave"=>array());
            $present = 0;
            $late = 0;
            $absent = 0;
            $leave = 0;
            
            foreach($bacthes as $value)
            {
                if($value['main_status']==1)
                {
                   $student['present'][$present]['student_name'] = $value['student_name'];
                   $student['present'][$present]['roll_no'] = $value['roll_no'];
                   $present++; 
                  
                }    
                else if($value['main_status']==0)
                {
                   $student['absent'][$absent]['student_name'] = $value['student_name'];
                   $student['absent'][$absent]['roll_no'] = $value['roll_no'];
                   $absent++;
                } 
                else if($value['main_status']==2)
                {
                   $student['late'][$late]['student_name'] = $value['student_name'];
                   $student['late'][$late]['roll_no'] = $value['roll_no'];
                   $late++; 
                } 
                else if($value['main_status']==3)
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
        
        if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $batch_id)
        {
            if(!$date)
            {
                $date = date("Y-m-d");
            }
            $attendence = new Attendances();
            $bacthes = $attendence->getBatchStudentTodayAttendence($batch_id,$date);
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
        
        if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $student_id)
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
        $fullname = ($std->first_name)?$std->first_name." ":"";
        $fullname.= ($std->middle_name)?$std->middle_name." ":"";
        $fullname.= ($std->last_name)?$std->last_name:"";
        $student['name'] = $fullname;
        $student['roll'] = $std->class_roll_no;
        $student['admission_no'] = $std->admission_no;
        $student['date_of_birth'] = $std->date_of_birth;
        $student['gender'] = $std->gender;
        $student['admission_no'] = $std->admission_no;
        $student['class'] = $std['batchDetails']['courseDetails']->course_name;
        $student['batch'] = $std['batchDetails']->name;
        $fullname = "";
        if(isset($std['guradianDetails']->first_name))
        {
            $fullname = ($std['guradianDetails']->first_name)?$std['guradianDetails']->first_name." ":"";
            $fullname.= ($std['guradianDetails']->last_name)?$std['guradianDetails']->last_name:"";
        }  
        $free = new Freeusers();
       
        
        $user_image = $free->getUserImage($std->user_id);
        $student['user_image'] = "";
        
        if(isset($user_image['profile_image']))
        {
            $student['user_image'] = $user_image['profile_image'];
        }    
        
        $student['guradian'] = $fullname;
        return $student;
    }        
    
    public function actionGetBatch()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        
        if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
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
