<?php

/**
 * This is the model class for table "school_domains".
 *
 * The followings are the available columns in table 'school_domains':
 * @property integer $id
 * @property string $domain
 * @property string $created_at
 * @property string $updated_at
 * @property integer $linkable_id
 * @property string $linkable_type
 */
class SchoolDomains extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'school_domains';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('linkable_id', 'numerical', 'integerOnly'=>true),
			array('domain, linkable_type', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, domain, created_at, updated_at, linkable_id, linkable_type', 'safe', 'on'=>'search'),
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
			'domain' => 'Domain',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'linkable_id' => 'Linkable',
			'linkable_type' => 'Linkable Type',
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
		$criteria->compare('domain',$this->domain,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('linkable_id',$this->linkable_id);
		$criteria->compare('linkable_type',$this->linkable_type,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SchoolDomains the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getSchoolDomainBySchoolId($school_id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = "*";
            $criteria->compare('linkable_id', $school_id);
            $domain = $this->find($criteria);
            if($domain)
            {
                return $domain;
            }
            return FALSE;
        } 
}
