<?php

/**
 * This is the model class for table "hostel_fees".
 *
 * The followings are the available columns in table 'hostel_fees':
 * @property integer $id
 * @property integer $student_id
 * @property integer $finance_transaction_id
 * @property integer $hostel_fee_collection_id
 * @property string $rent
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class HostelFees extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'hostel_fees';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('student_id, finance_transaction_id, hostel_fee_collection_id, school_id', 'numerical', 'integerOnly'=>true),
			array('rent', 'length', 'max'=>8),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, student_id, finance_transaction_id, hostel_fee_collection_id, rent, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'student_id' => 'Student',
			'finance_transaction_id' => 'Finance Transaction',
			'hostel_fee_collection_id' => 'Hostel Fee Collection',
			'rent' => 'Rent',
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
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('finance_transaction_id',$this->finance_transaction_id);
		$criteria->compare('hostel_fee_collection_id',$this->hostel_fee_collection_id);
		$criteria->compare('rent',$this->rent,true);
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
	 * @return HostelFees the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
