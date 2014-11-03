<?php

/**
 * This is the model class for table "user_palettes".
 *
 * The followings are the available columns in table 'user_palettes':
 * @property integer $id
 * @property integer $user_id
 * @property integer $palette_id
 * @property integer $position
 * @property integer $is_minimized
 * @property integer $school_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $column_number
 */
class UserPalettes extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_palettes';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, palette_id, position, is_minimized, school_id, column_number', 'numerical', 'integerOnly'=>true),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, palette_id, position, is_minimized, school_id, created_at, updated_at, column_number', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'palette_id' => 'Palette',
			'position' => 'Position',
			'is_minimized' => 'Is Minimized',
			'school_id' => 'School',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'column_number' => 'Column Number',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('palette_id',$this->palette_id);
		$criteria->compare('position',$this->position);
		$criteria->compare('is_minimized',$this->is_minimized);
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('column_number',$this->column_number);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserPalettes the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
