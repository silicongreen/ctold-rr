<?php

/**
 * This is the model class for table "exam_connect_subject_comments".
 *
 * The followings are the available columns in table 'exam_connect_subject_comments':
 * @property integer $id
 * @property integer $exam_connect_id
 * @property integer $subject_id
 * @property integer $student_id
 * @property integer $employee_id
 * @property string $comments
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class ExamConnectSubjectComments extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'exam_connect_subject_comments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('exam_connect_id, subject_id, student_id, employee_id, created_at, school_id', 'required'),
			array('exam_connect_id, subject_id, student_id, employee_id, school_id', 'numerical', 'integerOnly'=>true),
			array('comments, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, exam_connect_id, subject_id, student_id, employee_id, comments, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'exam_connect_id' => 'Exam Connect',
			'subject_id' => 'Subject',
			'student_id' => 'Student',
			'employee_id' => 'Employee',
			'comments' => 'Comments',
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
		$criteria->compare('exam_connect_id',$this->exam_connect_id);
		$criteria->compare('subject_id',$this->subject_id);
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('employee_id',$this->employee_id);
		$criteria->compare('comments',$this->comments,true);
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
	 * @return ExamConnectSubjectComments the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getCommentAllSubjects($exam_connect_id,$subject_id,$students)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 't.comments,t.student_id'; 
            $criteria->compare('t.exam_connect_id', $exam_connect_id);
            $criteria->addInCondition('t.subject_id', $subject_id);
            $comments = $this->findAll($criteria);
            $return_comments = array();
            foreach($students as $value)
            {
                foreach($subject_id as $sub)
                $return_comments[$value][$sub] = "";
            }
            if($comments)
            {
                foreach($comments as $value)
                {
                    $return_comments[$value->student_id][$value->subject_id] = $value->comments;
                } 
            }
            return  $return_comments;  
        }
        public function getCommentAll($exam_connect_id,$subject_id,$students)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 't.comments,t.student_id'; 
            $criteria->compare('t.exam_connect_id', $exam_connect_id);
            $criteria->compare('t.subject_id', $subject_id);
            $comments = $this->findAll($criteria);
            $return_comments = array();
            foreach($students as $value)
            {
                $return_comments[$value] = "";
            }
            if($comments)
            {
                foreach($comments as $value)
                {
                    $return_comments[$value->student_id] = $value->comments;
                } 
            }
            return  $return_comments;  
        }
        public function getComment($exam_connect_id,$student_id,$subject_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 't.comments'; 
            $criteria->compare('t.exam_connect_id', $exam_connect_id);
            $criteria->compare('t.subject_id', $subject_id);
            $criteria->compare('t.student_id', $student_id);
            $comments = $this->find($criteria);
            if($comments)
            {
                return $comments->comments;
            }
            else
            {
                return "";
            }    
        }
        
}
