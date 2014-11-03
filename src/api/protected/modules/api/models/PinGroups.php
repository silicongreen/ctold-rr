<?php

/**
 * This is the model class for table "pin_groups".
 *
 * The followings are the available columns in table 'pin_groups':
 * @property integer $id
 * @property string $course_ids
 * @property string $valid_from
 * @property string $valid_till
 * @property string $name
 * @property integer $pin_count
 * @property integer $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class PinGroups extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'pin_groups';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pin_count, is_active, school_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('course_ids, valid_from, valid_till, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, course_ids, valid_from, valid_till, name, pin_count, is_active, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'course_ids' => 'Course Ids',
			'valid_from' => 'Valid From',
			'valid_till' => 'Valid Till',
			'name' => 'Name',
			'pin_count' => 'Pin Count',
			'is_active' => 'Is Active',
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
		$criteria->compare('course_ids',$this->course_ids,true);
		$criteria->compare('valid_from',$this->valid_from,true);
		$criteria->compare('valid_till',$this->valid_till,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('pin_count',$this->pin_count);
		$criteria->compare('is_active',$this->is_active);
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
	 * @return PinGroups the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
