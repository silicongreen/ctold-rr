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
    public $total;
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
                'joinType' => 'LEFT JOIN',
            ),
            'periodEntry' => array(self::BELONGS_TO, 'PeriodEntries', 'period_table_entry_id',
                'select' => 'periodEntry.id, periodEntry.month_date, periodEntry.batch_id, periodEntry.subject_id, periodEntry.class_timing_id, periodEntry.employee_id, periodEntry.school_id',
                'joinType' => 'LEFT JOIN',
                'with' => array('subjectDetails', 'classTimingDetails', 'employeesDetails'),
            ),
            'schoolDetails' => array(self::BELONGS_TO, 'Schools', 'school_id',
                'select' => 'schoolDetails.id, schoolDetails.name, schoolDetails.code',
                'joinType' => 'LEFT JOIN',
            ),
            'batchDetails' => array(self::BELONGS_TO, 'Batches', 'batch_id',
                'select' => 'batchDetails.id, batchDetails.name, batchDetails.course_id',
                'joinType' => 'LEFT JOIN',
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
    
    public function getWeekend($school_id,$batch_id=0)
    {
        $timetable = new TimeTableWeekdays();
        $week_day = $timetable->getWeekDaySet($school_id,$batch_id);
        $ar_weekdays = Settings::$ar_weekdays;
        
        if($week_day)
        {
            $weekdays_set = new WeekdaySetsWeekdays();
            $weekdays_set->setAttribute("weekday_set_id", $week_day->weekday_set_id);
            $weekdays = $weekdays_set->getWeekDays();


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
    public function deleteAttendanceStudent($school_id,$date,$std_id=array())
    {
        $criteria = new CDbCriteria;
        $criteria->select="t.id";
        $criteria->compare('month_date', $date); 
        $criteria->compare('school_id', $school_id);
        if($std_id)
        {
            $criteria->compare('student_id', $std_id);
        }
        $this->deleteAll($criteria);
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
    public function check_date($value)
    {
        $day_type = "1";
        $holiday = new Events();
        $holiday_array = $holiday->getHolidayMonth($value, $value, Yii::app()->user->schoolId);
        if($holiday_array)
        {
            $day_type="Holiday";
        }

        $attendance = new Attendances();
        $weekend_array = $attendance->getWeekend(Yii::app()->user->schoolId);

        $check_weekend_value = new DateTime($value);
        if (in_array($check_weekend_value->format("w"), $weekend_array))
        {
            $day_type="Weekend";
        }
        return $day_type;
    } 
           
    public function getClassComparisomGraph($class_name,$type="days",$report_type=1,$day=false,$section_name=false)
    {
//        $schoo_obj = new Schools();
//        $school_info = $schoo_obj->findByPk(Yii::app()->user->schoolId);
//        
//        if(isset($school_info->attandence_start) && $school_info->attandence_start)
//        {
//            $attandence_start = $school_info->attandence_start;
//        }
//        else
//        {
//            $attandence_start = date("Y-m-d",  strtotime($school_info->created_at));
//        }
//        
        $batchobj = new Batches();
        $attandence_start = $batchobj->getBatchStartMax(false, $class_name, false);
       
        if($type!="days"  || $day==false)
        {
            $value = date("Y-m-d");
        }
        else 
        {
            $value = $day;
        }
        $prev_day = $value;
        if($type != "days")
        {  
            $time_val = "-1 ".$type;
            $check_date = date('Y-m-d', strtotime($time_val));
            if($check_date < $attandence_start)
            {
                $prev_day = $attandence_start;
            }
            else if ($check_date == $attandence_start)
            {
                $prev_day = $attandence_start;
            }
            else
            {
                $prev_day = $check_date;
            }      
        } 
        
        
        $criteria = new CDbCriteria;
        $criteria->select="t.student_id,t.forenoon,t.afternoon,t.reason,t.is_leave";

        if($type != "days")
        {
            $criteria->addCondition("( t.month_date>='$prev_day' and t.month_date<='$value' )");
        }
        else
        {
            $criteria->compare('t.month_date', $value);
        } 



        $criteria->compare('t.school_id', Yii::app()->user->schoolId);
        
        
        $criteria->compare('courseDetails.course_name', $class_name); 
        
        if($section_name)
        {
            $criteria->compare('courseDetails.section_name', $section_name); 
        }
        
            
        $criteria->with = array(
            "batchDetails" => array(
                "select" => "batchDetails.name",
                'joinType' => "LEFT JOIN",
                'with' => array(
                    "courseDetails" => array(
                        "select" => "courseDetails.course_name",
                        'joinType' => "LEFT JOIN",
                    )
                )
            )  
        );

        $data = $this->findAll($criteria);
        $stdobj = new Students();
        $total = $stdobj->getStudentTotalCourse($class_name,$section_name);
        
        if($type != "days")
        {
            $number_of_days = $this->getNumberOfdays($prev_day, $value);
            $total = $total*$number_of_days;
        }

        if($report_type == 1)
        {
            $count_data = $total-count($data);
        }
        else
        {
            $count_data = 0;
            foreach($data as $avalue)
            {
               if($avalue->forenoon == 1 && $avalue->afternoon == 1 && $avalue->is_leave==0 && $report_type==2)
               {
                   $count_data++;
               } 
               else if( $avalue->is_leave==0 && $report_type==3 )
               {
                   $count_data++;
               }
               else if( $avalue->is_leave==1 && $report_type==4 )
               {
                   $count_data++;
               }
            }    
        }  
        
        $r_data = 0;
        if($total>0)
        {
            $r_data = round(($count_data/$total)*100);
        }
        return $r_data;
        
    } 
    public function getTotalPrsent($batch_id,$connect_exam_id,$students)
    {
        //$batchobj = new Batches();
        //$attandence_start = $batchobj->getBatchStartMax(false, false, $batch_id);
        
        $objExamConnect = new ExamConnect();
        
        $start_date_end_date = $objExamConnect->findByPk($connect_exam_id);
        
        
        $attandence_start = date("Y-m-d" , strtotime($start_date_end_date->attandence_start_date));
        $attandence_end =  date("Y-m-d" , strtotime($start_date_end_date->attandence_end_date));
        $number_of_days = $this->getNumberOfdays($attandence_start, $attandence_end, $batch_id);
        
        $criteria = new CDbCriteria;
        $criteria->select="count(t.student_id) as total,t.student_id";
        
        $criteria->addCondition("( t.month_date>='$attandence_start' and t.month_date<='$attandence_end' )");

        $criteria->compare('t.school_id', Yii::app()->user->schoolId);

        $criteria->compare('t.batch_id', $batch_id); 
        $criteria->compare('t.forenoon', 1); 
        $criteria->compare('t.afternoon', 1); 
        $criteria->group = "t.student_id";

        $criteria->with = array(
            "batchDetails" => array(
                "select" => "batchDetails.name",
                'joinType' => "LEFT JOIN",
                'with' => array(
                    "courseDetails" => array(
                        "select" => "courseDetails.course_name",
                        'joinType' => "LEFT JOIN",
                    )
                )
            )  
        );
       $data = $this->findAll($criteria);
       $return = array();
       $return2 = array();
       $absent = array();
       foreach($students as $value)
       {
          $stdobj = new Students();
          $std_data = $stdobj->findByPk($value->id);
          if($std_data)
          {
            $std_admission = $std_data->admission_date;
            if($std_admission>$attandence_start)
            {
                $number_of_days2 = $this->getNumberOfdays($std_admission, $attandence_end,$batch_id);
                $return2[$value->id] = $number_of_days2;
            }
            else
            {
                $return2[$value->id] = $number_of_days; 
            }    
          } 
          else 
          {
               
               $return2[$value->id] = $number_of_days;
          }
          $return[$value->id] = $number_of_days;
          $absent[$value->id] = 0;
       }
       
       
       foreach($data as $value)
       {
           if(isset($return[$value->student_id]))
           {
                $return[$value->student_id] = $return[$value->student_id]-$value->total;
                $absent[$value->student_id] = $value->total;
           }
           else 
           {
               $return[$value->student_id] = $number_of_days-$value->total;
               $absent[$value->student_id] = $value->total;
           }
           
       }
       
       
       return array($number_of_days,$return,$return2,$absent);
        
    }   
    
    public function getStudentTotalPrsent($batch_id,$student_id,$connect_exam_id)
    {
        //$batchobj = new Batches();
        //$attandence_start = $batchobj->getBatchStartMax(false, false, $batch_id);
        
        $objExamConnect = new ExamConnect();
        
        $start_date_end_date = $objExamConnect->findByPk($connect_exam_id);
        
        
        $attandence_start = date("Y-m-d" , strtotime($start_date_end_date->attandence_start_date));
        $attandence_end =  date("Y-m-d" , strtotime($start_date_end_date->attandence_end_date));
        $number_of_days = $this->getNumberOfdays($attandence_start, $attandence_end,$batch_id);
        $number_of_days2 = $number_of_days;
        
        $stdobj = new Students();
        $std_data = $stdobj->findByPk($student_id);
        if($std_data)
        {
          $std_admission = $std_data->admission_date;
          if($std_admission>$attandence_start)
          {
              $number_of_days2 = $this->getNumberOfdays($std_admission, $attandence_end,$batch_id);
              
          }   
        }
        
        $criteria = new CDbCriteria;
        $criteria->select="t.student_id,t.forenoon,t.afternoon,t.reason,t.is_leave";
        
        $criteria->addCondition("( t.month_date>='$attandence_start' and t.month_date<='$attandence_end' )");

        $criteria->compare('t.school_id', Yii::app()->user->schoolId);

        $criteria->compare('t.batch_id', $batch_id); 
        $criteria->compare('t.student_id', $student_id);
        $criteria->compare('t.forenoon', 1); 
        $criteria->compare('t.afternoon', 1); 

        $criteria->with = array(
            "batchDetails" => array(
                "select" => "batchDetails.name",
                'joinType' => "LEFT JOIN",
                'with' => array(
                    "courseDetails" => array(
                        "select" => "courseDetails.course_name",
                        'joinType' => "LEFT JOIN",
                    )
                )
            )  
        );
       $data = $this->findAll($criteria);
       
       $present = $number_of_days-count($data);
       $absent = count($data);
       return array($number_of_days,$present,$number_of_days2,$absent);
        
    }        
    
    public function getStudentAttendenceGraph($number_of_day=10,$type="days",$report_type=1,$batch_name=false,$class_name=false,$batch_id=false)
    {
//        $schoo_obj = new Schools();
//        $school_info = $schoo_obj->findByPk(Yii::app()->user->schoolId);
//        
//        if(isset($school_info->attandence_start) && $school_info->attandence_start)
//        {
//            $attandence_start = $school_info->attandence_start;
//        }
//        else
//        {
//            $attandence_start = date("Y-m-d",  strtotime($school_info->created_at));
//        }   
        
        $batchobj = new Batches();
        $attandence_start = $batchobj->getBatchStartMax($batch_name, $class_name, $batch_id);
        
        $main_date = date("Y-m-d");
        $dates_array = array();
        $dates_array[] = $main_date;
        for ($i = 1; $i < $number_of_day; $i++)
        {
            $time_val = "-" . $i . " ".$type;
            $check_date = date('Y-m-d', strtotime($time_val));
          
            if($check_date < $attandence_start)
            {
                $dates_array[] = $attandence_start;
                break;
            }
            else if ($check_date == $attandence_start)
            {
                $dates_array[] = $attandence_start;
                break;
            }
            else
            {
                $dates_array[] = $check_date;
            }     
        }
        //$dates_array = array_reverse($dates_array);
        
     
        
        $att_array = array();
        $att_date_array = array();
        $j = 1;
        foreach($dates_array as $key=>$value)
        {
            
            $prev_day = $value;
            if($type != "days")
            {
                if(isset($dates_array[$key+1]))
                {
                    $prev_day = $dates_array[$key+1];
                }
                else
                {
                    break;
                }    
            }  
            
         
            if($type == "days")
            {
                $holiday = new Events();
                $holiday_array = $holiday->getHolidayMonth($value, $value, Yii::app()->user->schoolId);
                if($holiday_array)
                {
                    continue;
                }
                
                $attendance = new Attendances();
                $weekend_array = $attendance->getWeekend(Yii::app()->user->schoolId);
                
                $check_weekend_value = new DateTime($value);
                if (in_array($check_weekend_value->format("w"), $weekend_array))
                {
                    continue;
                }
            }
            
            $criteria = new CDbCriteria;
            $criteria->select="t.student_id,t.forenoon,t.afternoon,t.reason,t.is_leave";
            
            if($type != "days")
            {
                $criteria->addCondition("( t.month_date>='$prev_day' and t.month_date<='$value' )");
            }
            else
            {
                $criteria->compare('t.month_date', $value);
            } 
            
              
            
            $criteria->compare('t.school_id', Yii::app()->user->schoolId);
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
            $criteria->with = array(
                "batchDetails" => array(
                    "select" => "batchDetails.name",
                    'joinType' => "LEFT JOIN",
                    'with' => array(
                        "courseDetails" => array(
                            "select" => "courseDetails.course_name",
                            'joinType' => "LEFT JOIN",
                        )
                    )
                )  
            );

            $data = $this->findAll($criteria);
            
            
            
            $stdobj = new Students();
            $total = $stdobj->getStudentTotal($batch_name,$class_name,$batch_id);
            if($type != "days")
            {
                $number_of_days = $this->getNumberOfdays($prev_day, $value);
                $total = $total*$number_of_days;
                
                $att_date_array[$j] = date("j M", strtotime($prev_day))."-".date("j M", strtotime($value));
            }
            else
            {
                $att_date_array[$j] = date("j F", strtotime($value));
            }    
            
            if($report_type == 1)
            {
                $count_data = $total-count($data);
            }
            else
            {
                $count_data = 0;
                foreach($data as $avalue)
                {
                   if($avalue->forenoon == 1 && $avalue->afternoon == 1 && $avalue->is_leave==0 && $report_type==2)
                   {
                       $count_data++;
                   } 
                   else if( $avalue->is_leave==0 && $report_type==3 )
                   {
                       $count_data++;
                   }
                   else if( $avalue->is_leave==1 && $report_type==4 )
                   {
                       $count_data++;
                   }
                }    
            }     
            
            if($total==0)
            {
                continue;
            }
            $att_array[$j] = round(($count_data/$total)*100);
            $j++;
            
        }
        //$att_array = array_reverse($att_array);
        return array($att_array,$att_date_array);
    }
    
    private function getNumberOfdays($start_date,$end_date,$batch_id = 0)
    {
        $start_day = new DateTime($start_date);
        $end_day = new DateTime($end_date);
        $date_interval = DateInterval::createFromDateString('1 day');
        $date_period = new DatePeriod($start_day, $date_interval, $end_day);
        
        //making holiday
        $holiday = new Events();
        $holidays = $holiday->getHolidayMonth($start_date, $end_date, Yii::app()->user->schoolId,$batch_id);
        $holiday_array = array();
        foreach ($holidays as $value)
        {
            $start_holiday = new DateTime($value['start_date']);
            $end_holiday = new DateTime($value['end_date']);
            $holiday_interval = DateInterval::createFromDateString('1 day');
            $holiday_period = new DatePeriod($start_holiday, $holiday_interval, $end_holiday);

            foreach ($holiday_period as $hdt)
            {
                $holiday_array[] = $hdt->format("Y-m-d");
            }
            $holiday_array[] = $end_holiday->format("Y-m-d");
        }
        $attendance = new Attendances();
        $weekend_array = $attendance->getWeekend(Yii::app()->user->schoolId);
        
        
        $i = 0;
        foreach ($date_period as $dt)
        {
            if (in_array($dt->format("Y-m-d"), $holiday_array))
            {
                continue;
            }
            if (in_array($dt->format("w"), $weekend_array))
            {
                continue;
            }
            $i++;
        } 
        return $i;
        
    }
    
    public function getStudentClassAttandence($date,$class_name,$section_name=false)
    {
        $criteria = new CDbCriteria;
        $criteria->select="t.student_id,t.forenoon,t.afternoon,t.reason";
        $criteria->compare('t.month_date', $date);
        $criteria->compare('t.school_id', Yii::app()->user->schoolId);
        $criteria->compare('courseDetails.course_name', $class_name); 
        if($section_name)
        {
            $criteria->compare('courseDetails.section_name', $section_name); 
        }
        $criteria->with = array(
            "batchDetails" => array(
                "select" => "batchDetails.name",
                'joinType' => "LEFT JOIN",
                'with' => array(
                    "courseDetails" => array(
                        "select" => "courseDetails.course_name",
                        'joinType' => "LEFT JOIN",
                    )
                )
            )  
        );
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
        
        $stdobj = new Students();
        $students = $stdobj->getStudentCourse($class_name,$section_name);
        $p = 0;
        $a = 0;
        $la = 0;
        $le = 0;
        foreach($students as $value)
        {
            if(isset($leave_today['approved']) && in_array($value->id, $leave_today['approved']))
            {
                $le++;
            }
            else if(in_array($value->id, $student_ids))
            {
                if($all_data[$value->id]['fullday']==1)
                {
                     $a++;
                } 
                else
                {
                     $la++;
                }
                
            } 
            else
            {
                $p++;
            }    
        }
        if(!$section_name)
        {
            $r_data = array("class_section"=>$class_name,"total"=>count($students),"present"=>$p,"absent"=>$a,"late"=>$la,"leave"=>$le);
        }
        else
        {
            $r_data = array("class_section"=>$section_name,"total"=>count($students),"present"=>$p,"absent"=>$a,"late"=>$la,"leave"=>$le);
        }    
        return $r_data;
        
    } 
    
    public function getHighestLowestAtt($type=1,$limit=10,$batch_name=false,$class_name=false,$batch_id=false)
    {
//        $schoo_obj = new Schools();
//        $school_info = $schoo_obj->findByPk(Yii::app()->user->schoolId);
//        
//        
//        if(isset($school_info->attandence_start) && $school_info->attandence_start)
//        {
//            $attandence_start = $school_info->attandence_start;
//        }
//        else
//        {
//            $attandence_start = date("Y-m-d",  strtotime($school_info->created_at));
//        }
        
        $batchobj = new Batches();
        $attandence_start = $batchobj->getBatchStartMax(false, $class_name, false);
        
        $month_start = date("Y-m-d",  strtotime("-1 months"));
        
        if($month_start<$attandence_start)
        {
            $month_start = $attandence_start;
        } 
        
        $last_three_days = date("Y-m-d",  strtotime("-".$limit." days"));
        
        $end_date = date("Y-m-d");
        
        $total_number_of_days = $this->getNumberOfdays($month_start,$end_date);
        
      
        
        $criteria = new CDbCriteria;
        $criteria->select="t.student_id,t.forenoon,t.afternoon,t.reason,t.is_leave,t.month_date";
        $criteria->addCondition("month_date >= '" . $month_start . "'");
        $criteria->addCondition("month_date <= '" . $end_date . "'");
        $criteria->compare('t.school_id', Yii::app()->user->schoolId);
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
        $criteria->with = array(
            "batchDetails" => array(
                "select" => "batchDetails.name",
                'joinType' => "LEFT JOIN",
                'with' => array(
                    "courseDetails" => array(
                        "select" => "courseDetails.course_name",
                        'joinType' => "LEFT JOIN",
                    )
                )
            )  
        );
        $criteria->order = "t.month_date DESC";
        
        $data = $this->findAll($criteria);
        
        
        $attandence_count = array();
        $attandence_count_absent_continues = array();
        $three_days_absent = array();
        $attandence_count_absent = array();
        $all_data = array();
        $i = 0;
        foreach($data as $value)
        {
            if(!isset($attandence_count[$value->student_id]))
            {
                $attandence_count[$value->student_id] = 0;
            }
            if(!isset($attandence_count_absent[$value->student_id]))
            {
                $attandence_count_absent[$value->student_id] = 0;
            }
            if(!isset($attandence_count_absent_continues[$value->student_id]))
            {
                $attandence_count_absent_continues[$value->student_id] = 0;
            }
            $attandence_count[$value->student_id] = $attandence_count[$value->student_id]+1;
            
            if($value->is_leave==0)
            {
                if($value->forenoon == 1 && $value->afternoon == 1)
                {
                    $attandence_count_absent[$value->student_id] = $attandence_count_absent[$value->student_id]+1;
                    if($value->month_date>=$last_three_days)
                    {
                        $attandence_count_absent_continues[$value->student_id] =$attandence_count_absent_continues[$value->student_id]+1;
                        if($attandence_count_absent_continues[$value->student_id]>=$limit)
                        {
                            $three_days_absent[$value->student_id] = true;
                        }    
                    }
                }
            }    
            
        } 
        
        $stdobj = new Students();
        $students = $stdobj->getStudentAll($batch_name,$class_name,$batch_id);
        
        $attendence = array();
        $i = 0;
        foreach($students as $value)
        {
            
            if($type==4)
            {
                if(!isset($three_days_absent[$value->id]))
                {
                    continue;
                }
            }    
            $attendence[$i]['att_number_of_days'] = 0;
            
            
            if($type==3)
            {
                if(isset($attandence_count_absent[$value->id]))
                {
                   if($attandence_count_absent[$value->id]>=$limit) 
                   {
                       $attendence[$i]['att_number_of_days'] = $attandence_count_absent[$value->id];
                   }
                   else
                   {
                       unset($attendence[$i]);
                       continue;
                   }    
                }
                else
                {
                    unset($attendence[$i]);
                    continue; 
                }    
            }    
            $fullname = ($value->first_name)?$value->first_name." ":"";
            $fullname.= ($value->middle_name)?$value->middle_name." ":"";
            $fullname.= ($value->last_name)?$value->last_name:"";
            $attendence[$i]['student_name'] = $fullname;
            $attendence[$i]['class_name'] = $value['batchDetails']['courseDetails']->course_name;
            $attendence[$i]['section_name'] = $value['batchDetails']['courseDetails']->section_name;
            $attendence[$i]['shift_name'] = $value['batchDetails']['courseDetails']->section_name;
            $attendence[$i]['att']  = 100;
            
            if(isset($attandence_count[$value->id]))
            {
                if($total_number_of_days>$attandence_count[$value->id])
                {
                    $diff = $total_number_of_days-$attandence_count[$value->id];
                    $attendence[$i]['att']  = round(($diff/$total_number_of_days)*100);
                    
                }
                else 
                {
                    $attendence[$i]['att'] = 0;
                }
            }    
            
            $i++;
        }
        if($attendence)
        {
            if($type==1)
            {
                usort($attendence, function($a, $b) {
                    return $b['att'] - $a['att'];
                });
            }
            else if($type==2 || $type==3 || $type==4)
            {
                usort($attendence, function($a, $b) {
                    return $a['att'] - $b['att'];
                });
            }
        }
        
        if($type==2 || $type==1)
        {
            $attendence = array_slice($attendence, 0, $limit);
        }
        
        return $attendence;
        
    } 
    
    public function getStudentTodayAttendenceFull($date,$batch_name=false,$class_name=false,$batch_id=false)
    {
        $criteria = new CDbCriteria;
        $criteria->select="t.student_id,t.forenoon,t.afternoon,t.reason";
        $criteria->compare('t.month_date', $date);
        $criteria->compare('t.school_id', Yii::app()->user->schoolId);
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
        $criteria->with = array(
            "batchDetails" => array(
                "select" => "batchDetails.name",
                'joinType' => "LEFT JOIN",
                'with' => array(
                    "courseDetails" => array(
                        "select" => "courseDetails.course_name",
                        'joinType' => "LEFT JOIN",
                    )
                )
            )  
        );
        
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
        $students = $stdobj->getStudentAll($batch_name,$class_name,$batch_id);
        
        $attendence = array();
        $i = 0;
        foreach($students as $value)
        {
            $fullname = ($value->first_name)?$value->first_name." ":"";
            $fullname.= ($value->middle_name)?$value->middle_name." ":"";
            $fullname.= ($value->last_name)?$value->last_name:"";
            $attendence[$i]['viewed_by_teacher'] = 0;
            
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
                $attendence[$i]['viewed_by_teacher'] = $leave_today['viewed_by_teacher'][$key];
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
                $attendence[$i]['viewed_by_teacher'] = $leave_today['viewed_by_teacher'][$key];
            }
            
            $i++;
            
        }  
        
        return $attendence;
        
    }
    
    
    public function getBatchStudentTodayAttendenceFull($date)
    {
        $criteria = new CDbCriteria;
        $criteria->select="t.student_id,t.forenoon,t.afternoon,t.reason";
        $criteria->compare('month_date', $date);
        $criteria->compare('school_id', Yii::app()->user->schoolId);
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
        $students = $stdobj->getStudentAll();
        
        $attendence = array();
        $i = 0;
        foreach($students as $value)
        {
            $fullname = ($value->first_name)?$value->first_name." ":"";
            $fullname.= ($value->middle_name)?$value->middle_name." ":"";
            $fullname.= ($value->last_name)?$value->last_name:"";
            $attendence[$i]['viewed_by_teacher'] = 0;
            
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
                $attendence[$i]['viewed_by_teacher'] = $leave_today['viewed_by_teacher'][$key];
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
                $attendence[$i]['viewed_by_teacher'] = $leave_today['viewed_by_teacher'][$key];
            }
            
            $i++;
            
        }  
        
        return $attendence;
        
    }
   
    public function Register($batch_id,$date,$school_id = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select="t.student_id,t.forenoon,t.afternoon,t.is_leave";
        $criteria->compare('month_date', $date);
        $criteria->compare('batch_id', $batch_id);
        $data = $this->findAll($criteria);
        
        $stdobj = new Students();
        $students = $stdobj->getStudentByBatch($batch_id);
        
        $present = $total = count($students);
        $late = 0;
        $absent = 0;
        $leave = 0;
        foreach($data as $value)
        {
            if($value->is_leave == 1)
            {
                $leave++;
            }    
            else if($value->forenoon == 1 && $value->afternoon == 1)
            {
                $absent++;
            }
            else if($value->forenoon == 1 || $value->afternoon == 1)
            {
                $late++;
            }
        }
        $present = $present - $absent - $leave;
        
        $att_register_obj = new AttendanceRegisters();
        $att_register_data = $att_register_obj->getRegisterData($date, $batch_id);

        if ($att_register_data)
        {
            $att_register = $att_register_obj->findByPk($att_register_data->id);
        } 
        else
        {
            $att_register = new AttendanceRegisters();
            $att_register->attendance_date = $date;
            $att_register->created_at = date("Y-m-d H:i:s");
        }
        
        $att_register->total = $total;
        $att_register->batch_id = $batch_id;
        if($school_id == 0)
        {
            $att_register->employee_id = Yii::app()->user->profileId;
            $att_register->school_id = Yii::app()->user->schoolId;
        }
        else
        {
            $att_register->employee_id = 0;
            $att_register->school_id = $school_id; 
        }    
        $att_register->present = $present;
        $att_register->absent = $absent;
        $att_register->late = $late;
        $att_register->leave = $leave;
        $att_register->updated_at = date("Y-m-d H:i:s");
        
        $att_register->save();
        
        
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
            $attendence[$i]['viewed_by_teacher'] = 0;
            
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
                $attendence[$i]['viewed_by_teacher'] = $leave_today['viewed_by_teacher'][$key];
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
                $attendence[$i]['viewed_by_teacher'] = $leave_today['viewed_by_teacher'][$key];
            }
            
            $i++;
            
        }  
        
        return $attendence;
        
    }

    public function getAbsentStudentMonth($start_date, $end_date, $student_id,$holiday_array_for_count=array(),$weekend_array=array(),$leave_array_modified=array()) {

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
                $dateobj = new DateTime(date("Y-m-d",  strtotime($value->month_date)));
                if (in_array($dateobj->format("Y-m-d"), $holiday_array_for_count))
                {
                    continue;
                }
                if (in_array($dateobj->format("w"), $weekend_array))
                {
                    continue;
                }
                $continue_leave = false;
                if($leave_array_modified)
                {
                    foreach($leave_array_modified as $lmvalue)
                    {
                        if ($dateobj->format("Y-m-d")==$lmvalue['start_date'])
                        {
                            $continue_leave=true;
                            break;
                        }
                    }
                }
                if($continue_leave)
                {
                    continue;
                }
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
