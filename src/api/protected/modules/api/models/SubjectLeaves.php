<?php

/**
 * This is the model class for table "subject_leaves".
 *
 * The followings are the available columns in table 'subject_leaves':
 * @property integer $id
 * @property integer $student_id
 * @property string $month_date
 * @property integer $subject_id
 * @property integer $employee_id
 * @property integer $class_timing_id
 * @property string $reason
 * @property string $created_at
 * @property string $updated_at
 * @property integer $batch_id
 * @property integer $school_id
 */
class SubjectLeaves extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'subject_leaves';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('student_id, subject_id, employee_id, class_timing_id, batch_id, school_id', 'numerical', 'integerOnly'=>true),
			array('reason', 'length', 'max'=>255),
			array('month_date, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, student_id, month_date, subject_id, employee_id, class_timing_id, reason, created_at, updated_at, batch_id, school_id', 'safe', 'on'=>'search'),
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
			'student_id' => 'Student',
			'month_date' => 'Month Date',
			'subject_id' => 'Subject',
			'employee_id' => 'Employee',
			'class_timing_id' => 'Class Timing',
			'reason' => 'Reason',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'batch_id' => 'Batch',
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
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('month_date',$this->month_date,true);
		$criteria->compare('subject_id',$this->subject_id);
		$criteria->compare('employee_id',$this->employee_id);
		$criteria->compare('class_timing_id',$this->class_timing_id);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SubjectLeaves the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
