<?php

/**
 * This is the model class for table "assessment_scores".
 *
 * The followings are the available columns in table 'assessment_scores':
 * @property integer $id
 * @property integer $student_id
 * @property double $grade_points
 * @property string $created_at
 * @property string $updated_at
 * @property integer $exam_id
 * @property integer $batch_id
 * @property integer $descriptive_indicator_id
 * @property integer $school_id
 */
class AssessmentScores extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'assessment_scores';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('student_id, exam_id, batch_id, descriptive_indicator_id, school_id', 'numerical', 'integerOnly'=>true),
			array('grade_points', 'numerical'),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, student_id, grade_points, created_at, updated_at, exam_id, batch_id, descriptive_indicator_id, school_id', 'safe', 'on'=>'search'),
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
			'student_id' => 'Student',
			'grade_points' => 'Grade Points',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'exam_id' => 'Exam',
			'batch_id' => 'Batch',
			'descriptive_indicator_id' => 'Descriptive Indicator',
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
		$criteria->compare('grade_points',$this->grade_points);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('exam_id',$this->exam_id);
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('descriptive_indicator_id',$this->descriptive_indicator_id);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AssessmentScores the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
