<?php

/**
 * This is the model class for table "finance_fees".
 *
 * The followings are the available columns in table 'finance_fees':
 * @property integer $id
 * @property integer $fee_collection_id
 * @property string $transaction_id
 * @property integer $student_id
 * @property integer $is_paid
 * @property string $balance
 * @property string $created_at
 * @property string $updated_at
 * @property integer $batch_id
 * @property integer $school_id
 */
class FinanceFees extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'finance_fees';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fee_collection_id, student_id, is_paid, batch_id, school_id', 'numerical', 'integerOnly'=>true),
			array('transaction_id', 'length', 'max'=>255),
			array('balance', 'length', 'max'=>15),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fee_collection_id, transaction_id, student_id, is_paid, balance, created_at, updated_at, batch_id, school_id', 'safe', 'on'=>'search'),
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
                    'collection' => array(self::BELONGS_TO, 'FinanceFeeCollections', 'fee_collection_id',
                                'joinType' => 'INNER JOIN',
                    ),
                    'feetransactions' => array(self::HAS_MANY, 'FeeTransactions', 'finance_fee_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'fee_collection_id' => 'Fee Collection',
			'transaction_id' => 'Transaction',
			'student_id' => 'Student',
			'is_paid' => 'Is Paid',
			'balance' => 'Balance',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'batch_id' => 'Batch',
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
		$criteria->compare('fee_collection_id',$this->fee_collection_id);
		$criteria->compare('transaction_id',$this->transaction_id,true);
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('is_paid',$this->is_paid);
		$criteria->compare('balance',$this->balance,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return FinanceFees the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function feesStudentDueHistory($student_id)
        {
            $criteria = new CDbCriteria;

            $criteria->select = 't.id, t.is_paid, t.balance';

            $criteria->compare("t.student_id", $student_id);
            $criteria->compare("t.is_paid",1);
            $criteria->with = array(
                'collection' => array(
                    'select' => 'collection.name,collection.due_date'
                )
            );
            $objfess = $this->findAll($criteria);
            
            $afees = array();
            $i = 0;
            if($objfess)
                foreach ($objfess as $key => $value)
                {
                    $afees[$i]['id'] = $value->id;
                    $afees[$i]['is_paid'] = $value->is_paid;
                    $afees[$i]['balance'] = $value->balance;
                    $afees[$i]['name'] = $value['collection']->name;
                    $afees[$i]['duedate'] = $value['collection']->due_date;
                    $i++;
                }
            return  $afees;   
        }
        
        public function feesStudentDue($student_id)
        {
            $criteria = new CDbCriteria;

            $criteria->select = 't.id, t.is_paid, t.balance';

            $criteria->compare("t.student_id", $student_id);
            $criteria->compare("t.is_paid", 0);
            $criteria->addCondition("collection.due_date>='".date("Y-m-d")."'");
            $criteria->with = array(
                'collection' => array(
                    'select' => 'collection.name,collection.due_date'
                ),
                'feetransactions' => array(
                    'select' => 'feetransactions.id',
                    'with' => array(
                        'transaction' => array(
                            'select' => 'transaction.amount'
                        ),
                    )
                )
            );
            $objfess = $this->findAll($criteria);
            
            $afees = array();
            $i = 0;
            if($objfess)
                foreach ($objfess as $key => $value)
                {
                    $afees[$i]['id'] = $value->id;
                    $afees[$i]['is_paid'] = $value->is_paid;
                    $afees[$i]['balance'] = $value->balance;
                    if(isset($value['feetransactions']) && count($value['feetransactions']) > 0)
                    {
                        $balance = 0;
                        foreach($value['feetransactions'] as $falue)
                        {
                            if(isset($falue['transaction']))
                            {
                                $balance = $balance+$falue['transaction']->amount;
                            }    
                        } 
                        $afees[$i]['balance'] = $balance;
                    }   
                    $afees[$i]['name'] = $value['collection']->name;
                    $afees[$i]['duedate'] = $value['collection']->due_date;
                    $i++;
                }
            return  $afees;   
        }
}
