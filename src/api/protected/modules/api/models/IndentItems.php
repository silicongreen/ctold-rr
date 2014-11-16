<?php

/**
 * This is the model class for table "indent_items".
 *
 * The followings are the available columns in table 'indent_items':
 * @property integer $id
 * @property integer $quantity
 * @property string $batch_no
 * @property integer $pending
 * @property integer $issued
 * @property string $issued_type
 * @property string $price
 * @property integer $required
 * @property integer $is_deleted
 * @property integer $indent_id
 * @property integer $store_item_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class IndentItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'indent_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('quantity, pending, issued, required, is_deleted, indent_id, store_item_id, school_id', 'numerical', 'integerOnly'=>true),
			array('batch_no, issued_type', 'length', 'max'=>255),
			array('price', 'length', 'max'=>10),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, quantity, batch_no, pending, issued, issued_type, price, required, is_deleted, indent_id, store_item_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'batch_no' => 'Batch No',
			'pending' => 'Pending',
			'issued' => 'Issued',
			'issued_type' => 'Issued Type',
			'price' => 'Price',
			'required' => 'Required',
			'is_deleted' => 'Is Deleted',
			'indent_id' => 'Indent',
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
		$criteria->compare('batch_no',$this->batch_no,true);
		$criteria->compare('pending',$this->pending);
		$criteria->compare('issued',$this->issued);
		$criteria->compare('issued_type',$this->issued_type,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('required',$this->required);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('indent_id',$this->indent_id);
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
	 * @return IndentItems the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
