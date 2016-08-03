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
                        'category' => array(self::BELONGS_TO, 'LessonplanCategory', 'lessonplan_category_id')
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
        public function getLessonPlanTotal($subject_id, $batch_id=0, $lessonplan_category_id=0)
        {
           
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            if($lessonplan_category_id)
            $criteria->compare('t.lessonplan_category_id', $lessonplan_category_id);
            
            $criteria->compare('t.author_id', Yii::app()->user->id);
            
            if($batch_id)
            $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
            
            if($subject_id)
            $criteria->addCondition("FIND_IN_SET(".$subject_id.", subject_ids)");
            
            $data = $this->find($criteria);
            return $data->total;
        } 
        public function getLessonPlanLastUpdated($subject_id, $batch_id=0)
        {
           
            $criteria = new CDbCriteria();
            $criteria->select = 't.publish_date';
            
            $criteria->compare('t.is_show', 1);
            $criteria->addCondition('t.publish_date IS NOT NULL AND t.publish_date<="'.date('Y-m-d').'"');
            
            
            if($batch_id)
            $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
            
            if($subject_id)
            $criteria->addCondition("FIND_IN_SET(".$subject_id.", subject_ids)");
            
            $criteria->limit = 1;
            $criteria->order = "t.publish_date DESC";
            
            $data = $this->find($criteria);
            return $data->publish_date;
        }
        
        public function getLessonPlanStudent($subject_id = 0, $batch_id = 0, $page = 1, $page_size)
        {
            
            
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.is_show', 1);
            $criteria->addCondition('t.publish_date IS NOT NULL AND t.publish_date<="'.date('Y-m-d').'"');
            
            if($batch_id)
            $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
            
            if($subject_id)
            $criteria->addCondition("FIND_IN_SET(".$subject_id.", subject_ids)");
            
            $criteria->order = "t.publish_date DESC";
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
                $marge['title'] = $value->title;
                $marge['publish_date'] = $value->publish_date;
                $marge['attachment_file_name'] = ""; 
                if($value->attachment_file_name)
                {
                    $marge['attachment_file_name'] = $value->attachment_file_name;
                }
                $response_array[] = $marge;     
                
            }
            return $response_array;
            
        }
        public function getLessonPlanTotalStudent($subject_id, $batch_id=0)
        {
           
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            
            $criteria->compare('t.is_show', 1);
            $criteria->addCondition('t.publish_date IS NOT NULL AND t.publish_date<="'.date('Y-m-d').'"');
            
            if($batch_id)
            $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
            
            if($subject_id)
            $criteria->addCondition("FIND_IN_SET(".$subject_id.", subject_ids)");
            
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
                $response_array['publish_date'] = "";
                if($value->publish_date)
                $response_array['publish_date']   = $value->publish_date;
                $response_array['is_show']   = $value->is_show;
                $response_array['attachment_file_name'] = ""; 
                if($value->attachment_file_name)
                {
                    $response_array['attachment_file_name'] = $value->attachment_file_name;
                }
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
        
        
        public function getLessonPlan($subject_id = 0, $batch_id = 0, $lessonplan_category_id = 0, $page = 1, $page_size)
        {
            
            
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            if($lessonplan_category_id)
            $criteria->compare('t.lessonplan_category_id', $lessonplan_category_id);
            
            $criteria->compare('t.author_id', Yii::app()->user->id);
            
            if($batch_id)
            $criteria->addCondition("FIND_IN_SET(".$batch_id.", batch_ids)");
            
            if($subject_id)
            $criteria->addCondition("FIND_IN_SET(".$subject_id.", subject_ids)");
            
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
                $marge['is_show'] = $value->is_show;
                $marge['attachment_file_name'] = ""; 
                if($value->attachment_file_name)
                {
                    $marge['attachment_file_name'] = $value->attachment_file_name;
                }
              
                $marge['subjects'] = "";
                $subjectobj = new Subjects();
                if($value->subject_ids)
                {
                    $sub_array = ($subject_id) ? $subject_id : explode(",", $value->subject_ids);
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
