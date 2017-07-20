<?php

/**
 * This is the model class for table "archived_guardians".
 *
 * The followings are the available columns in table 'archived_guardians':
 * @property integer $id
 * @property integer $ward_id
 * @property string $first_name
 * @property string $last_name
 * @property string $relation
 * @property string $email
 * @property string $office_phone1
 * @property string $office_phone2
 * @property string $mobile_phone
 * @property string $office_address_line1
 * @property string $office_address_line2
 * @property string $city
 * @property string $state
 * @property integer $country_id
 * @property string $dob
 * @property string $occupation
 * @property string $income
 * @property string $education
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class ArchivedGuardians extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'archived_guardians';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ward_id, country_id, school_id', 'numerical', 'integerOnly'=>true),
			array('first_name, last_name, relation, email, office_phone1, office_phone2, mobile_phone, office_address_line1, office_address_line2, city, state, occupation, income, education', 'length', 'max'=>255),
			array('dob, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, ward_id, first_name, last_name, relation, email, office_phone1, office_phone2, mobile_phone, office_address_line1, office_address_line2, city, state, country_id, dob, occupation, income, education, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'ward_id' => 'Ward',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'relation' => 'Relation',
			'email' => 'Email',
			'office_phone1' => 'Office Phone1',
			'office_phone2' => 'Office Phone2',
			'mobile_phone' => 'Mobile Phone',
			'office_address_line1' => 'Office Address Line1',
			'office_address_line2' => 'Office Address Line2',
			'city' => 'City',
			'state' => 'State',
			'country_id' => 'Country',
			'dob' => 'Dob',
			'occupation' => 'Occupation',
			'income' => 'Income',
			'education' => 'Education',
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
		$criteria->compare('ward_id',$this->ward_id);
		$criteria->compare('first_name',$this->first_name,true);
		$criteria->compare('last_name',$this->last_name,true);
		$criteria->compare('relation',$this->relation,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('office_phone1',$this->office_phone1,true);
		$criteria->compare('office_phone2',$this->office_phone2,true);
		$criteria->compare('mobile_phone',$this->mobile_phone,true);
		$criteria->compare('office_address_line1',$this->office_address_line1,true);
		$criteria->compare('office_address_line2',$this->office_address_line2,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('state',$this->state,true);
		$criteria->compare('country_id',$this->country_id);
		$criteria->compare('dob',$this->dob,true);
		$criteria->compare('occupation',$this->occupation,true);
		$criteria->compare('income',$this->income,true);
		$criteria->compare('education',$this->education,true);
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
	 * @return ArchivedGuardians the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
