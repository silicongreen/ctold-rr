<?php

/**
 * This is the model class for table "ranking_levels".
 *
 * The followings are the available columns in table 'ranking_levels':
 * @property integer $id
 * @property string $name
 * @property string $gpa
 * @property string $marks
 * @property integer $subject_count
 * @property integer $priority
 * @property string $created_at
 * @property string $updated_at
 * @property integer $full_course
 * @property integer $course_id
 * @property string $subject_limit_type
 * @property string $marks_limit_type
 * @property integer $school_id
 */
class RankingLevels extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ranking_levels';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('subject_count, priority, full_course, course_id, school_id', 'numerical', 'integerOnly'=>true),
			array('name, subject_limit_type, marks_limit_type', 'length', 'max'=>255),
			array('gpa, marks', 'length', 'max'=>15),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, gpa, marks, subject_count, priority, created_at, updated_at, full_course, course_id, subject_limit_type, marks_limit_type, school_id', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'gpa' => 'Gpa',
			'marks' => 'Marks',
			'subject_count' => 'Subject Count',
			'priority' => 'Priority',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'full_course' => 'Full Course',
			'course_id' => 'Course',
			'subject_limit_type' => 'Subject Limit Type',
			'marks_limit_type' => 'Marks Limit Type',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('gpa',$this->gpa,true);
		$criteria->compare('marks',$this->marks,true);
		$criteria->compare('subject_count',$this->subject_count);
		$criteria->compare('priority',$this->priority);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('full_course',$this->full_course);
		$criteria->compare('course_id',$this->course_id);
		$criteria->compare('subject_limit_type',$this->subject_limit_type,true);
		$criteria->compare('marks_limit_type',$this->marks_limit_type,true);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RankingLevels the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
