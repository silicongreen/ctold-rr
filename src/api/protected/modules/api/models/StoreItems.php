<?php

/**
 * This is the model class for table "store_items".
 *
 * The followings are the available columns in table 'store_items':
 * @property integer $id
 * @property string $item_name
 * @property integer $quantity
 * @property string $unit_price
 * @property string $tax
 * @property string $batch_number
 * @property integer $is_deleted
 * @property integer $store_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class StoreItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'store_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('quantity, is_deleted, store_id, school_id', 'numerical', 'integerOnly'=>true),
			array('item_name, batch_number', 'length', 'max'=>255),
			array('unit_price, tax', 'length', 'max'=>10),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, item_name, quantity, unit_price, tax, batch_number, is_deleted, store_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'item_name' => 'Item Name',
			'quantity' => 'Quantity',
			'unit_price' => 'Unit Price',
			'tax' => 'Tax',
			'batch_number' => 'Batch Number',
			'is_deleted' => 'Is Deleted',
			'store_id' => 'Store',
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
		$criteria->compare('item_name',$this->item_name,true);
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('unit_price',$this->unit_price,true);
		$criteria->compare('tax',$this->tax,true);
		$criteria->compare('batch_number',$this->batch_number,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('store_id',$this->store_id);
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
	 * @return StoreItems the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
