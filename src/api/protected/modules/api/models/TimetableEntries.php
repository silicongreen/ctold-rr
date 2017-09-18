<?php

/**
 * This is the model class for table "timetable_entries".
 *
 * The followings are the available columns in table 'timetable_entries':
 * @property integer $id
 * @property integer $batch_id
 * @property integer $weekday_id
 * @property integer $class_timing_id
 * @property integer $subject_id
 * @property integer $employee_id
 * @property integer $timetable_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class TimetableEntries extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public $total; 
    public function tableName() {
        return 'timetable_entries';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('batch_id, weekday_id, class_timing_id, subject_id, employee_id, timetable_id, school_id', 'numerical', 'integerOnly' => true),
            array('created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, batch_id, weekday_id, class_timing_id, subject_id, employee_id, timetable_id, created_at, updated_at, school_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'batchDetails' => array(self::BELONGS_TO, 'Batches', 'batch_id',
                'select' => 'batchDetails.id, batchDetails.name, batchDetails.course_id,batchDetails.class_timing_set_id',
                'joinType' => 'INNER JOIN',
                'with' => array('courseDetails'),
            ),
            'classTimingDetails' => array(self::BELONGS_TO, 'ClassTimings', 'class_timing_id',
                'select' => 'classTimingDetails.id, classTimingDetails.name, classTimingDetails.start_time, classTimingDetails.end_time, classTimingDetails.is_break, classTimingDetails.class_timing_set_id',
                'joinType' => 'INNER JOIN',
            ),
            'subjectDetails' => array(self::BELONGS_TO, 'Subjects', 'subject_id',
                'select' => 'subjectDetails.id, subjectDetails.name, subjectDetails.code, subjectDetails.icon_number,subjectDetails.elective_group_id',
                'joinType' => 'INNER JOIN',
            ),
            'employeeDetails' => array(self::BELONGS_TO, 'Employees', 'employee_id',
                'select' => 'employeeDetails.id, employeeDetails.first_name, employeeDetails.middle_name, employeeDetails.last_name,employeeDetails.short_code',
                'joinType' => 'INNER JOIN',
            ),
            'timeTableDetails' => array(self::BELONGS_TO, 'Timetables', 'timetable_id',
                'select' => 'timeTableDetails.id, timeTableDetails.start_date, timeTableDetails.end_date',
                'joinType' => 'INNER JOIN',
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'batch_id' => 'Batch',
            'weekday_id' => 'Weekday',
            'class_timing_id' => 'Class Timing',
            'subject_id' => 'Subject',
            'employee_id' => 'Employee',
            'timetable_id' => 'Timetable',
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
        $criteria->compare('batch_id', $this->batch_id);
        $criteria->compare('weekday_id', $this->weekday_id);
        $criteria->compare('class_timing_id', $this->class_timing_id);
        $criteria->compare('subject_id', $this->subject_id);
        $criteria->compare('employee_id', $this->employee_id);
        $criteria->compare('timetable_id', $this->timetable_id);
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
     * @return TimetableEntries the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function classStarted($batch_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id,t.class_timing_id';
        $date = date("Y-m-d");
        $time = date("H:i:s",  strtotime("+10 minutes"));
        
        $cur_day_name = Settings::getCurrentDay($date);
        $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
        $criteria->compare('t.weekday_id', $cur_day_key);
        $criteria->addCondition("classTimingDetails.start_time<'".$time."'");
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
        $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        $criteria->limit = 15;
        $criteria->with=array('classTimingDetails',
                               'batchDetails'=>array("with"=>"courseDetails"), 
                                'subjectDetails', 
                                'employeeDetails', 
                                'timeTableDetails');
        //$criteria->with=array('classTimingDetails','timeTableDetails');
        $criteria->order = 'classTimingDetails.start_time ASC';
        $data = $this->findAll($criteria);
        $data = $this->checkDataOkAndReturn($data);
        if($data)
        {
            return true;
        }
        else
        {
            return false;
        }    
    }
    
    public function getNextStudent($batch_id,$cur_day_key = 'current', $call=1)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.weekday_id,t.class_timing_id';
        $date = date("Y-m-d");
        $time = date("H:i:s");
        if($call==1 && $cur_day_key=='current')
        {
            $cur_day_name = Settings::getCurrentDay($date);
            $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
        }
        
          
        $criteria->compare('t.weekday_id', $cur_day_key);
        
        if($call==1)
        {
            $criteria->addCondition("classTimingDetails.start_time>'".$time."'");
        }
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
        $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        //$criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        $criteria->order = 'classTimingDetails.start_time ASC';

        $criteria->limit = 10;
        $criteria->with=array('classTimingDetails',
                               'batchDetails'=>array("with"=>"courseDetails"), 
                                'subjectDetails', 
                                'employeeDetails', 
                                'timeTableDetails');

        $data = $this->findAll($criteria);
        
        if ($data) {
            $data = $this->checkDataOkAndReturnSingle($data);
           
            if($data)
            {
                return $this->formatTimeNext($data);
            }
            else if($call<7)
            {
                if($cur_day_key == 6)
                {
                    $next_day = 0;
                }
                else
                {
                    $next_day = $cur_day_key+1;
                } 
                $call++;
                $return_data = $this->getNextStudent($batch_id,$next_day,$call);
                return $return_data;
            }
            else
            {
                return false;
            } 
        }
        else if($call<7)
        {
            if($cur_day_key == 6)
            {
                $next_day = 0;
            }
            else
            {
                $next_day = $cur_day_key+1;
            } 
            $call++;
            $return_data = $this->getNextStudent($batch_id,$next_day,$call);
            return $return_data;
        }
        else
        {
            return false;
        }    

        
    }
    
    public function getNextTeacherMulti($school_id,$emplyee_id,$cur_day_key = null, $call=1,$limit=2)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.weekday_id,t.class_timing_id';
        $criteria->compare('t.school_id', $school_id);
        $date = date("Y-m-d");
        $time = date("H:i:s");
        if($call==1 && $cur_day_key!==0 && !$cur_day_key)
        {
            $cur_day_name = Settings::getCurrentDay($date);
            $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
        }
        
          
        $criteria->compare('t.weekday_id', $cur_day_key);
        
        if($call==1)
        {
            $criteria->addCondition("classTimingDetails.start_time>'".$time."'");
        }
        $criteria->compare('t.employee_id', $emplyee_id);
        $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
        $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        //$criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        $criteria->order = 'classTimingDetails.start_time ASC';

        $criteria->limit = $limit;
        $criteria->with=array('classTimingDetails',
                               'batchDetails'=>array("with"=>"courseDetails"), 
                                'subjectDetails', 
                                'employeeDetails', 
                                'timeTableDetails');

        $data = $this->findAll($criteria);
        //$data = $this->checkDataOkAndReturn($data);
        if ($data) {
            $data = $this->checkDataOkAndReturn($data);
            if($data)
            {
                return $this->formatTimeNextMulti($data);
            }
            else
            {
                 $return_data = $this->getNextTeacherMulti($school_id,$emplyee_id,$cur_day_key,$call);
                 return $return_data;
            }  
            
        }
        else if($call<7)
        {
            if($cur_day_key == 6)
            {
                $next_day = 0;
            }
            else
            {
                $next_day = $cur_day_key+1;
            } 
            $call++;
            $return_data = $this->getNextTeacherMulti($school_id,$emplyee_id,$next_day,$call);
            return $return_data;
        }
        else
        {
            return array();
        }    

        
    }
    public function formatTimeNextMulti($data)
    {
        $classess = array();
        foreach($data as $row)
        {
            $_data['batch_name'] = rtrim($row['batchDetails']->name);
            $_data['course_name'] = rtrim($row['batchDetails']['courseDetails']->course_name);
            $_data['id'] = $row['subjectDetails']->id;
            $_data['subject_code'] = $row['subjectDetails']->code;
            $_data['subject_name'] = $row['subjectDetails']->name;
            $_data['subject_icon_name'] = $row['subjectDetails']->icon_number;
            $_data['subject_icon_path'] = (!empty($row['subjectDetails']->icon_number)) ? Settings::$domain_name . '/images/icons/subjects/' . $row['subjectDetails']->icon_number : null;
            $_data['class_start_time'] = Settings::formatTime($row['classTimingDetails']->start_time);
            $_data['class_end_time'] = Settings::formatTime($row['classTimingDetails']->end_time);
            $_data['period_name'] = $row['classTimingDetails']->name;

            $middle_name = (!empty($row['employeeDetails']->middle_name)) ? $row['employeeDetails']->middle_name . ' ' : '';
            $_data['teacher_first_name'] = rtrim($row['employeeDetails']->first_name);
            $_data['teacher_full_name'] = rtrim($row['employeeDetails']->first_name . ' ' . $middle_name . $row['employeeDetails']->last_name);
            $_data['teacher_short_code'] = rtrim($row['employeeDetails']->short_code);

            $_data['weekday_id'] = $row->weekday_id;
            $_data['weekday_text'] = Settings::$ar_weekdays[$row->weekday_id];
            $classess[] = $_data;
        }
        return $classess;
    } 
    
    public function getCurrentAdmin($school_id)
    {
        $criteria = new CDbCriteria;
        $criteria->together = true;
        $criteria->select = 't.id, t.weekday_id,t.class_timing_id';
        $criteria->compare('t.school_id', $school_id);
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $cur_day_name = Settings::getCurrentDay($date);
        $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
        $criteria->compare('t.weekday_id', $cur_day_key);
        $criteria->addCondition("classTimingDetails.start_time<='".$time."'");
        $criteria->addCondition("classTimingDetails.end_time>='".$time."'");
        
        $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
        $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        $criteria->order = 'classTimingDetails.start_time ASC';
        $criteria->limit = 10;
        $criteria->with=array('classTimingDetails',
                               'batchDetails'=>array("with"=>"courseDetails"), 
                                'subjectDetails', 
                                'employeeDetails', 
                                'timeTableDetails');

        $data = $this->findAll($criteria);
        $data = $this->checkDataOkAndReturn($data);
        $class = array();
        if ($data) {
            foreach($data as $classess)
            {
                $class[] = $this->formatTimeNext($classess);
            }
            return $class;
        }
        else 
        {
            return false;
        }
        
    }
    public function getTotalClassArrayWeek($batch_name=false,$class_name=false,$batch_id=false,$employee_id=false)
    {
        $date = date("Y-m-d");
        $criteria = new CDbCriteria;
        $criteria->together = true;
        $criteria->select = 'count(t.id) as total,t.weekday_id';
        $criteria->compare('t.school_id', Yii::app()->user->schoolId);
        
        $criteria->group = "t.weekday_id";
        $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
        $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        if($batch_id)
        {
            $criteria->compare('t.batch_id', $batch_id);
        }
        else
        {
            if($batch_name)
            {
                $criteria->compare('batchDetails.name', $batch_name);
            }
            if($class_name)
            {
                $criteria->compare('courseDetails.course_name', $class_name);
            }
        }
        
        if($employee_id)
        {
            $criteria->compare('t.employee_id', $employee_id);
        }
       
        
        $criteria->with=array('classTimingDetails',
                               'batchDetails'=>array("with"=>"courseDetails"), 
                                'subjectDetails', 
                                'employeeDetails', 
                                'timeTableDetails');
        
        
        $data = $this->findAll($criteria);
        
        $week_data = array();
        
        foreach($data as $value)
        {
            $week_data[$value->weekday_id] = $value->total;
        }    
        
        return $week_data;
        
    }
    public function getTotalClassTeacher($date,$employee_id)
    {
        $cur_day_name = Settings::getCurrentDay($date);
        $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
        
        $criteria = new CDbCriteria;
        $criteria->together = true;
        $criteria->select = 'count(t.id) as total';
        $criteria->compare('t.school_id', Yii::app()->user->schoolId);
        $criteria->compare('t.weekday_id', $cur_day_key);
        $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
        $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        $criteria->compare('t.employee_id', $employee_id);
        $criteria->with=array('timeTableDetails');
        $data = $this->find($criteria);
        if($data)
        {
            return $data->total;
        }
        else
        {
            return 0;
        } 
    }
    public function getTotalClass($date,$batch_name=false,$class_name=false,$batch_id=false)
    {
        $cur_day_name = Settings::getCurrentDay($date);
        $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
        
        $criteria = new CDbCriteria;
        $criteria->together = true;
        $criteria->select = 'count(t.id) as total';
        $criteria->compare('t.school_id', Yii::app()->user->schoolId);
        $criteria->compare('t.weekday_id', $cur_day_key);
        $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
        $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        if($batch_id)
        {
            $criteria->compare('t.batch_id', $batch_id);
        }
        else
        {
            if($batch_name)
            {
                $criteria->compare('batchDetails.name', $batch_name);
            }
            if($class_name)
            {
                $criteria->compare('courseDetails.course_name', $class_name);
            }
        }
       
        
        $criteria->with=array('classTimingDetails',
                               'batchDetails'=>array("with"=>"courseDetails"), 
                                'subjectDetails', 
                                'employeeDetails', 
                                'timeTableDetails');
        $data = $this->find($criteria);
       
        if($data)
        {
            return $data->total;
        }
        else
        {
            return 0;
        }    
        
    }
    public function getNextAdmin($school_id,$cur_day_key = null, $call=1)
    {
        $criteria = new CDbCriteria;
        $criteria->together = true;
        $criteria->select = 't.id, t.weekday_id,t.class_timing_id';
        $criteria->compare('t.school_id', $school_id);
        $date = date("Y-m-d");
        $time = date("H:i:s");
        if($call==1 && $cur_day_key!==0 && !$cur_day_key)
        {
            $cur_day_name = Settings::getCurrentDay($date);
            $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
        }
        
   
        $criteria->compare('t.weekday_id', $cur_day_key);
        
        if($call==1)
        {
            $criteria->addCondition("classTimingDetails.start_time>'".$time."'");
        }
        
        $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
        $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        $criteria->order = 'classTimingDetails.start_time ASC';
        
        $criteria->limit = 15;
        
        $criteria->with=array('classTimingDetails',
                               'batchDetails'=>array("with"=>"courseDetails"), 
                                'subjectDetails', 
                                'employeeDetails', 
                                'timeTableDetails');

        $data = $this->findAll($criteria);
        $data = $this->checkDataOkAndReturn($data);
        
        $class = array();
        if ($data) {
            foreach($data as $classess)
            {
                $class[] = $this->formatTimeNext($classess);
            }
            
            return $class;
        }
        else if($call<7)
        {
            if($cur_day_key == 6)
            {
                $next_day = 0;
            }
            else
            {
                $next_day = $cur_day_key+1;
            } 
            $call++;
            $return_data = $this->getNextAdmin($school_id,$next_day,$call);
           
            return $return_data;
        }
        else
        {
            return false;
        }    

        
    }
    
    public function getNextTeacher($school_id,$emplyee_id,$cur_day_key = null, $call=1)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.weekday_id,t.class_timing_id';
        $criteria->compare('t.school_id', $school_id);
        $date = date("Y-m-d");
        $time = date("H:i:s");
        if($call==1 && $cur_day_key!==0 && !$cur_day_key)
        {
            $cur_day_name = Settings::getCurrentDay($date);
            $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
        }
        
          
        $criteria->compare('t.weekday_id', $cur_day_key);
        
        if($call==1)
        {
            $criteria->addCondition("classTimingDetails.start_time>'".$time."'");
        }
        $criteria->compare('t.employee_id', $emplyee_id);
        
        $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
        $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        //$criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        $criteria->order = 'classTimingDetails.start_time ASC';
        
        $criteria->limit = 10;
        
        $criteria->with=array('classTimingDetails',
                               'batchDetails'=>array("with"=>"courseDetails"), 
                                'subjectDetails', 
                                'employeeDetails', 
                                'timeTableDetails');

        $data = $this->findAll($criteria);
        
        
        $emp_sub = new EmployeesSubjects();
        
        $employees_subject = $emp_sub->getEmployeeSubjectElective($emplyee_id);
        
        if($employees_subject[0] && $employees_subject[1])
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id, t.weekday_id,t.class_timing_id';
            $criteria->compare('t.school_id', $school_id);
            $criteria->compare('t.weekday_id', $cur_day_key);
            $criteria->compare('t.subject_id', $employees_subject[0]);
            if($call==1)
            {
                $criteria->addCondition("classTimingDetails.start_time>'".$time."'");
            }

            $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
            $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
            $criteria->order = 'classTimingDetails.start_time ASC';
            $criteria->limit = 10;

            $criteria->with=array('classTimingDetails',
                                   'batchDetails'=>array("with"=>"courseDetails"), 
                                    'subjectDetails', 
                                    'employeeDetails', 
                                    'timeTableDetails');

            $edata = $this->findAll($criteria);
    
            $edata = $this->checkDataOkAndReturnSingle($edata);
            
            
            $edata = $this->changeElectiveSubjectData($employees_subject,$edata);
             
           
        }
        
        
        
        if ($data) {
            $data = $this->checkDataOkAndReturnSingle($data);
            if($data)
            {
                if( isset($edata) && $edata && $data['classTimingDetails']->start_time > $edata['classTimingDetails']->start_time )
                {
                    return $this->formatTimeNext($edata);
                }    
                return $this->formatTimeNext($data);
            }
            else if(isset($edata) && $edata)
            {
                return $this->formatTimeNext($edata);
            }
            else if($call<7)
            {
                if($cur_day_key == 6)
                {
                    $next_day = 0;
                }
                else
                {
                    $next_day = $cur_day_key+1;
                } 
                $call++;
                $return_data = $this->getNextTeacher($school_id,$emplyee_id,$next_day,$call);
                return $return_data;
            }
            else
            {
                return false;
            }    
        }
        else if(isset($edata) && $edata)
        {
        
            return $this->formatTimeNext($edata);
        }
        else if($call<7)
        {
            if($cur_day_key == 6)
            {
                $next_day = 0;
            }
            else
            {
                $next_day = $cur_day_key+1;
            } 
            $call++;
            $return_data = $this->getNextTeacher($school_id,$emplyee_id,$next_day,$call);
            return $return_data;
        }
        else
        {
            return false;
        }    

        
    }
    private function changeElectiveSubjectData($employees_subject,$erow=array())
    {
        $change_data = array();
       
        if(isset($erow) && $erow)
        {
            foreach($employees_subject[1] as $value)
            {
                if(isset($erow) && $erow && $erow['subjectDetails']->elective_group_id == $value->elective_group_id)
                {
                    $change_data_main =  $erow;
                    $change_data_main['subjectDetails'] = $value;
                    $change_data = $change_data_main;
                    break;

                }
            } 
            
        }
        return $change_data;
    }        
    public function formatTimeNext($row)
    {
        if(!$row['subjectDetails']->elective_group_id || !Yii::app()->user->isStudent )
        {
            $_data['batch_name'] = rtrim($row['batchDetails']->name);
            $_data['course_name'] = rtrim($row['batchDetails']['courseDetails']->course_name." ".$row['batchDetails']['courseDetails']->section_name);
            $_data['id'] = $row['subjectDetails']->id;
            $_data['subject_code'] = $row['subjectDetails']->code;
            $_data['subject_name'] = $row['subjectDetails']->name;
            $_data['subject_icon_name'] = $row['subjectDetails']->icon_number;
            $_data['subject_icon_path'] = (!empty($row['subjectDetails']->icon_number)) ? Settings::$domain_name . '/images/icons/subjects/' . $row['subjectDetails']->icon_number : null;
            $_data['class_start_time'] = Settings::formatTime($row['classTimingDetails']->start_time);
            $_data['class_end_time'] = Settings::formatTime($row['classTimingDetails']->end_time);
            $_data['period_name'] = $row['classTimingDetails']->name;

            $middle_name = (!empty($row['employeeDetails']->middle_name)) ? $row['employeeDetails']->middle_name . ' ' : '';
            $_data['teacher_first_name'] = rtrim($row['employeeDetails']->first_name);
            $_data['teacher_full_name'] = rtrim($row['employeeDetails']->first_name . ' ' . $middle_name . $row['employeeDetails']->last_name);
            $_data['teacher_short_code'] = rtrim($row['employeeDetails']->short_code);

            $_data['weekday_id'] = $row->weekday_id;
            $_data['weekday_text'] = Settings::$ar_weekdays[$row->weekday_id];
        }
        else 
        {
            $_data = $this->changeToEsubject($row);
            
        }
        return $_data;
    }  
    
    private function changeToEsubject($row)
    {
        $std_subject = new StudentsSubjects();
        $std_subjects = $std_subject->getStudentSubject(Yii::app()->user->profileId);

        $std_ids = array();

        foreach($std_subjects as $value)
        {
            $std_ids[] = $value->id;
        } 
        $subjects = new Subjects();
        $e_subject = $subjects->getSubjectElectiveGroup($row['subjectDetails']->elective_group_id);
        $std_subject_assign = array();
        foreach($e_subject as $value)
        {
            if(in_array($value->id, $std_ids))
            {
                $std_subject_assign = $value;
                break;
            }
        } 

        if($std_subject_assign)
        {
            $employee_sub = new EmployeesSubjects();
            $employees = $employee_sub->getEmployeeSubject($std_subject_assign->id);
            if($employees)
            {
                $_data['batch_name'] = rtrim($row['batchDetails']->name);
                $_data['course_name'] = rtrim($row['batchDetails']['courseDetails']->course_name." ".$row['batchDetails']['courseDetails']->section_name);
                $middle_name = (!empty($employees[0]['employee']->middle_name)) ? $employees[0]['employee']->middle_name . ' ' : '';
                $_data['teacher_first_name'] = rtrim($employees[0]['employee']->first_name);
                $_data['teacher_full_name'] = rtrim($employees[0]['employee']->first_name . ' ' . $middle_name . $employees[0]['employee']->last_name);
                $_data['teacher_short_code'] = rtrim($employees[0]['employee']->short_code);
                $_data['id'] = $std_subject_assign->id;
                $_data['subject_code'] = $std_subject_assign->code;
                $_data['subject_name'] = $std_subject_assign->name;
                $_data['subject_icon_name'] = $std_subject_assign->icon_number;
                $_data['subject_icon_path'] = (!empty($std_subject_assign->icon_number)) ? Settings::$domain_name . '/images/icons/subjects/' . $std_subject_assign->icon_number : null;
                $_data['class_start_time'] = Settings::formatTime($row['classTimingDetails']->start_time);
                $_data['class_end_time'] = Settings::formatTime($row['classTimingDetails']->end_time);
                $_data['period_name'] = $row['classTimingDetails']->name;
                $_data['weekday_id'] = $row->weekday_id;
                $_data['weekday_text'] = Settings::$ar_weekdays[$row->weekday_id];
            }
            else 
            {
                return false;
            }
        }
        else 
        {
            return false;
        }
        return $_data;
    }        
    
    public function getTimeTablesTeacher($school_id,$date,$emplyee_id,$day_id = false)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.weekday_id,t.class_timing_id';
        $criteria->compare('t.school_id', $school_id);
        if ($day_id===false) {
            $cur_day_name = Settings::getCurrentDay($date);
            $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
        }
        else
        {
            $cur_day_key = $day_id;
        }
        $criteria->compare('t.weekday_id', $cur_day_key);
        $criteria->compare('t.employee_id', $emplyee_id);
        
        $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
        $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        $criteria->addCondition("subjectDetails.elective_group_id is null");
        $criteria->order = 'classTimingDetails.start_time ASC';
       
        $criteria->with=array('classTimingDetails',
                               'batchDetails'=>array("with"=>"courseDetails"), 
                                'subjectDetails', 
                                'employeeDetails', 
                                'timeTableDetails');

        $data = $this->findAll($criteria);
        
        $data = $this->checkDataOkAndReturn($data);
        
        
        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.weekday_id,t.class_timing_id';
        $criteria->compare('t.school_id', $school_id);
        $criteria->compare('t.weekday_id', $cur_day_key);
        $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
        $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        $criteria->addCondition("subjectDetails.elective_group_id is not null");
        $criteria->order = 'classTimingDetails.start_time ASC';
       
        $criteria->with=array('classTimingDetails',
                               'batchDetails'=>array("with"=>"courseDetails"), 
                                'subjectDetails', 
                                'employeeDetails', 
                                'timeTableDetails');

        $data2 = $this->findAll($criteria); 
        $data2 = $this->checkDataOkAndReturn($data2);
        
        
        
        $emp_sub = new EmployeesSubjects();
        
        $employees_subject = $emp_sub->getEmployeeElective($emplyee_id);
   
        
        
        $all_routine = $data;
        if($employees_subject)
        {
            if (!empty($data2)) {

                foreach($data2 as $row)
                {
                    
                   $sub_obj = new Subjects();
                   $e_subject = $sub_obj->getSubjectElectiveGroup($row['subjectDetails']->elective_group_id);
                  
                   if($e_subject)
                   {    $i = 0;
                        $new_row = array();
                        foreach($e_subject as $esvalue)
                        {
                            
                             if(in_array($esvalue->id, $employees_subject))
                             {
                                 print_r($row);
                                 echo $esvalue->name."|||";
                                 $new_row = $row;
                                
                                 $new_row['subjectDetails'] = $esvalue;
                                
                                 $all_routine[] = $new_row;
                                
                                 
                             }
                        }
                   }

                }
            }  
        }
        exit;
        
      
        
        $time_table = array();
        if($all_routine)
        {
            $time_table = $this->formatTimeTable($all_routine, false, true);
            usort($time_table, function($a, $b) {
                            return $a['class_start_time'] - $b['class_start_time'];
            });
            return $time_table;
            
        } 
        return false;
        
    }
    
    private function checkDataOkAndReturnSingle($obj)
    {
        $new_obj = array();
        if($obj)
        {
            foreach($obj as $value)
            {
                if($value['batchDetails']->class_timing_set_id == $value['classTimingDetails']->class_timing_set_id)
                {
                    $new_obj = $value;
                    break;
                }
            }    
        }
        return $new_obj;
       
        
    }  
    
    private function checkDataOkAndReturn($obj)
    {
        $new_obj = array();
        if($obj)
        {
            foreach($obj as $value)
            {
                if($value['batchDetails']->class_timing_set_id == $value['classTimingDetails']->class_timing_set_id)
                {
                    $new_obj[] = $value;
                }
            }    
        }
        return $new_obj;
        
    }        

    public function getTimeTables($school_id, $date = '', $b_full_week = false, $batch_id = null,$day_id=false) {

        $criteria = new CDbCriteria;

        $criteria->select = 't.id, t.weekday_id,t.class_timing_id';
        $criteria->compare('t.school_id', $school_id);

        if (!$b_full_week) {
            $cur_day_name = Settings::getCurrentDay($date);
            $cur_day_key[] = Settings::$ar_weekdays_key[$cur_day_name];
        } 
        else if($day_id!==false)
        {
            $cur_day_key[] = $day_id;
            $b_full_week = false;
        }
        else
        {
            $weekdays = new Weekdays;
            $weekdays = $weekdays->getWorkingDays($school_id);
            $cur_day_key = $weekdays;
        }
        
        

        
        $criteria->compare('t.batch_id', $batch_id);
       

        $criteria->addInCondition('t.weekday_id', $cur_day_key);
        $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
        $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
         
        
        $criteria->order = 'classTimingDetails.start_time ASC';
        $criteria->with=array('classTimingDetails',
                                   'batchDetails'=>array("with"=>"courseDetails"), 
                                    'subjectDetails', 
                                    'employeeDetails', 
                                    'timeTableDetails');

        $data = $this->findAll($criteria);
        
        
        
        $data = $this->checkDataOkAndReturn($data);

   
        if (!empty($data)) {
            return $this->formatTimeTable($data, $b_full_week,false);
        }

        return false;
    }

    public function formatTimeTable($_obj_time_table, $b_full_week = false, $teacher=false) {

        
        
        
        
        $ar_formatted_data = array();

        if (!$b_full_week) {

            foreach ($_obj_time_table as $row) {
                if(!$row['subjectDetails']->elective_group_id || !Yii::app()->user->isStudent)
                {
                    $middle_name = (!empty($row['employeeDetails']->middle_name)) ? $row['employeeDetails']->middle_name . ' ' : '';

                    if($teacher==false)
                    {
                        $_data['teacher_first_name'] = rtrim($row['employeeDetails']->first_name);
                        $_data['teacher_full_name'] = rtrim($row['employeeDetails']->first_name . ' ' . $middle_name . $row['employeeDetails']->last_name);
                        $_data['teacher_short_code'] = rtrim($row['employeeDetails']->short_code);
                    }
                    else
                    {
                        $_data['batch_name'] = rtrim($row['batchDetails']->name);
                        $_data['course_name'] = rtrim($row['batchDetails']['courseDetails']->course_name)." ".$row['batchDetails']['courseDetails']->section_name;
                    } 
                    $_data['id'] = $row['subjectDetails']->id;
                    $_data['subject_code'] = $row['subjectDetails']->code;
                    $_data['subject_name'] = $row['subjectDetails']->name;
                    $_data['subject_icon_name'] = $row['subjectDetails']->icon_number;
                    $_data['subject_icon_path'] = (!empty($row['subjectDetails']->icon_number)) ? Settings::$domain_name . '/images/icons/subjects/' . $row['subjectDetails']->icon_number : null;
                    $_data['class_start_time'] = Settings::formatTime($row['classTimingDetails']->start_time);
                    $_data['class_end_time'] = Settings::formatTime($row['classTimingDetails']->end_time);
                    $_data['period_name'] = $row['classTimingDetails']->name;
                    
                    
                    $_data['classtime_set'] = $row['batchDetails']->class_timing_set_id;
                    $_data['classtime_set2'] = $row['classTimingDetails']->class_timing_set_id;
                    
                    
                    $_data['weekday_id'] = $row->weekday_id;
                    $_data['weekday_text'] = Settings::$ar_weekdays[$row->weekday_id];
                    
                    
                    
                }
                else
                {
                    $check_data = $this->changeToEsubject($row);
                    if($check_data)
                    {
                        $_data = $check_data;
                    }
                    else
                    {
                        continue;
                    }   
                }    

                $ar_formatted_data[] = $_data;
            }
        } else {

            $ar_unique_times = array();
            foreach ($_obj_time_table as $row) {

                $ar_key = Settings::formatTime($row['classTimingDetails']->start_time) . '-' . Settings::formatTime($row['classTimingDetails']->end_time);

                foreach ($_obj_time_table as $value) {
                    $ar_time_to_match = Settings::formatTime($value['classTimingDetails']->start_time) . '-' . Settings::formatTime($value['classTimingDetails']->end_time);

                    if ($ar_key == $ar_time_to_match) {
                        $ar_unique_times[] = $ar_time_to_match;
                    }
                }
            }

            $ar_unique_times = array_unique($ar_unique_times);

            foreach ($ar_unique_times as $time) {
                $ar_formatted_data[] = $this->makeTimeTableArray($_obj_time_table, $time);
            }
        }

        return $ar_formatted_data;
    }

    private function makeTimeTableArray($obj_data, $key) {

        $ar_formatted_data = array();
        foreach ($obj_data as $row) {

            $ar_key_to_match = Settings::formatTime($row['classTimingDetails']->start_time) . '-' . Settings::formatTime($row['classTimingDetails']->end_time);

            if ($key == $ar_key_to_match) {

                $middle_name = (!empty($row['employeeDetails']->middle_name)) ? $row['employeeDetails']->middle_name . ' ' : '';

                $_data['teacher_first_name'] = rtrim($row['employeeDetails']->first_name);
                $_data['teacher_full_name'] = rtrim($row['employeeDetails']->first_name . ' ' . $middle_name . $row['employeeDetails']->last_name);
                $_data['teacher_short_code'] = rtrim($row['employeeDetails']->short_code);
                $_data['subject_code'] = $row['subjectDetails']->code;
                $_data['subject_name'] = $row['subjectDetails']->name;
                $_data['weekday_id'] = $row->weekday_id;
                $_data['weekday_text'] = Settings::$ar_weekdays[$row->weekday_id];

                $ar_formatted_data[$key][] = $_data;
            }
        }

        return $ar_formatted_data;
    }

}
