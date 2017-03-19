<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class CardattController extends Controller
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
                'actions' => array('addattendance'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
     private function sendnotificationAttendence($student_id, $newattendence, $late)
    {
        $reminderrecipients = array();
        $batch_ids = array();
        $student_ids = array();
        $studentobj = new Students();
        
        $sms_numbers = array();
        $sms_msg = array();
        
        $studentdata = $studentobj->findByPk($student_id);
        $reminderrecipients[] = $studentdata->user_id;
        $batch_ids[$studentdata->user_id] = $studentdata->batch_id;
        $student_ids[$studentdata->user_id] = $studentdata->id;
        
        $message = $studentdata->first_name . " " . $studentdata->last_name . " is absent on " . $newattendence->month_date;
        if($studentdata->phone2)
        {
            $sms_numbers[] = $studentdata->phone2;
            $sms_msg[] = $message;
        }
        
        $gstudent = new GuardianStudent(); 
        
        $all_g = $gstudent->getGuardians($student_id);

        if ($all_g)
        {
            foreach($all_g as $value)
            {
                if(isset($value['guardian']) && isset($value['guardian']->id))
                {
                    $gr = new Guardians();
                    $grdata = $gr->findByPk($value['guardian']->id);
                    if($grdata && $grdata->user_id && !in_array($grdata->user_id, $reminderrecipients))
                    {
                        $reminderrecipients[] = $grdata->user_id;
                        $batch_ids[$grdata->user_id] = $studentdata->batch_id;
                        $student_ids[$grdata->user_id] = $studentdata->id;
                        if($grdata->mobile_phone && $grdata->id == $studentdata->immediate_contact_id)
                        {
                            $sms_numbers[] = $grdata->mobile_phone;
                            $sms_msg[] = $message;
                        }
                    }
                }
            }    
            
        }
        
        Sms::send_sms_ssl($sms_numbers, $sms_msg, $studentdata->school_id);

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
                $reminder->batch_id = $batch_ids[$value];
                $reminder->student_id = $student_ids[$value];
                
                $reminder->updated_at = date("Y-m-d H:i:s");
                $reminder->school_id = $student_id;
                $reminder->save();
                $notification_ids[] = $reminder->id;
            }
            $notification_id = implode(",", $notification_ids);
            $user_id = implode(",", $reminderrecipients);
            Settings::sendCurlNotification($user_id, $notification_id);
        }
    }
    private function insert_student($std,$ids_array,$entry_date_time_array,$school_id)
    {
        $ids_unique = array_unique($ids_array); 
        $user_mapping = $std->getUserByIdsStudent($ids_unique, $school_id);
        $insert_array = array();
        foreach($ids_array as $key=>$value)
        {
            $valid_id = $value;
            if($value && isset($user_mapping[$valid_id]))
            {
                $user_id_and_profile_id = explode("|||", $user_mapping[$valid_id]);
                $timestamp = $entry_date_time_array[$key];

                $date = date("Y-m-d", strtotime($timestamp));
                $time = date("H:i:s",  strtotime($timestamp));

                $insert_array[] = array("school_id"=>$school_id,"user_id"=>$user_id_and_profile_id[0],"profile_id"=>$user_id_and_profile_id[1],"date"=>$date,"time"=>$time,"type"=>2);

            }
        } 
        if($insert_array)
        {
            $builder=Yii::app()->db->schema->commandBuilder;
            $command=$builder->createMultipleInsertCommand('card_attendance', $insert_array);
            $command->execute();
        }
    } 
    private function insert_employee($emp,$ids_array,$entry_date_time_array,$school_id)
    {
        $ids_unique = array_unique($ids_array); 
        $user_mapping = $emp->getUserByIds($ids_unique, $school_id);
        $insert_array = array();
        foreach($ids_array as $key=>$value)
        {
            $valid_id = $value;
            if($value && isset($user_mapping[$valid_id]))
            {
                $user_id_and_profile_id = explode("|||", $user_mapping[$valid_id]);
                $timestamp = $entry_date_time_array[$key];

                $date = date("Y-m-d", strtotime($timestamp));
                $time = date("H:i:s",  strtotime($timestamp));

                $insert_array[] = array("school_id"=>$school_id,"user_id"=>$user_id_and_profile_id[0],"profile_id"=>$user_id_and_profile_id[1],"date"=>$date,"time"=>$time,"type"=>1);

            }
        } 
        if($insert_array)
        {
            $builder=Yii::app()->db->schema->commandBuilder;
            $command=$builder->createMultipleInsertCommand('card_attendance', $insert_array);
            $command->execute();
        }
    }        
       
    public function actionAddAttendance()
    {
        
         $card_numbers = Yii::app()->request->getPost('card_numbers');
         $school_id = Yii::app()->request->getPost('school_id');
         $students_id = Yii::app()->request->getPost('student_ids');
         $all_students_id = Yii::app()->request->getPost('all_ids');
         $date = Yii::app()->request->getPost('date');
         $card_number_array = explode(",", $card_numbers);
         $student_id_array = explode(",", $students_id);
         $std = new Students();
         
         $emp = new Employees();
         
         $ids = Yii::app()->request->getPost('ids');
         $entry_date_time = Yii::app()->request->getPost('entry_date_time');
         
        ///insert card attendnace intime out time
        if($school_id && $entry_date_time && $ids && in_array($school_id,Settings::$card_attendence_school))
        {
            //date_default_timezone_set(Settings::$school_card_time_zone[$school_id]);
            $ids_array = explode(",", $ids);
            $entry_date_time_array = explode(",",$entry_date_time); 
            if(count($ids_array) == count($entry_date_time_array))
            {

                $this->insert_employee($emp,$ids_array,$entry_date_time_array,$school_id);
                $this->insert_student($std,$ids_array,$entry_date_time_array,$school_id);

            }
        }   
        ///end card attendance intime out time

         if($all_students_id && $school_id && in_array($school_id,Settings::$card_attendence_school) && !Settings::$sync_off)
         {
             
            $card_logs = new CardLog();
            $card_logs->school_id = $school_id;
            $card_logs->date = $date;
            $card_logs->log = json_encode($_POST);
            $card_logs->save();
            
            
            

            $all_std_machine = explode("|", $all_students_id);
            $all_std_admission = array();
            foreach($all_std_machine as $value)
            {
                $card_id = explode("+",$value);
                if(isset($card_id[1]) && $card_id[1]!="")
                {
                    $all_std_admission[]= $card_id[1];
                }    
            }
            $all_std_id = array();
            if($all_std_admission)
            {
                $all_std_id = $std->getMechineStd($school_id,$all_std_admission);
            }
            
            
            //employee attendance
            $all_emp_id = array();
            if($all_std_admission)
            {
                $all_emp_id = $emp->getMechineEmp($school_id,$all_std_admission);
            }
            
            
            if($all_emp_id)
            {
               $absent_employee = $emp->getEmployeeNotInEmployeeNumber($student_id_array,$school_id,$all_std_admission); 
               $em_attendance = new EmployeeAttendances();
               $em_attendance->deleteAttendanceEmployee($school_id,$date,$all_emp_id);
               
               if($absent_employee)
               {
                   foreach($absent_employee as $value)
                   {
                       $em_attendance = new EmployeeAttendances();
                       $em_attendance->employee_id = $value->id;
                       $em_attendance->attendance_date = $date;
                       $em_attendance->created_at = date("Y-m-d H:i:s");
                       $em_attendance->updated_at = date("Y-m-d H:i:s");
                       $em_attendance->school_id = $school_id;
                       $em_attendance->reason = "From Smart Card";
                       $em_attendance->is_half_day = 0;
                       $em_attendance->save();
                   }    
               }
               
            }
            // employee attendance
            
            if($all_std_id)
            {
                $absent_studnets = $std->getStudentNotInAdmission($student_id_array,$school_id,$all_std_admission);
                $attendence = new Attendances();
                $attendence->deleteAttendanceStudent($school_id, $date,$all_std_id);


                $leaveStudent = new ApplyLeaveStudents();
                $leave_today = $leaveStudent->getallleaveStudentsDate($date,$school_id);
                $notification_send = new NotificationSend();
                $students_ids_array = $notification_send->getNotificationSend($date, $school_id);
                $students_ids = array();
                $notification_send_id = 0;
                if($students_ids_array)
                {
                    $students_ids = $students_ids_array['students_ids'];
                    $notification_send_id = $students_ids_array['id'];
                }

                if($absent_studnets)
                {
                   $fatt = new ForceAttendences();
                   $stdf = $fatt->getAll($school_id, $date);
                   foreach ($absent_studnets as $student)
                   {

                       if(in_array($student->id, $stdf))
                       {
                           continue;
                       }
                       $student_id = $student->id;


                       $newattendence = new Attendances();



                       if (isset($leave_today['approved']) && in_array($student_id, $leave_today['approved']))
                       {
                           $newattendence->is_leave = 1;
                       }

                       $newattendence->batch_id = $student->batch_id;
                       $newattendence->student_id = $student_id;
                       $newattendence->month_date = $date;
                       $newattendence->created_at = date("Y-m-d H:i:s");
                       $newattendence->updated_at = date("Y-m-d H:i:s");
                       $newattendence->school_id = $school_id;

                       $newattendence->forenoon = 1;
                       $newattendence->afternoon = 1;

                       $newattendence->save();

                       if((!isset($newattendence->is_leave) || $newattendence->is_leave!=1) && !in_array($student_id, $students_ids))
                       {
                           $this->sendnotificationAttendence($student_id, $newattendence, 0);
                           $students_ids[] = $student_id;
                       }
                   }

                    if($notification_send_id)
                    {
                        $notification_sendobj = $notification_send->findByPk($notification_send_id);
                        $notification_sendobj->student_ids = json_encode($students_ids);
                        $notification_sendobj->save();
                    }
                    else if($students_ids)
                    {
                        $notification_send->school_id = $school_id;
                        $notification_send->student_ids = json_encode($students_ids);
                        $notification_send->date = $date;
                        $notification_send->save();
                    }


                }
                $response['success'] = true;
                $response['msg'] = "Successfully Inseted";
            }   
         }
         else
         {
            $response['post_data'] = $_POST; 
            $response['get_data'] = $_GET; 
            $response['request_data'] = $_REQUEST; 
            $response['success'] = false;
            $response['msg'] = "Invalid School";
         }  
         $response['card_count'] = count($card_number_array);
         $response['std_count'] = count($student_id_array);
           
         echo CJSON::encode($response);
         Yii::app()->end();
    }
    
   
}

