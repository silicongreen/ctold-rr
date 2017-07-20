<?php

/**
 * This is the model class for table "tds_tags".
 *
 * The followings are the available columns in table 'tds_tags':
 * @property integer $id
 * @property string $tags_name
 * @property string $hit_count
 *
 * The followings are the available model relations:
 * @property PostTags[] $postTags
 */
class Gcm extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tds_gcm_ids';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('gcm_id', 'required'),
			array('id, gcm_id, device_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'gcm_id' => 'Gcm Id',
			'device_id' => 'Device Id',
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
		$criteria->compare('gcm_id',$this->gcm_id,true);
		$criteria->compare('device_id',$this->device_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Tag the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getGcmDeviceId($device_id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id';
            $criteria->compare('device_id', $device_id);
            $criteria->limit = 1;
            $obj_gcm = $this->find($criteria);
            
            if($obj_gcm)
            {
              return $obj_gcm->id;
            }    

            return false;
        }
        public function getGcm($gcm_id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id';
            $criteria->compare('gcm_id', $gcm_id);
            $criteria->limit = 1;
            $obj_gcm = $this->find($criteria);
            
            if($obj_gcm)
            {
                return $obj_gcm->id;
            }    

            return false;
        }
        public function getAllGcm()
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.gcm_id';
            $obj_gcm = $this->findAll($criteria);
            
            $gcm_ids = array();
            
            if($obj_gcm)
            {
                foreach($obj_gcm as $value)
                {
                    $gcm_ids[] = $value->gcm_id;
                }    
            }    

            return $gcm_ids;
        }
        
        
}
