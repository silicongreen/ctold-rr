<?php

/**
 * This is the model class for table "online_exam_groups".
 *
 * The followings are the available columns in table 'online_exam_groups':
 * @property integer $id
 * @property string $name
 * @property string $start_date
 * @property string $end_date
 * @property string $maximum_time
 * @property string $pass_percentage
 * @property integer $option_count
 * @property integer $batch_id
 * @property integer $is_deleted
 * @property integer $is_published
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class OnlineExamGroups extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'online_exam_groups';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('option_count, batch_id, is_deleted, is_published, school_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('maximum_time', 'length', 'max'=>7),
			array('pass_percentage', 'length', 'max'=>6),
			array('start_date, end_date, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, start_date, end_date, maximum_time, pass_percentage, option_count, batch_id, is_deleted, is_published, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
			'maximum_time' => 'Maximum Time',
			'pass_percentage' => 'Pass Percentage',
			'option_count' => 'Option Count',
			'batch_id' => 'Batch',
			'is_deleted' => 'Is Deleted',
			'is_published' => 'Is Published',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('maximum_time',$this->maximum_time,true);
		$criteria->compare('pass_percentage',$this->pass_percentage,true);
		$criteria->compare('option_count',$this->option_count);
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('is_published',$this->is_published);
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
	 * @return OnlineExamGroups the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
