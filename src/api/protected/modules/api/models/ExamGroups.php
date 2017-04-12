<?php

/**
 * This is the model class for table "exam_groups".
 *
 * The followings are the available columns in table 'exam_groups':
 * @property integer $id
 * @property string $name
 * @property integer $batch_id
 * @property string $exam_type
 * @property integer $is_published
 * @property integer $result_published
 * @property string $exam_date
 * @property integer $is_final_exam
 * @property integer $cce_exam_category_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class ExamGroups extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public $allMark;
    public function tableName()
    {
        return 'exam_groups';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('batch_id, is_published, result_published, is_final_exam, cce_exam_category_id, school_id', 'numerical', 'integerOnly' => true),
            array('name, exam_type', 'length', 'max' => 255),
            array('exam_date, created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, batch_id, exam_type, is_published, result_published, exam_date, is_final_exam, cce_exam_category_id, created_at, updated_at, school_id', 'safe', 'on' => 'search'),
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
            'Exams' => array(self::HAS_MANY, 'Exams', 'exam_group_id',
                'joinType' => 'LEFT JOIN',
                'with' => array('Subjects'),
            ),
            'GroupedExams' => array(self::HAS_MANY, 'GroupedExams', 'exam_group_id',
                'joinType' => 'LEFT JOIN'
            ),
            'Batches' => array(self::BELONGS_TO, 'Batches', 'batch_id',
                        'joinType' => 'LEFT JOIN',
                    ),
            'Acknowledge' => array(self::HAS_MANY, 'UserExamAcknowledge', 'exam_id',
                'joinType' => 'LEFT JOIN',
            ),
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
            'batch_id' => 'Batch',
            'exam_type' => 'Exam Type',
            'is_published' => 'Is Published',
            'result_published' => 'Result Published',
            'exam_date' => 'Exam Date',
            'is_final_exam' => 'Is Final Exam',
            'cce_exam_category_id' => 'Cce Exam Category',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('batch_id', $this->batch_id);
        $criteria->compare('exam_type', $this->exam_type, true);
        $criteria->compare('is_published', $this->is_published);
        $criteria->compare('result_published', $this->result_published);
        $criteria->compare('exam_date', $this->exam_date, true);
        $criteria->compare('is_final_exam', $this->is_final_exam);
        $criteria->compare('cce_exam_category_id', $this->cce_exam_category_id);
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
     * @return ExamGroups the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    public function getAllExamsClass($class_name)
    {
       $criteria = new CDbCriteria();
       $criteria->select = 't.id.t.name';
       $criteria->compare('t.is_deleted', 0);
       $criteria->compare('t.result_published', 1);
       $criteria->compare('t.school_id', Yii::app()->user->schoolId);
       $criteria->compare('t.only_comment_base', 0);
       $criteria->compare('courseDetails.course_name', trim($class_name));
       $criteria->with = array(
                'Batches' => array(
                    'select' => 'Batches.name',
                    'joinType' => 'LEFT JOIN',
                    'with' => array(
                        'courseDetails' => array(
                            'select' => 'courseDetails.section_name',
                            'joinType' => 'LEFT JOIN'
                        )
                    )
                )
        );
        $data = $this->findAll($criteria);
        return $data;
       
    } 
    public function getAllExamsPublishClass($class_name,$exam_name)
    {
       $criteria = new CDbCriteria();
       $criteria->select = 't.id';
       $criteria->compare('t.is_deleted', 0);
       $criteria->compare('t.result_published', 1);
       $criteria->compare('t.name', trim($exam_name));
       $criteria->compare('t.school_id', Yii::app()->user->schoolId);
       $criteria->compare('t.only_comment_base', 0);
       $criteria->compare('courseDetails.course_name', trim($class_name));
       $criteria->with = array(
                'Batches' => array(
                    'select' => 'Batches.name',
                    'joinType' => 'LEFT JOIN',
                    'with' => array(
                        'courseDetails' => array(
                            'select' => 'courseDetails.section_name',
                            'joinType' => 'LEFT JOIN'
                        )
                    )
                )
        );
        $data = $this->findAll($criteria);
        return $data;
       
    }        
    
    public function getAllExamsResultPublish($batch_id,$category_id=1,$no_exams=0)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.id,t.name,t.exam_date,t.only_comment_base';
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.result_published', 1);
        
        if($no_exams==0)
        {
           $criteria->compare('t.only_comment_base', 0); 
        }    
        if($category_id>0)
        {
            $criteria->compare('t.exam_category', $category_id);
        }
        $criteria->order = "t.created_at DESC";
        $data = $this->findAll($criteria);
        $all_exam = array();
        if($data != NULL)
        {
            $i = 0;
            
            foreach($data as $kvalue)
            {
                $rid[]= $kvalue->id;
            }
            $robject = new Reminders();

            $new_data = $robject->FindUnreadData(3, $rid);
            foreach($data as $value)
            {
                $all_exam[$i]['id'] = $value->id;
                $all_exam[$i]['name'] = $value->name;
                if($value->exam_date != "1979-01-01")
                {
                    $all_exam[$i]['exam_date'] = $value->exam_date;
                }
                else 
                {
                    $all_exam[$i]['exam_date'] = "N/A";
                }
                $all_exam[$i]['only_comment_base'] = $value->only_comment_base;
                $all_exam[$i]['is_new'] = 0;
                    
                if(in_array($value->id, $new_data))
                {
                    $all_exam[$i]['is_new'] = 1;
                }
               
                $i++;
            }
        }
        
        return $all_exam;
    }
    
    public function getAllExamsBatch($batch_id,$no_exams=0)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.id,t.name,t.exam_date,t.only_comment_base';
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.is_published', 1);
        if($no_exams==0)
        {
           $criteria->compare('t.only_comment_base', 0); 
        } 
        $criteria->order = "t.exam_date ASC";
        $data = $this->findAll($criteria);
        
        $all_exam = array();
        if($data != NULL)
        {
            $i = 0;
            
            foreach($data as $kvalue)
            {
                $rid[]= $kvalue->id;
            }
            $robject = new Reminders();

            $new_data = $robject->FindUnreadData(2, $rid);
            foreach($data as $value)
            {
                $all_exam[$i]['id'] = $value->id;
                $all_exam[$i]['name'] = $value->name;
                if($value->exam_date!="1979-01-01")
                {
                    $all_exam[$i]['exam_date'] = $value->exam_date;
                }
                else 
                {
                    $all_exam[$i]['exam_date'] = "N/A";
                }
                $all_exam[$i]['only_comment_base'] = $value->only_comment_base;
                $all_exam[$i]['is_new'] = 0;
                    
                if(in_array($value->id, $new_data))
                {
                    $all_exam[$i]['is_new'] = 1;
                }
               
                $i++;
            }
        }
        
        return $all_exam;
    }
    
    public function getCommentAll($exam_group_id,$subject_id,$students)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.id'; 
        $criteria->compare('t.id', $exam_group_id);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('Subjects.is_deleted', false);
        $criteria->compare('Subjects.id', $subject_id);
        $criteria->with = array(
                'Exams' => array(
                    'select' => 'Exams.id',
                    'with' => array(
                        'Scores' => array(
                            'select' => 'Scores.remarks',
                            'with' => array(
                                    'Students' => array(
                                        'select' => 'Students.id',
                                    ),
                             )
                        ),
                        'Subjects' => array(
                            'select' => 'Subjects.id',
                        )

                    )
                )
        );
        $criteria->order = "Subjects.priority ASC";
        $comments = $this->find($criteria);
        $return_comments = array();
        foreach($students as $value)
        {
            $return_comments[$value] = "";
        }
        if($comments)
        {
            foreach($comments['Exams'] as $examresult)
            {
                if(isset($examresult) && isset($examresult['Scores']) )
                {
                    foreach($examresult['Scores'] as $score)
                    {
                        if( isset($score->remarks))
                        {
                            $return_comments[$score['Students']->id] = $score->remarks;
                        }
                    }     
                }
            } 
        }
        return  $return_comments;  
    }
    
    public function getComment($exam_group_id,$student_id,$subject_id)
    {
        $comment = "";
        $criteria = new CDbCriteria();
        $criteria->select = 't.id'; 
        $criteria->compare('t.id', $exam_group_id);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('Subjects.is_deleted', false);
        $criteria->compare('Subjects.id', $subject_id);
        $criteria->compare('Students.id', $student_id);
        $criteria->with = array(
                'Exams' => array(
                    'select' => 'Exams.id',
                    'with' => array(
                        'Scores' => array(
                            'select' => 'Scores.remarks',
                            'with' => array(
                                    'Students' => array(
                                        'select' => 'Students.id',
                                    ),
                             )
                        ),
                        'Subjects' => array(
                            'select' => 'Subjects.id',
                        )

                    )
                )
        );
        $criteria->order = "Subjects.priority ASC";
        $examresult = $this->find($criteria);
        
        if($examresult)
        {
            if(isset($examresult['Exams'][0]) && isset($examresult['Exams'][0]['Scores']) && isset($examresult['Exams'][0]['Scores'][0]) && isset($examresult['Exams'][0]['Scores'][0]->remarks))
            {
                $comment = $examresult['Exams'][0]['Scores'][0]->remarks;
            }     
        }
         
        
        return $comment;
        
    }
    
    public function getExamGroupResultMaxMarkContinues($exam_group_id,$result=array(),$max_mark=array())
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.name,t.id'; 
        $criteria->compare('t.is_deleted', 0);
        $criteria->addInCondition('t.id', $exam_group_id);
        //$criteria->compare('t.result_published', 1);
        $criteria->compare('Subjects.no_exams', false);
        $criteria->compare('Subjects.is_deleted', false);
        $criteria->with = array(
                'Exams' => array(
                    'select' => 'Exams.maximum_marks',
                    'with' => array(
                        'Scores' => array(
                            'select' => 'Scores.marks,Scores.student_id',
                        ),
                        'Subjects' => array(
                            'select' => 'Subjects.id',
                        )

                    )
                )
        );
        $examresult_obj = $this->findAll($criteria);
        if($examresult_obj)
        {
            foreach($examresult_obj as $examresult)
            {
                if($examresult['Exams'])
                {
                    foreach($examresult['Exams'] as $value)
                    {
                        if($value['Scores'])
                        {
                            foreach($value['Scores'] as $score)
                            {
                                if($score->marks)
                                {
                                    if(isset($result[$value['Subjects']->id][$score->student_id]['total_mark']))
                                    {
                                       $result[$value['Subjects']->id][$score->student_id]['total_mark'] = $result[$value['Subjects']->id][$score->student_id]['total_mark']+$score->marks; 
                                    }
                                    else 
                                    {
                                       $result[$value['Subjects']->id][$score->student_id]['total_mark'] = $score->marks; 
                                    }
                                    if(isset($max_mark[$value['Subjects']->id]))
                                    {
                                       if($result[$value['Subjects']->id][$score->student_id]['total_mark']>$max_mark[$value['Subjects']->id]) 
                                       {
                                           $max_mark[$value['Subjects']->id] = $result[$value['Subjects']->id][$score->student_id]['total_mark'];
                                       }
                                    }
                                    else
                                    {
                                        $max_mark[$value['Subjects']->id] = $result[$value['Subjects']->id][$score->student_id]['total_mark'];
                                    }  
                                       

                                }
                            } 
                        }
                    }
                }
            }
        }
        
        return array($result,$max_mark);
    }
    
    
    public function getExamGroupResultMaxMark($exam_group_id,$result=array(),$max_mark=array())
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.name,t.id'; 
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.id', $exam_group_id);
        //$criteria->compare('t.result_published', 1);
        $criteria->compare('Subjects.no_exams', false);
        $criteria->compare('Subjects.is_deleted', false);
        $criteria->with = array(
                'Exams' => array(
                    'select' => 'Exams.maximum_marks',
                    'with' => array(
                        'Scores' => array(
                            'select' => 'Scores.marks,Scores.student_id',
                        ),
                        'Subjects' => array(
                            'select' => 'Subjects.id',
                        )

                    )
                )
        );
        $examresult = $this->find($criteria);
        if($examresult)
        {
            if($examresult['Exams'])
            {
                foreach($examresult['Exams'] as $value)
                {
                    if($value['Scores'])
                    {
                        foreach($value['Scores'] as $score)
                        {
                            if($score->marks)
                            {
                                if(isset($result[$value['Subjects']->id][$score->student_id]['total_mark']))
                                {
                                   $result[$value['Subjects']->id][$score->student_id]['total_mark'] = $result[$value['Subjects']->id][$score->student_id]['total_mark']+$score->marks; 
                                }
                                else 
                                {
                                   $result[$value['Subjects']->id][$score->student_id]['total_mark'] = $score->marks; 
                                }
                                
                                if(isset($max_mark[$value['Subjects']->id]))
                                {
                                   if($result[$value['Subjects']->id][$score->student_id]['total_mark']>$max_mark[$value['Subjects']->id]) 
                                   {
                                       $max_mark[$value['Subjects']->id] = $result[$value['Subjects']->id][$score->student_id]['total_mark'];
                                   }
                                }
                                else
                                {
                                    $max_mark[$value['Subjects']->id] = $result[$value['Subjects']->id][$score->student_id]['total_mark'];
                                }    
                                
                            }
                        } 
                    }
                }
            }
        }
        
        return array($result,$max_mark);
    }
    public function getExamGroupResultSubjectAllStudentCont($exam_group_id,$count,$result,$weightage,$students)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.name,t.id,t.sba,t.exam_category,t.quarter'; 
        $criteria->compare('t.id', $exam_group_id);
        $criteria->compare('t.is_deleted', 0);
        //$criteria->compare('t.result_published', 1);
        $criteria->compare('Subjects.no_exams', false);
        $criteria->compare('Subjects.is_deleted', false);
        
        $criteria->with = array(
                'Exams' => array(
                    'select' => 'Exams.maximum_marks',
                    'with' => array(
                        'Scores' => array(
                            'select' => 'Scores.marks',
                            'with' => array(
                                    'Examgrade' => array(
                                        'select' => 'Examgrade.name',
                                    ),
                                    'Students' => array(
                                        'select' => 'Students.id',
                                    ),
                             )
                        ),
                        'Subjects' => array(
                            'select' => 'Subjects.id',
                        )

                    )
                )
        );
        $criteria->order = "Subjects.priority ASC";
        $examresult = $this->find($criteria);
        
        
        $criteria = new CDbCriteria();
        $criteria->select = 't.name,t.id,t.sba,t.exam_category,t.quarter'; 
        $criteria->compare('t.id', $exam_group_id);
        //$criteria->compare('t.result_published', 1);
        $criteria->compare('Subjects.no_exams', false);
        $criteria->compare('Subjects.is_deleted', false);
        $criteria->with = array(
                'Exams' => array(
                    'select' => 'Exams.maximum_marks',
                    'with' => array(
                        'Subjects' => array(
                            'select' => 'Subjects.id',
                        )

                    )
                )
        );
        $criteria->order = "Subjects.priority ASC";
        $all_exams = $this->find($criteria);
        
        
        
        
        $exam_ids = array();
        if($all_exams)
        {
            foreach($students as $student)
            {
                $result[$student]['exams'][$count]['quarter'] = $all_exams->quarter;
                $result[$student]['exams'][$count]['exam_name'] = $all_exams->name;
                $result[$student]['exams'][$count]['sba'] = $all_exams->sba;

                $result[$student]['exams'][$count]['exam_category'] = $all_exams->exam_category;
                $result[$student]['exams'][$count]['exam_id'] = $all_exams->id;
            
                foreach($all_exams['Exams'] as $value)
                {
                    $result[$student]['exams'][$count]['result'][$all_exams->id][$value['Subjects']->id]['marks_obtained'] = "AB";
                    $result[$student]['exams'][$count]['result'][$all_exams->id][$value['Subjects']->id]['grade'] = "N/A";
                    $result[$student]['exams'][$count]['result'][$all_exams->id][$value['Subjects']->id]['weightage_mark'] = 0;
                    $result[$student]['exams'][$count]['result'][$all_exams->id][$value['Subjects']->id]['full_mark'] = $value->maximum_marks;
                   
                } 
                $exam_ids[$student][$count] = $all_exams->id;
            }
        }    
        
        if($examresult)
        {
         
            foreach($examresult['Exams'] as $value)
            {
               

               if(isset($value['Scores']))
               {
                   foreach($value['Scores'] as $key=>$score)
                   {
                       if(isset($score->marks) && isset($score['Students']->id) && isset($value['Subjects']->id))
                       {
                           
                            $result[$score['Students']->id]['exams'][$count]['result'][$examresult->id][$value['Subjects']->id]['marks_obtained'] = $score->marks;
                            if($value->maximum_marks==0)
                            {
                                $result[$score['Students']->id]['exams'][$count]['result'][$examresult->id][$value['Subjects']->id]['weightage_mark'] = 0;
                            }
                            else 
                            {
                                $result[$score['Students']->id]['exams'][$count]['result'][$examresult->id][$value['Subjects']->id]['weightage_mark'] = (($score->marks/$value->maximum_marks)*$weightage);
                            }
                            if(isset($score['Examgrade']->name))
                            {
                                $result[$score['Students']->id]['exams'][$count]['result'][$examresult->id][$value['Subjects']->id]['grade'] = $score['Examgrade']->name;
                            }
                       }
                        
                   }
               }
            }  
        } 
        
        return $result;
        
    } 
    
    
    public function getExamGroupResultSubjectAllStudentContinues($exam_group_id,$students)
    {
        $criteria = new CDbCriteria();
        $criteria->select = "t.name,t.id,t.sba,t.exam_category,t.quarter"; 
        $criteria->addIncondition('t.id', $exam_group_id);
        $criteria->compare('t.is_deleted', 0);
        //$criteria->compare('t.result_published', 1);
        $criteria->compare('Subjects.no_exams', false);
        $criteria->compare('Subjects.is_deleted', false);
        
        $criteria->with = array(
                'Exams' => array(
                    'select' => 'Exams.maximum_marks',
                    'with' => array(
                        'Scores' => array(
                            'select' => 'Scores.marks',
                            'with' => array(
                                    'Examgrade' => array(
                                        'select' => 'Examgrade.name',
                                    ),
                                    'Students' => array(
                                        'select' => 'Students.id',
                                    ),
                             )
                        ),
                        'Subjects' => array(
                            'select' => 'Subjects.id',
                        )

                    )
                ),
                'GroupedExams' => array(
                   'select' => 'GroupedExams.id' 
                )
        );
        $criteria->order = "FIELD(t.id,".implode(",",$exam_group_id)."),Subjects.priority ASC";
        $examresult_obj = $this->findAll($criteria);
        
        
        $criteria = new CDbCriteria();
        $criteria->select = "t.name,t.id,t.sba,t.exam_category,t.quarter"; 
        $criteria->addIncondition('t.id', $exam_group_id);
        //$criteria->compare('t.result_published', 1);
        $criteria->compare('Subjects.no_exams', false);
        $criteria->compare('Subjects.is_deleted', false);
        $criteria->with = array(
                'Exams' => array(
                    'select' => 'Exams.maximum_marks',
                    'with' => array(
                        'Subjects' => array(
                            'select' => 'Subjects.id',
                        )

                    )
                ),
                'GroupedExams' => array(
                   'select' => 'GroupedExams.id' 
                )
        );
        $criteria->order = "FIELD(t.id,".implode(",",$exam_group_id)."),Subjects.priority ASC";
        $all_exams_obj = $this->findAll($criteria);
        
        
        
        
        $result = array();
        
        $exam_ids = array();
        if($all_exams_obj)
        {
            foreach($students as $student)
            {
                $i = 0;
                foreach($all_exams_obj as $all_exams)
                {
                    $result[$student]['exams'][$i]['quarter'] = $all_exams->quarter;
                    $result[$student]['exams'][$i]['exam_name'] = $all_exams->name;
                    $result[$student]['exams'][$i]['sba'] = $all_exams->sba;

                    $result[$student]['exams'][$i]['exam_category'] = $all_exams->exam_category;
                    $result[$student]['exams'][$i]['exam_id'] = $all_exams->id;
                    foreach($all_exams['Exams'] as $value)
                    {
                       $result[$student]['exams'][$i]['result'][$all_exams->id][$value['Subjects']->id]['marks_obtained'] = "AB";
                       $result[$student]['exams'][$i]['result'][$all_exams->id][$value['Subjects']->id]['grade'] = "N/A";
                       $result[$student]['exams'][$i]['result'][$all_exams->id][$value['Subjects']->id]['weightage_mark'] = 0;
                       $result[$student]['exams'][$i]['result'][$all_exams->id][$value['Subjects']->id]['full_mark'] = $value->maximum_marks;
                    }
                    $exam_ids[$student][$i] = $all_exams->id;
                    $i++;
                }
            }
        }    
        
        if($examresult_obj)
        {
           
           foreach($examresult_obj as $examresult)
            {
               
                foreach($examresult['Exams'] as $value)
                {
                   
                        
                    if(isset($value['Scores']))
                    {
                        foreach($value['Scores'] as $key=>$score)
                        {
                            if(isset($score->marks) && isset($score['Students']->id) && isset($value['Subjects']->id) && isset($exam_ids[$score['Students']->id]))
                            {
                               
                                if(in_array($examresult->id, $exam_ids[$score['Students']->id]))
                                {
                                    $i = array_search($examresult->id, $exam_ids[$score['Students']->id]);
                                    $result[$score['Students']->id]['exams'][$i]['result'][$examresult->id][$value['Subjects']->id]['marks_obtained'] = $score->marks;
                                    if($value->maximum_marks==0)
                                    {
                                        $result[$score['Students']->id]['exams'][$i]['result'][$examresult->id][$value['Subjects']->id]['weightage_mark'] = 0;
                                    }
                                    else 
                                    {
                                        $result[$score['Students']->id]['exams'][$i]['result'][$examresult->id][$value['Subjects']->id]['weightage_mark'] = $score->marks/$value->maximum_marks;
                                    }
                                    if(isset($score['Examgrade']->name))
                                    {
                                        $result[$score['Students']->id]['exams'][$i]['result'][$examresult->id][$value['Subjects']->id]['grade'] = $score['Examgrade']->name;
                                    }
                                }
                            }

                        }
                    }
                   
                }
             
            }
        } 
        
        return $result;
        
    }
    
    public function getExamGroupResultSubjectAllStudent($exam_group_id,$weightage,$students)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.name,t.id,t.sba,t.exam_category,t.quarter'; 
        $criteria->compare('t.id', $exam_group_id);
        $criteria->compare('t.is_deleted', 0);
        //$criteria->compare('t.result_published', 1);
        $criteria->compare('Subjects.no_exams', false);
        $criteria->compare('Subjects.is_deleted', false);
        
        $criteria->with = array(
                'Exams' => array(
                    'select' => 'Exams.maximum_marks',
                    'with' => array(
                        'Scores' => array(
                            'select' => 'Scores.marks',
                            'with' => array(
                                    'Examgrade' => array(
                                        'select' => 'Examgrade.name',
                                    ),
                                    'Students' => array(
                                        'select' => 'Students.id',
                                    ),
                             )
                        ),
                        'Subjects' => array(
                            'select' => 'Subjects.id',
                        )

                    )
                )
        );
        $criteria->order = "Subjects.priority ASC";
        $examresult = $this->find($criteria);
        
        
        $criteria = new CDbCriteria();
        $criteria->select = 't.name,t.id,t.sba,t.exam_category,t.quarter'; 
        $criteria->compare('t.id', $exam_group_id);
        //$criteria->compare('t.result_published', 1);
        $criteria->compare('Subjects.no_exams', false);
        $criteria->compare('Subjects.is_deleted', false);
        $criteria->with = array(
                'Exams' => array(
                    'select' => 'Exams.maximum_marks',
                    'with' => array(
                        'Subjects' => array(
                            'select' => 'Subjects.id',
                        )

                    )
                )
        );
        $criteria->order = "Subjects.priority ASC";
        $all_exams = $this->find($criteria);
        
        
        
        
        $result = array();
        
        if($all_exams)
        {
            foreach($students as $student)
            {
                $result['quarter'] = $all_exams->quarter;
                $result['exam_name'] = $all_exams->name;
                $result['sba'] = $all_exams->sba;

                $result['exam_category'] = $all_exams->exam_category;
                $result['exam_id'] = $all_exams->id;
                foreach($all_exams['Exams'] as $value)
                {
                   $result['result'][$all_exams->id][$value['Subjects']->id][$student]['marks_obtained'] = "AB";
                   $result['result'][$all_exams->id][$value['Subjects']->id][$student]['grade'] = "N/A";
                   $result['result'][$all_exams->id][$value['Subjects']->id][$student]['weightage_mark'] = 0;
                   $result['result'][$all_exams->id][$value['Subjects']->id][$student]['full_mark'] = $value->maximum_marks;
                } 
            }
        }    
        
        if($examresult)
        {
            $result['quarter'] = $examresult->quarter;
            $result['exam_name'] = $examresult->name;
            $result['exam_id'] = $examresult->id;
            $result['sba'] = $examresult->sba;   
            $result['exam_category'] = $examresult->exam_category;
            foreach($examresult['Exams'] as $value)
            {
               

               if(isset($value['Scores']))
               {
                   foreach($value['Scores'] as $key=>$score)
                   {
                       if(isset($score->marks) && isset($score['Students']->id) && isset($value['Subjects']->id))
                       {
                           
                            $result['result'][$examresult->id][$value['Subjects']->id][$score['Students']->id]['marks_obtained'] = $score->marks;
                            if($value->maximum_marks==0)
                            {
                                $result['result'][$examresult->id][$value['Subjects']->id][$score['Students']->id]['weightage_mark'] = 0;
                            }
                            else 
                            {
                                $result['result'][$examresult->id][$value['Subjects']->id][$score['Students']->id]['weightage_mark'] = (($score->marks/$value->maximum_marks)*$weightage);
                            }
                            if(isset($score['Examgrade']->name))
                            {
                                $result['result'][$examresult->id][$value['Subjects']->id][$score['Students']->id]['grade'] = $score['Examgrade']->name;
                            }
                       }
                        
                   }
               }
            }  
        } 
        
        return $result;
        
    }   
    
    public function getExamGroupResultSubject($exam_group_id,$student_id,$weightage)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.name,t.id,t.sba,t.exam_category,t.quarter'; 
        $criteria->compare('t.id', $exam_group_id);
        $criteria->compare('t.is_deleted', 0);
        //$criteria->compare('t.result_published', 1);
        $criteria->compare('Subjects.no_exams', false);
        $criteria->compare('Subjects.is_deleted', false);
        $criteria->compare('Students.id', $student_id);
        
        $criteria->with = array(
                'Exams' => array(
                    'select' => 'Exams.maximum_marks',
                    'with' => array(
                        'Scores' => array(
                            'select' => 'Scores.marks',
                            'with' => array(
                                    'Examgrade' => array(
                                        'select' => 'Examgrade.name',
                                    ),
                                    'Students' => array(
                                        'select' => 'Students.id',
                                    ),
                             )
                        ),
                        'Subjects' => array(
                            'select' => 'Subjects.id',
                        )

                    )
                )
        );
        $criteria->order = "Subjects.priority ASC";
        $examresult = $this->find($criteria);
        
        
        $criteria = new CDbCriteria();
        $criteria->select = 't.name,t.id,t.sba,t.exam_category,t.quarter'; 
        $criteria->compare('t.id', $exam_group_id);
        //$criteria->compare('t.result_published', 1);
        $criteria->compare('Subjects.no_exams', false);
        $criteria->compare('Subjects.is_deleted', false);
        $criteria->with = array(
                'Exams' => array(
                    'select' => 'Exams.maximum_marks',
                    'with' => array(
                        'Subjects' => array(
                            'select' => 'Subjects.id',
                        )

                    )
                )
        );
        $criteria->order = "Subjects.priority ASC";
        $all_exams = $this->find($criteria);
        
        
        
        
        $result = array();
        
        if($all_exams)
        {
            $result['quarter'] = $all_exams->quarter;
            $result['exam_name'] = $all_exams->name;
            $result['sba'] = $all_exams->sba;
            
            $result['exam_category'] = $all_exams->exam_category;
            $result['exam_id'] = $all_exams->id;
            foreach($all_exams['Exams'] as $value)
            {
               $result['result'][$all_exams->id][$value['Subjects']->id]['marks_obtained'] = "AB";
               $result['result'][$all_exams->id][$value['Subjects']->id]['grade'] = "N/A";
               $result['result'][$all_exams->id][$value['Subjects']->id]['weightage_mark'] = 0;
               $result['result'][$all_exams->id][$value['Subjects']->id]['full_mark'] = $value->maximum_marks;
            }   
        }    
        
        if($examresult)
        {
            $result['exam_name'] = $examresult->name;
            $result['exam_id'] = $examresult->id;
            $result['sba'] = $examresult->sba;
            $result['quarter'] = $all_exams->quarter;
            
            $result['exam_category'] = $examresult->exam_category;
            foreach($examresult['Exams'] as $value)
            {
               $result['result'][$examresult->id][$value['Subjects']->id]['marks_obtained'] = "AB";
               $result['result'][$examresult->id][$value['Subjects']->id]['grade'] = "N/A";
               $result['result'][$examresult->id][$value['Subjects']->id]['weightage_mark'] = 0;
               $result['result'][$examresult->id][$value['Subjects']->id]['full_mark'] = $value->maximum_marks;

               if(isset($value['Scores'][0]->marks))
               {
                    $result['result'][$examresult->id][$value['Subjects']->id]['marks_obtained'] = $value['Scores'][0]->marks;
                    if($value->maximum_marks==0)
                    {
                        $result['result'][$examresult->id][$value['Subjects']->id]['weightage_mark'] = 0;
                    }
                    else 
                    {
                        $result['result'][$examresult->id][$value['Subjects']->id]['weightage_mark'] = (($value['Scores'][0]->marks/$value->maximum_marks)*$weightage);
                    }
               }
               if(isset($value['Scores'][0]['Examgrade']->name))
               {
                    $result['result'][$examresult->id][$value['Subjects']->id]['grade'] = $value['Scores'][0]['Examgrade']->name;
               }
            }  
        } 
        
        return $result;
        
    }   
    
    public function getAllExamByID($id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.name'; 
        $criteria->compare('t.id', $id);
        $criteria->compare('t.is_deleted', 0);
        //$criteria->compare('t.result_published', 1);
        $criteria->compare('Subjects.no_exams', false);
        $criteria->compare('Subjects.is_deleted', false);
        $criteria->with = array(
                'Exams' => array(
                    'select' => 'Exams.subject_id,Exams.maximum_marks',
                    'with' => array(
                        'Scores' => array(
                            'select' => 'Scores.marks,Scores.student_id',
                            'with' => array(
                                    'Examgrade' => array(
                                        'select' => 'Examgrade.name,Examgrade.credit_points',
                                    ),
                                    'Students' => array(
                                        'select' => 'Students.first_name,Students.middle_name,Students.last_name,Students.class_roll_no',
                                    ),
                             )
                        ),
                        'Subjects' => array(
                            'select' => 'Subjects.name,Subjects.code',
                        )

                    )
                )
        );
        $criteria->order = "Subjects.priority ASC";
        $data = $this->findAll($criteria);
        return $data;
        
    }    





    public function getTermExamsBatch($batch_id, $student_id,$id=0, $no_exams= 0)
    {
        $ar_sid = Yii::app()->db->createCommand()->select('subject_id')->from('students_subjects')->where('student_id = :sid', array(':sid' => $student_id))->queryAll();
        
        $sids = array();
        foreach ($ar_sid as $sid)
        {
            $sids[] = $sid['subject_id'];
        }
        $sids = implode(',', $sids);
        $sids_string ="";
        if($sids)
        {
            $sids_string = "OR Subjects.id IN ($sids)";
        }   
        
        
        
        $criteria = new CDbCriteria();
        $criteria->select = 't.*';
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.batch_id', $batch_id);
        if($id>0)
        {
            $criteria->compare('t.id', $id);
        }
        else
        {
            $criteria->compare('t.exam_category', 3);
        }    
        
        $criteria->compare('t.result_published', 1);
        $criteria->compare('Subjects.batch_id', $batch_id);
        if($no_exams==0)
        {
            $criteria->compare('Subjects.no_exams', false);
        }
        $criteria->compare('Subjects.is_deleted', false);
        $criteria->addCondition("(Subjects.elective_group_id IS NULL OR Subjects.elective_group_id = '' $sids_string )");
        $criteria->together = TRUE;
        $criteria->order = "t.exam_date ASC";
        $data = $this->with("Exams","Acknowledge")->findAll($criteria);
        return $data;
    }
    
    public function getExamCategory($school_id = null, $batch_id = null, $category_id = null) {
        
        $criteria = new CDbCriteria();
        $criteria->select = 't.id, t.name, t.exam_date';
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.school_id', $school_id);
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.exam_category', $category_id);
        $criteria->order = 't.id ASC';
        
        $data = $this->with("Exams")->findAll($criteria);
        $data = $this->formatExamCategory($data);
        
        return (!empty($data)) ? $data : array();
        
    }
    
    public function formatExamCategory($obj_exam_cat) {
        
        $ar_formatted_data = array();
        
        foreach ($obj_exam_cat as $row) {
            $_data['id'] = $row->id;
            $_data['title'] = $row->name;
            if($row->exam_date != "1979-01-01")
            {
                $_data['exam_date'] = $row->exam_date;
            }
            else
            {
                $_data['exam_date'] = "N/A";
            }    
            
            $ar_formatted_data[] = $_data;
        }
        return $ar_formatted_data;
    }

}
