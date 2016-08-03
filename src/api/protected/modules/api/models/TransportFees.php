<?php

/**
 * This is the model class for table "transport_fees".
 *
 * The followings are the available columns in table 'transport_fees':
 * @property integer $id
 * @property integer $receiver_id
 * @property string $bus_fare
 * @property integer $transaction_id
 * @property integer $transport_fee_collection_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $receiver_type
 * @property integer $school_id
 */
class TransportFees extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'transport_fees';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('receiver_id, transaction_id, transport_fee_collection_id, school_id', 'numerical', 'integerOnly'=>true),
			array('bus_fare', 'length', 'max'=>8),
			array('receiver_type', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, receiver_id, bus_fare, transaction_id, transport_fee_collection_id, created_at, updated_at, receiver_type, school_id', 'safe', 'on'=>'search'),
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
			'receiver_id' => 'Receiver',
			'bus_fare' => 'Bus Fare',
			'transaction_id' => 'Transaction',
			'transport_fee_collection_id' => 'Transport Fee Collection',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'receiver_type' => 'Receiver Type',
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
		$criteria->compare('receiver_id',$this->receiver_id);
		$criteria->compare('bus_fare',$this->bus_fare,true);
		$criteria->compare('transaction_id',$this->transaction_id);
		$criteria->compare('transport_fee_collection_id',$this->transport_fee_collection_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('receiver_type',$this->receiver_type,true);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TransportFees the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
