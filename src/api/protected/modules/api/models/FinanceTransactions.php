<?php

/**
 * This is the model class for table "finance_transactions".
 *
 * The followings are the available columns in table 'finance_transactions':
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $amount
 * @property integer $fine_included
 * @property integer $category_id
 * @property integer $student_id
 * @property integer $finance_fees_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $transaction_date
 * @property string $fine_amount
 * @property integer $master_transaction_id
 * @property integer $finance_id
 * @property string $finance_type
 * @property integer $payee_id
 * @property string $payee_type
 * @property string $receipt_no
 * @property string $voucher_no
 * @property string $payment_mode
 * @property string $payment_note
 * @property integer $user_id
 * @property integer $batch_id
 * @property integer $lastvchid
 * @property integer $school_id
 */
class FinanceTransactions extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'finance_transactions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fine_included, category_id, student_id, finance_fees_id, master_transaction_id, finance_id, payee_id, user_id, batch_id, lastvchid, school_id', 'numerical', 'integerOnly'=>true),
			array('title, description, finance_type, payee_type, receipt_no, voucher_no, payment_mode', 'length', 'max'=>255),
			array('amount', 'length', 'max'=>15),
			array('fine_amount', 'length', 'max'=>10),
			array('created_at, updated_at, transaction_date, payment_note', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, description, amount, fine_included, category_id, student_id, finance_fees_id, created_at, updated_at, transaction_date, fine_amount, master_transaction_id, finance_id, finance_type, payee_id, payee_type, receipt_no, voucher_no, payment_mode, payment_note, user_id, batch_id, lastvchid, school_id', 'safe', 'on'=>'search'),
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
			'title' => 'Title',
			'description' => 'Description',
			'amount' => 'Amount',
			'fine_included' => 'Fine Included',
			'category_id' => 'Category',
			'student_id' => 'Student',
			'finance_fees_id' => 'Finance Fees',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'transaction_date' => 'Transaction Date',
			'fine_amount' => 'Fine Amount',
			'master_transaction_id' => 'Master Transaction',
			'finance_id' => 'Finance',
			'finance_type' => 'Finance Type',
			'payee_id' => 'Payee',
			'payee_type' => 'Payee Type',
			'receipt_no' => 'Receipt No',
			'voucher_no' => 'Voucher No',
			'payment_mode' => 'Payment Mode',
			'payment_note' => 'Payment Note',
			'user_id' => 'User',
			'batch_id' => 'Batch',
			'lastvchid' => 'Lastvchid',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('fine_included',$this->fine_included);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('finance_fees_id',$this->finance_fees_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('transaction_date',$this->transaction_date,true);
		$criteria->compare('fine_amount',$this->fine_amount,true);
		$criteria->compare('master_transaction_id',$this->master_transaction_id);
		$criteria->compare('finance_id',$this->finance_id);
		$criteria->compare('finance_type',$this->finance_type,true);
		$criteria->compare('payee_id',$this->payee_id);
		$criteria->compare('payee_type',$this->payee_type,true);
		$criteria->compare('receipt_no',$this->receipt_no,true);
		$criteria->compare('voucher_no',$this->voucher_no,true);
		$criteria->compare('payment_mode',$this->payment_mode,true);
		$criteria->compare('payment_note',$this->payment_note,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('lastvchid',$this->lastvchid);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return FinanceTransactions the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
