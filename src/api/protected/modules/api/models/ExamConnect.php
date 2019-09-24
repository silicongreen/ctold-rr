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
class ExamConnect extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'exam_connects';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'groupedexam' => array(self::HAS_MANY, 'GroupedExams', 'connect_exam_id',
                'joinType' => 'LEFT JOIN'
            )    
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function getConnectExam($batch_id)
    {
        
             
        $criteria = new CDbCriteria;
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.is_published', 1);
        $criteria->select = 't.*';
        $criteria->order = "published_date DESC";
        $connect_exam = $this->findAll($criteria);
        return $connect_exam;
    }
    
    public function getConnectExamKgFirstTerm($batch_id)
    {
        $res_type = array(5);
        if(Yii::app()->user->schoolId == 352)
        {
            $res_type = array(1,3,5);
        } 
        $criteria = new CDbCriteria;
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.is_deleted', 0);
        if(Yii::app()->user->schoolId == 352)
        {
            $criteria->compare('t.quarter_number', 1);
        }
        $criteria->addInCondition('t.result_type',$res_type);
        $criteria->select = 't.id';
        $criteria->order = "created_at DESC";
        $criteria->limit = 1;
        $connect_exam = $this->find($criteria);
        
        if($connect_exam)
        {
            return $connect_exam->id;
        }
        return false;
    }
    
    public function getConnectExamFirstTerm($batch_id)
    {
        $res_type = array(2);
        $quarter_number = 0;
        if(Yii::app()->user->schoolId == 352)
        {
            $res_type = array(1,3,5);
            $quarter_number = 1;
        }        
        $criteria = new CDbCriteria;
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.is_deleted', 0);
        if(Yii::app()->user->schoolId != 312)
        {
          $criteria->compare('t.quarter_number', $quarter_number); 
        }
        
        $criteria->addInCondition('t.result_type',$res_type);
        $criteria->select = 't.id';
        $criteria->order = "created_at DESC";
        $criteria->limit = 1;
        $connect_exam = $this->find($criteria);
        
        if($connect_exam)
        {
            return $connect_exam->id;
        }
        return false;
    }
    
   
    
    public function getConnectExamByBatch($batch_id,$result_type = 4,$name="")
    {
        $criteria = new CDbCriteria;
        $criteria->compare('t.batch_id', $batch_id);
//        $criteria->compare('t.is_deleted', 0);
        if($name)
        {
            $criteria->compare('t.name', $name);
        }
        $criteria->compare('t.result_type', $result_type);
        $criteria->select = 't.id';
        $criteria->order = "created_at DESC";
        $criteria->limit = 1;
        $connect_exam = $this->find($criteria);
        
        if($connect_exam)
        {
            return $connect_exam->id;
        }
        return false;
    }        

    public function getConnectExamReportAll($id, $subject_id)
    {
        $subjectObj = new Subjects();
        $sub_data = $subjectObj->findByPk($subject_id);
        
        if($sub_data->elective_group_id)
        {
            $sub_std = new StudentsSubjects();
            $all_student_this_subject = $sub_std->getSubjectStudentFull($sub_data->id, $sub_data->batch_id);
        }
        else
        {
            $sub_std = new Students();
            $all_student_this_subject = $sub_std->getBatchStudentFull($sub_data->batch_id);
        }    
        
        $criteria = new CDbCriteria;
        $criteria->together = true;
        $criteria->compare('t.id', $id);
        $criteria->compare('t.is_deleted', 0);
        $criteria->select = 't.*';
        $criteria->with = array(
            'groupedexam' => array(
                'select' => 'groupedexam.id',
                'joinType' => 'LEFT JOIN',
                'with' => array(
                    'examgroup' => array(
                        'select' => 'examgroup.id,examgroup.name,examgroup.exam_category,examgroup.quarter,examgroup.exam_date',
                        'joinType' => 'LEFT JOIN',
                        'with' => array(
                            'Exams' => array(
                                'select' => 'Exams.id,Exams.maximum_marks,Exams.alternative_title,Examps.exam_date,Exams.end_time,Exams.weightage',
                                'joinType' => 'LEFT JOIN',
                                'with' => array(
                                    'Scores' => array(
                                        'select' => 'Scores.marks,Scores.student_id,Scores.remarks',
                                        'joinType' => 'LEFT JOIN',
                                        'with' => array(
                                            'Students' => array(
                                                'joinType' => 'LEFT JOIN',
                                                'select' => 'Students.first_name,Students.last_name,Students.middle_name,Students.class_roll_no,Students.id,Students.batch_id',
                                            ),
                                        )
                                    ),
                                    'Subjects' => array(
                                        'select' => 'Subjects.id',
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
        $criteria->order = "groupedexam.priority ASC,examgroup.created_at ASC";
        $criteria->compare('Exams.subject_id', $subject_id);
        
        $examresult = $this->find($criteria);
        
        $result = array();
        $students = array();
        $allstudents = array();
        if($examresult)
        {
         
            $i = 0;
            $k = 0;
            $j = 0;
            $f = 0;
            $m = 0;
            $max_mark_ct = 0;
            $max_mark_st = 0;
            if($examresult['groupedexam'])
            {
                foreach($examresult['groupedexam'] as $groupedexam)
                {
                    if($groupedexam['examgroup']->exam_category == 1)
                    {
                        $exam =$groupedexam['examgroup']['Exams'][0];
                        $result['CT'][$i]['exam_category'] = $groupedexam['examgroup']->exam_category;
                        $result['CT'][$i]['name'] = $groupedexam['examgroup']->name;
                        $result['CT'][$i]['alternative_title'] = $exam->alternative_title;
                        
                        $result['CT'][$i]['exam_date'] = date("d/m/y",strtotime($exam->end_time));
                        if($exam->exam_date)
                        {
                            $result['CT'][$i]['alternative_title'] = $exam->alternative_title." ".date("d/m",strtotime($exam->exam_date));
                        }
                        $result['CT'][$i]['quarter'] = $groupedexam['examgroup']->quarter;
                        
                        $result['CT'][$i]['maximum_marks'] = $exam->maximum_marks;
                        $result['CT'][$i]['converted_marks'] = $exam->weightage;
                        if($i == 0)
                        {
                            $max_mark_ct = $exam->maximum_marks;
                        }
                        foreach($exam['Scores'] as $scores)
                        {
                            if(isset($scores['Students']) && $sub_data->batch_id == $scores['Students']->batch_id )
                            {
                                $std_middle_name = ($scores['Students']->middle_name)?$scores['Students']->middle_name." ":"";
                                if(!in_array($scores['Students']->id, $students))
                                {
                                    $students[] = $scores['Students']->id;
                                    $result['students'][$j]['name'] = $scores['Students']->first_name." ".$std_middle_name.$scores['Students']->last_name;
                                    $result['students'][$j]['id'] = $scores['Students']->id;
                                    $result['students'][$j]['class_roll_no'] = $scores['Students']->class_roll_no;
                                    $j++;
                                }
                                
                            
                            
                                $result['CT'][$i]['students'][$scores['Students']->id]['score'] = $scores->marks;
                                $result['CT'][$i]['students'][$scores['Students']->id]['remarks'] = $scores->remarks;
                            }
                            else
                            {
                                continue;
                            }
                            
                        } 
                        foreach($all_student_this_subject as $svalue)
                        {
                            if(!in_array($svalue['student_id'], $students))
                            {
                                $students[] = $svalue['student_id'];
                                $result['students'][$j]['name'] = $svalue['student_name'];
                                $result['students'][$j]['id'] = $svalue['student_id'];
                                $result['students'][$j]['class_roll_no'] = $svalue['roll_no'];
                                $j++;
                                $result['CT'][$i]['students'][$svalue['student_id']]['score'] = 0.0;
                                $result['CT'][$i]['students'][$svalue['student_id']]['remarks'] = "";
                                
                            }
                        }
                        $i++;
                         
                        
                    }
                    else if($groupedexam['examgroup']->exam_category == 4)
                    {
                        $exam =$groupedexam['examgroup']['Exams'][0];
                        $result['ST'][$k]['exam_category'] = $groupedexam['examgroup']->exam_category;
                        $result['ST'][$k]['name'] = $groupedexam['examgroup']->name;
                        $result['ST'][$k]['alternative_title'] = $exam->alternative_title;
                        if($exam->exam_date)
                        {
                            $result['ST'][$k]['alternative_title'] = $exam->alternative_title." ".date("d/m",strtotime($exam->exam_date));
                        }
                        $result['ST'][$k]['exam_date'] = date("d/m/y",strtotime($exam->end_time));
                        $result['ST'][$k]['quarter'] = $groupedexam['examgroup']->quarter;
                        
                        $result['ST'][$k]['maximum_marks'] = $exam->maximum_marks;
                        $result['ST'][$k]['converted_marks'] = $exam->weightage;
                        if($k == 0)
                        {
                            $max_mark_st = $exam->maximum_marks;
                        }
                        foreach($exam['Scores'] as $scores)
                        {
                            if(isset($scores['Students']) && $sub_data->batch_id == $scores['Students']->batch_id )
                            {
                                $std_middle_name = ($scores['Students']->middle_name)?$scores['Students']->middle_name." ":"";
                                if(!in_array($scores['Students']->id, $students))
                                {
                                    $students[] = $scores['Students']->id;
                                    $result['students'][$j]['name'] = $scores['Students']->first_name." ".$std_middle_name.$scores['Students']->last_name;
                                    $result['students'][$j]['id'] = $scores['Students']->id;
                                    $result['students'][$j]['class_roll_no'] = $scores['Students']->class_roll_no;
                                    $j++;
                                }
                                $result['ST'][$k]['students'][$scores['Students']->id]['score'] = $scores->marks;
                                $result['ST'][$k]['students'][$scores['Students']->id]['remarks'] = $scores->remarks;
                            }
                            else
                            {
                                continue;
                            }
                          
                        } 
                        foreach($all_student_this_subject as $svalue)
                        {
                            if(!in_array($svalue['student_id'], $students))
                            {
                                $students[] = $svalue['student_id'];
                                $result['students'][$j]['name'] = $svalue['student_name'];
                                $result['students'][$j]['id'] = $svalue['student_id'];
                                $result['students'][$j]['class_roll_no'] = $svalue['roll_no'];
                                $j++;
                                $result['ST'][$k]['students'][$svalue['student_id']]['score'] = 0.0;
                                $result['ST'][$k]['students'][$svalue['student_id']]['remarks'] = "";
                                
                            }
                        }
                        $k++;
                    
                    }
                    
                    
                    $exam =$groupedexam['examgroup']['Exams'][0];
                    $result['ALL'][$f]['exam_category'] = $groupedexam['examgroup']->exam_category;
                    $result['ALL'][$f]['name'] = $groupedexam['examgroup']->name;
                    $result['ALL'][$f]['alternative_title'] = $exam->alternative_title;
                    if($exam->exam_date)
                    {
                        $result['ALL'][$f]['alternative_title'] = $exam->alternative_title."<br/>".date("d/m",strtotime($exam->exam_date));
                    }
                    $result['ALL'][$f]['exam_id'] = $exam->id;
                    $result['ALL'][$f]['exam_date'] =  date("d/m/y",strtotime($exam->end_time));
                    $result['ALL'][$f]['quarter'] = $groupedexam['examgroup']->quarter;
                    $result['ALL'][$f]['exam_category'] = $groupedexam['examgroup']->exam_category;
                    $result['ALL'][$f]['maximum_marks'] = $exam->maximum_marks;
                    $result['ALL'][$f]['converted_marks'] = $exam->weightage;
                    foreach($exam['Scores'] as $scores)
                    {
                        if(isset($scores['Students']) && $sub_data->batch_id == $scores['Students']->batch_id )
                        {
                            $std_middle_name = ($scores['Students']->middle_name)?$scores['Students']->middle_name." ":"";
                            if(!in_array($scores['Students']->id, $allstudents))
                            {
                                $allstudents[] = $scores['Students']->id;
                                $result['al_students'][$m]['name'] = $scores['Students']->first_name." ".$std_middle_name.$scores['Students']->last_name;
                                $result['al_students'][$m]['id'] = $scores['Students']->id;
                                $result['al_students'][$m]['class_roll_no'] = $scores['Students']->class_roll_no;
                                $m++;
                            }


                            $result['ALL'][$f]['students'][$scores['Students']->id]['score'] = $scores->marks;
                            $result['ALL'][$f]['students'][$scores['Students']->id]['remarks'] = $scores->remarks;
                        }
                        else
                        {
                                continue;
                        }
                        

                    } 
                    foreach($all_student_this_subject as $svalue)
                    {
                        if(!in_array($svalue['student_id'], $students))
                        {
                            $students[] = $svalue['student_id'];
                            $result['students'][$j]['name'] = $svalue['student_name'];
                            $result['students'][$j]['id'] = $svalue['student_id'];
                            $result['students'][$j]['class_roll_no'] = $svalue['roll_no'];
                            $j++;
                            $result['ALL'][$f]['students'][$svalue['student_id']]['score'] = 0.0;
                            $result['ALL'][$f]['students'][$svalue['student_id']]['remarks'] = "";

                        }
                    }
                    $f++;
                    
                } 
              $result['max_mark_ct'] = $max_mark_ct;
              $result['max_mark_st'] = $max_mark_st;
               if (Yii::app()->user->schoolId == "340" or Yii::app()->user->schoolId == "319")
                {
                 
                   if(isset($result['students']))
                    {

                      usort($result['students'], function($a, $b) {
                            return strcmp($a["name"], $b["name"]);
                      });
                    }
                    if(isset($result['al_students']))
                    {
                      usort($result['al_students'], function($a, $b) {
                            return strcmp($a["name"], $b["name"]);
                      });
                    } 
                }
                else
                {
              
                    if(isset($result['students']))
                    {

                      usort($result['students'], function($a, $b) {
                            return $a['class_roll_no'] - $b['class_roll_no'];
                      });
                    }
                    if(isset($result['al_students']))
                    {
                      usort($result['al_students'], function($a, $b) {
                            return $a['class_roll_no'] - $b['class_roll_no'];
                      });
                    }
                }
                
            }
            
        }
        return $result;

    }

}
