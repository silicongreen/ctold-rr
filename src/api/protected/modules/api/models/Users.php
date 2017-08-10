<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property integer $admin
 * @property integer $student
 * @property integer $employee
 * @property string $hashed_password
 * @property string $salt
 * @property string $reset_password_code
 * @property string $reset_password_code_until
 * @property string $created_at
 * @property string $updated_at
 * @property integer $parent
 * @property integer $is_first_login
 * @property integer $is_deleted
 * @property string $google_refresh_token
 * @property string $google_access_token
 * @property string $google_expired_at
 * @property integer $school_id
 * @property string $ud_id
 * @property string $api_token
 */
class Users extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'users';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('admin, student, employee, parent, is_first_login, is_deleted, school_id', 'numerical', 'integerOnly' => true),
            array('username, first_name, last_name, email, hashed_password, salt, reset_password_code, google_refresh_token, google_access_token, google_expired_at', 'length', 'max' => 255),
            array('reset_password_code_until, created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, username, first_name, last_name, email, admin, student, employee, hashed_password, salt, reset_password_code, reset_password_code_until, created_at, updated_at, parent, is_first_login, is_deleted, google_refresh_token, google_access_token, google_expired_at, school_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'studentDetails' => array(self::HAS_ONE, 'Students', 'user_id',
                'select' => 'studentDetails.id, studentDetails.admission_no, studentDetails.class_roll_no, studentDetails.first_name, studentDetails.middle_name, studentDetails.last_name,studentDetails.batch_id, studentDetails.date_of_birth',
                'joinType' => 'LEFT JOIN',
            ),
            'employeeDetails' => array(self::HAS_ONE, 'Employees', 'user_id',
                'select' => 'employeeDetails.id, employeeDetails.first_name, employeeDetails.middle_name, employeeDetails.last_name, employeeDetails.date_of_birth',
                'joinType' => 'LEFT JOIN',
            ),
            'guardiansDetails' => array(self::HAS_ONE, 'Guardians', 'user_id',
                'select' => 'guardiansDetails.id, guardiansDetails.first_name, guardiansDetails.last_name, guardiansDetails.dob',
                'joinType' => 'LEFT JOIN',
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'username' => 'Username',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'admin' => 'Admin',
            'student' => 'Student',
            'employee' => 'Employee',
            'hashed_password' => 'Hashed Password',
            'salt' => 'Salt',
            'reset_password_code' => 'Reset Password Code',
            'reset_password_code_until' => 'Reset Password Code Until',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'parent' => 'Parent',
            'is_first_login' => 'Is First Login',
            'is_deleted' => 'Is Deleted',
            'google_refresh_token' => 'Google Refresh Token',
            'google_access_token' => 'Google Access Token',
            'google_expired_at' => 'Google Expired At',
            'school_id' => 'School',
            'ud_id' => 'Unique Device Id',
            'api_token' => 'API Verification Token',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('first_name', $this->first_name, true);
        $criteria->compare('last_name', $this->last_name, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('admin', $this->admin);
        $criteria->compare('student', $this->student);
        $criteria->compare('employee', $this->employee);
        $criteria->compare('hashed_password', $this->hashed_password, true);
        $criteria->compare('salt', $this->salt, true);
        $criteria->compare('reset_password_code', $this->reset_password_code, true);
        $criteria->compare('reset_password_code_until', $this->reset_password_code_until, true);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('parent', $this->parent);
        $criteria->compare('is_first_login', $this->is_first_login);
        $criteria->compare('is_deleted', $this->is_deleted);
        $criteria->compare('google_refresh_token', $this->google_refresh_token, true);
        $criteria->compare('google_access_token', $this->google_access_token, true);
        $criteria->compare('google_expired_at', $this->google_expired_at, true);
        $criteria->compare('school_id', $this->school_id);
        $criteria->compare('ud_id', $this->ud_id);
        $criteria->compare('api_token', $this->api_token);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Users the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * Logs in the user using the given username and password in the model.
     * @return boolean whether login is successful
     */
    public function login() {
        $user = $this->checkUser();

        if ($user !== false) {
            $userIdentity = new UserIdentity($this, $user);

            if ($userIdentity->authenticate()) {
                $duration = 3153600000; // 1 Yr
                Yii::app()->user->login($userIdentity, $duration);

                return true;
            } else {
                return false;
            }
        } else {
            $freeuserobj = new Freeusers();
            $data = $freeuserobj->login($this->username,$this->hashed_password);
            if($data)
            {
                if($data->paid_password && $data->paid_username)
                {
                   $this->username = $data->paid_username;
                   $this->hashed_password = $data->paid_password;
                   $user = $this->checkUser();
                   if ($user !== false) {
                        $userIdentity = new UserIdentity($this, $user);

                        if ($userIdentity->authenticate()) {
                            $duration = 3153600000; // 1 Yr
                            Yii::app()->user->login($userIdentity, $duration);

                            return array($data->paid_password,$data->paid_username);
                        } else {
                            return false;
                        }
                    }
                   
                }    

            } 
            else
            {
                return false;
            }    
            //Yii::app()->user->status_code = 404;
            //Yii::app()->user->status_msg = 'User Not Found';

            
        }
    }
    
     public function checkStudentExists($user_id,$school_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "id";
        $criteria->compare('t.username', $user_id);
        $criteria->compare('t.school_id', $school_id);
        $criteria->compare('student', 1);
        $user = $this->with('studentDetails')->find($criteria);
 
        if($user)
        {
            return $user;
        }
        return FALSE;
    } 
    
    public function getUser($user_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "*";
        $criteria->compare('username', $user_id);
        $user = $this->find($criteria);
        if($user)
        {
            return $user;
        }
        return FALSE;
    } 
    
    public function checkUserExists($user_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "id";
        $criteria->compare('username', $user_id);
        $user = $this->find($criteria);
        if($user)
        {
            return FALSE;
        }
        return TRUE;
    }        

    /**
     * Finds user by provided username.
     * @return If found then returns user object else returns false.
     */
    public function checkUser($username = '', $api_token = '') {
        $username = (empty($username)) ? $this->username : $username;
        $api_token = (empty($api_token)) ? $this->api_token : $api_token;

        $criteria = new CDbCriteria;
        $criteria->select = 'id, username, hashed_password, salt, first_name, last_name, email, admin, student, employee, parent, school_id, is_first_login, api_token, ud_id';

        if (!empty($api_token)) {
            $criteria->compare('api_token', $api_token);
        } else {
            $criteria->compare('username', $username);
        }

        $criteria->addCondition("(is_deleted = 0 OR parent=1) and is_approved=1");
      

        $user = $this->find($criteria);

        if (!empty($user)) {

            if ($this->checkFirstLogin($user)) {
                $user->ud_id = $this->ud_id;
                $user->api_token = $this->generateTkoen($user);
                $user->update();
            }

            return $user;
        }

        return false;
    }

    private function generateTkoen($user) {
        $salt = time() . $user->id . $user->salt;
        $token = hash('sha256', $salt);
        return $token;
        exit;
    }

    private function checkFirstLogin($obj_user) {
        $first_login_from_api = false;

        if (empty($obj_user->api_token)) {
            $first_login_from_api = true;
        }

        return $first_login_from_api;
    }

    /**
     * Finds user by provided date.
     * @return If found then returns user object else returns false.
     */
    public function getBirthDays($date = '', $school_id = '') {
        $date = (!empty($date)) ? date('m-d', strtotime($date)) : date('m-d', time());
        $school_id = (!empty($school_id)) ? $school_id : Yii::app()->user->schoolId;

        $criteria = new CDbCriteria();
        $criteria->select = 't.id, admin, employee, student, parent';
        $criteria->compare("DATE_FORMAT(studentDetails.date_of_birth, '%m-%d')", $date);
        $criteria->compare("DATE_FORMAT(employeeDetails.date_of_birth, '%m-%d')", $date, false, 'OR');
        $criteria->compare("DATE_FORMAT(guardiansDetails.dob, '%m-%d')", $date, false, 'OR');
        $criteria->compare('t.school_id', $school_id);
        $criteria->compare('t.is_deleted', 0);

        $data = $this->with('studentDetails', 'employeeDetails', 'guardiansDetails')->findAll($criteria);

        if (!empty($data)) {
            return $formatted_data = $this->formatData($data);
        }

        return false;
    }

    public function getSudent($id) {
        $data = $this->with('studentDetails')->findByPk($id);

        if (!empty($data)) {
            return $data;
        }

        return false;
    }
    
    public function studentListParent($profile_id)
    {
        $other_student = new GuardianStudent();
        $other_students = $other_student->getChildren($profile_id);

        $user_array = array();

        if ($other_students) {
            $i = 0;
            foreach ($other_students as $value) {
                $middle_name = (!empty($value['students']->middle_name)) ? $value['students']->middle_name . ' ' : '';
                $user_array[$i]['full_name'] = rtrim($value['students']->first_name . ' ' . $middle_name . $value['students']->last_name);
                $user_array[$i]['batch'] = $value['students']['batchDetails']['courseDetails']->course_name." ".$value['students']['batchDetails']->name;
               
                $i++;
            }
        }

        return $user_array;
    }
    public function getEmployeeTerm($term)
    {
         $criteria = new CDbCriteria;
         $criteria->select = 't.*';
         $criteria->compare('t.school_id', Yii::app()->user->schoolId);
         $criteria->addCondition("( (t.first_name like '%".$term."%' or t.last_name like '%".$term."%') and t.employee=1 )");
         $criteria->compare('t.is_deleted', 0);
         $criteria->order = "CASE 
               WHEN t.first_name like '". $term."%' THEN 0
               WHEN t.first_name like '% %".$term."% %' THEN 1
               WHEN t.first_name like '%".$term."' THEN 2
               WHEN t.last_name like '".$term."%' THEN 3
               WHEN t.last_name like '% %".$term."% %' THEN 4
               WHEN t.last_name like '%".$term."' THEN 5
               ELSE 6 END, t.first_name";
         $data = $this->with('employeeDetails')->findAll($criteria);
         if (!empty($data)) {
          
            return $formatted_data = $this->formatDataStdEmp($data);
         }

         return array();
         
         
         
    } 
    public function getStudentTerm($term)
    {
         $criteria = new CDbCriteria;
         $criteria->select = 't.*';
         $criteria->compare('t.school_id', Yii::app()->user->schoolId);
         $criteria->addCondition("( (t.first_name like '%".$term."%' or t.last_name like '%".$term."%') and t.student=1  )");
         $criteria->compare('t.is_deleted', 0);
         $criteria->order = "CASE 
               WHEN t.first_name like '".$term."%' THEN 0
               WHEN t.first_name like '% %".$term."% %' THEN 1
               WHEN t.first_name like '%".$term."' THEN 2
               WHEN t.last_name like '".$term."%' THEN 3
               WHEN t.last_name like '% %".$term."% %' THEN 4
               WHEN t.last_name like '%".$term."' THEN 5
               ELSE 6 END, t.first_name";
         $data = $this->with('studentDetails')->findAll($criteria);
         if ($data) {
          
            return $formatted_data = $this->formatDataStdEmp($data);
         }

         return array();
         
         
         
    }        
   

    public function studentList($profile_id) 
    {
        $other_student = new GuardianStudent();
        $other_students = $other_student->getChildren($profile_id);

        $user_array = array();

        if ($other_students) {
            $i = 0;
            foreach ($other_students as $value) {
                $middle_name = (!empty($value['students']->middle_name)) ? $value['students']->middle_name . ' ' : '';

                $exam_category = new ExamGroups;
                $exam_category = $exam_category->getExamCategory($value['students']->school_id, $value['students']->batch_id, 3);
                
                
                
                $freobj = new Freeusers();
                $fUserInfo = $freobj->getFreeuserPaid($value['students']->user_id,$value['students']->school_id);
                $profile_image = "";

                if ($fUserInfo)
                {
                    $profile_image = Settings::getProfileImage($fUserInfo);
                }
                //$profile_image = $freobj->getUserImage($value['students']->user_id);
                
                
                $schoolobj = new Schools();
                $schoo_data = $schoolobj->findByPk($value['students']->school_id);

                $user_array[$i]['id'] = $value['students']->user_id;
                $user_array[$i]['profile_id'] = $value['students']->id;
                $user_array[$i]['profile_image'] = "";
                if(isset($profile_image))
                {
                   $user_array[$i]['profile_image'] = $profile_image; 
                }
                $user_array[$i]['full_name'] = rtrim($value['students']->first_name . ' ' . $middle_name . $value['students']->last_name);
                $user_array[$i]['school_id'] = $value['students']->school_id;
                $user_array[$i]['batch_id'] = $value['students']->batch_id;
                $user_array[$i]['school_name'] = $schoo_data->name;
                $user_array[$i]['batch_name'] = $value['students']['batchDetails']->name;
                $user_array[$i]['course_name'] = $value['students']['batchDetails']['courseDetails']->course_name;
                $user_array[$i]['section_name'] = $value['students']['batchDetails']['courseDetails']->section_name;
                $user_array[$i]['terms'] = $exam_category;
                $i++;
            }
        }

        return $user_array;
    }
    public function formatDataStdEmp($obj_data) {
        $ar_formatted_data = array();
        if($obj_data)
        {
            foreach ($obj_data as $row) 
            {
                

                if ($row->student == 1) {
                    $ar_key = 'studentDetails';
                }

                if ($row->employee == 1) {
                    $ar_key = 'employeeDetails';
                }
                if(isset($row[$ar_key]->id))
                {
                    $middle_name = (!empty($row[$ar_key]->middle_name)) ? $row[$ar_key]->middle_name . ' ' : '';
                    $_data['profile_id'] = $row[$ar_key]->id;
                    $_data['full_name'] = $row[$ar_key]->first_name . ' ' . $middle_name. $row[$ar_key]->last_name;
                    $ar_formatted_data[] = $_data;
                }

            }
        }
        return $ar_formatted_data;
    }

    public function formatData($obj_data) {

        $ar_formatted_data = array();

        foreach ($obj_data as $row) {
            if ($row->admin == 1) {
                $user_type = 'Admin';
                $ar_key = 'employeeDetails';
            }

            if ($row->student == 1) {
                $user_type = 'Student';
                $ar_key = 'studentDetails';
            }

            if ($row->employee == 1) {
                $user_type = 'Teacher';
                $ar_key = 'employeeDetails';
            }

            if ($row->parent == 1) {
                $user_type = 'Guardian';
                $ar_key = 'guardiansDetails';
            }

            $middle_name = (!empty($row[$ar_key]->middle_name)) ? $row[$ar_key]->middle_name . ' ' : '';
            $dob_attr_name = ($ar_key == 'guardiansDetails') ? 'dob' : 'date_of_birth';

            $_data['profile_id'] = $row[$ar_key]->id;
            $_data['type'] = $user_type;
            $_data['full_name'] = rtrim($row[$ar_key]->first_name . ' ' . $middle_name . $row[$ar_key]->last_name);
            $_data['profile_image_url'] = '';
            $_data['dob'] = $row[$ar_key]->$dob_attr_name;

            $ar_formatted_data[] = $_data;
        }

        return $ar_formatted_data;
    }

}
