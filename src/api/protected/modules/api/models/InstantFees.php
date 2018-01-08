<?php

/**
 * This is the model class for table "instant_fees".
 *
 * The followings are the available columns in table 'instant_fees':
 * @property integer $id
 * @property integer $instant_fee_category_id
 * @property string $custom_category
 * @property integer $payee_id
 * @property string $payee_type
 * @property string $guest_payee
 * @property string $amount
 * @property string $pay_date
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 * @property string $custom_description
 */
class InstantFees extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'instant_fees';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('instant_fee_category_id, payee_id, school_id', 'numerical', 'integerOnly'=>true),
			array('custom_category, payee_type, guest_payee', 'length', 'max'=>255),
			array('amount', 'length', 'max'=>15),
			array('pay_date, created_at, updated_at, custom_description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, instant_fee_category_id, custom_category, payee_id, payee_type, guest_payee, amount, pay_date, created_at, updated_at, school_id, custom_description', 'safe', 'on'=>'search'),
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
			'instant_fee_category_id' => 'Instant Fee Category',
			'custom_category' => 'Custom Category',
			'payee_id' => 'Payee',
			'payee_type' => 'Payee Type',
			'guest_payee' => 'Guest Payee',
			'amount' => 'Amount',
			'pay_date' => 'Pay Date',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'school_id' => 'School',
			'custom_description' => 'Custom Description',
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
		$criteria->compare('instant_fee_category_id',$this->instant_fee_category_id);
		$criteria->compare('custom_category',$this->custom_category,true);
		$criteria->compare('payee_id',$this->payee_id);
		$criteria->compare('payee_type',$this->payee_type,true);
		$criteria->compare('guest_payee',$this->guest_payee,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('pay_date',$this->pay_date,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('custom_description',$this->custom_description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InstantFees the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
