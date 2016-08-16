<?php

/**
 * This is the model class for table "subjects".
 *
 * The followings are the available columns in table 'subjects':
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property integer $batch_id
 * @property integer $no_exams
 * @property integer $max_weekly_classes
 * @property integer $elective_group_id
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $credit_hours
 * @property integer $prefer_consecutive
 * @property string $amount
 * @property integer $school_id
 */
class Subjects extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'subjects';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('batch_id, no_exams, max_weekly_classes, elective_group_id, is_deleted, prefer_consecutive, school_id', 'numerical', 'integerOnly' => true),
            array('name, code', 'length', 'max' => 255),
            array('credit_hours, amount', 'length', 'max' => 15),
            array('created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, code, batch_id, no_exams, max_weekly_classes, elective_group_id, is_deleted, created_at, updated_at, credit_hours, prefer_consecutive, amount, school_id', 'safe', 'on' => 'search'),
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
            'Subjectbatch' => array(self::BELONGS_TO, 'Batches', 'batch_id',
                'joinType' => 'INNER JOIN',
            ),
            
            'electiveGroup' => array(self::BELONGS_TO, 'ElectiveGroups', 'elective_group_id',
                'joinType' => 'LEFT JOIN',
            ),
            'employee' => array(self::HAS_MANY, 'EmployeesSubjects', 'subject_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'code' => 'Code',
            'batch_id' => 'Batch',
            'no_exams' => 'No Exams',
            'max_weekly_classes' => 'Max Weekly Classes',
            'elective_group_id' => 'Elective Group',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'credit_hours' => 'Credit Hours',
            'prefer_consecutive' => 'Prefer Consecutive',
            'amount' => 'Amount',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('batch_id', $this->batch_id);
        $criteria->compare('no_exams', $this->no_exams);
        $criteria->compare('max_weekly_classes', $this->max_weekly_classes);
        $criteria->compare('elective_group_id', $this->elective_group_id);
        $criteria->compare('is_deleted', $this->is_deleted);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('credit_hours', $this->credit_hours, true);
        $criteria->compare('prefer_consecutive', $this->prefer_consecutive);
        $criteria->compare('amount', $this->amount, true);
        $criteria->compare('school_id', $this->school_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Subjects the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    public function getSubjectElectiveGroup($id)
    {
       $criteria = new CDbCriteria(); 
       $criteria->select = 't.*';
       $criteria->compare('t.elective_group_id', $id);
       $subjects = $this->findAll($criteria);
       return $subjects;
    }
    public function getSubjectFullName($id)
    {
       $criteria = new CDbCriteria(); 
       $criteria->select = 't.name';
       $criteria->compare('t.id', $id);
       $criteria->with =array(
                        "Subjectbatch" => array(
                            "select" => "Subjectbatch.name",
                            'joinType' => "INNER JOIN",
                            'with' => array(
                                "courseDetails" => array(
                                    "select" => "courseDetails.course_name",
                                    'joinType' => "INNER JOIN",
                                )
                            )
                     )
            );
       $subject = array();
       $subjects = $this->findAll($criteria);
       if($subjects)
       foreach($subjects as $value)
       {
           $subject[] = $value->name." ".$value['Subjectbatch']->name." ".$value['Subjectbatch']['courseDetails']->course_name;
       }
      
       
       return $subject;
    }
    
    public function getSubjectNoExam($batch_id,$student_id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.name,t.id,t.icon_number,t.no_exams';
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.no_exams', 1);
        $criteria->order = "t.name asc";
        $data_subject = $this->findAll($criteria);
        $stsub = new StudentsSubjects();
        $student_subject = $stsub->getStudentSubject($student_id);
        $subject_array = array();
        $i = 0;
        if($data_subject)
        {
            foreach($data_subject as $value)
            {
                $subject_array[$i]['name'] = $value->name;
                $subject_array[$i]['id'] = $value->id;
                $subject_array[$i]['icon'] = "";
                if(isset($value->icon_number))
                {
                    $subject_array[$i]['icon'] = $value->icon_number;
                }
                $i++;
            }    
        } 
        if($student_subject)
        {
            foreach($student_subject as $value)
            {
                $subject_array[$i]['name'] = $value->name;
                $subject_array[$i]['id'] = $value->id;
                $subject_array[$i]['icon'] = "";
                if(isset($value->icon_number))
                {
                    $subject_array[$i]['icon'] = $value->icon_number;
                }
                $i++;
            }    
        } 
        return $subject_array;
    }
    
    public function getSubject($batch_id,$student_id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.name,t.id,t.icon_number,t.no_exams';
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.no_exams', 0);
        $criteria->order = "t.name asc";
        $data_subject = $this->findAll($criteria);
        $stsub = new StudentsSubjects();
        $student_subject = $stsub->getStudentSubject($student_id);
        $subject_array = array();
        $i = 0;
        if($data_subject)
        {
            foreach($data_subject as $value)
            {
                $subject_array[$i]['name'] = $value->name;
                $subject_array[$i]['id'] = $value->id;
                $subject_array[$i]['icon'] = "";
                if(isset($value->icon_number))
                {
                    $subject_array[$i]['icon'] = $value->icon_number;
                }
                $i++;
            }    
        } 
        if($student_subject)
        {
            foreach($student_subject as $value)
            {
                $subject_array[$i]['name'] = $value->name;
                $subject_array[$i]['id'] = $value->id;
                $subject_array[$i]['icon'] = "";
                if(isset($value->icon_number))
                {
                    $subject_array[$i]['icon'] = $value->icon_number;
                }
                $i++;
            }    
        } 
        return $subject_array;
    }
    
    public function getTotalMarkPercent($id)
    {
        $examGroup = new ExamGroups();
        $exam_group = $examGroup->findByPk($id);
        
        $std = new Students();
        $all_std = $std->getStudentByBatchFull($exam_group->batch_id);
        
        $exams = $examGroup->getAllExamByID($id);
        
        
        $all_report = array();
        $total_number = 0;
        if($exams)
        {
            $k = 0;
            foreach($exams as $value)
            {
                foreach($value['Exams'] as $examvalue)
                {

                    if($examvalue['Scores'])
                    {
                      foreach($examvalue['Scores'] as $examscore)
                      {
                            if(isset($examscore->marks) && $examscore->marks)
                            {
                                if(!isset($all_report[$examscore->student_id]))
                                {
                                    $all_report[$examscore->student_id] = 0;
                                }    
                                $all_report[$examscore->student_id] = $all_report[$examscore->student_id]+$examscore->marks;
                            }
                      }

                    }
                    if(isset($examvalue->maximum_marks) && $examvalue->maximum_marks)
                    {
                        $total_number = $total_number+$examvalue->maximum_marks;
                    }
                  
                }  
            }
        }
        foreach($all_std as $value)
        {
            if(!in_array($value->id, array_keys($all_report)))
            {
                $all_report[$value->id] = 0;
            }
        }    
        
        $percent_student = array("80-100"=>0,"60-79"=>0,"40-59"=>0,"0-39"=>0);
        foreach($all_report as $value)
        {
            $percent = ($value/$total_number)*100;
            
            foreach($percent_student as $key=>$kvalue)
            {
                $key_explode = explode("-", $key);
                if($percent>=$key_explode[0] && $percent<=$key_explode[1])
                {
                    $percent_student[$key] = $kvalue+1;
                    break;
                }
            }    
            
        }  
        $std_count = count($all_std);
        foreach($percent_student as $key=>$value)
        {
            if($std_count>0)
            {
                $percent_student[$key] = round(($value/$std_count)*100);
            }
        }
        return $percent_student;
    }        
    
    public function getTermReportAll($id)
    {
        $examGroup = new ExamGroups();
        $exams = $examGroup->getAllExamByID($id); 
        
        
       
        $all_report = array();
        $exam_students = array();
        $exam_students_for_sorting = array();
        $std_id_array = array();
        $total_point = array();
        $total_number = array();
        $total_point_number = array();
        if($exams)
        {
            $k = 0;
            foreach($exams as $value)
            {
                $i = 0;
                foreach($value['Exams'] as $examvalue)
                {
                  $all_report[$i]['subject_name'] =  $examvalue['Subjects']->name;
                  $all_report[$i]['subject_id'] =  $examvalue->subject_id;
                  
                  if($examvalue['Scores'])
                  {
                    foreach($examvalue['Scores'] as $examscore)
                    {
                          $all_report[$i]['score'][$examvalue->subject_id][$examscore->student_id]['score'] = 0;
                          
                          if(!isset($total_number[$examscore->student_id]))
                          {
                              $total_number[$examscore->student_id] = 0;
                          }
                          
                          if(!isset($total_point[$examscore->student_id]))
                          {
                              $total_point[$examscore->student_id] = 0;
                          }
                          
                          
                          if(isset($examscore->marks) && $examscore->marks)
                          {
                            $all_report[$i]['score'][$examvalue->subject_id][$examscore->student_id]['score'] = $examscore->marks;
                            $total_number[$examscore->student_id] = $total_number[$examscore->student_id]+$examscore->marks;
                            
                          }
                          
                          $all_report[$i]['score'][$examvalue->subject_id][$examscore->student_id]['grade'] = "-";
                          $all_report[$i]['score'][$examvalue->subject_id][$examscore->student_id]['grade_point'] = "-";

                          if(isset($examscore['Examgrade']->name))
                          $all_report[$i]['score'][$examvalue->subject_id][$examscore->student_id]['grade'] = $examscore['Examgrade']->name;
                          if(isset($examscore['Examgrade']->credit_points))
                          {
                            $all_report[$i]['score'][$examvalue->subject_id][$examscore->student_id]['grade_point'] = $examscore['Examgrade']->credit_points;
                            $total_point[$examscore->student_id] = $total_point[$examscore->student_id]+$examscore['Examgrade']->credit_points;
                          }
                          
                          $total_point_number[$examscore->student_id] = $total_point[$examscore->student_id]."-".$total_number[$examscore->student_id];
                          
                          
                      

                          if(!in_array($examscore->student_id, $std_id_array) && isset($examscore['Students']->first_name))
                          {
                              $std_id_array[] = $examscore->student_id;
                              $exam_students[$k]['id'] =  $examscore->student_id;
                              $std_name = $examscore['Students']->first_name;

                              if($examscore['Students']->middle_name)
                              {
                                  $std_name = $std_name." ".$examscore['Students']->middle_name;
                              }
                              $std_name = $std_name." ".$examscore['Students']->last_name;
                              $exam_students[$k]['name'] =  $std_name;
                              $exam_students_for_sorting[$k] =  $examscore->student_id;
                              $exam_students[$k]['position'] =  0;
                              $exam_students[$k]['total'] =  0;
                              $exam_students[$k]['gpa'] =  0;
                              $exam_students[$k]['grade'] =  "-";
                              $k++;
                          }

                    } 
                  }
                  $i++;
                }
            }
        }
        $grading_level = new GradingLevels();
        $all_grade =  $grading_level->getAllGrade(Yii::app()->user->schoolId);
        if($total_point_number && $exam_students)      
        {
            arsort($total_point_number);
            $i=1;
            foreach($total_point_number as $key=>$value)
            {
                $st_key = array_search($key, $exam_students_for_sorting);
                if($st_key !== false)
                {
                    $exam_students[$st_key]['position'] =  $i;
                    $exam_students[$st_key]['total'] =  $value;
                    
                    $exploded_value = explode("-", $value);
                    
                    $exam_students[$st_key]['total_number'] =  $exploded_value[1];
                    if($exploded_value[0]>0)
                    {
                        
                        $exam_students[$st_key]['gpa'] = number_format(($exploded_value[0]/count($all_report)), 2);
                        $exam_students[$st_key]['grade'] = $this->getGrade($exam_students[$st_key]['gpa'],$all_grade);
                    }    
                    
                }
                $i++;
            }    
            
        } 
        if($exam_students)
        {
            usort($exam_students, function($a, $b) {
                        return $a['position'] - $b['position'];
            });
        }
        
        return array('report'=>$all_report,"students"=>$exam_students);
        
        
    }
    private function getGrade($grade_point,$all_grade)
    {
        $grade_name = "-";
        if($all_grade)
        {
            foreach($all_grade as $value)
            {
                if($grade_point>=$value->credit_points)
                {
                    
                    $grade_name = $value->name;
                    break;
                }    
            }    
        }
        return $grade_name;
        
    }        

    public function getTermReport($batch_id, $student_id, $id = 0, $no_exams=0)
    {

        $examGroup = new ExamGroups();
        $exams = $examGroup->getTermExamsBatch($batch_id,$student_id,$id,$no_exams);
        $report_term = array();
        
        $objStudent = new Students();
        
        $total_student = $objStudent->countByAttributes(array(
            'batch_id'=> $batch_id
        ));
        
        $examModel = new Exams();
       
        foreach($exams as $value)
        {
          
            
           $report_term_merge['exam_id']      =  $value->id;
           
           $report_term_merge['exam_name']    =  $value->name;
           $report_term_merge['acknowledge']  =  false;
           if($value['Acknowledge'] && !Yii::app()->user->isTeacher)
           {
                if (Yii::app()->user->isStudent) {
                     $ack_by = '0';
                 }

                 if (Yii::app()->user->isParent) {
                     $ack_by = '1';
                 }
                 foreach ($value['Acknowledge'] as $acvalue)
                 {
                     if($acvalue->acknowledge_by==$ack_by)
                     {
                        $report_term_merge['acknowledge']  =  true;
                        break;
                     }    
                 }    
           }    
           $i = 0;
           $total_grade_point = 0;
           $only_grade_point = 0;
           $total_cradit_hours = 0;
           $total_mark = 0;
           foreach($value['Exams'] as $examvalue)
           {
                $examScore = new ExamScores();
                $student_result = $examScore->getSingleExamStudentResult($student_id, $examvalue->id);
                if($student_result)
                {
                    $total_mark = $total_mark+$student_result->marks; 
                    if($student_result['Examgrade'])
                    {
                        $only_grade_point = $only_grade_point+$student_result['Examgrade']->credit_points;
                    }
                }
                if($examvalue['Subjects']->no_exams && $no_exams==0)
                {
                    continue;
                }
                $report_term_merge['exam_subjects'][$i]['subject_name'] = $examvalue['Subjects']->name;
                $report_term_merge['exam_subjects'][$i]['subject_code'] = $examvalue['Subjects']->code;
                $report_term_merge['exam_subjects'][$i]['no_exams']     = $examvalue['Subjects']->no_exams;
                $report_term_merge['exam_subjects'][$i]['subject_id']   = $examvalue['Subjects']->id;
                $report_term_merge['exam_subjects'][$i]['subject_icon'] = $examvalue['Subjects']->icon_number;
                $cradit_hours = 100;
                if($examvalue['Subjects']->credit_hours && !$examvalue['Subjects']->no_exams )
                {
                    $cradit_hours = $examvalue['Subjects']->credit_hours;
                    $total_cradit_hours = $total_cradit_hours+$examvalue['Subjects']->credit_hours;
                }
                else if(!$examvalue['Subjects']->no_exams)
                {
                     $total_cradit_hours = $total_cradit_hours+100;
                }    
                $report_term_merge['exam_subjects'][$i]['cradit_hours'] = $examvalue['Subjects']->credit_hours;
                
                $report_term_merge['exam_subjects'][$i]['total_cradit_hours'] = $total_cradit_hours;
               
               
               
             
                
                $max_mark = $examScore->getExamStudentMaxMark($examvalue->id);
                $report_term_merge['exam_subjects'][$i]['your_grade']    = "-";
                $report_term_merge['exam_subjects'][$i]['your_mark']     = "-";
                $report_term_merge['exam_subjects'][$i]['your_percent']  = "-";
                $report_term_merge['exam_subjects'][$i]['credit_points'] = "-";
                $report_term_merge['exam_subjects'][$i]['percentile'] = 0;
                $report_term_merge['exam_subjects'][$i]['max_mark']   = 0;
                $report_term_merge['exam_subjects'][$i]['total_mark'] = 0;
                $report_term_merge['exam_subjects'][$i]['remarks'] = "-";
                
                if($student_result)
                {
                    if($student_result['Examgrade'])
                    $report_term_merge['exam_subjects'][$i]['your_grade']   = $student_result['Examgrade']->name;
                    if($student_result->marks)
                    {
                        $report_term_merge['exam_subjects'][$i]['your_mark']    = $student_result->marks;
                        $report_term_merge['exam_subjects'][$i]['percentile'] = $examModel->getPercentile($value->id, $student_result->marks, $examvalue['Subjects']->id);
                    }
                    
                    if($student_result['Examgrade'])
                    $report_term_merge['exam_subjects'][$i]['credit_points']= $student_result['Examgrade']->credit_points;
                    
                    
                    if($examvalue->maximum_marks && $student_result->marks)
                    $report_term_merge['exam_subjects'][$i]['your_percent'] = ($student_result->marks / $examvalue->maximum_marks) * 100;
                    
                    if($examvalue->maximum_marks && $student_result->marks)
                    $report_term_merge['exam_subjects'][$i]['your_percent'] = intval($report_term_merge['exam_subjects'][$i]['your_percent']);
                    
                  
                    
                    if($student_result['Examgrade'] && !$examvalue['Subjects']->no_exams)
                    {
                        $total_grade_point = $total_grade_point+($student_result['Examgrade']->credit_points*$cradit_hours);
                        
                    }
                   
                    
                    $report_term_merge['exam_subjects'][$i]['remarks'] = $student_result->remarks;
                }
                if($max_mark)
                $report_term_merge['exam_subjects'][$i]['max_mark']   = $max_mark;
                if($examvalue->maximum_marks)
                $report_term_merge['exam_subjects'][$i]['total_mark'] = $examvalue->maximum_marks;
                $i++;
                
           }
           if($total_grade_point && $total_grade_point>0 && $total_cradit_hours>0)
           {
                $grading_level = new GradingLevels();
                $report_term_merge['GPA']   = number_format(($total_grade_point/$total_cradit_hours), 2);
                $report_term_merge['grade'] = $grading_level->getGrade($report_term_merge['GPA'],Yii::app()->user->schoolId);
           }
           else
           {
                $report_term_merge['GPA']   = "-";
                $report_term_merge['grade'] = "-";
           }
           $report_term_merge['total_mark'] = $total_mark;
           //$report_term_merge['total_grade_point'] = $total_grade_point;
           $report_term_merge['total_student'] = $total_student;
         
           
           
           $report_term_merge['Your_position'] = $examModel->getPosition($value->id, $only_grade_point, $total_mark);
         
           
           $report_term[] = $report_term_merge; 
        }   
       
        return $report_term;
        
    }
    public function getPrograssAll($batch_id, $student_id, $exam_category=0)
    {
      
        $progress = array();
        $subject = new Subjects();
        $all_subject = $subject->getSubject($batch_id, $student_id);
         
        $color_array = ['#000000','#FF0000','#00FF00','#0000FF','#BF8277','#FF00FF','#00FFFF','#5954D8','#84C1A3','#AAA5BF','#D3CE87','#DDBA87','#CE5E60','#829E8C','#876656','#CE5E60','#7F7F9B','#AD998C'];
        
        $j = 0;
        foreach($all_subject as $value)
        {
            $examModel = new Exams();
            $exam_details_all = $examModel->getPublishExam($value['id'], $batch_id, $exam_category);
            if (!empty($exam_details_all))
            {
                $i = 0;
                $progress['subject'][$j]['name'] = $value['name'];
                $progress['subject'][$j]['color'] = $color_array[$j];
                foreach ($exam_details_all as $exam_details)
                {
                    $examScore = new ExamScores();
                    $student_result = $examScore->getSingleExamStudentResult($student_id, $exam_details->id);
                    
                    if($exam_details['Examgroup']->exam_category == 2)
                    {
                        $e_name = "P";
                    } 
                    else if($exam_details['Examgroup']->exam_category == 1)
                    {
                        $e_name = "C";
                    } 
                    else
                    {
                        $e_name = "T";
                    }  
                    $extra = $i+1;
                    $progress['subject'][$j]['exam'][$i]['name'] = $e_name." ".$extra;
                    $progress['subject'][$j]['exam'][$i]['point'] = 0;
                   
                    if (!empty($student_result))
                    {
                        $your_percent = ($student_result->marks / $exam_details->maximum_marks) * 100;

                        $progress['subject'][$j]['exam'][$i]['point'] = intval($your_percent);
                    }
                    $i++;
                }
             
              $j++;
            }
            
        }
        
        return $progress;
    }
    
    public function getSubjectReport($subject_id,$student_id,$exam_group_id=0,$exam_id=0)
    {
        $examModel = new Exams();
        $exam = $examModel->getExamSubject($subject_id,$exam_group_id, $exam_id);
        
       
        $examScore = new ExamScores();
        $student_result = $examScore->getSingleExamStudentResult($student_id, $exam->id);
        $max_mark = $examScore->getExamStudentMaxMark($exam->id);
        $avg_mark = $examScore->getExamStudentAvgMark($exam->id);
        $result = array();
       
        $result['exam_id'] = $exam->id;
        $result['exam_name'] = $exam['Examgroup']->name;
        $result['exam_date'] = DATE("Y-m-d", strtotime($exam->start_time));
        $result['category'] = $exam['Examgroup']->exam_category;
        $result['total_mark'] = $exam->maximum_marks;

        $result['your_grade'] = "-";
        $result['grade_point'] = "-"; 
        $result['remark'] = "-";

        $result['your_mark'] = "-";
        $result['your_percent'] = 0.00;
        $result['max_mark'] = "-";
        $result['max_mark_percent'] = 0.00;
        $result['avg_mark'] = 0.00;
        $result['avg_mark_percent'] = 0.00;
        $result['percentile'] = 0.00;
        if (!empty($student_result) && !empty($max_mark))
        {
            if(isset($student_result['Examgrade']->name))
            $result['your_grade'] = $student_result['Examgrade']->name;

            if(isset($student_result['Examgrade']->credit_points))
            $result['grade_point'] = $student_result['Examgrade']->credit_points;
            
            if(isset($student_result->remarks))
            $result['remark'] = $student_result->remarks;
            
            if($student_result->marks)
            {
                $result['your_mark'] = $student_result->marks;

                $result['your_percent'] = ($student_result->marks / $exam->maximum_marks) * 100;

                $result['your_percent'] = intval($result['your_percent']);
                $result['percentile'] = $examModel->getPercentile($exam->exam_group_id, $student_result->marks, $subject_id);
            }

            //$total_mark = $total_mark+$result['your_percent'];

            $result['max_mark'] = $max_mark;
            $result['max_mark_percent'] = ($max_mark/ $exam->maximum_marks) * 100;
            $result['max_mark_percent'] = intval($result['max_mark_percent']);

            $result['avg_mark'] = round($avg_mark, 2);
            $result['avg_mark_percent'] = ($avg_mark/ $exam->maximum_marks) * 100;
            $result['avg_mark_percent'] = intval($result['avg_mark_percent']);
            
        }
        return $result;
    }        
    
    
    public function getPrograss($batch_id, $student_id, $subject_id, $exam_category=0)
    {
      
        $progress = array();
      

        $examModel = new Exams();
        $exam_details_all = $examModel->getPublishExam($subject_id, $batch_id, $exam_category);

        $avg = 0;
        if (!empty($exam_details_all))
        {
            $i = 0;
            $total_mark = 0;
            
            foreach ($exam_details_all as $exam_details)
            {

                $examScore = new ExamScores();

                $student_result = $examScore->getSingleExamStudentResult($student_id, $exam_details->id);
                $max_mark = $examScore->getExamStudentMaxMark($exam_details->id);
                $avg_mark = $examScore->getExamStudentAvgMark($exam_details->id);
                if (!empty($student_result) && !empty($max_mark))
                {
                    
                    $progress['exam'][$i]['exam_id'] = $exam_details->id;
                    $progress['exam'][$i]['exam_name'] = $exam_details['Examgroup']->name;
                    $progress['exam'][$i]['exam_date'] = DATE("Y-m-d", strtotime($exam_details->start_time));

                    $progress['exam'][$i]['your_grade'] = "-";
                    $progress['exam'][$i]['grade_point'] = "-";

                    if(isset($student_result['Examgrade']->name))
                    $progress['exam'][$i]['your_grade'] = $student_result['Examgrade']->name;

                    if(isset($student_result['Examgrade']->credit_points))
                    $progress['exam'][$i]['grade_point'] = $student_result['Examgrade']->credit_points;


                    $progress['exam'][$i]['your_mark'] = $student_result->marks;
                  
                    $progress['exam'][$i]['your_percent'] = ($student_result->marks / $exam_details->maximum_marks) * 100;

                    $progress['exam'][$i]['your_percent'] = intval($progress['exam'][$i]['your_percent']);
                    
                    $total_mark = $total_mark+$progress['exam'][$i]['your_percent'];
                    
                    $progress['exam'][$i]['max_mark'] = $max_mark;
                    $progress['exam'][$i]['max_mark_percent'] = ($max_mark/ $exam_details->maximum_marks) * 100;
                    $progress['exam'][$i]['max_mark_percent'] = intval($progress['exam'][$i]['max_mark_percent']);
                    
                    $progress['exam'][$i]['avg_mark'] = round($avg_mark, 2);
                    $progress['exam'][$i]['avg_mark_percent'] = ($avg_mark/ $exam_details->maximum_marks) * 100;
                    $progress['exam'][$i]['avg_mark_percent'] = intval($progress['exam'][$i]['avg_mark_percent']);
                    
                    
                    $progress['exam'][$i]['category'] = $exam_details['Examgroup']->exam_category;
                    $progress['exam'][$i]['total_mark'] = $exam_details->maximum_marks;
                    
                    

                    $i++;
                      


                }
            }
            if($total_mark && $i)
            {
                $avg = $total_mark/$i;
                $avg = intval($avg);
                $progress['avg'] = $avg;
            }
       
        }
        
        return $progress;
    }
   
    
    public function getBatchSubjectClassTestProjectReport($batch_id, $student_id, $exam_group=0, $no_exams=0)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.*';
        $criteria->compare('t.batch_id', $batch_id);
        if($no_exams == 0)
        {
            $criteria->compare('t.no_exams', false);
        }
        $criteria->compare('t.is_deleted', false);
        
        $data_subject = $this->findAll($criteria);



        $report_class_test = array();
        foreach ($data_subject as $value)
        {
            if($no_exams==0 && $value->no_exams==1)
            {
                continue;
            }

            $examModel = new Exams();
            $exam_details_all = $examModel->getPublishClassTestProjectSubjectWise($value->id, $batch_id, false, $exam_group);
            
            $report_class_test_merge['subject_name'] = $value->name;
            $report_class_test_merge['no_exams']     = $value->no_exams;
            $report_class_test_merge['subject_code'] = $value->code;
            $report_class_test_merge['subject_id']   = $value->id;
            $report_class_test_merge['subject_icon'] = $value->icon_number;
            $report_class_test_merge['subject_exam']['class_test'] = array();
            $report_class_test_merge['subject_exam']['project']    = array();
            $i = 0;
            $j = 0;
            
            if (!empty($exam_details_all))
            {
                foreach ($exam_details_all as $exam_details)
                {

                    $examScore = new ExamScores();

                    $student_result = $examScore->getSingleExamStudentResult($student_id, $exam_details->id);



                    $max_mark = $examScore->getExamStudentMaxMark($exam_details->id);
                    if (!empty($student_result) && !empty($max_mark))
                    {
                        if($exam_details['Examgroup']->exam_category==1)
                        {
                            $report_class_test_merge['subject_exam']['class_test'][$i]['exam_id'] = $exam_details->id;
                            $report_class_test_merge['subject_exam']['class_test'][$i]['exam_name'] = $exam_details['Examgroup']->name;
                            $report_class_test_merge['subject_exam']['class_test'][$i]['exam_group_id'] = $exam_details['Examgroup']->id;
                            $report_class_test_merge['subject_exam']['class_test'][$i]['exam_date'] = DATE("Y-m-d", strtotime($exam_details->start_time));
                            
                            $report_class_test_merge['subject_exam']['class_test'][$i]['your_grade'] = "-";
                            $report_class_test_merge['subject_exam']['class_test'][$i]['grade_point'] = "-";
                            $report_class_test_merge['subject_exam']['class_test'][$i]['your_mark'] = "-";
                            $report_class_test_merge['subject_exam']['class_test'][$i]['your_percent'] = "-";
                            $report_class_test_merge['subject_exam']['class_test'][$i]['topic'] = "-";
                            $report_class_test_merge['subject_exam']['class_test'][$i]['remark'] = "-";
                            
                            if($student_result->remarks)
                            $report_class_test_merge['subject_exam']['class_test'][$i]['remark'] = $student_result->remarks;
                            
                            if(isset($student_result['Examgrade']->name))
                            $report_class_test_merge['subject_exam']['class_test'][$i]['your_grade'] = $student_result['Examgrade']->name;
                            
                            if(isset($student_result['Examgrade']->name))
                            $report_class_test_merge['subject_exam']['class_test'][$i]['grade_point'] = $student_result['Examgrade']->credit_points;
                            
                            if($student_result->marks)
                            {
                                $report_class_test_merge['subject_exam']['class_test'][$i]['your_mark'] = $student_result->marks;
                                $report_class_test_merge['subject_exam']['class_test'][$i]['your_percent'] = ($student_result->marks / $exam_details->maximum_marks) * 100;

                                $report_class_test_merge['subject_exam']['class_test'][$i]['your_percent'] = intval($report_class_test_merge['subject_exam']['class_test'][$i]['your_percent']);
                            }
                            if($exam_details['Examgroup']->topic)
                            {
                                $report_class_test_merge['subject_exam']['class_test'][$i]['topic'] = $exam_details['Examgroup']->topic;
                            }
                            $report_class_test_merge['subject_exam']['class_test'][$i]['max_mark'] = $max_mark;
                            $report_class_test_merge['subject_exam']['class_test'][$i]['category'] = $exam_details['Examgroup']->exam_category;
                            $report_class_test_merge['subject_exam']['class_test'][$i]['total_mark'] = $exam_details->maximum_marks;
                        
                            $i++;
                        }
                        else
                        {
                            $report_class_test_merge['subject_exam']['project'][$j]['exam_id'] = $exam_details->id;
                            $report_class_test_merge['subject_exam']['project'][$j]['exam_name'] = $exam_details['Examgroup']->name;
                            $report_class_test_merge['subject_exam']['project'][$i]['exam_group_id'] = $exam_details['Examgroup']->id;
                            $report_class_test_merge['subject_exam']['project'][$j]['exam_date'] = DATE("Y-m-d", strtotime($exam_details->start_time));
                            
                            $report_class_test_merge['subject_exam']['project'][$j]['your_grade'] = "-";
                            $report_class_test_merge['subject_exam']['project'][$j]['grade_point'] = "-";
                            $report_class_test_merge['subject_exam']['project'][$j]['your_mark'] = "-";
                            $report_class_test_merge['subject_exam']['project'][$j]['your_percent'] = "-";
                            $report_class_test_merge['subject_exam']['project'][$j]['topic'] = "-";
                            
                            $report_class_test_merge['subject_exam']['project'][$i]['remark'] = "-";
                            
                            if($student_result->remarks)
                            $report_class_test_merge['subject_exam']['project'][$i]['remark'] = $student_result->remarks;
                            
                            if(isset($student_result['Examgrade']->name))
                            $report_class_test_merge['subject_exam']['project'][$j]['your_grade'] = $student_result['Examgrade']->name;
                            
                            if(isset($student_result['Examgrade']->name))
                            $report_class_test_merge['subject_exam']['project'][$j]['grade_point'] = $student_result['Examgrade']->credit_points;
                            
                            
                            if($student_result->marks)
                            {
                                $report_class_test_merge['subject_exam']['project'][$j]['your_mark'] = $student_result->marks;
                                $report_class_test_merge['subject_exam']['project'][$j]['your_percent'] = ($student_result->marks / $exam_details->maximum_marks) * 100;

                                $report_class_test_merge['subject_exam']['project'][$j]['your_percent'] = intval($report_class_test_merge['subject_exam']['project'][$j]['your_percent']);
                            }
                            if($exam_details['Examgroup']->topic)
                            {
                                $report_class_test_merge['subject_exam']['project'][$j]['topic'] = $exam_details['Examgroup']->topic;
                            }
                            $report_class_test_merge['subject_exam']['project'][$j]['max_mark'] = $max_mark;
                            $report_class_test_merge['subject_exam']['project'][$j]['category'] = $exam_details['Examgroup']->exam_category;
                            $report_class_test_merge['subject_exam']['project'][$j]['total_mark'] = $exam_details->maximum_marks;
                        
                            $j++;
                        }    
                    
                        
                    }
                }
                if($report_class_test_merge['subject_exam']['class_test'] || $report_class_test_merge['subject_exam']['project'])
                $report_class_test[] = $report_class_test_merge;
            }
        }
        return $report_class_test;
    }
    
   

}
