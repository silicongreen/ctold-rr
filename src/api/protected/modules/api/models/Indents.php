<?php

/**
 * This is the model class for table "indents".
 *
 * The followings are the available columns in table 'indents':
 * @property integer $id
 * @property string $indent_no
 * @property string $expected_date
 * @property string $status
 * @property integer $is_deleted
 * @property string $description
 * @property integer $user_id
 * @property integer $store_id
 * @property integer $manager_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Indents extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'indents';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_deleted, user_id, store_id, manager_id, school_id', 'numerical', 'integerOnly'=>true),
			array('indent_no, status', 'length', 'max'=>255),
			array('expected_date, description, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, indent_no, expected_date, status, is_deleted, description, user_id, store_id, manager_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'indent_no' => 'Indent No',
			'expected_date' => 'Expected Date',
			'status' => 'Status',
			'is_deleted' => 'Is Deleted',
			'description' => 'Description',
			'user_id' => 'User',
			'store_id' => 'Store',
			'manager_id' => 'Manager',
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
		$criteria->compare('indent_no',$this->indent_no,true);
		$criteria->compare('expected_date',$this->expected_date,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('manager_id',$this->manager_id);
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
	 * @return Indents the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
