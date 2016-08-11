<?php

/**
 * This is the model class for table "tds_dailydose".
 *
 * The followings are the available columns in table 'tds_dailydose':
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property string $summary
 * @property integer $status
 * @property string $date
 */
class TdsDailydose extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total;
	public function tableName()
	{
		return 'tds_dailydose';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content, date', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			array('summary', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, content, summary, status, date', 'safe', 'on'=>'search'),
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
			'title' => 'Title',
			'content' => 'Content',
			'summary' => 'Summary',
			'status' => 'Status',
			'date' => 'Date',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('summary',$this->summary,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('date',$this->date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdsDailydose the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getdailydosesingle($id)
        {
            $criteria=new CDbCriteria;
            $criteria->select = 't.id, t.title,t.share_content, t.image_link, t.summary, t.content, t.date';
            $criteria->compare('id',$id);
            $data = $this->find($criteria);
            
            if($data)
            {
                if(!$data->share_content)
                {
                   $data->share_content = $data->content;
                }
                $data->image_link = Settings::content_single_images($data->share_content);
                
            } 
            if(!$data)
            {
                $x = new stdClass();
                return $x;
            }    
            return $data;  
        } 
        
        public function getdailydose()
        {
            $criteria=new CDbCriteria;
            $criteria->select = 't.id, t.title,t.share_content, t.image_link, t.summary, t.content, t.date';
            $criteria->compare('date',date("Y-m-d"));
            $data = $this->find($criteria);
            
            if($data)
            {
            
                $criteria=new CDbCriteria;
                $criteria->select = 't.id, t.title,t.share_content, t.image_link, t.summary, t.content, t.date';
                $criteria->order = 't.date DESC';
                $criteria->limit = 1;
                $data = $this->find($criteria);
                if(!$data->share_content)
                {
                   $data->share_content = $data->content;
                }
                $data->image_link = Settings::content_single_images($data->share_content);
                
            } 
            if(!$data)
            {
                $x = new stdClass();
                return $x;
            }    
            return $data;  
        } 
        public function getdailydoseCount()
        {
            $criteria=new CDbCriteria;
            $criteria->select = 'count(t.id) as total';
            $criteria->addCondition("date<='".date("Y-m-d")."'");
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
        
        public function getdailydoseSearch($term)
        {
            $criteria=new CDbCriteria;
            $criteria->select = 't.id, t.title,t.share_content, t.image_link, t.summary, t.content, t.date';
            $criteria->order = 't.date DESC';
            $criteria->addCondition("t.content like '%".$term."%'");
            $criteria->limit = 5;
            $data = $this->findAll($criteria);
            $dailydose = array();
            foreach($data as $value)
            {
                if(!$value->share_content)
                {
                   $value->share_content = $value->content;
                }
                $value->image_link = Settings::content_single_images($value->share_content);
                $dailydose[] = $value;
                
            }    
            
            return $data;
        }
        
        
        public function getdailydoseAll($page = 1, $page_size = 10)
        {
            $criteria=new CDbCriteria;
            $criteria->select = 't.id, t.title,t.share_content, t.image_link, t.summary, t.content, t.date';
            $criteria->order = 't.date DESC';
            $criteria->addCondition("date<='".date("Y-m-d")."'");
            $start = ($page - 1) * $page_size;
            $criteria->limit = $page_size;
            $criteria->offset = $start;
            $data = $this->findAll($criteria);
            $dailydose = array();
            foreach($data as $value)
            {
                if(!$value->share_content)
                {
                   $value->share_content = $value->content;
                }
                $value->image_link = Settings::content_single_images($value->share_content);
                $dailydose[] = $value;
                
            }    
            
            return $data;
        }
}
