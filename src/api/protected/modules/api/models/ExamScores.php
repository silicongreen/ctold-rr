<?php

/**
 * This is the model class for table "exam_scores".
 *
 * The followings are the available columns in table 'exam_scores':
 * @property integer $id
 * @property integer $student_id
 * @property integer $exam_id
 * @property string $marks
 * @property integer $grading_level_id
 * @property string $remarks
 * @property integer $is_failed
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class ExamScores extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $max_marks;
        public $avg_marks;
       
	public function tableName()
	{
		return 'exam_scores';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('student_id, exam_id, grading_level_id, is_failed, school_id', 'numerical', 'integerOnly'=>true),
			array('marks', 'length', 'max'=>7),
			array('remarks', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, student_id, exam_id, marks, grading_level_id, remarks, is_failed, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
                     'Examgrade' => array(self::BELONGS_TO, 'GradingLevels', 'grading_level_id',
                            'joinType' => 'LEFT JOIN',
                        ),
                    'Students' => array(self::BELONGS_TO, 'Students', 'student_id',
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
			'student_id' => 'Student',
			'exam_id' => 'Exam',
			'marks' => 'Marks',
			'grading_level_id' => 'Grading Level',
			'remarks' => 'Remarks',
			'is_failed' => 'Is Failed',
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
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('exam_id',$this->exam_id);
		$criteria->compare('marks',$this->marks,true);
		$criteria->compare('grading_level_id',$this->grading_level_id);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('is_failed',$this->is_failed);
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
	 * @return ExamScores the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getExamStudentAvgMark($exam_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 't.id,AVG(t.marks) as avg_marks';
            $criteria->compare('t.exam_id', $exam_id);
            $criteria->limit = 1;
            $data = $this->find($criteria);
            
            return $data->avg_marks;
            
        }
        public function getExamStudentMaxMark($exam_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 't.id,MAX(t.marks) as max_marks';
            $criteria->compare('t.exam_id', $exam_id);
            $criteria->limit = 1;
            $data = $this->find($criteria);
            
            return $data->max_marks;
            
        }
        
        
        public function getSingleExamStudentResult($student_id,$exam_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.exam_id', $exam_id);
            $criteria->compare('t.student_id', $student_id);
            
            $criteria->limit = 1;
            $data = $this->with("Examgrade")->find($criteria);
            
            return $data;
            
        }
}
