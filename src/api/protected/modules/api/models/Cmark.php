<?php

/**
 * This is the model class for table "assignments".
 *
 * The followings are the available columns in table 'assignments':
 * @property integer $id
 * @property integer $employee_id
 * @property integer $subject_id
 * @property string $student_list
 * @property string $title
 * @property string $content
 * @property string $duedate
 * @property string $attachment_file_name
 * @property string $attachment_content_type
 * @property integer $attachment_file_size
 * @property string $attachment_updated_at
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Cmark extends CActiveRecord
{
	
	public function tableName()
	{
		return 'tds_assesment_mark';
	}
        
	public function relations()
	{
            return array(
                'assessment' => array(self::BELONGS_TO, 'Cassignments', 'assessment_id')
            );
	}
        public function getUserMark($user_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 't.mark,t.created_date';
            $criteria->compare('t.user_id', $user_id); 
            $criteria->with = array(
                'assessment' => array(
                    'select' => 'assessment.title'
                )
            ); 
           $data = $this->findAll($criteria); 
           $response_array = array();
           
           if($data != NULL)
           {
               $i = 0;
               foreach($data as $value)
               {
                   $response_array[$i]['mark'] = $value->mark;
                   $response_array[$i]['created_date'] = Settings::get_post_time($value->created_date);
                   $response_array[$i]['title'] = $value['assessment']->title;
                   $i++;
               }
               
           } 
           return $response_array;
        }
        public function getUserMarkAssessment($user_id,$assessment_id)
        {
           $criteria = new CDbCriteria();
           $criteria->select = 't.mark,t.id';
           $criteria->compare('t.user_id', $user_id); 
           $criteria->compare('t.assessment_id', $assessment_id); 
            
           $data = $this->find($criteria); 
          
           if($data != NULL)
           {
               return $data;
               
           } 
           return false;
        }  
        
        
       
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
