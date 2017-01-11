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
                'actions' => array('index','homeworkintelligence','teacherintelligence', 'Done','subjects','publishhomework','singleteacher','assessmentscore','singlehomework', 'saveassessment', 'assessment', 'getassessment', 'getproject', 'getsubject', 'addhomework', 'teacherhomework', 'homeworkstatus', 'teacherQuiz'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionTeacherIntelligence()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $date = Yii::app()->request->getPost('date');
        $department_id = Yii::app()->request->getPost('department_id');
        
        $sort_by = Yii::app()->request->getPost('sort_by');
        $sort_type = Yii::app()->request->getPost('sort_type');
        $time_range = Yii::app()->request->getPost('time_range');
        
        if(!$sort_by)
        {
            $sort_by = "frequency";
        }
        if(!$sort_type)
        {
            $sort_type = "1";
        }
        
        if(!$time_range)
        {
            $time_range = "day";
        }
        
        
        
       
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
            if (!$date)
            {
                $date = date("Y-m-d");
            }
            
            if(!$department_id)
            {
                $department_id = FALSE;
            }
            $attendence = new Attendances();
            
            $day_type = $attendence->check_date($date);
            
            
            $assignments = new Assignments();
            
            
            
            $employee_data = $assignments->getAssignmentEmployee($date,$sort_by,$sort_type,$time_range,$department_id);

            $response['data']['day_type'] = $day_type;
            $response['data']['employee_data'] = $employee_data;

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
    public function actionHomeworkIntelligence()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $date = Yii::app()->request->getPost('date');
        $batch_name = Yii::app()->request->getPost('batch_name');
        $class_name = Yii::app()->request->getPost('class_name');
        $batch_id = Yii::app()->request->getPost('batch_id');
        
        $number_of_day = Yii::app()->request->getPost('number_of_day');
        if(!$number_of_day)
        {
            $number_of_day = 10;
        } 
        $type = Yii::app()->request->getPost('type');
        if(!$type)
        {
            $type = "days";
        }
        
       
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
            if (!$date)
            {
                $date = date("Y-m-d");
            }
            if(!$batch_name)
            {
                $batch_name = FALSE;
            }
            if(!$class_name)
            {
                $class_name = FALSE;
            }
            if(!$batch_id)
            {
                $batch_id = FALSE;
            }
            $attendence = new Attendances();
            
            $day_type = $attendence->check_date($date);
            
            
            $assignments = new Assignments();
            $timetable = new TimetableEntries();
            $homework_given= 0;
            $total_class = 0;
            $frequency = "N/A";
            
            if($day_type=="1")
            {
                $homework_given = $assignments->getAssignmentTotalAdmin($date,$batch_name,$class_name,$batch_id);
                $total_class = $timetable->getTotalClass($date,$batch_name,$class_name,$batch_id);
                if($homework_given>0)
                {
                    $frequency = round($total_class/$homework_given);
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
        }
        else
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

                if(!$batch_id)
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
                }
                else
                {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "Data Not Found";
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

                if (count($answer_question) == count($answer_option) && count($answer_option) == count($answer_correct))
                {

                    $student_id = Yii::app()->user->profileId;
                    $school_id = Yii::app()->user->schoolId;

                    $examAttendanc = new OnlineExamAttendances();

                    $examAttendanc->online_exam_group_id = $id;
                    $examAttendanc->student_id = $student_id;
                    $examAttendanc->start_time = $start_time;
                    $examAttendanc->end_time = $end_time;
                    $examAttendanc->is_passed = $is_passed;
                    $examAttendanc->total_score = $total_score;
                    $examAttendanc->created_at = date("Y-m-d H:i:s");
                    $examAttendanc->updated_at = date("Y-m-d H:i:s");
                    $examAttendanc->school_id = $school_id;

                    $examAttendanc->save();
                    if (isset($examAttendanc->id) && $examAttendanc->id)
                    {
                        if ($answer_question)
                        {
                            foreach ($answer_question as $key=>$value)
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
                    $robject->ReadReminderNew(Yii::app()->user->id, 0 ,15, $id);
                    
                    $response['data']['current_date'] = date("Y-m-d H:i:s");
                    $response['data']['assesment'] = $homework_data;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                }
                else
                {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "Data Not Found";
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
                if(Yii::app()->user->isStudent)
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                
                if($not_started)
                {
                    $not_started = 1;
                }
                else 
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
                    $page_size = 10;
                }
                
                if (!$subject_id)
                {
                    $subject_id = 0;
                }


                $assignment = new OnlineExamGroups();


                $homework_data = $assignment->getOnlineExamList($batch_id, $student_id,$page_number,$page_size,"",$subject_id,$not_started);
                $response['data']['total'] = $assignment->getOnlineExamTotal($batch_id, $student_id,$subject_id,$not_started);
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size)
                {
                    $has_next = true;
                }
                $response['data']['has_next'] = $has_next;
                $response['data']['homework'] = $homework_data;
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
                }
                else
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
                }
                else
                {
                    $response['data']['total'] = 0;
                    $response['data']['has_next'] = false;
                    $response['data']['homework'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Not Found";
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
            $homework_data = $homework->getAssignmentTeacher($employee_id, $page_number, $page_size,1, $id);
            if ($homework_data)
            {

                $response['data']['homework'] = $homework_data[0];
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Found";
            }
            else
            {
                $response['data']['total'] = 0;
                $response['data']['has_next'] = false;
                $response['data']['homework'] = array();
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Not Found";
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
                }
                else
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                $assignment = new Assignments();
                $homework_data = $assignment->getAssignment($batch_id, $student_id, "", 1, null, 1, 1,$id);
                if ($homework_data)
                {
                    $robject = new Reminders();
                    $robject->ReadReminderNew(Yii::app()->user->id, 0 ,4, $id);
                    
                    $response['data']['homework'] = $homework_data[0];
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                }
                else
                {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "Data Not Found";
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
                }
                else
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                $assignment = new Assignments();

                $page_number = Yii::app()->request->getPost('page_number');

                $page_size = Yii::app()->request->getPost('page_size');

                $subject_id = Yii::app()->request->getPost('subject_id');
                
                $duedate = Yii::app()->request->getPost('duedate');

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
                } else {
                    $duedate = date('Y-m-d', strtotime($duedate));
                }
                
                $homework_data = $assignment->getAssignment($batch_id, $student_id, "", $page_number, $subject_id, $page_size, 1, 0,$duedate);
                if ($homework_data)
                {

                    $response['data']['total'] = $assignment->getAssignmentTotal($batch_id, $student_id, "", $subject_id, 1, $duedate);
                    $has_next = false;
                    if ($response['data']['total'] > $page_number * $page_size)
                    {
                        $has_next = true;
                    }
                    $response['data']['has_next'] = $has_next;
                    $response['data']['homework'] = $homework_data;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                }
                else
                {
                    $response['data']['total'] = 0;
                    $response['data']['has_next'] = false;
                    $response['data']['homework'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Not Found";
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
        $user_secret = Yii::app()->request->getPost('user_secret');

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
        {
            $emplyee_subject = new EmployeesSubjects();
            $subjects = $emplyee_subject->getSubject(Yii::app()->user->profileId);
            $response['data']['subjects'] = $subjects;
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
        }
        else
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
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) )
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
            if($draft)
            {
                $is_published = 0;
            }    
            $homework_data = $homework->getAssignmentTeacher($employee_id, $page_number, $page_size,$is_published,0,$subject_id,$duedate);
            if ($homework_data)
            {

                $response['data']['total'] = $homework->getAssignmentTotalTeacher($employee_id,$is_published,$subject_id,$duedate);
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size)
                {
                    $has_next = true;
                }
                $response['data']['has_next'] = $has_next;
                $response['data']['homework'] = $homework_data;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Found";
            }
            else
            {
                $response['data']['total'] = 0;
                $response['data']['has_next'] = false;
                $response['data']['homework'] = array();
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Not Found";
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
    
    public function actionPublishHomework()
    {
       $id = Yii::app()->request->getPost('id'); 
       $user_secret = Yii::app()->request->getPost('user_secret');
       if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $id)
       {
           $homework = new Assignments();
           $ehomework = $homework->findByPk($id);
           if($ehomework)
           {
                $ehomework->is_published = 1; 
                $ehomework->created_at = date("Y-m-d H:i:s"); 
                $ehomework->save();
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
                        foreach($all_g as $value)
                        {
                            $gr = new Guardians();
                            $grdata = $gr->findByPk($value['guardian']->id);
                            if($grdata->user_id)
                            {
                                $reminderrecipients[] = $grdata->user_id;
                                $batch_ids[$grdata->user_id] = $studentsobj->batch_id;
                                $student_ids[$grdata->user_id] = $studentsobj->id;
                            }
                        }    

                    }
                } 
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
//                foreach ($students as $value)
//                {
//                    $studentsobj = $stdobj->findByPk($value);
//                    $reminder = new Reminders();
//                    $reminder->sender = Yii::app()->user->id;
//                    $reminder->subject = Settings::$HomeworkText . ":" . $ehomework->title;
//                    $reminder->body = Settings::$HomeworkText . " Added for " . $subject_details->name . " Please check the homework For details";
//                    $reminder->recipient = $studentsobj->user_id;
//                    $reminder->school_id = Yii::app()->user->schoolId;
//                    $reminder->rid = $ehomework->id;
//                    $reminder->rtype = 4;
//                    $reminder->created_at = date("Y-m-d H:i:s");
//
//                    $reminder->updated_at = date("Y-m-d H:i:s");
//                    $reminder->save();
//                    $reminderrecipients[] = $studentsobj->user_id;
//                    $notification_ids[] = $reminder->id;
//                }
                if($notification_ids)
                {
                    $notification_id = implode(",", $notification_ids);
                    $user_id = implode(",", $reminderrecipients);
                    Settings::sendCurlNotification($user_id, $notification_id);
                }
                $response['status']['code'] = 200;
                $response['status']['msg'] = "SUCCESS";
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
    private function upload_homework($file,$homework)
    {
        $homework->attachment_updated_at = date("Y-m-d H:i:s");
        $homework->updated_at = date("Y-m-d H:i:s");
                    
        $attachment_datetime_chunk = explode(" ", $homework->updated_at);

        $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
        $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

        $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
        $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];

        $uploads_dir = Settings::$paid_image_path . "uploads/assignments/attachments/".$homework->id."/original/";
        $file_name =  str_replace(" ", "+",$file['attachment_file_name']['name']) . "?" .$attachment_extra;
        $tmp_name = $file["attachment_file_name"]["tmp_name"];
        
        if(!is_dir($uploads_dir))
        {
            @mkdir($uploads_dir, 0777, true);
        }
        
        $uploads_dir = $uploads_dir.$file_name;
        

        if(@move_uploaded_file($tmp_name, "$uploads_dir"))
        {
            $homework->attachment_file_name = $file['attachment_file_name']['name'];
            $homework->save();
        } 
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
        $school_id = Yii::app()->user->schoolId;
        $id = Yii::app()->request->getPost('id');

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $subject_ids && $content && $title && $duedate && $school_id && $assignment_type)
        {
            
            if($id)
            {
                $objhomework = new Assignments();
                $homework = $objhomework->findByPk($id);
            }
            else
            {
                $homework = new Assignments();
            }
            $subject_id_array = explode(",", $subject_ids);
            if($subject_id_array)
            {
                foreach($subject_id_array as $subject_id)
                {
             
                    $homework->subject_id = $subject_id;
                    $homework->content = $content;
                    $homework->title = $title;
                    $homework->duedate = $duedate;
                    $homework->school_id = Yii::app()->user->schoolId;
                    $homework->employee_id = Yii::app()->user->profileId;
                    $homework->assignment_type = $assignment_type;







                    if($is_draft)
                    {
                        $homework->is_published = 0; 
                    }


                    $homework->created_at = date("Y-m-d H:i:s");
                    if(!$id)
                    {
                        $homework->updated_at = date("Y-m-d H:i:s");
                    }




                    $studentsubjectobj = new StudentsSubjects();

                    $subobj = new Subjects();
                    $subject_details = $subobj->findByPk($subject_id);



                    $stdobj = new Students();

                    $students1 = $stdobj->getStudentByBatch($subject_details->batch_id);
                    $students2 = $studentsubjectobj->getSubjectStudent($subject_id);

                    $students = array_unique(array_merge($students1, $students2));
                    $homework->student_list = implode(",", $students);
                    $homework->save();

                    if (isset($_FILES['attachment_file_name']['name']) && !empty($_FILES['attachment_file_name']['name']))
                    {
                        $homework->updated_at = date("Y-m-d H:i:s");
                        $homework->attachment_content_type = Yii::app()->request->getPost('mime_type');
                        $homework->attachment_file_size = Yii::app()->request->getPost('file_size');
                        $this->upload_homework($_FILES, $homework);
                    }     



                    if(!$is_draft)
                    {
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
                                foreach($all_g as $value)
                                {
                                    $gr = new Guardians();
                                    $grdata = $gr->findByPk($value['guardian']->id);
                                    if($grdata->user_id)
                                    {
                                        $reminderrecipients[] = $grdata->user_id;
                                        $batch_ids[$grdata->user_id] = $studentsobj->batch_id;
                                        $student_ids[$grdata->user_id] = $studentsobj->id;
                                    }
                                }    

                            }
                        }    
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
                        }
                        if($notification_ids)
                        {
                            $notification_id = implode(",", $notification_ids);
                            $user_id = implode(",", $reminderrecipients);
                            Settings::sendCurlNotification($user_id, $notification_id);
                        }
                    }
                }
                
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
    
    public function actionTeacherQuiz() {
        if (isset($_POST) && !empty($_POST)) {
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
            if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin)) {
                
                $mod_timetable_entries = TimetableEntries::model()->findAllByAttributes( array('employee_id' => Yii::app()->user->profileId), array('select' => 'subject_id', 'group' => 'batch_id') );
                
                $subject_ids = array();
                
                if($mod_timetable_entries)
                foreach($mod_timetable_entries as $te) {
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
                
            } else {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            }
            
            echo CJSON::encode($response);
            Yii::app()->end();
        }
    }
    
}
