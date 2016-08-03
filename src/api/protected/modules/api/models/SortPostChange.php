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
class SortPostChange extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tds_sorting_change';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
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
        public function checkNewsUpdated($update_date,$post_type=0,$category_id=0)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id';
            $criteria->compare('t.post_type', $post_type);;
            $criteria->compare('t.category_id', $category_id);
            $criteria->addCondition("t.updated_date > '" . $update_date. "'");
            $criteria->limit = 1;
            $obj_news_update = $this->find($criteria);
            $pin_post = array();
            
            if($obj_news_update)
            {
                return true;
            }
            else
            {
                return false;
            }   
            
        }
        
        
        
}
