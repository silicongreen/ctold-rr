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
class Spellingbee extends CActiveRecord
{

    public $rank = 0;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tds_spellingbee_test';
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

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    public function getWordsByLevel( $iLevel, $iMaxWord = 25,$user_word_played = array(),$iUserId=0  )
    {
       
        $criteria = new CDbCriteria;
        $criteria->select = 't.*';
        $criteria->compare('t.level', $iLevel);
        $criteria->compare('t.enabled', 1);
        
        if(count($user_word_played)>0)
        {
            $criteria->addNotInCondition('t.id', $user_word_played);
        }
        $criteria->order = 't.year DESC, RAND()';
        
        $criteria->limit = $iMaxWord;
        $data = $this->findAll($criteria);
        
        if(count($data)==0)
        { 
            $cache_name = "YII-SPELLINGBEE-LEVEL-STATUS";  
            $levelstatus = Settings::getSpellingBeeCache($cache_name);
            $all_clear = true;
            for($i =0; $i<4; $i++)
            {
                if(!isset($levelstatus) || !isset($levelstatus[$iUserId]) || !isset($levelstatus[$iUserId][$i]))
                {
                    $all_clear = false;
                    break;
                }
            }
            
            if($all_clear)
            {
                unset($levelstatus[$iUserId]);
                Settings::setSpellingBeeCache($cache_name, $levelstatus);
                $data = $this->getWordsByLevel( $iLevel, $iMaxWord,array(),$iUserId);
                return $data;
                
            }    
            else
            {    
                $response['words'] = array();
                $response['word_complete'] = 1;
                $response['level'] = $iLevel; 
            }
        }
        else
        {
            $response['words'] = $data;
            $response['word_complete'] = 0;
            $response['level'] = $iLevel;
            if(count($data)!=$iMaxWord)
            {
                $response['word_complete'] = 1;
                $cache_name = "YII-SPELLINGBEE-LEVEL-STATUS";
                $responsecache[$iUserId][$iLevel] = 1;
                Settings::setSpellingBeeCache($cache_name, $responsecache);
            }
        }
        return $response;
    }

}
