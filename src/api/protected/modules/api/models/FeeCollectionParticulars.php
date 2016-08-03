<?php

/**
 * This is the model class for table "fee_collection_particulars".
 *
 * The followings are the available columns in table 'fee_collection_particulars':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $amount
 * @property integer $finance_fee_collection_id
 * @property integer $student_category_id
 * @property string $admission_no
 * @property integer $student_id
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class FeeCollectionParticulars extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'fee_collection_particulars';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('finance_fee_collection_id, student_category_id, student_id, is_deleted, school_id', 'numerical', 'integerOnly'=>true),
			array('name, admission_no', 'length', 'max'=>255),
			array('amount', 'length', 'max'=>12),
			array('description, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, description, amount, finance_fee_collection_id, student_category_id, admission_no, student_id, is_deleted, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'description' => 'Description',
			'amount' => 'Amount',
			'finance_fee_collection_id' => 'Finance Fee Collection',
			'student_category_id' => 'Student Category',
			'admission_no' => 'Admission No',
			'student_id' => 'Student',
			'is_deleted' => 'Is Deleted',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('finance_fee_collection_id',$this->finance_fee_collection_id);
		$criteria->compare('student_category_id',$this->student_category_id);
		$criteria->compare('admission_no',$this->admission_no,true);
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('is_deleted',$this->is_deleted);
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
	 * @return FeeCollectionParticulars the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
