<?php

/**
 * This is the model class for table "tds_science_rocks_category".
 *
 * The followings are the available columns in table 'tds_science_rocks_category':
 * @property integer $id
 * @property string $name
 * @property string $details
 * @property integer $priority
 * @property integer $status
 * @property string $logo
 */
class TdsScienceRocksCategory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tds_science_rocks_category';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('priority, status', 'numerical', 'integerOnly'=>true),
			array('name, details, logo', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, details, priority, status, logo', 'safe', 'on'=>'search'),
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
			'details' => 'Details',
			'priority' => 'Priority',
			'status' => 'Status',
			'logo' => 'Logo',
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
		$criteria->compare('details',$this->details,true);
		$criteria->compare('priority',$this->priority);
		$criteria->compare('status',$this->status);
		$criteria->compare('logo',$this->logo,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdsScienceRocksCategory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getCategorySearch($term)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id, t.name,t.en_name, t.details';
            $criteria->compare('status',1);
            $criteria->addCondition("t.name like '".$term."%' or t.en_name like '".$term."%'");
            $criteria->order = 't.priority ASC';
            $criteria->limit = 5;
            $data = $this->findAll($criteria);
            
            $sc_category = array();
            if($data)
            {
                foreach ($data as $value)
                {
                   if(!$value->en_name)
                   {
                     $value->en_name =   $value->name;
                   }
                   $sc_category[] = $value;
                }    
            }
            
            return $sc_category;
        } 
        
        public function getCategory()
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id, t.name,t.en_name, t.details';
            $criteria->compare('status',1);
            $criteria->order = 't.priority ASC';
            $data = $this->findAll($criteria);
            
            $sc_category = array();
            if($data)
            {
                foreach ($data as $value)
                {
                   if(!$value->en_name)
                   {
                     $value->en_name =   $value->name;
                   }
                   $sc_category[] = $value;
                }    
            }
            
            return $sc_category;
        }        
}
