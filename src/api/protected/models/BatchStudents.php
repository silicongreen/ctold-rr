<?php

/**
 * This is the model class for table "batch_students".
 *
 * The followings are the available columns in table 'batch_students':
 * @property integer $id
 * @property integer $student_id
 * @property integer $batch_id
 * @property string $updated_at
 * @property string $created_at
 * @property integer $school_id
 * @property string $session
 * @property string $batch_start
 * @property string $batch_end
 */
class BatchStudents extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'batch_students';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('student_id, batch_id, school_id', 'numerical', 'integerOnly'=>true),
			array('session', 'length', 'max'=>255),
			array('updated_at, created_at, batch_start, batch_end', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, student_id, batch_id, updated_at, created_at, school_id, session, batch_start, batch_end', 'safe', 'on'=>'search'),
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
			'batch_id' => 'Batch',
			'updated_at' => 'Updated At',
			'created_at' => 'Created At',
			'school_id' => 'School',
			'session' => 'Session',
			'batch_start' => 'Batch Start',
			'batch_end' => 'Batch End',
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
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('session',$this->session,true);
		$criteria->compare('batch_start',$this->batch_start,true);
		$criteria->compare('batch_end',$this->batch_end,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BatchStudents the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
