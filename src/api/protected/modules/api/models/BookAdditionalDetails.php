<?php

/**
 * This is the model class for table "book_additional_details".
 *
 * The followings are the available columns in table 'book_additional_details':
 * @property integer $id
 * @property integer $book_id
 * @property integer $book_additional_field_id
 * @property string $additional_info
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class BookAdditionalDetails extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'book_additional_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('book_id, book_additional_field_id, school_id', 'numerical', 'integerOnly'=>true),
			array('additional_info', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, book_id, book_additional_field_id, additional_info, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'book_id' => 'Book',
			'book_additional_field_id' => 'Book Additional Field',
			'additional_info' => 'Additional Info',
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
		$criteria->compare('book_id',$this->book_id);
		$criteria->compare('book_additional_field_id',$this->book_additional_field_id);
		$criteria->compare('additional_info',$this->additional_info,true);
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
	 * @return BookAdditionalDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
