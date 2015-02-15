<?php

/**
 * This is the model class for table "attendances".
 *
 * The followings are the available columns in table 'attendances':
 * @property integer $id
 * @property integer $student_id
 * @property integer $period_table_entry_id
 * @property integer $forenoon
 * @property integer $afternoon
 * @property string $reason
 * @property string $month_date
 * @property integer $batch_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Attendances extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'attendances';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('student_id, period_table_entry_id, forenoon, afternoon, batch_id, school_id', 'numerical', 'integerOnly' => true),
            array('reason', 'length', 'max' => 255),
            array('month_date, created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, student_id, period_table_entry_id, forenoon, afternoon, reason, month_date, batch_id, created_at, updated_at, school_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'studentDetails' => array(self::BELONGS_TO, 'Students', 'student_id',
                'select' => 'studentDetails.id, studentDetails.admission_no, studentDetails.class_roll_no, studentDetails.first_name, studentDetails.middle_name, studentDetails.last_name',
                'joinType' => 'INNER JOIN',
            ),
            'periodEntry' => array(self::BELONGS_TO, 'PeriodEntries', 'period_table_entry_id',
                'select' => 'periodEntry.id, periodEntry.month_date, periodEntry.batch_id, periodEntry.subject_id, periodEntry.class_timing_id, periodEntry.employee_id, periodEntry.school_id',
                'joinType' => 'INNER JOIN',
                'with' => array('subjectDetails', 'classTimingDetails', 'employeesDetails'),
            ),
            'schoolDetails' => array(self::BELONGS_TO, 'Schools', 'school_id',
                'select' => 'schoolDetails.id, schoolDetails.name, schoolDetails.code',
                'joinType' => 'INNER JOIN',
            ),
            'batchDetails' => array(self::BELONGS_TO, 'Batches', 'batch_id',
                'select' => 'batchDetails.id, batchDetails.name, batchDetails.course_id',
                'joinType' => 'INNER JOIN',
                'with' => array('courseDetails'),
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'student_id' => 'Student',
            'period_table_entry_id' => 'Period Table Entry',
            'forenoon' => 'Forenoon',
            'afternoon' => 'Afternoon',
            'reason' => 'Reason',
            'month_date' => 'Month Date',
            'batch_id' => 'Batch',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('student_id', $this->student_id);
        $criteria->compare('period_table_entry_id', $this->period_table_entry_id);
        $criteria->compare('forenoon', $this->forenoon);
        $criteria->compare('afternoon', $this->afternoon);
        $criteria->compare('reason', $this->reason, true);
        $criteria->compare('month_date', $this->month_date, true);
        $criteria->compare('batch_id', $this->batch_id);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('school_id', $this->school_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Attendances the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function getWeekend($school_id)
    {
        $timetable = new TimeTableWeekdays();
        $week_day = $timetable->getWeekDaySet($school_id);
        $weekdays_set = new WeekdaySetsWeekdays();
        $weekdays_set->setAttribute("weekday_set_id", $week_day->weekday_set_id);
        $weekdays = $weekdays_set->getWeekDays();
        $ar_weekdays = Settings::$ar_weekdays;
        if ($weekdays != NULL)
        {
            foreach ($weekdays as $value)
            {
                if (in_array($value->weekday_id, array_keys($ar_weekdays)))
                {
                    unset($ar_weekdays[$value->weekday_id]);
                }
            }
        }
        return array_keys($ar_weekdays);
    }
    public function getAttendenceBatch($batch_id,$date)
    {
        $criteria = new CDbCriteria;
        $criteria->select="t.id";
        $criteria->compare('month_date', date("Y-m-d"));
        $criteria->compare('batch_id', $batch_id);
        $data = $this->findAll($criteria);
        return $data;
    }
    public function getAttendenceStudent($student_id,$date)
    {
        $criteria = new CDbCriteria;
        $criteria->select="t.id";
        $criteria->compare('month_date', $date); 
        $criteria->compare('student_id', $student_id);
        $data = $this->find($criteria);
        return $data;
    }
    public function getAttendence($batch_id,$student_id,$date)
    {
        $criteria = new CDbCriteria;
        $criteria->select="t.id";
        $criteria->compare('month_date', date("Y-m-d"));
        $criteria->compare('batch_id', $batch_id); 
        $criteria->compare('student_id', $student_id);
        $data = $this->find($criteria);
        return $data;
    }
   
          
    
    public function getBatchStudentTodayAttendence($batch_id,$date)
    {
        $criteria = new CDbCriteria;
        $criteria->select="t.student_id,t.forenoon,t.afternoon,t.reason";
        $criteria->compare('month_date', $date);
        $criteria->compare('batch_id', $batch_id);
        $data = $this->findAll($criteria);
        
        $student_ids = array();
        $all_data = array();
        $i = 0;
        foreach($data as $value)
        {
            $student_ids[$i] = $value->student_id;
            $all_data[$value->student_id]['fullday'] = 0;
            if($value->forenoon == 1 && $value->afternoon == 1)
            {
                $all_data[$value->student_id]['fullday'] = 1;
            }
            $all_data[$value->student_id]['reason'] = $value->reason;
            $i++;
        } 
        
        $leaveStudent = new ApplyLeaveStudents();
        
      
        $leave_today = $leaveStudent->getallleaveStudentsDate($date);
        
     
        //$unapproved_leave = $leaveStudent->getUnapprovedleaveStudentsDate($date);
        
        $stdobj = new Students();
        $students = $stdobj->getStudentByBatchFull($batch_id);
        
        $attendence = array();
        $i = 0;
        foreach($students as $value)
        {
            $fullname = ($value->first_name)?$value->first_name." ":"";
            $fullname.= ($value->middle_name)?$value->middle_name." ":"";
            $fullname.= ($value->last_name)?$value->last_name:"";
            
            $attendence[$i]['student_name'] = $fullname;
            
            $attendence[$i]['roll_no'] = $value->class_roll_no;
            
            $attendence[$i]['student_id'] = $value->id;
            
            $attendence[$i]['status'] = 1;
            
            $attendence[$i]['main_status'] = 1;
            
            $attendence[$i]['reason'] = "";
            
            $attendence[$i]['leave_id'] = 0;
            
            $attendence[$i]['leave_start_date'] = "";
            $attendence[$i]['leave_end_date'] = "";
            
            if(in_array($value->id, $student_ids))
            {
                if($all_data[$value->id]['fullday']==1)
                {
                     $attendence[$i]['status'] = 0;
                     $attendence[$i]['main_status'] = 0;
                } 
                else
                {
                     $attendence[$i]['status'] = 2;
                     $attendence[$i]['main_status'] = 2;
                }
                $reason = "";
                if($all_data[$value->id]['reason'])
                {
                    $reason = $all_data[$value->id]['reason'];
                }   
                $attendence[$i]['reason'] =$reason;
                
            } 
          
            if(isset($leave_today['approved']) && in_array($value->id, $leave_today['approved']))
            {
                $key = array_search($value->id, $leave_today['approved']);
                $attendence[$i]['status'] = 3;
                $attendence[$i]['main_status'] = 3;
                if($leave_today['reason'][$key])
                {
                    $attendence[$i]['reason'] = $leave_today['reason'][$key];
                }
                $attendence[$i]['leave_id'] = $leave_today['leave_id'][$key];
                $attendence[$i]['leave_start_date'] = $leave_today['start_date'][$key];
                $attendence[$i]['leave_end_date'] = $leave_today['end_date'][$key];
            }
            if(isset($leave_today['unapproved']) && in_array($value->id, $leave_today['unapproved']))
            {
                $key = array_search($value->id, $leave_today['unapproved']);
                $attendence[$i]['status'] = 4;
                if($leave_today['reason'][$key])
                {
                    $attendence[$i]['reason'] = $leave_today['reason'][$key];
                }
                $attendence[$i]['leave_id'] = $leave_today['leave_id'][$key];
                $attendence[$i]['leave_start_date'] = $leave_today['start_date'][$key];
                $attendence[$i]['leave_end_date'] = $leave_today['end_date'][$key];
                
            }
            
            $i++;
            
        }  
        
        return $attendence;
        
    }

    public function getAbsentStudentMonth($start_date, $end_date, $student_id) {

        $criteria = new CDbCriteria;
        $criteria->addCondition("month_date >= '" . $start_date . "'");
        $criteria->addCondition("month_date <= '" . $end_date . "'");
        $criteria->compare('is_leave',0);
        if (Yii::app()->user->isParent && is_array($student_id)) {
            $criteria->addInCondition('student_id', $student_id);
        } else {
            $criteria->compare('student_id', $student_id);
        }

        $data = $this->with('studentDetails')->findAll($criteria);
        $return_array['absent'] = array();
        $return_array['late'] = array();
        if ($data != NULL) {
            foreach ($data as $value) {
                
                $middle_name = (!empty($value['studentDetails']->middle_name)) ? $value['studentDetails']->middle_name . ' ' : '';
                
                $merge1 = array();
                $merge2 = array();

                if (Yii::app()->user->isParent && is_array($student_id)) {

                    if ($value->forenoon == 1 && $value->afternoon == 1) {
                        $merge1['student_id'] = $value->student_id;
                        $merge1['full_name'] = rtrim($value['studentDetails']->first_name . ' ' . $middle_name . $value['studentDetails']->last_name);
                        $merge1['date'] = $value->month_date;
                        $merge1['reason'] = $value->reason;
                        
                    } else {
                        $merge2['student_id'] = $value->student_id;
                        $merge2['full_name'] = rtrim($value['studentDetails']->first_name . ' ' . $middle_name . $value['studentDetails']->last_name);
                        $merge2['date'] = $value->month_date;
                        $merge2['reason'] = $value->reason;
                    }
                } else {

                    if ($value->forenoon == 1 && $value->afternoon == 1) {
                        $merge1['date'] = $value->month_date;
                        $merge1['reason'] = $value->reason;
                    } else {
                        $merge2['date'] = $value->month_date;
                        $merge2['reason'] = $value->reason;
                    }
                }

                if ($merge1) {
                    $return_array['absent'][] = $merge1;
                }
                if ($merge2) {
                    $return_array['late'][] = $merge2;
                }
            }
        }
        return $return_array;
    }

}
