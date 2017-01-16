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
        $criteria->addCondition('t.published_date<="'.date("Y-m-d").'"');
        $criteria->select = 't.*';
        $criteria->order = "published_date DESC";
        $connect_exam = $this->findAll($criteria);
        return $connect_exam;
    }  
    
    public function getConnectExamByBatch($batch_id,$quarter=1)
    {
        $criteria = new CDbCriteria;
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.quarter_number', $quarter);
        $criteria->compare('t.result_type', 4);
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
        $criteria = new CDbCriteria;
        $criteria->compare('t.id', $id);
        $criteria->select = 't.*';
        $criteria->with = array(
            'groupedexam' => array(
                'select' => 'groupedexam.id',
                'joinType' => 'LEFT JOIN',
                'with' => array(
                    'examgroup' => array(
                        'select' => 'examgroup.id,examgroup.name,examgroup.exam_category,examgroup.quarter',
                        'joinType' => 'LEFT JOIN',
                        'with' => array(
                            'Exams' => array(
                                'select' => 'Exams.id,Exams.maximum_marks',
                                'joinType' => 'LEFT JOIN',
                                'with' => array(
                                    'Scores' => array(
                                        'select' => 'Scores.marks,Scores.student_id',
                                        'with' => array(
                                            'Students' => array(
                                                'select' => 'Students.first_name,Students.last_name,Students.middle_name,Students.class_roll_no,Students.id',
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
                        $result['CT'][$i]['name'] = $groupedexam['examgroup']->name;
                        $result['CT'][$i]['quarter'] = $groupedexam['examgroup']->quarter;
                        
                        $result['CT'][$i]['maximum_marks'] = $exam->maximum_marks;
                        if($i == 0)
                        {
                            $max_mark_ct = $exam->maximum_marks;
                        }
                        foreach($exam['Scores'] as $scores)
                        {
                            if(isset($scores['Students']))
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
                            }
                            else
                            {
                                continue;
                            }
                            
                        } 
                        $i++;
                         
                        
                    }
                    else if($groupedexam['examgroup']->exam_category == 4)
                    {
                        $exam =$groupedexam['examgroup']['Exams'][0];
                        $result['ST'][$k]['name'] = $groupedexam['examgroup']->name;
                        $result['ST'][$k]['quarter'] = $groupedexam['examgroup']->quarter;
                        
                        $result['ST'][$k]['maximum_marks'] = $exam->maximum_marks;
                        if($k == 0)
                        {
                            $max_mark_st = $exam->maximum_marks;
                        }
                        foreach($exam['Scores'] as $scores)
                        {
                            if(isset($scores['Students']))
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
                            }
                            else
                            {
                                continue;
                            }
                          
                        } 
                        $k++;
                    
                    }
                    
                    
                    $exam =$groupedexam['examgroup']['Exams'][0];
                    $result['ALL'][$f]['name'] = $groupedexam['examgroup']->name;
                    $result['ALL'][$f]['quarter'] = $groupedexam['examgroup']->quarter;
                    $result['ALL'][$f]['exam_category'] = $groupedexam['examgroup']->exam_category;
                    $result['ALL'][$f]['maximum_marks'] = $exam->maximum_marks;
                    foreach($exam['Scores'] as $scores)
                    {
                        if(isset($scores['Students']))
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
                        }
                        else
                        {
                                continue;
                        }
                        

                    } 
                    $f++;
                    
                } 
              $result['max_mark_ct'] = $max_mark_ct;
              $result['max_mark_st'] = $max_mark_st;
              
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
        return $result;

    }

}
