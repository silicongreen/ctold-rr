<?php

/**
 * This is the model class for table "discipline_complaints".
 *
 * The followings are the available columns in table 'discipline_complaints':
 * @property integer $id
 * @property string $subject
 * @property string $body
 * @property string $trial_date
 * @property integer $school_id
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $complaint_no
 * @property integer $action_taken
 */
class DisciplineComplaints extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'discipline_complaints';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('school_id, user_id, action_taken', 'numerical', 'integerOnly'=>true),
			array('subject, complaint_no', 'length', 'max'=>255),
			array('body, trial_date, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, subject, body, trial_date, school_id, user_id, created_at, updated_at, complaint_no, action_taken', 'safe', 'on'=>'search'),
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
			'subject' => 'Subject',
			'body' => 'Body',
			'trial_date' => 'Trial Date',
			'school_id' => 'School',
			'user_id' => 'User',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'complaint_no' => 'Complaint No',
			'action_taken' => 'Action Taken',
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
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('body',$this->body,true);
		$criteria->compare('trial_date',$this->trial_date,true);
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('complaint_no',$this->complaint_no,true);
		$criteria->compare('action_taken',$this->action_taken);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DisciplineComplaints the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
