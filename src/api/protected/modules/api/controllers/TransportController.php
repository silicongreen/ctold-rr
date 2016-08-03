<?php

class TransportController extends Controller {

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
                'actions' => array('index'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex() {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {
            
            $response = array();
            
            $user_secret = Yii::app()->request->getPost('user_secret');
            
            $receiver_id = Yii::app()->request->getPost('student_id');
            $receiver_id = (!empty($receiver_id)) ? $receiver_id : null;
            
           
            
            if (Yii::app()->user->isParent && empty($receiver_id)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
                echo CJSON::encode($response);
                Yii::app()->end();
            }
            
            if (Yii::app()->user->user_secret === $user_secret) {

                $receiver_type = 'Student';
                if (Yii::app()->user->isTeacher) {
                    $receiver_type = 'Employee';
                }
                
                if(!Yii::app()->user->isParent){
                    $receiver_id = Yii::app()->user->profileId;
                }
                
                $transport_schedule = new RouteSchedules;
                $transport_schedule = $transport_schedule->getRouteSchedule($receiver_type, $receiver_id);
                
                if (!$transport_schedule) {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = 'NO_TRANSPORT_FOUND';
                } else {

                    $response['data']['transport'] = $transport_schedule;

                    $response['status']['code'] = 200;
                    $response['status']['msg'] = 'TRANSPORT_FOUND.';
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
