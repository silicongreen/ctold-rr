<?php

/**
 * This is the model class for table "finance_fee_structure_elements".
 *
 * The followings are the available columns in table 'finance_fee_structure_elements':
 * @property integer $id
 * @property string $amount
 * @property string $label
 * @property integer $batch_id
 * @property integer $student_category_id
 * @property integer $student_id
 * @property integer $parent_id
 * @property integer $fee_collection_id
 * @property integer $deleted
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class FinanceFeeStructureElements extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'finance_fee_structure_elements';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('batch_id, student_category_id, student_id, parent_id, fee_collection_id, deleted, school_id', 'numerical', 'integerOnly'=>true),
			array('amount', 'length', 'max'=>15),
			array('label', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, amount, label, batch_id, student_category_id, student_id, parent_id, fee_collection_id, deleted, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'amount' => 'Amount',
			'label' => 'Label',
			'batch_id' => 'Batch',
			'student_category_id' => 'Student Category',
			'student_id' => 'Student',
			'parent_id' => 'Parent',
			'fee_collection_id' => 'Fee Collection',
			'deleted' => 'Deleted',
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
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('label',$this->label,true);
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('student_category_id',$this->student_category_id);
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('fee_collection_id',$this->fee_collection_id);
		$criteria->compare('deleted',$this->deleted);
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
	 * @return FinanceFeeStructureElements the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
