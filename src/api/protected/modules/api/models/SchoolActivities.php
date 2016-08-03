<?php

/**
 * This is the model class for table "tds_school_activities".
 *
 * The followings are the available columns in table 'tds_school_activities':
 * @property integer $id
 * @property integer $school_id
 * @property string $title
 * @property string $content
 * @property string $date
 */
class SchoolActivities extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public $total = 0;
    public function tableName()
    {
        return 'tds_school_activities';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('school_id, title, content, date', 'required'),
            array('school_id', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, school_id, title, content, date', 'safe', 'on' => 'search'),
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
            'activityGalleries' => array(self::HAS_MANY, 'SchoolActivitiesGallery', 'activities_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'school_id' => 'School',
            'title' => 'Title',
            'content' => 'Content',
            'date' => 'Date',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('school_id', $this->school_id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('date', $this->date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SchoolActivities the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    public function getActivityTotal($school_id)
    {

        $criteria = new CDbCriteria();
        $criteria->select = 'count(t.id) as total';
        $criteria->compare("t.school_id", $school_id);
        
        $criteria->group = "t.school_id";
        $data = $this->find($criteria);
        if($data)
        {
            return $data->total;
        }
        else
        {
            return 0;

        }
    }

    public function getActivity($school_id, $page_size = 10,$page = 1)
    {
        $criteria = new CDbCriteria();
        $criteria->select = "t.title,t.content,t.mobile_content";
        $criteria->together = true;
        $criteria->compare("t.school_id", $school_id);
        $criteria->order = "t.id DESC";
        
        
        $criteria->with = array(
            'activityGalleries' => array(
                'select' => '',
                'with' => array(
                    "material" => array(
                        "select" => "material.material_url",
                    )
                )
            ),
        );
        
        $start = ($page - 1) * $page_size;
        $criteria->limit = $page_size;

        $criteria->offset = $start;
        
        
        $activity = $this->findALL($criteria);

        $activity_array = array();

        $i = 0;
        foreach($activity as $value)
        {
            $activity_array[$i]['title']   = $value->title;
            if(isset($value->mobile_content) && strlen(Settings::substr_with_unicode($value->mobile_content))>0)
            {
               $activity_array[$i]['content'] = $value->mobile_content; 
            }
            else
            {
               $activity_array[$i]['content'] = $value->content; 
            } 
            //$activity_array[$i]['content'] = $value->content;
            $activity_array[$i]['summary'] = Settings::substr_with_unicode($value->content);
            $activity_array[$i]['images'] = Settings::content_images($value->content);
            
            $activity_array[$i]['gallery'] = array();
            if ($value['activityGalleries'])
            {
                foreach ($value['activityGalleries'] as $gallery)
                {
                    if (trim($gallery['material']->material_url))
                    {
                        $activity_array[$i]['gallery'][] = Settings::get_mobile_image(Settings::$image_path . $gallery['material']->material_url);
                    }
                }
            }
            
            $i++;
        }
        

        return $activity_array;
    }

}
