<?php

class SyllabusController extends Controller {

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
                'actions' => array('index', 'terms'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex() {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->request->getPost('school');

            $term_id = Yii::app()->request->getPost('term');
            $batch_id = Yii::app()->request->getPost('batch_id');

            if (empty($term_id) || !isset($term_id) || $term_id == '') {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request.";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {

                if (empty($school_id) || !isset($school_id) || $school_id == '') {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request.";
                    echo CJSON::encode($response);
                    Yii::app()->end();
                }

                if (!Yii::app()->user->isStudent && empty($batch_id)) {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request.";
                    echo CJSON::encode($response);
                    Yii::app()->end();
                }

                $syllabus = new Syllabuses;
                $syllabus = $syllabus->getSyllabus($school_id, $term_id, $batch_id);

                if (!$syllabus) {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = 'NO_SYLLABUS_FOUND';
                } else {
                    $response['data']['syllabus'] = $syllabus;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = 'SYLLABUS_FOUND';
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

    public function actionTerms() {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->request->getPost('school');
            $batch_id = Yii::app()->request->getPost('batch_id');

            if (Yii::app()->user->user_secret === $user_secret) {

                if (empty($school_id) || !isset($school_id) || $school_id == '') {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request.";
                    echo CJSON::encode($response);
                    Yii::app()->end();
                }

                if (!Yii::app()->user->isStudent && empty($batch_id)) {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request.";
                    echo CJSON::encode($response);
                    Yii::app()->end();
                }

                if (Yii::app()->user->isStudent) {
                    $batch_id = Yii::app()->user->batchId;
                }

                $exam_category = new ExamGroups;
                $exam_category = $exam_category->getExamCategory($school_id, $batch_id, 3);

                if (!empty($exam_category)) {
                    $response['data']['terms'] = $exam_category;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "TERMS_FOUND";
                } else {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "NO_TERMS_FOUND";
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
