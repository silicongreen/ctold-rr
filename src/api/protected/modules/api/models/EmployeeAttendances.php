<?php

/**
 * This is the model class for table "employee_attendances".
 *
 * The followings are the available columns in table 'employee_attendances':
 * @property integer $id
 * @property string $attendance_date
 * @property integer $employee_id
 * @property integer $employee_leave_type_id
 * @property string $reason
 * @property integer $is_half_day
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class EmployeeAttendances extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'employee_attendances';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('employee_id, employee_leave_type_id, is_half_day, school_id', 'numerical', 'integerOnly'=>true),
			array('reason', 'length', 'max'=>255),
			array('attendance_date, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, attendance_date, employee_id, employee_leave_type_id, reason, is_half_day, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'attendance_date' => 'Attendance Date',
			'employee_id' => 'Employee',
			'employee_leave_type_id' => 'Employee Leave Type',
			'reason' => 'Reason',
			'is_half_day' => 'Is Half Day',
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
		$criteria->compare('attendance_date',$this->attendance_date,true);
		$criteria->compare('employee_id',$this->employee_id);
		$criteria->compare('employee_leave_type_id',$this->employee_leave_type_id);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('is_half_day',$this->is_half_day);
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
	 * @return EmployeeAttendances the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getAttTeacher($employee_id) 
        {
                $today = date("Y-m-d"); 
                $criteria = new CDbCriteria();
                $criteria->compare('employee_id', $employee_id);
                
                
                $criteria->compare('date(t.attendance_date)',$today);
                
                return $this->find($criteria);
        }
        
        public function deleteAttendanceEmployee($school_id,$date,$emp_id=array())
        {
            $criteria = new CDbCriteria;
            $criteria->select="t.id";
            $criteria->compare('attendance_date', $date); 
            $criteria->compare('school_id', $school_id);
            if($emp_id)
            {
                $criteria->compare('employee_id', $emp_id);
            }
            $this->deleteAll($criteria);
        }
}
