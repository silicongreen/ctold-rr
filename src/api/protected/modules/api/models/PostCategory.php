<?php

/**
 * This is the model class for table "tds_post_category".
 *
 * The followings are the available columns in table 'tds_post_category':
 * @property integer $id
 * @property string $post_id
 * @property integer $category_id
 * @property integer $inner_priority
 *
 * The followings are the available model relations:
 * @property Categories $category
 * @property Post $post
 */
class PostCategory extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public $total = 0;

    public function tableName()
    {
        return 'tds_post_category';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('post_id, category_id', 'required'),
            array('category_id, inner_priority', 'numerical', 'integerOnly' => true),
            array('post_id', 'length', 'max' => 20),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, post_id, category_id, inner_priority', 'safe', 'on' => 'search'),
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
            'category' => array(self::BELONGS_TO, 'Categories', 'category_id'),
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
            'category_id' => 'Category',
            'inner_priority' => 'Inner Priority',
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
        $criteria->compare('post_id', $this->post_id, true);
        $criteria->compare('category_id', $this->category_id);
        $criteria->compare('inner_priority', $this->inner_priority);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PostCategory the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getPostTotal($category_id, $user_type, $already_showed = false, $lang = FALSE)
    {

        $criteria = new CDbCriteria();
        $criteria->select = 'count(t.id) as total';
        $criteria->compare("post.status", 5);
        $criteria->together = true;
        $criteria->compare("postType.type_id", $user_type);
        $criteria->compare("t.category_id", $category_id);
        $criteria->compare("post.school_id", 0);
        $criteria->compare("post.teacher_id", 0);
        if($already_showed)
        {
            $criteria->addNotInCondition('post.id', explode(",",$already_showed));
        }
        $criteria->addCondition("DATE(post.published_date) <= '" . date("Y-m-d") . "'");
        $criteria->with = array(
            'post' => array(
                'select' => '',
                'joinType' => "INNER JOIN",
                'with' => array(
                    "postType" => array(
                        "select" => "",
                        'joinType' => "INNER JOIN",
                    )
                )
            ),
        );
        $criteria->group = "t.category_id";
        $criteria->order = 'DATE(post.published_date) DESC, t.inner_priority ASC';

        $data = $this->find($criteria);
        if ($data)
        {
            return $data->total;
        }
        else
        {
            return 0;
        }
    }
    public function nextpreviousid($category_id, $user_type,$current_id,$published_date,$inner_priority,$target="next",$another_id=0,$call=1)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        $criteria->together = true;
        $criteria->compare("post.status", 5);
        $criteria->compare("t.category_id", $category_id);
        $criteria->compare("post.id !", $current_id);
        $criteria->compare("post.school_id", 0);
        $criteria->compare("post.teacher_id", 0);
        if($another_id!=0)
        {
            $criteria->compare("post.id !", $another_id);
        }    
        $criteria->compare("postType.type_id", $user_type);
        $criteria->addCondition("DATE(post.published_date) <= '" . date("Y-m-d") . "'");
        if($target=="next")
        {
            if($call==1)
            {
                $criteria->addCondition("DATE(post.published_date) = '" . date("Y-m-d",  strtotime($published_date)) . "'"
                    . " AND t.inner_priority >= '" . $inner_priority . "'");
            }
            else
            {
               $criteria->addCondition("DATE(post.published_date) < '" . date("Y-m-d",  strtotime($published_date)) . "'"); 
            }    
            
            $criteria->order = 'DATE(post.published_date) DESC, t.inner_priority ASC';
        }  
        else
        {
            if($call==1)
            {
                $criteria->addCondition("DATE(post.published_date) = '" . date("Y-m-d",  strtotime($published_date)) . "'"
                    . " AND t.inner_priority <= '" . $inner_priority . "'");
            }
            else
            {
               $criteria->addCondition("DATE(post.published_date) > '" . date("Y-m-d",  strtotime($published_date)) . "'"); 
            } 
           
            $criteria->order = 'DATE(post.published_date) ASC, t.inner_priority DESC';
        }    
        $criteria->with = array(
             'post' => array(
                 'select' => 'post.id',
                 'joinType' => "INNER JOIN",
                 'with' => array(
                     "postType" => array(
                         "select" => "",
                         'joinType' => "INNER JOIN",
                     )
                 )
             )
         );
        $criteria->limit = 1;
        $obj_post = $this->find($criteria);
        if($obj_post)
        {
            return $obj_post['post']->id;
        }
        else if($call==1)
        {
            return $this->nextpreviousid($category_id, $user_type,$current_id,$published_date,$inner_priority,$target,$another_id,2);
            
        }  
        else 
        {
            return false;
        }
        
    }        

    public function getPostAll($category_id, $user_type)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        $criteria->together = true;
        $criteria->compare("post.status", 5);
        $criteria->compare("t.category_id", $category_id);
        $criteria->compare("postType.type_id", $user_type);
        $criteria->compare("post.school_id", 0);
        $criteria->compare("post.teacher_id", 0);
        $criteria->addCondition("DATE(post.published_date) <= '" . date("Y-m-d") . "'");
        $criteria->order = 'DATE(post.published_date) DESC, t.inner_priority ASC';

        $criteria->with = array(
            'post' => array(
                'select' => 'post.id',
                'joinType' => "INNER JOIN",
                'with' => array(
                    "postType" => array(
                        "select" => "",
                        'joinType' => "INNER JOIN",
                    )
                )
            )
        );

        $obj_post = $this->findAll($criteria);
        $formated_post = $this->formatpostall($obj_post);

        return $formated_post;
    }

    private function formatpostall($obj_post)
    {
        $post_array = array();
        $i = 0;
        if ($obj_post)
            foreach ($obj_post as $postValue)
            {
                $post_array[$i]['id'] = $postValue['post']->id;
                $i++;
            }
        return $post_array;
    }

    public function getPost($category_id, $user_type, $page = 1, $page_size = 10, $popular_sort = false,
            $game_type = false, $fetaured = false, $already_showed = false, $lang = false)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        if($game_type)
        {
          $criteria->addCondition("post.game_type= '" . $game_type . "'"); 
        }
        if($fetaured == 1 && $fetaured!=false)
        {
           $criteria->compare("post.is_featured",1);  
        } 
        else if($fetaured == 2 && $fetaured!=false)
        {
            $criteria->addCondition("post.is_featured = 0 OR post.is_featured IS NULL");
        }
        
        if($lang) {
            $criteria->addCondition("post.language = '".$lang."' OR post.post_type = '2'");
        }
        
        if(!$popular_sort)
        {
            $criteria->order = 'DATE(post.published_date) DESC, t.inner_priority ASC';
        }
        else
        {
            $criteria->order = 'post.user_view_count DESC, DATE(post.published_date) DESC, t.inner_priority ASC';
          
        }    

        $criteria->with = array(
            'post' => array(
                'select' => 'post.id',
                'joinType' => "INNER JOIN",
                'with' => array(
                    "postType" => array(
                        "select" => "",
                        'joinType' => "INNER JOIN",
                    )
                    
                ),
                
            )
        );
        
        $start = $page;
        $criteria->limit = $page_size;

        $criteria->offset = $start;
        $criteria->compare("post.school_id", 0);
        $criteria->compare("post.teacher_id", 0);
        $criteria->compare("post.status", 5);
        $criteria->compare("t.category_id", $category_id);
        $criteria->compare("postType.type_id", $user_type);
        $criteria->addCondition("DATE(post.published_date) <= '" . date("Y-m-d") . "'");
        if($already_showed)
        {
            $criteria->addNotInCondition('post.id', explode(",",$already_showed));
        }
        $criteria->together = true;

        $obj_post = $this->findAll($criteria);
        
        $formated_post = $this->formatPost($obj_post);

        return $formated_post;
    }

    private function formatPost($obj_post)
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
