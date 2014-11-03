<?php

/**
 * This is the model class for table "purchase_orders".
 *
 * The followings are the available columns in table 'purchase_orders':
 * @property integer $id
 * @property string $po_no
 * @property string $po_date
 * @property string $po_status
 * @property string $reference
 * @property integer $is_deleted
 * @property integer $store_id
 * @property integer $indent_id
 * @property integer $supplier_id
 * @property integer $supplier_type_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class PurchaseOrders extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'purchase_orders';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_deleted, store_id, indent_id, supplier_id, supplier_type_id, school_id', 'numerical', 'integerOnly'=>true),
			array('po_no, po_status, reference', 'length', 'max'=>255),
			array('po_date, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, po_no, po_date, po_status, reference, is_deleted, store_id, indent_id, supplier_id, supplier_type_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'po_no' => 'Po No',
			'po_date' => 'Po Date',
			'po_status' => 'Po Status',
			'reference' => 'Reference',
			'is_deleted' => 'Is Deleted',
			'store_id' => 'Store',
			'indent_id' => 'Indent',
			'supplier_id' => 'Supplier',
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
		$criteria->compare('po_no',$this->po_no,true);
		$criteria->compare('po_date',$this->po_date,true);
		$criteria->compare('po_status',$this->po_status,true);
		$criteria->compare('reference',$this->reference,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('store_id',$this->store_id);
		$criteria->compare('indent_id',$this->indent_id);
		$criteria->compare('supplier_id',$this->supplier_id);
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
	 * @return PurchaseOrders the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
