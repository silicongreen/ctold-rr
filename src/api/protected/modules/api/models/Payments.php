<?php

/**
 * This is the model class for table "payments".
 *
 * The followings are the available columns in table 'payments':
 * @property integer $id
 * @property string $payee_type
 * @property integer $payee_id
 * @property string $payment_type
 * @property integer $payment_id
 * @property string $gateway_response
 * @property integer $finance_transaction_id
 * @property integer $school_id
 * @property string $created_at
 * @property string $updated_at
 */
class Payments extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'payments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('payee_id, payment_id, finance_transaction_id, school_id', 'numerical', 'integerOnly'=>true),
			array('payee_type, payment_type', 'length', 'max'=>255),
			array('gateway_response, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, payee_type, payee_id, payment_type, payment_id, gateway_response, finance_transaction_id, school_id, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'payee_type' => 'Payee Type',
			'payee_id' => 'Payee',
			'payment_type' => 'Payment Type',
			'payment_id' => 'Payment',
			'gateway_response' => 'Gateway Response',
			'finance_transaction_id' => 'Finance Transaction',
			'school_id' => 'School',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
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
		$criteria->compare('payee_type',$this->payee_type,true);
		$criteria->compare('payee_id',$this->payee_id);
		$criteria->compare('payment_type',$this->payment_type,true);
		$criteria->compare('payment_id',$this->payment_id);
		$criteria->compare('gateway_response',$this->gateway_response,true);
		$criteria->compare('finance_transaction_id',$this->finance_transaction_id);
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Payments the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
