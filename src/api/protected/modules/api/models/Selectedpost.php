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
class Selectedpost extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tds_selected_post';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('category_id,post_id,position', 'required'),
			array('id, category_id, post_id, position', 'safe', 'on'=>'search'),
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
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Tag the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getSelectedPost($category_id=0)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.post_id,t.position';
            $criteria->order = "t.position ASC";
            $criteria->compare('category_id', $category_id);
            $criteria->limit = 10;
            $obj_pin_post = $this->findAll($criteria);
            $pin_post = array();
            
            if($obj_pin_post)
            foreach($obj_pin_post as $value)
            {
              
                $pin_post[$value->position] = $value->post_id;
            }    
            return $pin_post;
            
        }
        
        
        
}
