<?php

/**
 * This is the model class for table "tds_materials".
 *
 * The followings are the available columns in table 'tds_materials':
 * @property integer $id
 * @property string $material_url
 * @property integer $gallery_id
 * @property string $imagedate
 * @property string $caption
 * @property string $source
 * @property integer $video_id
 * @property integer $menu_id
 *
 * The followings are the available model relations:
 * @property PostGallery[] $postGalleries
 */
class Materials extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tds_materials';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('material_url, gallery_id, imagedate, caption, source', 'required'),
			array('gallery_id, video_id, menu_id', 'numerical', 'integerOnly'=>true),
			array('material_url', 'length', 'max'=>200),
			array('source', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, material_url, gallery_id, imagedate, caption, source, video_id, menu_id', 'safe', 'on'=>'search'),
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
			'postGalleries' => array(self::HAS_MANY, 'PostGallery', 'material_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'material_url' => 'Material Url',
			'gallery_id' => 'Gallery',
			'imagedate' => 'Imagedate',
			'caption' => 'Caption',
			'source' => 'Source',
			'video_id' => 'Video',
			'menu_id' => 'Menu',
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
		$criteria->compare('material_url',$this->material_url,true);
		$criteria->compare('gallery_id',$this->gallery_id);
		$criteria->compare('imagedate',$this->imagedate,true);
		$criteria->compare('caption',$this->caption,true);
		$criteria->compare('source',$this->source,true);
		$criteria->compare('video_id',$this->video_id);
		$criteria->compare('menu_id',$this->menu_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Materials the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
