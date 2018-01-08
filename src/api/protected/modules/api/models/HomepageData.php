<?php

/**
 * This is the model class for table "tds_homepage_data".
 *
 * The followings are the available columns in table 'tds_homepage_data':
 * @property integer $id
 * @property integer $post_id
 * @property string $date
 * @property integer $priority
 * @property integer $status
 */
class HomepageData extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public $total=0;
    public $maxorder = 0;
    public function tableName()
    {
        return 'tds_homepage_data';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('post_id, date', 'required'),
            array('post_id, priority, status', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, post_id, date, priority, status', 'safe', 'on' => 'search'),
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
            'post' => array(self::BELONGS_TO, 'Post', 'post_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'post_id' => 'Post',
            'date' => 'Date',
            'priority' => 'Priority',
            'status' => 'Status',
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
        $criteria->compare('post_id', $this->post_id);
        $criteria->compare('date', $this->date, true);
        $criteria->compare('priority', $this->priority);
        $criteria->compare('status', $this->status);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return HomepageData the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    public function getPostTotal($user_type,$date_value=false,$category_not_to_show=false, $lang = FALSE)
    {
        if($date_value==false)
        {
            $date_value = date("Y-m-d");
        }
        $criteria = new CDbCriteria;
        $criteria->together = true;
        $criteria->select = 'count(DISTINCT t.post_id) as total';
        $criteria->compare("post.status", 5);
        $criteria->compare("post.school_id", 0);
        $criteria->compare("post.teacher_id", 0);
        
        if($lang)
        {
            $criteria->compare("post.language", $lang);
        }
        
        if($category_not_to_show)
        {
            //$criteria->addInCondition('postCategories.category_id', explode(",",$category_not_to_show));
        }
        
        $criteria->compare("t.status", 1);       
        if(Settings::$news_in_index['show_old_news'])
        {
             $target_date = strtotime($date_value);
             $date_value = date("Y-m-d", strtotime(Settings::$news_in_index['days_to_retrieve_news'], $target_date));
             $criteria->addCondition("t.date >= '" . $date_value . "'"); 
        }
        else
        {
             $criteria->compare("t.date", $date_value);
        }   
        $criteria->compare("t.post_type", $user_type);
        $criteria->addCondition("DATE(post.published_date) <= '" . date("Y-m-d") . "'");

        $criteria->with = array(
            'post' => array(
                'select' => '',
                'joinType' => 'LEFT JOIN',
                'with' => array(
                    "postCategories" => array(
                        "select" => "",
                        'joinType' => 'LEFT JOIN'
                    )
                )
            )
        );
        $criteria->group = "post.status";
        $data = $this->find($criteria);   
        if(Settings::$news_in_index['show_old_news'] === false)
        {
            if($data && count($data)>0)
            {
                $return = $data->total;
            } 
            else
            {
                $date_value = date("Y-m-d", strtotime("-1 Days", $date_value));
                $this->getPostTotal($user_type, $page, $page_size, $date_value);
            }
        }
        else
        {
            if($data)
            {
                $return = $data->total;
            }
            else
            {
                $return = 0;

            }
        }
        return $return;
    }

    public function getHomePagePost($website_only, $user_type, $page = 1, $page_size = 9,
            $date_value = false, $already_showed = false, $from_main_site = false,
            $category_not_to_show = false, $lang = FALSE)
    {
        if($date_value==false)
        {
            $date_value = date("Y-m-d");
        }
        
        $criteria = new CDbCriteria;
        $criteria->together = true;
        $criteria->select = 'MAX(t.priority) as maxorder';
        $criteria->compare("post.status", 5);
        $criteria->compare("post.school_id", 0);
        $criteria->compare("post.teacher_id", 0);
        
        if($lang) {
            $criteria->addCondition("post.language = '".$lang."' OR post.post_type = '2'");
        }
        
        $criteria->compare("t.status", 1);
        if($category_not_to_show)
        {
            //$criteria->addInCondition('postCategories.category_id', explode(",",$category_not_to_show));
        }
        if($already_showed)
        {
            $criteria->addNotInCondition('t.post_id', explode(",",$already_showed));
        }
        
        if(Settings::$news_in_index['show_old_news'])
        {
             $target_date = strtotime($date_value);
             $date_value = date("Y-m-d", strtotime(Settings::$news_in_index['days_to_retrieve_news'], $target_date));
             
//             $criteria->addCondition("t.date >= '" . $date_value . "'");
             $criteria->addBetweenCondition('t.date', $date_value, date("Y-m-d"));
        }
        else
        {
             $criteria->compare("t.date", $date_value);
        }    
        
        $website_only = (int)$website_only;
        if($website_only == 1) {
            $criteria->addInCondition("post.website_only", array(1,2));
        } else {
            $criteria->addInCondition("post.website_only", array(0,1));
        }
        
        $criteria->compare("t.post_type", $user_type);
        $criteria->addCondition("DATE(post.published_date) <= '" . date("Y-m-d") . "'");
        
    
        $criteria->group = "t.post_id";
        $criteria->order = 'maxorder DESC';
       

        $criteria->with = array(
            'post' => array(
                'select' => 'post.id',
                'joinType' => 'LEFT JOIN',
                'with' => array(
                    "postCategories" => array(
                        "select" => "",
                        "with" => array(
                            "category" => array(
                                "select" => ""
                            )
                        )
                    )
                )
            ),
        );
        $start = ($page - 1) * $page_size;
        $criteria->limit = $page_size;

        $criteria->offset = 0;
        
//        $subQuery=$this->getCommandBuilder()->createFindCommand($this->getTableSchema(),$criteria)->getText();
//        var_dump($subQuery);exit;
        $obj_post = $this->findAll($criteria);
       
        if(Settings::$news_in_index['show_old_news'] === false)
        {
            if($obj_post && count($obj_post)>0)
            {
                $return = $this->formatHomePageData($obj_post);
            } 
            else
            {
                $date_value = date("Y-m-d", strtotime("-1 Days", $date_value));
                $this->getHomePagePost($website_only, $user_type, $page, $page_size, $date_value);
            }
        }
        else
        {
           $return = $this->formatHomePageData($obj_post,$from_main_site); 
        }    
        return $return;
    }

    private function formatHomePageData($obj_post,$from_main_site=false)
    {
        $post_array = array();
        $i = 0;
        if($obj_post)
        foreach ($obj_post as $postValue)
        {
            $post_array[$i]['id']     = $postValue['post']->id;           
            $i++;
        }
        return $post_array;
    }

    

}
