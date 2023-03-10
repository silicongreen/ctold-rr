<?php

/**
 * This is the model class for table "descriptive_indicators".
 *
 * The followings are the available columns in table 'descriptive_indicators':
 * @property integer $id
 * @property string $name
 * @property string $desc
 * @property integer $describable_id
 * @property string $describable_type
 * @property string $created_at
 * @property string $updated_at
 * @property integer $sort_order
 * @property integer $school_id
 */
class DescriptiveIndicators extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'descriptive_indicators';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('describable_id, sort_order, school_id', 'numerical', 'integerOnly'=>true),
			array('name, desc, describable_type', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, desc, describable_id, describable_type, created_at, updated_at, sort_order, school_id', 'safe', 'on'=>'search'),
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
			'desc' => 'Desc',
			'describable_id' => 'Describable',
			'describable_type' => 'Describable Type',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'sort_order' => 'Sort Order',
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
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('describable_id',$this->describable_id);
		$criteria->compare('describable_type',$this->describable_type,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('sort_order',$this->sort_order);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DescriptiveIndicators the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
