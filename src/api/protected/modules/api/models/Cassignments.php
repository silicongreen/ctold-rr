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
class Cassignments extends CActiveRecord
{
	
	public function tableName()
	{
		return 'tds_assessment';
	}
        
	public function relations()
	{
          
		return array(
                    'question' => array(self::HAS_MANY, 'Cquestion', 'assesment_id'),
                    'post' => array(self::HAS_MANY, 'Post', 'assesment_id'),
		);
	}
        
               
        
        public function getAssessment($id)
        {   
           
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.id', $id); 
            $criteria->with = array(
                'question' => array(
                    'select' => 'question.id,question.mark,question.style',
                    'with' => array(
                        "option" => array(
                            "select" => "option.answer,option.answer_image,option.correct"
                        )
                    )
                )
            );
            
            
            $data = $this->find($criteria);
             
            
            
            $response_array = array();
            $assesment_valid = false;
            if($data != NULL)
            {
                if(isset($data['question']) && count($data['question']>0))
                {
                    foreach($data['question'] as $questions)
                    {
                       if(isset($questions['option']) && count($questions['option']>1))
                       {
                           $assesment_valid = true;
                           break;
                       }    
                    }    
                }
                
                if($assesment_valid)
                {
                    
                    $response_array['id'] = $data->id;
                    $response_array['title'] = $data->title;
                    $response_array['use_time'] = $data->use_time;
                    $response_array['time'] = $data->time; 
                    $response_array['played'] = $data->played; 
                    $response_array['created_date'] = $data->created_date;
                    
                    $response_array['question'] = array();
                    
                    $i = 0;
                    
                    foreach($data['question'] as $questions)
                    {
                        if(isset($questions['option']) && count($questions['option']>1))
                        {
                            $response_array['question'][$i]['question'] = $questions->question;
                            $response_array['question'][$i]['mark'] = $questions->mark;
                            $response_array['question'][$i]['style'] = $questions->style;
                            $response_array['question'][$i]['created_date'] = $questions->created_date;
                            
                            $response_array['question'][$i]['option'] = array();
                            
                            $j = 0;
                            foreach($questions['option'] as $options)
                            {
                               $response_array['question'][$i]['option'][$j]['answer'] = $options->answer;
                               $response_array['question'][$i]['option'][$j]['answer_image'] = $options->answer_image;
                               $response_array['question'][$i]['option'][$j]['correct'] = $options->correct;
                               
                               $j++;
                            } 
                            
                            $i++;
                            
                        }    
                    }
                    
                    
                }
                    
            }
            return $response_array;
            
        }
        
       
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
