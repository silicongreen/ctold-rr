<?php

/**
 * This is the model class for table "batch_students".
 *
 * The followings are the available columns in table 'batch_students':
 * @property integer $student_id
 * @property integer $batch_id
 * @property string $created_at
 * @property integer $id
 * @property string $updated_at
 * @property integer $school_id
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
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('student_id, batch_id, created_at, id, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'student_id' => 'Student',
			'batch_id' => 'Batch',
			'created_at' => 'Created At',
			'id' => 'ID',
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

		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('id',$this->id);
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
	 * @return BatchStudents the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
