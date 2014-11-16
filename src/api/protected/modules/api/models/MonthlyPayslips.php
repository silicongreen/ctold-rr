<?php

/**
 * This is the model class for table "monthly_payslips".
 *
 * The followings are the available columns in table 'monthly_payslips':
 * @property integer $id
 * @property string $salary_date
 * @property integer $employee_id
 * @property integer $payroll_category_id
 * @property string $amount
 * @property integer $is_approved
 * @property integer $approver_id
 * @property integer $is_rejected
 * @property integer $rejector_id
 * @property string $reason
 * @property string $remark
 * @property string $created_at
 * @property string $updated_at
 * @property integer $finance_transaction_id
 * @property integer $school_id
 */
class MonthlyPayslips extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'monthly_payslips';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('employee_id, payroll_category_id, is_approved, approver_id, is_rejected, rejector_id, finance_transaction_id, school_id', 'numerical', 'integerOnly'=>true),
			array('amount, reason, remark', 'length', 'max'=>255),
			array('salary_date, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, salary_date, employee_id, payroll_category_id, amount, is_approved, approver_id, is_rejected, rejector_id, reason, remark, created_at, updated_at, finance_transaction_id, school_id', 'safe', 'on'=>'search'),
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
			'salary_date' => 'Salary Date',
			'employee_id' => 'Employee',
			'payroll_category_id' => 'Payroll Category',
			'amount' => 'Amount',
			'is_approved' => 'Is Approved',
			'approver_id' => 'Approver',
			'is_rejected' => 'Is Rejected',
			'rejector_id' => 'Rejector',
			'reason' => 'Reason',
			'remark' => 'Remark',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'finance_transaction_id' => 'Finance Transaction',
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
		$criteria->compare('salary_date',$this->salary_date,true);
		$criteria->compare('employee_id',$this->employee_id);
		$criteria->compare('payroll_category_id',$this->payroll_category_id);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('is_approved',$this->is_approved);
		$criteria->compare('approver_id',$this->approver_id);
		$criteria->compare('is_rejected',$this->is_rejected);
		$criteria->compare('rejector_id',$this->rejector_id);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('finance_transaction_id',$this->finance_transaction_id);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MonthlyPayslips the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
