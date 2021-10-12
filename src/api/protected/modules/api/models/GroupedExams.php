<?php

/**
 * This is the model class for table "grouped_exams".
 *
 * The followings are the available columns in table 'grouped_exams':
 * @property integer $id
 * @property integer $exam_group_id
 * @property integer $batch_id
 * @property string $weightage
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class GroupedExams extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'grouped_exams';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('exam_group_id, batch_id, school_id', 'numerical', 'integerOnly'=>true),
			array('weightage', 'length', 'max'=>15),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, exam_group_id, batch_id, weightage, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
                    'examgroup' => array(self::BELONGS_TO, 'ExamGroups', 'exam_group_id'),
                    'examconnect' => array(self::BELONGS_TO, 'ExamConnect', 'connect_exam_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'exam_group_id' => 'Exam Group',
			'batch_id' => 'Batch',
			'weightage' => 'Weightage',
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
		$criteria->compare('exam_group_id',$this->exam_group_id);
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('weightage',$this->weightage,true);
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
	 * @return GroupedExams the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getContinuesResult($batch_id,$connect_exam_id, $previous_exam = 0, $unsolved_exam = 0)
        {
           $students_ids = [];
           $cont_exam = new ExamConnect();
           $connect_exam = $cont_exam->findByPk($connect_exam_id);
           $criteria=new CDbCriteria;
           $criteria->compare('connect_exam_id',$connect_exam_id);
           $criteria->select = 't.*'; 
        
           if($unsolved_exam)
           {
                $criteria->with = array(
                    'examgroup' => array(
                        'select' => 'examgroup.id',
                        'with' => array('Exams' => array(
                                'select' => 'Exams.maximum_marks,Exams.id,Exams.weightage',
                                'with' => array(
                                    'Scores' => array(
                                        'select' => 'Scores.id',
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
                        )
                    ),
                    'examconnect' => array(
                        'select' => "examconnect.id,examconnect.result_type"
                    )   
                );
                $criteria->order = "t.priority ASC,examgroup.created_at ASC";
                $examgroups = $this->findAll($criteria);
                
                if($examgroups)
                {
                    foreach($examgroups as $value)
                    {
                    
                        if(isset($value['examgroup']['Exams']))
                        {
                            
                            foreach($value['examgroup']['Exams'] as $exam)
                            {
                                
                                if(isset($exam['Scores']))
                                {
                                    foreach($exam['Scores'] as $key=>$score)
                                    {
                                        if(isset($score['Students']->id))
                                        {
                                            if(!in_array($score['Students']->id, $students_ids))
                                            {
                                                $students_ids[] = $score['Students']->id; 
                                            }   
                                        }
                                    }      
                                }    
                            }
                            
                        }
                    }
                }
           }
           else
           {
                $criteria->with = array(
                    'examgroup' => array(
                        'select' => 'examgroup.id',
                        'with' => array('Exams' => array(
                                'select' => 'Exams.maximum_marks,Exams.id,Exams.weightage',
                                'with' => array(
                                    'Subjects' => array(
                                        'select' => 'Subjects.id',
                                    )
                                )
                            )
                        )
                    ),
                    'examconnect' => array(
                        'select' => "examconnect.id,examconnect.result_type"
                    )   
                );
                $criteria->order = "t.priority ASC,examgroup.created_at ASC";
                $examgroups = $this->findAll($criteria);
           }

           
            
           
            $subjects_ids = array();
            $exam_ids = array();
            if($examgroups)
            {
                foreach($examgroups as $value)
                {
                   
                    if(isset($value['examgroup']['Exams']))
                    {
                        
                        foreach($value['examgroup']['Exams'] as $exam)
                        {
                            
                            if(isset($exam['Subjects']->id))
                            {
                                if(!in_array($exam['Subjects']->id, $subjects_ids))
                                {
                                    $subjects_ids[] =$exam['Subjects']->id; 
                                }        
                            }    
                        }
                        
                    }
                }
            }
            
            
            
            $subjectObj = new Subjects();
            
            $all_subject_without_no_exam = $subjectObj->getSubject($batch_id,0,$subjects_ids,false,$connect_exam->result_type);
            $subject_no_exam = $subjectObj->getSubjectNoExam($batch_id,0,$subjects_ids,$connect_exam->result_type);
            $results = array();
            
           
            
            
            $examgroups_ids = array();
            if($examgroups)
            {
                $stdobj = new Students();
                $batch_student = $stdobj->getStudentByBatch($batch_id,0,false,$students_ids);
                $batch_student_full = $stdobj->getStudentByBatchFull($batch_id,0,false,$students_ids);
                $examsGroupObj = new ExamGroups();
                
                $first_term_id = 0;
                $final_term_id = 0;
                $i = 0;
                $results['all_result'] = array();
                $subject_result = array();
                $max_mark = array();
                $exam_loop = 0;
                foreach($examgroups as $value)
                {
                    if($value['examgroup']->quarter == 5)
                    {
                        $first_term_id = $value['examgroup']->id;
                    } 
                    if($value['examgroup']->quarter == 6)
                    {
                        $final_term_id = $value['examgroup']->id;
                    }
                    $examgroups_ids[] = $value['examgroup']->id;
                    $results['all_result'] =  $examsGroupObj->getExamGroupResultSubjectAllStudentCont($value['examgroup']->id,$i,$results['all_result'],$value->weightage,$batch_student,$value->priority); 
                    list($subject_result,$max_mark) = $examsGroupObj->getExamGroupResultMaxMark($value['examgroup']->id,$subject_result,$max_mark,$exam_loop,$value['examconnect']->result_type);
                    $exam_loop++;
                   
                    $i++;
                } 
               
                
                
                
                
                $examsObj = new Exams();
                
                //list($subject_result,$max_mark) = $examsObj->getExamGroupResultMaxMarkContinues($examgroups_ids,$subject_result,$max_mark);
              
                
                //$results['all_result'] =  $examsGroupObj->getExamGroupResultSubjectAllStudentContinues($examgroups_ids,$batch_student); 

                
                
                
                $results['exam_comments'] = array();
                
                $cmt_connect = new ExamConnectSubjectComments();
                
                $sub_id_with_exam = array();
                foreach($all_subject_without_no_exam as $value)
                {
                    $sub_id_with_exam[] =$value['id']; 
                }
                
                if($connect_exam->result_type == 1 || $connect_exam->result_type == 10 || $connect_exam->result_type == 11 || $connect_exam->result_type == 16 || $connect_exam->result_type == 17)
                {
                    if($connect_exam->result_type == 1)
                    {
                       $first_term_id_for_class_performance = $cont_exam->getConnectExamByBatch($batch_id,2);
                        
                    }
                    else if($connect_exam->result_type == 17)
                    {
                        $first_term_id_for_class_performance = $cont_exam->getConnectExamByBatch($batch_id,14);
                    }
                    else if($connect_exam->result_type == 10)
                    {
                        $first_term_id_for_class_performance = $cont_exam->getConnectExamByBatch($batch_id,2);
                    }
                    else if($connect_exam->result_type == 16)
                    {
                        $first_term_id_for_class_performance = $cont_exam->getConnectExamByBatch($batch_id,13);
                    }
                    else
                    {
                        $first_term_id_for_class_performance = $cont_exam->getConnectExamByBatch($batch_id,8);
                    }    
                    if(!$first_term_id_for_class_performance)
                    {
                        $first_term_id_for_class_performance = $cont_exam->getConnectExamByBatch($batch_id,15);

                    }
                    if($first_term_id_for_class_performance)
                    {
                        $sub_comments = $cmt_connect->getCommentAllSubjects($first_term_id_for_class_performance,$sub_id_with_exam,$batch_student);
                        foreach($batch_student as $student)
                        {
                           foreach($all_subject_without_no_exam as $value)
                           {
                                $results['exam_comments'][$student]['comments2'][$value['id']]=$sub_comments[$student][$value['id']];
                           } 

                        }
                    }
                }
               
                $sub_comments = $cmt_connect->getCommentAllSubjects($connect_exam_id,$sub_id_with_exam,$batch_student);
                foreach($batch_student as $student)
                {
                   foreach($all_subject_without_no_exam as $value)
                   {
                        $results['exam_comments'][$student]['comments'][$value['id']]=$sub_comments[$student][$value['id']];
                   } 

                }
                   
                $sub_id_without_exam = array();
                $subject_comments_no_exam = array();
                $subject_comments_no_exam_prev = array();
                if($subject_no_exam)
                {
                    foreach($subject_no_exam as $value)
                    {
                        $sub_id_without_exam[] = $value['id'];
                    }
                  
                    $subject_comments_no_exam = $cmt_connect->getCommentAllSubjects($connect_exam_id,$sub_id_without_exam,$batch_student);
                    if($previous_exam)
                    {
                        $subject_comments_no_exam_prev = $cmt_connect->getCommentAllSubjects($previous_exam,$sub_id_without_exam,$batch_student);
                    }
                    
                }
                
                $results['max_mark'] = $max_mark;
                $results['no_exam_comments'] = array();
                
                
                $examGroupObj = new ExamGroups();
                if($connect_exam->result_type==1)
                {
                    $quarter_3_id = $cont_exam->getConnectExamByBatch($batch_id,3);
                    $j = 0;
                    if($subject_no_exam && $quarter_3_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                          
                            $results['quarter_result'][$value['id']]['subject_comment3'] = $cmt_connect->getCommentAll($quarter_3_id,$value['id'],$batch_student);
                            $j++;
                        }    
                    }  
                    
                    $quarter_4_id = $cont_exam->getConnectExamByBatch($batch_id,4);
                    $j = 0;
                    if($subject_no_exam && $quarter_4_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                         
                            $results['quarter_result'][$value['id']]['subject_comment4'] = $cmt_connect->getCommentAll($quarter_4_id,$value['id'],$batch_student);
                            $j++;
                        }    
                    } 
                    
                    $j = 0;
                    if($subject_no_exam && $final_term_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                            $results['quarter_result'][$value['id']]['subject_comment6'] = $examGroupObj->getCommentAll($final_term_id,$value['id'],$batch_student);
                            $j++;
                        }    
                    } 
                    
                }  
                
                if($connect_exam->result_type==2 or $connect_exam->result_type==1)
                {
                    $quarter_1_id = $cont_exam->getConnectExamByBatch($batch_id,4,"1st Quarter");
                    $j = 0;
                    if($subject_no_exam && $quarter_1_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                     
                            $results['quarter_result'][$value['id']]['subject_comment1'] = $cmt_connect->getCommentAll($quarter_1_id,$value['id'],$batch_student);

                            $j++;
                        }    
                    }  
                    
                    $quarter_2_id = $cont_exam->getConnectExamByBatch($batch_id,4,"2nd Quarter");
                    $j = 0;
                    if($subject_no_exam && $quarter_2_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                            
                            $results['quarter_result'][$value['id']]['subject_comment2'] = $cmt_connect->getCommentAll($quarter_2_id,$value['id'],$batch_student);

                            $j++;
                        }    
                    }  
                    $j = 0;
                    if($subject_no_exam && $first_term_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                          
                            $results['quarter_result'][$value['id']]['subject_comment5'] = $examGroupObj->getCommentAll($first_term_id,$value['id'],$batch_student);
                            $j++;
                        }    
                    } 
                    
                } 
                
               
                if($subject_no_exam)
                {
                    foreach($batch_student as $student)
                    {
                        $j = 0;
                        foreach($subject_no_exam as $value)
                        {
                            $results['no_exam_comments'][$student]['no_exam_subject_resutl'][$j]['id'] =  $value['id'];
                            $results['no_exam_comments'][$student]['no_exam_subject_resutl'][$j]['code'] =  $value['code'];
                            $results['no_exam_comments'][$student]['no_exam_subject_resutl'][$j]['subject_name'] =  $value['name'];
                            $results['no_exam_comments'][$student]['no_exam_subject_resutl'][$j]['subject_comment'] = $subject_comments_no_exam[$student][$value['id']];
                            if($previous_exam)
                            {
                                $results['no_exam_comments'][$student]['no_exam_subject_resutl'][$j]['prev_subject_comment'] = $subject_comments_no_exam_prev[$student][$value['id']];
                            }
                            $j++;
                        }  
                    }
                }
                $results['students'] = $batch_student_full;
                $results['subjects'] = $all_subject_without_no_exam;
                return $results;
            }
        }
        
        
        public function getTabulation($batch_id,$connect_exam_id,$unsolved_exam = 0)
        {
            $students_ids = [];
            $criteria=new CDbCriteria;
            $criteria->compare('connect_exam_id',$connect_exam_id);
                
            
            $criteria->compare('examgroup.is_deleted', 0);
            $criteria->compare('examconnect.is_deleted', 0);
            $criteria->select = 't.*'; 

            if($unsolved_exam)
            {
                    $criteria->with = array(
                        'examgroup' => array(
                            'select' => 'examgroup.id,examgroup.quarter',
                            'with' => array('Exams' => array(
                                    'select' => 'Exams.maximum_marks,Exams.id,Exams.weightage',
                                    'with' => array(
                                        'Scores' => array(
                                            'select' => 'Scores.id',
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
                            )
                        ),
                        'examconnect' => array(
                            'select' => "examconnect.id,examconnect.result_type"
                        )   
                    );
                    $criteria->order = "t.priority ASC,examgroup.created_at ASC";
                    $examgroups = $this->findAll($criteria);
                    
                    if($examgroups)
                    {
                        foreach($examgroups as $value)
                        {
                        
                            if(isset($value['examgroup']['Exams']))
                            {
                                
                                foreach($value['examgroup']['Exams'] as $exam)
                                {
                                    
                                    if(isset($exam['Scores']))
                                    {
                                        foreach($exam['Scores'] as $key=>$score)
                                        {
                                            if(isset($score['Students']->id))
                                            {
                                                if(!in_array($score['Students']->id, $students_ids))
                                                {
                                                    $students_ids[] = $score['Students']->id; 
                                                }   
                                            }
                                        }      
                                    }    
                                }
                                
                            }
                        }
                    }
            }
            else
            {
                    $criteria->with = array(
                        'examgroup' => array(
                            'select' => 'examgroup.id,examgroup.quarter',
                            'with' => array('Exams' => array(
                                    'select' => 'Exams.maximum_marks,Exams.id,Exams.weightage',
                                    'with' => array(
                                        'Subjects' => array(
                                            'select' => 'Subjects.id',
                                        )
                                    )
                                )
                            )
                        ),
                        'examconnect' => array(
                            'select' => "examconnect.id,examconnect.result_type"
                        )   
                    );
                    $criteria->order = "t.priority ASC,examgroup.created_at ASC";
                    $examgroups = $this->findAll($criteria);
            }

            
            
            $subjects_ids = array();
            $exam_ids = array();
            if($examgroups)
            {
                foreach($examgroups as $value)
                {
                   
                    if(isset($value['examgroup']['Exams']))
                    {
                        
                        foreach($value['examgroup']['Exams'] as $exam)
                        {
                            
                            if(isset($exam['Subjects']->id))
                            {
                                if(!in_array($exam['Subjects']->id, $subjects_ids))
                                {
                                    $subjects_ids[] =$exam['Subjects']->id; 
                                }        
                            }    
                        }
                        
                    }
                }
            }
            
            
            
            $subjectObj = new Subjects();
            
            $all_subject_without_no_exam = $subjectObj->getSubject($batch_id,0,$subjects_ids);
            $subject_no_exam = $subjectObj->getSubjectNoExam($batch_id,0,$subjects_ids);
            $results = array();
            
            $subject_result = array();
            $max_mark = array();
            if($examgroups)
            {
                $stdobj = new Students();
                $batch_student = $stdobj->getStudentByBatch($batch_id,0,false,$students_ids);
                $batch_student_full = $stdobj->getStudentByBatchFull($batch_id,0,false,$students_ids);
                $results['students'] = $batch_student_full;
                $examsGroupObj = new ExamGroups();
                $results['subjects'] = $all_subject_without_no_exam;
              
                $cont_exam = new ExamConnect();
                $connect_exam = $cont_exam->findByPk($connect_exam_id);
                $first_term_id = 0;
                $final_term_id = 0;
                $all_exam_group_id = array();
                foreach($examgroups as $value)
                {
                    $all_exam_group_id[] = $value['examgroup']->id;
                    if($value['examgroup']->quarter == 5)
                    {
                        $first_term_id = $value['examgroup']->id;
                    } 
                    if($value['examgroup']->quarter == 6)
                    {
                        $final_term_id = $value['examgroup']->id;
                    }
                    $result_main =  $examsGroupObj->getExamGroupResultSubjectAllStudent($value['examgroup']->id,$value->weightage,$batch_student,$value->priority);    
                    if($result_main)
                    {
                        $results['exams'][] = $result_main;
                    }

                }
                
                if(Yii::app()->user->schoolId == 280 || Yii::app()->user->schoolId == 325 || Yii::app()->user->schoolId == 342)
                {
                    $scoreObj = new ExamScores();
                    $results['students'] = $scoreObj->getrankedStudents($all_exam_group_id);
                }
                
                
                $results['comments'] = array();
                
                $cmt_connect = new ExamConnectSubjectComments();
                foreach($all_subject_without_no_exam as $value)
                {
                  
                       $subject_comment = $cmt_connect->getCommentAll($connect_exam_id,$value['id'],$batch_student);
                       $results['comments'][$value['id']] = $subject_comment;
                     
                }    
                
                
                $results['no_exam_subject_resutl'] = array();
                
                
                $examGroupObj = new ExamGroups();
                if($connect_exam->result_type==1)
                {
                    $quarter_3_id = $cont_exam->getConnectExamByBatch($batch_id,3);
                    $j = 0;
                    if($subject_no_exam && $quarter_3_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                          
                            $results['no_exam_subject_resutl'][$j]['subject_comment3'] = $cmt_connect->getCommentAll($quarter_3_id,$value['id'],$batch_student);
                            $j++;
                        }    
                    }  
                    
                    $quarter_4_id = $cont_exam->getConnectExamByBatch($batch_id,4);
                    $j = 0;
                    if($subject_no_exam && $quarter_4_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                         
                            $results['no_exam_subject_resutl'][$j]['subject_comment4'] = $cmt_connect->getCommentAll($quarter_4_id,$value['id'],$batch_student);
                            $j++;
                        }    
                    } 
                    
                    $j = 0;
                    if($subject_no_exam && $final_term_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                            $results['no_exam_subject_resutl'][$j]['subject_comment6'] = $examGroupObj->getCommentAll($final_term_id,$value['id'],$batch_student);
                            $j++;
                        }    
                    } 
                    
                }  
                
                if($connect_exam->result_type==2)
                {
                    $quarter_1_id = $cont_exam->getConnectExamByBatch($batch_id,4,"1st Quarter");
                    $j = 0;
                    if($subject_no_exam && $quarter_1_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                     
                            $results['no_exam_subject_resutl'][$j]['subject_comment1'] = $cmt_connect->getCommentAll($quarter_1_id,$value['id'],$batch_student);

                            $j++;
                        }    
                    }  
                    
                    $quarter_2_id = $cont_exam->getConnectExamByBatch($batch_id,4,"2nd Quarter");
                    $j = 0;
                    if($subject_no_exam && $quarter_2_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                            
                            $results['no_exam_subject_resutl'][$j]['subject_comment2'] = $cmt_connect->getCommentAll($quarter_2_id,$value['id'],$batch_student);

                            $j++;
                        }    
                    }  
                    $j = 0;
                    if($subject_no_exam && $first_term_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                          
                            $results['no_exam_subject_resutl'][$j]['subject_comment5'] = $examGroupObj->getCommentAll($first_term_id,$value['id'],$batch_student);
                            $j++;
                        }    
                    } 
                    
                } 
                
                
                
                $results['max_mark'] = $max_mark;
                
                $j = 0;
                if($subject_no_exam)
                {
                    foreach($subject_no_exam as $value)
                    {
                        $results['no_exam_subject_resutl'][$j]['id'] =  $value['id'];
                        $results['no_exam_subject_resutl'][$j]['code'] =  $value['code'];
                        $results['no_exam_subject_resutl'][$j]['subject_name'] =  $value['name'];
                        $results['no_exam_subject_resutl'][$j]['subject_comment'] = $cmt_connect->getCommentAll($connect_exam_id,$value['id'],$batch_student);
                        
                        $j++;
                    }    
                }    
            }
            return $results;
        }        
        
        public function getGroupedExamReport($batch_id, $student_id, $connect_exam_id, $previous_exam)
        {
            $exam_connect_obj = new ExamConnect();
            $exm_connect_data = $exam_connect_obj->findByPk($connect_exam_id);
            
            $send_no_exam = false;
           
                    
            $criteria=new CDbCriteria;
            $criteria->compare('connect_exam_id',$connect_exam_id);
            $criteria->compare('examgroup.is_deleted', 0);
            $criteria->compare('examconnect.is_deleted', 0);
            $criteria->select = 't.*'; 
            $criteria->with = array(
                'examgroup' => array(
                    'select' => 'examgroup.id,examgroup.quarter',
                    'with' => array('Exams' => array(
                            'select' => 'Exams.maximum_marks,Exams.id,Exams.weightage',
                            'with' => array(
                                'Subjects' => array(
                                    'select' => 'Subjects.id',
                                )
                            )
                        )
                    )
                ),
                'examconnect' => array(
                    'select' => "examconnect.id,examconnect.result_type"
                 )
                );
            $criteria->order = "t.priority ASC,examgroup.created_at ASC";
            $examgroups = $this->findAll($criteria);
            
            $subjects_ids = array();
            if($examgroups)
            {
                foreach($examgroups as $value)
                {
                   
                    if(isset($value['examgroup']['Exams']))
                    {
                        
                        foreach($value['examgroup']['Exams'] as $exam)
                        {
                            
                            if(isset($exam['Subjects']->id))
                            {
                                if(!in_array($exam['Subjects']->id, $subjects_ids))
                                {
                                    $subjects_ids[] =$exam['Subjects']->id; 
                                }        
                            }    
                        }
                        
                    }
                }
            }
            
            
            
            $subjectObj = new Subjects();
            
            $all_subject_without_no_exam = $subjectObj->getSubject($batch_id,$student_id,$subjects_ids);
            $subject_no_exam = $subjectObj->getSubjectNoExam($batch_id,$student_id,$subjects_ids);
            
            $results = array();
            
            $subject_result = array();
            $max_mark = array();
            if($examgroups)
            {
                $examsGroupObj = new ExamGroups();
                $results['subjects'] = $all_subject_without_no_exam;
                $exam_loop = 0;
                foreach($examgroups as $value)
                {
                    $result_main =  $examsGroupObj->getExamGroupResultSubject($value['examgroup']->id,$student_id,$value->weightage,$value->priority,$send_no_exam);    
                    if($result_main)
                    {
                        $results['exams'][] = $result_main;
                    }
                    list($subject_result,$max_mark) = $examsGroupObj->getExamGroupResultMaxMark($value['examgroup']->id,$subject_result,$max_mark,$exam_loop,$value['examconnect']->result_type);
                    $exam_loop++;
                }
                $results['comments'] = array();
                
                $cmt_connect = new ExamConnectSubjectComments();
                foreach($all_subject_without_no_exam as $value)
                {
                  
                       $subject_comment = $cmt_connect->getComment($connect_exam_id,$student_id,$value['id']);
                       $results['comments'][$value['id']] = $subject_comment;
                     
                } 
                $results['comments2'] = array();
                $cont_exam = new ExamConnect();
                if($exm_connect_data->result_type==1 || $exm_connect_data->result_type == 10 || $exm_connect_data->result_type == 11 || $exm_connect_data->result_type == 16 || $exm_connect_data->result_type == 17)
                {
                    if($exm_connect_data->result_type == 1)
                    {
                       $first_term_id_for_class_performance = $cont_exam->getConnectExamByBatch($batch_id,2);
                        
                    }
                    else if($exm_connect_data->result_type == 17)
                    {
                        $first_term_id_for_class_performance = $cont_exam->getConnectExamByBatch($batch_id,14);
                    }
                    else if($exm_connect_data->result_type == 10)
                    {
                        $first_term_id_for_class_performance = $cont_exam->getConnectExamByBatch($batch_id,2);
                    }
                    else if($exm_connect_data->result_type == 16)
                    {
                        $first_term_id_for_class_performance = $cont_exam->getConnectExamByBatch($batch_id,13);
                    }
                    else
                    {
                        $first_term_id_for_class_performance = $cont_exam->getConnectExamByBatch($batch_id,8);
                    }    
                    if(!$first_term_id_for_class_performance)
                    {
                        $first_term_id_for_class_performance = $cont_exam->getConnectExamByBatch($batch_id,15);
                    }
                
                    //$first_term_id_for_class_performance = $exam_connect_obj->getConnectExamByBatch($batch_id,2);
                    if($first_term_id_for_class_performance)
                    {
                        foreach($all_subject_without_no_exam as $value)
                        {
                             $subject_comment = $cmt_connect->getComment($first_term_id_for_class_performance,$student_id,$value['id']);
                             $results['comments2'][$value['id']] = $subject_comment;
                        } 

                    }
                }
                
                
                $results['no_exam_subject_resutl'] = array();
                
                $results['no_exam_subject_resutl_previous_exam'] = array();
                
                $results['max_mark'] = $max_mark;
                
                $j = 0;
                if($subject_no_exam)
                {
                    foreach($subject_no_exam as $value)
                    {
                        $results['no_exam_subject_resutl'][$j]['id'] =  $value['id'];
                        $results['no_exam_subject_resutl'][$j]['subject_name'] =  $value['name'];
                        $results['no_exam_subject_resutl'][$j]['subject_comment'] = $cmt_connect->getComment($connect_exam_id,$student_id,$value['id']);
                        
                        if($previous_exam)
                        {
                            $results['no_exam_subject_resutl'][$j]['prev_subject_comment'] = $cmt_connect->getComment($previous_exam,$student_id,$value['id']);
                        }
                        
                        $j++;
                    }    
                }    
            }
            return $results;
        }  
}
