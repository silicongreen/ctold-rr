<?php

/**
 * This is the model class for table "tds_related_news".
 *
 * The followings are the available columns in table 'tds_related_news':
 * @property integer $id
 * @property string $post_id
 * @property string $new_link
 * @property string $title
 * @property string $published_date
 * @property integer $related_type
 *
 * The followings are the available model relations:
 * @property Post $post
 */
class RelatedNews extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tds_related_news';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('published_date', 'required'),
			array('related_type', 'numerical', 'integerOnly'=>true),
			array('post_id, published_date', 'length', 'max'=>20),
			array('new_link', 'length', 'max'=>255),
			array('title', 'length', 'max'=>150),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, post_id, new_link, title, published_date, related_type', 'safe', 'on'=>'search'),
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
			'post' => array(self::BELONGS_TO, 'Post', 'post_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'post_id' => 'Post',
			'new_link' => 'New Link',
			'title' => 'Title',
			'published_date' => 'Published Date',
			'related_type' => 'Related Type',
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
		$criteria->compare('post_id',$this->post_id,true);
		$criteria->compare('new_link',$this->new_link,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('published_date',$this->published_date,true);
		$criteria->compare('related_type',$this->related_type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        public function getRelatedNews($post_id)
        {
            $criteria=new CDbCriteria;
            $criteria->compare('post_id',$post_id);
            $obj_releted = $this->findAll($criteria);
                    
            $post_array = array();
            $i = 0;
            if($obj_releted)
            foreach ($obj_releted as $postValue)
            {
                $news_link_array = explode("-", $postValue->new_link);
                $news_id = $news_link_array[count($news_link_array)-1];
                
                $post_array[$i]['id']     = $news_id;           
                $i++;
            }
            return $post_array;
        }        

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RelatedNews the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
