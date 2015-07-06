<?php

/**
 * This is the model class for table "events".
 *
 * The followings are the available columns in table 'events':
 * @property integer $id
 * @property integer $event_category_id
 * @property string $title
 * @property string $description
 * @property string $start_date
 * @property string $end_date
 * @property integer $is_common
 * @property integer $is_holiday
 * @property integer $is_exam
 * @property integer $is_due
 * @property string $created_at
 * @property string $updated_at
 * @property integer $origin_id
 * @property string $origin_type
 * @property integer $school_id
 */
class Highscore extends CActiveRecord
{

    public $rank = 0;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tds_spell_highscore';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('play_total_time, spell_year', 'required', 'on' => 'insert')
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
            'UserFree' => array(self::BELONGS_TO, 'Freeusers', 'userid'),
            'Countries' => array(self::BELONGS_TO, 'Countries', 'country'),
        );
        
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    public function getUserRank( $score_for_rank,$time_for_rank,$country,$division="" )
    {
        if($score_for_rank==0)
        {
            return 0;
        }    
        $criteria = new CDbCriteria;
        $criteria->select = 'count(t.id)+1 AS rank';
        if ($division)
        {
            $criteria->compare('t.division', $division);
        }
        else
        {
            $criteria->compare('t.country', $country);
        }
        $criteria->compare('t.is_cancel', 0);
        $criteria->addCondition("t.score > ".$score_for_rank." OR (t.score = ".$score_for_rank." AND t.test_time < ".$time_for_rank." ) ");
        $data = $this->find($criteria);
        if($data)
        {
            return $data->rank;
        }
        else
        {
            return 0; 
        }    
    }
    
    public function getUserScore($user_id,$with_cancel=false)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id,t.play_total_time,t.score,t.test_time';
        $criteria->compare('t.userid', $user_id);
        if($with_cancel===false)
        $criteria->compare('t.is_cancel', 0);
        $criteria->order = 't.score DESC';
        $data = $this->find($criteria);
        return $data;
        
    }        

    public function getLeaderBoard($iLimit = 10, $division = "",$country = "")
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.userid,t.division,t.score,SEC_TO_TIME(t.test_time) as test_time';

        $criteria->with = array(
            'UserFree' => array(
                'select' => 'UserFree.email,UserFree.school_name,UserFree.profile_image,'
                . 'UserFree.first_name,UserFree.middle_name,UserFree.last_name',
                'joinType' => "INNER JOIN"
            ),
            
            /** Country join was throwing away user without country id 
             * even if it was the highest scorer**/
            
//            'Countries' => array(
//                'select' => 'Countries.name',
//                'joinType' => "INNER JOIN"
//            )
        );
        $division = strtolower($division);

        if ($division)
        {
            $criteria->compare('t.division', $division);
        }
        else
        {
//            $criteria->compare('t.country', $country);
        }    

        $criteria->compare('t.is_cancel', 0);
        $criteria->compare('t.spell_year', date('Y'));
        $criteria->addCondition("t.userid >0  AND t.score > 0 ");
        $criteria->order = "t.score DESC, test_time ASC";
        $criteria->limit = $iLimit;
        $data = $this->findAll($criteria);
        $arScoresData = array();
        if ($data)
        {
           
            foreach ($data as $value)
            {
//                if (isset($value['UserFree']->email))
//                {
                    $arUser = array();
                    $arUser['high_score'] = $value->score;
                    $arUser['time'] = (!empty($value->test_time)) ? $value->test_time : '00:50:50';
                    if($value['UserFree']->school_name)
                    {
                        $arUser['school_name'] = $value['UserFree']->school_name;
                    }
                    else
                    {
                       $arUser['school_name'] = "Champs21"; 
                    }    
                    $arUser['profile_image'] = $value['UserFree']->profile_image;

                    $middle_name = ($value["UserFree"]->middle_name) ? $value["UserFree"]->middle_name . ' ' : ' ';
                    $students_name = rtrim($value["UserFree"]->first_name . $middle_name . $value["UserFree"]->last_name);
                    $arUser['user_fullname'] = $students_name;

                    if ($division)
                    {
                        $arUser['division_name'] = $value->division;
                        $arUser['is_local'] = 1;
                    }
                    else
                    {
                        $arUser['division_name'] = $value->division;
//                        $arUser['division_name'] = substr($value['Countries']->name, 0, strpos($value['Countries']->name," ("));
                        $arUser['is_local'] = 0;
                    }
                    array_push( $arScoresData, ( object ) $arUser );
                
//                }
            }
        }

        return $arScoresData;
    }

}
