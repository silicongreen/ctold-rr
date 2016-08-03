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
        
        $studentdata = $studentobj->findByPk($student_id);
        $reminderrecipients[] = $studentdata->user_id;
        $batch_ids[$studentdata->user_id] = $studentdata->batch_id;
        $student_ids[$studentdata->user_id] = $studentdata->id;
        
        if($studentdata->phone2)
        {
            $sms_numbers[] = $studentdata->phone2;
        }    

        if ($late == 1)
            $message = $studentdata->first_name . " " . $studentdata->last_name . " is Present but Late on " . $newattendence->month_date;
        else
            $message = $studentdata->first_name . " " . $studentdata->last_name . " is absent on " . $newattendence->month_date;
        
        $gstudent = new GuardianStudent(); 
        
        $all_g = $gstudent->getGuardians($student_id);

        if ($all_g)
        {
            foreach($all_g as $value)
            {
                $gr = new Guardians();
                $grdata = $gr->findByPk($value['guardian']->id);
                if($grdata->user_id)
                {
                    $reminderrecipients[] = $grdata->user_id;
                    $batch_ids[$grdata->user_id] = $studentdata->batch_id;
                    $student_ids[$grdata->user_id] = $studentdata->id;
                    if($grdata->mobile_phone && $grdata->id == $studentdata->immediate_contact_id)
                    {
                        $sms_numbers[] = $grdata->mobile_phone;
                    }
                }
            }    
            
        }
        
        
//        if($sms_numbers && in_array($studentdata->school_id,Sms::$sms_attendence_school))
//        {
//            $sms_msg = new SmsMessages();
//            $sms_msg->body = str_replace(" ","+", $message);
//            $sms_msg->created_at = date("Y-m-d H:i:s");
//            $sms_msg->updated_at = date("Y-m-d H:i:s");
//            $sms_msg->school_id = $studentdata->school_id;
//            $sms_msg->save();
//            foreach ($sms_numbers as $value)
//            {
//                $sms_data = array($value,str_replace(" ","+", $message));
//                Sms::send_sms($sms_data, $studentdata->school_id,$sms_msg->id);
//            } 
//        }        


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
    public function actionAddAttendance()
    {
        
         $card_numbers = Yii::app()->request->getPost('card_numbers');
         $school_id = Yii::app()->request->getPost('school_id');
         $students_id = Yii::app()->request->getPost('student_ids');
         $date = Yii::app()->request->getPost('date');
         $card_number_array = explode(",", $card_numbers);
         $student_id_array = explode(",", $students_id);
         $std = new Students();

         if($school_id)
         {
            if(count($card_number_array) == count($student_id_array))
            {
                $absent_studnets = $std->getStudentNotInAdmission($student_id_array,$school_id);
            }
            else 
            {
                $absent_studnets = $std->getStudentNotInCard($card_number_array,$school_id);
            }
           
            $attendence = new Attendances();
            $attendence->deleteAttendanceStudent($school_id, $date);
            $leaveStudent = new ApplyLeaveStudents();
            $leave_today = $leaveStudent->getallleaveStudentsDate($date,$school_id);

            if($absent_studnets)
            {
               foreach ($absent_studnets as $student)
               {

                    $student_id = $student->id;
                   
                    
//                    $attendence_batch = $attendence->getAttendenceStudent($student_id, $date);
//
//                    if ($attendence_batch)
//                    {
//                        $previous_attendence = $attendence->findbypk($attendence_batch->id);
//                        if ($previous_attendence)
//                        {
//                            $reminder = new Reminders();
//
//                            $reminderdata = $reminder->getReminder($attendence_batch->id);
//
//                            if ($reminderdata)
//                            {
//                                foreach ($reminderdata as $rvalue)
//                                {
//                                    $rfordelete = $reminder->findByPk($rvalue->id);
//                                    $rfordelete->delete();
//                                }
//                            }
//
//                            $previous_attendence->delete();
//                        }
//                    }
                   
                   
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

                   if(!isset($newattendence->is_leave) || $newattendence->is_leave!=1)
                   {
                       $this->sendnotificationAttendence($student_id, $newattendence, 0);
                   }
               }
               
            }
            $response['success'] = true;
            $response['msg'] = "Successfully Inseted";
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

