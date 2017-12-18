<?php

class AttendanceController extends Controller
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
                'actions' => array('getsubject', 'getstudents', 'addattendence','getstudentsbysubname','reportteacherbyname', 'reportteacher','reportallstd', 'associatesubject', 'report' , 'reportallteacher', 'reportallteachername'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actiongetSubject()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )))
            {
                if (Yii::app()->user->isParent)
                {
                    $batch_id = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                
                $subject = new Subjects();
                $all_subject = $subject->getSubject($batch_id, $student_id);
                $response['data']['subject'] = $all_subject;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Found";
                
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
    
    public function actiongetStudentsBySubName()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $subject_id = Yii::app()->request->getPost('subject_id');
        $date = Yii::app()->request->getPost('date');
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $subject_id)
        {           
            if (!$date)
            {
                $date = date("Y-m-d");
            }
            $subjectObj = new Subjects();
            $sub_data = $subjectObj->findByPk($subject_id);
            
            if($sub_data->elective_group_id)
            {
                $stdObj = new StudentsSubjects();
                $all_student = $stdObj->getSubjectStudentFull($subject_id,$sub_data->batch_id);
            }
            else
            {
                $student = new Students();
                $all_student = $student->getBatchStudentFull($sub_data->batch_id);
            }

            $att_register = new SubjectAttendanceRegisters();
            $att_register_data = $att_register->getRegisterDataBySubjectName($subject_id, $date,$sub_data->batch_id);

            if ($att_register_data)
            {
               
                $response['data']['register'] = $att_register_data->id;
                $attobj = new SubjectAttendances();
                $att_data = $attobj->getAttendenceTimeTableSubName($subject_id, $date,$sub_data->batch_id);
                $i = 0;
                foreach ($all_student as $value)
                {
                    $students[$i] = $value;
                    $students[$i]['att'] = 1;
                    if (isset($att_data[$value['student_id']]))
                    {
                        if ($att_data[$value['student_id']] == 1)
                        {
                            $students[$i]['att'] = 3;
                        } 
                        else
                        {
                            $students[$i]['att'] = 2;
                        }
                    }
                    $i++;
                }
            } 
            else
            {
                $response['data']['register'] = "0";
                $students = $all_student;
            }
            $response['data']['students'] = $students;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Data Found";
            
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    public function actiongetStudents()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $subject_id = Yii::app()->request->getPost('subject_id');
        $date = Yii::app()->request->getPost('date');
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $subject_id)
        {           
            if (!$date)
            {
                $date = date("Y-m-d");
            }
            $subjectObj = new Subjects();
            $sub_data = $subjectObj->findByPk($subject_id);
            
            if($sub_data->elective_group_id)
            {
                $stdObj = new StudentsSubjects();
                $all_student = $stdObj->getSubjectStudentFull($subject_id,$sub_data->batch_id);
            }
            else
            {
                $student = new Students();
                $all_student = $student->getBatchStudentFull($sub_data->batch_id);
            }

            $att_register = new SubjectAttendanceRegisters();
            $att_register_data = $att_register->getRegisterData($subject_id, $date,$sub_data->batch_id);

            if ($att_register_data)
            {
               
                $response['data']['register'] = $att_register_data->id;
                $attobj = new SubjectAttendances();
                $att_data = $attobj->getAttendenceTimeTable($subject_id, $date,$sub_data->batch_id);
                $i = 0;
                foreach ($all_student as $value)
                {
                    $students[$i] = $value;
                    $students[$i]['att'] = 1;
                    if (isset($att_data[$value['student_id']]))
                    {
                        if ($att_data[$value['student_id']] == 1)
                        {
                            $students[$i]['att'] = 3;
                        } 
                        else
                        {
                            $students[$i]['att'] = 2;
                        }
                    }
                    $i++;
                }
            } 
            else
            {
                $response['data']['register'] = "0";
                $students = $all_student;
            }
            $response['data']['students'] = $students;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Data Found";
            
        } else
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
        $subject_id = Yii::app()->request->getPost('subject_id');
        $student_id = Yii::app()->request->getPost('student_id');
        $late = Yii::app()->request->getPost('late');
        $date = Yii::app()->request->getPost('date');
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $subject_id)
        {
            
            if (!$date)
            {
                $date = date("Y-m-d");
            }

            $subjectObj = new Subjects();
            $sub_data = $subjectObj->findByPk($subject_id);
            
            if($sub_data->elective_group_id)
            {
                $stdObj = new StudentsSubjects();
                $all_student = $stdObj->getSubjectStudentFull($subject_id,$sub_data->batch_id);
            }
            else
            {
                $student = new Students();
                $all_student = $student->getBatchStudentFull($sub_data->batch_id);
            }
            
            $total_student = count($all_student);
            $std_att_data = $this->formateStdData($student_id, $late);
            $this->deletePreviousReminder($subject_id, $date);
            $ids = array();
            $att_date = $date;
           
            $att_id = array();
            $lates_array = array();

            $present = $total_student;
            $late = 0;
            $absent = 0;

            if ($std_att_data)
            {
                foreach ($std_att_data as $student_data)
                {
                    $student_id = $student_data['id'];
                    $ids[] = $student_id;
                    $newattendence = new SubjectAttendances();
                    $newattendence->is_late = $student_data['late'];

                    if ($newattendence->is_late)
                    {
                        $lates_array[] = 1;
                        $late++;
                    } else
                    {
                        $lates_array[] = 0;
                        $absent++;
                        $present--;
                    }

                   
                    $newattendence->student_id = $student_id;
                    $newattendence->subject_id = $subject_id;
                    $newattendence->batch_id = $sub_data->batch_id;
                    $newattendence->attendance_date = $date;
                    $newattendence->created_at = date("Y-m-d H:i:s");
                    $newattendence->updated_at = date("Y-m-d H:i:s");
                    $newattendence->school_id = Yii::app()->user->schoolId;
                    $newattendence->save();
                    $att_id[] = $newattendence->id;
                }
                if ($att_id)
                {
                    $this->sendNotificationAll($ids, $att_date, $subject_id, $att_id, $lates_array);
                }
            }
            $this->register($subject_id,$sub_data->batch_id, $present, $absent, $late,$date);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Success";
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    public function actionReportAllTeacherName()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $subject_id = Yii::app()->request->getPost('subject_id');
        $date_start = Yii::app()->request->getPost('date_start');
        $date_end = Yii::app()->request->getPost('date_end');
        
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $subject_id)
        { 
            $subjectObj = new Subjects();
            $sub_data = $subjectObj->findByPk($subject_id);     
            if($sub_data->elective_group_id)
            {
                $stdObj = new StudentsSubjects();
                $all_student = $stdObj->getSubjectStudentFull($subject_id,$sub_data->batch_id);
            }
            else
            {
                $student = new Students();
                $all_student = $student->getBatchStudentFull($sub_data->batch_id);
            }
            $att_register = new SubjectAttendanceRegisters();
            $total_class = $att_register->getRegisterClassName($subject_id,$sub_data->batch_id,$date_start,$date_end);
            
            $std_array = array();
            if($all_student)
            {
                foreach($all_student as $value)
                {
                    $std_array[] = $value['student_id'];
                }  
            }
            
            $att_std = new SubjectAttendances();
            $absent = $att_std->getAllStdAttname($std_array, $subject_id, $sub_data->batch_id,0,$date_start,$date_end);
            $late = $att_std->getAllStdAttname($std_array, $subject_id, $sub_data->batch_id, 1,$date_start,$date_end);
            
            $std_data = array();
            if($all_student)
            {
                $i = 0;
               foreach($all_student as $value)
               {
                  $std_data[$i]['roll_no'] = $value['roll_no'];
                  $std_data[$i]['name'] = $value['student_name'];
                  $present = $total_class;
                  $std_data[$i]['absent'] = 0;
                  $std_data[$i]['late'] =  0;
                  if(isset($absent[$value['student_id']]))
                  {
                      $std_data[$i]['absent'] = (int)$absent[$value['student_id']];
                      $present = $present -$absent[$value['student_id']]; 
                  } 
                  if(isset($late[$value['student_id']]))
                  {
                      $std_data[$i]['late'] = (int)$late[$value['student_id']];
                      $present = $present -$late[$value['student_id']];
                  }
                  $std_data[$i]['present'] = (int)$present;
                  $i++;
                  
               } 
            } 
            $response['data']['std_att'] = $std_data;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Data Found";
            
            
            
            
        } 
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    public function actionReportAllTeacher()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $subject_id = Yii::app()->request->getPost('subject_id');
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $subject_id)
        { 
            $subjectObj = new Subjects();
            $sub_data = $subjectObj->findByPk($subject_id);     
            if($sub_data->elective_group_id)
            {
                $stdObj = new StudentsSubjects();
                $all_student = $stdObj->getSubjectStudentFull($subject_id,$sub_data->batch_id);
            }
            else
            {
                $student = new Students();
                $all_student = $student->getBatchStudentFull($sub_data->batch_id);
            }
            $att_register = new SubjectAttendanceRegisters();
            $total_class = $att_register->getRegisterClass($subject_id,$sub_data->batch_id);
            
            $std_array = array();
            if($all_student)
            {
                foreach($all_student as $value)
                {
                    $std_array[] = $value['student_id'];
                }  
            }
            
            $att_std = new SubjectAttendances();
            $absent = $att_std->getAllStdAtt($std_array, $subject_id, $sub_data->batch_id);
            $late = $att_std->getAllStdAtt($std_array, $subject_id, $sub_data->batch_id, 1);
            
            $std_data = array();
            if($all_student)
            {
                $i = 0;
               foreach($all_student as $value)
               {
                  $std_data[$i]['roll_no'] = $value['roll_no'];
                  $std_data[$i]['name'] = $value['student_name'];
                  $present = $total_class;
                  $std_data[$i]['absent'] = 0;
                  $std_data[$i]['late'] =  0;
                  if(isset($absent[$value['student_id']]))
                  {
                      $std_data[$i]['absent'] = (int)$absent[$value['student_id']];
                      $present = $present -$absent[$value['student_id']]; 
                  } 
                  if(isset($late[$value['student_id']]))
                  {
                      $std_data[$i]['late'] = (int)$late[$value['student_id']];
                      $present = $present -$late[$value['student_id']];
                  }
                  $std_data[$i]['present'] = (int)$present;
                  $i++;
                  
               } 
            } 
            $response['data']['std_att'] = $std_data;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Data Found";
            
            
            
            
        } 
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }  
    
    public function actionReportTeacherbyName()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $subject_id = Yii::app()->request->getPost('subject_id');
        $date = Yii::app()->request->getPost('date');
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $subject_id)
        {
            
            $class_completed = 0;
            if (!$date)
            {
                $date = date("Y-m-d");
            }
            
            $subjectObj = new Subjects();
            $sub_data = $subjectObj->findByPk($subject_id);
            
            $present = 0;
            $absent = 0;
            $late = 0;
            $att_register = new SubjectAttendanceRegisters();
            
            

            $att_register_data = $att_register->getRegisterDataBySubjectName($subject_id, $date, $sub_data->batch_id);
            if ($att_register_data)
            {
                $class_completed = 1;
                $present = $present + $att_register_data->present;
                $absent = $absent + $att_register_data->absent;
                $late = $late + $att_register_data->late;
            }

            $response['data']['date'] = $date;
            $response['data']['class_completed'] = $class_completed;
            $response['data']['total'] = $present + $absent;
            $response['data']['present'] = $present;
            $response['data']['absent'] = $absent;
            $response['data']['late'] = $late;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Data Found";
        
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    public function actionReportTeacher()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $subject_id = Yii::app()->request->getPost('subject_id');
        $date = Yii::app()->request->getPost('date');
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $subject_id)
        {
            
            $class_completed = 0;
            if (!$date)
            {
                $date = date("Y-m-d");
            }
            
            $subjectObj = new Subjects();
            $sub_data = $subjectObj->findByPk($subject_id);
            
            $present = 0;
            $absent = 0;
            $late = 0;
            $att_register = new SubjectAttendanceRegisters();
            
            

            $att_register_data = $att_register->getRegisterDataBySubjectName($subject_id, $date, $sub_data->batch_id);
            if ($att_register_data)
            {
                $class_completed = 1;
                $present = $present + $att_register_data->present;
                $absent = $absent + $att_register_data->absent;
                $late = $late + $att_register_data->late;
            }

            $response['data']['date'] = $date;
            $response['data']['class_completed'] = $class_completed;
            $response['data']['total'] = $present + $absent;
            $response['data']['present'] = $present;
            $response['data']['absent'] = $absent;
            $response['data']['late'] = $late;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Data Found";
        
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    } 
    public function actionAssociateSubject()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
            $employee_id = Yii::app()->user->profileId;
            $empsubobj = new EmployeesSubjects();
            $employee_subjects = $empsubobj->getAllSubject($employee_id);
            $response['data']['subjects'] = $employee_subjects;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Data Found";
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    public function actionReportAllStd()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $student_id = Yii::app()->request->getPost('student_id');

        if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent && $student_id )))
        {
            if (Yii::app()->user->isStudent)
            {
                $student_id = Yii::app()->user->profileId;
                $batch_id = Yii::app()->user->batchId;
            } else
            {
                $stdobj = new Students();
                $stdData = $stdobj->findByPk($student_id);
                $batch_id = $stdData->batch_id;
            }
            $subject_report = array();
            $subject = new Subjects();
            $all_subject = $subject->getSubject($batch_id, $student_id);
            $i = 0;
            if($all_subject)
            {
                foreach($all_subject as $value)
                {
                    $subject_report[$i] = $value;
                    $subject_id = $value['id'];
                    $registerobj = new SubjectAttendanceRegisters();
                    $subject_report[$i]['total'] = $registerobj->getTotalRegisterStudent($subject_id, $batch_id, 1);
                    $atovj = new SubjectAttendances();
                    $subject_report[$i]['absent'] = $atovj->getAllattendence($student_id, $subject_id, $batch_id, 1);
                    $subject_report[$i]['late'] = $atovj->getAllattendence($student_id, $subject_id, $batch_id, 1, 1);
                    $subject_report[$i]['present'] = $subject_report[$i]['total']-$subject_report[$i]['absent']-$subject_report[$i]['late'];
                    $i++;
                } 
            }
            
           
               
            $response['data']['report'] = $subject_report;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "REPORT FOUND";
           
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }        
    public function actionreport()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $student_id = Yii::app()->request->getPost('student_id');
        $subject_id = Yii::app()->request->getPost('subject_id');
        $report_type = Yii::app()->request->getPost('report_type');

        if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent && $student_id )))
        {
            if (Yii::app()->user->isStudent)
            {
                $student_id = Yii::app()->user->profileId;
                $batch_id = Yii::app()->user->batchId;
            } else
            {
                $stdobj = new Students();
                $stdData = $stdobj->findByPk($student_id);
                $batch_id = $stdData->batch_id;
            }

            if (!$report_type)
            {
                $report_type = 0;
            }

            $registerobj = new SubjectAttendanceRegisters();
            $total = $registerobj->getTotalRegisterStudent($subject_id, $batch_id, $report_type);
            if ($total > 0)
            {
                $atovj = new SubjectAttendances();

                $absent = $atovj->getAllattendence($student_id, $subject_id, $batch_id, $report_type);
                $late = $atovj->getAllattendence($student_id, $subject_id, $batch_id, $report_type, 1);
                
                $subject_name = "";
                if($subject_id)
                {
                    $subObj = new Subjects();
                    $subData = $subObj->findByPk($subject_id);
                    $subject_name = $subData->name;
                    
                }

                $present = $total - $absent-$late;
                $response['data']['report']['subject_name'] = $subject_name;
                $response['data']['report']['total'] = (int) $total;
                $response['data']['report']['absent'] = (int) $absent;
                $response['data']['report']['late'] = (int) $late;
                $response['data']['report']['present'] = (int) $present;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "EVENTS_FOUND";
            } else
            {
                $response['status']['code'] = 404;
                $response['status']['msg'] = "No attendance report found";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    private function sendNotificationAll($ids, $att_date, $subject_id, $att_id, $late)
    {
        $subobj = new Subjects();
        $sub_data = $subobj->findByPk($subject_id);
        $studentobj = new Students();
        $all_students = $studentobj->getByIdsStudent($ids);
        $reminder_ids = array();
        $receiptionist_ids = array();
        $sms_numbers = array();
        $sms_msg_array = array();
        foreach ($all_students as $key => $value)
        {
            $reminderrecipients = array();
            $batch_ids = array();
            $student_ids = array();

            $reminderrecipients[] = $receiptionist_ids[] = $value->user_id;
            $batch_ids[$value->user_id] = $value->batch_id;
            $student_ids[$value->user_id] = $value->id;
            if (isset($value['guradianDetails']) && $value['guradianDetails'])
            {
                $reminderrecipients[] = $receiptionist_ids[] = $value['guradianDetails']->user_id;
                $batch_ids[$value['guradianDetails']->user_id] = $value->batch_id;
                $student_ids[$value['guradianDetails']->user_id] = $value->id;
            }

            if ($late[$key] == 1)
            {
                $message = $value->first_name . " " . $value->last_name . " is Present but Late in " . $sub_data->name . " on " . $att_date;
            } 
            else
            {
                $message = $value->first_name . " " . $value->last_name . " is absent in " . $sub_data->name . " on " . $att_date;
            }

            if ($value->phone2)
            {
                $sms_numbers[] = $value->phone2;
                $sms_msg_array[] = $message;
            }
            if (isset($value['guradianDetails']) && $value['guradianDetails'] && $value['guradianDetails']->mobile_phone)
            {
                $sms_numbers[] = $value['guradianDetails']->mobile_phone;
                $sms_msg_array[] = $message;
            }

            

            if ($reminderrecipients)
            {
                $notification_ids = array();
                foreach ($reminderrecipients as $rvalue)
                {
                    $reminder = new Reminders();
                    $reminder->sender = Yii::app()->user->id;
                    $reminder->recipient = $rvalue;
                    $reminder->subject = "Subject Attendance Notice";
                    $reminder->body = $message;
                    $reminder->created_at = date("Y-m-d H:i:s");
                    $reminder->rid = $att_id[$key];
                    $reminder->rtype = 45;
                    $reminder->batch_id = $batch_ids[$rvalue];
                    $reminder->student_id = $student_ids[$rvalue];
                    $reminder->updated_at = date("Y-m-d H:i:s");
                    $reminder->school_id = Yii::app()->user->schoolId;
                    $reminder->save();
                    $notification_ids[] = $reminder_ids[] = $reminder->id;
                }
            }
        }
        
        if(count($sms_numbers)>0 && in_array(Yii::app()->user->schoolId,Sms::$sms_subject_attendence_school))
        {
            Sms::send_sms_ssl($sms_numbers, $sms_msg_array,  Yii::app()->user->schoolId);
        }    
        
        if ($reminder_ids && $receiptionist_ids)
        {
            $notification_id = implode(",", $reminder_ids);
            $user_id = implode(",", $receiptionist_ids);
            shell_exec("php pushnoti.php $notification_id $user_id  > /dev/null 2>/dev/null &");
           
        }
    }

    
    private function deletePreviousReminder($subject_id, $date)
    {
        $subjectObj = new Subjects();
        $sub_data = $subjectObj->findByPk($subject_id);
        
        $attendence = new SubjectAttendances();
        $attendence_subject = $attendence->getAttendence($subject_id, $date, $sub_data->batch_id);
        $all_rids = array();
        if ($attendence_subject)
        {
            foreach ($attendence_subject as $value)
            {

                $all_rids[] = $value->id;
            }
        }
        if ($all_rids)
        {
            $all_rid_string = implode(",", $all_rids);
            Reminders::model()->deleteAll(
                    "`rid` IN (:rid) AND `rtype`=:rtype", array(':rid' => $all_rid_string, ':rtype' => 45)
            );
        }
        SubjectAttendances::model()->deleteAll(
                "`attendance_date` =:attendance_date AND `subject_id`=:subject_id", array(':attendance_date' => $date, ':subject_id' => $subject_id)
        );
    }

    private function formateStdData($student_id, $late)
    {
        $student_ids = array();
        if ($student_id)
        {
            $student_ids = explode(",", $student_id);
            $lates = explode(",", $late);
        }

        $std_att_data = array();
        $i = 0;
        if ($student_ids)
        {
            foreach ($student_ids as $key => $value)
            {
                $std_att_data[$i]['id'] = $value;
                $std_att_data[$i]['late'] = (isset($lates[$key])) ? $lates[$key] : 0;
                $i++;
            }
        }
        if ($std_att_data)
        {
            usort($std_att_data, function($a, $b)
            {
                return $b['id'] - $a['id'];
            });
        }
        return $std_att_data;
    }
    private function register($subject_id,$batch_id, $present, $absent, $late, $date)
    {
        $subjectObj = new Subjects();
        $sub_data = $subjectObj->findByPk($subject_id);
        
        $att_register_obj = new SubjectAttendanceRegisters();
        $att_register_data = $att_register_obj->getRegisterData($subject_id, $date, $sub_data->batch_id);

        if ($att_register_data)
        {
            $att_register = $att_register_obj->findByPk($att_register_data->id);
        } else
        {
            $att_register = new SubjectAttendanceRegisters();
            $att_register->attendance_date = $date;
            $att_register->created_at = date("Y-m-d H:i:s");
        }
        $att_register->subject_id = $subject_id;
        $att_register->batch_id = $batch_id;
        $att_register->employee_id = Yii::app()->user->profileId;
        $att_register->present = $present;
        $att_register->absent = $absent;
        $att_register->late = $late;
        $att_register->updated_at = date("Y-m-d H:i:s");
        $att_register->school_id = Yii::app()->user->schoolId;
        $att_register->save();
        return $att_register;
    }

}
