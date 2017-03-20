<?php

/**
 * This is the model class for table "attendance_registers".
 *
 * The followings are the available columns in table 'attendance_registers':
 * @property integer $id
 * @property string $attendance_date
 * @property integer $batch_id
 * @property integer $employee_id
 * @property integer $present
 * @property integer $absent
 * @property integer $late
 * @property integer $leave
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class AttendanceRegisters extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'attendance_registers';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('attendance_date, batch_id, employee_id, present, created_at, updated_at, school_id', 'required'),
			array('batch_id, employee_id, present, absent, late, leave, school_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, attendance_date, batch_id, employee_id, present, absent, late, leave, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'batch_id' => 'Batch',
			'employee_id' => 'Employee',
			'present' => 'Present',
			'absent' => 'Absent',
			'late' => 'Late',
			'leave' => 'Leave',
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
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('employee_id',$this->employee_id);
		$criteria->compare('present',$this->present);
		$criteria->compare('absent',$this->absent);
		$criteria->compare('late',$this->late);
		$criteria->compare('leave',$this->leave);
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
	 * @return AttendanceRegisters the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getRegisterData($date, $batch_id)
        {
            $criteria = new CDbCriteria;
            $criteria->compare('t.batch_id', $batch_id);
            
            $criteria->compare("attendance_date", $date);
            $data = $this->find($criteria);
            return $data;
        }
}
