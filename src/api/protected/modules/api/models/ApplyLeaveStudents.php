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
		return array(
			array('reason, school_id,start_date,end_date','required'),
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
                    'students' => array(self::BELONGS_TO, 'Students', 'student_id',
                                'joinType' => 'INNER JOIN'
                        )
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	

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
        public function checkLeaveOk($student_id,$start_date,$end_date)
        {

            if($end_date<$start_date)
            {
                return false;
            }
            $criteria = new CDbCriteria;
            $criteria->select = "t.id";
            $criteria->addCondition("(student_id = ".$student_id." AND (approved IS NULL OR approved=1)) AND ((date(start_date)  BETWEEN '".$start_date."' AND '".$end_date."' ) OR (date(end_date)  BETWEEN '".$start_date."' AND '".$end_date."' ))");
            $criteria->limit = 1;
            $data = $this->find($criteria);
            if($data)
            {
                return false;
            }
            return true;
        }
        
        public function getSingleleave($id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = "t.id,t.student_id,t.created_at,t.leave_subject,t.approved,t.reason,t.start_date,t.end_date, t.viewed_by_teacher";
            $criteria->compare('id', $id); 
            
           
            $return_array = array();
            $data = $this->find($criteria);
            if($data)
            { 
                if($data->approved && $data->approved==1)
                {
                    $return_array['approved'] = $data->student_id;
                }
                else
                {
                    $return_array['unapproved'] = $data->student_id;
                }  
                $return_array['leave_subject']  = "";
                if($data->leave_subject)
                {
                    $return_array['leave_subject'] = $data->leave_subject;
                }
              
                $return_array['viewed_by_teacher'] = $data->viewed_by_teacher;
                $return_array['reason'] = $data->reason;
                $return_array['leave_id'] = $data->id;
                $return_array['start_date'] = $data->start_date;
                $return_array['end_date'] = $data->end_date;
                $return_array['created_at'] = date("Y-m-d", strtotime($value->created_at));
            }    
            
            return $return_array;
        }
        public function getallleaveStudentsDate($date,$school_id="")
        {
            if(!$school_id)
            {
                $school_id = Yii::app()->user->schoolId;
            }
            $criteria = new CDbCriteria;
            $criteria->select = "t.id,t.student_id,t.leave_subject,t.approved,t.attachment_file_name,t.reason,t.start_date,t.end_date, t.viewed_by_teacher";
            $criteria->addCondition("DATE(start_date) <= '" . $date . "'");
            $criteria->addCondition("DATE(end_date) >= '" . $date . "'");
            $criteria->compare('school_id', $school_id);
            
           
            $return_array = array();
            $data = $this->findAll($criteria);
            
            $i = 0;
            foreach ($data as $value) 
            {
                
                if($value->approved && $value->approved==1)
                {
                    $return_array['approved'][$i] = $value->student_id;
                }
                else
                {
                    $return_array['unapproved'][$i] = $value->student_id;
                }  
                $return_array['leave_subject'][$i]  = "";
                if($value->leave_subject)
                {
                    $return_array['leave_subject'][$i] = $value->leave_subject;
                }
                $return_array[$i]['attachment_file_name'] = ""; 
                if($value->attachment_file_name)
                {
                    $return_array[$i]['attachment_file_name'] = $value->attachment_file_name;
                }
              
                $return_array['viewed_by_teacher'][$i] = $value->viewed_by_teacher;
                $return_array['reason'][$i] = $value->reason;
                $return_array['leave_id'][$i] = $value->id;
                $return_array['start_date'][$i] = $value->start_date;
                $return_array['end_date'][$i] = $value->end_date;
                $i++;
            }
            return $return_array;
        }
        
        public function getStudentLeaveParent($student_id) 
        {
            $today = date("Y-m-d", strtotime("-6 Month")); 
            $criteria = new CDbCriteria();
            $criteria->compare('student_id', $student_id);           
            $criteria->addCondition('date(t.start_date)>="'.$today.'"');
            $criteria->order = 't.created_at DESC';
            return $this->findAll($criteria);
        }
        public function getStudentLeave($profile_id) 
        {
            $configuration = new Configurations();
            $section_manager = $configuration->getValue("LeaveSectionManager");
            
            if($section_manager == 1 && $section_manager != 1)
            {
                $empObj = Employees();
                $empData = $empObj->findByPk($profile_id);
                if(!isset($empData->meeting_forwarder) || $empData->meeting_forwarder == 0)
                {
                    return array();
                } 
                else 
                {
                    $batch_tutor = new BatchTutors();
                    $batches = $batch_tutor->get_batch_id(false);
                }
            }
            else
            {    
                $esubject = new EmployeesSubjects();
                $batches = $esubject->getBatchId($profile_id);
            }
            $today = date("Y-m-d",  strtotime("-1 Month")); 
            $criteria = new CDbCriteria;
            $criteria->select = "t.id,t.student_id,t.approved,t.attachment_file_name,t.leave_subject,t.reason,t.start_date,t.end_date,t.created_at";
            $criteria->addCondition("DATE(t.start_date) >= '" . $today . "'");
            $criteria->addInCondition("students.batch_id", $batches);
            $criteria->addCondition("(t.approving_teacher IS NULL or t.approving_teacher = 0)");
            
            $criteria->with = array(
                       'students' => array(
                           'select' => 'students.first_name,students.middle_name,students.last_name',
                           'joinType' => "INNER JOIN",
                           'with' => array(
                                    "batchDetails" => array(
                                        "select" => "batchDetails.name",
                                        'joinType' => "INNER JOIN",
                                        'with' => array(
                                            "courseDetails" => array(
                                                "select" => "courseDetails.course_name",
                                                'joinType' => "INNER JOIN",
                                            )
                                        )
                                    )
                                )
                           )
                 );
            
            $data = $this->findAll($criteria);
            $return_array = array();
            $i = 0;
            foreach ($data as $value) 
            {
                $middle_name = (!empty($value["students"]->middle_name)) ? $value["students"]->middle_name.' ' : '';
                $students_name = rtrim($value["students"]->first_name.' '.$middle_name.$value["students"]->last_name);
                $return_array[$i]['student_id'] = $value->student_id;
                $return_array[$i]['student_name'] = $students_name;
                $return_array[$i]['batch'] = $value['students']['batchDetails']['courseDetails']->course_name." ".$value['students']['batchDetails']->name;
                $return_array[$i]['approved'] = $value->approved;
                $return_array[$i]['attachment_file_name'] = ""; 
                if($value->attachment_file_name)
                {
                    $return_array[$i]['attachment_file_name'] = $value->attachment_file_name;
                }
                $return_array[$i]['leave_subject']  = "";
                if($value->leave_subject)
                {
                    $return_array[$i]['leave_subject'] = $value->leave_subject;
                }
                $return_array[$i]['reason'] = $value->reason;
                $return_array[$i]['leave_id'] = $value->id;
                $return_array[$i]['leave_start_date'] = $value->start_date;
                $return_array[$i]['leave_end_date'] = $value->end_date;
                $return_array[$i]['created_at'] = date("Y-m-d",  strtotime($value->created_at));
                $i++;
            }
            return $return_array;
        }
        public function getleaveStudentsDate($date)
        {
            $criteria = new CDbCriteria;
            $criteria->select = "t.student_id";
            $criteria->addCondition("DATE(start_date) <= '" . $date . "'");
            $criteria->addCondition("DATE(end_date) >= '" . $date . "'");
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
            //$criteria->addCondition("(DATE(start_date) >= '" . $start_date . "' AND DATE(start_date) <= '" . $end_date . "') OR (DATE(end_date) <= '" . $end_date . "') " );
            //$criteria->addCondition("DATE(start_date) <= '" . $end_date . "'");
            
            $criteria->addCondition("DATE(end_date) >= '" . $start_date . "'");
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
