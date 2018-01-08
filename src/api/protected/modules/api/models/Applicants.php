<?php

/**
 * This is the model class for table "applicants".
 *
 * The followings are the available columns in table 'applicants':
 * @property integer $id
 * @property integer $school_id
 * @property string $reg_no
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $date_of_birth
 * @property string $address_line1
 * @property string $address_line2
 * @property string $city
 * @property string $state
 * @property integer $country_id
 * @property integer $nationality_id
 * @property string $pin_code
 * @property string $phone1
 * @property string $phone2
 * @property string $email
 * @property string $gender
 * @property integer $registration_course_id
 * @property integer $photo_file_size
 * @property string $photo_file_name
 * @property string $photo_content_type
 * @property string $status
 * @property integer $has_paid
 * @property string $created_at
 * @property string $updated_at
 * @property string $pin_number
 * @property string $print_token
 * @property string $subject_ids
 * @property integer $is_academically_cleared
 * @property integer $is_financially_cleared
 * @property string $amount
 * @property string $normal_subject_ids
 * @property string $photo_updated_at
 */
class Applicants extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'applicants';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('school_id, country_id, nationality_id, registration_course_id, photo_file_size, has_paid, is_academically_cleared, is_financially_cleared', 'numerical', 'integerOnly'=>true),
			array('reg_no, first_name, middle_name, last_name, address_line1, address_line2, city, state, pin_code, phone1, phone2, email, gender, photo_file_name, photo_content_type, status, print_token', 'length', 'max'=>255),
			array('amount', 'length', 'max'=>12),
			array('date_of_birth, created_at, updated_at, pin_number, subject_ids, normal_subject_ids, photo_updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, school_id, reg_no, first_name, middle_name, last_name, date_of_birth, address_line1, address_line2, city, state, country_id, nationality_id, pin_code, phone1, phone2, email, gender, registration_course_id, photo_file_size, photo_file_name, photo_content_type, status, has_paid, created_at, updated_at, pin_number, print_token, subject_ids, is_academically_cleared, is_financially_cleared, amount, normal_subject_ids, photo_updated_at', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'school_id' => 'School',
			'reg_no' => 'Reg No',
			'first_name' => 'First Name',
			'middle_name' => 'Middle Name',
			'last_name' => 'Last Name',
			'date_of_birth' => 'Date Of Birth',
			'address_line1' => 'Address Line1',
			'address_line2' => 'Address Line2',
			'city' => 'City',
			'state' => 'State',
			'country_id' => 'Country',
			'nationality_id' => 'Nationality',
			'pin_code' => 'Pin Code',
			'phone1' => 'Phone1',
			'phone2' => 'Phone2',
			'email' => 'Email',
			'gender' => 'Gender',
			'registration_course_id' => 'Registration Course',
			'photo_file_size' => 'Photo File Size',
			'photo_file_name' => 'Photo File Name',
			'photo_content_type' => 'Photo Content Type',
			'status' => 'Status',
			'has_paid' => 'Has Paid',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'pin_number' => 'Pin Number',
			'print_token' => 'Print Token',
			'subject_ids' => 'Subject Ids',
			'is_academically_cleared' => 'Is Academically Cleared',
			'is_financially_cleared' => 'Is Financially Cleared',
			'amount' => 'Amount',
			'normal_subject_ids' => 'Normal Subject Ids',
			'photo_updated_at' => 'Photo Updated At',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('reg_no',$this->reg_no,true);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('middle_name',$this->middle_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('date_of_birth',$this->date_of_birth,true);
		$criteria->compare('address_line1',$this->address_line1,true);
		$criteria->compare('address_line2',$this->address_line2,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('country_id',$this->country_id);
		$criteria->compare('nationality_id',$this->nationality_id);
		$criteria->compare('pin_code',$this->pin_code,true);
		$criteria->compare('phone1',$this->phone1,true);
		$criteria->compare('phone2',$this->phone2,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('gender',$this->gender,true);
		$criteria->compare('registration_course_id',$this->registration_course_id);
		$criteria->compare('photo_file_size',$this->photo_file_size);
		$criteria->compare('photo_file_name',$this->photo_file_name,true);
		$criteria->compare('photo_content_type',$this->photo_content_type,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('has_paid',$this->has_paid);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('pin_number',$this->pin_number,true);
		$criteria->compare('print_token',$this->print_token,true);
		$criteria->compare('subject_ids',$this->subject_ids,true);
		$criteria->compare('is_academically_cleared',$this->is_academically_cleared);
		$criteria->compare('is_financially_cleared',$this->is_financially_cleared);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('normal_subject_ids',$this->normal_subject_ids,true);
		$criteria->compare('photo_updated_at',$this->photo_updated_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Applicants the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
