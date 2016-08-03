<?php

class ClubController extends Controller {

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

            $child_id = Yii::app()->request->getPost('student_id');
            $child_id = (!empty($child_id)) ? $child_id : null;

            if (Yii::app()->user->isParent && empty($child_id)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {
                $school_id = Yii::app()->user->schoolId;
                if (Yii::app()->user->isStudent) {
                    $events = new Events;
                    $events = $events->getEvents($school_id, $from_date, $to_date, $page_no, $page_size, $category_id, true, $child_id);
                }

                if (Yii::app()->user->isParent) {
                    $user = new Users;
                    $ar_childern_id = $user->studentList(Yii::app()->user->profileId);
                    $ar_childern_id = Settings::extractIds($ar_childern_id, 'profile_id');

                    $events = new EventAcknowledges;
                    $events = $events->getClubJoinNotifications($ar_childern_id, $school_id);
                }

                if (!$events) {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = 'NO_CLUB_NEWS_FOUND.';
                } else {

                    $response['data']['clubs'] = $events;

                    $events_cnt = new Events;
                    $response['data']['total'] = $events_cnt->getEvents($school_id, $from_date, $to_date, $page_no, $page_size, $category_id, true, true);

                    $has_next = false;
                    if ($response['data']['total'] > $page_no * $page_size) {
                        $has_next = true;
                    }

                    $response['data']['has_next'] = $has_next;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = 'CLUB_NEWS_FOUND.';
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
            $school_id = Yii::app()->user->schoolId;
            $event_id = Yii::app()->request->getPost('club_id');
            $child_id = Yii::app()->request->getPost('student_id');

            if (empty($child_id) || empty($event_id) || empty($school_id) || empty($user_secret)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request.";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->user_secret === $user_secret) {

                $event = new EventAcknowledges;
                $event = $event->acknowledgeClubJoin($event_id, $child_id, $school_id);
                
                if ($event == 0 || $event == 1 ) {
                    $response['data']['club_ack'] = (int) $event;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "CLUB_JOIN_ACKNOWLEDGED";
                } else {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "NO_CLUB_NEWS_ACKNOWLEDGED";
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
