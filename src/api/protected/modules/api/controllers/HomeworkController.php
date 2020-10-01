<?php

class HomeworkController extends Controller
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
                'actions' => array('index', 'homeworkintelligence','totalclass','submittedlist','savemark','statuschange','singlesubmit','savecomments','comments','savecommentsstudent','commentsstudent','submit','submitdelete','defaulterList','delete','getdefaulterlist','adddefaulter','getsubjectstudents', 'teacherintelligence', 'Done', 'subjects', 'publishhomework', 'singleteacher', 'assessmentscore', 'singlehomework', 'saveassessment', 'assessment', 'getassessment', 'getproject', 'getsubject', 'addhomework', 'teacherhomework', 'homeworkstatus', 'teacherQuiz'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionDefaulterList()
    {
       $user_secret = Yii::app()->request->getPost('user_secret');
       $id = Yii::app()->request->getPost('id');
       if(Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $id)
       {
          $assingmentRegistrationObj = new AssignmentDefaulterRegistrations();
          $asignment_given = 0;
          $assignment_register = 0;
          $assignment_not_given = 0;
          $asregData = $assingmentRegistrationObj->findByAssignmentId($id);
          if($asregData)
          {
              $assignment_register = 1;
              $asignment_given = $asregData->assignment_given;
              $assignment_not_given = $asregData->assignment_not_given;
          }
          $deListObj = new AssignmentDefaulterLists();
          $deList = $deListObj->findAllByAssignmentId($id,$assignment_register,1);
          $response['data']['assignment_register'] = $assignment_register;
          $response['data']['assignment_not_given'] = $assignment_not_given;
          $response['data']['asignment_given'] = $asignment_given;
          $response['data']['d_list'] = $deList;
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
    public function actionGetDefaulterList()
    {
       $user_secret = Yii::app()->request->getPost('user_secret');
       $id = Yii::app()->request->getPost('id');
       if(Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $id)
       {
          $assingmentRegistrationObj = new AssignmentDefaulterRegistrations();
          $asignment_given = 0;
          $assignment_register = 0;
          $assignment_not_given = 0;
          $asregData = $assingmentRegistrationObj->findByAssignmentId($id);
          if($asregData)
          {
              $assignment_register = 1;
              $asignment_given = $asregData->assignment_given;
              $assignment_not_given = $asregData->assignment_not_given;
          }
          $deListObj = new AssignmentDefaulterLists();
          $deList = $deListObj->findAllByAssignmentId($id,$assignment_register);
          $response['data']['assignment_register'] = $assignment_register;
          $response['data']['assignment_not_given'] = $assignment_not_given;
          $response['data']['asignment_given'] = $asignment_given;
          $response['data']['d_list'] = $deList;
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
    public function actionAddDefaulter()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $id = Yii::app()->request->getPost('id');
        $student_id = Yii::app()->request->getPost('student_id');
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $id)
        {
            $assingmentRegistrationObj = new AssignmentDefaulterRegistrations();
            $asregData = $assingmentRegistrationObj->findByAssignmentId($id);
            $student_ids = [];
            if($student_id)
            {
                $student_ids = explode(",", $student_id);
                
            }
            AssignmentDefaulterLists::model()->deleteAll(
               "`assignment_id` =:assignment_id", array(':assignment_id' => $id)
            );
            $assignmentObj = new Assignments();
            $assData = $assignmentObj->findByPk($id);
            $total_std_ids = explode(",",$assData['student_list']);
            
            if($asregData)
            {
                $asregData->assignment_not_given = count($student_ids);
                $asregData->assignment_given = count($total_std_ids)-count($student_ids);
                $asregData->save();
            }
            else
            {
                $assingmentRegistrationObj->assignment_id = $id;
                $assingmentRegistrationObj->assignment_not_given = count($student_ids);
                $assingmentRegistrationObj->assignment_given = count($total_std_ids)-count($student_ids);
                $assingmentRegistrationObj->created_at = date("Y-m-d H:i:s");
                $assingmentRegistrationObj->updated_at = date("Y-m-d H:i:s");
                $assingmentRegistrationObj->employee_id = Yii::app()->user->profileId;
                $assingmentRegistrationObj->school_id = Yii::app()->user->schoolId;
                $assingmentRegistrationObj->save();
                
            }  
            $student_id_new = $student_ids;
            if($student_id_new)
            {
                $reminderrecipients = [];
                $batch_ids =[];
                $student_ids = [];
                
                
                $reminderrecipients2 = [];
                $batch_ids2 =[];
                $student_ids2 = [];
                
                $notification_ids = array();
                foreach($student_id_new as $std_id_h)
                {
                  
                    $assingmentList = new AssignmentDefaulterLists();
                    $assingmentList->assignment_id = $id;
                    $assingmentList->student_id = $std_id_h;
                    $assingmentList->save();
                    
                    $stdobj = new Students();
                    $studentsobj = $stdobj->findByPk($std_id_h);
                    if($studentsobj && $studentsobj->user_id)
                    {
                        $reminderrecipients[] = $studentsobj->user_id;
                        $batch_ids[$studentsobj->user_id] = $studentsobj->batch_id;
                        $student_ids[$studentsobj->user_id] = $studentsobj->id;

                        $gstudent = new GuardianStudent();
                        $all_g = $gstudent->getGuardians($studentsobj->id);

                        if ($all_g)
                        {
                            foreach ($all_g as $value)
                            {
                                $gr = new Guardians();
                                if (isset($value['guardian']) && isset($value['guardian']->id))
                                {
                                    $grdata = $gr->findByPk($value['guardian']->id);
                                    if($grdata && $grdata->user_id && !in_array($grdata->user_id, $reminderrecipients2))
                                    {
                                        $reminderrecipients2[] = $grdata->user_id;
                                        $batch_ids2[$grdata->user_id] = $studentsobj->batch_id;
                                        $student_ids2[$grdata->user_id] = $studentsobj->id;
                                    }
                                }
                            }
                        }
                    }
                    
                    
                }
              
                if($reminderrecipients)
                {
                    foreach ($reminderrecipients as $value)
                    {
                        $reminder = new Reminders();
                        $reminder->sender = Yii::app()->user->id;
                        $reminder->subject = "Homework Defaulter :" . $assData->title;
                        $reminder->body = "You did not submit the homework '" . $assData->title . "'";
                        $reminder->recipient = $value;
                        $reminder->school_id = Yii::app()->user->schoolId;
                        $reminder->rid = $assData->id;
                        $reminder->rtype = 4;
                        $reminder->batch_id = $batch_ids[$value];
                        $reminder->student_id = $student_ids[$value];
                        $reminder->created_at = date("Y-m-d H:i:s");
                        $reminder->updated_at = date("Y-m-d H:i:s");
                        $reminder->save();
                        $notification_ids[] = $reminder->id;
                    }
                }
                
                if($reminderrecipients2)
                {
                    foreach ($reminderrecipients2 as $value2)
                    {
                        $reminder = new Reminders();
                        $reminder->sender = Yii::app()->user->id;
                        $reminder->subject = "Homework Defaulter :" . $assData->title;
                        $reminder->body = "Your child did not submit the homework '" . $assData->title . "'";
                        $reminder->recipient = $value2;
                        $reminder->school_id = Yii::app()->user->schoolId;
                        $reminder->rid = $assData->id;
                        $reminder->rtype = 4;
                        $reminder->batch_id = $batch_ids2[$value2];
                        $reminder->student_id = $student_ids2[$value2];
                        $reminder->created_at = date("Y-m-d H:i:s");
                        $reminder->updated_at = date("Y-m-d H:i:s");
                        $reminder->save();
                        $notification_ids[] = $reminder->id;
                    }
                }

                if ($notification_ids)
                {
                    $notification_id = implode("*", $notification_ids);
                    $user_id = implode("*", $reminderrecipients);
                    //Settings::sendNotification($notification_id, $user_id);
                    //shell_exec("php pushnoti.php $notification_id $user_id  > /dev/null 2>/dev/null &");
                }
                
                
            }
           $response['data']['msg'] = "Successfully Saved";
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
    
    
  
    
    
    
    
    public function actionGetSubjectStudents()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $subject_id = Yii::app()->request->getPost('subject_id');
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $subject_id)
        { 
            $subjectObj = new Subjects();
            $sub_data = $subjectObj->findByPk($subject_id);
            $all_student = array();
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
            $response['data']['students'] = $all_student;
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

    public function actionTeacherIntelligence()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $date = Yii::app()->request->getPost('date');
        $start_date = Yii::app()->request->getPost('start_date');
        $department_id = Yii::app()->request->getPost('department_id');

        $sort_by = Yii::app()->request->getPost('sort_by');
        $sort_type = Yii::app()->request->getPost('sort_type');

        if (!$sort_by)
        {
            $sort_by = "homework_given";
        }
        if (!$sort_type)
        {
            $sort_type = "1";
        }

        




        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
            if (!$date)
            {
                $date = date("Y-m-d");
            }
            if (!$start_date)
            {
                $start_date = $date;
            }

            if (!$department_id)
            {
                $department_id = FALSE;
            }
            $attendence = new Attendances();

            $day_type = $attendence->check_date($date);


            $assignments = new Assignments();



            $employee_data = $assignments->getAssignmentEmployee($date, $sort_by, $sort_type, $start_date, $department_id,true);

            $response['data']['day_type'] = $day_type;
            $response['data']['employee_data'] = $employee_data;

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    public function actionTotalClass()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $date = Yii::app()->request->getPost('date');
        $batch_name = Yii::app()->request->getPost('batch_name');
        $class_name = Yii::app()->request->getPost('class_name');
        $batch_id = Yii::app()->request->getPost('batch_id');
        $type = "day";
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
            if (!$date or $date == "false")
            {
                $date = date("Y-m-d");
            }
            if (!$batch_name or $batch_name == "false")
            {
                $batch_name = FALSE;
            }
            if (!$class_name or $class_name == "")
            {
                $class_name = FALSE;
            }
            if (!$batch_id or $batch_id = 0)
            {
                $batch_id = FALSE;
            }
           
            $timetable = new TimetableEntries();
            $total_class = $timetable->getTotalClass($date, $batch_name, $class_name, $batch_id);
            $response['data']['total_class'] = $total_class;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end(); 
    }

    public function actionHomeworkIntelligence()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $date = Yii::app()->request->getPost('date');
        $batch_name = Yii::app()->request->getPost('batch_name');
        $class_name = Yii::app()->request->getPost('class_name');
        $batch_id = Yii::app()->request->getPost('batch_id');

        $number_of_day = Yii::app()->request->getPost('number_of_day');
        if (!$number_of_day)
        {
            $number_of_day = 10;
        }
        $type = Yii::app()->request->getPost('type');
        if (!$type)
        {
            $type = "days";
        }


        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
            if (!$date)
            {
                $date = date("Y-m-d");
            }
            if (!$batch_name)
            {
                $batch_name = FALSE;
            }
            if (!$class_name)
            {
                $class_name = FALSE;
            }
            if (!$batch_id)
            {
                $batch_id = FALSE;
            }
            $attendence = new Attendances();

            $day_type = $attendence->check_date($date);


            $assignments = new Assignments();
            $timetable = new TimetableEntries();
            $homework_given = 0;
            $total_class = 0;
            $frequency = "N/A";

            if ($day_type == "1")
            {
                $homework_given = $assignments->getAssignmentTotalAdmin($date, $batch_name, $class_name, $batch_id);
                $total_class = $timetable->getTotalClass($date, $batch_name, $class_name, $batch_id);
                if ($total_class > 0)
                {
                    $frequency = $homework_given / $total_class;
                    $frequency = number_format((float) $frequency, 2, '.', '');
                }
            }

            $graph_data = $assignments->getAssignmentGraph($number_of_day, $type, $batch_name, $class_name, $batch_id);

            $response['data']['day_type'] = $day_type;
            $response['data']['total_class'] = $total_class;
            $response['data']['homework_given'] = $homework_given;
            $response['data']['frequency'] = $frequency;

            $response['data']['att_graph'] = $graph_data[0];
            $response['data']['att_graph_date'] = $graph_data[1];

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionAssessmentScore()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $id = Yii::app()->request->getPost('id');
            $batch_id = Yii::app()->request->getPost('batch_id');
            $student_id = Yii::app()->request->getPost('student_id');
            $response = array();
            if ($id && Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || ($batch_id && $student_id)))
            {

                if (!$batch_id)
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                $assignment = new OnlineExamGroups();
                $homework_data = $assignment->getOnlineExamScore($id, $batch_id, $student_id);
                if ($homework_data)
                {
                    $response['data']['assesment'] = $homework_data;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                } else
                {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "Data Not Found";
                }
            } else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSaveAssessment()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $id = Yii::app()->request->getPost('id');
            $start_time = Yii::app()->request->getPost('start_time');
            $total_time = Yii::app()->request->getPost('total_time');
            $end_time = date("Y-m-d H:i:s", strtotime("+" . $total_time . " seconds", strtotime($start_time)));
            $total_score = Yii::app()->request->getPost('total_score');
            $is_passed = Yii::app()->request->getPost('is_passed');

            $answer_question = Yii::app()->request->getPost('answer_question');
            $answer_option = Yii::app()->request->getPost('answer_option');
            $answer_correct = Yii::app()->request->getPost('answer_correct');
            $response = array();
            if ($id && Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isStudent && $start_time && $total_time && $total_score !== false && $is_passed !== false && $answer_question && $answer_option && $answer_correct)
            {  
                $student_id = Yii::app()->user->profileId;
                 $school_id = Yii::app()->user->schoolId;
                $examAttendanc = new OnlineExamAttendances();
                $total = $examAttendanc->getAttendance($id, $student_id);
                if (count($answer_question) == count($answer_option) && count($answer_option) == count($answer_correct) && ( !$total or $total == 0 ) )
                {

                    

                    

                    $examAttendanc->online_exam_group_id = $id;
                    $examAttendanc->student_id = $student_id;
                    $examAttendanc->start_time = $start_time;
                    $examAttendanc->end_time = $end_time;
                    $examAttendanc->is_passed = $is_passed;
                    $examAttendanc->total_score = $total_score;
                    $examAttendanc->created_at = date("Y-m-d H:i:s");
                    $examAttendanc->updated_at = date("Y-m-d H:i:s");
                    $examAttendanc->school_id = $school_id;
                    $examAttendanc->from_mobile = 1;

                    $examAttendanc->save();
                    if (isset($examAttendanc->id) && $examAttendanc->id)
                    {
                        if ($answer_question)
                        {
                            foreach ($answer_question as $key => $value)
                            {

                                $objexamScore = new OnlineExamScoreDetails();
                                $objexamScore->online_exam_question_id = $value;
                                $objexamScore->online_exam_attendance_id = $examAttendanc->id;
                                $objexamScore->online_exam_option_id = $answer_option[$key];
                                $objexamScore->is_correct = $answer_correct[$key];
                                $objexamScore->school_id = $school_id;
                                $objexamScore->created_at = date("Y-m-d H:i:s");
                                $objexamScore->updated_at = date("Y-m-d H:i:s");
                                $objexamScore->save();
                            }
                        }


                        $response['status']['code'] = 200;
                        $response['status']['msg'] = "Data Found";
                    } else
                    {
                        $response['status']['code'] = 400;
                        $response['status']['msg'] = "Bad Request";
                    }
                } else
                {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request";
                }
            } else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetAssessment()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $id = Yii::app()->request->getPost('id');

            $response = array();
            if ($id && Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent))
            {


                $batch_id = Yii::app()->user->batchId;
                $student_id = Yii::app()->user->profileId;



                $assignment = new OnlineExamGroups();


                $homework_data = $assignment->getOnlineExam($id, $batch_id, $student_id);
                if ($homework_data)
                {
                    $robject = new Reminders();
                    $robject->ReadReminderNew(Yii::app()->user->id, 0, 15, $id);

                    $response['data']['current_date'] = date("Y-m-d H:i:s");
                    $response['data']['assesment'] = $homework_data;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                } else
                {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "Data Not Found";
                }
            } else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionAssessment()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $batch_id = Yii::app()->request->getPost('batch_id');
            $not_started = Yii::app()->request->getPost('not_started');
            $student_id = Yii::app()->request->getPost('student_id');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || ($batch_id && $student_id)))
            {
                if (Yii::app()->user->isStudent)
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }

                if ($not_started)
                {
                    $not_started = 1;
                } else
                {
                    $not_started = 0;
                }


                $page_number = Yii::app()->request->getPost('page_number');

                $page_size = Yii::app()->request->getPost('page_size');



                $subject_id = Yii::app()->request->getPost('subject_id');



                if (empty($page_number))
                {
                    $page_number = 1;
                }
                if (empty($page_size))
                {
                    $page_size = 50;
                }

                if (!$subject_id)
                {
                    $subject_id = 0;
                }


                $assignment = new OnlineExamGroups();


                $homework_data = $assignment->getOnlineExamList($batch_id, $student_id, $page_number, $page_size, "", $subject_id, $not_started);
                $response['data']['total'] = $assignment->getOnlineExamTotal($batch_id, $student_id, $subject_id, $not_started);
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size)
                {
                    $has_next = true;
                }
                $response['data']['has_next'] = $has_next;
                $response['data']['homework'] = $homework_data;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Found";
            } else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actiongetproject()
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
                } else
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                $assignment = new Assignments();

                $page_number = Yii::app()->request->getPost('page_number');

                $page_size = Yii::app()->request->getPost('page_size');

                $subject_id = Yii::app()->request->getPost('subject_id');

                if (empty($page_number))
                {
                    $page_number = 1;
                }
                if (empty($page_size))
                {
                    $page_size = 10;
                }

                if (empty($subject_id))
                {
                    $subject_id = NULL;
                }

                $homework_data = $assignment->getAssignment($batch_id, $student_id, "", $page_number, $subject_id, $page_size, 2);
                if ($homework_data)
                {

                    $response['data']['total'] = $assignment->getAssignmentTotal($batch_id, $student_id, "", $subject_id, 2);
                    $has_next = false;
                    if ($response['data']['total'] > $page_number * $page_size)
                    {
                        $has_next = true;
                    }
                    $response['data']['has_next'] = $has_next;
                    $response['data']['homework'] = $homework_data;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                } else
                {
                    $response['data']['total'] = 0;
                    $response['data']['has_next'] = false;
                    $response['data']['homework'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Not Found";
                }
            } else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSingleTeacher()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $employee_id = Yii::app()->user->profileId;
        $id = Yii::app()->request->getPost('id');
        if ($id && Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
        {
            $homework = new Assignments();
            $page_number = Yii::app()->request->getPost('page_number');
            $page_size = Yii::app()->request->getPost('page_size');
            if (empty($page_number))
            {
                $page_number = 1;
            }
            if (empty($page_size))
            {
                $page_size = 10;
            }
            $homework_data = $homework->getAssignmentTeacher($employee_id, $page_number, $page_size, 1, $id);
            if ($homework_data)
            {

                $attachments = Settings::attachmentUrlAssignment($id);
                $response['data']['homework'] = $homework_data[0];
                $response['data']['attachments'] = $attachments;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Found";
            } else
            {
                $response['data']['total'] = 0;
                $response['data']['has_next'] = false;
                $response['data']['homework'] = array();
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Not Found";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSingleHomework()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $id = Yii::app()->request->getPost('id');
            $response = array();
            if ($id && Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )))
            {
                if (Yii::app()->user->isParent)
                {
                    $batch_id = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                } else
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                $assignment = new Assignments();
                $homework_data = $assignment->getAssignment($batch_id, $student_id, "", 1, null, 1, 1, $id);
                if ($homework_data)
                {
                    $robject = new Reminders();
                    $robject->ReadReminderNew(Yii::app()->user->id, 0, 4, $id);
                    
                    $attachments = Settings::attachmentUrlAssignment($id);

                    $response['data']['homework'] = $homework_data[0];
                    $response['data']['attachments'] = $attachments;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                } else
                {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "Data Not Found";
                }
            } else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSubjects()
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
                } else
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }

                $subject = new Subjects();
                $all_subject = $subject->getSubject($batch_id, $student_id);
                $response['data']['subject'] = $all_subject;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Found";
            } else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionIndex()
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
                } else
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                $assignment = new Assignments();

                $page_number = Yii::app()->request->getPost('page_number');

                $page_size = Yii::app()->request->getPost('page_size');

                $subject_id = Yii::app()->request->getPost('subject_id');

                $duedate = Yii::app()->request->getPost('duedate');
                $call_from_web = Yii::app()->request->getPost('call_from_web');

                if (empty($page_number))
                {
                    $page_number = 1;
                }
                if (empty($page_size))
                {
                    $page_size = 10;
                }

                if (empty($subject_id))
                {
                    $subject_id = NULL;
                }
                if (!$duedate)
                {
                    $duedate = NULL;
                } else
                {
                    $duedate = date('Y-m-d', strtotime($duedate));
                }

                $homework_data = $assignment->getAssignment($batch_id, $student_id, "", $page_number, $subject_id, $page_size, 1, 0, $duedate, $call_from_web);
                if ($homework_data)
                {

                    $response['data']['total'] = $assignment->getAssignmentTotal($batch_id, $student_id, "", $subject_id, 1, $duedate, $call_from_web);
                    $has_next = false;
                    if ($response['data']['total'] > $page_number * $page_size)
                    {
                        $has_next = true;
                    }
                    $response['data']['has_next'] = $has_next;
                    $response['data']['homework'] = $homework_data;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                } else
                {
                    $response['data']['total'] = 0;
                    $response['data']['has_next'] = false;
                    $response['data']['homework'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Not Found";
                }
            } else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetSubject()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
        {
            $emplyee_subject = new Subjects();
            $subjects = $emplyee_subject->get_employee_subjects(Yii::app()->user->profileId);
            $response['data']['subjects'] = $subjects;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "EVENTS_FOUND";
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionHomeworkStatus()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $assignment_id = Yii::app()->request->getPost('assignment_id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $assignment_id)
        {
            $assignment_answer = new AssignmentAnswers();
            $homework_status = $assignment_answer->homeworkStatus($assignment_id);
            $response['data']['homework_status'] = $homework_status;
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

    public function actionTeacherHomework()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $draft = Yii::app()->request->getPost('draft');
        $employee_id = Yii::app()->user->profileId;
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
            $homework = new Assignments();
            $page_number = Yii::app()->request->getPost('page_number');
            $page_size = Yii::app()->request->getPost('page_size');
            $subject_id = Yii::app()->request->getPost('subject_id');
            $duedate = Yii::app()->request->getPost('duedate');

            if (!$duedate)
            {
                $duedate = NULL;
            }

            if (empty($page_number))
            {
                $page_number = 1;
            }
            if (empty($page_size))
            {
                $page_size = 10;
            }
            if (empty($subject_id))
            {
                $subject_id = NULL;
            }
            $is_published = 1;
            if ($draft)
            {
                $is_published = 0;
            }
            
            $homework_data = $homework->getAssignmentTeacher($employee_id, $page_number, $page_size, $is_published, 0, $subject_id, $duedate);
            if ($homework_data)
            {

                $response['data']['total'] = $homework->getAssignmentTotalTeacher($employee_id, $is_published, $subject_id, $duedate);
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size)
                {
                    $has_next = true;
                }
                $response['data']['has_next'] = $has_next;
                $response['data']['homework'] = $homework_data;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Found";
            } else
            {
                $response['data']['total'] = 0;
                $response['data']['has_next'] = false;
                $response['data']['homework'] = array();
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Not Found";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }
    private function checkHomeworkPublisher()
    {
        $configuration = new Configurations();
        $home_work_forward_config = (int)$configuration->getValue("HomeworkWillForwardOnly");
        $empObj = new Employees();
        $emp_data = $empObj->findByPk(Yii::app()->user->profileId);
        if($home_work_forward_config == 0 || ($emp_data && $emp_data->homework_publisher==1))
        {
            return true;
        }
        return false;
    }        

    public function actionPublishHomework()
    {
        $id = Yii::app()->request->getPost('id');
        $user_secret = Yii::app()->request->getPost('user_secret');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $id)
        {
            $homework = new Assignments();
            $ehomework = $homework->findByPk($id);
            if ($ehomework)
            {
                $ehomework->is_published = 1;
                $ehomework->created_at = date("Y-m-d H:i:s");
                
                if($this->checkHomeworkPublisher()==false)
                {
                    $ehomework->is_published = 2;
                } 
                
                $ehomework->save();
                
                if($ehomework->is_published == 1)
                {
                    $studentsubjectobj = new StudentsSubjects();

                    $subobj = new Subjects();
                    $subject_details = $subobj->findByPk($ehomework->subject_id);



                    $stdobj = new Students();

                    $students1 = $stdobj->getStudentByBatch($subject_details->batch_id);
                    $students2 = $studentsubjectobj->getSubjectStudent($ehomework->subject_id);
                    $students = array_unique(array_merge($students1, $students2));

                    $notification_ids = array();
                    $reminderrecipients = array();
                    foreach ($students as $value)
                    {
                        $studentsobj = $stdobj->findByPk($value);
                        $reminderrecipients[] = $studentsobj->user_id;
                        $batch_ids[$studentsobj->user_id] = $studentsobj->batch_id;
                        $student_ids[$studentsobj->user_id] = $studentsobj->id;

                        $gstudent = new GuardianStudent();
                        $all_g = $gstudent->getGuardians($studentsobj->id);

                        if ($all_g)
                        {
                            foreach ($all_g as $value)
                            {

                                $gr = new Guardians();
                                if (isset($value['guardian']) && isset($value['guardian']->id))
                                {
                                    $grdata = $gr->findByPk($value['guardian']->id);
                                    if ($grdata && $grdata->user_id && !in_array($grdata->user_id, $reminderrecipients))
                                    {
                                        $reminderrecipients[] = $grdata->user_id;
                                        $batch_ids[$grdata->user_id] = $studentsobj->batch_id;
                                        $student_ids[$grdata->user_id] = $studentsobj->id;
                                    }
                                }
                            }
                        }
                    }
                    $notification_ids = Settings::addReminderHomeworkClasswork($subject_details,$ehomework,"Homework");
                    foreach ($reminderrecipients as $value)
                    {
                        $reminder = new Reminders();
                        $reminder->sender = Yii::app()->user->id;
                        $reminder->subject = Settings::$HomeworkText . ":" . $ehomework->title;
                        $reminder->body = Settings::$HomeworkText . " Added for " . $subject_details->name . " Please check the homework For details";
                        $reminder->recipient = $value;
                        $reminder->school_id = Yii::app()->user->schoolId;
                        $reminder->rid = $ehomework->id;
                        $reminder->rtype = 4;
                        $reminder->batch_id = $batch_ids[$value];
                        $reminder->student_id = $student_ids[$value];
                        $reminder->created_at = date("Y-m-d H:i:s");
                        $reminder->updated_at = date("Y-m-d H:i:s");
                        $reminder->save();
                        $notification_ids[] = $reminder->id;
                    }

                    if ($notification_ids)
                    {
                        $notification_id = implode("*", $notification_ids);
                        $user_id = implode("*", $reminderrecipients);
                        //Settings::sendNotification($notification_id, $user_id);
                        //shell_exec("php pushnoti.php $notification_id $user_id  > /dev/null 2>/dev/null &");
                    }
                }
                $response['status']['code'] = 200;
                $response['status']['msg'] = "SUCCESS";
            } else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    
    private function upload_homework_ans($file, $homework)
    {
        $homework->attachment_updated_at = date("Y-m-d H:i:s");
        $homework->updated_at = date("Y-m-d H:i:s");

        $home_work_id = strlen($homework->assignment_id);
        
        $new_id = "";
        $diff = 9-$home_work_id;
        for($i = 0; $i<$diff; $i++)
        {
            $new_id = $new_id."0";
        }
        $new_id = $new_id."".$homework->assignment_id;
        
        $ass_ids = str_split($new_id, 3);
        
        $home_school_id = strlen($homework->school_id);
        
        $new_id = "";
        $diff = 9-$home_school_id;
        for($i = 0; $i<$diff; $i++)
        {
            $new_id = $new_id."0";
        }
        $new_id = $new_id."".$homework->school_id;
        
        $school_ids = str_split($new_id, 3);
        $file['attachment_file_name']['name'] = Settings::clean($file['attachment_file_name']['name']);

        $uploads_dir = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/assignment_answers/attachments/".$ass_ids[0]."/".$ass_ids[1]."/".$ass_ids[2]."/";
        
        $file_name = $file['attachment_file_name']['name'];
        $tmp_name = $file["attachment_file_name"]["tmp_name"];

        if (!is_dir($uploads_dir))
        {
            @mkdir($uploads_dir, 0777, true);
            
            $uploads_dir1 = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/";
            @chmod($uploads_dir1, 0777);
            $uploads_dir2 = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/assignment_answers/attachments/".$ass_ids[0]."/";
            @chmod($uploads_dir2, 0777);
            $uploads_dir3 = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/assignment_answers/attachments/".$ass_ids[0]."/".$ass_ids[1]."/";
            @chmod($uploads_dir3, 0777);
            @chmod($uploads_dir, 0777);
        }

        $uploads_dir = $uploads_dir . $file_name;
        

        if (@move_uploaded_file($tmp_name, "$uploads_dir"))
        {
            $homework->attachment_file_name = $file['attachment_file_name']['name'];
            if( !$homework->attachment_content_type )
            {
                if( strpos($homework->attachment_file_name, ".jpeg") !== false or strpos($homework->attachment_file_name, ".jpg") !== false )
                {
                    $homework->attachment_content_type = "image/jpeg";
                }
                if( strpos($homework->attachment_file_name, ".png") !== false )
                {
                    $homework->attachment_content_type = "image/png";
                }
            }
            $homework->save();
        }
        return $uploads_dir;
    }

    private function upload_homework($file, $homework)
    {
        $homework->attachment_updated_at = date("Y-m-d H:i:s");
        $homework->updated_at = date("Y-m-d H:i:s");

        $attachment_datetime_chunk = explode(" ", $homework->updated_at);

        $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
        $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

        $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
        $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];
        
        $file['attachment_file_name']['name'] = Settings::clean($file['attachment_file_name']['name']);

        $uploads_dir = Settings::$paid_image_path . "uploads/assignments/attachments/" . $homework->id . "/original/";
        $file_name = str_replace(" ", "+", $file['attachment_file_name']['name']) . "?" . $attachment_extra;
        $tmp_name = $file["attachment_file_name"]["tmp_name"];

        if (!is_dir($uploads_dir))
        {
            @mkdir($uploads_dir, 0777, true);
        }

        $uploads_dir = $uploads_dir . $file_name;


        if (move_uploaded_file($tmp_name, "$uploads_dir"))
        {
            $homework->attachment_file_name = $file['attachment_file_name']['name'];
            if( !$homework->attachment_content_type )
            {
                if( strpos($homework->attachment_file_name, ".jpeg") !== false or strpos($homework->attachment_file_name, ".jpg") !== false )
                {
                    $homework->attachment_content_type = "image/jpeg";
                }
                if( strpos($homework->attachment_file_name, ".png") !== false )
                {
                    $homework->attachment_content_type = "image/png";
                }
            }
            $homework->save();
        }
        return $uploads_dir;
    }

    private function copy_homework($origin, $file_name, $homework)
    {
        $homework->attachment_updated_at = date("Y-m-d H:i:s");
        $homework->updated_at = date("Y-m-d H:i:s");

        $attachment_datetime_chunk = explode(" ", $homework->updated_at);

        $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
        $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

        $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
        $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];
        
        $file_name = Settings::clean($file_name);

        $uploads_dir = Settings::$paid_image_path . "uploads/assignments/attachments/" . $homework->id . "/original/";
        $file_name_new = str_replace(" ", "+", $file_name) . "?" . $attachment_extra;


        if (!is_dir($uploads_dir))
        {
            @mkdir($uploads_dir, 0777, true);
        }

        $uploads_dir = $uploads_dir . $file_name_new;


        if (@copy($origin, "$uploads_dir"))
        {
            $homework->attachment_file_name = $file_name;
            if( !$homework->attachment_content_type )
            {
                if( strpos($file_name, ".jpeg") !== false or strpos($file_name, ".jpg") !== false )
                {
                    $homework->attachment_content_type = "image/jpeg";
                }
                if( strpos($file_name, ".png") !== false )
                {
                    $homework->attachment_content_type = "image/png";
                }
            }
            $homework->save();
        }
        return $uploads_dir;
    }
    public function actionDelete()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $id = Yii::app()->request->getPost('id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $id)
        {
            $assignments = new Assignments();
            $assignments_data = $assignments->findByPk($id);
            if( $assignments_data )
            {
                $assignments_data->delete();
            }
            $response['status']['code'] = 200;
            $response['status']['msg'] = "SUCCESS";
        } 
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }        

    public function actionAddHomework()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $subject_ids = Yii::app()->request->getPost('subject_id');
        $content = Yii::app()->request->getPost('content');
        $title = Yii::app()->request->getPost('title');
        $is_draft = Yii::app()->request->getPost('is_draft');
        $assignment_type = Yii::app()->request->getPost('type');
        $duedate = Yii::app()->request->getPost('duedate');
        $students_array = Yii::app()->request->getPost('students');
        $total_mark = Yii::app()->request->getPost('total_mark');
        $school_id = Yii::app()->user->schoolId;
        $id = Yii::app()->request->getPost('id');

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $subject_ids && $content && $title && $duedate && $school_id && $assignment_type)
        {
            if($students_array)
            {
                $students_per_subject = explode("|", $students_array);
            }

            $subject_id_array = explode(",", $subject_ids);
            if ($subject_id_array)
            {
                foreach ($subject_id_array as $key=>$subject_id)
                {
                    if ($subject_id)
                    {
                        $old_subject_id = 0;
                        if ($id)
                        {
                            $objhomework = new Assignments();
                            $homework = $objhomework->findByPk($id);
                            $old_subject_id = $homework->subject_id;
                        } else
                        {
                            $homework = new Assignments();
                        }

                        $homework->subject_id = $subject_id;
                        $homework->content = $content;
                        if($total_mark)
                        {
                         $homework->total_mark = $total_mark;
                        }
                        $homework->title = $title;
                        $homework->duedate = $duedate;
                        $homework->school_id = Yii::app()->user->schoolId;
                        $homework->employee_id = Yii::app()->user->profileId;
                        $homework->assignment_type = $assignment_type;


                        if ($is_draft)
                        {
                            $homework->is_published = 0;
                        }
                        else if($this->checkHomeworkPublisher()==false)
                        {
                            $homework->is_published = 2;
                        }   


                        $homework->created_at = date("Y-m-d H:i:s");
                        if (!$id)
                        {
                            $homework->updated_at = date("Y-m-d H:i:s");
                        }




                        $studentsubjectobj = new StudentsSubjects();

                        $subobj = new Subjects();
                        $subject_details = $subobj->findByPk($subject_id);
                        


                        
                        $stdobj = new Students();
                        
                        if($old_subject_id != $homework->subject_id)
                        {
                            if(isset($students_per_subject) && isset($students_per_subject[$key]))
                            {
                                $students = explode(",",$students_per_subject[$key]);
                            }
                            else
                            {
                                $students1 = $stdobj->getStudentByBatch($subject_details->batch_id);
                                $students2 = $studentsubjectobj->getSubjectStudent($subject_id);
                                $students = array_unique(array_merge($students1, $students2));
                            }
                            $homework->student_list = implode(",", $students);
                        }
                        else
                        {
                            $students = explode(",",$homework->student_list);
                        }    
                        $homework->save();

                        if (isset($_FILES['attachment_file_name']['name']) && !empty($_FILES['attachment_file_name']['name']))
                        {
                            if (!isset($file_name_main) && !isset($origin))
                            {
                                $file_name_main = $_FILES['attachment_file_name']['name'];

                                $homework->updated_at = date("Y-m-d H:i:s");
                                $homework->attachment_content_type = Yii::app()->request->getPost('mime_type');
                                $homework->attachment_file_size = Yii::app()->request->getPost('file_size');
                                $origin = $this->upload_homework($_FILES, $homework);
                            } 
                            else
                            {
                                $homework->updated_at = date("Y-m-d H:i:s");
                                $homework->attachment_content_type = Yii::app()->request->getPost('mime_type');
                                $homework->attachment_file_size = Yii::app()->request->getPost('file_size');
                                $origin = $this->copy_homework($origin, $file_name_main, $homework);
                            }
                        }
                        
                        if (isset($_FILES['attachment2_file_name']['name']) && !empty($_FILES['attachment2_file_name']['name']))
                        {
                            if (!isset($file_name_main2) && !isset($origin2))
                            {
                                $file_name_main2 = $_FILES['attachment2_file_name']['name'];
                                $homework->attachment2_content_type = Yii::app()->request->getPost('mime_type2');
                                $homework->attachment2_file_size = Yii::app()->request->getPost('file_size2');
                                $origin2 = $this->upload_homework2($_FILES, $homework);
                            } 
                            else
                            {
                                
                                $homework->attachment2_content_type = Yii::app()->request->getPost('mime_type2');
                                $homework->attachment2_file_size = Yii::app()->request->getPost('file_size2');
                                $origin = $this->copy_homework2($origin2, $file_name_main2, $homework);
                            }
                        }
                        
                        if (isset($_FILES['attachment3_file_name']['name']) && !empty($_FILES['attachment3_file_name']['name']))
                        {
                            if (!isset($file_name_main3) && !isset($origin3))
                            {
                                $file_name_main3 = $_FILES['attachment3_file_name']['name'];
                                $homework->attachment3_content_type = Yii::app()->request->getPost('mime_type3');
                                $homework->attachment3_file_size = Yii::app()->request->getPost('file_size3');
                                $origin3 = $this->upload_homework3($_FILES, $homework);
                            } 
                            else
                            {
                              
                                $homework->attachment3_content_type = Yii::app()->request->getPost('mime_type3');
                                $homework->attachment3_file_size = Yii::app()->request->getPost('file_size3');
                                $origin = $this->copy_homework3($origin3, $file_name_main3, $homework);
                            }
                        }



                        if ($homework->is_published == 1)
                        {
                            $notification_ids = array();
                            $reminderrecipients = array();
                            foreach ($students as $value)
                            {
                                $studentsobj = $stdobj->findByPk($value);
                                if(isset($studentsobj))
                                {
                                    $reminderrecipients[] = $studentsobj->user_id;
                                    $batch_ids[$studentsobj->user_id] = $studentsobj->batch_id;
                                    $student_ids[$studentsobj->user_id] = $studentsobj->id;

                                    $gstudent = new GuardianStudent();
                                    $all_g = $gstudent->getGuardians($studentsobj->id);

                                    if ($all_g)
                                    {
                                        foreach ($all_g as $value)
                                        {
                                            $gr = new Guardians();
                                            if (isset($value['guardian']) && isset($value['guardian']->id))
                                            {
                                                $grdata = $gr->findByPk($value['guardian']->id);
                                                if ($grdata && $grdata->user_id && !in_array($grdata->user_id, $reminderrecipients))
                                                {
                                                    $reminderrecipients[] = $grdata->user_id;
                                                    $batch_ids[$grdata->user_id] = $studentsobj->batch_id;
                                                    $student_ids[$grdata->user_id] = $studentsobj->id;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $notification_ids = Settings::addReminderHomeworkClasswork($subject_details,$homework,"Homework");
                            foreach ($reminderrecipients as $value)
                            {
                                $reminder = new Reminders();
                                $reminder->sender = Yii::app()->user->id;
                                $reminder->subject = Settings::$HomeworkText . ":" . $title;
                                $reminder->body = Settings::$HomeworkText . " Added for " . $subject_details->name . " Please check the homework For details";
                                $reminder->recipient = $value;
                                $reminder->school_id = Yii::app()->user->schoolId;
                                $reminder->rid = $homework->id;
                                $reminder->rtype = 4;
                                $reminder->batch_id = $batch_ids[$value];
                                $reminder->student_id = $student_ids[$value];
                                $reminder->created_at = date("Y-m-d H:i:s");
                                $reminder->updated_at = date("Y-m-d H:i:s");
                                $reminder->save();
                                $notification_ids[] = $reminder->id;
//                          Settings::sendCurlNotification($value, $reminder->id);
                            }
                            if ($notification_ids)
                            {
                                $notification_id = implode("*", $notification_ids);
                                $user_id = implode("*", $reminderrecipients);
                                //Settings::sendNotification($notification_id, $user_id);
                                //shell_exec("php pushnoti.php $notification_id $user_id  > /dev/null 2>/dev/null &");
                            }
                        }
                    }
                }
            }

            $response['status']['code'] = 200;
            $response['status']['msg'] = "SUCCESS";
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    public function actionsubmittedList()
    {
       $user_secret = Yii::app()->request->getPost('user_secret');
       $id = Yii::app()->request->getPost('id');
       if(Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $id)
       {
          $assignmentAnswersObj = new AssignmentAnswers();
          $submitted_list = $assignmentAnswersObj->submitted_list($id);
          $response['data']['submitted_list'] = $submitted_list;
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
    
    public function actionSaveComments()
    {
        $id = Yii::app()->request->getPost('id');
        $user_secret = Yii::app()->request->getPost('user_secret');
        $student_id = Yii::app()->request->getPost('student_id'); 
        $comment = Yii::app()->request->getPost('comment'); 
        if(Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $id && $student_id && $comment)
        {
            $assignmentCommentsObj = new AssignmentComments();
            $assignmentCommentsObj->assignment_id = $id;
            $assignmentCommentsObj->student_id = $student_id;
            $assignmentCommentsObj->content = $comment;
            $assignmentCommentsObj->author_id = Yii::app()->user->id;
            $assignmentCommentsObj->created_at = date('Y-m-d H:i:s', strtotime("-6 hours"));
            $assignmentCommentsObj->updated_at = date('Y-m-d H:i:s', strtotime("-6 hours"));
            $assignmentCommentsObj->school_id = Yii::app()->user->schoolId;
            $assignmentCommentsObj->insert();
            $stdobj = new Students();
            $studentsobj = $stdobj->findByPk($student_id);
            $notification_ids = array();
            $reminderrecipients = array();
            if($studentsobj)
            {
                $reminderrecipients[] = $studentsobj->user_id;
                $batch_ids[$studentsobj->user_id] = $studentsobj->batch_id;
                $student_ids[$studentsobj->user_id] = $studentsobj->id;
                foreach ($reminderrecipients as $value)
                {
                    $reminder = new Reminders();
                    $reminder->sender = Yii::app()->user->id;
                    $reminder->subject = "New Commment Added By Teacher";
                    $reminder->body = "New Commment Added By Teacher. Please check the homework For details";
                    $reminder->recipient = $value;
                    $reminder->school_id = Yii::app()->user->schoolId;
                    $reminder->rid = $id;
                    $reminder->rtype = 601;
                    $reminder->batch_id = $batch_ids[$value];
                    $reminder->student_id = $student_ids[$value];
                    $reminder->created_at = date("Y-m-d H:i:s");
                    $reminder->updated_at = date("Y-m-d H:i:s");
                    $reminder->save();
                    $notification_ids[] = $reminder->id;
                    $notification_id = implode("*", $notification_ids);
                    $user_id = implode("*", $reminderrecipients);
                    Settings::sendNotification($notification_id, $user_id);
                }
            }
            
            $response['data']['msg'] = "Success";
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


    public function actionComments()
    {
        $id = Yii::app()->request->getPost('id');
        $user_secret = Yii::app()->request->getPost('user_secret');
        $student_id = Yii::app()->request->getPost('student_id');  
        if(Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $id && $student_id)
        {
            $robject = new Reminders();
            $robject->ReadReminderNew(Yii::app()->user->id, 0, 602, $id);
            
            $assignment = new Assignments();
            $assignment_data = $assignment->findByPk($id);
            $subject = new Subjects();
            $subject_data = $subject->findByPk($assignment_data->subject_id);
            
            $student = new Students();
            $student_data = $student->findByPk($student_id);
            
            $free_user = new Freeusers();
            $user = new Users();
            $userdata = $user->findByPk($student_data->user_id);
            $response['data']['student_name'] = trim(str_replace("  "," ", $student_data->first_name." ".$student_data->middle_name." ".$student_data->last_name));
            $response['data']['roll'] = $student_data->class_roll_no;
            
            $response['data']['student_image'] = "";
            $response['data']['assignment_title'] = $assignment_data->title;
            $response['data']['subject'] = $subject_data->name;
            $free_user_id = $free_user->getFreeuserPaid($userdata->id,$userdata->school_id);
            if($free_user_id)
            {
                $response['data']['employee_image'] = Settings::getProfileImage($free_user_id);
            }
            
            $assignmentCommentsObj = new AssignmentComments();
            $comments = $assignmentCommentsObj->getComments($id,$student_id);
            $response['data']['comments'] = $comments;
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
    
    
    public function actionSaveCommentsStudent()
    {
        $id = Yii::app()->request->getPost('id');
        $user_secret = Yii::app()->request->getPost('user_secret');
        $comment = Yii::app()->request->getPost('comment'); 
        if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isStudent && $id && $comment)
        {
            $student_id = Yii::app()->user->profileId;
            $assignmentCommentsObj = new AssignmentComments();
            $assignmentCommentsObj->assignment_id = $id;
            $assignmentCommentsObj->student_id = $student_id;
            $assignmentCommentsObj->content = $comment;
            $assignmentCommentsObj->author_id = Yii::app()->user->id;
            $assignmentCommentsObj->created_at = date('Y-m-d H:i:s', strtotime("-6 hours"));
            $assignmentCommentsObj->updated_at = date('Y-m-d H:i:s', strtotime("-6 hours"));
            $assignmentCommentsObj->school_id = Yii::app()->user->schoolId;
            $assignmentCommentsObj->insert();
            
            $assignmentObj = new Assignments();
            $assignment = $assignmentObj->findByPk($id);
            $employee_id = $assignment->employee_id;
            $employeeOj = new Employees();
            $employee = $employeeOj->findByPk($employee_id);
            $notification_ids = array();
            $reminderrecipients = array();
            if($employee)
            {
                $reminderrecipients[] = $employee->user_id;
                $stdObj = new Students();
                $std_details = $stdObj->findByPk($student_id);
                $batch_id = $std_details->batch_id;
                foreach ($reminderrecipients as $value)
                {
                    $reminder = new Reminders();
                    $reminder->sender = Yii::app()->user->id;
                    $reminder->subject = "New Commment Added By student in homework";
                    $reminder->body = "New Commment added by student in homework Please check the homework For details";
                    $reminder->recipient = $value;
                    $reminder->school_id = Yii::app()->user->schoolId;
                    $reminder->rid = $id;
                    $reminder->rtype = 602;
                    $reminder->batch_id = $batch_id;
                    $reminder->student_id = $student_id;
                    $reminder->created_at = date("Y-m-d H:i:s");
                    $reminder->updated_at = date("Y-m-d H:i:s");
                    $reminder->save();
                    $notification_ids[] = $reminder->id;
                    $notification_id = implode("*", $notification_ids);
                    $user_id = implode("*", $reminderrecipients);
                    Settings::sendNotification($notification_id, $user_id);
                }
            }    
            $response['data']['msg'] = "Success";
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


    public function actionCommentsStudent()
    {
        $id = Yii::app()->request->getPost('id');
        $user_secret = Yii::app()->request->getPost('user_secret'); 
        if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isStudent && $id )
        {
            $robject = new Reminders();
            $robject->ReadReminderNew(Yii::app()->user->id, 0, 601, $id);
            $assignment = new Assignments();
            $assignment_data = $assignment->findByPk($id);
            $subject = new Subjects();
            $subject_data = $subject->findByPk($assignment_data->subject_id);
            $employeeObj = new Employees();
            $employee_data = $employeeObj->findByPk($assignment_data->employee_id);
            $free_user = new Freeusers();
            $user = new Users();
            $userdata = $user->findByPk($employee_data->user_id);
            $response['data']['employee_name'] = trim(str_replace("  "," ", $employee_data->first_name." ".$employee_data->middle_name." ".$employee_data->last_name));
            $student_id = Yii::app()->user->profileId;
            $assignmentCommentsObj = new AssignmentComments();
            $comments = $assignmentCommentsObj->getComments($id,$student_id);
            $response['data']['employee_image'] = "";
            $response['data']['assignment_title'] = $assignment_data->title;
            $response['data']['subject'] = $subject_data->name;
            $free_user_id = $free_user->getFreeuserPaid($userdata->id,$userdata->school_id);
            if($free_user_id)
            {
                $response['data']['employee_image'] = Settings::getProfileImage($free_user_id);
            }
            
            $response['data']['comments'] = $comments;
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
    
    
    public function actionSaveMark()
    {
        $id = Yii::app()->request->getPost('id');
        $user_secret = Yii::app()->request->getPost('user_secret');
        $mark = Yii::app()->request->getPost('mark');
        if(Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $id && $mark)
        {
            $assignmentAnswersObj = new AssignmentAnswers();
            $answer = $assignmentAnswersObj->findByPk($id);
            if( $answer ) 
            {
                $answer->mark = $mark;
                $answer->save();
            }
            $response['data']['msg'] = "Success";
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
    
    public function actionstatusChange()
    {
        $id = Yii::app()->request->getPost('id');
        $user_secret = Yii::app()->request->getPost('user_secret');
        $status = Yii::app()->request->getPost('status');
        if(Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $id && $status)
        {
            $assignmentAnswersObj = new AssignmentAnswers();
            $answer = $assignmentAnswersObj->findByPk($id);
            if( $answer ) 
            {
                $answer->status = $status;
                $answer->save();
            }
            $response['data']['msg'] = "Success";
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
    public function actionsingleSubmit()
    {
       $user_secret = Yii::app()->request->getPost('user_secret');
       $id = Yii::app()->request->getPost('id');
       $student_id = Yii::app()->request->getPost('student_id');
       if(Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin || Yii::app()->user->isStudent) && $id && $student_id)
       {
          $assignmentAnswersObj = new AssignmentAnswers();
          $submitted = $assignmentAnswersObj->single_submit($id,$student_id);
          $attachments = Settings::attachmentUrl($submitted->id);
          $response['data']['submit_data'] = $submitted;
          $response['data']['attachments'] = $attachments;
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
    public function actionsubmitDelete()
    {
       $user_secret = Yii::app()->request->getPost('user_secret');
       $id = Yii::app()->request->getPost('id');
       $student_id = Yii::app()->request->getPost('student_id');
       if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $id && $student_id)
       {
          $assignmentAnswersObj = new AssignmentAnswers();
          $assignmentAnswersObj->delete_all_answer($id,$student_id);
          
          $response['status']['code'] = 200;
          $response['status']['msg'] = "Successfuly Deleted";
          
       }
       else
       {
           $response['status']['code'] = 400;
           $response['status']['msg'] = "Bad Request";
       }  
       echo CJSON::encode($response);
       Yii::app()->end();
    }
    
    public function actionSubmit()
    {
       if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $assignment_id = Yii::app()->request->getPost('assignment_id');
            $title = Yii::app()->request->getPost('title');
            $content = Yii::app()->request->getPost('content');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && $assignment_id != "" && $title && Yii::app()->user->isStudent)
            {
                $assignment_answer = new AssignmentAnswers();
                $assignment_answer->assignment_id = $assignment_id;
                $assignment_answer->student_id = Yii::app()->user->profileId;
                $assignment_answer->title = $title;
                $assignment_answer->content = $content;
                $assignment_answer->created_at = date("Y-m-d H:i:s");
                $assignment_answer->updated_at = date("Y-m-d H:i:s");
                $assignment_answer->school_id = Yii::app()->user->schoolId;
                $assignment_answer->insert();
                
                if (isset($_FILES['attachment_file_name']['name']) && !empty($_FILES['attachment_file_name']['name']))
                {

                    $file_name_main = $_FILES['attachment_file_name']['name'];
                    $assignment_answer->updated_at = date("Y-m-d H:i:s");
                    $assignment_answer->attachment_content_type = Yii::app()->request->getPost('mime_type');
                    $assignment_answer->attachment_file_size = Yii::app()->request->getPost('file_size');
                    $this->upload_homework_ans($_FILES, $assignment_answer);

                }
                
                if (isset($_FILES['attachment2_file_name']['name']) && !empty($_FILES['attachment2_file_name']['name']))
                {

                    $file_name_main = $_FILES['attachment2_file_name']['name'];
                    $assignment_answer->attachment2_content_type = Yii::app()->request->getPost('mime_type');
                    $assignment_answer->attachment2_file_size = Yii::app()->request->getPost('file_size');
                    $this->upload_homework_ans2($_FILES, $assignment_answer);

                }
                
                if (isset($_FILES['attachment3_file_name']['name']) && !empty($_FILES['attachment3_file_name']['name']))
                {

                    $file_name_main = $_FILES['attachment3_file_name']['name'];
                    $assignment_answer->attachment3_content_type = Yii::app()->request->getPost('mime_type3');
                    $assignment_answer->attachment3_file_size = Yii::app()->request->getPost('file_size3');
                    $this->upload_homework_ans3($_FILES, $assignment_answer);

                }
                
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Successfully Saved";
                //}
            } else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end(); 
    }
    public function actionDone()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $assignment_id = Yii::app()->request->getPost('assignment_id');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && $assignment_id != "" && Yii::app()->user->isStudent)
            {
                $assignment_answer = new AssignmentAnswers();
                $assignment_answer->assignment_id = $assignment_id;
                $assignment_answer->student_id = Yii::app()->user->profileId;
                $assignment_answer->title = "Done";
                $assignment_answer->content = "Homework Submitted";
                $assignment_answer->status = "ACCEPTED";
                $assignment_answer->created_at = date("Y-m-d H:i:s");
                $assignment_answer->school_id = Yii::app()->user->schoolId;
                $assignment_answer->insert();
                $assignment = new Assignments();

                //$homework_data = $assignment->getAssignment(Yii::app()->user->batchId, Yii::app()->user->profileId);
                //if ($homework_data)
                //{


                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Found";
                //}
            } else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionTeacherQuiz()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $page_number = Yii::app()->request->getPost('page_number');
            $page_size = Yii::app()->request->getPost('page_size');
            $subject_id = Yii::app()->request->getPost('subject_id');

            if (empty($page_number))
            {
                $page_number = 1;
            }

            if (empty($page_size))
            {
                $page_size = 10;
            }

            if (empty($subject_id))
            {
                $subject_id = 0;
            }

            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
            {

                $mod_timetable_entries = TimetableEntries::model()->findAllByAttributes(array('employee_id' => Yii::app()->user->profileId), array('select' => 'subject_id', 'group' => 'batch_id'));

                $subject_ids = array();

                if ($mod_timetable_entries)
                    foreach ($mod_timetable_entries as $te)
                    {
                        $subject_ids[] = $te->subject_id;
                    }

                $mod_online_exam = new OnlineExamGroups();
                $online_exams = $mod_online_exam->getOnlineExamListTeacher($page_number, $page_size, $subject_ids);

                $response['data']['total'] = $mod_online_exam->getOnlineExamListTeacher($page_number, $page_size, $subject_ids, TRUE);
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size)
                {
                    $has_next = true;
                }

                $response['data']['has_next'] = $has_next;
                $response['data']['teacher_quiz'] = $online_exams;
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
    }
    
    
    private function upload_homework_ans3($file, $homework)
    {
        $homework->attachment3_updated_at = date("Y-m-d H:i:s");

        $home_work_id = strlen($homework->assignment_id);
        
        $new_id = "";
        $diff = 9-$home_work_id;
        for($i = 0; $i<$diff; $i++)
        {
            $new_id = $new_id."0";
        }
        $new_id = $new_id."".$homework->assignment_id;
        
        $ass_ids = str_split($new_id, 3);
        
        $home_school_id = strlen($homework->school_id);
        
        $new_id = "";
        $diff = 9-$home_school_id;
        for($i = 0; $i<$diff; $i++)
        {
            $new_id = $new_id."0";
        }
        $new_id = $new_id."".$homework->school_id;
        
        $school_ids = str_split($new_id, 3);

        $uploads_dir = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/assignment_answers/attachment3s/".$ass_ids[0]."/".$ass_ids[1]."/".$ass_ids[2]."/attach3/";
        
        $file_name = $file['attachment3_file_name']['name'];
        $tmp_name = $file["attachment3_file_name"]["tmp_name"];

        if (!is_dir($uploads_dir))
        {
            @mkdir($uploads_dir, 0777, true);
            
            $uploads_dir1 = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/";
            @chmod($uploads_dir1, 0777);
            $uploads_dir2 = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/assignment_answers/attachment3s/".$ass_ids[0]."/";
            @chmod($uploads_dir2, 0777);
            $uploads_dir3 = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/assignment_answers/attachment3s/".$ass_ids[0]."/".$ass_ids[1]."/";
            @chmod($uploads_dir3, 0777);
            $uploads_dir4 = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/assignment_answers/attachment3s/".$ass_ids[0]."/".$ass_ids[1]."/attach3/";
            @chmod($uploads_dir4, 0777);
            @chmod($uploads_dir, 0777);
        }

        $uploads_dir = $uploads_dir . $file_name;
        

        if (@move_uploaded_file($tmp_name, "$uploads_dir"))
        {
            $homework->attachment3_file_name = $file['attachment3_file_name']['name'];
            $homework->save();
        }
        return $uploads_dir;
    }
    
    
    private function upload_homework_ans2($file, $homework)
    {
        $homework->attachment2_updated_at = date("Y-m-d H:i:s");

        $home_work_id = strlen($homework->assignment_id);
        
        $new_id = "";
        $diff = 9-$home_work_id;
        for($i = 0; $i<$diff; $i++)
        {
            $new_id = $new_id."0";
        }
        $new_id = $new_id."".$homework->assignment_id;
        
        $ass_ids = str_split($new_id, 3);
        
        $home_school_id = strlen($homework->school_id);
        
        $new_id = "";
        $diff = 9-$home_school_id;
        for($i = 0; $i<$diff; $i++)
        {
            $new_id = $new_id."0";
        }
        $new_id = $new_id."".$homework->school_id;
        
        $school_ids = str_split($new_id, 3);

        $uploads_dir = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/assignment_answers/attachment2s/".$ass_ids[0]."/".$ass_ids[1]."/".$ass_ids[2]."/attach2/";
        
        $file_name = $file['attachment2_file_name']['name'];
        $tmp_name = $file["attachment2_file_name"]["tmp_name"];

        if (!is_dir($uploads_dir))
        {
            @mkdir($uploads_dir, 0777, true);
            
            $uploads_dir1 = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/";
            @chmod($uploads_dir1, 0777);
            $uploads_dir2 = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/assignment_answers/attachment2s/".$ass_ids[0]."/";
            @chmod($uploads_dir2, 0777);
            $uploads_dir3 = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/assignment_answers/attachment2s/".$ass_ids[0]."/".$ass_ids[1]."/";
            @chmod($uploads_dir3, 0777);
            $uploads_dir4 = Settings::$paid_image_path . "uploads/".$school_ids[0]."/".$school_ids[1]."/".$school_ids[2]."/assignment_answers/attachment2s/".$ass_ids[0]."/".$ass_ids[1]."/attach2/";
            @chmod($uploads_dir4, 0777);
            @chmod($uploads_dir, 0777);
        }

        $uploads_dir = $uploads_dir . $file_name;
        

        if (@move_uploaded_file($tmp_name, "$uploads_dir"))
        {
            $homework->attachment2_file_name = $file['attachment2_file_name']['name'];
            $homework->save();
        }
        return $uploads_dir;
    }

    private function upload_homework2($file, $homework)
    {
        
        if( !$homework->updated_at )
        {
            $homework->updated_at = date("Y-m-d H:i:s");
        }
        $attachment_datetime_chunk = explode(" ", $homework->updated_at);

        $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
        $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

        $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
        $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];

        $uploads_dir = Settings::$paid_image_path . "uploads/assignments/attachment2s/" . $homework->id . "/original/";
        $file_name = str_replace(" ", "+", $file['attachment2_file_name']['name']) . "?" . $attachment_extra;
        $tmp_name = $file["attachment2_file_name"]["tmp_name"];

        if (!is_dir($uploads_dir))
        {
            $uploads_dir_main = Settings::$paid_image_path . "uploads/assignments/";
            @chmod($uploads_dir_main, 0777);
            mkdir($uploads_dir, 0777, true);
            $uploads_dir1 = Settings::$paid_image_path . "uploads/assignments/attachment2s/";
            @chmod($uploads_dir1, 0777);
            $uploads_dir2 = Settings::$paid_image_path . "uploads/assignments/attachment2s/" . $homework->id . "/";
            @chmod($uploads_dir2, 0777);
            @chmod($uploads_dir, 0777);
            
        }

        $uploads_dir = $uploads_dir . $file_name;


        if (@move_uploaded_file($tmp_name, "$uploads_dir"))
        {
            $homework->attachment2_file_name = $file['attachment2_file_name']['name'];
            $homework->save();
        }
        return $uploads_dir;
    }

    private function copy_homework2($origin, $file_name, $homework)
    {
        if( !$homework->updated_at )
        {
            $homework->updated_at = date("Y-m-d H:i:s");
        }

        $attachment_datetime_chunk = explode(" ", $homework->updated_at);

        $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
        $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

        $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
        $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];

        $uploads_dir = Settings::$paid_image_path . "uploads/assignments/attachment2s/" . $homework->id . "/original/";
        $file_name_new = str_replace(" ", "+", $file_name) . "?" . $attachment_extra;


        if (!is_dir($uploads_dir))
        {
            $uploads_dir_main = Settings::$paid_image_path . "uploads/assignments/";
            @chmod($uploads_dir_main, 0777);
            @mkdir($uploads_dir, 0777, true);
            $uploads_dir1 = Settings::$paid_image_path . "uploads/assignments/attachment2s/";
            @chmod($uploads_dir1, 0777);
            $uploads_dir2 = Settings::$paid_image_path . "uploads/assignments/attachment2s/" . $homework->id . "/";
            @chmod($uploads_dir2, 0777);
            @chmod($uploads_dir, 0777);
        }

        $uploads_dir = $uploads_dir . $file_name_new;


        if (@copy($origin, "$uploads_dir"))
        {
            $homework->attachment2_file_name = $file_name;
            $homework->save();
        }
        return $uploads_dir;
    }
    
    private function upload_homework3($file, $homework)
    {
        
        if( !$homework->updated_at )
        {
            $homework->updated_at = date("Y-m-d H:i:s");
        }
        $attachment_datetime_chunk = explode(" ", $homework->updated_at);

        $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
        $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

        $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
        $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];

        $uploads_dir = Settings::$paid_image_path . "uploads/assignments/attachment3s/" . $homework->id . "/original/";
        $file_name = str_replace(" ", "+", $file['attachment3_file_name']['name']) . "?" . $attachment_extra;
        $tmp_name = $file["attachment3_file_name"]["tmp_name"];

        if (!is_dir($uploads_dir))
        {
            $uploads_dir_main = Settings::$paid_image_path . "uploads/assignments/";
            @chmod($uploads_dir_main, 0777);
            @mkdir($uploads_dir, 0777, true);
            $uploads_dir1 = Settings::$paid_image_path . "uploads/assignments/attachment3s/";
            @chmod($uploads_dir1, 0777);
            $uploads_dir2 = Settings::$paid_image_path . "uploads/assignments/attachment3s/" . $homework->id . "/";
            @chmod($uploads_dir2, 0777);
            @chmod($uploads_dir, 0777);
        }

        $uploads_dir = $uploads_dir . $file_name;


        if (@move_uploaded_file($tmp_name, "$uploads_dir"))
        {
            $homework->attachment3_file_name = $file['attachment3_file_name']['name'];
            $homework->save();
        }
        return $uploads_dir;
    }

    private function copy_homework3($origin, $file_name, $homework)
    {
        if( !$homework->updated_at )
        {
            $homework->updated_at = date("Y-m-d H:i:s");
        }

        $attachment_datetime_chunk = explode(" ", $homework->updated_at);

        $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
        $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

        $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
        $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];

        $uploads_dir = Settings::$paid_image_path . "uploads/assignments/attachment3s/" . $homework->id . "/original/";
        $file_name_new = str_replace(" ", "+", $file_name) . "?" . $attachment_extra;


        if (!is_dir($uploads_dir))
        {
            $uploads_dir_main = Settings::$paid_image_path . "uploads/assignments/";
            @chmod($uploads_dir_main, 0777);
            @mkdir($uploads_dir, 0777, true);
            $uploads_dir1 = Settings::$paid_image_path . "uploads/assignments/attachment3s/";
            @chmod($uploads_dir1, 0777);
            $uploads_dir2 = Settings::$paid_image_path . "uploads/assignments/attachment3s/" . $homework->id . "/";
            @chmod($uploads_dir2, 0777);
            @chmod($uploads_dir, 0777);
        }

        $uploads_dir = $uploads_dir . $file_name_new;


        if (@copy($origin, "$uploads_dir"))
        {
            $homework->attachment3_file_name = $file_name;
            $homework->save();
        }
        return $uploads_dir;
    }


}
