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
class LastVisited extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'last_visited';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id', 'required')
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
			'user_id' => 'User',
			'last_visited' => 'Last Visited',
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
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('last_visited',$this->last_visited,true);

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
        public function getLastVisited()
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.last_visited';
            $criteria->compare('user_id', Yii::app()->user->id);
            $criteria->limit = 1;
            $obj = $this->find($criteria);
            
            if($obj)
            {
                return Settings::get_post_time($obj->last_visited,3);
            }    

            return false;
        }
        public function addLastVisited()
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id';
            $criteria->compare('user_id', Yii::app()->user->id);
            $criteria->limit = 1;
            $obj = $this->find($criteria);
            
            if($obj)
            {
                $lastvisited = new LastVisited();
                $lastvisited = $lastvisited->findByPk($obj->id);
                $lastvisited->last_visited = date("Y-m-d H:i:s");
            } 
            else
            {
                $lastvisited = new LastVisited();
                $lastvisited->last_visited = date("Y-m-d H:i:s");
                $lastvisited->user_id = Yii::app()->user->id;
                
            }  
            $lastvisited->save();
        }
       
        
        
}
