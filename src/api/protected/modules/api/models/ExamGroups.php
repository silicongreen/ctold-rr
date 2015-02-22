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
    
    public function getAllExamsResultPublish($batch_id,$category_id=1)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.id,t.name,t.exam_date';
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.result_published', 1);
        $criteria->compare('t.exam_category', $category_id);
        $criteria->order = "t.created_at DESC";
        $data = $this->findAll($criteria);
        return $data;
    }
    
    public function getAllExamsBatch($batch_id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.id,t.name,t.exam_date';
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.is_published', 1);
        $criteria->order = "t.created_at DESC";
        $data = $this->findAll($criteria);
        return $data;
    }

    public function getTermExamsBatch($batch_id, $student_id,$id=0)
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
        $criteria->compare('Subjects.no_exams', false);
        $criteria->compare('Subjects.is_deleted', false);
        $criteria->addCondition("(Subjects.elective_group_id IS NULL OR Subjects.elective_group_id = '' $sids_string )");
        $criteria->together = TRUE;
        $criteria->order = "t.exam_date ASC";
        $data = $this->with("Exams","Acknowledge")->findAll($criteria);
        return $data;
    }
    
    public function getExamCategory($school_id = null, $batch_id = null, $category_id = null) {
        
        $criteria = new CDbCriteria();
        $criteria->select = 't.id, t.name';
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
            
            $ar_formatted_data[] = $_data;
        }
        return $ar_formatted_data;
    }

}
