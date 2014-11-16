<?php

/**
 * This is the model class for table "groups".
 *
 * The followings are the available columns in table 'groups':
 * @property integer $id
 * @property string $group_name
 * @property string $group_description
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $logo_file_name
 * @property string $logo_content_type
 * @property integer $logo_file_size
 * @property string $logo_updated_at
 * @property integer $school_id
 */
class Groups extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'groups';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, logo_file_size, school_id', 'numerical', 'integerOnly'=>true),
			array('group_name, logo_file_name, logo_content_type', 'length', 'max'=>255),
			array('group_description, created_at, updated_at, logo_updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, group_name, group_description, user_id, created_at, updated_at, logo_file_name, logo_content_type, logo_file_size, logo_updated_at, school_id', 'safe', 'on'=>'search'),
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
			'group_name' => 'Group Name',
			'group_description' => 'Group Description',
			'user_id' => 'User',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'logo_file_name' => 'Logo File Name',
			'logo_content_type' => 'Logo Content Type',
			'logo_file_size' => 'Logo File Size',
			'logo_updated_at' => 'Logo Updated At',
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
		$criteria->compare('group_name',$this->group_name,true);
		$criteria->compare('group_description',$this->group_description,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('logo_file_name',$this->logo_file_name,true);
		$criteria->compare('logo_content_type',$this->logo_content_type,true);
		$criteria->compare('logo_file_size',$this->logo_file_size);
		$criteria->compare('logo_updated_at',$this->logo_updated_at,true);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Groups the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
