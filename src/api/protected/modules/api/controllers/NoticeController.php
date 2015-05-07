<?php

class NoticeController extends Controller {

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
                'actions' => array('index','getnotice','getsinglenotice', 'acknowledge'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionGetSingleNotice()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $id = Yii::app()->request->getPost('id');
        if ($id && Yii::app()->user->user_secret === $user_secret)
        {
            $news = new News;
            $news = $news->getSingleNews($id);
            
            if($news)
            {
               $response['data']['notice'] = $news;
               $response['status']['code'] = 200;
               $response['status']['msg'] = 'NOTICE_FOUND.'; 
            } 
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request.";
            }    
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request.";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    public function actionGetNotice() {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $notice_type = Yii::app()->request->getPost('notice_type');
        

            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {
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
                if(!$notice_type)
                {
                    $notice_type = 1;
                }

               
                $news = new News;
                
                
                $response['data']['total'] = $news->getNoticeCount($notice_type);
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size)
                {
                    $has_next = true;
                }
                $response['data']['has_next'] = $has_next;
                $response['data']['notice'] = $news->getNotice($notice_type,$page_number,$page_size);
                $response['status']['code'] = 200;
                $response['status']['msg'] = 'NOTICE_FOUND.';

                
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

    public function actionIndex() {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            
            $notice_type = Yii::app()->request->getPost('notice_type');
            $author_id = Yii::app()->request->getPost('author');

            $to_date = Yii::app()->request->getPost('to_date');
            $to_date = (!empty($to_date)) ? $to_date : \date('Y-m-d', \time());

            $from_date = Yii::app()->request->getPost('from_date');
            $from_date = (!empty($from_date)) ? $from_date : \date('Y-m-d', \strtotime("-1 week", \strtotime($to_date)));

            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {
                $school_id = Yii::app()->user->schoolId;
                if ($notice_type == 4) {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = 'NO_EVENT_FOUND. PLEASE TRY EVENT API';
                } else {
                    $news = new News;
                    $news = $news->getNews($school_id, $from_date, $to_date, $notice_type, $author_id);

                    if (!$news) {
                        $response['status']['code'] = 404;
                        $response['status']['msg'] = 'NO_NOTICE_FOUND.';
                    } else {
                        $response['data']['notice'] = $news;
                        $response['status']['code'] = 200;
                        $response['status']['msg'] = 'NOTICE_FOUND.';
                    }
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
            
            $notice_id = Yii::app()->request->getPost('notice_id');

            if (empty($notice_id)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request.";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->user_secret === $user_secret) {
                $school_id = Yii::app()->user->schoolId;
                $notice = new NewsAcknowledges;
                $notice = $notice->acknowledgeNotice($notice_id);

                if ($notice) {
                    $response['data']['notice_ack'] = $notice;
                } else {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "NO_NOTICE_ACKNOWLEDGED.";
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
