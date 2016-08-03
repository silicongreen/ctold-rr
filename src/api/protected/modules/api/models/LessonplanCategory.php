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
class LessonplanCategory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total = 0;
	public function tableName()
	{
		return 'lessonplan_categories';
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
		return array(
                );
	}
        
        public function getUserCategory($id = 0,$return_selcted_category=false)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id,t.name';
            $criteria->compare("t.author_id", Yii::app()->user->id);
            $criteria->compare("t.status", 1);
            $obj_categopry = $this->findAll($criteria);
            $category = array();
            if($obj_categopry)
            {
                $i = 0;
                $category_selected_id = 0;
                if($id>0)
                {
                    $lessonplan = new Lessonplan();
                    $lessonplan = $lessonplan->findByPk($id); 
                    $category_selected_id = $lessonplan->lessonplan_category_id;
                }    
                foreach ($obj_categopry as $value)
                {
                    if($return_selcted_category)
                    {
                      
                        if($category_selected_id == $value->id)
                        {
                            $category[$i]['id'] = $value->id;
                            $category[$i]['name'] = $value->name;
                            $i++;
                        }
                          
                    }
                    else
                    {
                        $category[$i]['id'] = $value->id;
                        $category[$i]['name'] = $value->name;
                        $category[$i]['selected'] = 0;
                        if($category_selected_id == $value->id)
                        {
                            $category[$i]['selected'] = 1;
                        }
                        $i++;  
                    }
                    
                }    
                
            }
            
            return $category;
               
        
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
        
        
        
        
}
