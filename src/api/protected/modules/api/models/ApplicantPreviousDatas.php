<?php

/**
 * This is the model class for table "applicant_previous_datas".
 *
 * The followings are the available columns in table 'applicant_previous_datas':
 * @property integer $id
 * @property integer $school_id
 * @property integer $applicant_id
 * @property string $last_attended_school
 * @property string $qualifying_exam
 * @property string $qualifying_exam_year
 * @property string $qualifying_exam_roll
 * @property string $qualifying_exam_final_score
 * @property string $created_at
 * @property string $updated_at
 */
class ApplicantPreviousDatas extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'applicant_previous_datas';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('school_id, applicant_id', 'numerical', 'integerOnly'=>true),
			array('last_attended_school, qualifying_exam, qualifying_exam_year, qualifying_exam_roll, qualifying_exam_final_score', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, school_id, applicant_id, last_attended_school, qualifying_exam, qualifying_exam_year, qualifying_exam_roll, qualifying_exam_final_score, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'school_id' => 'School',
			'applicant_id' => 'Applicant',
			'last_attended_school' => 'Last Attended School',
			'qualifying_exam' => 'Qualifying Exam',
			'qualifying_exam_year' => 'Qualifying Exam Year',
			'qualifying_exam_roll' => 'Qualifying Exam Roll',
			'qualifying_exam_final_score' => 'Qualifying Exam Final Score',
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
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('applicant_id',$this->applicant_id);
		$criteria->compare('last_attended_school',$this->last_attended_school,true);
		$criteria->compare('qualifying_exam',$this->qualifying_exam,true);
		$criteria->compare('qualifying_exam_year',$this->qualifying_exam_year,true);
		$criteria->compare('qualifying_exam_roll',$this->qualifying_exam_roll,true);
		$criteria->compare('qualifying_exam_final_score',$this->qualifying_exam_final_score,true);
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
	 * @return ApplicantPreviousDatas the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
