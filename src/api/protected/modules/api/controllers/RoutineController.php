<?php

class RoutineController extends Controller {

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
                'actions' => array('index','teacherexam','nextclassstudent','allexam', 'exam','getdateroutine','teacher','nextclass','nextclassadmin'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionNextClassStudent() {

        if (isset($_POST) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $bacth_id = Yii::app()->request->getPost('batch_id');
            if (Yii::app()->user->isParent && empty($bacth_id)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->isStudent) {
                $bacth_id = Yii::app()->user->batchId;
            }
            
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {
                $time_table = new TimetableEntries;
                $time_table = $time_table->getNextStudent($bacth_id);
                
                $response['data']['today'] = date("Y-m-d");
                $response['data']['time_table'] = array();
                if ($time_table) {
                    $response['data']['time_table'] = $time_table;

                }
                $response['status']['code'] = 200;
                $response['status']['msg'] = "ROUTINE_FOUND";
               

                echo CJSON::encode($response);
                Yii::app()->end();
            }
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }
    }
    public function actionTeacherExam() {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $limit = Yii::app()->request->getPost('limit');
        $no_exams = Yii::app()->request->getPost('no_exams');
        if ((Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && Yii::app()->user->user_secret === $user_secret) 
        {
            if(!$limit)
            {
               $limit = 10; 
            }
            if(!$no_exams)
            {
                $no_exams = 0;
            }
            $examobj = new Exams();
            $response['data']['time_table'] = $examobj->getTeacherExam($limit,$no_exams);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "ROUTINE_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
     public function actionNextClassAdmin() {

        if (isset($_POST) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->user->schoolId;
            $response = array();
            if (Yii::app()->user->isAdmin && $school_id && Yii::app()->user->user_secret === $user_secret) {
                $time_table = new TimetableEntries;
                $next_classess = $time_table->getNextAdmin($school_id);
               
                $current_class = $time_table->getCurrentAdmin($school_id);
                
                $response['data']['today'] = date("Y-m-d");
                $response['data']['next_classess'] = array();
                $response['data']['current_class'] = array();
             
                if ($next_classess) {
                    $response['data']['next_classess'] = $next_classess;

                }
                if ($current_class) {
                    $response['data']['current_class'] = $current_class;

                }
                $response['status']['code'] = 200;
                $response['status']['msg'] = "ROUTINE_FOUND";
                

                
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";
            }
            echo CJSON::encode($response);
            Yii::app()->end();
        }
    }
     public function actionNextClass() {

        if (isset($_POST) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->user->schoolId;
            $response = array();
            if ((Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $school_id && Yii::app()->user->user_secret === $user_secret) {
                $time_table = new TimetableEntries;
                $time_table = $time_table->getNextTeacher($school_id, Yii::app()->user->profileId);
                
                $response['data']['today'] = date("Y-m-d");
                $response['data']['time_table'] = array();
                if ($time_table) {
                    $response['data']['time_table'] = $time_table;

                }
                $response['status']['code'] = 200;
                $response['status']['msg'] = "ROUTINE_FOUND";
                

                
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";
            }
            echo CJSON::encode($response);
            Yii::app()->end();
        }
    }
    
    public function actionTeacher() {

        if (isset($_POST) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->user->schoolId;
           
            $date = Yii::app()->request->getPost('date');
            $day_id = Yii::app()->request->getPost('day_id',-1);
            $date = (!empty($date)) ? $date : \date('Y-m-d', \time());
            $response = array();
            if ((Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) && $school_id && Yii::app()->user->user_secret === $user_secret) {
                if($day_id==-1)
                {
                    $day_id = false;
                }
                $time_table = new TimetableEntries;
                $time_table = $time_table->getTimeTablesTeacher($school_id,$date, Yii::app()->user->profileId, $day_id);
                
                
                $response['data']['time_table'] = array();

                $response['data']['weekdays'] = Settings::$ar_weekdays;

                $cur_day_name = Settings::getCurrentDay();
                $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];

                $response['data']['cur_week'] = $cur_day_key;
                if ($time_table) {
                    $response['data']['time_table'] = $time_table;

                }
                $response['status']['code'] = 200;
                $response['status']['msg'] = "ROUTINE_FOUND";
               

                
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";
            }
            echo CJSON::encode($response);
            Yii::app()->end();
        }
    }
    
    public function actionGetDateRoutine() {

        if (isset($_POST) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->user->schoolId;
            $bacth_id = Yii::app()->request->getPost('batch_id');
            $date = Yii::app()->request->getPost('date');
            $daily = Yii::app()->request->getPost('daily');
            
            $day_id = Yii::app()->request->getPost('day_id');
            $cur_day_name = Settings::getCurrentDay();
            $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
          
            if($day_id=="current")
            {
                $day_id = $cur_day_key;
            }    
            
            $date = (!empty($date)) ? $date : \date('Y-m-d', \time());

            $weekly = ($daily == 1) ? false : true;

            if (Yii::app()->user->isParent && empty($bacth_id)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->isStudent) {
                $bacth_id = Yii::app()->user->batchId;
            }
            
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {
                $time_table = new TimetableEntries;
                $time_table = $time_table->getTimeTables($school_id, $date, $weekly, $bacth_id,$day_id);

               
                $response['data']['weekdays'] = Settings::$ar_weekdays;

                $response['data']['cur_week'] = $cur_day_key;
                $response['data']['time_table'] = array();
                if ($time_table) {
                    $response['data']['time_table'] = $time_table;

                }
                $response['status']['code'] = 200;
                $response['status']['msg'] = "ROUTINE_FOUND";
                

                echo CJSON::encode($response);
                Yii::app()->end();
            }
        }
    }

    public function actionIndex() {

        if (isset($_POST) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->user->schoolId;
            $bacth_id = Yii::app()->request->getPost('batch_id');
            $date = Yii::app()->request->getPost('date');
            $daily = Yii::app()->request->getPost('daily');
            
            
            $date = (!empty($date)) ? $date : \date('Y-m-d', \time());

            $weekly = ($daily == 1) ? false : true;

            if (Yii::app()->user->isParent && empty($bacth_id)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->isStudent) {
                $bacth_id = Yii::app()->user->batchId;
            }
            
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {
                $time_table = new TimetableEntries;
                $time_table = $time_table->getTimeTables($school_id, $date, $weekly, $bacth_id);

               
                    
                $response['data']['time_table'] = array();
                if ($time_table) {
                    $response['data']['time_table'] = $time_table;

                }
                $response['status']['code'] = 200;
                $response['status']['msg'] = "ROUTINE_FOUND";


                echo CJSON::encode($response);
                Yii::app()->end();
            }
        }
    }
    
    public function actionAllExam() {

        if (isset($_POST) && !empty($_POST)) {
            $no_exams = Yii::app()->request->getPost('no_exams');
            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->user->schoolId;
            $batch_id = Yii::app()->request->getPost('batch_id');
            $student_id = Yii::app()->request->getPost('student_id');

            $response = array();

            if ((Yii::app()->user->isParent) && ( empty($school_id) || empty($batch_id) || empty($student_id) )) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->isStudent) {
                $school_id = Yii::app()->user->schoolId;
                $batch_id = Yii::app()->user->batchId;
                $student_id = Yii::app()->user->profileId;
            }

            if (Yii::app()->user->user_secret === $user_secret) {
                if(!$no_exams)
                {
                    $no_exams = 0;
                }

                $time_table = new ExamGroups();
                $time_table = $time_table->getAllExamsBatch($batch_id,$no_exams);

                if ($time_table) {
                    $response['data']['all_exam'] = $time_table;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_FOUND";
                } else {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "EXAM_ROUTINE_NOT_FOUND";
                }
            }
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionExam() {

        if (isset($_POST) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $no_exams = Yii::app()->request->getPost('no_exams');
            $school_id = Yii::app()->user->schoolId;
            $batch_id = Yii::app()->request->getPost('batch_id');
            $student_id = Yii::app()->request->getPost('student_id');
            $exam_id = Yii::app()->request->getPost('exam_id');

            $response = array();

            if ((Yii::app()->user->isParent) && ( empty($school_id) || empty($batch_id) || empty($student_id) )) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->isStudent) {
                $school_id = Yii::app()->user->schoolId;
                $batch_id = Yii::app()->user->batchId;
                $student_id = Yii::app()->user->profileId;
            }

            if (Yii::app()->user->user_secret === $user_secret) {
                if(!$no_exams)
                {
                    $no_exams = 0;
                }

                $time_table = new Exams;
                $time_table = $time_table->getExamTimeTable($school_id, $batch_id, $student_id,$exam_id,"",$no_exams);

                if ($time_table) {
                    
                    $robject = new Reminders();
                    $robject->ReadReminderNew(Yii::app()->user->id, 0 ,2, $exam_id);
                    
                    $response['data']['exam_time_table'] = $time_table;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_FOUND";
                } else {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "EXAM_ROUTINE_NOT_FOUND";
                }
            }
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

}
