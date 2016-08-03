<?php

/**
 * This is the model class for table "tds_post_category".
 *
 * The followings are the available columns in table 'tds_post_category':
 * @property integer $id
 * @property string $post_id
 * @property integer $category_id
 * @property integer $inner_priority
 *
 * The followings are the available model relations:
 * @property Categories $category
 * @property Post $post
 */
class PostSchoolShare extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public $total = 0;

    public function tableName()
    {
        return 'tds_post_share';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('post_id, school_id, user_id', 'required'),
            array('post_id, school_id, user_id', 'numerical', 'integerOnly' => true)
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
            'freeUser' => array(self::BELONGS_TO, 'Freeusers', 'user_id'),
        );
    }

   
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getSchoolSharePost($school_id,$id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        $criteria->compare("t.school_id", $school_id);
        $criteria->compare("t.post_id", $id);
        $criteria->limit = 1;
        $obj_post = $this->find($criteria);
        if($obj_post)
        {
            return true;
        }
        else
        {
            return false;
        }    
        
    } 
    
}
