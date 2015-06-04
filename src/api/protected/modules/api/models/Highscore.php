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

    public $num_rows = 0;

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
            array('play_total_time, spell_year, division', 'required', 'on' => 'insert')
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

    public function getLeaderBoard($iLimit = 10, $division = "",$country = "")
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.userid,t.score,SEC_TO_TIME(t.test_time) as test_time';

        $criteria->with = array(
            'UserFree' => array(
                'select' => 'UserFree.email,UserFree.school_name,UserFree.profile_image,'
                . 'UserFree.first_name,UserFree.middle_name,UserFree.last_name',
                'joinType' => "INNER JOIN"
            ),
            'Countries' => array(
                'select' => 'Countries.name',
                'joinType' => "INNER JOIN"
            )
        );
        $division = strtolower($division);

        if ($division)
        {
            $criteria->compare('t.district', $division);
        }
        else
        {
            $criteria->compare('t.country', $country);
        }    

        $criteria->compare('t.is_cancel', 0);
        $criteria->compare('t.spell_year', date('Y'));
        $criteria->addCondition("t.userid >0  AND t.score > 0 ");
        $criteria->order = "t.score DESC,test_time ASC";
        $criteria->limit = $iLimit;
        $data = $this->findAll($criteria);
        $arScoresData = array();
        if ($data)
        {
            $i = 0;

            foreach ($data as $value)
            {
                if (isset($value['UserFree']->email))
                {

                    $arScoresData[$i]['high_score'] = $value->score;
                    $arScoresData[$i]['time'] = $value->test_time;
                    if($value['UserFree']->school_name)
                    {
                        $arScoresData[$i]['school_name'] = $value['UserFree']->school_name;
                    }
                    else
                    {
                       $arScoresData[$i]['school_name'] = "Champs21"; 
                    }    
                    $arScoresData[$i]['profile_image'] = $value['UserFree']->profile_image;

                    $middle_name = ($value["UserFree"]->middle_name) ? $value["UserFree"]->middle_name . ' ' : ' ';
                    $students_name = rtrim($value["UserFree"]->first_name . $middle_name . $value["UserFree"]->last_name);
                    $arScoresData[$i]['user_fullname'] = $students_name;

                    if ($division)
                    {
                        $arScoresData[$i]['division_name'] = $value->division;
                        $arScoresData[$i]['is_local'] = 1;
                    }
                    else
                    {
                        $arScoresData[$i]['division_name'] = substr($value['Countries']->name, 0, strpos($value['Countries']->name," ("));
                        $arScoresData[$i]['is_local'] = 0;
                    }
                    $i++;
                }
            }
        }

        return $arScoresData;
    }

}
