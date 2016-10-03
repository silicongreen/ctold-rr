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
                        'select' => 'examgroup.id,examgroup.name,examgroup.exam_category',
                        'joinType' => 'LEFT JOIN',
                        'with' => array(
                            'Exams' => array(
                                'select' => 'Exams.id,Exams.maximum_marks',
                                'joinType' => 'LEFT JOIN',
//                                'with' => array(
//                                    'Scores' => array(
//                                        'select' => 'Scores.marks,Scores.student_id',
//                                        'with' => array(
//                                            'Students' => array(
//                                                'select' => 'Students.first_name,Students.last_name,Students.middle_name,Students.class_roll_no,Students.id',
//                                            ),
//                                        )
//                                    ),
//                                    'Subjects' => array(
//                                        'select' => 'Subjects.id',
//                                    )
//                                )
                            )
                        )
                    )
                )
            )
        );
        //$criteria->compare('Exams.subject_id', $subject_id);
        
        $examresult = $this->find($criteria);
        
        $result = array();
        if($examresult)
        {
            echo "here";
            exit;
            $i = 0;
            $j = 0;
            $k = 0;
            $l = 0;
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
                        
                        $result['CT'][$i]['maximum_marks'] = $exam->maximum_marks;
                        if($i == 0)
                        {
                            $max_mark_ct = $exam->maximum_marks;
                        }
                        foreach($exam['Scores'] as $scores)
                        {
                            $std_middle_name = ($scores['Scores']['Students']->middle_name)?$scores['Scores']['Students']->middle_name." ":"";
                            $result['CT'][$i]['students'][$j]['name'] = $scores['Scores']['Students']->first_name." ".$std_middle_name.$scores['Scores']['Students']->last_name;
                            $result['CT'][$i]['students'][$j]['id'] = $scores['Scores']['Students']->id;
                            $result['CT'][$i]['students'][$j]['class_roll_no'] = $scores['Scores']['Students']->class_roll_no;
                            $result['CT'][$i]['students'][$j]['score'] = $scores['Scores']->marks;
                            $j++;
                        } 
                        $i++;
                         
                        
                    }
                    else if($groupedexam['examgroup']->exam_category == 4)
                    {
                        $exam =$groupedexam['examgroup']['Exams'][0];
                        $result['ST'][$k]['name'] = $groupedexam['examgroup']->name;
                        
                        $result['ST'][$k]['maximum_marks'] = $exam->maximum_marks;
                        if($i == 0)
                        {
                            $max_mark_st = $exam->maximum_marks;
                        }
                        foreach($exam['Scores'] as $scores)
                        {
                            $std_middle_name = ($scores['Scores']['Students']->middle_name)?$scores['Scores']['Students']->middle_name." ":"";
                            $result['ST'][$k]['students'][$l]['name'] = $scores['Scores']['Students']->first_name." ".$std_middle_name.$scores['Scores']['Students']->last_name;
                            $result['ST'][$k]['students'][$l]['id'] = $scores['Scores']['Students']->id;
                            $result['ST'][$k]['students'][$l]['class_roll_no'] = $scores['Scores']['Students']->class_roll_no;
                            $result['ST'][$k]['students'][$l]['score'] = $scores['Scores']->marks;
                            $l++;
                        } 
                        $k++;
                    }
                } 
              $result['max_mark_ct'] = $max_mark_ct;
              $result['max_mark_st'] = $max_mark_st;  
                
            }
            
        }
        return $result;

    }

}
