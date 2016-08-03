<?php

/**
 * This is the model class for table "grouped_exam_reports".
 *
 * The followings are the available columns in table 'grouped_exam_reports':
 * @property integer $id
 * @property integer $batch_id
 * @property integer $student_id
 * @property integer $exam_group_id
 * @property string $marks
 * @property string $score_type
 * @property integer $subject_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class GroupedExamReports extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'grouped_exam_reports';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('batch_id, student_id, exam_group_id, subject_id, school_id', 'numerical', 'integerOnly'=>true),
			array('marks', 'length', 'max'=>15),
			array('score_type', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, batch_id, student_id, exam_group_id, marks, score_type, subject_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'batch_id' => 'Batch',
			'student_id' => 'Student',
			'exam_group_id' => 'Exam Group',
			'marks' => 'Marks',
			'score_type' => 'Score Type',
			'subject_id' => 'Subject',
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
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('exam_group_id',$this->exam_group_id);
		$criteria->compare('marks',$this->marks,true);
		$criteria->compare('score_type',$this->score_type,true);
		$criteria->compare('subject_id',$this->subject_id);
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
	 * @return GroupedExamReports the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
