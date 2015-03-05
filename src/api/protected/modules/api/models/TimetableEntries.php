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
                'select' => 'batchDetails.id, batchDetails.name, batchDetails.course_id',
                'joinType' => 'INNER JOIN',
                'with' => array('courseDetails'),
            ),
            'classTimingDetails' => array(self::BELONGS_TO, 'ClassTimings', 'class_timing_id',
                'select' => 'classTimingDetails.id, classTimingDetails.name, classTimingDetails.start_time, classTimingDetails.end_time, classTimingDetails.is_break, classTimingDetails.class_timing_set_id',
                'joinType' => 'INNER JOIN',
            ),
            'subjectDetails' => array(self::BELONGS_TO, 'Subjects', 'subject_id',
                'select' => 'subjectDetails.id, subjectDetails.name, subjectDetails.code, subjectDetails.icon_number',
                'joinType' => 'INNER JOIN',
            ),
            'employeeDetails' => array(self::BELONGS_TO, 'Employees', 'employee_id',
                'select' => 'employeeDetails.id, employeeDetails.first_name, employeeDetails.middle_name, employeeDetails.last_name',
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
        $criteria->select = 't.id';
        $date = date("Y-m-d");
        $time = date("H:i:s",  strtotime("+10 minutes"));
        
        $cur_day_name = Settings::getCurrentDay($date);
        $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
        $criteria->compare('t.weekday_id', $cur_day_key);
        $criteria->addCondition("classTimingDetails.start_time<'".$time."'");
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->addCondition("timeTableDetails.start_date <= '" . $date . "' ");
        $criteria->addCondition("timeTableDetails.end_date >= '" . $date . "' ");
        $criteria->limit = 1;
        $criteria->with=array('classTimingDetails','timeTableDetails');
        $criteria->order = 'classTimingDetails.start_time ASC';
        $data = $this->find($criteria);
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
        $criteria->select = 't.id, t.weekday_id';
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

        $criteria->limit = 1;
        $criteria->with=array('classTimingDetails',
                               'batchDetails'=>array("with"=>"courseDetails"), 
                                'subjectDetails', 
                                'employeeDetails', 
                                'timeTableDetails');

        $data = $this->find($criteria);
        if ($data) {
            
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
    
    public function getNextTeacher($school_id,$emplyee_id,$cur_day_key = null, $call=1)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.weekday_id';
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

        $criteria->limit = 1;
        $criteria->with=array('classTimingDetails',
                               'batchDetails'=>array("with"=>"courseDetails"), 
                                'subjectDetails', 
                                'employeeDetails', 
                                'timeTableDetails');

        $data = $this->find($criteria);
        if ($data) {
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
            $return_data = $this->getNextTeacher($school_id,$emplyee_id,$next_day,$call);
            return $return_data;
        }
        else
        {
            return false;
        }    

        
    }
    public function formatTimeNext($row)
    {
        $_data['batch_name'] = rtrim($row['batchDetails']->name);
        $_data['course_name'] = rtrim($row['batchDetails']['courseDetails']->course_name);
        $_data['subject_code'] = $row['subjectDetails']->code;
        $_data['subject_name'] = $row['subjectDetails']->name;
        $_data['subject_icon_name'] = $row['subjectDetails']->icon_number;
        $_data['subject_icon_path'] = (!empty($row['subjectDetails']->icon_number)) ? Settings::$domain_name . '/images/icons/subjects/' . $row['subjectDetails']->icon_number : null;
        $_data['class_start_time'] = Settings::formatTime($row['classTimingDetails']->start_time);
        $_data['class_end_time'] = Settings::formatTime($row['classTimingDetails']->end_time);
        
        $middle_name = (!empty($row['employeeDetails']->middle_name)) ? $row['employeeDetails']->middle_name . ' ' : '';
        $_data['teacher_first_name'] = rtrim($row['employeeDetails']->first_name);
        $_data['teacher_full_name'] = rtrim($row['employeeDetails']->first_name . ' ' . $middle_name . $row['employeeDetails']->last_name);
        
        $_data['weekday_id'] = $row->weekday_id;
        $_data['weekday_text'] = Settings::$ar_weekdays[$row->weekday_id];
        return $_data;
    }        
    
    public function getTimeTablesTeacher($school_id,$date,$emplyee_id,$day_id = null)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.weekday_id';
        $criteria->compare('t.school_id', $school_id);
        if (!$day_id) {
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
        $criteria->order = 't.weekday_id, t.class_timing_id ASC';
       
        $criteria->with=array('classTimingDetails',
                               'batchDetails'=>array("with"=>"courseDetails"), 
                                'subjectDetails', 
                                'employeeDetails', 
                                'timeTableDetails');

        $data = $this->findAll($criteria);
        if (!empty($data)) {
            return $this->formatTimeTable($data, false, true);
        }

        return false;
    }

    public function getTimeTables($school_id, $date = '', $b_full_week = false, $batch_id = null,$day_id=false) {

        $criteria = new CDbCriteria;

        $criteria->select = 't.id, t.weekday_id';
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
        $criteria->order = 't.weekday_id, t.class_timing_id ASC';

        $data = $this->with('classTimingDetails', 'subjectDetails', 'employeeDetails', 'timeTableDetails')->findAll($criteria);

       
        if (!empty($data)) {
            return $this->formatTimeTable($data, $b_full_week,false);
        }

        return false;
    }

    public function formatTimeTable($_obj_time_table, $b_full_week = false, $teacher=false) {

        $ar_formatted_data = array();

        if (!$b_full_week) {

            foreach ($_obj_time_table as $row) {

                $middle_name = (!empty($row['employeeDetails']->middle_name)) ? $row['employeeDetails']->middle_name . ' ' : '';

                if($teacher==false)
                {
                    $_data['teacher_first_name'] = rtrim($row['employeeDetails']->first_name);
                    $_data['teacher_full_name'] = rtrim($row['employeeDetails']->first_name . ' ' . $middle_name . $row['employeeDetails']->last_name);
                }
                else
                {
                    $_data['batch_name'] = rtrim($row['batchDetails']->name);
                    $_data['course_name'] = rtrim($row['batchDetails']['courseDetails']->course_name);
                }    
                $_data['subject_code'] = $row['subjectDetails']->code;
                $_data['subject_name'] = $row['subjectDetails']->name;
                $_data['subject_icon_name'] = $row['subjectDetails']->icon_number;
                $_data['subject_icon_path'] = (!empty($row['subjectDetails']->icon_number)) ? Settings::$domain_name . '/images/icons/subjects/' . $row['subjectDetails']->icon_number : null;
                $_data['class_start_time'] = Settings::formatTime($row['classTimingDetails']->start_time);
                $_data['class_end_time'] = Settings::formatTime($row['classTimingDetails']->end_time);
                $_data['weekday_id'] = $row->weekday_id;
                $_data['weekday_text'] = Settings::$ar_weekdays[$row->weekday_id];

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
