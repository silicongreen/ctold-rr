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

    public $maxmark = 0;
    public function tableName()
    {
        return 'tds_assesment_mark';
    }

    public function relations()
    {
        return array(
            'assessment' => array(self::BELONGS_TO, 'Cassignments', 'assessment_id'),
            'freeUser' => array(self::BELONGS_TO, 'Freeusers', 'user_id')
        );
    }
    public function assessmentHighistMark($id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'MAX(t.mark) as maxmark';
        $criteria->compare('t.assessment_id', $id);
        
        $data = $this->find($criteria); 
        if(isset($data->maxmark) && $data->maxmark)
        {
           return  $data->maxmark;
        }
        else
        {
           return 0;
        }
         
    }
    public function getTopMark($id, $limit = 100, $user_id = 0, $type = 0)
    {
        $criteria = new CDbCriteria();
        if($type == 2) {
            $criteria->select = 't.user_id, SUM(t.mark) AS mark, t.level, t.created_date, SUM(t.time_taken) AS time_taken, SUM(t.no_played) AS no_played, SUM(t.avg_time_per_ques) AS avg_time_per_ques';
            $criteria->group = 't.user_id';
        } else{
            $criteria->select = 't.mark AS mark, t.user_id, t.level, t.created_date, t.time_taken AS time_taken, t.no_played AS no_played';
        }
        
        $criteria->compare('assessment_id', $id);
        
        if(!empty($user_id) && ($user_id > 0)) {
            $criteria->compare('t.user_id', $user_id);
        }
        
        $criteria->order = "mark DESC, time_taken ASC, no_played ASC, avg_time_per_ques ASC, t.created_date ASC";
        $criteria->limit = $limit;
        $criteria->with = array(
            'assessment' => array(
                'select' => 'assessment.id, assessment.title, assessment.topic',
                'with' =>array('question' => array(
                        'select' => 'question.mark'
                    ),
                    'post' => array(
                        'select' => 'post.id'
                    )
                )
            ),
            'freeUser' => array(
                'select' => 'freeUser.first_name, freeUser.middle_name, freeUser.last_name, freeUser.email, freeUser.profile_image, freeUser.school_name'
            )        
        );
        $data = $this->findAll($criteria);
        $response_array = array();

        if ($data != NULL)
        {
            $i = 0;
            foreach ($data as $value)
            {
                $user_name = "";
                $image = "";
                $school = "";
                if(isset($value['freeUser']->profile_image))
                {
                    $image = $value['freeUser']->profile_image;
                } 
                if(isset($value['freeUser']->first_name) && $value['freeUser']->first_name)
                {
                    $user_name .= $value['freeUser']->first_name." ";
                }
                if(isset($value['freeUser']->middle_name) && $value['freeUser']->middle_name)
                {
                    $user_name .= $value['freeUser']->middle_name." ";
                }
                if(isset($value['freeUser']->last_name) && $value['freeUser']->last_name)
                {
                    $user_name .= $value['freeUser']->last_name;
                }
                if(!$user_name)
                {
                    if(isset($value['freeUser']->email))
                    $user_name = $value['freeUser']->email;
                }
                if(isset($value['freeUser']->school_name) && $value['freeUser']->school_name)
                {
                    $school = $value['freeUser']->school_name;
                }
                
                $response_array[$i]['user_name'] = $user_name;
                $response_array[$i]['user_id'] = $value->user_id;
                $response_array[$i]['level'] = $value->level;
                $response_array[$i]['school'] = $school;
                $response_array[$i]['profile_image'] = $image;
                $response_array[$i]['pid'] = 0;
                if(isset($value['assessment']['post']) && count($value['assessment']['post'])>0)
                {
                    $response_array[$i]['pid'] = $value['assessment']['post'][0]->id;
                }
                $response_array[$i]['assessment_id'] = $value['assessment']->id;
                
                //$response_array[$i]['highist_mark'] = $this->assessmentHighistMark($value['assessment']->id);
                $response_array[$i]['mark'] = $value->mark;
                $response_array[$i]['time_taken'] = $value->time_taken;
                $response_array[$i]['number_of_attempt'] = $value->no_played;
                $response_array[$i]['created_date'] = $value->created_date;
                $response_array[$i]['title'] = $value['assessment']->title;
                $response_array[$i]['topic'] = $value['assessment']->topic;
                $totalmark = 0;
                if(isset($value['assessment']['question']) && count($value['assessment']['question'])>0)
                {
                    foreach($value['assessment']['question'] as $qvalue)
                    {
                        $totalmark = $totalmark+$qvalue->mark;
                    }    
                }  
                $response_array[$i]['total'] = $totalmark;
                
                $i++;
            }
        }
        return $response_array;
    }
    public function getUserMark($user_id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.mark,t.created_date';
        $criteria->compare('t.user_id', $user_id);
        $criteria->with = array(
            'assessment' => array(
                'select' => 'assessment.id,assessment.title,assessment.topic',
                'with' =>array(
                    'question' => array(
                        'select' => 'question.mark'
                    ),
                    'post' => array(
                        'select' => 'post.id'
                    )
                )
            )
        );
        $data = $this->findAll($criteria);
        $response_array = array();

        if ($data != NULL)
        {
            $i = 0;
            foreach ($data as $value)
            {
                $response_array[$i]['pid'] = 0;
                if(isset($value['assessment']['post']) && count($value['assessment']['post'])>0)
                {
                    $response_array[$i]['pid'] = $value['assessment']['post'][0]->id;
                }
                $response_array[$i]['id'] = $value['assessment']->id;
                $response_array[$i]['highist_mark'] = $this->assessmentHighistMark($value['assessment']->id);
                $response_array[$i]['mark'] = $value->mark;
                $response_array[$i]['created_date'] = $value->created_date;
                $response_array[$i]['title'] = $value['assessment']->title;
                $response_array[$i]['topic'] = $value['assessment']->topic;
                $totalmark = 0;
                if(isset($value['assessment']['question']) && count($value['assessment']['question'])>0)
                {
                    foreach($value['assessment']['question'] as $qvalue)
                    {
                        $totalmark = $totalmark+$qvalue->mark;
                    }    
                }  
                $response_array[$i]['total'] = $totalmark;
                
                $i++;
            }
        }
        return $response_array;
    }

    public function getUserMarkAssessment($user_id, $assessment_id, $type = NULL)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.mark,t.id,t.no_played,t.time_taken,t.level,t.user_id,t.avg_time_per_ques,t.created_date';
        $criteria->compare('t.user_id', $user_id);
        $criteria->compare('t.assessment_id', $assessment_id);
        
        if( empty($type) || $type <= 1) {
            $data = $this->find($criteria);
        } else {
            $data = $this->findAll($criteria);
        }
        

        if ($data != NULL)
        {
            return $data;
        }
        return false;
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}
