<?php

/**
 * This is the model class for table "archived_employees".
 *
 * The followings are the available columns in table 'archived_employees':
 * @property integer $id
 * @property integer $employee_category_id
 * @property string $employee_number
 * @property string $joining_date
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $gender
 * @property string $job_title
 * @property integer $employee_position_id
 * @property integer $employee_department_id
 * @property integer $reporting_manager_id
 * @property integer $employee_grade_id
 * @property string $qualification
 * @property string $experience_detail
 * @property integer $experience_year
 * @property integer $experience_month
 * @property integer $status
 * @property string $status_description
 * @property string $date_of_birth
 * @property string $marital_status
 * @property integer $children_count
 * @property string $father_name
 * @property string $mother_name
 * @property string $husband_name
 * @property string $blood_group
 * @property integer $nationality_id
 * @property string $home_address_line1
 * @property string $home_address_line2
 * @property string $home_city
 * @property string $home_state
 * @property integer $home_country_id
 * @property string $home_pin_code
 * @property string $office_address_line1
 * @property string $office_address_line2
 * @property string $office_city
 * @property string $office_state
 * @property integer $office_country_id
 * @property string $office_pin_code
 * @property string $office_phone1
 * @property string $office_phone2
 * @property string $mobile_phone
 * @property string $home_phone
 * @property string $email
 * @property string $fax
 * @property string $photo_file_name
 * @property string $photo_content_type
 * @property string $photo_data
 * @property string $created_at
 * @property string $updated_at
 * @property integer $photo_file_size
 * @property string $former_id
 * @property integer $user_id
 * @property string $photo_updated_at
 * @property string $library_card
 * @property integer $school_id
 */
class ArchivedEmployees extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'archived_employees';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('employee_category_id, employee_position_id, employee_department_id, reporting_manager_id, employee_grade_id, experience_year, experience_month, status, children_count, nationality_id, home_country_id, office_country_id, photo_file_size, user_id, school_id', 'numerical', 'integerOnly'=>true),
			array('employee_number, first_name, middle_name, last_name, gender, job_title, qualification, status_description, marital_status, father_name, mother_name, husband_name, blood_group, home_address_line1, home_address_line2, home_city, home_state, home_pin_code, office_address_line1, office_address_line2, office_city, office_state, office_pin_code, office_phone1, office_phone2, mobile_phone, home_phone, email, fax, photo_file_name, photo_content_type, former_id, library_card', 'length', 'max'=>255),
			array('joining_date, experience_detail, date_of_birth, photo_data, created_at, updated_at, photo_updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, employee_category_id, employee_number, joining_date, first_name, middle_name, last_name, gender, job_title, employee_position_id, employee_department_id, reporting_manager_id, employee_grade_id, qualification, experience_detail, experience_year, experience_month, status, status_description, date_of_birth, marital_status, children_count, father_name, mother_name, husband_name, blood_group, nationality_id, home_address_line1, home_address_line2, home_city, home_state, home_country_id, home_pin_code, office_address_line1, office_address_line2, office_city, office_state, office_country_id, office_pin_code, office_phone1, office_phone2, mobile_phone, home_phone, email, fax, photo_file_name, photo_content_type, photo_data, created_at, updated_at, photo_file_size, former_id, user_id, photo_updated_at, library_card, school_id', 'safe', 'on'=>'search'),
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
			'employee_category_id' => 'Employee Category',
			'employee_number' => 'Employee Number',
			'joining_date' => 'Joining Date',
			'first_name' => 'First Name',
			'middle_name' => 'Middle Name',
			'last_name' => 'Last Name',
			'gender' => 'Gender',
			'job_title' => 'Job Title',
			'employee_position_id' => 'Employee Position',
			'employee_department_id' => 'Employee Department',
			'reporting_manager_id' => 'Reporting Manager',
			'employee_grade_id' => 'Employee Grade',
			'qualification' => 'Qualification',
			'experience_detail' => 'Experience Detail',
			'experience_year' => 'Experience Year',
			'experience_month' => 'Experience Month',
			'status' => 'Status',
			'status_description' => 'Status Description',
			'date_of_birth' => 'Date Of Birth',
			'marital_status' => 'Marital Status',
			'children_count' => 'Children Count',
			'father_name' => 'Father Name',
			'mother_name' => 'Mother Name',
			'husband_name' => 'Husband Name',
			'blood_group' => 'Blood Group',
			'nationality_id' => 'Nationality',
			'home_address_line1' => 'Home Address Line1',
			'home_address_line2' => 'Home Address Line2',
			'home_city' => 'Home City',
			'home_state' => 'Home State',
			'home_country_id' => 'Home Country',
			'home_pin_code' => 'Home Pin Code',
			'office_address_line1' => 'Office Address Line1',
			'office_address_line2' => 'Office Address Line2',
			'office_city' => 'Office City',
			'office_state' => 'Office State',
			'office_country_id' => 'Office Country',
			'office_pin_code' => 'Office Pin Code',
			'office_phone1' => 'Office Phone1',
			'office_phone2' => 'Office Phone2',
			'mobile_phone' => 'Mobile Phone',
			'home_phone' => 'Home Phone',
			'email' => 'Email',
			'fax' => 'Fax',
			'photo_file_name' => 'Photo File Name',
			'photo_content_type' => 'Photo Content Type',
			'photo_data' => 'Photo Data',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'photo_file_size' => 'Photo File Size',
			'former_id' => 'Former',
			'user_id' => 'User',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('employee_category_id',$this->employee_category_id);
		$criteria->compare('employee_number',$this->employee_number,true);
		$criteria->compare('joining_date',$this->joining_date,true);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('middle_name',$this->middle_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('gender',$this->gender,true);
		$criteria->compare('job_title',$this->job_title,true);
		$criteria->compare('employee_position_id',$this->employee_position_id);
		$criteria->compare('employee_department_id',$this->employee_department_id);
		$criteria->compare('reporting_manager_id',$this->reporting_manager_id);
		$criteria->compare('employee_grade_id',$this->employee_grade_id);
		$criteria->compare('qualification',$this->qualification,true);
		$criteria->compare('experience_detail',$this->experience_detail,true);
		$criteria->compare('experience_year',$this->experience_year);
		$criteria->compare('experience_month',$this->experience_month);
		$criteria->compare('status',$this->status);
		$criteria->compare('status_description',$this->status_description,true);
		$criteria->compare('date_of_birth',$this->date_of_birth,true);
		$criteria->compare('marital_status',$this->marital_status,true);
		$criteria->compare('children_count',$this->children_count);
		$criteria->compare('father_name',$this->father_name,true);
		$criteria->compare('mother_name',$this->mother_name,true);
		$criteria->compare('husband_name',$this->husband_name,true);
		$criteria->compare('blood_group',$this->blood_group,true);
		$criteria->compare('nationality_id',$this->nationality_id);
		$criteria->compare('home_address_line1',$this->home_address_line1,true);
		$criteria->compare('home_address_line2',$this->home_address_line2,true);
		$criteria->compare('home_city',$this->home_city,true);
		$criteria->compare('home_state',$this->home_state,true);
		$criteria->compare('home_country_id',$this->home_country_id);
		$criteria->compare('home_pin_code',$this->home_pin_code,true);
		$criteria->compare('office_address_line1',$this->office_address_line1,true);
		$criteria->compare('office_address_line2',$this->office_address_line2,true);
		$criteria->compare('office_city',$this->office_city,true);
		$criteria->compare('office_state',$this->office_state,true);
		$criteria->compare('office_country_id',$this->office_country_id);
		$criteria->compare('office_pin_code',$this->office_pin_code,true);
		$criteria->compare('office_phone1',$this->office_phone1,true);
		$criteria->compare('office_phone2',$this->office_phone2,true);
		$criteria->compare('mobile_phone',$this->mobile_phone,true);
		$criteria->compare('home_phone',$this->home_phone,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('fax',$this->fax,true);
		$criteria->compare('photo_file_name',$this->photo_file_name,true);
		$criteria->compare('photo_content_type',$this->photo_content_type,true);
		$criteria->compare('photo_data',$this->photo_data,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('photo_file_size',$this->photo_file_size);
		$criteria->compare('former_id',$this->former_id,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('photo_updated_at',$this->photo_updated_at,true);
		$criteria->compare('library_card',$this->library_card,true);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ArchivedEmployees the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
