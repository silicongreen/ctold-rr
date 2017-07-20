<?php

/**
 * This is the model class for table "grn_items".
 *
 * The followings are the available columns in table 'grn_items':
 * @property integer $id
 * @property integer $quantity
 * @property string $unit_price
 * @property string $tax
 * @property string $discount
 * @property string $expiry_date
 * @property integer $is_deleted
 * @property integer $grn_id
 * @property integer $store_item_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class GrnItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'grn_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('quantity, is_deleted, grn_id, store_item_id, school_id', 'numerical', 'integerOnly'=>true),
			array('unit_price, tax, discount', 'length', 'max'=>10),
			array('expiry_date, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, quantity, unit_price, tax, discount, expiry_date, is_deleted, grn_id, store_item_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'unit_price' => 'Unit Price',
			'tax' => 'Tax',
			'discount' => 'Discount',
			'expiry_date' => 'Expiry Date',
			'is_deleted' => 'Is Deleted',
			'grn_id' => 'Grn',
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
		$criteria->compare('unit_price',$this->unit_price,true);
		$criteria->compare('tax',$this->tax,true);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('expiry_date',$this->expiry_date,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('grn_id',$this->grn_id);
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
	 * @return GrnItems the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
