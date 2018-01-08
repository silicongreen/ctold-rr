<?php

/**
 * This is the model class for table "additional_exams".
 *
 * The followings are the available columns in table 'additional_exams':
 * @property integer $id
 * @property integer $additional_exam_group_id
 * @property integer $subject_id
 * @property string $start_time
 * @property string $end_time
 * @property integer $maximum_marks
 * @property integer $minimum_marks
 * @property integer $grading_level_id
 * @property integer $weightage
 * @property integer $event_id
 * @property string $created_at
 * @property string $updated_at
 */
class AdditionalExams extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'additional_exams';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('additional_exam_group_id, subject_id, maximum_marks, minimum_marks, grading_level_id, weightage, event_id', 'numerical', 'integerOnly'=>true),
			array('start_time, end_time, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, additional_exam_group_id, subject_id, start_time, end_time, maximum_marks, minimum_marks, grading_level_id, weightage, event_id, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'additional_exam_group_id' => 'Additional Exam Group',
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
		$criteria->compare('additional_exam_group_id',$this->additional_exam_group_id);
		$criteria->compare('subject_id',$this->subject_id);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('maximum_marks',$this->maximum_marks);
		$criteria->compare('minimum_marks',$this->minimum_marks);
		$criteria->compare('grading_level_id',$this->grading_level_id);
		$criteria->compare('weightage',$this->weightage);
		$criteria->compare('event_id',$this->event_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AdditionalExams the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
