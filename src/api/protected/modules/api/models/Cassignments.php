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
                    'post' => array(self::HAS_MANY, 'Post', 'assessment_id'),
		);
	}
        
               
        
        public function getAssessment($id, $webview = false, $type = 0, $level = 0)
        {   
           
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.id', $id);
            
            if($type > 0) {
                $criteria->compare('t.type', $type);
            }
            
            if($level > 0) {
                $criteria->compare('question.level', $level);
            }
            
            $criteria->with = array(
                'question' => array(
                    'select' => 'question.id,question.explanation,question.mark,question.level,question.time,question.style,question.question,question.created_date',
                    'order' => "RAND()",
                    'with' => array(
                        "option" => array(
                            "select" => "option.id,option.answer,option.answer_image,option.correct"
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
                    $response_array['type'] = $data->type;
                    $response_array['type_text'] = Settings::$assessment_config['types'][$data->type];
                    $response_array['use_time'] = $data->use_time;
                    $response_array['time'] = $data->time; 
                    $response_array['played'] = $data->played; 
                    $response_array['topic'] = $data->topic;
                    $response_array['created_date'] = Settings::get_post_time($data->created_date);
                    
                    $response_array['question'] = array();
                    
                    $i = 0;
                    
                    foreach($data['question'] as $questions)
                    {
                        if(isset($questions['option']) && count($questions['option']>1))
                        {
                            $q_image = "";
                            $qimages = Settings::content_images($questions->question);
                            if(count($qimages)>0)
                            {
                                $q_image = $qimages[0];
                            }    
                             
                            $response_array['question'][$i]['id'] = $questions->id;
                            $response_array['question'][$i]['question'] = Settings::substr_with_unicode($questions->question);
                            $response_array['question'][$i]['explanation'] = Settings::substr_with_unicode($questions->explanation);
                            $response_array['question'][$i]['image'] = $q_image;
                            if($webview)
                            {
                                $response_array['question'][$i]['explanation_webview'] = $questions->explanation;
                                $response_array['question'][$i]['question_webview'] = $questions->question;
                            }
                            $response_array['question'][$i]['mark'] = $questions->mark;
                            $response_array['question'][$i]['level'] = $questions->level;
                            $response_array['question'][$i]['time'] = $questions->time;
                            $response_array['question'][$i]['style'] = $questions->style;
                            $response_array['question'][$i]['created_date'] = $questions->created_date;
                            
                            $response_array['question'][$i]['option'] = array();
                            
                            $j = 0;
                            foreach($questions['option'] as $options)
                            {
                               $a_image = "";
                               $images = Settings::content_images($options->answer);
                               if(!$options->answer_image)
                               {
                                   if(count($images)>0)
                                   {
                                       $a_image = $images[0];
                                   }    
                               }  
                               else if($options->answer_image)
                               {
                                   $a_image = $options->answer_image;
                               }
                               $response_array['question'][$i]['option'][$j]['id'] = $options->id;
                               $response_array['question'][$i]['option'][$j]['answer'] = Settings::substr_with_unicode($options->answer);
                               $response_array['question'][$i]['option'][$j]['answer_image'] = $a_image;
                               if($webview)
                               {
                                  $response_array['question'][$i]['option'][$j]['answer_webview'] = $options->answer;
                               }
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
        
        public function getAssessmentLevels($assessment_id){
            
            $response_array = array();
            
            $levels = Yii::app()->db->createCommand()->select('GROUP_CONCAT( DISTINCT `level`) AS `levels`')->from('tds_assessment_question q')->where('q.assesment_id=:a_id', array(':a_id' => $assessment_id))->queryRow();
            return $response_array = $levels['levels'];
        }
        
       
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
