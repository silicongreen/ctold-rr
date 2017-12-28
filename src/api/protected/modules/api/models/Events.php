<?php

/**
 * This is the model class for table "events".
 *
 * The followings are the available columns in table 'events':
 * @property integer $id
 * @property integer $event_category_id
 * @property string $title
 * @property string $description
 * @property string $start_date
 * @property string $end_date
 * @property integer $is_common
 * @property integer $is_holiday
 * @property integer $is_exam
 * @property integer $is_due
 * @property string $created_at
 * @property string $updated_at
 * @property integer $origin_id
 * @property string $origin_type
 * @property integer $school_id
 */
class Events extends CActiveRecord {

    public $num_rows = 0;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'events';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('is_common, event_category_id, is_holiday, is_exam, is_due, origin_id, school_id', 'numerical', 'integerOnly' => true),
            array('title, origin_type', 'length', 'max' => 255),
            array('description, start_date, end_date, created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_category_id, title, description, start_date, end_date, is_common, is_holiday, is_exam, is_due, created_at, updated_at, origin_id, origin_type, school_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'eventBatch' => array(self::HAS_MANY, 'BatchEvents', 'event_id',
                'select' => 'eventBatch.id',
                'joinType' => 'LEFT JOIN',
            ),
            'eventDepartment' => array(self::HAS_MANY, 'EmployeeDepartmentEvents', 'event_id',
                'select' => 'eventDepartment.id',
                'joinType' => 'LEFT JOIN',
            //'with' => array('employeeDepartmentDetails'),
            ),
            'eventUser' => array(self::HAS_MANY, 'UserEvents', 'event_id',
                'select' => 'eventUser.id',
                'joinType' => 'LEFT JOIN',
                'with' => array('userDetails'),
            ),
            'eventCategory' => array(self::BELONGS_TO, 'EventCategory', 'event_category_id',
                'select' => 'eventCategory.id, eventCategory.name, eventCategory.is_club, eventCategory.icon_number',
                'joinType' => 'LEFT JOIN',
            ),
            'eventAcknowledge' => array(self::HAS_MANY, 'EventAcknowledges', 'event_id',
                'select' => 'eventAcknowledge.event_id, eventAcknowledge.acknowledged_by, eventAcknowledge.acknowledged_by_id, eventAcknowledge.status',
                'joinType' => 'LEFT JOIN',
            ),
            'examDetails' => array(self::BELONGS_TO, 'Exams', 'exam_group_id',
                'joinType' => 'LEFT JOIN',
                'with' => array('Subjects'),
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'event_category_id' => 'Category',
            'title' => 'Title',
            'description' => 'Description',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'is_common' => 'Is Common',
            'is_holiday' => 'Is Holiday',
            'is_exam' => 'Is Exam',
            'is_due' => 'Is Due',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'origin_id' => 'Origin',
            'origin_type' => 'Origin Type',
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
        $criteria->compare('event_category_id', $this->event_category_id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('start_date', $this->start_date, true);
        $criteria->compare('end_date', $this->end_date, true);
        $criteria->compare('is_common', $this->is_common);
        $criteria->compare('is_holiday', $this->is_holiday);
        $criteria->compare('is_exam', $this->is_exam);
        $criteria->compare('is_due', $this->is_due);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('origin_id', $this->origin_id);
        $criteria->compare('origin_type', $this->origin_type, true);
        $criteria->compare('school_id', $this->school_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Events the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function getHolidayAsArray($start_date,$end_date)
    {
        $holiday = new Events();
        $holiday_array = $holiday->getHolidayMonth($start_date, $end_date, Yii::app()->user->schoolId); 
        $holiday_array_return = array();
        foreach ($holiday_array as $value)
        {
            $start_holiday = new DateTime($value['start_date']);
            $end_holiday = new DateTime($value['end_date']);
            $holiday_interval = DateInterval::createFromDateString('1 day');
            $holiday_period = new DatePeriod($start_holiday, $holiday_interval, $end_holiday);
            foreach ($holiday_period as $hdt)
            {
              $holiday_array_return[] = $hdt->format("Y-m-d");
            }
            $holiday_array_return[] = $end_holiday->format("Y-m-d");
        }
        return $holiday_array_return;
    }        

    public function getHolidayMonth($start_date, $end_date, $school_id,$batch_id = 0) {

        $criteria = new CDbCriteria;
        $criteria->compare('t.school_id', $school_id);
        $criteria->compare('t.is_holiday', 1);
        $criteria->compare('t.is_published', 1);
        $with = array('eventCategory');
        
        if($batch_id > 0)
        {
            $with[] = 'eventBatch';
            $criteria->addCondition("(eventBatch.batch_id = '" . $batch_id . "' or t.is_common=1)");
        }
        
        $criteria->addCondition("DATE(end_date) >= '" . $start_date . "'");
        $criteria->addCondition("DATE(start_date) <= '" . $end_date . "'");
        
        $data = $this->with($with)->findAll($criteria);
        $return_array = array();
        if ($data != NULL)
            foreach ($data as $value) {
                if(date("Y-m-d", strtotime($value->end_date))>$end_date)
                {
                    $last_date = $end_date;
                }
                else
                {
                    $last_date = date("Y-m-d", strtotime($value->end_date));
                }    
                $merge = array();
                $merge['title'] = $value->title;
                $merge['description'] = $value->description;
                $merge['start_date'] = date("Y-m-d", strtotime($value->start_date));
                $merge['end_date'] = $last_date;

                $return_array[] = $merge;
            }
           
        return $return_array;
    }
    
    public function getSingleEvents($id) {

        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.title,t.is_holiday,t.icon_file_name,t.school_id,t.created_at, t.description, t.start_date, t.end_date, t.is_common, t.event_category_id,t.fees';
        $criteria->compare('t.id',$id);
        //$criteria->compare('t.is_published', 1);
        
        $with = array('eventCategory');
        $criteria->addCondition('(eventCategory.is_club is NULL or  eventCategory.is_club != 1)');
       
        $obj_event = $this->with($with)->find($criteria);
        if($obj_event)
        {
            $formatted_event = $this->formatSingleEvents($obj_event);
            return $formatted_event;
        }
        return false;
        
    }
    public function formatSingleEvents($row) {
        $_data['event_id'] = $row->id;
        $_data['event_title'] = $row->title;
        $_data['created_at'] = $row->created_at;
        $_data['event_category_id'] = $row->event_category_id;
        if($row->event_category_id)
        {
            $_data['event_category_name'] = $row['eventCategory']->name;
            $_data['event_icon_name'] = $row['eventCategory']->icon_number;
            $_data['event_icon_path'] = (!empty($row['eventCategory']->icon_number)) ? Settings::$domain_name . '/images/icons/events/' . $row['eventCategory']->icon_number : null;
        }
        $_data['event_icon'] = "";
        if(!empty($row->icon_file_name))
        {
            $sd = new SchoolDomains();
            $domains = $sd->getSchoolDomainBySchoolId(Yii::app()->user->schoolId);
            $_data['event_icon']  = "http://".$domains->domain."/events/icon/".$row->id."/original/".$row->icon_file_name;
        }
        
        $_data['event_start_date'] = $row->start_date;
        $_data['event_end_date'] = $row->end_date;
        $_data['event_description'] = $row->description;
        $_data['event_common'] = $row->is_common;
        $_data['is_holiday'] = $row->is_holiday;
        $_data['upcomming'] = false;
        if(date("Y-m-d",  strtotime($row->start_date))>=date("Y-m-d"))
        {
            $_data['upcomming'] = true;
        }        
        $_data['club_fees'] = (float) $row->fees;
        $event_ack = new EventAcknowledges;
        $_data['event_acknowledge'] = (int) $event_ack->getEventAcknowledgeData($row->school_id, $row->id);
        return $_data;
    }
    
    
     public function getEventsTerm($term, $batch_id=false,$student_id=false) 
       {
         
        $criteria = new CDbCriteria;
        $criteria->together = true;
        $criteria->select = 't.id, t.title,t.is_holiday, t.description, t.start_date, t.end_date, t.is_common, t.event_category_id';
        $criteria->compare('t.is_published', 1);


        $with = array('eventCategory');

        if (Yii::app()->user->isStudent || Yii::app()->user->isParent) {
            $with[] = 'eventBatch';
            $criteria->addCondition("(eventBatch.batch_id = '" . $batch_id . "' or t.is_common=1)");
        }
        else {
            $employee = new Employees;
            $employeeData = $employee->getEmployeeDepartment();
            
            if($employeeData)
            {
                $with[] = 'eventDepartment';
                $criteria->addCondition("(eventDepartment.employee_department_id = '" . $employeeData->employee_department_id . "' or t.is_common=1)");
            }
            else
            {
                $criteria->compare('t.is_common', 1);
            }    
           
        }   
        $criteria->addCondition("t.title like '%".$term."%'");
        $criteria->addCondition("DATE(t.start_date) >= '" . date("Y-m-d") . "'");
        $criteria->compare('t.school_id', Yii::app()->user->schoolId);
        $criteria->order = 't.start_date ASC';
        $criteria->limit = 5;

        $obj_events = $this->with($with)->findAll($criteria);

        if (!empty($obj_events)) {

            $formatted_events = $this->formatEvents($obj_events, Yii::app()->user->schoolId, $student_id);
            return $formatted_events;
        }

        return array();
    }
    
    

    public function getEvents($school_id, $from_date, $to_date = NULL, $page = 1, $page_size = 10, $category_id = null, $b_is_club = false, $b_count = false, $b_archive = false, $student_id = NULL) {

        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.title,t.is_holiday,t.icon_file_name, t.description, t.start_date, t.end_date, t.is_common, t.event_category_id';
        $criteria->compare('t.is_published', 1);

        if ($b_count) {
            $criteria->select = 'COUNT(t.id) AS num_rows';
        }

        $with = array('eventCategory');

        if (Yii::app()->user->isStudent) {
            $with[] = 'eventBatch';
            //$criteria->compare('eventBatch.batch_id', Yii::app()->user->batchId);
            $criteria->addCondition("(eventBatch.batch_id = '" . Yii::app()->user->batchId . "' or t.is_common=1)");
        }

        else if (Yii::app()->user->isTeacher) {
            $employee = new Employees;
            $employee = $employee->getEmployeeDepartment();

            $with[] = 'eventDepartment';
            $criteria->addCondition("(eventDepartment.employee_department_id = '" . $employee->employee_department_id . "' or t.is_common=1)");
           // $criteria->compare('eventDepartment.employee_department_id', $employee->employee_department_id);
        }
        else
        {
             $criteria->compare('t.is_common', 1);
        }    

        

        if (!$b_archive && !$b_is_club) {

            $criteria->addCondition("DATE(t.start_date) >= '" . $from_date . "'");
            if (!empty($to_date)) {
                $criteria->addCondition("DATE(t.end_date) <= '" . $to_date . "'");
            }
        } elseif ($b_archive && !$b_is_club) {
            $criteria->addCondition("DATE(t.end_date) < '" . date('Y-m-d', time()) . "'");
        }

        //$criteria->compare('t.event_category_id !', 1);
        if (!empty($category_id)) {
            $criteria->compare('t.event_category_id', $category_id);
        }

        if ($b_is_club) {
            $criteria->select .= ', t.fees';
            $criteria->compare('eventCategory.is_club', 1);
        } else {
            $criteria->addCondition('(eventCategory.is_club is NULL or  eventCategory.is_club != 1)');
        }

       // $criteria->addCondition("(t.origin_id IS NULL OR t.origin_id = '') AND (t.origin_type IS NULL OR t.origin_type = '')");
        $criteria->compare('eventCategory.status', 1);
        $criteria->compare('t.school_id', $school_id);
       // $criteria->compare('t.is_holiday', 0);

        if (!$b_count) {
            if($b_archive)
            {
                $criteria->order = 't.start_date DESC';
            }
            else
            {
                $criteria->order = 't.start_date ASC'; 
            }    
            $criteria->together = true;
            $start = ($page - 1) * $page_size;
            $criteria->offset = $start;
            $criteria->limit = $page_size;
        }

        $obj_events = $this->with($with)->findAll($criteria);

        if (!empty($obj_events)) {

            if (!$b_count) {
                $formatted_events = $this->formatEvents($obj_events, $school_id, $student_id);
            } else {
                $formatted_events = $obj_events[0]->num_rows;
            }

            return $formatted_events;
        }

        return false;
    }

    public function formatEvents($obj_events, $school_id = null, $student_id = NULL) {

        $formatted_events = array();
        foreach ($obj_events as $row) {

            if ($row['eventCategory']->is_club == 0) {

                $_data['event_id'] = $row->id;
                $_data['event_title'] = $row->title;
                $_data['event_category_id'] = $row->event_category_id;
                $_data['event_category_name'] = $row['eventCategory']->name;
                $_data['event_icon_name'] = $row['eventCategory']->icon_number;
                $_data['event_icon_path'] = (!empty($row['eventCategory']->icon_number)) ? Settings::$domain_name . '/images/icons/events/' . $row['eventCategory']->icon_number : null;
                $_data['event_icon'] = "";
                if(!empty($row->icon_file_name))
                {
                    $sd = new SchoolDomains();
                    $domains = $sd->getSchoolDomainBySchoolId(Yii::app()->user->schoolId);
                    $_data['event_icon']  = "http://".$domains->domain."/events/icon/".$row->id."/original/".$row->icon_file_name;
                }
                
                $_data['event_start_date'] = $row->start_date;
                $_data['event_end_date'] = $row->end_date;
                $_data['event_description'] = $row->description;
                $_data['event_common'] = $row->is_common;
                
                $_data['is_holiday'] = $row->is_holiday;

                $event_ack = new EventAcknowledges;
                $_data['event_acknowledge'] = (int) $event_ack->getEventAcknowledgeData($school_id, $row->id);
            } elseif ($row['eventCategory']->is_club == 1) {

                $_data['club_id'] = $row->id;
                $_data['club_activity'] = $row->title;
                $_data['club_category_id'] = $row->event_category_id;
                $_data['club_category_name'] = $row['eventCategory']->name;
                $_data['club_icon_name'] = $row['eventCategory']->icon_number;
                $_data['club_icon_path'] = (!empty($row['eventCategory']->icon_number)) ? Settings::$domain_name . '/images/icons/clubs/' . $row['eventCategory']->icon_number : null;
                $_data['club_schedule'] = $row->description;
                $_data['club_fees'] = (float) $row->fees;
                $_data['club_common'] = $row->is_common;
                $_data['is_holiday'] = $row->is_holiday;

                $event_ack = new EventAcknowledges;
                $_data['club_acknowledge'] = (int) $event_ack->getClubAcknowledgeData($school_id, $row->id, $student_id);
            }

            $formatted_events[] = $_data;
        }

        return $formatted_events;
    }

    public function eventAcknowledge($obj_acks) {

        $formatted_acks = array();
        foreach ($obj_acks as $rows) {
            $_data['acknowledge_by_key'] = $rows->acknowledged_by;
            $_data['acknowledge_by_text'] = Settings::$ar_notice_acknowledge_by[$rows->acknowledged_by];
            $_data['acknowledge_by_id'] = $rows->acknowledged_by_id;
            $_data['acknowledge_status'] = $rows->status;
            $_data['acknowledge_msg'] = Settings::$ar_event_status[$rows->status];

            $formatted_acks[] = $_data;
        }

        return $formatted_acks;
    }

    public function getAcademicCalendar($school_id, $from_date, $to_date = NULL, $batch_id = null, $origin = 0, $page_no = 1, $page_size = 10, $b_count = false,$not_use_origin=false) {

        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.title, t.description, t.start_date';
        $criteria->compare('t.is_published', 1);

        if ($b_count) {
            $criteria->select = 'COUNT(t.id) AS num_rows';
        }

        $with = array('eventCategory');

        if (Yii::app()->user->isStudent) {
            $with[] = 'eventBatch';
            $criteria->addCondition("(eventBatch.batch_id = '" . Yii::app()->user->batchId . "' or t.is_common=1)");
           // $criteria->compare('eventBatch.batch_id', Yii::app()->user->batchId);
        }

        else if (Yii::app()->user->isTeacher) {
            $employee = new Employees;
            $employee = $employee->getEmployeeDepartment();

            $with[] = 'eventDepartment';
            $criteria->addCondition("(eventDepartment.employee_department_id = '" . $employee->employee_department_id . "' or t.is_common=1)");
            //$criteria->compare('eventDepartment.employee_department_id', $employee->employee_department_id);
        }
        else
        {
            $criteria->compare('t.is_common', 1);
        }    

        

        $criteria->addCondition("DATE(t.end_date) >= '" . date("Y-m-d") . "'");
        //$criteria->addCondition("DATE(t.end_date) <= '" . $to_date . "'");

        $criteria->addCondition('(eventCategory.is_club is NULL or  eventCategory.is_club != 1)');

        if(!$not_use_origin)
        {
            $extra_condition = Settings::$ar_event_origins[$origin];
            $criteria->addCondition("{$extra_condition['condition']}");
        }

        //$criteria->compare('eventCategory.status', 1);
        $criteria->compare('t.school_id', $school_id);

        if (!$b_count) {
            $criteria->order = 't.start_date ASC';
            $criteria->together = true;
            $start = ($page_no - 1) * $page_size;
            $criteria->offset = $start;
            $criteria->limit = $page_size;
        }

        $obj_events = $this->with($with)->findAll($criteria);

        if (!empty($obj_events)) {

            if (!$b_count) {
                $formatted_events = $this->formatAcademicCalendar($obj_events, $school_id);
            } else {
                $formatted_events = $obj_events[0]->num_rows;
            }

            return $formatted_events;
        }

        return false;
    }

    public function formatAcademicCalendar($obj_events) {

        $formatted_events = array();
        foreach ($obj_events as $row) {

            $_data['event_id'] = $row->id;
            $_data['event_title'] = $row->title;
            $_data['event_start_date'] = date('Y-m-d', strtotime($row->start_date));
            $_data['event_description'] = $row->description;
            
            $formatted_events[] = $_data;
        }

        return $formatted_events;
    }

}
