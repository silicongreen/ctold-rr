<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

    /**
     * @var object $model
     */
    private $__model;

    /**
     * @var integer user id
     */
    private $_id;

    /**
     * @var string username from api request
     */
    private $__api_username;

    /**
     * @var string password from api request
     */
    private $__api_password;

    /**
     * @var string username from DB
     */
    private $__ar_username;

    /**
     * @var string password from DB
     */
    private $__ar_password;

    /**
     * @var string salt from DB
     */
    private $__ar_salt;

    /**
     * @var string token from api request
     */
    private $__api_user_token;

    /**
     * @var string token from api DB
     */
    private $__ar_user_token;

    /**
     * @var boolean token from api DB
     */
    private $__b_paid_user;

    public function __construct($api_model, $ar_model, $b_paid_user = true) {

        // username and password from api request
        $this->__api_username = ($b_paid_user) ? $api_model->username : $api_model->email;
        $this->__api_password = ($b_paid_user) ? $api_model->hashed_password : $api_model->password;
        $this->__api_user_token = ($b_paid_user) ? $api_model->api_token : NULL;

        // username and password from db
        $this->__ar_username = ($b_paid_user) ? $ar_model->username : $ar_model->email;
        $this->__ar_password = ($b_paid_user) ? $ar_model->hashed_password : $ar_model->password;
        $this->__ar_salt = $ar_model->salt;
        $this->__ar_user_token = ($b_paid_user) ? $ar_model->api_token : NULL;

        $this->__model = $ar_model;
        $this->__b_paid_user = $b_paid_user;
    }

    /**
     * Authenticates a user.
     * The example implementation makes sure if the username and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * @return boolean whether authentication succeeds.
     */
    public function authenticate() {
        
        if ($this->__b_paid_user) {
            
            if ($this->__api_user_token === $this->__ar_user_token) {
                $this->_id = $this->__model->id;
                $this->setState('status_code', 200);
                $this->setState('status_msg', 'OK');
                $this->setState('user_secret', $this->__model->api_token);
                $this->setState('isAdmin', $this->isAdmin());
                $this->setState('isStudent', $this->isStudent());

                $profile = $this->loadProfile();

                if ($profile !== false) {

                    $this->setState('profileId', $profile['id']);

                    if ($this->isStudent()) {
                        $this->setState('batchId', $profile['batch_id']);
                    }
                }

                $this->setState('isParent', $this->isParent());
                $this->setState('isTeacher', $this->isTeacher());
                $this->setState('schoolId', $this->schoolId());

                $this->errorCode = self::ERROR_NONE;

                return true;
            }

            if (strtolower($this->__api_username) !== strtolower($this->__ar_username)) {
                $this->setState('status_code', 404);
                $this->setState('status_msg', 'User Not Found');

                $this->errorCode = self::ERROR_USERNAME_INVALID;
            } elseif ($this->__ar_password !== sha1($this->__ar_salt . $this->__api_password)) {
                $this->setState('status_code', 403);
                $this->setState('status_msg', 'Access Denied. Invalid Username or Password.');

                $this->errorCode = self::ERROR_PASSWORD_INVALID;
            } else {
                $this->_id = $this->__model->id;
                $this->setState('status_code', 200);
                $this->setState('status_msg', 'OK');
                $this->setState('user_secret', $this->__model->api_token);
                $this->setState('isAdmin', $this->isAdmin());
                $this->setState('isStudent', $this->isStudent());
                $this->setState('isParent', $this->isParent());
                $this->setState('isTeacher', $this->isTeacher());
                $this->setState('schoolId', $this->schoolId());

                $profile = $this->loadProfile();

                if ($profile !== false) {

                    $this->setState('profileId', $profile['id']);

                    if ($this->isStudent()) {
                        $this->setState('batchId', $profile['batch_id']);
                    }
                }

                $this->errorCode = self::ERROR_NONE;

                return true;
            }
            
        } else {
            
            $this->_id = $this->__model->id;
            $this->setState('status_code', 200);
            $this->setState('status_msg', 'OK');
            $this->setState('free_id', $this->_id);

            $this->errorCode = self::ERROR_NONE;

            return true;
        }

        return false;
    }

    public function getId() {
        return $this->_id;
    }

    private function isAdmin() {
        return ($this->__model->admin == 1) ? true : false;
    }

    private function isStudent() {
        return ($this->__model->student == 1) ? true : false;
    }

    private function isParent() {
        return ($this->__model->parent == 1) ? true : false;
    }

    private function isTeacher() {
        return ($this->__model->employee == 1) ? true : false;
    }

    private function schoolId() {
        return $this->__model->school_id;
    }

    private function loadProfile() {

        $table_name = 'employees';
        $select = 'id';

        if ($this->__model->student) {
            $table_name = 'students';
            $select .= ', batch_id';
        }

        if ($this->__model->parent) {
            $table_name = 'guardians';
        }

        $profile = Yii::app()->db->createCommand()->select($select)->from($table_name)->where('user_id = :uid', array(':uid' => $this->_id))->queryRow();

        return $profile;
    }

}
