<?php

/**
 * This is the model class for table "suppliers".
 *
 * The followings are the available columns in table 'suppliers':
 * @property integer $id
 * @property string $name
 * @property string $contact_no
 * @property string $address
 * @property integer $tin_no
 * @property string $region
 * @property string $help_desk
 * @property integer $is_deleted
 * @property integer $supplier_type_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Suppliers extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'suppliers';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tin_no, is_deleted, supplier_type_id, school_id', 'numerical', 'integerOnly'=>true),
			array('name, contact_no, region', 'length', 'max'=>255),
			array('address, help_desk, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, contact_no, address, tin_no, region, help_desk, is_deleted, supplier_type_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'contact_no' => 'Contact No',
			'address' => 'Address',
			'tin_no' => 'Tin No',
			'region' => 'Region',
			'help_desk' => 'Help Desk',
			'is_deleted' => 'Is Deleted',
			'supplier_type_id' => 'Supplier Type',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('contact_no',$this->contact_no,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('tin_no',$this->tin_no);
		$criteria->compare('region',$this->region,true);
		$criteria->compare('help_desk',$this->help_desk,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('supplier_type_id',$this->supplier_type_id);
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
	 * @return Suppliers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
