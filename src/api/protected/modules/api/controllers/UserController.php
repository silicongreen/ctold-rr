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
                'actions' => array(
                    'error',
                    'auth',
                    'setfreeuserid',
                    "unsetfreeuserid",
                    'checkauth',
                    'geterrors',
                    'logout',
                    'updateprofile',
                    'updateprofilepaiduser',
                    'createuser',
                    'delete_by_paid_id',
                    'forgotpassword',
                    'resetpassword',
                    'checkversion',
                    'paymentmail',
                    'gcmtofcm'
                ),
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
    public function actionGcmToFcm()
    {
      
       $device_id = Yii::app()->request->getPost('device_id');
       $fcm_id = Yii::app()->request->getPost('fcm_id');
       if($device_id && $fcm_id)
       {
            $gcmobj = new Gcm();
            $gcm_added = $gcmobj->getGcmDeviceId($device_id);
            if($gcm_added)
            {
                $gcmData = $gcmobj->findByPk($gcm_added);
                $gcmData->gcm_id = $fcm_id;
                $gcmData->fcm_converted = 1;
                $gcmData->save();
            }
            $response['data']['id'] = $fcm_id;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "SUCCESFULLY_SAVED";
       }
       echo CJSON::encode($response);
       Yii::app()->end();
       
    } 
    public function actionCheckVersion() {
        $response['data']['version'] = Settings::$version_update;
        $response['status']['code'] = 200;
        $response['status']['msg'] = 'Success';
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    public function actionPaymentMail() {
        $this->layout = FALSE;
        $this->pageHeader = Yii::app()->request->getPost('header');

        $body =  Yii::app()->request->getPost('body');

        $mail = new YiiMailer();
        $mail->setSmtp('host.champs21.com', 465, 'ssl', true, 'info@champs21.com', '174097@hM&^256');
        $mail->setTo(array(Yii::app()->request->getPost('email') => Yii::app()->request->getPost('first_name') . ' ' . Yii::app()->request->getPost('last_name')));
        $mail->setSubject(Yii::app()->request->getPost('header'));
        $mail->setFrom('info@classtune.com', 'Team Classtune ');

        $mail->setBody($body);
        $mail->send();
    }

    public function actionForgotpassword() {

        $email = Yii::app()->request->getPost('email');
        $username = Yii::app()->request->getPost('username');

        if (empty($email) || empty($username)) {
            $response['status']['code'] = 400;
            $response['status']['msg'] = 'Bad Request';

            echo CJSON::encode($response);
            Yii::app()->end();
        }

        $user = Users::model()->findByAttributes(array('username' => $username, 'email' => $email));

        if (!empty($user)) {

            $user->reset_password_code = hash('sha256', Settings::getUniqueId($user->id, 32));
            $user->reset_password_code_until = date('Y-m-d H:i:s', strtotime("+30 minutes"));

            if ($user->update()) {
                $response['status']['code'] = 200;
                $response['status']['msg'] = 'Please check email';
                $this->sendForgotPasswordMail($user);
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = 'Something went wrong please try again';
            }    

            
        } else {
            $response['status']['code'] = 404;
            $response['status']['msg'] = 'Invalid username or email.';
        }


        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionResetpassword() {

        $new_password = Yii::app()->request->getPost('password');
        $token = Yii::app()->request->getPost('token');

        if (empty($new_password) || empty($token)) {
            $response['status']['code'] = 400;
            $response['status']['msg'] = 'Bad Request';

            echo CJSON::encode($response);
            Yii::app()->end();
        }

        $now = time();

        $user = Users::model()->findByAttributes(array('reset_password_code' => $token));

        if ( !empty($user) && ($now <= strtotime($user->reset_password_code_until)) ) {
            
            $free_user = Freeusers::model()->findByAttributes(array('paid_username' => $user->username));

            $salt = base64_encode(Settings::getUniqueId($user->id, 8));
            $salt = substr($salt, 4, 12);

            $user->salt = $salt;
            $user->hashed_password = sha1($user->salt . $new_password);
            $user->reset_password_code_until = date('Y-m-d H:i:s', strtotime("-1 minute"));
            
            $free_user->paid_password = $new_password;
            $free_user->password = hash('sha512', $free_user->salt . $new_password);
            $free_user->update();

            if ($user->update()) {
                $response['status']['code'] = 200;
                $response['status']['msg'] = 'Password changed successfully.';
            } else {
                $response['status']['code'] = 500;
                $response['status']['msg'] = 'Somthing bad happened, please try again later';
            }
        } else {
            $response['status']['code'] = 403;
            $response['status']['msg'] = 'Token expired.';
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    private function encrypt($field, $salt) {
        return hash('sha512', $salt . $field);
    }

    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) { 
            $error_log = new ErrorLogYii();
            if (isset($error['code'])) {
                $error_log->ecode = $error['code'];
            }
            if (isset($error['type'])) {
                $error_log->etype = $error['type'];
            }
            if (isset($error['message'])) {
                $error_log->emsg = $error['message'];
            }
            if (isset($error['file'])) {
                $error_log->efile = $error['file'];
            }
            if (isset($error['line'])) {
                $error_log->eline = $error['line'];
            }
            if (isset($error['trace'])) {
                $error_log->etrace = $error['trace'];
            }
            $error_log->is_paid = 0;
            if (isset(Yii::app()->user->id)) {
                $error_log->is_paid = 1;
                $error_log->paid_user_id = Yii::app()->user->id;
            }

            if (isset(Yii::app()->user->free_id)) {
                $error_log->user_id = Yii::app()->user->free_id;
            }

            if(strpos($error_log->emsg, "CWebUser.profileId") == false && strpos($error_log->emsg, "favicon.ico") == false )
            {
                $error_log->save();
            }
           
            $response['status']['code'] = $error_log->ecode;
            $response['status']['msg'] = "System Error";
            echo CJSON::encode($response);
            Yii::app()->end();
        }
    }

    public function actionCreateUser() {

        $email = Yii::app()->request->getPost('email');
        $password = Yii::app()->request->getPost('password');
        $first_name = Yii::app()->request->getPost('first_name');
        $paid_id = Yii::app()->request->getPost('paid_id');
        $paid_username = Yii::app()->request->getPost('paid_username');
        $paid_password = Yii::app()->request->getPost('paid_password');
        $paid_school_id = Yii::app()->request->getPost('paid_school_id');
        $paid_school_code = Yii::app()->request->getPost('paid_school_code');

        $freeuserObj = new Freeusers();
        $freeuserObj->salt = md5(uniqid(rand(), true));
        $freeuserObj->password = $this->encrypt($password, $freeuserObj->salt);
        $freeuserObj->email = Yii::app()->request->getPost('email');
        $freeuserObj->user_type = Yii::app()->request->getPost('user_type');

        if ($paid_id) {
            $freeuserObj->paid_id = $paid_id;
        }

        if ($paid_username) {
            $freeuserObj->paid_username = $paid_username;
        }

        if ($paid_password) {
            $freeuserObj->paid_password = $paid_password;
        }

        if ($paid_school_id) {
            $freeuserObj->paid_school_id = $paid_school_id;
        }

        if ($paid_school_code) {
            $freeuserObj->paid_school_code = $paid_school_code;
        }

        if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['name'])) {
            $main_dir = Settings::$image_path . 'upload/free_user_profile_images/';
            $uploads_dir = Settings::$main_path . 'upload/free_user_profile_images/';
            $tmp_name = $_FILES["profile_image"]["tmp_name"];
            $name = "file_" . $user_id . "_" . time() . "_" . str_replace(" ", "-", $_FILES["profile_image"]["name"]);

            move_uploaded_file($tmp_name, "$uploads_dir/$name");
            $freeuserObj->profile_image = $main_dir . $name;
        }

        $freeuserObj->first_name = $first_name;

        if (Yii::app()->request->getPost('last_name')) {
            $freeuserObj->last_name = Yii::app()->request->getPost('last_name');
        }

        if (Yii::app()->request->getPost('middle_name')) {
            $freeuserObj->middle_name = Yii::app()->request->getPost('middle_name');
        }

        if (Yii::app()->request->getPost('gender')) {
            $freeuserObj->gender = Yii::app()->request->getPost('gender');
        }

        if (Yii::app()->request->getPost('nick_name')) {
            $freeuserObj->nick_name = Yii::app()->request->getPost('nick_name');
        }

        if (Yii::app()->request->getPost('user_type')) {
            $freeuserObj->user_type = Yii::app()->request->getPost('user_type');
        }

        if (Yii::app()->request->getPost('medium')) {
            $freeuserObj->medium = Yii::app()->request->getPost('medium');
        }

        if (Yii::app()->request->getPost('country')) {
            $freeuserObj->tds_country_id = Yii::app()->request->getPost('country');
        }

        if (Yii::app()->request->getPost('district')) {
            $freeuserObj->district = Yii::app()->request->getPost('district');
        }

        if (Yii::app()->request->getPost('grade_ids')) {
            $freeuserObj->grade_ids = Yii::app()->request->getPost('grade_ids');
        }

        if (Yii::app()->request->getPost('mobile_no')) {
            $freeuserObj->mobile_no = Yii::app()->request->getPost('mobile_no');
        }

        if (Yii::app()->request->getPost('dob')) {
            $freeuserObj->dob = Yii::app()->request->getPost('dob');
        }

        if (Yii::app()->request->getPost('school_name')) {
            $freeuserObj->school_name = Yii::app()->request->getPost('school_name');
        }

        if (Yii::app()->request->getPost('location')) {
            $freeuserObj->location = Yii::app()->request->getPost('location');
        }

        if (Yii::app()->request->getPost('teaching_for')) {
            $freeuserObj->teaching_for = Yii::app()->request->getPost('teaching_for');
        }

        if (Yii::app()->request->getPost('occupation')) {
            $freeuserObj->occupation = Yii::app()->request->getPost('occupation');
        }

        if (!$freeuserObj->save()) {
            $all_errors = $freeuserObj->errors;
            Yii::app()->cache->set("all_erros", $all_errors, 86400);

            $response['status']['code'] = 400;
            $response['status']['msg'] = $all_errors;

            echo CJSON::encode($response);
            Yii::app()->end();
        }

        $folderObj = new UserFolder();
        $folderObj->createGoodReadFolder($freeuserObj->id);

        $response['status']['code'] = 200;
        $response['status']['msg'] = "Successfully Saved";

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actiongetErrors() {

        $response = Yii::app()->cache->get("all_erros");
        print_r($response);
    }

    public function actionUpdateProfilePaidUser() {
        $freeuserObj = new Freeusers();
        $paid_id = Yii::app()->request->getPost('paid_id');
        $school_id = Yii::app()->request->getPost('paid_school_id');
        $paid_password = Yii::app()->request->getPost('paid_password');
        $first_name = Yii::app()->request->getPost('first_name');

        if ($school_id && $paid_id && $user_id = $freeuserObj->getFreeuserPaid($paid_id, $school_id)) {
            $freeuserObj = $freeuserObj->findByPk($user_id);

            if ($paid_password) {
                $freeuserObj->salt = md5(uniqid(rand(), true));
                $freeuserObj->password = $this->encrypt($paid_password, $freeuserObj->salt);
            }

            if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['name'])) {
                $main_dir = Settings::$image_path . 'upload/free_user_profile_images/';
                $uploads_dir = Settings::$main_path . 'upload/free_user_profile_images/';
                $tmp_name = $_FILES["profile_image"]["tmp_name"];
                $name = "file_" . $user_id . "_" . time() . "_" . str_replace(" ", "-", $_FILES["profile_image"]["name"]);

                move_uploaded_file($tmp_name, "$uploads_dir/$name");
                $freeuserObj->profile_image = $main_dir . $name;
            }

            if ($paid_password)
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
        else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }


        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionUpdateProfile() {
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

        if ($user_id) {
            $freeuserObj = new Freeusers();
            $freeuserObj = $freeuserObj->findByPk($user_id);
            if ($paid_id)
                $freeuserObj->paid_id = $paid_id;
            if ($paid_username)
                $freeuserObj->paid_username = $paid_username;
            if ($paid_password)
                $freeuserObj->paid_password = $paid_password;
            if ($paid_school_id)
                $freeuserObj->paid_school_id = $paid_school_id;
            if ($paid_school_code)
                $freeuserObj->paid_school_code = $paid_school_code;
            if ($paid_password) {
                $freeuserObj->salt = md5(uniqid(rand(), true));
                $freeuserObj->password = $this->encrypt($paid_password, $freeuserObj->salt);
            }

            if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['name'])) {
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
        else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }


        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionCheckAuth() {
        if (isset($_POST) && !empty($_POST)) {
            $user_id = Yii::app()->request->getPost('user_id');
            $auth_id = Yii::app()->request->getPost('auth_id');
            $activation_code = Yii::app()->request->getPost('activation_code');
            if ($user_id && $auth_id) {
                $authobj = new Userauth();
                if ($authobj->getAuth($user_id, $auth_id)) {
                    if ($activation_code) {

                        if ($authobj->getAuth($user_id, $auth_id, $activation_code)) {
                            $response['status']['code'] = 200;
                            $response['status']['msg'] = "valid";
                        } else {
                            $response['status']['code'] = 400;
                            $response['status']['msg'] = "Bad Request";
                        }
                    } else {
                        $response['status']['code'] = 200;
                        $response['status']['msg'] = "Success";
                    }
                } else {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request";
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

    public function actionlogout() {
        $gcm_id = Yii::app()->request->getPost('gcm_id');
        $user_secret = Yii::app()->request->getPost('user_secret');
        $gcmobj = new Gcm();
        $gcm_id = $gcmobj->getGcm($gcm_id);
        if ($gcm_id) {
            $user_gcm = new UserGcm();
            $user_gcm->deleteUserGcmByGcmId($gcm_id);
//            $usergcm = $user_gcm->getUserGcm($gcm_id, Yii::app()->user->id);
//            if ($usergcm) {
//                $user_gcm_to_delete = $user_gcm->findByPk($usergcm);
//                $user_gcm_to_delete->delete();
//            }
        }
        $response['status']['code'] = 200;
        $response['status']['msg'] = "Logout Successfull";

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSetFreeUserId() {
        $id = Yii::app()->request->getParam('id');
        Yii::app()->user->setState("free_id_flash", $id);
    }

    public function actionUnsetFreeUserId() {
        Yii::app()->user->setState("free_id_flash", null);
    }

    public function actionAuth() {

        $username = '';
        $password = '';

        if (isset($_POST) && !empty($_POST)) {

//            if (!isset($_POST['user_secret'])) {

            if (!isset($_POST['username']) || !isset($_POST['password']) || empty($_POST['username']) || empty($_POST['password'])) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = 'Bad Request.';

                echo CJSON::encode($response);
                Yii::app()->end();
            }
//            }

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

                    //check gcm and add gcm
                    $gcm_id = Yii::app()->request->getPost('gcm_id');
                    if ($gcm_id) {
                        $gcmobj = new Gcm();
                        $gcm_id = $gcmobj->getGcm($gcm_id);
                        if ($gcm_id) {
                            $user_gcm = new UserGcm();
                            $user_gcm->deleteUserGcmByGcmId($gcm_id);
//                            $usergcm = $user_gcm->getUserGcm($gcm_id, Yii::app()->user->id);
//                            if (!$usergcm) {
                            $user_gcm->user_id = Yii::app()->user->id;
                            $user_gcm->gcm_id = $gcm_id;
                            $user_gcm->save();
//                            }
                        }
                    }

                    $school_obj = new Schools();
                    $school_details = $school_obj->findByPk(Yii::app()->user->schoolId);
                    $school_code = $school_details->code;


                    $userpaidobj = new Users();
                    $userpaidData = $userpaidobj->findByPk(Yii::app()->user->id);

                    $data = $free_user->login($username, $password, true);

                    if ($data) {
                        $user_type_edit = 1;
                        $freedata = $free_user->findByPk($data->id);
                        if (Yii::app()->user->isStudent) {
                            $freedata->user_type = 2;
                        } else if (Yii::app()->user->isParent) {
                            $freedata->user_type = 4;
                        } else {
                            $freedata->user_type = 3;
                        }
                        $freedata->school_name = $school_details->name;
                        $freedata->save();

                        $response['data']['can_play_spellingbee'] = Settings::can_play_spelling_bee($freedata);



                        $folderObj = new UserFolder();

                        $folderObj->createGoodReadFolder($data->id);
                        $response['data']['free_id'] = $data->id;
                        $response['data']['user'] = $free_user->getUserInfo($data->id, Yii::app()->user->schoolId, $freedata->user_type);
                    } else {

                        Yii::app()->db->createCommand()->delete('tds_free_users', 'email = (?) OR paid_username = (?) OR paid_id = (?)', array($username, $username, Yii::app()->user->id));

                        $free_user->paid_id = Yii::app()->user->id;
                        $free_user->paid_username = $username;
                        $free_user->paid_password = $password;
                        $free_user->paid_school_id = Yii::app()->user->schoolId;
                        $free_user->paid_school_code = $school_code;
                        $free_user->salt = md5(uniqid(rand(), true));
                        $free_user->password = $this->encrypt($password, $free_user->salt);

                        $free_user->first_name = $userpaidData->first_name;
                        $free_user->last_name = $userpaidData->last_name;

                        $free_user->email = $username;

                        $free_user->nick_name = 1;
                        if (Yii::app()->user->isStudent) {
                            $free_user->user_type = 2;
                            $stdobj = new Students();
                            $stddata = $stdobj->findByPk(Yii::app()->user->profileId);
                            if ($stddata) {
                                if (isset($stddata->gender) && $stddata->gender == "m") {
                                    $free_user->gender = 1;
                                } else {
                                    $free_user->gender = 2;
                                }
                                if (isset($stddata->date_of_birth) && $stddata->date_of_birth) {
                                    $free_user->dob = $stddata->date_of_birth;
                                }
                                $free_user->tds_country_id = $stddata->nationality_id;
                            }
                        } else if (Yii::app()->user->isParent) {
                            $stdobj = new Guardians();
                            $stddata = $stdobj->findByPk(Yii::app()->user->profileId);
                            if ($stddata) {
                                if (isset($stddata->dob) && $stddata->dob) {
                                    $free_user->dob = $stddata->dob;
                                }

                                $free_user->tds_country_id = $stddata->country_id;
                            }
                            $free_user->user_type = 4;
                        } else {
                            $stdobj = new Employees();
                            $stddata = $stdobj->findByPk(Yii::app()->user->profileId);
                            if ($stddata) {
                                if (isset($stddata->gender) && $stddata->gender == "m") {
                                    $free_user->gender = 1;
                                } else {
                                    $free_user->gender = 2;
                                }
                                if (isset($stddata->date_of_birth) && $stddata->date_of_birth) {
                                    $free_user->dob = $stddata->date_of_birth;
                                }
                                $free_user->tds_country_id = $stddata->nationality_id;
                            }
                            $free_user->user_type = 3;
                        }
                        $free_user->school_name = $school_details->name;


                        $free_user->save();

                        $response['data']['can_play_spellingbee'] = Settings::can_play_spelling_bee($free_user);



                        $folderObj = new UserFolder();

                        $folderObj->createGoodReadFolder($free_user->id);
                        $response['data']['free_id'] = $free_user->id;
                        $response['data']['user'] = $free_user->getUserInfo($free_user->id, Yii::app()->user->schoolId, $free_user->user_type);
                    }
                    $response['data']['user_type'] = 1;
                    $freeschool = new School();
                    $response['data']['paid_user'] = $freeschool->getSchoolPaidCoverLogo(Yii::app()->user->schoolId);
                    $response['data']['paid_user']['is_first_login'] = $userpaidData->is_first_login;

                    $userpaidData->is_first_login = 0;
                    $userpaidData->save();

                    $response['data']['paid_user']['id'] = Yii::app()->user->id;
                    $response['data']['paid_user']['is_admin'] = Yii::app()->user->isAdmin;
                    $response['data']['paid_user']['is_student'] = Yii::app()->user->isStudent;

                    $objreminder = new Reminders();
                    $response['data']['paid_user']['unread_total'] = $objreminder->getReminderTotalUnread(Yii::app()->user->id);




                    if (Yii::app()->user->isStudent) {
                        $response['data']['paid_user']['batch_id'] = Yii::app()->user->batchId;

                        $exam_category = new ExamGroups;
                        $exam_category = $exam_category->getExamCategory(Yii::app()->user->schoolId, Yii::app()->user->batchId, 3);

                        $response['data']['paid_user']['terms'] = array();

                        if ($exam_category)
                            $response['data']['paid_user']['terms'] = $exam_category;
                    }


                    $response['data']['paid_user']['profile_id'] = Yii::app()->user->profileId;
                    $response['data']['paid_user']['is_parent'] = Yii::app()->user->isParent;
                    $response['data']['paid_user']['is_teacher'] = Yii::app()->user->isTeacher;
                    $response['data']['paid_user']['school_id'] = Yii::app()->user->schoolId;
                    $response['data']['paid_user']['school_type'] = $school_obj->getschooltype(Yii::app()->user->schoolId);
                    $response['data']['paid_user']['school_name'] = $school_details->name;
                    
                    
                    $configuration = new Configurations();
                    
                    $response['data']['paid_user']['routine_shortcode'] = (int)$configuration->getValue("RoutineViewTeacherShortCode");
                    $response['data']['paid_user']['routine_period'] = (int)$configuration->getValue("RountineViewPeriodNameNoTiming");



                    if (is_array($user_paid_login_data)) {
                        $username = $user_paid_login_data[1];
                        $password = $user_paid_login_data[0];
                    }



                    $attendance = new Attendances();
                    $response['data']['weekend'] = $attendance->getWeekend(Yii::app()->user->schoolId);

                    $response['data']['children'] = array();
                    if (Yii::app()->user->isParent) {
                        $response['data']['children'] = $user->studentList(Yii::app()->user->profileId);
                        $gurdianModel = new Guardians();
                        $gurdian = $gurdianModel->findBypk(Yii::app()->user->profileId);
                        $response['data']['paid_user']['relation'] = $gurdian->relation;
                    }

                    if (!isset($user_secret)) {
                        $response['data']['paid_user']['secret'] = Yii::app()->user->user_secret;
                    }

                    $response['data']['session'] = Yii::app()->session->getSessionID();
                    //$fedenatoken = Settings::getFedenaToken($school_code, $username, $password);


                    Yii::app()->user->setState("school_code", $school_code);

//                    if(isset($fedenatoken->access_token))
//                    {
//                        Yii::app()->user->setState("access_token_user",$fedenatoken->access_token);
//                    }
//                    else
//                    {
//                        $response['data'] = array();
//                        $response['status']['code'] = 404;
//                        $response['status']['msg'] = "USER_NOT_FOUND";
//                    } 

                    Yii::app()->user->setState("free_id", $response['data']['free_id']);

                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "USER_FOUND";
                } else if ($data = $free_user->login($username, $password)) {
                    $folderObj = new UserFolder();

                    $folderObj->createGoodReadFolder($data->id);
                    //for paid
//                    $response['data']['paid_user'] = array();
//                    $response['data']['weekend'] = array();
//                    $response['data']['children'] = array();
//                    $response['data']['session'] =  Yii::app()->session->getSessionID();
                    //for paid

                    $response['data']['can_play_spellingbee'] = Settings::can_play_spelling_bee($data);




                    Yii::app()->user->setState("free_id", $data->id);
                    $response['data']['user_type'] = 0;
                    $response['data']['free_id'] = $data->id;
                    $response['data']['user'] = $free_user->getUserInfo($data->id);
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "USER_FOUND";
                } else {
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

    public function actionDelete_by_paid_id() {

        $paid_user_id = \trim(Yii::app()->request->getPost('paid_id'));
        $paid_school_id = \trim(Yii::app()->request->getPost('paid_school_id'));

        $response = array();
        if (empty($paid_user_id) || empty($paid_school_id)) {

            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";

            echo CJSON::encode($response);
            Yii::app()->end();
        }

        $obj_free_user = new Freeusers();
        $obj_free_user_data = $obj_free_user->getFreeuserPaid($paid_user_id, $paid_school_id);

        if (!$obj_free_user_data) {

            $response['status']['code'] = 404;
            $response['status']['msg'] = "User Not Found";

            echo CJSON::encode($response);
            Yii::app()->end();
        }

        if ($obj_free_user->deleteByPk($obj_free_user_data)) {

            $response['status']['code'] = 200;
            $response['status']['msg'] = "User Successfully Deleted";

            echo CJSON::encode($response);
            Yii::app()->end();
        } else {

            $all_errors = $obj_free_user->errors;
            Yii::app()->cache->set("all_erros", $all_errors, 86400);
        }
    }

    private function sendForgotPasswordMail($obj_user) {
        $this->layout = FALSE;
        $this->pageHeader = 'Classtune Reset User Password';

        $reset_password_url = Settings::$classtune_domain_name . 'reset-password-classtune?token=' . $obj_user->reset_password_code;
        $body = $this->renderPartial('//mail/_reset_password', array(
            'user_data' => $obj_user,
            'reset_password_url' => $reset_password_url,
                ), TRUE);

        $mail = new YiiMailer();
        $mail->setSmtp('host.champs21.com', 465, 'ssl', true, 'info@champs21.com', '174097@hM&^256');
        $mail->setTo(array($obj_user->email => $obj_user->first_name . ' ' . $obj_user->last_name));
        $mail->setSubject('Classtune Reset User Password');
        $mail->setFrom('info@classtune.com', 'Team Classtune ');

        $mail->setBody($body);
        $mail->send();
    }

}
