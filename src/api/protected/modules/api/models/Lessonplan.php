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
class Lessonplan extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total = 0;
	public function tableName()
	{
		return 'lessonplans';
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
                        'category' => array(self::BELONGS_TO, 'lessonplancategory', 'lessonplan_category_id')
                );
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
        public function getLessonPlanTotal($batch_id, $lessonplan_category_id)
        {
           
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            $criteria->compare('t.lessonplan_category_id', $lessonplan_category_id);
            $criteria->compare('t.author_id', Yii::app()->user->profileId);
            $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
            
            $data = $this->find($criteria);
            return $data->total;
        } 
        public function getLessonPlanSingle($id)
        {
            
            
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.id', $id);
            $criteria->limit = 1;    
            $value = $this->with("category")->find($criteria);
            if($value)
            {
                $response_array['category']   = $value["category"]->name;
                $response_array['title']   = $value->title;
                $response_array['content']   = $value->content;
                $response_array['publish_date']   = $value->publish_date;
                $response_array['is_show']   = $value->is_show;
                $response_array['subjects'] = "";
                $subjectobj = new Subjects();
                if($value->subject_ids)
                {
                    $sub_array = explode(",", $value->subject_ids);
                    $subject_names = $subjectobj->getSubjectFullName($sub_array);
                    if($subject_names)
                    {
                        $response_array['subjects'] = implode(", ", $subject_names);
                    }
                    
                }  
                
                
            }  
            return $response_array;
            
        }
        
        
        public function getLessonPlan($batch_id, $lessonplan_category_id,$page=1,$page_size)
        {
            
            
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.lessonplan_category_id', $lessonplan_category_id);
            $criteria->compare('t.author_id', Yii::app()->user->profileId);
            $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
            
            $criteria->order = "t.created_at DESC";
            $start = ($page-1)*$page_size;
            $criteria->limit = $page_size;

            $criteria->offset = $start;
            
            $data = $this->with("category")->findAll($criteria);
            $response_array = array();
            if($data != NULL)
            foreach($data as $value)
            {
                $marge = array();
                
                $marge['id']   = $value->id;
                
                $marge['category']   = $value["category"]->name;
                $marge['title'] = $value->title;
              
                $marge['subjects'] = "";
                $subjectobj = new Subjects();
                if($value->subject_ids)
                {
                    $sub_array = explode(",", $value->subject_ids);
                    $subject_names = $subjectobj->getSubjectFullName($sub_array);
                    if($subject_names)
                    {
                        $marge['subjects'] = implode(", ", $subject_names);
                    }
                    
                }    
                
                $response_array[] = $marge;     
                
            }
            return $response_array;
            
        }
        
        
        
        
}
