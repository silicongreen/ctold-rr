<?php

/**
 * This is the model class for table "apply_leaves".
 *
 * The followings are the available columns in table 'apply_leaves':
 * @property integer $id
 * @property integer $employee_id
 * @property integer $employee_leave_types_id
 * @property integer $is_half_day
 * @property string $start_date
 * @property string $end_date
 * @property string $reason
 * @property integer $approved
 * @property integer $viewed_by_manager
 * @property string $manager_remark
 * @property integer $approving_manager
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class ApplyLeaveStudents extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'apply_leave_students';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		
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
			'employee_leave_types_id' => 'Employee Leave Types',
			'is_half_day' => 'Is Half Day',
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
			'reason' => 'Reason',
			'approved' => 'Approved',
			'viewed_by_manager' => 'Viewed By Manager',
			'manager_remark' => 'Manager Remark',
			'approving_manager' => 'Approving Manager',
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
		$criteria->compare('employee_leave_types_id',$this->employee_leave_types_id);
		$criteria->compare('is_half_day',$this->is_half_day);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('approved',$this->approved);
		$criteria->compare('viewed_by_manager',$this->viewed_by_manager);
		$criteria->compare('manager_remark',$this->manager_remark,true);
		$criteria->compare('approving_manager',$this->approving_manager);
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
	 * @return ApplyLeaves the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getleaveStudentsDate($date)
        {
            $criteria = new CDbCriteria;
            $criteria->select = "t.student_id";
            $criteria->addCondition("DATE(start_date) >= '" . $date . "'");
            $criteria->addCondition("DATE(end_date) <= '" . $date . "'");
            $criteria->compare('approved', 1);
           
            $return_array = array();
            $data = $this->findAll($criteria);
            foreach ($data as $value) 
            {
                $merge = array();
                $merge = $value->student_id;
                $return_array[] = $merge;
            }
            return $return_array;
        }
        
        public function getleaveStudentMonth($start_date, $end_date, $student_id)
        {
            $criteria = new CDbCriteria;
            $criteria->addCondition("DATE(start_date) >= '" . $start_date . "'");
            $criteria->addCondition("DATE(start_date) <= '" . $end_date . "'");
            $criteria->compare('student_id', $student_id);
            $criteria->compare('approved', 1);
           
            $return_array = array();
            $data = $this->findAll($criteria);
            foreach ($data as $value) 
            {
                $merge = array();

                $merge['title'] = $value->reason;
                $merge['start_date'] = date("Y-m-d", strtotime($value->start_date));
                $merge['end_date'] = date("Y-m-d", strtotime($value->end_date));

                $return_array[] = $merge;
            }
            return $return_array;
        }
}
