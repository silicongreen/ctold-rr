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
                'actions' => array('index', 'getnotice', 'getsinglenotice', 'acknowledge', 'downloadnoticeattachment'),
                'users' => array('@'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('downloadnoticeattachment','getnoticeschool','getteacher'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionGetSingleNotice() {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $id = Yii::app()->request->getPost('id');
        if ($id && Yii::app()->user->user_secret === $user_secret) {
            $news = new News;
            $news = $news->getSingleNews($id);

            if ($news) {
                $response['data']['notice'] = $news;
                $response['status']['code'] = 200;
                $response['status']['msg'] = 'NOTICE_FOUND.';
            } else {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request.";
            }
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request.";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionDownloadnoticeattachment() {
        $id = Yii::app()->request->getParam('id');
        
        if ($id) {
            $news = new News();
            $newsObj = $news->findByPk($id);
            if ($newsObj && $newsObj->attachment_file_name) {
                $attachment_datetime_chunk = explode(" ", $newsObj->updated_at);

                $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
                $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

                $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
                $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];
                
                $url = Settings::$notice_attachment_path . $id . "/original/" . str_replace(")", "%29", str_replace("(", "%28", str_replace(" ", "+", $newsObj->attachment_file_name))) . "?" . $attachment_extra;
                
                $url = str_replace("&", "%26",$url);
                if (file_exists($url)) {
                    return Yii::app()->getRequest()->sendFile($newsObj->attachment_file_name, @file_get_contents($url));
                }
                else
                {
                    echo $url;
                }    
//
//
//                header("Content-Disposition: attachment; filename=" . $newsObj->attachment_file_name);
//                header("Content-Type: {$newsObj->attachment_content_type}");
//                header("Content-Length: " . $newsObj->attachment_file_size);
//                readfile($url);
            }
        }
    }
    
    public function actionGetTeacher()
    {
        $school_id = Yii::app()->request->getPost('school_id');


        
        $employee = new Employees();
        $employees = $employee->getEmployeeSchool($school_id);
        $em_array = array();
        $i = 0;
        if ($employees)
            foreach ($employees as $value)
            {
                $fullname = ($value->first_name) ? $value->first_name . " " : "";
                $fullname.= ($value->middle_name) ? $value->middle_name . " " : "";
                $fullname.= ($value->last_name) ? $value->last_name : "";
                $em_array[$i]['id'] = $value->id;
                $em_array[$i]['name'] = $fullname;
                $em_array[$i]['department'] = $value['department']->name;
                $em_array[$i]['email'] = $value->email;
                $em_array[$i]['image'] = Settings::getProfileImagePaid($value->user_id);
                $i++;
            }
        $response['data']['employees'] = $em_array;
        $response['status']['code'] = 200;
        $response['status']['msg'] = "Success";
        
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    public function actionGetNoticeSchool() {

        
        $school_id = Yii::app()->request->getPost('school_id');


        $response = array();
           
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        if (empty($page_number)) {
            $page_number = 1;
        }
        if (empty($page_size)) {
            $page_size = 10;
        }
       


        $news = new News;


        $response['data']['total'] = $news->getNoticeCountSchool($school_id);
        $has_next = false;
        if ($response['data']['total'] > $page_number * $page_size) {
            $has_next = true;
        }
        $response['data']['has_next'] = $has_next;
        $response['data']['notice'] = $news->getNoticeSchool($school_id, $page_number, $page_size);
        $response['status']['code'] = 200;
        $response['status']['msg'] = 'NOTICE_FOUND.';
        
           
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetNotice() {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $notice_type = Yii::app()->request->getPost('notice_type');
            
            $batch_id = Yii::app()->request->getPost('batch_id');


            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {
                $page_number = Yii::app()->request->getPost('page_number');
                $page_size = Yii::app()->request->getPost('page_size');
                if (empty($page_number)) {
                    $page_number = 1;
                }
                if (empty($page_size)) {
                    $page_size = 10;
                }
                if (!$notice_type) {
                    $notice_type = 1;
                }


                $news = new News;


                $response['data']['total'] = $news->getNoticeCount($notice_type,"","",$batch_id);
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size) {
                    $has_next = true;
                }
                $response['data']['has_next'] = $has_next;
                $response['data']['notice'] = $news->getNotice($notice_type, $page_number, $page_size,$batch_id);
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
