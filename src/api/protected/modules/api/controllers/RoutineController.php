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
                'actions' => array('index', 'exam'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex() {

        if (isset($_POST) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->request->getPost('school');
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

                if ($time_table) {
                    $response['data']['time_table'] = $time_table;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "ROUTINE_FOUND";
                } else {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "ROUTINE_NOT_FOUND";
                }

                echo CJSON::encode($response);
                Yii::app()->end();
            }
        }
    }

    public function actionExam() {

        if (isset($_POST) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->request->getPost('school');
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

                $time_table = new Exams;
                $time_table = $time_table->getExamTimeTable($school_id, $batch_id, $student_id);

                if ($time_table) {
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
