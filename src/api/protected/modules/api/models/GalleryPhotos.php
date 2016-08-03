<?php

/**
 * This is the model class for table "gallery_photos".
 *
 * The followings are the available columns in table 'gallery_photos':
 * @property integer $id
 * @property integer $gallery_category_id
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $photo_file_name
 * @property string $photo_content_type
 * @property integer $photo_file_size
 * @property string $photo_updated_at
 * @property string $name
 * @property integer $school_id
 */
class GalleryPhotos extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'gallery_photos';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('gallery_category_id, photo_file_size, school_id', 'numerical', 'integerOnly'=>true),
			array('description, photo_file_name, photo_content_type, name', 'length', 'max'=>255),
			array('created_at, updated_at, photo_updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, gallery_category_id, description, created_at, updated_at, photo_file_name, photo_content_type, photo_file_size, photo_updated_at, name, school_id', 'safe', 'on'=>'search'),
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
			'gallery_category_id' => 'Gallery Category',
			'description' => 'Description',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'photo_file_name' => 'Photo File Name',
			'photo_content_type' => 'Photo Content Type',
			'photo_file_size' => 'Photo File Size',
			'photo_updated_at' => 'Photo Updated At',
			'name' => 'Name',
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
		$criteria->compare('gallery_category_id',$this->gallery_category_id);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('photo_file_name',$this->photo_file_name,true);
		$criteria->compare('photo_content_type',$this->photo_content_type,true);
		$criteria->compare('photo_file_size',$this->photo_file_size);
		$criteria->compare('photo_updated_at',$this->photo_updated_at,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GalleryPhotos the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
