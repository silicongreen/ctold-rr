<?php

/**
 * This is the model class for table "payroll_categories".
 *
 * The followings are the available columns in table 'payroll_categories':
 * @property integer $id
 * @property string $name
 * @property double $percentage
 * @property integer $payroll_category_id
 * @property integer $is_deduction
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $is_deleted
 * @property integer $school_id
 */
class PayrollCategories extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'payroll_categories';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('payroll_category_id, is_deduction, status, is_deleted, school_id', 'numerical', 'integerOnly'=>true),
			array('percentage', 'numerical'),
			array('name', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, percentage, payroll_category_id, is_deduction, status, created_at, updated_at, is_deleted, school_id', 'safe', 'on'=>'search'),
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
			'percentage' => 'Percentage',
			'payroll_category_id' => 'Payroll Category',
			'is_deduction' => 'Is Deduction',
			'status' => 'Status',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'is_deleted' => 'Is Deleted',
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
		$criteria->compare('percentage',$this->percentage);
		$criteria->compare('payroll_category_id',$this->payroll_category_id);
		$criteria->compare('is_deduction',$this->is_deduction);
		$criteria->compare('status',$this->status);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PayrollCategories the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getPayRollCategory($school_id)
        {
            $criteria=new CDbCriteria;
            $criteria->select = "t.id,t.name";
            $criteria->compare('school_id',$school_id);
            $data = $this->findAll($criteria);
            if($data)
            {
                return $data;
            }
            return false;
        }        
}
