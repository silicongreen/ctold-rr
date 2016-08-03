<?php

/**
 * This is the model class for table "fine_rules".
 *
 * The followings are the available columns in table 'fine_rules':
 * @property integer $id
 * @property integer $fine_id
 * @property integer $fine_days
 * @property string $fine_amount
 * @property integer $is_amount
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class FineRules extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'fine_rules';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fine_id, fine_days, is_amount, user_id, school_id', 'numerical', 'integerOnly'=>true),
			array('fine_amount', 'length', 'max'=>10),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, fine_id, fine_days, fine_amount, is_amount, user_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'fine_id' => 'Fine',
			'fine_days' => 'Fine Days',
			'fine_amount' => 'Fine Amount',
			'is_amount' => 'Is Amount',
			'user_id' => 'User',
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
		$criteria->compare('fine_id',$this->fine_id);
		$criteria->compare('fine_days',$this->fine_days);
		$criteria->compare('fine_amount',$this->fine_amount,true);
		$criteria->compare('is_amount',$this->is_amount);
		$criteria->compare('user_id',$this->user_id);
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
	 * @return FineRules the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
