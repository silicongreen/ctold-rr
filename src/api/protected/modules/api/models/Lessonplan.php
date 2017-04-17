<?php

/**
 * This is the model class for table "tds_tags".
 *
 * The followings are the available columns in table 'tds_tags':
 * @property integer $id
 * @property string $tags_name
 * @property string $hit_count
 *
 * The followings are the available model relations:
 * @property PostTags[] $postTags
 */
class Lessonplan extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total = 0;
	public function tableName()
	{
		return 'lessonplans';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
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
                        'category' => array(self::BELONGS_TO, 'LessonplanCategory', 'lessonplan_category_id')
                );
	}

	

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Tag the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        
        public function getLessonplanTotalTeacherDate($employee_id,$start_date,$end_date)
        {
            
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
          
           
            $criteria->compare('t.is_show', 1);
            
            $criteria->compare('t.author_id', $employee_id);
            
            $criteria->addCondition("DATE(t.created_at) >= '".$start_date."' and DATE(t.created_at) <= '".$end_date."'");

            
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
        
        public function getLessonplanEmployee($date,$sort_by,$sort_type,$time_range,$department_id=false)
        {
            $employee = new Employees();
            $all_employee = $employee->getEmployee($department_id);
            
            $emp_lessonplan_data = array();
            $i = 0;
            if($all_employee)
            {
                foreach($all_employee as $value)
                {
                      $emp_name = $value->first_name." ";
                      if($value->middle_name)
                      $emp_name = $emp_name.$value->middle_name." ";
                      $emp_name = $emp_name.$value->last_name;
                      $emp_lessonplan_data[$i]['emp_name'] = $emp_name;
                      $emp_lessonplan_data[$i]['emp_dep'] = $value['department']->name;
                      
                      $lessonplan = new Lessonplan();
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
                      
                      $emp_lessonplan_data[$i]['lessonplan_given'] = $lessonplan->getLessonplanTotalTeacherDate($value->user_id,$start_date,$date);
                     
                      
                      if($time_range=="day")
                      {
                        $emp_lessonplan_data[$i]['total_class'] = $timetable->getTotalClassTeacher($date,$value->id);
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
                            $emp_lessonplan_data[$i]['total_class'] = $total_class;
                            
                          
                      }   
                      
                       
                      $emp_lessonplan_data[$i]['frequency'] = "N/A";
                      if($emp_lessonplan_data[$i]['lessonplan_given']>0)
                      {
                          $emp_lessonplan_data[$i]['frequency'] = round($emp_lessonplan_data[$i]['total_class']/$emp_lessonplan_data[$i]['lessonplan_given']);
                      }
                      $i++;
                      
                }  
            }
           
            if($emp_lessonplan_data)
            {
                
                if($sort_type==1)
                {
                    usort($emp_lessonplan_data, function($a, $b) use ($sort_by) {
                        return $b[$sort_by] - $a[$sort_by];
                    });
                }
                else
                {
                    usort($emp_lessonplan_data, function($a, $b) use ($sort_by) {
                        return $a[$sort_by] - $b[$sort_by];
                    });
                }
            }    
            return $emp_lessonplan_data;
            
        }  
        
        public function getLessonplanGraph($number_of_day=10,$type="days",$batch_name=false,$class_name=false,$batch_id=false)
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
            $lessonplans_array = array();
            $lessonplans_date_array = array();
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
                
                   
                
                $lessonplanObj = new Lessonplan();
                $total_homework = $lessonplanObj->getLessonplanTotalWithinDate($prev_day, $value,$batch_name,$class_name,$batch_id);
                
                if($total_homework<1)
                {
                    continue;
                } 
                
                
                if($type != "days")
                {
                   $lessonplans_date_array[$j] = date("j M", strtotime($prev_day))."-".date("j M", strtotime($value));
                }
                else
                {
                    $lessonplans_date_array[$j] = date("j F", strtotime($value));
                }
                
            
                $lessonplans_array[$j] = round($total_class/$total_homework);
                $j++;
            } 
            return array($lessonplans_array,$lessonplans_date_array);
        }
        public function getLessonplanTotalWithinDate($start_date,$end_date,$batch_name=false,$class_name=false,$batch_id=false)
        {
            
            $criteria = new CDbCriteria();
            $criteria->together = true;
            $criteria->select = 'count(t.id) as total';
            $criteria->compare('t.is_show', 1);
           
            $criteria->addCondition("DATE(t.created_at) >= '".$start_date."' and DATE(t.created_at) <= '".$end_date."'");
            if($batch_id)
            {
                $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
                
            }
            else
            {
                $batchObj = new Batches();
                
                
                $sql_string_array = array();
                $sql_string_main = "";
                if($batch_name)
                {
                    
                    $batches = $batchObj->getBatchsByName($batch_name);
                    if($batches)
                    {
                        $sql_array = array();
                        $sql_string = "";
                        foreach($batches as $value)
                        {
                            $sql_array[] = "FIND_IN_SET(".$value->id.", batch_ids)";
                        }
                        $sql_string = implode(" OR ", $sql_array);
                        $sql_string = "(".$sql_string.")";
                        $sql_string_array[]= $sql_string;
                    }
                   
                }
                if($class_name)
                {
                    $sql_array = array();
                    $sql_string = "";
                    $batches = $batchObj->getBatchsByName("",$class_name);
                    if($batches)
                    {
                        foreach($batches as $value)
                        {
                            $sql_array[] = "FIND_IN_SET(".$value->id.", batch_ids)";
                        }
                        $sql_string = implode(" OR ", $sql_array);
                        $sql_string = "(".$sql_string.")";
                        $sql_string_array[]= $sql_string;
                    }
                   
                }
                if($sql_string_array)
                {
                    $sql_string_main = implode(" AND ", $sql_string_array);
                    $sql_string_main = "(".$sql_string_main.")";
                    $criteria->addCondition($sql_string_main);
                    
                }
            }
            
         
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
        
        public function getLessonplanTotalAdmin($date,$batch_name=false,$class_name=false,$batch_id=false)
        {
            
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            $criteria->together = true;
           
            $criteria->compare('t.is_show', 1);
           
            if($date)
            {
                $criteria->compare('DATE(t.created_at)', $date);
            }
            
            if($batch_id)
            {
                $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
            }
            else
            {
                $batchObj = new Batches();
                
                
                $sql_string_array = array();
                $sql_string_main = "";
                if($batch_name)
                {
                    
                    $batches = $batchObj->getBatchsByName($batch_name);
                    if($batches)
                    {
                        $sql_array = array();
                        $sql_string = "";
                        foreach($batches as $value)
                        {
                            $sql_array[] = "FIND_IN_SET(".$value->id.", batch_ids)";
                        }
                        $sql_string = implode(" OR ", $sql_array);
                        $sql_string = "(".$sql_string.")";
                        $sql_string_array[]= $sql_string;
                    }
                   
                }
                if($class_name)
                {
                    $sql_array = array();
                    $sql_string = "";
                    $batches = $batchObj->getBatchsByName("",$class_name);
                    if($batches)
                    {
                        foreach($batches as $value)
                        {
                            $sql_array[] = "FIND_IN_SET(".$value->id.", batch_ids)";
                        }
                        $sql_string = implode(" OR ", $sql_array);
                        $sql_string = "(".$sql_string.")";
                        $sql_string_array[]= $sql_string;
                    }
                   
                }
                if($sql_string_array)
                {
                    $sql_string_main = implode(" AND ", $sql_string_array);
                    $sql_string_main = "(".$sql_string_main.")";
                    $criteria->addCondition($sql_string_main);
                    
                }
            }
            
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
        
        
        
        
        
        
        public function getLessonPlanTotal($subject_id, $batch_id=0, $lessonplan_category_id=0)
        {
           
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            if($lessonplan_category_id)
            $criteria->compare('t.lessonplan_category_id', $lessonplan_category_id);
            
            $criteria->compare('t.author_id', Yii::app()->user->id);
            
            if($batch_id)
            $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
            
            if($subject_id)
            $criteria->addCondition("FIND_IN_SET(".$subject_id.", subject_ids)");
            
            $data = $this->find($criteria);
            return $data->total;
        } 
        public function getLessonPlanLastUpdated($subject_id, $batch_id=0)
        {
           
            $criteria = new CDbCriteria();
            $criteria->select = 't.publish_date';
            
            $criteria->compare('t.is_show', 1);
            $criteria->addCondition('t.publish_date IS NOT NULL AND t.publish_date<="'.date('Y-m-d').'"');
            
            
            if($batch_id)
            $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
            
            if($subject_id)
            $criteria->addCondition("FIND_IN_SET(".$subject_id.", subject_ids)");
            
            $criteria->limit = 1;
            $criteria->order = "t.publish_date DESC";
            
            $data = $this->find($criteria);
            return $data->publish_date;
        }
        
        public function getLessonPlanStudent($subject_id = 0, $batch_id = 0, $page = 1, $page_size)
        {
            
            
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.is_show', 1);
            $criteria->addCondition('t.publish_date IS NOT NULL AND t.publish_date<="'.date('Y-m-d').'"');
            
            if($batch_id)
            $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
            
            if($subject_id)
            $criteria->addCondition("FIND_IN_SET(".$subject_id.", subject_ids)");
            
            $criteria->order = "t.publish_date DESC";
            $start = ($page-1)*$page_size;
            $criteria->limit = $page_size;

            $criteria->offset = $start;
            
            $data = $this->with("category")->findAll($criteria);
            $response_array = array();
            if($data != NULL)
            foreach($data as $value)
            {
                $marge = array();
                $marge['id']   = $value->id;
                $marge['title'] = $value->title;
                $marge['publish_date'] = $value->publish_date;
                $marge['attachment_file_name'] = ""; 
                if($value->attachment_file_name)
                {
                    $marge['attachment_file_name'] = $value->attachment_file_name;
                }
                $response_array[] = $marge;     
                
            }
            return $response_array;
            
        }
        public function getLessonPlanTotalStudent($subject_id, $batch_id=0)
        {
           
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            
            $criteria->compare('t.is_show', 1);
            $criteria->addCondition('t.publish_date IS NOT NULL AND t.publish_date<="'.date('Y-m-d').'"');
            
            if($batch_id)
            $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
            
            if($subject_id)
            $criteria->addCondition("FIND_IN_SET(".$subject_id.", subject_ids)");
            
            $data = $this->find($criteria);
            return $data->total;
        }
        public function getLessonPlanSingle($id)
        {
            
            
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.id', $id);
            $criteria->limit = 1;    
            $value = $this->with("category")->find($criteria);
            if($value)
            {
                $response_array['category']   = $value["category"]->name;
                $response_array['title']   = $value->title;
                $response_array['content']   = $value->content;
                $response_array['publish_date'] = "";
                if($value->publish_date)
                $response_array['publish_date']   = $value->publish_date;
                $response_array['is_show']   = $value->is_show;
                $response_array['attachment_file_name'] = ""; 
                if($value->attachment_file_name)
                {
                    $response_array['attachment_file_name'] = $value->attachment_file_name;
                }
                $response_array['subjects'] = "";
                $subjectobj = new Subjects();
                if($value->subject_ids)
                {
                    $sub_array = explode(",", $value->subject_ids);
                    $subject_names = $subjectobj->getSubjectFullName($sub_array);
                    if($subject_names)
                    {
                        $response_array['subjects'] = implode(", ", $subject_names);
                    }
                    
                }  
                
                
            }  
            return $response_array;
            
        }
        
        
        public function getLessonPlan($subject_id = 0, $batch_id = 0, $lessonplan_category_id = 0, $page = 1, $page_size)
        {
            
            
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            if($lessonplan_category_id)
            $criteria->compare('t.lessonplan_category_id', $lessonplan_category_id);
            
            $criteria->compare('t.author_id', Yii::app()->user->id);
            
            if($batch_id)
            $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
            
            if($subject_id)
            $criteria->addCondition("FIND_IN_SET(".$subject_id.", subject_ids)");
            
            $criteria->order = "t.created_at DESC";
            $start = ($page-1)*$page_size;
            $criteria->limit = $page_size;

            $criteria->offset = $start;
            
            $data = $this->with("category")->findAll($criteria);
            $response_array = array();
            if($data != NULL)
            foreach($data as $value)
            {
                $marge = array();
                
                $marge['id']   = $value->id;
                
                $marge['category']   = $value["category"]->name;
                $marge['title'] = $value->title;
                $marge['is_show'] = $value->is_show;
                $marge['attachment_file_name'] = ""; 
                if($value->attachment_file_name)
                {
                    $marge['attachment_file_name'] = $value->attachment_file_name;
                }
              
                $marge['subjects'] = "";
                $subjectobj = new Subjects();
                if($value->subject_ids)
                {
                    $sub_array = ($subject_id) ? $subject_id : explode(",", $value->subject_ids);
                    $subject_names = $subjectobj->getSubjectFullName($sub_array);
                    if($subject_names)
                    {
                        $marge['subjects'] = implode(", ", $subject_names);
                    }
                    
                }    
                
                $response_array[] = $marge;     
                
            }
            return $response_array;
            
        }
        
        
        
        
}
