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
                'actions' => array('index', 'Done','singlehomework', 'saveassessment', 'assessment', 'getassessment', 'getproject', 'getsubject', 'addhomework', 'teacherhomework', 'homeworkstatus'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
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
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent))
            {

                $batch_id = Yii::app()->user->batchId;
                $student_id = Yii::app()->user->profileId;

                $assignment = new OnlineExamGroups();


                $homework_data = $assignment->getOnlineExamList($batch_id, $student_id);
                if ($homework_data)
                {
                    $response['data']['homework'] = $homework_data;
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

                    $response['data']['homework'] = $homework_datap[0];
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

                $homework_data = $assignment->getAssignment($batch_id, $student_id, "", $page_number, $subject_id, $page_size, 1);
                if ($homework_data)
                {

                    $response['data']['total'] = $assignment->getAssignmentTotal($batch_id, $student_id, "", $subject_id, 1);
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
        $employee_id = Yii::app()->user->profileId;
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
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
            $homework_data = $homework->getAssignmentTeacher($employee_id, $page_number, $page_size);
            if ($homework_data)
            {

                $response['data']['total'] = $homework->getAssignmentTotalTeacher($employee_id);
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
                $response['status']['code'] = 404;
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

    public function actionAddHomework()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $subject_id = Yii::app()->request->getPost('subject_id');
        $content = Yii::app()->request->getPost('content');
        $title = Yii::app()->request->getPost('title');
        $assignment_type = Yii::app()->request->getPost('type');
        $duedate = Yii::app()->request->getPost('duedate');
        $school_id = Yii::app()->user->schoolId;

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $subject_id && $content && $title && $duedate && $school_id && $assignment_type)
        {
            $homework = new Assignments();
            $homework->subject_id = $subject_id;
            $homework->content = $content;
            $homework->title = $title;
            $homework->duedate = $duedate;
            $homework->school_id = Yii::app()->user->schoolId;
            $homework->employee_id = Yii::app()->user->profileId;
            $homework->assignment_type = $assignment_type;


            $homework->created_at = date("Y-m-d H:i:s");

            $homework->updated_at = date("Y-m-d H:i:s");




            $studentsubjectobj = new StudentsSubjects();

            $subobj = new Subjects();
            $subject_details = $subobj->findByPk($subject_id);



            $stdobj = new Students();

            $students1 = $stdobj->getStudentByBatch($subject_details->batch_id);
            $students2 = $studentsubjectobj->getSubjectStudent($subject_id);

            $students = array_unique(array_merge($students1, $students2));
            $homework->student_list = implode(",", $students);
            $homework->save();



            $notifiation_ids = array();
            $reminderrecipients = array();
            foreach ($students as $value)
            {
                $studentsobj = $stdobj->findByPk($value);
                $reminder = new Reminders();
                $reminder->sender = Yii::app()->user->id;
                $reminder->subject = Settings::$HomeworkText . ":" . $title;
                $reminder->body = Settings::$HomeworkText . " Added for " . $subject_details->name . " Please check the homework For details";
                $reminder->recipient = $studentsobj->user_id;
                $reminder->school_id = Yii::app()->user->schoolId;
                $reminder->rid = $homework->id;
                $reminder->rtype = 4;
                $reminder->created_at = date("Y-m-d H:i:s");

                $reminder->updated_at = date("Y-m-d H:i:s");
                $reminder->save();
                $reminderrecipients[] = $studentsobj->user_id;
                $notifiation_ids[] = $reminder->id;
            }
            if($notifiation_ids)
            {
                $notifiation_id = implode(",", $notifiation_ids);
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
                $assignment_answer->content = "Please Accept";
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

}
