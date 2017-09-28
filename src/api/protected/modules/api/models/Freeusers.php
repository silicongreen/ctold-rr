<?php

/**
 * This is the model class for table "events".
 *
 * The followings are the available columns in table 'events':
 * @property integer $id
 * @property integer $event_category_id
 * @property string $title
 * @property string $description
 * @property string $start_date
 * @property string $end_date
 * @property integer $is_common
 * @property integer $is_holiday
 * @property integer $is_exam
 * @property integer $is_due
 * @property string $created_at
 * @property string $updated_at
 * @property integer $origin_id
 * @property string $origin_type
 * @property integer $school_id
 */
class Freeusers extends CActiveRecord {

    public $num_rows = 0;
    public $check_service = FALSE;
    public $check_id = 259;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'tds_free_users';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_type, email, password', 'required', 'on' => 'insert')
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
      
        return array(
            'id' => 'ID',
            'username' => 'username',
            'password' => 'password',
            'email' => 'email',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_neme' => 'Last Name',
            'nick_name' => 'Nick Name',
            'user_type' => 'Type',
            'gender' => 'Gender',
            'profile_image'=>"Profile Image",
            'for_all' => 'For All',
            'tds_country_id' => 'Country',
            'district' => 'District'
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
        $criteria->compare('event_category_id', $this->event_category_id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('start_date', $this->start_date, true);
        $criteria->compare('end_date', $this->end_date, true);
        $criteria->compare('is_common', $this->is_common);
        $criteria->compare('is_holiday', $this->is_holiday);
        $criteria->compare('is_exam', $this->is_exam);
        $criteria->compare('is_due', $this->is_due);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('origin_id', $this->origin_id);
        $criteria->compare('origin_type', $this->origin_type, true);
        $criteria->compare('school_id', $this->school_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Events the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    public function getFreeuserFb($id) {

        $criteria = new CDbCriteria;
        $criteria->compare('fb_profile_id', $id);

        $data = $this->find($criteria);
       
        if ($data != NULL)
        {
            return $data;
        }
        return false;
    }
    public function login($username,$password, $paid=false)
    {
        if($paid==false)
        {    
            $data = $this->getFreeuser($username);
            if($data)
            {
                $pass = hash('sha512', $data->salt . $password);
                $criteria = new CDbCriteria;
                $criteria->compare('password', $pass);

                $validdata = $this->find($criteria);
                if($validdata)
                {
                    $userIdentity = new UserIdentity($validdata, $validdata, false);

                    if ($userIdentity->authenticate()) {
                        $duration = 3153600000; // 1 Yr
                        Yii::app()->user->login($userIdentity, $duration);
                    }
                    return $validdata;
                }

            } 
        }
        else
        {
            $criteria = new CDbCriteria;
            $criteria->compare('paid_password', $password);
            $criteria->compare('paid_username', $username);

            $validdata = $this->find($criteria);
            if($validdata)
            {
                return $validdata;
            }
            else
            {
                $data = $this->getFreeuser($username);
                if($data)
                {
                    $pass = hash('sha512', $data->salt . $password);
                    $criteria = new CDbCriteria;
                    $criteria->compare('password', $pass);

                    $validdata = $this->find($criteria);
                    if($validdata)
                    {
                        return $validdata;
                    }

                }  
            }
        }    
        return false;
        
    }        
    public function getFreeuserGmail($id) {

        $criteria = new CDbCriteria;
        $criteria->compare('gl_profile_id', $id);

        $data = $this->find($criteria);
       
        if ($data != NULL)
        {
            return $data;
        }
        return false;
    }
    public function getFreeuserByCookie($cookie_value="") 
    {
        if($this->check_service)
        {
            return $this->check_id;
        } 
       
        if (isset($cookie_value) && $cookie_value) 
        {
            $criteria = new CDbCriteria;
            $criteria->compare('cookie_token', $cookie_value);
            $data = $this->find($criteria);
            if ($data != NULL)
            {
               return $data->id;
            }
        }
       
        
        return FALSE;
    }
    public function getFreeuserPaid($paid_id, $school_id) {

        $criteria = new CDbCriteria;
        $criteria->select = '*';
        $criteria->compare('paid_id', $paid_id);
        $criteria->compare('paid_school_id', $school_id);

        $data = $this->find($criteria);
       
        if ($data != NULL)
        {
            return $data->id;
        }
        return false;
    }
    
    public function getFreeuser($email) {

        $criteria = new CDbCriteria;
        $criteria->select = '*';
        $criteria->compare('email', $email);

        $data = $this->find($criteria);
       
        if ($data != NULL)
        {
            return $data;
        }
        return false;
    }
    public function getUserImage($paid_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = '*';
        $criteria->compare('paid_id', $paid_id);

        $value = $this->find($criteria);
        $user_info = array();
        if ($value)
        { 
           $user_info['profile_image'] = $value->profile_image; 
        }
        return $user_info;
    }
    public function getPaidUserInfo($freeuserObj="")
    {
        if(!Yii::app()->user->isGuest && Yii::app()->user->id && ($freeuserObj=="" || $freeuserObj->paid_id==Yii::app()->user->id))
        {
            if ( isset(Yii::app()->user->schoolId) )
            {
                $school_obj  = new Schools();
                $school_details = $school_obj->findByPk(Yii::app()->user->schoolId);
                $school_code = $school_details->code;


                $userpaidobj = new Users();
                $userpaidData = $userpaidobj->findByPk(Yii::app()->user->id);
                $user_info['user_type'] = 1;
                $freeschool = new School();
                $user_info['paid_user'] = $freeschool->getSchoolPaidCoverLogo(Yii::app()->user->schoolId);
                $user_info['paid_user']['is_first_login'] = $userpaidData->is_first_login;

                $userpaidData->is_first_login = 0;
                $userpaidData->save();

                $user_info['paid_user']['id'] = Yii::app()->user->id;
                $user_info['paid_user']['is_admin'] = Yii::app()->user->isAdmin;
                $user_info['paid_user']['is_student'] = Yii::app()->user->isStudent;
                if (Yii::app()->user->isStudent) {
                    $user_info['paid_user']['batch_id'] = Yii::app()->user->batchId;

                    $exam_category = new ExamGroups;
                    $exam_category = $exam_category->getExamCategory(Yii::app()->user->schoolId, Yii::app()->user->batchId, 3);

                    $user_info['paid_user']['terms'] = array();

                    if($exam_category)
                    $user_info['paid_user']['terms'] = $exam_category;
                }


                $user_info['paid_user']['profile_id'] = Yii::app()->user->profileId;
                $user_info['paid_user']['is_parent'] = Yii::app()->user->isParent;
                $user_info['paid_user']['is_teacher'] = Yii::app()->user->isTeacher;
                
                $user_info['paid_user']['school_type'] = $school_obj->getschooltype(Yii::app()->user->schoolId);
                $user_info['paid_user']['school_id'] = Yii::app()->user->schoolId;
                $user_info['paid_user']['school_name'] = $school_details->name;




    //            if(is_array($user_paid_login_data))
    //            {
    //              $username =  $user_paid_login_data[1]; 
    //              $password =  $user_paid_login_data[0];
    //            }



                $attendance = new Attendances();
                $user_info['weekend'] = $attendance->getWeekend(Yii::app()->user->schoolId);

                $user_info['children'] = array();
                if (Yii::app()->user->isParent) {
                    $user = new Users();
                    $user_info['children'] = $user->studentList(Yii::app()->user->profileId);
                    $gurdianModel = new Guardians();
                    $gurdian = $gurdianModel->findBypk(Yii::app()->user->profileId);
                    $user_info['paid_user']['relation'] = $gurdian->relation;
                }

                if (!isset($user_secret)) {
                    $user_info['paid_user']['secret'] = Yii::app()->user->user_secret;
                }

                $user_info['session'] = Yii::app()->session->getSessionID();
            }
            else
            {
                $user_info['user_type'] = 0;
            }
        }
        else
        {
            $user_info['user_type'] = 0;
        }
        return $user_info;   
    }        
    public function getUserInfo($id,$paid_school_id=0,$type=0)
    {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $id);

        $value = $this->find($criteria);
        $user_info = array();
        if ($value != NULL)
        { 
            
               
            $user_info['user_id']    = $value->id;
            $user_info['first_name'] = $value->first_name;
            $user_info['profile_image'] = Settings::getProfileImage($value->id);
            //$user_info['profile_image'] = $value->profile_image;
            $user_info['last_name'] = $value->last_name;
            $user_info['middle_name'] = $value->middle_name;
            
            if( isset(Yii::app()->user->id) &&  Yii::app()->user->id)
            {
              $user_obj = new Users();
              $user_data = $user_obj->findByPk(Yii::app()->user->id);
              if($user_data)
              {
                  $user_info['first_name'] = $user_data->first_name;
                  $user_info['last_name'] = $user_data->last_name;
              }
            } 
            if($value->nick_name==1 || $value->nick_name==2 || $value->nick_name==3 )
            {
                $user_info['nick_name'] = $value->nick_name;
            }  
            else
            {
                $user_info['nick_name'] = 1;
            }    
            
            $user_info['user_type'] = $value->user_type;
            $user_info['medium'] = $value->medium;
            $user_info['email'] = $value->email;
            $user_info['gender'] = $value->gender;
            $user_info['tds_country_id'] = $value->tds_country_id;
            $user_info['district'] = $value->district;
            $user_info['division'] = "";
            if($value->division)
            {
                $user_info['division'] = $value->division;
            }
            
            $user_info['grade_ids'] = $value->grade_ids;
            $user_info['dob'] = $value->dob;
            $user_info['mobile_no'] = $value->mobile_no;
            $user_info['school_name'] = $value->school_name;
            $user_info['location'] = $value->location;
            $user_info['teaching_for'] = $value->teaching_for;
            $user_info['occupation'] = $value->occupation;
           
            $schooluser = new SchoolUser();
            $user_info['user_schools'] = $schooluser->userSchool($value->id,0,$paid_school_id,$type);
       
            
        }
        if($user_info)
        {
            return $user_info;
        } 
        return false;   
        
    }        

    

}
