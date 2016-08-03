<?php

/**
 * This is the model class for table "applicant_addl_fields".
 *
 * The followings are the available columns in table 'applicant_addl_fields':
 * @property integer $id
 * @property integer $school_id
 * @property integer $applicant_addl_field_group_id
 * @property string $field_name
 * @property string $field_type
 * @property integer $is_active
 * @property integer $position
 * @property integer $is_mandatory
 * @property string $created_at
 * @property string $updated_at
 */
class ApplicantAddlFields extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'applicant_addl_fields';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('school_id, applicant_addl_field_group_id, is_active, position, is_mandatory', 'numerical', 'integerOnly'=>true),
			array('field_name, field_type', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, school_id, applicant_addl_field_group_id, field_name, field_type, is_active, position, is_mandatory, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'applicant_addl_field_group_id' => 'Applicant Addl Field Group',
			'field_name' => 'Field Name',
			'field_type' => 'Field Type',
			'is_active' => 'Is Active',
			'position' => 'Position',
			'is_mandatory' => 'Is Mandatory',
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
		$criteria->compare('applicant_addl_field_group_id',$this->applicant_addl_field_group_id);
		$criteria->compare('field_name',$this->field_name,true);
		$criteria->compare('field_type',$this->field_type,true);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('position',$this->position);
		$criteria->compare('is_mandatory',$this->is_mandatory);
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
	 * @return ApplicantAddlFields the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
