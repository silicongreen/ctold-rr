<?php

/**
 * This is the model class for table "students".
 *
 * The followings are the available columns in table 'students':
 * @property integer $id
 * @property string $admission_no
 * @property string $class_roll_no
 * @property string $admission_date
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property integer $batch_id
 * @property string $date_of_birth
 * @property string $gender
 * @property string $blood_group
 * @property string $birth_place
 * @property integer $nationality_id
 * @property string $language
 * @property string $religion
 * @property integer $student_category_id
 * @property string $address_line1
 * @property string $address_line2
 * @property string $city
 * @property string $state
 * @property string $pin_code
 * @property integer $country_id
 * @property string $phone1
 * @property string $phone2
 * @property string $email
 * @property integer $immediate_contact_id
 * @property integer $is_sms_enabled
 * @property string $photo_file_name
 * @property string $photo_content_type
 * @property string $photo_data
 * @property string $status_description
 * @property integer $is_active
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $updated_at
 * @property integer $has_paid_fees
 * @property integer $photo_file_size
 * @property integer $user_id
 * @property integer $is_email_enabled
 * @property integer $sibling_id
 * @property string $photo_updated_at
 * @property string $library_card
 * @property integer $school_id
 */
class Students extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'students';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('batch_id, nationality_id, student_category_id, country_id, immediate_contact_id, is_sms_enabled, is_active, is_deleted, has_paid_fees, photo_file_size, user_id, is_email_enabled, sibling_id, school_id', 'numerical', 'integerOnly' => true),
            array('admission_no, class_roll_no, first_name, middle_name, last_name, gender, blood_group, birth_place, language, religion, address_line1, address_line2, city, state, pin_code, phone1, phone2, email, photo_file_name, photo_content_type, status_description, library_card', 'length', 'max' => 255),
            array('admission_date, date_of_birth, photo_data, created_at, updated_at, photo_updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, admission_no, class_roll_no, admission_date, first_name, middle_name, last_name, batch_id, date_of_birth, gender, blood_group, birth_place, nationality_id, language, religion, student_category_id, address_line1, address_line2, city, state, pin_code, country_id, phone1, phone2, email, immediate_contact_id, is_sms_enabled, photo_file_name, photo_content_type, photo_data, status_description, is_active, is_deleted, created_at, updated_at, has_paid_fees, photo_file_size, user_id, is_email_enabled, sibling_id, photo_updated_at, library_card, school_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'userDetails' => array(self::BELONGS_TO, 'Users', 'user_id',
                'joinType' => 'INNER JOIN',
            ),
            'guradianDetails' => array(self::BELONGS_TO, 'Guardians', 'immediate_contact_id',
                'joinType' => 'INNER JOIN',
            )
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'admission_no' => 'Admission No',
            'class_roll_no' => 'Class Roll No',
            'admission_date' => 'Admission Date',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'batch_id' => 'Batch',
            'date_of_birth' => 'Date Of Birth',
            'gender' => 'Gender',
            'blood_group' => 'Blood Group',
            'birth_place' => 'Birth Place',
            'nationality_id' => 'Nationality',
            'language' => 'Language',
            'religion' => 'Religion',
            'student_category_id' => 'Student Category',
            'address_line1' => 'Address Line1',
            'address_line2' => 'Address Line2',
            'city' => 'City',
            'state' => 'State',
            'pin_code' => 'Pin Code',
            'country_id' => 'Country',
            'phone1' => 'Phone1',
            'phone2' => 'Phone2',
            'email' => 'Email',
            'immediate_contact_id' => 'Immediate Contact',
            'is_sms_enabled' => 'Is Sms Enabled',
            'photo_file_name' => 'Photo File Name',
            'photo_content_type' => 'Photo Content Type',
            'photo_data' => 'Photo Data',
            'status_description' => 'Status Description',
            'is_active' => 'Is Active',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'has_paid_fees' => 'Has Paid Fees',
            'photo_file_size' => 'Photo File Size',
            'user_id' => 'User',
            'is_email_enabled' => 'Is Email Enabled',
            'sibling_id' => 'Sibling',
            'photo_updated_at' => 'Photo Updated At',
            'library_card' => 'Library Card',
            'school_id' => 'School',
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
        $criteria->compare('admission_no', $this->admission_no, true);
        $criteria->compare('class_roll_no', $this->class_roll_no, true);
        $criteria->compare('admission_date', $this->admission_date, true);
        $criteria->compare('first_name', $this->first_name, true);
        $criteria->compare('middle_name', $this->middle_name, true);
        $criteria->compare('last_name', $this->last_name, true);
        $criteria->compare('batch_id', $this->batch_id);
        $criteria->compare('date_of_birth', $this->date_of_birth, true);
        $criteria->compare('gender', $this->gender, true);
        $criteria->compare('blood_group', $this->blood_group, true);
        $criteria->compare('birth_place', $this->birth_place, true);
        $criteria->compare('nationality_id', $this->nationality_id);
        $criteria->compare('language', $this->language, true);
        $criteria->compare('religion', $this->religion, true);
        $criteria->compare('student_category_id', $this->student_category_id);
        $criteria->compare('address_line1', $this->address_line1, true);
        $criteria->compare('address_line2', $this->address_line2, true);
        $criteria->compare('city', $this->city, true);
        $criteria->compare('state', $this->state, true);
        $criteria->compare('pin_code', $this->pin_code, true);
        $criteria->compare('country_id', $this->country_id);
        $criteria->compare('phone1', $this->phone1, true);
        $criteria->compare('phone2', $this->phone2, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('immediate_contact_id', $this->immediate_contact_id);
        $criteria->compare('is_sms_enabled', $this->is_sms_enabled);
        $criteria->compare('photo_file_name', $this->photo_file_name, true);
        $criteria->compare('photo_content_type', $this->photo_content_type, true);
        $criteria->compare('photo_data', $this->photo_data, true);
        $criteria->compare('status_description', $this->status_description, true);
        $criteria->compare('is_active', $this->is_active);
        $criteria->compare('is_deleted', $this->is_deleted);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('has_paid_fees', $this->has_paid_fees);
        $criteria->compare('photo_file_size', $this->photo_file_size);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('is_email_enabled', $this->is_email_enabled);
        $criteria->compare('sibling_id', $this->sibling_id);
        $criteria->compare('photo_updated_at', $this->photo_updated_at, true);
        $criteria->compare('library_card', $this->library_card, true);
        $criteria->compare('school_id', $this->school_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Students the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getStudentByUserId($uid) {

        $criteria = new CDbCriteria();
        $criteria->select = 'id, batch_id';
        $criteria->compare('user_id', $uid);

        return $this->find($criteria);
    }

    public function getStudentBySiblings($sibling_id) {
        $criteria = new CDbCriteria();
        $criteria->compare('sibling_id', $sibling_id);
        return $this->findAll($criteria);
    }
    public function getStudentByBatchFull($batch_id) {

        $criteria = new CDbCriteria();
        
        $criteria->select = 't.id,t.first_name,t.middle_name,t.last_name';
        $criteria->compare('batch_id',$batch_id);
        $students = $this->findAll($criteria);
       
        return $students;
    }
    
    public function getStudentByBatch($batch_id) {

        $criteria = new CDbCriteria();
        
        $criteria->select = 't.id';
        $criteria->compare('batch_id',$batch_id);
        
        $students = $this->findAll($criteria);
        
        $students_array = array();
            
        foreach($students as $value)
        {
            $students_array[] = $value->id;
        } 

        return $students_array;
    }

    public function getParentId($student_id) {

        $criteria = new CDbCriteria();
        
        $criteria->select = 'immediate_contact_id';
        $criteria->compare('id', Yii::app()->user->profileId);
        
        return $this->find($criteria)->immediate_contact_id;
    }

}
