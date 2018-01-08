<?php

/**
 * This is the model class for table "tally_ledgers".
 *
 * The followings are the available columns in table 'tally_ledgers':
 * @property integer $id
 * @property integer $school_id
 * @property string $ledger_name
 * @property integer $tally_company_id
 * @property integer $tally_voucher_type_id
 * @property integer $tally_account_id
 * @property string $created_at
 * @property string $updated_at
 */
class TallyLedgers extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tally_ledgers';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('school_id, tally_company_id, tally_voucher_type_id, tally_account_id', 'numerical', 'integerOnly'=>true),
			array('ledger_name', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, school_id, ledger_name, tally_company_id, tally_voucher_type_id, tally_account_id, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'school_id' => 'School',
			'ledger_name' => 'Ledger Name',
			'tally_company_id' => 'Tally Company',
			'tally_voucher_type_id' => 'Tally Voucher Type',
			'tally_account_id' => 'Tally Account',
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
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('ledger_name',$this->ledger_name,true);
		$criteria->compare('tally_company_id',$this->tally_company_id);
		$criteria->compare('tally_voucher_type_id',$this->tally_voucher_type_id);
		$criteria->compare('tally_account_id',$this->tally_account_id);
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
	 * @return TallyLedgers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
