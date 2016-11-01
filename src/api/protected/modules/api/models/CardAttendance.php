<?php

/**
 * This is the model class for table "Active Logs".
 *
 * The followings are the available columns in table 'Active Logs':
 * @property integer $id
 * @property string $tags_name
 * @property string $hit_count
 *
 * The followings are the available model relations:
 */
class CardAttendance extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $maxtime;
        public $mintime;
        public $total;
	public function tableName()
	{
		return 'card_attendance';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id,school_id,date,time', 'required'),
			array('id, user_id,school_id,date,time', 'safe', 'on'=>'search'),
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
			'user_id' => 'User Id',
			'school_id' => 'School Id',
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
		$criteria->compare('school_id',$this->school_id,true);

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
        public function getCampusAttendanceCount($user_id,$profile_id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 'count(DISTINCT date) as total';
            $criteria->compare('user_id',$user_id);
            $data = $this->find($criteria);
            if($data)
            {
                return $data->total;
            }
            else
            {
                return 0;
            }    
            
        }
        
        public function getCampusAttendanceDate($user_id,$profile_id,$start_date,$end_date)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 'max(t.time) as maxtime,min(t.time) as mintime,t.date';
            $criteria->compare('user_id',$user_id);
            $criteria->addCondition("t.date >= '$start_date' && t.date<='$end_date'");
            $criteria->order = 't.date DESC';
            $criteria->group = 't.date';
            
            $data = $this->findAll($criteria);
            $att = array();
            $att_array = array();
            if($data)
            {
                $i = 0;
                foreach($data as $value)
                {
                    $att[$i]['in_time'] = $value->mintime;
                    $att[$i]['out_time'] = $value->maxtime;
                    $att[$i]['date'] = $value->date;
                    $i++;
                }    
            }
            return $att;
            
        } 
        
        
        public function getCampusAttendance($user_id,$profile_id,$page_number = 1, $page_size = 10)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 'max(t.time) as maxtime,min(t.time) as mintime,t.date';
            $criteria->compare('user_id',$user_id);
            $criteria->order = 't.date DESC';
            $criteria->group = 't.date';
            $start = ($page_number - 1) * $page_size;
            $criteria->limit = $page_size;
            $criteria->offset = $start;
            $data = $this->findAll($criteria);
            $att = array();
            if($data)
            {
                $i = 0;
                foreach($data as $value)
                {
                    $att[$i]['in_time'] = $value->mintime;
                    $att[$i]['out_time'] = $value->maxtime;
                    $att[$i]['date'] = $value->date;
                    $i++;
                }    
            }
            return $att;
            
        }        
        
        
        
}
