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
class ApplyLeaves extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'apply_leaves';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('employee_id, employee_leave_types_id, is_half_day, approved, viewed_by_manager, approving_manager, school_id', 'numerical', 'integerOnly'=>true),
			array('reason, manager_remark', 'length', 'max'=>255),
			array('start_date, end_date, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, employee_id, employee_leave_types_id, is_half_day, start_date, end_date, reason, approved, viewed_by_manager, manager_remark, approving_manager, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
                    'leavetype' => array(self::BELONGS_TO, 'EmployeeLeaveTypes', 'employee_leave_types_id',
                                'joinType' => 'INNER JOIN'
                        )
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
        
        public function checkLeaveOk($employee_id,$start_date,$end_date)
        {

            $criteria = new CDbCriteria;
            $criteria->select = "t.id";
            $criteria->addCondition("(employee_id = ".$employee_id." AND (approved IS NULL OR approved=1)) AND ((date(start_date)  BETWEEN '".$start_date."' AND '".$end_date."' ) OR (date(end_date) BETWEEN '".$start_date."' AND '".$end_date."' ))");
            $criteria->limit = 1;
            $data = $this->find($criteria);
            if($data)
            {
                return false;
            }
            return true;
        }
        
        public function getleaveTeacher($employee_id) 
        {
                $today = date("Y-m-d"); 
                $criteria = new CDbCriteria();
                $criteria->compare('employee_id', $employee_id);
                
                
                $criteria->addCondition('date(t.start_date)<="'.$today.'"');
                $criteria->addCondition('date(t.end_date)>="'.$today.'"');
                
                return $this->find($criteria);
        }
        
        public function getSingleLeave($id) 
        {
            $criteria = new CDbCriteria();
            $criteria->compare('id', $id);
            $criteria->with = array(
                   'leavetype' => array(
                       'select' => 'leavetype.name',
                       'joinType' => "INNER JOIN"
                       )
            );
            $value = $this->find($criteria);
            $leave = array();
            if ($value)
            {
                $leave['leave_type'] = $value['leavetype']->name;
                $leave['leave_start_date'] = $value->start_date;
                $leave['leave_end_date'] = $value->end_date;
                if (!$value->approving_manager)
                {
                    $leave['status'] = 2;
                }
                else if ($value->approved == 1)
                {
                    $leave['status'] = 1;
                }
                else
                {
                    $leave['status'] = 0;
                }
                $leave['created_at'] = date("Y-m-d", strtotime($value->created_at));
                $i++;
            }
            return $leave;
        }
        
        public function getTeacherLeave($employee_id) 
        {
                $today = date("Y-m-d", strtotime("-6 Month")); 
                $criteria = new CDbCriteria();
                $criteria->compare('employee_id', $employee_id);
                
                $criteria->addCondition('date(t.start_date)>="'.$today.'"');
                $criteria->order = 't.created_at DESC';
                $criteria->with = array(
                       'leavetype' => array(
                           'select' => 'leavetype.name',
                           'joinType' => "INNER JOIN"
                           )
                 );
                 return $this->findAll($criteria);
        }
}
