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
        public function getContinuesResult($batch_id,$connect_exam_id, $previous_exam = 0)
        {
           $criteria=new CDbCriteria;
           $criteria->compare('connect_exam_id',$connect_exam_id);
           $criteria->compare('examgroup.is_deleted', 0);
           $criteria->compare('examconnect.is_deleted', 0);
           $criteria->select = 't.*'; 
           $criteria->with = array(
                'examgroup' => array(
                    'select' => 'examgroup.id',
                    'with' => array('Exams' => array(
                            'select' => 'Exams.maximum_marks,Exams.id',
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
            
           
            
            
            $examgroups_ids = array();
            if($examgroups)
            {
                $stdobj = new Students();
                $batch_student = $stdobj->getStudentByBatch($batch_id);
                $batch_student_full = $stdobj->getStudentByBatchFull($batch_id);
                $examsGroupObj = new ExamGroups();
                $cont_exam = new ExamConnect();
                $connect_exam = $cont_exam->findByPk($connect_exam_id);
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
                    $results['all_result'] =  $examsGroupObj->getExamGroupResultSubjectAllStudentCont($value['examgroup']->id,$i,$results['all_result'],$value->weightage,$batch_student); 
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
                    $quarter_1_id = $cont_exam->getConnectExamByBatch($batch_id,1);
                    $j = 0;
                    if($subject_no_exam && $quarter_1_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                     
                            $results['no_exam_subject_resutl'][$j]['subject_comment1'] = $cmt_connect->getCommentAll($quarter_1_id,$value['id'],$batch_student);

                            $j++;
                        }    
                    }  
                    
                    $quarter_2_id = $cont_exam->getConnectExamByBatch($batch_id,2);
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
        
        
        public function getTabulation($batch_id,$connect_exam_id,$condition_in = false)
        {
            $criteria=new CDbCriteria;
            $criteria->compare('connect_exam_id',$connect_exam_id);
                
            
            $criteria->compare('examgroup.is_deleted', 0);
            $criteria->compare('examconnect.is_deleted', 0);
            $criteria->select = 't.*'; 
            $criteria->with = array(
                'examgroup' => array(
                    'select' => 'examgroup.id,examgroup.quarter',
                    'with' => array('Exams' => array(
                            'select' => 'Exams.maximum_marks,Exams.id',
                            'with' => array(
                                'Subjects' => array(
                                    'select' => 'Subjects.id',
                                )
                            )
                        )
                    )
                ),
                'examconnect' => array(
                    'select' => "examconnect.id"
                 )
            );
            $criteria->order = "t.priority ASC,examgroup.created_at ASC";
            $examgroups = $this->findAll($criteria);
            
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
                $batch_student = $stdobj->getStudentByBatch($batch_id);
                $batch_student_full = $stdobj->getStudentByBatchFull($batch_id);
                $results['students'] = $batch_student_full;
                $examsGroupObj = new ExamGroups();
                $results['subjects'] = $all_subject_without_no_exam;
              
                $cont_exam = new ExamConnect();
                $connect_exam = $cont_exam->findByPk($connect_exam_id);
                $first_term_id = 0;
                $final_term_id = 0;
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
                    $result_main =  $examsGroupObj->getExamGroupResultSubjectAllStudent($value['examgroup']->id,$value->weightage,$batch_student);    
                    if($result_main)
                    {
                        $results['exams'][] = $result_main;
                    }

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
                    $quarter_1_id = $cont_exam->getConnectExamByBatch($batch_id,1);
                    $j = 0;
                    if($subject_no_exam && $quarter_1_id)
                    {
                        foreach($subject_no_exam as $value)
                        {
                     
                            $results['no_exam_subject_resutl'][$j]['subject_comment1'] = $cmt_connect->getCommentAll($quarter_1_id,$value['id'],$batch_student);

                            $j++;
                        }    
                    }  
                    
                    $quarter_2_id = $cont_exam->getConnectExamByBatch($batch_id,2);
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
            $criteria=new CDbCriteria;
            $criteria->compare('connect_exam_id',$connect_exam_id);
            $criteria->compare('examgroup.is_deleted', 0);
            $criteria->compare('examconnect.is_deleted', 0);
            $criteria->select = 't.*'; 
            $criteria->with = array(
                'examgroup' => array(
                    'select' => 'examgroup.id',
                    ),
                'examconnect' => array(
                    'select' => "examconnect.id,examconnect.result_type"
                 )
                );
            $criteria->order = "t.priority ASC,examgroup.created_at ASC";
            $examgroups = $this->findAll($criteria);
            
            $subjectObj = new Subjects();
            
            $all_subject_without_no_exam = $subjectObj->getSubject($batch_id, $student_id);
            $subject_no_exam = $subjectObj->getSubjectNoExam($batch_id, $student_id);
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
                    $result_main =  $examsGroupObj->getExamGroupResultSubject($value['examgroup']->id,$student_id,$value->weightage);    
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
