<?php

class UserController extends Controller {

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
                'actions' => array('auth'),
                'users' => array('*'),
            ),
//            array('allow', // allow authenticated user to perform 'create' and 'update' actions
//                'actions' => array('create', 'update', 'index', 'view', 'list'),
//                'users' => array('@'),
//            ),
//            array('allow', // allow admin user to perform 'admin' and 'delete' actions
//                'actions' => array('admin', 'delete'),
//                'users' => array('admin'),
//            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionAuth() {

        $username = '';
        $password = '';

        if (isset($_POST) && !empty($_POST)) {

            if (!isset($_POST['user_secret'])) {

                if (!isset($_POST['username']) || !isset($_POST['password']) || empty($_POST['username']) || empty($_POST['password'])) {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = 'Bad Request.';

                    echo CJSON::encode($response);
                    Yii::app()->end();
                }
            }

            $username = Yii::app()->request->getPost('username');
            $password = Yii::app()->request->getPost('password');
            $ud_id = Yii::app()->request->getPost('udid');
            $user_secret = Yii::app()->request->getPost('user_secret');

            $free_user = new Freeusers();
            $user = new Users;
            $user->username = $username;
            $user->hashed_password = $password;
            $user->ud_id = $ud_id;
            $user->api_token = $user_secret;
            
            if ($user->validate()) {

                if ($user_paid_login_data = $user->login()) {

                    if($data = $free_user->login($username,$password, true))
                    {
                        $folderObj = new UserFolder();
                    
                        $folderObj->createGoodReadFolder($data->id);
                        $response['data']['free_id'] = $data->id;
                        $response['data']['user']  = $free_user->getUserInfo($data->id);
                    }   
                    else
                    {
                       $response['data']['free_id'] = "";
                       $response['data']['user'] = array();
                    }    
                    $response['data']['user_type'] = 1;
                    $response['data']['paid_user']['id'] = Yii::app()->user->id;
                    $response['data']['paid_user']['is_admin'] = Yii::app()->user->isAdmin;
                    $response['data']['paid_user']['is_student'] = Yii::app()->user->isStudent;

                    if (Yii::app()->user->isStudent) {
                        $response['data']['paid_user']['batch_id'] = Yii::app()->user->batchId;

                        $exam_category = new ExamGroups;
                        $exam_category = $exam_category->getExamCategory(Yii::app()->user->schoolId, Yii::app()->user->batchId, 3);

                        $response['data']['paid_user']['terms'] = array();
                        
                        if($exam_category)
                        $response['data']['paid_user']['terms'] = $exam_category;
                    }

                    $response['data']['paid_user']['profile_id'] = Yii::app()->user->profileId;
                    $response['data']['paid_user']['is_parent'] = Yii::app()->user->isParent;
                    $response['data']['paid_user']['is_teacher'] = Yii::app()->user->isTeacher;
                    $response['data']['paid_user']['school_id'] = Yii::app()->user->schoolId;
                    
                    $school_obj  = new Schools();
                    
                    $school_details = $school_obj->findByPk(Yii::app()->user->schoolId);
                    
                  
                    
                    $school_code = $school_details->code;
                    
                    if(is_array($user_paid_login_data))
                    {
                      $username =  $user_paid_login_data[1]; 
                      $password =  $user_paid_login_data[0];
                    }
                    
                      
                    
                    $attendance = new Attendances();
                    $response['data']['weekend'] = $attendance->getWeekend(Yii::app()->user->schoolId);

                    $response['data']['children'] = array();
                    if (Yii::app()->user->isParent) {
                        $response['data']['children'] = $user->studentList(Yii::app()->user->profileId);
                    }

                    if (!isset($user_secret)) {
                        $response['data']['paid_user']['secret'] = Yii::app()->user->user_secret;
                    }
                    
                    $response['data']['session'] = Yii::app()->session->getSessionID();
                    $fedenatoken = Settings::getFedenaToken($school_code, $username, $password);
                    
                    
                    Yii::app()->user->setState("school_code",$school_code);
                    
                    if(isset($fedenatoken->access_token))
                    {
                        Yii::app()->user->setState("access_token_user",$fedenatoken->access_token);
                    }
                    else
                    {
                        $response['data'] = array();
                        $response['status']['code'] = 404;
                        $response['status']['msg'] = "USER_NOT_FOUND";
                    }  

                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "USER_FOUND";
                } 
                else if($data = $free_user->login($username,$password))
                {
                   
                    $folderObj = new UserFolder();
                   
                    $folderObj->createGoodReadFolder($data->id);
                    //for paid
//                    $response['data']['paid_user'] = array();
//                    $response['data']['weekend'] = array();
//                    $response['data']['children'] = array();
//                    $response['data']['session'] =  Yii::app()->session->getSessionID();
                    //for paid
                    
                    $response['data']['user_type'] = 0;
                    $response['data']['free_id'] = $data->id;
                    $response['data']['user']  = $free_user->getUserInfo($data->id);
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "USER_FOUND";
                }        
                else {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "USER_NOT_FOUND";
                }
            } else {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            }
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

}
