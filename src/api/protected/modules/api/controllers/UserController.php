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
                'actions' => array('auth','checkauth','updateprofile','updateprofilepaiduser','createuser'),
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
    private function encrypt($field, $salt)
    {
        return hash('sha512', $salt . $field);
    }
    public function actionCreateUser()
    {
        
        $email = Yii::app()->request->getPost('email');
        $password = Yii::app()->request->getPost('password');
        $first_name = Yii::app()->request->getPost('first_name');
        $paid_id = Yii::app()->request->getPost('paid_id');
        $paid_username = Yii::app()->request->getPost('paid_username');
        $paid_password = Yii::app()->request->getPost('paid_password');
        $paid_school_id = Yii::app()->request->getPost('paid_school_id');
        $paid_school_code = Yii::app()->request->getPost('paid_school_code');
        $freeuserObj = new Freeusers();
        if(Yii::app()->request->getPost('user_type') && $email && !$freeuserObj->getFreeuser($email) && $password && $first_name)
        {
            
            $freeuserObj->salt = md5(uniqid(rand(), true));
            $freeuserObj->password = $this->encrypt($password, $freeuserObj->salt);
            $freeuserObj->email = Yii::app()->request->getPost('email');
            $freeuserObj->user_type = Yii::app()->request->getPost('user_type');
            if($paid_id)
            $freeuserObj->paid_id = $paid_id;
            if($paid_username)
            $freeuserObj->paid_username = $paid_username;
            if($paid_password)
            $freeuserObj->paid_password = $paid_password;
            if($paid_school_id)
            $freeuserObj->paid_school_id = $paid_school_id;
            if($paid_school_code)
            $freeuserObj->paid_school_code = $paid_school_code;
            
            if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['name']))
            {
                $main_dir = Settings::$image_path . 'upload/free_user_profile_images/';
                $uploads_dir = Settings::$main_path . 'upload/free_user_profile_images/';
                $tmp_name = $_FILES["profile_image"]["tmp_name"];
                $name = "file_" . $user_id . "_" . time() . "_" . str_replace(" ", "-", $_FILES["profile_image"]["name"]);

                move_uploaded_file($tmp_name, "$uploads_dir/$name");
                $freeuserObj->profile_image = $main_dir . $name;
            }
            
            $freeuserObj->first_name = $first_name;

            if (Yii::app()->request->getPost('last_name'))
                $freeuserObj->last_name = Yii::app()->request->getPost('last_name');

            if (Yii::app()->request->getPost('middle_name'))
                $freeuserObj->middle_name = Yii::app()->request->getPost('middle_name');

            if (Yii::app()->request->getPost('gender'))
                $freeuserObj->gender = Yii::app()->request->getPost('gender');

            if (Yii::app()->request->getPost('nick_name'))
                $freeuserObj->nick_name = Yii::app()->request->getPost('nick_name');

            if (Yii::app()->request->getPost('user_type'))
                $freeuserObj->user_type = Yii::app()->request->getPost('user_type');

            if (Yii::app()->request->getPost('medium'))
                $freeuserObj->medium = Yii::app()->request->getPost('medium');

            if (Yii::app()->request->getPost('country'))
                $freeuserObj->tds_country_id = Yii::app()->request->getPost('country');

            if (Yii::app()->request->getPost('district'))
                $freeuserObj->district = Yii::app()->request->getPost('district');

            if (Yii::app()->request->getPost('grade_ids'))
                $freeuserObj->grade_ids = Yii::app()->request->getPost('grade_ids');

            if (Yii::app()->request->getPost('mobile_no'))
                $freeuserObj->mobile_no = Yii::app()->request->getPost('mobile_no');

            if (Yii::app()->request->getPost('dob'))
                $freeuserObj->dob = Yii::app()->request->getPost('dob');

            if (Yii::app()->request->getPost('school_name'))
                $freeuserObj->school_name = Yii::app()->request->getPost('school_name');

            if (Yii::app()->request->getPost('location'))
                $freeuserObj->location = Yii::app()->request->getPost('location');

            if (Yii::app()->request->getPost('teaching_for'))
                $freeuserObj->teaching_for = Yii::app()->request->getPost('teaching_for');

            if (Yii::app()->request->getPost('occupation'))
                $freeuserObj->occupation = Yii::app()->request->getPost('occupation');

            $freeuserObj->save();
            $folderObj = new UserFolder();

            $folderObj->createGoodReadFolder($freeuserObj->id);


            $freeuserObj->save();
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Successfully Saved";
        }   
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }    
        
        
        echo CJSON::encode($response);
        Yii::app()->end();
       
    }  
    public function actionUpdateProfilePaidUser()
    {
        $freeuserObj = new Freeusers();
        $paid_id = Yii::app()->request->getPost('paid_id');
        $paid_password = Yii::app()->request->getPost('paid_password');
        $first_name = Yii::app()->request->getPost('first_name');
        if($paid_id && $user_id = $freeuserObj->getFreeuserPaid($paid_id))
        {
            
            $freeuserObj = $freeuserObj->findByPk($user_id);
            
            if ($paid_password)
            {
                $freeuserObj->salt = md5(uniqid(rand(), true));
                $freeuserObj->password = $this->encrypt($paid_password, $freeuserObj->salt);
            }

            if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['name']))
            {
                $main_dir = Settings::$image_path . 'upload/free_user_profile_images/';
                $uploads_dir = Settings::$main_path . 'upload/free_user_profile_images/';
                $tmp_name = $_FILES["profile_image"]["tmp_name"];
                $name = "file_" . $user_id . "_" . time() . "_" . str_replace(" ", "-", $_FILES["profile_image"]["name"]);

                move_uploaded_file($tmp_name, "$uploads_dir/$name");
                $freeuserObj->profile_image = $main_dir . $name;
            }

            if($paid_password)
            $freeuserObj->paid_password = $paid_password;
            
            if ($first_name)
                $freeuserObj->first_name = $first_name;

            if (Yii::app()->request->getPost('last_name'))
                $freeuserObj->last_name = Yii::app()->request->getPost('last_name');

            if (Yii::app()->request->getPost('middle_name'))
                $freeuserObj->middle_name = Yii::app()->request->getPost('middle_name');

            if (Yii::app()->request->getPost('gender'))
                $freeuserObj->gender = Yii::app()->request->getPost('gender');

            if (Yii::app()->request->getPost('nick_name'))
                $freeuserObj->nick_name = Yii::app()->request->getPost('nick_name');

            if (Yii::app()->request->getPost('user_type'))
                $freeuserObj->user_type = Yii::app()->request->getPost('user_type');

            if (Yii::app()->request->getPost('medium'))
                $freeuserObj->medium = Yii::app()->request->getPost('medium');

            if (Yii::app()->request->getPost('country'))
                $freeuserObj->tds_country_id = Yii::app()->request->getPost('country');

            if (Yii::app()->request->getPost('district'))
                $freeuserObj->district = Yii::app()->request->getPost('district');

            if (Yii::app()->request->getPost('grade_ids'))
                $freeuserObj->grade_ids = Yii::app()->request->getPost('grade_ids');

            if (Yii::app()->request->getPost('mobile_no'))
                $freeuserObj->mobile_no = Yii::app()->request->getPost('mobile_no');

            if (Yii::app()->request->getPost('dob'))
                $freeuserObj->dob = Yii::app()->request->getPost('dob');

            if (Yii::app()->request->getPost('school_name'))
                $freeuserObj->school_name = Yii::app()->request->getPost('school_name');

            if (Yii::app()->request->getPost('location'))
                $freeuserObj->location = Yii::app()->request->getPost('location');

            if (Yii::app()->request->getPost('teaching_for'))
                $freeuserObj->teaching_for = Yii::app()->request->getPost('teaching_for');

            if (Yii::app()->request->getPost('occupation'))
                $freeuserObj->occupation = Yii::app()->request->getPost('occupation');


            $freeuserObj->save();
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Successfully Saved";
        }   
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }    
        
        
        echo CJSON::encode($response);
        Yii::app()->end();
       
    }  
    
    public function actionUpdateProfile()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $paid_id = Yii::app()->request->getPost('paid_id');
        $paid_username = Yii::app()->request->getPost('paid_username');
        $paid_password = Yii::app()->request->getPost('paid_password');
        $paid_school_id = Yii::app()->request->getPost('paid_school_id');
        $paid_school_code = Yii::app()->request->getPost('paid_school_code');
        $first_name = Yii::app()->request->getPost('first_name');
//        
//        $user = new Users;
//        $user->username = $paid_username;
//        $user->hashed_password = $paid_password;
        
        if($user_id)
        {
            $freeuserObj = new Freeusers();
            $freeuserObj = $freeuserObj->findByPk($user_id);
            if($paid_id)
            $freeuserObj->paid_id = $paid_id;
            if($paid_username)
            $freeuserObj->paid_username = $paid_username;
            if($paid_password)
            $freeuserObj->paid_password = $paid_password;
            if($paid_school_id)
            $freeuserObj->paid_school_id = $paid_school_id;
            if($paid_school_code)
            $freeuserObj->paid_school_code = $paid_school_code;
            if ($paid_password)
            {
                $freeuserObj->salt = md5(uniqid(rand(), true));
                $freeuserObj->password = $this->encrypt($paid_password, $freeuserObj->salt);
            }

            if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['name']))
            {
                $main_dir = Settings::$image_path . 'upload/free_user_profile_images/';
                $uploads_dir = Settings::$main_path . 'upload/free_user_profile_images/';
                $tmp_name = $_FILES["profile_image"]["tmp_name"];
                $name = "file_" . $user_id . "_" . time() . "_" . str_replace(" ", "-", $_FILES["profile_image"]["name"]);

                move_uploaded_file($tmp_name, "$uploads_dir/$name");
                $freeuserObj->profile_image = $main_dir . $name;
            }

            if ($first_name)
                $freeuserObj->first_name = $first_name;

            if (Yii::app()->request->getPost('last_name'))
                $freeuserObj->last_name = Yii::app()->request->getPost('last_name');

            if (Yii::app()->request->getPost('middle_name'))
                $freeuserObj->middle_name = Yii::app()->request->getPost('middle_name');

            if (Yii::app()->request->getPost('gender'))
                $freeuserObj->gender = Yii::app()->request->getPost('gender');

            if (Yii::app()->request->getPost('nick_name'))
                $freeuserObj->nick_name = Yii::app()->request->getPost('nick_name');

            if (Yii::app()->request->getPost('user_type'))
                $freeuserObj->user_type = Yii::app()->request->getPost('user_type');

            if (Yii::app()->request->getPost('medium'))
                $freeuserObj->medium = Yii::app()->request->getPost('medium');

            if (Yii::app()->request->getPost('country'))
                $freeuserObj->tds_country_id = Yii::app()->request->getPost('country');

            if (Yii::app()->request->getPost('district'))
                $freeuserObj->district = Yii::app()->request->getPost('district');

            if (Yii::app()->request->getPost('grade_ids'))
                $freeuserObj->grade_ids = Yii::app()->request->getPost('grade_ids');

            if (Yii::app()->request->getPost('mobile_no'))
                $freeuserObj->mobile_no = Yii::app()->request->getPost('mobile_no');

            if (Yii::app()->request->getPost('dob'))
                $freeuserObj->dob = Yii::app()->request->getPost('dob');

            if (Yii::app()->request->getPost('school_name'))
                $freeuserObj->school_name = Yii::app()->request->getPost('school_name');

            if (Yii::app()->request->getPost('location'))
                $freeuserObj->location = Yii::app()->request->getPost('location');

            if (Yii::app()->request->getPost('teaching_for'))
                $freeuserObj->teaching_for = Yii::app()->request->getPost('teaching_for');

            if (Yii::app()->request->getPost('occupation'))
                $freeuserObj->occupation = Yii::app()->request->getPost('occupation');


            $freeuserObj->save();
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Successfully Saved";
        }   
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }    
        
        
        echo CJSON::encode($response);
        Yii::app()->end();
       
    }   
    public function actionCheckAuth() 
    {
       if (isset($_POST) && !empty($_POST)) 
       {
           $user_id = Yii::app()->request->getPost('user_id');
           $auth_id = Yii::app()->request->getPost('auth_id');
           $activation_code = Yii::app()->request->getPost('activation_code');
           if($user_id && $auth_id)
           {
               $authobj = new Userauth();
               if($authobj->getAuth($user_id, $auth_id))
               {
                  if($activation_code)
                  {
                    
                    if($authobj->getAuth($user_id, $auth_id,$activation_code))
                    {
                        $response['status']['code'] = 200;
                        $response['status']['msg'] = "valid";
                    }
                    else
                    {
                       $response['status']['code'] = 400;
                       $response['status']['msg'] = "Bad Request"; 
                    }    
                  }
                  else
                  {    
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Success"; 
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
               $response['status']['code'] = 400;
               $response['status']['msg'] = "Bad Request";
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
