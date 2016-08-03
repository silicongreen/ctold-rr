<?php

/**
 * This is the model class for table "online_exam_score_details".
 *
 * The followings are the available columns in table 'online_exam_score_details':
 * @property integer $id
 * @property integer $online_exam_question_id
 * @property integer $online_exam_attendance_id
 * @property integer $online_exam_option_id
 * @property integer $is_correct
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class OnlineExamScoreDetails extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'online_exam_score_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('online_exam_question_id, online_exam_attendance_id, online_exam_option_id, is_correct, school_id', 'numerical', 'integerOnly'=>true),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, online_exam_question_id, online_exam_attendance_id, online_exam_option_id, is_correct, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'online_exam_question_id' => 'Online Exam Question',
			'online_exam_attendance_id' => 'Online Exam Attendance',
			'online_exam_option_id' => 'Online Exam Option',
			'is_correct' => 'Is Correct',
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
		$criteria->compare('online_exam_question_id',$this->online_exam_question_id);
		$criteria->compare('online_exam_attendance_id',$this->online_exam_attendance_id);
		$criteria->compare('online_exam_option_id',$this->online_exam_option_id);
		$criteria->compare('is_correct',$this->is_correct);
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
	 * @return OnlineExamScoreDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
