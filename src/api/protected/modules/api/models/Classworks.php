<?php

/**
 * This is the model class for table "classworks".
 *
 * The followings are the available columns in table 'classworks':
 * @property integer $id
 * @property integer $employee_id
 * @property integer $subject_id
 * @property string $student_list
 * @property string $title
 * @property integer $classwork_type
 * @property string $content
 * @property string $attachment_file_name
 * @property string $attachment_content_type
 * @property integer $attachment_file_size
 * @property string $attachment_updated_at
 * @property integer $is_published
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Classworks extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total;
	public function tableName()
	{
		return 'classworks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('employee_id, subject_id, classwork_type, attachment_file_size, is_published, school_id', 'numerical', 'integerOnly'=>true),
			array('title, attachment_file_name, attachment_content_type', 'length', 'max'=>255),
			array('student_list, content, attachment_updated_at, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, employee_id, subject_id, student_list, title, classwork_type, content, attachment_file_name, attachment_content_type, attachment_file_size, attachment_updated_at, is_published, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
                    'employeeDetails' => array(self::BELONGS_TO, 'Employees', 'employee_id',
                         "select"=>"employeeDetails.id, employeeDetails.first_name, employeeDetails.middle_name, employeeDetails.last_name ",
                    ),
                    'subjectDetails' => array(self::BELONGS_TO, 'Subjects', 'subject_id',
                       "select"=>"subjectDetails.id, subjectDetails.name, subjectDetails.icon_number", 
                    ),
                    'classworkAnswer' => array(self::HAS_MANY, 'ClassworkAnswers', 'classwork_id'
                       
                    ),
		);
	}
        
        public function getClassworkTotalTeacherDate($employee_id,$start_date,$end_date)
        {
            
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            $criteria->together = true;
           
            $criteria->compare('t.is_published', 1);
            
            $criteria->compare('t.employee_id', $employee_id);
            
            $criteria->addCondition("DATE(t.created_at) >= '".$start_date."' and DATE(t.created_at) <= '".$end_date."'");
           // $criteria->compare('DATE(t.created_at)', $date);
             
                
            
            $data = $this->find($criteria);
            return $data->total;
        }
        
        public function getClassworkEmployee($date,$sort_by,$sort_type,$time_range,$department_id=false)
        {
            $employee = new Employees();
            $all_employee = $employee->getEmployee($department_id);
            
            $emp_classwork_data = array();
            $i = 0;
            if($all_employee)
            {
                foreach($all_employee as $value)
                {
                      $emp_name = $value->first_name." ";
                      if($value->middle_name)
                      $emp_name = $emp_name.$value->middle_name." ";
                      $emp_name = $emp_name.$value->last_name;
                      $emp_classwork_data[$i]['emp_name'] = $emp_name;
                      $emp_classwork_data[$i]['emp_dep'] = $value['department']->name;
                      
                      $classwork = new Classworks();
                      $timetable = new TimetableEntries();
                      
                      if($time_range=="day")
                      {
                        $start_date = $date;
                      }
                      else 
                      {
                          $time_val = " -1 ".$time_range;
                          
                          $start_date = date("Y-m-d", strtotime($date . $time_val));
                           
                      }
                      
                      $emp_classwork_data[$i]['classwork_given'] = $classwork->getClassworkTotalTeacherDate($value->id,$start_date,$date);
                      
                      if($time_range=="day")
                      {
                        $emp_classwork_data[$i]['total_class'] = $timetable->getTotalClassTeacher($date,$value->id);
                      }
                      else
                      {
                    
                     
                            $events = new Events();
                            $holidays = $events->getHolidayAsArray($start_date,$date);
                            
                            $class_week = $timetable->getTotalClassArrayWeek(false, false, false, $value->id);
                            
                            
                            
                          
                            
                            $total_class = 0;
                            $all_day = array();
                            $start_date_formated = new DateTime($start_date);
                            $end_date_formated = new DateTime($date);
                            $day_interval = DateInterval::createFromDateString('1 day');
                            $day_period = new DatePeriod($start_date_formated, $day_interval, $end_date_formated);
                            foreach ($day_period as $hdt)
                            {
                              $all_day[] = $hdt->format("Y-m-d");
                            }
                            $all_day[] = $end_date_formated->format("Y-m-d");
                            
                            
                            foreach($all_day as $allvalue)
                            {
                                $cur_day_name = Settings::getCurrentDay($allvalue);
                                $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
                                if(!isset($class_week[$cur_day_key]))
                                {
                                    continue;
                                } 
                                else if(in_array($allvalue, $holidays))
                                {
                                    continue;
                                }
                                else
                                {
                                    $total_class = $total_class+$class_week[$cur_day_key];
                                }
                            } 
                            $emp_classwork_data[$i]['total_class'] = $total_class;
                            
                          
                      }   
                      
                       
                      $emp_classwork_data[$i]['frequency'] = "N/A";
                      if($emp_classwork_data[$i]['classwork_given']>0)
                      {
                          $emp_classwork_data[$i]['frequency'] = round($emp_classwork_data[$i]['total_class']/$emp_classwork_data[$i]['classwork_given']);
                      }
                      $i++;
                      
                }  
            }
           
            if($emp_classwork_data)
            {
                
                if($sort_type==1)
                {
                    usort($emp_classwork_data, function($a, $b) use ($sort_by) {
                        return $b[$sort_by] - $a[$sort_by];
                    });
                }
                else
                {
                    usort($emp_classwork_data, function($a, $b) use ($sort_by) {
                        return $a[$sort_by] - $b[$sort_by];
                    });
                }
            }    
            return $emp_classwork_data;
            
        }  
        
        public function getClassworkGraph($number_of_day=10,$type="days",$batch_name=false,$class_name=false,$batch_id=false)
        {
            $schoo_obj = new Schools();
            $school_info = $schoo_obj->findByPk(Yii::app()->user->schoolId);

            //$school_start = date("Y-m-d",  strtotime($school_info->created_at));
            
            $batchobj = new Batches();
            $school_start = $batchobj->getBatchStartMax(false, $class_name, false);
             
            $main_date = date("Y-m-d");
            $dates_array = array();
            $dates_array[] = $main_date;
            for ($i = 1; $i <= $number_of_day; $i++)
            {
                $time_val = "-" . $i . " ".$type;
                $check_date = date('Y-m-d', strtotime($time_val));

                if($check_date < $school_start)
                {
                    $dates_array[] = $school_start;
                    break;
                }
                else if ($check_date == $school_start)
                {
                    $dates_array[] = $school_start;
                    break;
                }
                else
                {
                    $dates_array[] = $check_date;
                }   
            }
            
            $start_date = $main_date;
            $end_date = $dates_array[count($dates_array)-1];
            
            $events = new Events();
            $holidays = $events->getHolidayAsArray($start_date,$end_date);
            
            
            $timetable = new TimetableEntries();
            $weekClass = $timetable->getTotalClassArrayWeek($batch_name,$class_name,$batch_id);
            
            
           
            
            
            $j = 1;
            $classworks_array = array();
            $classworks_date_array = array();
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
                $total_class = 0;
                if($type == "days")
                {
                    $cur_day_name = Settings::getCurrentDay($value);
                    $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
                    if(!isset($weekClass[$cur_day_key]))
                    {
                        continue;
                    } 
                    else if(in_array($value, $holidays))
                    {
                        continue;
                    }
                    else
                    {
                        $total_class = $weekClass[$cur_day_key];
                    }     
                    
                }
                else 
                {
                    $all_day = array();
                    $start_date_formated = new DateTime($prev_day);
                    $end_date_formated = new DateTime($value);
                    $day_interval = DateInterval::createFromDateString('1 day');
                    $day_period = new DatePeriod($start_date_formated, $day_interval, $end_date_formated);
                    foreach ($day_period as $hdt)
                    {
                      $all_day[] = $hdt->format("Y-m-d");
                    }
                    $all_day[] = $end_date_formated->format("Y-m-d");
                    
                    foreach($all_day as $allvalue)
                    {
                        $cur_day_name = Settings::getCurrentDay($allvalue);
                        $cur_day_key = Settings::$ar_weekdays_key[$cur_day_name];
                        if(!isset($weekClass[$cur_day_key]))
                        {
                            continue;
                        } 
                        else if(in_array($allvalue, $holidays))
                        {
                            continue;
                        }
                        else
                        {
                            $total_class = $total_class+$weekClass[$cur_day_key];
                        }
                    }    
                    
                }
                
                   
                
                $classworkObj = new Classworks();
                $total_homework = $classworkObj->getClassworkTotalWithinDate($prev_day, $value,$batch_name,$class_name,$batch_id);
                
                if($total_homework<1)
                {
                    continue;
                } 
                
                
                if($type != "days")
                {
                   $classworks_date_array[$j] = date("j M", strtotime($prev_day))."-".date("j M", strtotime($value));
                }
                else
                {
                    $classworks_date_array[$j] = date("j F", strtotime($value));
                }
                
            
                $classworks_array[$j] = round($total_class/$total_homework);
                $j++;
            } 
            return array($classworks_array,$classworks_date_array);
        }
        public function getClassworkTotalWithinDate($start_date,$end_date,$batch_name=false,$class_name=false,$batch_id=false)
        {
            
            $criteria = new CDbCriteria();
            $criteria->together = true;
            $criteria->select = 'count(t.id) as total';
            $criteria->compare('t.is_published', 1);
           
            $criteria->addCondition("DATE(t.created_at) >= '".$start_date."' and DATE(t.created_at) <= '".$end_date."'");
            if($batch_id)
            {
                $criteria->compare('Subjectbatch.id', $batch_id);
            }
            else
            {
                if($batch_name)
                {
                    $criteria->compare('Subjectbatch.name', $batch_name);
                }
                if($class_name)
                {
                    $criteria->compare('courseDetails.course_name', $class_name);
                }
            }
            
             $criteria->with = array(
                'subjectDetails' => array(
                    'select' => 'subjectDetails.id',
                    'joinType' => "LEFT JOIN",
                    'with' => array(
                        "Subjectbatch" => array(
                            "select" => "Subjectbatch.id",
                            'joinType' => "LEFT JOIN",
                            'with' => array(
                                "courseDetails" => array(
                                    "select" => "courseDetails.id",
                                    'joinType' => "LEFT JOIN",
                                )
                            )
                        )
                    )
                )
            );
             
            
            $criteria->compare('t.school_id', Yii::app()->user->schoolId);
            $criteria->group='t.school_id';
            $data = $this->find($criteria);
            if ($data)
            {
                return $data->total;
            }
            else
            {
                return 0;
            }
        }
        
        public function getClassworkTotalAdmin($date,$batch_name=false,$class_name=false,$batch_id=false)
        {
            
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            $criteria->together = true;
           
            $criteria->compare('t.is_published', 1);
           
            if($date)
            {
                $criteria->compare('DATE(t.created_at)', $date);
            }
            
            if($batch_id)
            {
                $criteria->compare('Subjectbatch.id', $batch_id);
            }
            else
            {
                if($batch_name)
                {
                    $criteria->compare('Subjectbatch.name', $batch_name);
                }
                if($class_name)
                {
                    $criteria->compare('courseDetails.course_name', $class_name);
                }
            }
            
           $criteria->compare('t.school_id', Yii::app()->user->schoolId);
            
           
           $criteria->with = array(
                'subjectDetails' => array(
                    'select' => 'subjectDetails.id',
                    'joinType' => "LEFT JOIN",
                    'with' => array("Subjectbatch" => array(
                            "select" => "Subjectbatch.id",
                            'joinType' => "LEFT JOIN",
                            'with' => array(
                                "courseDetails" => array(
                                    "select" => "courseDetails.id",
                                    'joinType' => "LEFT JOIN",
                                )
                            )
                        )
                    )
                )
            );
            
            $criteria->group='t.school_id';
            
            
            $data = $this->find($criteria);
          
           
            if ($data)
            {
                return $data->total;
            }
            else
            {
                return 0;
            }
        }
        
        
        
        
        public function getClassworkTotalTeacher($employee_id,$is_published,$subject_id=NULL)
        {
            
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            $criteria->compare('t.is_published', $is_published);
            if($subject_id!=NULL)
            {         
                $criteria->compare('t.subject_id', $subject_id);
            }
            
            if(!Yii::app()->user->isAdmin)
            {
                $criteria->compare('t.employee_id', $employee_id);
            }
            else
            {
                $criteria->compare('t.school_id', Yii::app()->user->schoolId);
                
            }    
            
            $data = $this->find($criteria);
            return $data->total;
        }   
        private function checkHtmlTag($string)
        {
           
            if($string != strip_tags($string)) {
                return false;
            }
            return true;
        }        
        
        public function getClassworkTeacher($employee_id,$page=1,$page_size=10,$is_published,$id=0,$subject_id=NULL)
        {
            
            
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            
            if(!$id)
            {
                $criteria->compare('t.is_published', $is_published);
            }
            
            if(!Yii::app()->user->isAdmin)
            {
                $criteria->compare('t.employee_id', $employee_id);
            }
            else
            {
                $criteria->compare('t.school_id', Yii::app()->user->schoolId);
                
            }
            
            if($subject_id!=NULL)
            {
                $criteria->compare('t.subject_id', $subject_id);
              
            }
            if($id>0)
            {
               $criteria->compare('t.id', $id); 
            } 
            $criteria->order = "t.created_at DESC";         
            if($id>0)
            {
                $criteria->limit = 1;
            }
            else
            {    
                $start = ($page-1)*$page_size;
                $criteria->limit = $page_size;

                $criteria->offset = $start;
     
            } 
            
            $criteria->with = array(
                'subjectDetails' => array(
                    'select' => 'subjectDetails.id,subjectDetails.name,subjectDetails.icon_number',
                    'joinType' => "INNER JOIN",
                    'with' => array(
                        "Subjectbatch" => array(
                            "select" => "Subjectbatch.name",
                            'joinType' => "INNER JOIN",
                            'with' => array(
                                "courseDetails" => array(
                                    "select" => "courseDetails.course_name,courseDetails.section_name",
                                    'joinType' => "INNER JOIN",
                                )
                            )
                        )
                    )
                )
            );
            
            $data = $this->findAll($criteria);
            $response_array = array();
            if($data != NULL)
            foreach($data as $value)
            {
                $marge = array();
              
                $marge['subjects'] = $value["subjectDetails"]->name;
                $marge['batch'] = $value["subjectDetails"]['Subjectbatch']->name;
                
                $marge['attachment_file_name'] = ""; 
                if($value->attachment_file_name)
                {
                    $marge['attachment_file_name'] = $value->attachment_file_name;
                }
                
                $marge['course'] = $value["subjectDetails"]['Subjectbatch']['courseDetails']->course_name;
                $marge['section'] = "";
                if($value["subjectDetails"]['Subjectbatch']['courseDetails']->section_name)
                {
                    $marge['section'] = $value["subjectDetails"]['Subjectbatch']['courseDetails']->section_name;
                }
                $marge['subjects_id'] = $value["subjectDetails"]->id;
                $marge['subjects_icon'] = $value["subjectDetails"]->icon_number;
                $marge['assign_date'] = date("Y-m-d", strtotime($value->created_at));
                $marge['name'] = $value->title;
                $marge['content'] = $value->content;
                $marge['type'] = $value->classwork_type;
                $marge['id'] = $value->id;
                $marge['is_editable'] = $this->checkHtmlTag($value->content);
                $marge['is_published'] = $value->is_published;
                $response_array[] = $marge;     
                
            }
            return $response_array;
            
        }
        
        
        
        
        public function getClassworkTotal($batch_id, $student_id, $date = '', $subject_id=NULL, $type)
        {
            $date = (!empty($date)) ? $date : \date('Y-m-d', \time());
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            $criteria->compare('subjectDetails.batch_id', $batch_id);
            $criteria->compare('t.classwork_type', $type);
            $criteria->compare('t.is_published', 1);
            if($subject_id!=NULL)
            {
                $criteria->compare('t.subject_id', $subject_id);
            }
           
            
            $criteria->addCondition("FIND_IN_SET(".$student_id.", student_list)");
            
            $criteria->order = "t.created_at DESC";
            
            $data = $this->with("subjectDetails")->find($criteria);
            if($data)
                return $data->total;
            else
                return 0;    
        } 
        
       
        public function getClassworkSubject($batch_id, $student_id)
        {
            
            $criteria = new CDbCriteria();
            $criteria->select = 't.id,t.attachment_file_name';
            $criteria->compare('subjectDetails.batch_id', $batch_id);
            $criteria->compare('t.is_published', 1);
            $criteria->order = "t.created_at DESC";
            $criteria->addCondition("FIND_IN_SET(".$student_id.", student_list)");
            $data = $this->with("subjectDetails")->findAll($criteria);
            $response_array = array();
            if($data != NULL)
            foreach($data as $value)
            {
                $marge['attachment_file_name'] = "";      
                if($value->attachment_file_name)
                {
                    $marge['attachment_file_name'] = $value->attachment_file_name;
                } 
                $marge['subjects'] = $value["subjectDetails"]->name;
                $marge['subjects_id'] = $value["subjectDetails"]->id;
                $marge['subjects_icon'] = $value["subjectDetails"]->icon_number;
                
                $response_array[] = $marge;     
                
            }
            return $response_array;
            
        }
        
        public function getClassworkTerm($term,$batch_id=false,$student_id=false,$employee_id = false)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.is_published', 1);
            $criteria->order = "t.created_at DESC";
            $criteria->limit = 5;
            
            $criteria->addCondition("t.title like '%".$term."%'");
            
            if($batch_id)
            {
                $criteria->compare('subjectDetails.batch_id', $batch_id);
            }
            
            if($student_id)
            {
                $criteria->addCondition("FIND_IN_SET(".$student_id.", student_list)");
            }
            
            if($employee_id)
            {
                 $criteria->compare('t.employee_id', $employee_id);
            }
            $criteria->compare('t.school_id', Yii::app()->user->schoolId);
            
            $criteria->with = array(
                'subjectDetails' => array(
                    'select' => 'subjectDetails.id,subjectDetails.name,subjectDetails.icon_number',
                    'joinType' => "INNER JOIN",
                    'with' => array(
                        "Subjectbatch" => array(
                            "select" => "Subjectbatch.name",
                            'joinType' => "INNER JOIN",
                            'with' => array(
                                "courseDetails" => array(
                                    "select" => "courseDetails.course_name,courseDetails.section_name",
                                    'joinType' => "INNER JOIN",
                                )
                            )
                        )
                    )
                )
            );
            
            $data = $this->findAll($criteria);
            $response_array = array();
            if($data != NULL)
            foreach($data as $value)
            {
                $marge = array();
              
                $marge['subjects'] = $value["subjectDetails"]->name;
                $marge['batch'] = $value["subjectDetails"]['Subjectbatch']->name;
                
                $marge['attachment_file_name'] = ""; 
                if($value->attachment_file_name)
                {
                    $marge['attachment_file_name'] = $value->attachment_file_name;
                }
                
                $marge['course'] = $value["subjectDetails"]['Subjectbatch']['courseDetails']->course_name;
                $marge['section'] = "";
                if($value["subjectDetails"]['Subjectbatch']['courseDetails']->section_name)
                {
                    $marge['section'] = $value["subjectDetails"]['Subjectbatch']['courseDetails']->section_name;
                }
                $marge['subjects_id'] = $value["subjectDetails"]->id;
                $marge['subjects_icon'] = $value["subjectDetails"]->icon_number;
                $marge['assign_date'] = date("Y-m-d", strtotime($value->created_at));
                $marge['name'] = $value->title;
                $marge['content'] = $value->content;
                $marge['type'] = $value->classwork_type;
                $marge['id'] = $value->id;
                $marge['is_editable'] = $this->checkHtmlTag($value->content);
                $marge['is_published'] = $value->is_published;
                $response_array[] = $marge;     
                
            }
            return $response_array;
            
        }        
        
        public function getClasswork($batch_id="", $student_id=array(), $date = '',$page=1, $subject_id=NULL, $page_size,$type,$id=0)
        {
            $date = (!empty($date)) ? $date : \date('Y-m-d', \time());
            
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.is_published', 1);
            
            if($batch_id)
            $criteria->compare('subjectDetails.batch_id', $batch_id);
          
            if($id>0)
            {
               $criteria->compare('t.id', $id); 
            } 
            else
            {
                $criteria->compare('t.classwork_type', $type);
            }    
            if($subject_id!=NULL)
            {
                $criteria->compare('t.subject_id', $subject_id);
                
            }
            $criteria->order = "t.created_at DESC";
            if($student_id)
            $criteria->addCondition("FIND_IN_SET(".$student_id.", student_list)");
            
            
            if($id>0)
            {
                $criteria->limit = 1;
            }
            else
            {    
                $start = ($page-1)*$page_size;
                $criteria->limit = $page_size;

                $criteria->offset = $start;
            }
            
            $data = $this->with("subjectDetails","employeeDetails")->findAll($criteria);
            
            $rid = array();
            
            
            $response_array = array();
            if($data != NULL)
            {
                foreach($data as $kvalue)
                {
                    $rid[]= $kvalue->id;
                }
                $robject = new Reminders();
                
                $new_data = $robject->FindUnreadData(31, $rid);
                foreach($data as $value)
                {
                    $marge = array();
                    $middle_name = (!empty($value["employeeDetails"]->middle_name)) ? $value["employeeDetails"]->middle_name.' ' : '';
                    $marge['id']   = $value->id;
                    
                    $marge['is_new'] = 0;
                    
                    if(in_array($value->id, $new_data))
                    {
                        $marge['is_new'] = 1;
                    }

                    $marge['attachment_file_name'] = "";

                    if($value->attachment_file_name)
                    {
                        $marge['attachment_file_name'] = $value->attachment_file_name;
                    }

                    $marge['teacher_name'] = rtrim($value["employeeDetails"]->first_name.' '.$middle_name.$value["employeeDetails"]->last_name);
                    $marge['teacher_id']   = $value["employeeDetails"]->id;
                    $marge['subjects'] = $value["subjectDetails"]->name;
                    $marge['subjects_id'] = $value["subjectDetails"]->id;
                    $marge['subjects_icon'] = $value["subjectDetails"]->icon_number;
                    $marge['assign_date'] = date("Y-m-d", strtotime($value->created_at));
                    
                    $marge['name'] = $value->title;
                    $marge['content'] = $value->content;
                    $marge['type'] = $value->classwork_type;
                    $marge['is_editable'] = $this->checkHtmlTag($value->content);
                    $marge['id'] = $value->id;
                    $response_array[] = $marge;     

                }
            }
            return $response_array;
            
        }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'employee_id' => 'Employee',
			'subject_id' => 'Subject',
			'student_list' => 'Student List',
			'title' => 'Title',
			'classwork_type' => 'Classwork Type',
			'content' => 'Content',
			'attachment_file_name' => 'Attachment File Name',
			'attachment_content_type' => 'Attachment Content Type',
			'attachment_file_size' => 'Attachment File Size',
			'attachment_updated_at' => 'Attachment Updated At',
			'is_published' => 'Is Published',
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
		$criteria->compare('subject_id',$this->subject_id);
		$criteria->compare('student_list',$this->student_list,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('classwork_type',$this->classwork_type);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('attachment_file_name',$this->attachment_file_name,true);
		$criteria->compare('attachment_content_type',$this->attachment_content_type,true);
		$criteria->compare('attachment_file_size',$this->attachment_file_size);
		$criteria->compare('attachment_updated_at',$this->attachment_updated_at,true);
		$criteria->compare('is_published',$this->is_published);
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
	 * @return Classworks the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
