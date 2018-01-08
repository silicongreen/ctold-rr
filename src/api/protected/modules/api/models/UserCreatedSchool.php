<?php

/**
 * This is the model class for table "tds_user_created_school".
 *
 * The followings are the available columns in table 'tds_user_created_school':
 * @property integer $id
 * @property integer $freeuser_id
 * @property string $school_name
 * @property string $contact
 * @property string $address
 * @property string $zip_code
 * @property string $about
 * @property string $logo
 * @property string $picture
 * @property string $created
 */
class UserCreatedSchool extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tds_user_created_school';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('freeuser_id, school_name, contact, address, about', 'required'),
			array('freeuser_id', 'numerical', 'integerOnly'=>true),
			array('school_name, contact, zip_code, logo, picture', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, freeuser_id, school_name, contact, address, zip_code, about, logo, picture, created', 'safe', 'on'=>'search'),
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
			'freeuser_id' => 'Freeuser',
			'school_name' => 'School Name',
			'contact' => 'Contact',
			'address' => 'Address',
			'zip_code' => 'Zip Code',
			'about' => 'About',
			'logo' => 'Logo',
			'picture' => 'Picture',
			'created' => 'Created',
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
		$criteria->compare('freeuser_id',$this->freeuser_id);
		$criteria->compare('school_name',$this->school_name,true);
		$criteria->compare('contact',$this->contact,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('zip_code',$this->zip_code,true);
		$criteria->compare('about',$this->about,true);
		$criteria->compare('logo',$this->logo,true);
		$criteria->compare('picture',$this->picture,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserCreatedSchool the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
