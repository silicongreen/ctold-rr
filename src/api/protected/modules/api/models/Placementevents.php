<?php

/**
 * This is the model class for table "placementevents".
 *
 * The followings are the available columns in table 'placementevents':
 * @property integer $id
 * @property string $title
 * @property string $company
 * @property string $place
 * @property string $description
 * @property integer $is_active
 * @property string $date
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Placementevents extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'placementevents';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_active, school_id', 'numerical', 'integerOnly'=>true),
			array('title, company, place', 'length', 'max'=>255),
			array('description, date, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, company, place, description, is_active, date, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'title' => 'Title',
			'company' => 'Company',
			'place' => 'Place',
			'description' => 'Description',
			'is_active' => 'Is Active',
			'date' => 'Date',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('company',$this->company,true);
		$criteria->compare('place',$this->place,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('date',$this->date,true);
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
	 * @return Placementevents the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
