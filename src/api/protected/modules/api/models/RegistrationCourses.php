<?php

/**
 * This is the model class for table "registration_courses".
 *
 * The followings are the available columns in table 'registration_courses':
 * @property integer $id
 * @property integer $school_id
 * @property integer $course_id
 * @property integer $minimum_score
 * @property integer $is_active
 * @property double $amount
 * @property string $created_at
 * @property string $updated_at
 * @property integer $subject_based_fee_colletion
 * @property integer $enable_approval_system
 * @property integer $min_electives
 * @property integer $max_electives
 * @property integer $is_subject_based_registration
 * @property integer $include_additional_details
 * @property string $additional_field_ids
 */
class RegistrationCourses extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'registration_courses';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('school_id, course_id, minimum_score, is_active, subject_based_fee_colletion, enable_approval_system, min_electives, max_electives, is_subject_based_registration, include_additional_details', 'numerical', 'integerOnly'=>true),
			array('amount', 'numerical'),
			array('additional_field_ids', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, school_id, course_id, minimum_score, is_active, amount, created_at, updated_at, subject_based_fee_colletion, enable_approval_system, min_electives, max_electives, is_subject_based_registration, include_additional_details, additional_field_ids', 'safe', 'on'=>'search'),
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
			'course_id' => 'Course',
			'minimum_score' => 'Minimum Score',
			'is_active' => 'Is Active',
			'amount' => 'Amount',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'subject_based_fee_colletion' => 'Subject Based Fee Colletion',
			'enable_approval_system' => 'Enable Approval System',
			'min_electives' => 'Min Electives',
			'max_electives' => 'Max Electives',
			'is_subject_based_registration' => 'Is Subject Based Registration',
			'include_additional_details' => 'Include Additional Details',
			'additional_field_ids' => 'Additional Field Ids',
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
		$criteria->compare('course_id',$this->course_id);
		$criteria->compare('minimum_score',$this->minimum_score);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('amount',$this->amount);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('subject_based_fee_colletion',$this->subject_based_fee_colletion);
		$criteria->compare('enable_approval_system',$this->enable_approval_system);
		$criteria->compare('min_electives',$this->min_electives);
		$criteria->compare('max_electives',$this->max_electives);
		$criteria->compare('is_subject_based_registration',$this->is_subject_based_registration);
		$criteria->compare('include_additional_details',$this->include_additional_details);
		$criteria->compare('additional_field_ids',$this->additional_field_ids,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RegistrationCourses the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
