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
    
    public function getSubject($batch_id,$student_id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.name,t.id';
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.is_deleted', 0);
        $criteria->order = "t.name desc";
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
                $i++;
            }    
        } 
        if($student_subject)
        {
            foreach($student_subject as $value)
            {
                $subject_array[$i]['name'] = $value->name;
                $subject_array[$i]['id'] = $value->id;
                $i++;
            }    
        } 
        return $subject_array;
    }        

    public function getTermReport($batch_id, $student_id, $id = 0)
    {

        $examGroup = new ExamGroups();
        $exams = $examGroup->getTermExamsBatch($batch_id,$student_id,$id);
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
                $report_term_merge['exam_subjects'][$i]['subject_name'] = $examvalue['Subjects']->name;
                $report_term_merge['exam_subjects'][$i]['subject_code'] = $examvalue['Subjects']->code;
                $report_term_merge['exam_subjects'][$i]['subject_id']   = $examvalue['Subjects']->id;
                $report_term_merge['exam_subjects'][$i]['subject_icon'] = $examvalue['Subjects']->icon_number;
                $cradit_hours = 100;
                if($examvalue['Subjects']->credit_hours)
                {
                    $cradit_hours = $examvalue['Subjects']->credit_hours;
                    $total_cradit_hours = $total_cradit_hours+$examvalue['Subjects']->credit_hours;
                }
                else
                {
                     $total_cradit_hours = $total_cradit_hours+100;
                }    
                $report_term_merge['exam_subjects'][$i]['cradit_hours'] = $examvalue['Subjects']->credit_hours;
                
                $report_term_merge['exam_subjects'][$i]['total_cradit_hours'] = $total_cradit_hours;
               
               
                $examScore = new ExamScores();
                $student_result = $examScore->getSingleExamStudentResult($student_id, $examvalue->id);
                $max_mark = $examScore->getExamStudentMaxMark($examvalue->id);
                $report_term_merge['exam_subjects'][$i]['your_grade']    = "-";
                $report_term_merge['exam_subjects'][$i]['your_mark']     = "-";
                $report_term_merge['exam_subjects'][$i]['your_percent']  = "-";
                $report_term_merge['exam_subjects'][$i]['credit_points'] = "-";
                
                if($student_result)
                {
                    if($student_result['Examgrade'])
                    $report_term_merge['exam_subjects'][$i]['your_grade']   = $student_result['Examgrade']->name;
                    $report_term_merge['exam_subjects'][$i]['your_mark']    = $student_result->marks;
                    if($student_result['Examgrade'])
                    $report_term_merge['exam_subjects'][$i]['credit_points']= $student_result['Examgrade']->credit_points;
                    
                    $report_term_merge['exam_subjects'][$i]['your_percent'] = ($student_result->marks / $examvalue->maximum_marks) * 100;
                    
                    $report_term_merge['exam_subjects'][$i]['your_percent'] = intval($report_term_merge['exam_subjects'][$i]['your_percent']);
                    $total_mark = $total_mark+$student_result->marks;
                    if($student_result['Examgrade'])
                    {
                        $total_grade_point = $total_grade_point+($student_result['Examgrade']->credit_points*$cradit_hours);
                        $only_grade_point = $only_grade_point+$student_result['Examgrade']->credit_points;
                    }
                   
                    
                    $report_term_merge['exam_subjects'][$i]['remarks'] = $student_result->remarks;
                }
                $report_term_merge['exam_subjects'][$i]['max_mark']   = $max_mark;
                $report_term_merge['exam_subjects'][$i]['total_mark'] = $examvalue->maximum_marks;
                $i++;
                
           }
           if($total_grade_point && $total_grade_point>0)
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

                    if(isset($student_result['Examgrade']->name))
                    $progress['exam'][$i]['grade_point'] = $student_result['Examgrade']->credit_points;


                    $progress['exam'][$i]['your_mark'] = $student_result->marks;
                  
                    $progress['exam'][$i]['your_percent'] = ($student_result->marks / $exam_details->maximum_marks) * 100;

                    $progress['exam'][$i]['your_percent'] = intval($progress['exam'][$i]['your_percent']);
                    
                    $total_mark = $total_mark+$progress['exam'][$i]['your_percent'];
                    
                    $progress['exam'][$i]['max_mark'] = $max_mark;
                    $progress['exam'][$i]['max_mark_percent'] = ($max_mark/ $exam_details->maximum_marks) * 100;
                    $progress['exam'][$i]['max_mark_percent'] = intval($progress['exam'][$i]['max_mark_percent']);
                    
                    $progress['exam'][$i]['avg_mark'] = $avg_mark;
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
   
    
    public function getBatchSubjectClassTestProjectReport($batch_id, $student_id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.*';
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.no_exams', false);
        $criteria->compare('t.is_deleted', false);
        $data_subject = $this->findAll($criteria);



        $report_class_test = array();
        foreach ($data_subject as $value)
        {

            $examModel = new Exams();
            $exam_details_all = $examModel->getPublishClassTestProjectSubjectWise($value->id, $batch_id, false);
            
            $report_class_test_merge['subject_name'] = $value->name;
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
                            $report_class_test_merge['subject_exam']['class_test'][$i]['exam_date'] = DATE("Y-m-d", strtotime($exam_details->start_time));
                            
                            $report_class_test_merge['subject_exam']['class_test'][$i]['your_grade'] = "-";
                            $report_class_test_merge['subject_exam']['class_test'][$i]['grade_point'] = "-";
                            
                            if(isset($student_result['Examgrade']->name))
                            $report_class_test_merge['subject_exam']['class_test'][$i]['your_grade'] = $student_result['Examgrade']->name;
                            
                            if(isset($student_result['Examgrade']->name))
                            $report_class_test_merge['subject_exam']['class_test'][$i]['grade_point'] = $student_result['Examgrade']->credit_points;
                            
                            
                            $report_class_test_merge['subject_exam']['class_test'][$i]['your_mark'] = $student_result->marks;
                            $report_class_test_merge['subject_exam']['class_test'][$i]['your_percent'] = ($student_result->marks / $exam_details->maximum_marks) * 100;

                            $report_class_test_merge['subject_exam']['class_test'][$i]['your_percent'] = intval($report_class_test_merge['subject_exam']['class_test'][$i]['your_percent']);
                            $report_class_test_merge['subject_exam']['class_test'][$i]['topic'] = $exam_details['Examgroup']->topic;;
                            $report_class_test_merge['subject_exam']['class_test'][$i]['max_mark'] = $max_mark;
                            $report_class_test_merge['subject_exam']['class_test'][$i]['category'] = $exam_details['Examgroup']->exam_category;
                            $report_class_test_merge['subject_exam']['class_test'][$i]['total_mark'] = $exam_details->maximum_marks;
                        
                            $i++;
                        }
                        else
                        {
                            $report_class_test_merge['subject_exam']['project'][$j]['exam_id'] = $exam_details->id;
                            $report_class_test_merge['subject_exam']['project'][$j]['exam_name'] = $exam_details['Examgroup']->name;
                            $report_class_test_merge['subject_exam']['project'][$j]['exam_date'] = DATE("Y-m-d", strtotime($exam_details->start_time));
                            
                            $report_class_test_merge['subject_exam']['project'][$i]['your_grade'] = "-";
                            $report_class_test_merge['subject_exam']['project'][$i]['grade_point'] = "-";
                            
                            if(isset($student_result['Examgrade']->name))
                            $report_class_test_merge['subject_exam']['project'][$i]['your_grade'] = $student_result['Examgrade']->name;
                            
                            if(isset($student_result['Examgrade']->name))
                            $report_class_test_merge['subject_exam']['project'][$i]['grade_point'] = $student_result['Examgrade']->credit_points;
                            
                            
                            
                            $report_class_test_merge['subject_exam']['project'][$j]['your_mark'] = $student_result->marks;
                            $report_class_test_merge['subject_exam']['project'][$j]['your_percent'] = ($student_result->marks / $exam_details->maximum_marks) * 100;

                            $report_class_test_merge['subject_exam']['project'][$j]['your_percent'] = intval($report_class_test_merge['subject_exam']['project'][$j]['your_percent']);
                            $report_class_test_merge['subject_exam']['project'][$j]['topic'] = $exam_details['Examgroup']->topic;;
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
