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
    
    public function getFreeuser($email) {

        $criteria = new CDbCriteria;
        $criteria->compare('email', $email);

        $data = $this->find($criteria);
       
        if ($data != NULL)
        {
            return $data;
        }
        return false;
    }
    public function getUserInfo($id)
    {
        $criteria = new CDbCriteria;
        $criteria->compare('id', $id);

        $value = $this->find($criteria);
        $user_info = array();
        if ($value != NULL)
        { 
            $user_info['user_id']    = $value->id;
            $user_info['first_name'] = $value->first_name;
            $user_info['profile_image'] = $value->profile_image;
            $user_info['last_name'] = $value->last_name;
            $user_info['middle_name'] = $value->middle_name;
            $user_info['nick_name'] = $value->nick_name;
            $user_info['user_type'] = $value->user_type;
            $user_info['medium'] = $value->medium;
            $user_info['email'] = $value->email;
            $user_info['gender'] = $value->gender;
            $user_info['tds_country_id'] = $value->tds_country_id;
            $user_info['district'] = $value->district;
            $user_info['grade_ids'] = $value->grade_ids;
            $user_info['dob'] = $value->dob;
            $user_info['mobile_no'] = $value->mobile_no;
            $user_info['school_name'] = $value->school_name;
            $user_info['location'] = $value->location;
            $user_info['teaching_for'] = $value->teaching_for;
            $user_info['occupation'] = $value->occupation;
           
            $schooluser = new SchoolUser();
            $user_info['user_schools'] = $schooluser->userSchool($value->id);
       
            
        }
        if($user_info)
        {
            return $user_info;
        } 
        return false;   
        
    }        

    

}
