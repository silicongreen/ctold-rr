<?php

/**
 * This is the model class for table "exams".
 *
 * The followings are the available columns in table 'exams':
 * @property integer $id
 * @property integer $exam_group_id
 * @property integer $subject_id
 * @property string $start_time
 * @property string $end_time
 * @property string $maximum_marks
 * @property string $minimum_marks
 * @property integer $grading_level_id
 * @property integer $weightage
 * @property integer $event_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Exams extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public $total_score;
    public $total_points;
    public $std_score;

    public function tableName()
    {
        return 'exams';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('exam_group_id, subject_id, grading_level_id, weightage, event_id, school_id', 'numerical', 'integerOnly' => true),
            array('maximum_marks, minimum_marks', 'length', 'max' => 10),
            array('start_time, end_time, created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, exam_group_id, subject_id, start_time, end_time, maximum_marks, minimum_marks, grading_level_id, weightage, event_id, created_at, updated_at, school_id', 'safe', 'on' => 'search'),
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
            'Examgroup' => array(self::BELONGS_TO, 'ExamGroups', 'exam_group_id',
                'joinType' => 'INNER JOIN',
            ),
            'Subjects' => array(self::BELONGS_TO, 'Subjects', 'subject_id',
                'joinType' => 'INNER JOIN',
            ),
            'Scores' => array(self::HAS_MANY, 'ExamScores', 'exam_id',
                'joinType' => 'LEFT JOIN',
                'with' => array('Examgrade'),
            ),
            'studentSubject' => array(self::BELONGS_TO, 'StudentsSubjects', '',
                'on' => 'studentSubject.subject_id = t.subject_id',
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
            'exam_group_id' => 'Exam Group',
            'subject_id' => 'Subject',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'maximum_marks' => 'Maximum Marks',
            'minimum_marks' => 'Minimum Marks',
            'grading_level_id' => 'Grading Level',
            'weightage' => 'Weightage',
            'event_id' => 'Event',
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
        $criteria->compare('exam_group_id', $this->exam_group_id);
        $criteria->compare('subject_id', $this->subject_id);
        $criteria->compare('start_time', $this->start_time, true);
        $criteria->compare('end_time', $this->end_time, true);
        $criteria->compare('maximum_marks', $this->maximum_marks, true);
        $criteria->compare('minimum_marks', $this->minimum_marks, true);
        $criteria->compare('grading_level_id', $this->grading_level_id);
        $criteria->compare('weightage', $this->weightage);
        $criteria->compare('event_id', $this->event_id);
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
     * @return Exams the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getPercentile($exam_group_id, $student_mark, $subject_id)
    {
        $sql = "SELECT  Scores.marks AS std_score
        FROM exams
        LEFT JOIN exam_scores AS Scores ON Scores.exam_id = exams.id
        LEFT OUTER JOIN grading_levels AS Examgrade ON Scores.grading_level_id = Examgrade.id 
        WHERE exams.exam_group_id = $exam_group_id
        AND exams.subject_id = $subject_id
        GROUP BY Scores.student_id
        ORDER BY std_score DESC";

        $data = $this->findAllBySql($sql);




        $i = 1;
        $j = 0;
        foreach ($data as $value)
        {

            if ($student_mark == $value->std_score)
            {
                $j = $i;
            }
            $i++;
        }
        if ($j == 0)
        {
            $j = $i;
        }


        $percentile = (100 * $j) / $i;
        return $percentile;
    }
    public function getrankedStudents($exam_group_ids)
    {
        $criteria = new CDbCriteria();
        $criteria->together = true;
        $criteria->select = 't.id';
        $criteria->addInCondition('t.exam_group_id',$exam_group_ids); 
        
        $criteria->with = array(
                'Scores' => array(
                    'select' => 'SUM(Scores.marks) AS total_score',
                    'with' => array(
                        'Students' => array(
                            'select' => 'Students.*',
                            
                        )

                    )
                )
        );
        
        $criteria->group = 'Scores.student_id';
        $criteria->order = 'total_score DESC';
        $students_ranked = $this->find($criteria);
        
        $students = array();
        foreach ($students_ranked as $value)
        {
           $students[] = $value;
            
        }
        return $students;
    }        

    public function getPositionConnectExam($exam_group_ids, $total_grade_point, $total_mark)
    {
        $sql = "SELECT SUM( Scores.marks ) AS total_score
        FROM exams
        LEFT JOIN exam_scores AS Scores ON Scores.exam_id = exams.id
        WHERE exams.exam_group_id IN (" . $exam_group_ids . ")
        GROUP BY Scores.student_id
        ORDER BY total_score DESC";

        $data = $this->findAllBySql($sql);


        $i = 1;
        foreach ($data as $value)
        {
            if ($total_grade_point == $value->total_points && $total_mark == $value->total_score)
            {
                break;
            }
            $i++;
        }
        if (Yii::app()->user->schoolId == "280")
        {
            return 0;
        } else
        {
            return $i;
        }
    }

    public function getPosition($exam_group_id, $total_grade_point, $total_mark)
    {
        $sql = "SELECT SUM( Scores.marks ) AS total_score, SUM( Examgrade.credit_points ) AS total_points
        FROM exams
        LEFT JOIN exam_scores AS Scores ON Scores.exam_id = exams.id
        LEFT OUTER JOIN grading_levels AS Examgrade ON Scores.grading_level_id = Examgrade.id 
        WHERE exams.exam_group_id = $exam_group_id
        GROUP BY Scores.student_id
        ORDER BY total_points DESC, total_score DESC";

        $data = $this->findAllBySql($sql);


        $i = 1;
        foreach ($data as $value)
        {
            if ($total_grade_point == $value->total_points && $total_mark == $value->total_score)
            {
                break;
            }
            $i++;
        }
        if (Yii::app()->user->schoolId == "280")
        {
            return 0;
        } else
        {
            return $i;
        }
    }

    public function getExamSubject($subject_id, $exam_group_id = 0, $exam_id = 0)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.*';
        if ($exam_group_id)
        {
            $criteria->compare('t.exam_group_id', $exam_group_id);
        }
        if ($exam_id)
        {
            $criteria->compare('t.id', $exam_id);
        }
        $criteria->compare('t.subject_id', $subject_id);
        $data = $this->with("Examgroup")->find($criteria);
        return $data;
    }

    public function getPublishExam($subject_id, $batch_id, $exam_category = 0)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.*';
        $criteria->compare('Examgroup.batch_id', $batch_id);
        if ($exam_category)
        {
            $criteria->compare('Examgroup.exam_category', $exam_category);
        }

        $criteria->compare('Examgroup.result_published', 1);
        $criteria->compare('Examgroup.is_deleted', 0);
        $criteria->compare('t.subject_id', $subject_id);
        $criteria->order = "Examgroup.exam_date ASC";
        $data = $this->with("Examgroup")->findAll($criteria);
        return $data;
    }

    public function getPublishClassTestProjectSubjectWise($subject_id, $batch_id, $limit = true, $exam_group = 0)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.*';
        $criteria->compare('Examgroup.batch_id', $batch_id);
        $criteria->compare('Examgroup.is_deleted', 0);
        $criteria->compare('Examgroup.exam_category', 1);
        $criteria->compare('Examgroup.exam_category', 2, false, "OR");
        if ($exam_group > 0)
        {
            $criteria->compare('Examgroup.id', $exam_group);
        }

        $criteria->compare('Examgroup.result_published', 1);
        $criteria->compare('t.subject_id', $subject_id);
        $criteria->order = "start_time DESC";
        if ($limit)
        {
            $criteria->limit = 1;
            $data = $this->with("Examgroup")->find($criteria);
        } else
        {
            $data = $this->with("Examgroup")->findAll($criteria);
        }

        return $data;
    }

    public function getTeacherExam($limit = 10, $no_exams = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.start_time, t.end_time, t.no_date';
        $criteria->compare('Examgroup.is_deleted', 0);
        $criteria->together = true;

        if (Yii::app()->user->isAdmin)
        {
            $criteria->with = array(
                'Subjects' => array(
                    'select' => 'Subjects.name,Subjects.icon_number,Subjects.no_exams',
                    'with' => array(
                        'Subjectbatch' => array(
                            'select' => 'Subjectbatch.name',
                            'with' => array(
                                'courseDetails' => array(
                                    'select' => 'courseDetails.course_name,courseDetails.section_name',
                                ),
                            )
                        )
                    )
                ),
                'Examgroup' => array(
                    'select' => 'Examgroup.name',
                ),
                'studentSubject' => array(
                    'select' => 'studentSubject.id',
                ),
            );
            $criteria->compare('t.school_id', Yii::app()->user->schoolId);
        } else
        {
            $criteria->with = array(
                'Subjects' => array(
                    'select' => 'Subjects.name,Subjects.icon_number,Subjects.no_exams',
                    'with' => array(
                        'Subjectbatch' => array(
                            'select' => 'Subjectbatch.name',
                            'with' => array(
                                'courseDetails' => array(
                                    'select' => 'courseDetails.course_name,courseDetails.section_name',
                                ),
                            )
                        ),
                        'employee' => array(
                            'select' => ''
                        )
                    )
                ),
                'Examgroup' => array(
                    'select' => 'Examgroup.name',
                ),
                'studentSubject' => array(
                    'select' => 'studentSubject.id',
                ),
            );
            $criteria->compare("employee.employee_id", Yii::app()->user->profileId);
        }
        if ($no_exams == 0)
        {
            $criteria->compare("Subjects.no_exams", 0);
        }
        $criteria->limit = $limit;
        $criteria->addCondition("DATE(t.start_time)>='" . date("Y-m-d") . "'");
        $criteria->order = "t.start_time ASC";
        $data = $this->findAll($criteria);

        $return = array();
        if ($data)
        {
            $i = 0;
            foreach ($data as $value)
            {
                $return[$i]['subject'] = $value['Subjects']->name;
                $return[$i]['no_exams'] = $value['Subjects']->no_exams;
                $return[$i]['subject_icon'] = $value['Subjects']->icon_number;
                if($value->no_date == 1)
                {
                    $return[$i]['start_time'] = "N/A";
                    $return[$i]['end_time'] = "N/A";
                }
                else
                {
                    $return[$i]['start_time'] = $value->start_time;
                    $return[$i]['end_time'] = $value->end_time; 
                }    
                $return[$i]['exam_name'] = $value['Examgroup']->name;
                $return[$i]['batch'] = $value['Subjects']['Subjectbatch']->name . " " . $value['Subjects']['Subjectbatch']['courseDetails']->course_name . " " . $value['Subjects']['Subjectbatch']['courseDetails']->section_name;
                $i++;
            }
        }
        return $return;
    }

    public function getExamTimeTable($school_id = null, $batch_id = null, $student_id = null, $exam_id = null, $tommmorow = "", $no_exams = 0)
    {

        $criteria = new CDbCriteria;

        $criteria->select = 't.id, t.start_time, t.subject_id, t.end_time, t.no_date';

        $criteria->compare('Examgroup.is_deleted', 0);
        $criteria->with = array(
            'Subjects' => array(
                'select' => 'Subjects.name,Subjects.no_exams',
                'with' => array(
                    'electiveGroup' => array(
                        'select' => 'electiveGroup.id',
                    ),
                ),
            ),
            'Examgroup' => array(
                'select' => 'Examgroup.name',
            ),
            'studentSubject' => array(
                'select' => 'studentSubject.id',
            ),
        );
        $criteria->order = "t.start_time ASC";
        if ($exam_id)
        {
            
            if ($no_exams == 0)
            {
                $criteria->addCondition(
                        "(Examgroup.id = ".$exam_id." AND Examgroup.batch_id = ".$batch_id." AND Examgroup.school_id = ".$school_id.")
                       AND (
                              (
                              Subjects.elective_group_id IS NULL
                              AND Subjects.no_exams = 0
                              AND Subjects.is_deleted = 0
                              AND Subjects.school_id = ".$school_id."
                          )
                          OR (
                              studentSubject.student_id = ".$student_id."
                              AND studentSubject.batch_id = ".$batch_id."
                              AND electiveGroup.is_deleted = 0
                              AND electiveGroup.school_id = ".$school_id."
                          )
                       )"
                );
            } 
            else
            {
                $criteria->addCondition(
                        "(Examgroup.id = ".$exam_id." AND Examgroup.batch_id = ".$batch_id." AND Examgroup.school_id = ".$school_id.")
                       AND (
                              (
                              Subjects.elective_group_id IS NULL
                              AND Subjects.is_deleted = 0
                              AND Subjects.school_id = ".$school_id."
                          )
                          OR (
                              studentSubject.student_id = ".$student_id."
                              AND studentSubject.batch_id = ".$batch_id."
                              AND electiveGroup.is_deleted = 0
                              AND electiveGroup.school_id = ".$school_id."
                          )
                       )"
                );
               
            }
        } 
        else if ($tommmorow)
        {
            if ($no_exams == 0)
            {
                $criteria->addCondition(
                        "(DATE(t.start_time) = '".$tommmorow."' AND Examgroup.batch_id = ".$batch_id." AND Examgroup.school_id = ".$school_id.")
                       AND (
                              (
                              Subjects.elective_group_id IS NULL
                              AND Subjects.no_exams = 0
                              AND Subjects.is_deleted = 0
                              AND Subjects.school_id = ".$school_id."
                          )
                          OR (
                              studentSubject.student_id = ".$student_id."
                              AND studentSubject.batch_id = ".$batch_id."
                              AND electiveGroup.is_deleted = 0
                              AND electiveGroup.school_id = ".$school_id."
                          )
                       )"
                );
            } 
            else
            {
                $criteria->addCondition(
                        "(DATE(t.start_time) = '".$tommmorow."' AND Examgroup.batch_id = ".$batch_id." AND Examgroup.school_id = ".$school_id.")
                       AND (
                              (
                              Subjects.elective_group_id IS NULL
                              AND Subjects.is_deleted = 0
                              AND Subjects.school_id = ".$school_id."
                          )
                          OR (
                              studentSubject.student_id = ".$student_id."
                              AND studentSubject.batch_id = ".$batch_id."
                              AND electiveGroup.is_deleted = 0
                              AND electiveGroup.school_id = ".$school_id."
                          )
                       )"
                );
             
            }
        } 
        else
        {
            if ($no_exams == 0)
            {
                $criteria->addCondition(
                        "(Examgroup.is_current = 1 AND Examgroup.batch_id = ".$batch_id." AND Examgroup.school_id = ".$school_id.")
                       AND (
                              (
                              Subjects.elective_group_id IS NULL
                              AND Subjects.no_exams = 0
                              AND Subjects.is_deleted = 0
                              AND Subjects.school_id = ".$school_id."
                          )
                          OR (
                              studentSubject.student_id = ".$student_id."
                              AND studentSubject.batch_id = ".$batch_id."
                              AND electiveGroup.is_deleted = 0
                              AND electiveGroup.school_id = ".$school_id."
                          )
                       )"
                );
                
            } else
            {
                $criteria->addCondition(
                        "(Examgroup.is_current = 1 AND Examgroup.batch_id = ".$batch_id." AND Examgroup.school_id = ".$school_id.")
                       AND (
                              (
                              Subjects.elective_group_id IS NULL
                              AND Subjects.is_deleted = 0
                              AND Subjects.school_id = ".$school_id."
                          )
                          OR (
                              studentSubject.student_id = ".$student_id."
                              AND studentSubject.batch_id = ".$batch_id."
                              AND electiveGroup.is_deleted = 0
                              AND electiveGroup.school_id = ".$school_id."
                          )
                       )"
                );
            }
        }


        $data = $this->findAll($criteria);

        return (!empty($data)) ? $this->formatExamRoutine($data) : false;
    }

    public function formatExamRoutine($obj_exam_routine)
    {

        $formatted_exams = array();
        foreach ($obj_exam_routine as $rows)
        {
            $_data['exam_subject_id'] = $rows->subject_id;
            $_data['exam_subject_name'] = $rows->Subjects->name;
            $_data['no_exams'] = $rows->Subjects->no_exams;
            if($rows->no_date == 1)
            {
                $_data['exam_start_time'] = "N/A";
                $_data['exam_end_time'] = "N/A";
                $_data['exam_date'] = "N/A";
                $_data['exam_day'] = "N/A"; 
            }
            else
            {
                $_data['exam_start_time'] = date('h:i a', strtotime($rows->start_time));
                $_data['exam_end_time'] = date('h:i a', strtotime($rows->end_time));
                $_data['exam_date'] = date('d/m/Y', strtotime($rows->start_time));
                $_data['exam_day'] = date('l', strtotime($rows->start_time));
            }

            $formatted_exams[] = $_data;
        }

        return $formatted_exams;
    }

    public function getExamGroupResultMaxMarkContinues($exam_group_id, $result = array(), $max_mark = array())
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'Exams.maximum_marks';
        $criteria->addInCondition('t.exam_group_id', $exam_group_id);
        $criteria->compare('Subjects.no_exams', false);
        $criteria->compare('Subjects.is_deleted', false);
        $criteria->with = array(
            'Scores' => array(
                'select' => 'Scores.marks,Scores.student_id',
            ),
            'Subjects' => array(
                'select' => 'Subjects.id',
            )
        );
        $exams = $this->findAll($criteria);
        if ($exams)
        {
            foreach ($exams as $value)
            {

                if ($value['Scores'])
                {
                    foreach ($value['Scores'] as $score)
                    {
                        if ($score->marks)
                        {
                            if (isset($result[$value['Subjects']->id][$score->student_id]['total_mark']))
                            {
                                $result[$value['Subjects']->id][$score->student_id]['total_mark'] = $result[$value['Subjects']->id][$score->student_id]['total_mark'] + $score->marks;
                            } else
                            {
                                $result[$value['Subjects']->id][$score->student_id]['total_mark'] = $score->marks;
                            }
                            if (isset($max_mark[$value['Subjects']->id]))
                            {
                                if ($result[$value['Subjects']->id][$score->student_id]['total_mark'] > $max_mark[$value['Subjects']->id])
                                {
                                    $max_mark[$value['Subjects']->id] = $result[$value['Subjects']->id][$score->student_id]['total_mark'];
                                }
                            } else
                            {
                                $max_mark[$value['Subjects']->id] = $result[$value['Subjects']->id][$score->student_id]['total_mark'];
                            }
                        }
                    }
                }
            }
        }

        return array($result, $max_mark);
    }

}
