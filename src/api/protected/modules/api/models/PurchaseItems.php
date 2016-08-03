<?php

/**
 * This is the model class for table "purchase_items".
 *
 * The followings are the available columns in table 'purchase_items':
 * @property integer $id
 * @property integer $quantity
 * @property string $discount
 * @property string $tax
 * @property string $price
 * @property integer $is_deleted
 * @property integer $user_id
 * @property integer $purchase_order_id
 * @property integer $store_item_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class PurchaseItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'purchase_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('quantity, is_deleted, user_id, purchase_order_id, store_item_id, school_id', 'numerical', 'integerOnly'=>true),
			array('discount, tax, price', 'length', 'max'=>10),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, quantity, discount, tax, price, is_deleted, user_id, purchase_order_id, store_item_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'quantity' => 'Quantity',
			'discount' => 'Discount',
			'tax' => 'Tax',
			'price' => 'Price',
			'is_deleted' => 'Is Deleted',
			'user_id' => 'User',
			'purchase_order_id' => 'Purchase Order',
			'store_item_id' => 'Store Item',
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
		$criteria->compare('quantity',$this->quantity);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('tax',$this->tax,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('purchase_order_id',$this->purchase_order_id);
		$criteria->compare('store_item_id',$this->store_item_id);
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
	 * @return PurchaseItems the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
