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
        return 'tds_spellingbee';
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
    public function getWordsByLevel( $iLevel, $iMaxWord = 25, $iYear = 2015,$user_word_played = array(),$iUserId=0  )
    {
        $cache_name = "YII-SPELLINGBEE-USERYEAR";
        $responsecache = Yii::app()->cache->get($cache_name);
        if (isset($responsecache[$iUserId]) && isset($responsecache[$iUserId][$iLevel]) && isset($responsecache[$iUserId][$iLevel]['year']))
        {
            $iYear = $responsecache[$iUserId][$iLevel]['year'];
        }
        
        $criteria = new CDbCriteria;
        $criteria->select = 't.*';
        $criteria->compare('t.level', $iLevel);
        $criteria->compare('t.year', $iYear);
        $criteria->compare('t.enabled', 1);
        
        if(count($user_word_played)>0)
        {
            $criteria->addNotInCondition('t.id', $user_word_played);
        }
        $criteria->order = 'RAND()';
        
        $criteria->limit = $iMaxWord;
        $data = $this->findAll($criteria);
        
        if(count($data)==0)
        {
            $iYear = $iYear-1;
            if($iYear<2012)
            {
                $iYear = 2015;
                $cache_name = "YII-SPELLINGBEE-USERYEAR";
                $responsecache[$iUserId][$iLevel]['year'] = $iYear;
                
                Yii::app()->cache->set($cache_name, $responsecache, 3986400);
                
                $cache_name = "YII-SPELLINGBEE-USERWORD";
                $responsecach = Yii::app()->cache->get($cache_name);
                $responsecach[$iUserId][$iLevel]['words'] = array();
               
                Yii::app()->cache->set($cache_name, $responsecach, 3986400);
                
                $data = $this->getWordsByLevel( $iLevel, $iMaxWord, $iYear,array(),$iUserId);
                return $data;
            } 
            else
            {  
                $cache_name = "YII-SPELLINGBEE-USERYEAR";
                $responsecache[$iUserId][$iLevel]['year'] = $iYear;
                Yii::app()->cache->set($cache_name, $responsecache, 3986400);
                $data = $this->getWordsByLevel( $iLevel, $iMaxWord, $iYear,$user_word_played,$iUserId);
                return $data;
            }
            
        }
        else
        {
            $response['words'] = $data;
            $response['fulldata'] = 1;
            if(count($data)!=$iMaxWord)
            {
                $response['fulldata'] = 0;
            }
        }
        return $response;
    }

}
