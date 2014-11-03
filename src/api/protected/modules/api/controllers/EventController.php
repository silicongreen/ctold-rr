<?php

class EventController extends Controller {

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
                'actions' => array('index', 'acknowledge'),
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

            $from_date = Yii::app()->request->getPost('from_date');
            $from_date = (!empty($from_date)) ? $from_date : \date('Y-m-d', \time());

            $to_date = Yii::app()->request->getPost('to_date');
            $to_date = (!empty($to_date)) ? $to_date : null;

            $page_no = Yii::app()->request->getPost('page_number');
            $page_no = (!empty($page_no)) ? $page_no : 1;

            $page_size = Yii::app()->request->getPost('page_size');
            $page_size = (!empty($page_size)) ? $page_size : 10;

            $category_id = Yii::app()->request->getPost('category');
            $category_id = (!empty($category_id)) ? $category_id : null;

            $archive = Yii::app()->request->getPost('archive');
            $archive = (!empty($archive) && ($archive == 'true')) ? true : false;
            
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {

                $events = new Events;
                $events = $events->getEvents($school_id, $from_date, $to_date, $page_no, $page_size, $category_id, false, false, $archive);
                
                if (!$events) {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = 'NO_EVENT_FOUND.';
                } else {
                    
                    $response['data']['events'] = $events;
                    
                    $events_cnt = new Events;
                    $response['data']['total'] = $events_cnt->getEvents($school_id, $from_date, $to_date, $page_no, $page_size, $category_id, false, true);
                    
                    $has_next = false;
                    if ($response['data']['total'] > ($page_no * $page_size) ) {
                        $has_next = true;
                    }
                    
                    $response['data']['has_next'] = $has_next;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = 'EVENT_FOUND.';
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
    
    public function actionAcknowledge() {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->request->getPost('school');
            $event_id = Yii::app()->request->getPost('event_id');
            $status = Yii::app()->request->getPost('status');
            
            if (Yii::app()->user->user_secret === $user_secret) {
                
                if(empty($event_id) || !isset($status) || $status == ''){
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request.";
                    echo CJSON::encode($response);
                    Yii::app()->end();
                }
                
                $event = new EventAcknowledges;
                $event = $event->acknowledgeEvent($event_id, $status, $school_id);
                
                if ($event === false) {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "NO_EVENT_ACKNOWLEDGED.";
                } else {
                    $response['data']['event_ack'] = $event;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EVENT_ACKNOWLEDGED.";
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
