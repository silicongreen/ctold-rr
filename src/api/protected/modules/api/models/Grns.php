<?php

/**
 * This is the model class for table "grns".
 *
 * The followings are the available columns in table 'grns':
 * @property integer $id
 * @property string $grn_no
 * @property string $invoice_no
 * @property string $grn_date
 * @property string $invoice_date
 * @property string $other_charges
 * @property integer $is_deleted
 * @property integer $purchase_order_id
 * @property integer $finance_transaction_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Grns extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'grns';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_deleted, purchase_order_id, finance_transaction_id, school_id', 'numerical', 'integerOnly'=>true),
			array('grn_no, invoice_no', 'length', 'max'=>255),
			array('other_charges', 'length', 'max'=>10),
			array('grn_date, invoice_date, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, grn_no, invoice_no, grn_date, invoice_date, other_charges, is_deleted, purchase_order_id, finance_transaction_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'grn_no' => 'Grn No',
			'invoice_no' => 'Invoice No',
			'grn_date' => 'Grn Date',
			'invoice_date' => 'Invoice Date',
			'other_charges' => 'Other Charges',
			'is_deleted' => 'Is Deleted',
			'purchase_order_id' => 'Purchase Order',
			'finance_transaction_id' => 'Finance Transaction',
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
		$criteria->compare('grn_no',$this->grn_no,true);
		$criteria->compare('invoice_no',$this->invoice_no,true);
		$criteria->compare('grn_date',$this->grn_date,true);
		$criteria->compare('invoice_date',$this->invoice_date,true);
		$criteria->compare('other_charges',$this->other_charges,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('purchase_order_id',$this->purchase_order_id);
		$criteria->compare('finance_transaction_id',$this->finance_transaction_id);
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
	 * @return Grns the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
