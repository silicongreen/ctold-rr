<?php

/**
 * This is the model class for table "employee_leaves".
 *
 * The followings are the available columns in table 'employee_leaves':
 * @property integer $id
 * @property integer $employee_id
 * @property integer $employee_leave_type_id
 * @property string $leave_count
 * @property string $leave_taken
 * @property string $reset_date
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class EmployeeLeaves extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'employee_leaves';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('employee_id, employee_leave_type_id, school_id', 'numerical', 'integerOnly'=>true),
			array('leave_count, leave_taken', 'length', 'max'=>5),
			array('reset_date, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, employee_id, employee_leave_type_id, leave_count, leave_taken, reset_date, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'employee_id' => 'Employee',
			'employee_leave_type_id' => 'Employee Leave Type',
			'leave_count' => 'Leave Count',
			'leave_taken' => 'Leave Taken',
			'reset_date' => 'Reset Date',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
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
		$criteria->compare('employee_id',$this->employee_id);
		$criteria->compare('employee_leave_type_id',$this->employee_leave_type_id);
		$criteria->compare('leave_count',$this->leave_count,true);
		$criteria->compare('leave_taken',$this->leave_taken,true);
		$criteria->compare('reset_date',$this->reset_date,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EmployeeLeaves the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
