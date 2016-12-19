<?php

/**
 * This is the model class for table "students_subjects".
 *
 * The followings are the available columns in table 'students_subjects':
 * @property integer $id
 * @property integer $student_id
 * @property integer $subject_id
 * @property integer $batch_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class StudentsSubjects extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'students_subjects';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('student_id, subject_id, batch_id, school_id', 'numerical', 'integerOnly'=>true),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, student_id, subject_id, batch_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
                    'Subjectstudent' => array(self::BELONGS_TO, 'Students', 'student_id',
                            'joinType' => 'INNER JOIN',
                    ),
                    'MainSubjects' => array(self::BELONGS_TO, 'Subjects', 'subject_id',
                            'joinType' => 'INNER JOIN',
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
			'subject_id' => 'Subject',
			'batch_id' => 'Batch',
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
		$criteria->compare('subject_id',$this->subject_id);
		$criteria->compare('batch_id',$this->batch_id);
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
	 * @return StudentsSubjects the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getStudentSubject($batch_id,$student_id=0,$no_exam =0)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.batch_id', $batch_id);
            if($student_id)
            {
                $criteria->compare('t.student_id', $student_id);
            }
            $criteria->compare('MainSubjects.no_exams', $no_exam);
            
            $criteria->with =array(
                        "MainSubjects" => array(
                            "select" => "MainSubjects.*",
                            'joinType' => "INNER JOIN",
                            'with' => array(
                                "electiveGroup" => array(
                                    "select" => "electiveGroup.*",
                                    'joinType' => "LEFT JOIN",
                                )
                            )
                     )
            );
            $subjects = $this->findAll($criteria);
            
            $subject_array = array();
            $sub_ids = array();
            foreach($subjects as $value)
            {
                if(!in_array($value['MainSubjects']->id, $sub_ids))
                {
                    $sub_ids[] = $value['MainSubjects']->id;
                    $subject_array[] = $value['MainSubjects'];
                }
            }    
            return $subject_array;
        }
        
        public function getSubjectStudent($subject_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 't.student_id';
            $criteria->compare('t.subject_id', $subject_id);
            $students = $this->findAll($criteria); 
            $students_array = array();
            
            foreach($students as $value)
            {
                $students_array[] = $value->student_id;
            } 
            
            return $students_array;
        }
        
}
