<?php
class ReportController extends Controller
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
                'actions' => array('index','progressall','attendence','getsubject','progress','classtestreport','allexam', 'Getfullreport','getexamreport','acknowledge'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionattendence()
    {
       $user_secret = Yii::app()->request->getPost('user_secret');
       if(Yii::app()->user->user_secret === $user_secret)
       {
            $school_id = Yii::app()->user->schoolId;
            if(Yii::app()->user->isParent)
            {
                $batch_id   = Yii::app()->request->getPost('batch_id');
                $student_id = Yii::app()->request->getPost('student_id');
                $studentobj = new Students();
                $stddata = $studentobj->findByPk($student_id);
                $user_id = $stddata->user_id;
            }
            else if(Yii::app()->user->isStudent)
            {
                $batch_id   = Yii::app()->user->batchId;
                $student_id = Yii::app()->user->profileId;
                $user_id = Yii::app()->user->id;
            }
            else
            {
                $batch_id = 0;
                
                $student_id = Yii::app()->user->profileId;
                $user_id = Yii::app()->user->id;
            } 
            $response['data']['profile_picture'] = "";
            
            $freobj = new Freeusers();
            $profile_image = $freobj->getUserImage($user_id);
            if (isset($profile_image['profile_image']) && $profile_image['profile_image'])
            {
                $response['data']['profile_picture'] = $profile_image['profile_image'];
            }
            
            $text = "";
            $date = date("Y-m-d");
            $objattendence = new Attendances();
            $holiday = new Events();
            $holiday_array = $holiday->getHolidayMonth($date, $date, $school_id);
            if ($holiday_array)
            {
                $text = "Today is Holiday";
            }
            if(!$text)
            {
                $weekend_array = $objattendence->getWeekend($school_id,$batch_id);
                $weekday = date("w", strtotime($date));
                if (in_array($weekday, $weekend_array))
                {
                    $text = "Today is weekend";
                } 
            }
            if(!$text)
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isStudent)
                {
                    $leave = new ApplyLeaveStudents();
                    $leave_array = $leave->getleaveStudentMonth($date, $date, $student_id);
                    if ($leave_array)
                    {
                        $text = "was on Leave Today";
                    }
                    if(!$text)
                    {
                        
                        $attendance_array = $objattendence->getAbsentStudentMonth($date, $date, $student_id);
                        if ($attendance_array['late'])
                        {
                            $text = "was Late Today";
                        }
                        else if ($attendance_array['absent'])
                        {
                            $text = "was Absent Today";
                        }
                        else
                        {
                            $timetableobj = new TimetableEntries();
                            $class_started = $timetableobj->classStarted($batch_id);
                            if($class_started)
                            {
                                $text = "was Present Today";
                            }
                            else if($date==date("Y-m-d"))
                            {
                                $text = "Class yet not started";
                            }    
                            
                        }
                    }
                }
                else
                {
                    $leave = new ApplyLeaves();
                    $leave_array = $leave->getleaveTeacher($student_id);
                    if($leave_array)
                    {
                        $text = "was on Leave Today";
                    }
                    if(!$text)
                    {
                        $attendence = new EmployeeAttendances();
                        $leave_array = $attendence->getAttTeacher($student_id);
                        if($leave_array)
                        {
                            $text = "was Absent Today";
                        }
                        else
                        {
                            $text = "was Present Today";
                        }
                    }
                }
                
            }
            
            
           $response['data']['text']    = $text;
           $response['status']['code']       = 200;
           $response['status']['msg']        = "Data Found"; 
       }
       else
       {
           $response['status']['code'] = 403;
           $response['status']['msg'] = "Access Denied."; 
       }
       
       echo CJSON::encode($response);
       Yii::app()->end();
    }
    
    public function actionProgressAll()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $exam_category = Yii::app()->request->getPost('exam_category');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                } 
                if(!$exam_category)
                {
                    $exam_category = 0;
                }
                $subjects = new Subjects();
                $progress = $subjects->getPrograssAll($batch_id, $student_id, $exam_category);
                
                if ($progress)
                {
                    $response['data']['progress']    = $progress;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $response['data']['progress']    = array();
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Not Found";
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
    
    public function actionProgress()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $subject_id = Yii::app()->request->getPost('subject_id');
            $exam_category = Yii::app()->request->getPost('exam_category');
            $response = array();
            if ($subject_id && Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                } 
                if(!$exam_category)
                {
                    $exam_category = 0;
                }
                $subjects = new Subjects();
                $progress = $subjects->getPrograss($batch_id, $student_id, $subject_id, $exam_category);
                
                if ($progress)
                {
                    $response['data']['progress']    = $progress;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $response['data']['progress']    = array();
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Not Found";
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
    public function actionGetSubject()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }    
                $subjects = new Subjects();
                $std_subjects = $subjects->getSubject($batch_id, $student_id);
                
                if ($std_subjects)
                {
                    $response['data']['subjects']    = $std_subjects;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $response['data']['subjects']    = array();
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Not Found";
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
    
    public function actionGetExamReport() {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $category_id = Yii::app()->request->getPost('category_id');
            $id = Yii::app()->request->getPost('id');
            $response = array();
            if ($id && Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }   
                
                $subjects = new Subjects();
                $term_report = $subjects->getTermReport($batch_id, $student_id, $id);
                if ($term_report) {
                    $robject = new Reminders();
                    $robject->ReadReminderNew(Yii::app()->user->id, 0 ,3, $id);
                    
                    $response['data']['report'] = $term_report[0];
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_FOUND";
                } else {
                    $response['data']['report'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_NOT_FOUND";
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
    
    public function actionAllExam() {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $category_id = Yii::app()->request->getPost('category_id');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }   
                if(!$category_id)
                {
                    $category_id = 3;
                } 
                else if($category_id=="all")
                {
                    $category_id = 0;
                }    
                $time_table = new ExamGroups();
                $time_table = $time_table->getAllExamsResultPublish($batch_id,$category_id);
                if ($time_table) {
                    $response['data']['all_exam'] = $time_table;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_FOUND";
                } else {
                    $response['data']['all_exam'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_FOUND";
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
    
    public function actionClassTestReport()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $exam_group = Yii::app()->request->getPost('exam_group');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }  
                if(!$exam_group)
                {
                    $exam_group = 0;
                }   
                
                $subjects = new Subjects();
                $exam_data = $subjects->getBatchSubjectClassTestProjectReport($batch_id, $student_id, $exam_group);
                
                if ($exam_data)
                {
                    $exam_ids = array();
                    foreach($exam_data as $exam_value)
                    {
                        foreach($exam_value['subject_exam']['class_test'] as $cvalue)
                        {
                           if(!in_array($cvalue['exam_id'], $exam_ids))
                           {
                               $exam_ids[] =  $cvalue['exam_id'];
                           }
                           
                        }
                        foreach($exam_value['subject_exam']['project'] as $pvalue)
                        {
                           if(!in_array($pvalue['exam_id'], $exam_ids))
                           {
                               $exam_ids[] =  $pvalue['exam_id'];
                           } 
                        }
                        
                    }    
                    
                  
                    $robject = new Reminders();
                    $robject->ReadReminderNew(Yii::app()->user->id, 0 ,3, $exam_ids);
                    
                    
                    $response['data']['class_test_report']    = $exam_data;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $response['data']['class_test_report']    = array();
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Not Found";
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
    
    public function actionGetfullreport()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }    
                $subjects = new Subjects();
                $term_report = $subjects->getTermReport($batch_id, $student_id);
               
                $exam_data = $subjects->getBatchSubjectClassTestProjectReport($batch_id, $student_id);
                
                if ($exam_data || $term_report)
                {
                  
                    $response['data']['term_report']          = $term_report;
                    $response['data']['class_test_report']    = $exam_data;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $response['status']['code']       = 404;
                    $response['status']['msg']        = "Data Not Found";
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
    
     public function actionAcknowledge() {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->request->getPost('school');
            $exam_id = Yii::app()->request->getPost('exam_id');

            if (empty($exam_id)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request.";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->user_secret === $user_secret) {

                $exam = new UserExamAcknowledge;
                $exam_data = $exam->acknowledgeExam($exam_id);

                if ($exam_data) {
                    $response['data']['exam_ack'] = $exam_data;
                } else {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "NO_EXAM_ACKNOWLEDGED.";
                }
            } else {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request.";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
}
