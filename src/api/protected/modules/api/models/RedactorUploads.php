<?php

/**
 * This is the model class for table "redactor_uploads".
 *
 * The followings are the available columns in table 'redactor_uploads':
 * @property integer $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property string $image_file_name
 * @property string $image_content_type
 * @property integer $image_file_size
 * @property string $image_updated_at
 * @property integer $is_used
 * @property integer $school_id
 */
class RedactorUploads extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'redactor_uploads';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('image_file_size, is_used, school_id', 'numerical', 'integerOnly'=>true),
			array('name, image_file_name, image_content_type', 'length', 'max'=>255),
			array('created_at, updated_at, image_updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, created_at, updated_at, image_file_name, image_content_type, image_file_size, image_updated_at, is_used, school_id', 'safe', 'on'=>'search'),
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
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'image_file_name' => 'Image File Name',
			'image_content_type' => 'Image Content Type',
			'image_file_size' => 'Image File Size',
			'image_updated_at' => 'Image Updated At',
			'is_used' => 'Is Used',
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
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('image_file_name',$this->image_file_name,true);
		$criteria->compare('image_content_type',$this->image_content_type,true);
		$criteria->compare('image_file_size',$this->image_file_size);
		$criteria->compare('image_updated_at',$this->image_updated_at,true);
		$criteria->compare('is_used',$this->is_used);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RedactorUploads the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
