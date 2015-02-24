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
class Exams extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public $total_score;
    public $total_points;

    public function tableName() {
        return 'exams';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
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
    public function relations() {
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
    public function attributeLabels() {
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
    public function search() {
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
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getPosition($exam_group_id, $total_grade_point, $total_mark) {
        $sql = "SELECT SUM( Scores.marks ) AS total_score, SUM( Examgrade.credit_points ) AS total_points
        FROM exams
        LEFT JOIN exam_scores AS Scores ON Scores.exam_id = exams.id
        LEFT OUTER JOIN grading_levels AS Examgrade ON Scores.grading_level_id = Examgrade.id 
        WHERE exams.exam_group_id = $exam_group_id
        GROUP BY Scores.student_id
        ORDER BY total_points DESC, total_score DESC";

        $data = $this->findAllBySql($sql);


        $i = 1;
        foreach ($data as $value) {
            if ($total_grade_point == $value->total_points && $total_mark == $value->total_score) {
                break;
            }
            $i++;
        }
        return $i;
    }

    public function getPublishClassTestProjectSubjectWise($subject_id, $batch_id, $limit = true) {
        $criteria = new CDbCriteria();
        $criteria->select = 't.*';
        $criteria->compare('Examgroup.batch_id', $batch_id);
        $criteria->compare('Examgroup.exam_category', 1);
        $criteria->compare('Examgroup.exam_category', 2, false, "OR");
        $criteria->compare('Examgroup.result_published', 1);
        $criteria->compare('t.subject_id', $subject_id);
        $criteria->order = "start_time DESC";
        if ($limit) {
            $criteria->limit = 1;
            $data = $this->with("Examgroup")->find($criteria);
        } else {
            $data = $this->with("Examgroup")->findAll($criteria);
        }

        return $data;
    }

    public function getExamTimeTable($school_id = null, $batch_id = null, $student_id = null,$exam_id=null,$tommmorow="") {

        $criteria = new CDbCriteria;

        $criteria->select = 't.id, t.start_time, t.subject_id, t.end_time';
        

        $criteria->with = array(
            'Subjects' => array(
                'select' => 'Subjects.name',
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
        if($exam_id)
        {
            $criteria->addCondition(
                  "(Examgroup.id = :exam_id AND Examgroup.batch_id = :batch_id AND Examgroup.school_id = :school_id)
                   AND (
                          (
                          Subjects.elective_group_id IS NULL
                          AND Subjects.no_exams = '0'
                          AND Subjects.is_deleted = '0'
                          AND Subjects.school_id = :school_id
                      )
                      OR (
                          studentSubject.student_id = :student_id
                          AND studentSubject.batch_id = :batch_id
                          AND electiveGroup.is_deleted = '0'
                          AND electiveGroup.school_id = :school_id
                      )
                   )"
            ); 
            $params[':exam_id'] = $exam_id;
            $params[':school_id'] = $school_id;
            $params[':batch_id'] = $batch_id;
            $params[':student_id'] = $student_id;
        }
        else if($tommmorow)
        {
            $criteria->addCondition(
                "DATE(t.start_time) = :start_time AND (Examgroup.batch_id = :batch_id AND Examgroup.school_id = :school_id)
                 AND (
                        (
                        Subjects.elective_group_id IS NULL
                        AND Subjects.no_exams = '0'
                        AND Subjects.is_deleted = '0'
                        AND Subjects.school_id = :school_id
                    )
                    OR (
                        studentSubject.student_id = :student_id
                        AND studentSubject.batch_id = :batch_id
                        AND electiveGroup.is_deleted = '0'
                        AND electiveGroup.school_id = :school_id
                    )
                 )"
            );
            $params[':start_time'] = $tommmorow;
            $params[':school_id'] = $school_id;
            $params[':batch_id'] = $batch_id;
            $params[':student_id'] = $student_id;  
        }
        else
        {
           $criteria->addCondition(
                "(Examgroup.is_current = '1' AND Examgroup.batch_id = :batch_id AND Examgroup.school_id = :school_id)
                 AND (
                        (
                        Subjects.elective_group_id IS NULL
                        AND Subjects.no_exams = '0'
                        AND Subjects.is_deleted = '0'
                        AND Subjects.school_id = :school_id
                    )
                    OR (
                        studentSubject.student_id = :student_id
                        AND studentSubject.batch_id = :batch_id
                        AND electiveGroup.is_deleted = '0'
                        AND electiveGroup.school_id = :school_id
                    )
                 )"
            ); 
            $params[':school_id'] = $school_id;
            $params[':batch_id'] = $batch_id;
            $params[':student_id'] = $student_id;
        }    
   

        $criteria->params = $params;

        $data = $this->findAll($criteria);
        
        return (!empty($data)) ? $this->formatExamRoutine($data) : false;
    }

    public function formatExamRoutine($obj_exam_routine) {

        $formatted_exams = array();
        foreach ($obj_exam_routine as $rows) {
            $_data['exam_subject_id'] = $rows->subject_id;
            $_data['exam_subject_name'] = $rows->Subjects->name;
            $_data['exam_start_time'] = date('h:i a', strtotime($rows->start_time));
            $_data['exam_end_time'] = date('h:i a', strtotime($rows->end_time));
            $_data['exam_date'] = date('d/m/Y', strtotime($rows->start_time));
            $_data['exam_day'] = date('l', strtotime($rows->start_time));

            $formatted_exams[] = $_data;
        }

        return $formatted_exams;
    }

}
