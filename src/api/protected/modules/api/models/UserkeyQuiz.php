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
class UserkeyQuiz extends MyActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
    
        public function getDbConnection()
        {
            return self::getAdvertDbConnection2();
        }
       
	public function tableName()
	{
		return 'user_key';
	}
        
        public function getQuizUser($previous_id)
        {


            $criteria = new CDbCriteria;
            $criteria->compare('t.batch_id', $batch_id);
            $criteria->compare('t.is_deleted', 0);
            $criteria->addCondition("t.is_published = 1 and (t.is_common = 1 or FIND_IN_SET(".$student_id.",t.students))");
            $criteria->select = 't.*';
            $criteria->order = "published_date DESC";
            $connect_exam = $this->findAll($criteria);
            return $connect_exam;
        }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id,has_key', 'required'),
			array('id, user_id, has_key, expiry_date', 'safe', 'on'=>'search'),
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
        
        
        
}
