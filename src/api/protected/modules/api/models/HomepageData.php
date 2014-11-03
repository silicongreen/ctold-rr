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
    public function getPostTotal($user_type,$date_value=false,$category_not_to_show=false)
    {
        if($date_value==false)
        {
            $date_value = date("Y-m-d");
        }
        $criteria = new CDbCriteria;
        $criteria->together = true;
        $criteria->select = 'count(DISTINCT t.post_id) as total';
        $criteria->compare("post.status", 5);
        if($category_not_to_show)
        {
            $criteria->addInCondition('postCategories.category_id', explode(",",$category_not_to_show));
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

    public function getHomePagePost($user_type, $page = 1, $page_size = 9,$date_value=false,$already_showed=false,$from_main_site=false,$category_not_to_show=false)
    {
        if($date_value==false)
        {
            $date_value = date("Y-m-d");
        }
        
        $criteria = new CDbCriteria;
        $criteria->together = false;
        $criteria->select = 'MAX(t.priority) as maxorder';
        $criteria->compare("post.status", 5);
        
        $criteria->compare("t.status", 1);
        if($category_not_to_show)
        {
            $criteria->addInCondition('postCategories.category_id', explode(",",$category_not_to_show));
        }
        if($already_showed)
        {
            $criteria->addNotInCondition('t.post_id', explode(",",$already_showed));
        }
        
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
        
    
        $criteria->group = "t.post_id";
        $criteria->order = 'maxorder DESC';
       

        $criteria->with = array(
            'post' => array(
                'select' => 'post.id,post.content,post.is_featured,post.attach,post.show_byline_image,post.shoulder,post.other_language,'
                . 'post.lead_link,post.lead_caption,post.embedded,post.video_file,post.layout_color,post.post_layout,post.sort_title_type,post.inside_image,'
                . 'post.lead_source,post.post_type,post.referance_id,post.layout,post.language,post.sub_head,post.headline,post.view_count,post.user_view_count,post.headline_color,post.summary,post.short_title,post.lead_material,post.mobile_image,post.is_breaking,post.breaking_expire,post.is_exclusive,post.exclusive_expired,post.published_date',
                'joinType' => 'LEFT JOIN',
                'with' => array(
                    "postCategories" => array(
                        "select" => "postCategories.id",
                        "with" => array(
                            "category" => array(
                                "select" => "category.id,category.menu_icon,category.icon,category.name"
                            )
                        )
                    ),
                    'postAuthor' => array(
                        'select' => 'postAuthor.title,postAuthor.image'
                    ),
                    'postGalleries' => array(
                        'select' => 'postGalleries.type,postGalleries.caption,postGalleries.source',
                        'with' => array(
                            "material" => array(
                                "select" => "material.material_url",
                            )
                        )
                    )
                )
            ),
        );
        $start = ($page - 1) * $page_size;
        $criteria->limit = $page_size;

        $criteria->offset = 0;
        
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
                $this->getHomePagePost($user_type, $page, $page_size, $date_value);
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
            $post_array[$i]['title']     = $postValue['post']->headline;
            
            $post_array[$i]['post_type'] = $postValue['post']->post_type;
            
            $post_array[$i]['video_file'] = "";
            
            if($postValue['post']->video_file)
            $post_array[$i]['video_file'] = Settings::$image_path.$postValue['post']->video_file;
            
            
            $post_array[$i]['seen'] = $postValue['post']->view_count;
            $post_array[$i]['title_color'] = $postValue['post']->headline_color;
            
            $post_array[$i]['id'] = $postValue['post']->id;
            
            
            $post_array[$i]['post_layout']     = $postValue['post']->post_layout;
            
            $post_array[$i]['sort_title_type'] = $postValue['post']->sort_title_type;
            $post_array[$i]['inside_image'] = "";
            
            if ($postValue['post']->inside_image)
                $post_array[$i]['inside_image'] = Settings::get_mobile_image(Settings::$image_path . $postValue['post']->inside_image);
            
            $post_array[$i]['normal_post_type'] = Settings::get_simple_post_layout($postValue['post']);
                
            $post_array[$i]['author'] = "";
            $post_array[$i]['author_image'] = "";
            if(isset($postValue['post']['postAuthor']))
            {
                $post_array[$i]['author'] = $postValue['post']['postAuthor']->title;
                if($postValue['post']['postAuthor']->image)
                $post_array[$i]['author_image'] = Settings::$image_path .$postValue['post']['postAuthor']->image;
            }


            $post_array[$i]['post_id'] = $postValue['post']->id;
            $post_array[$i]['headline']     = $postValue['post']->headline;
            $post_array[$i]['content'] = $postValue['post']->content;
            $post_array[$i]['is_featured'] = $postValue['post']->is_featured;
            $post_array[$i]['show_byline_image'] = $postValue['post']->show_byline_image;
            $post_array[$i]['headline_color'] = $postValue['post']->headline_color;


            $post_array[$i]['short_title'] = $postValue['post']->short_title;
            $post_array[$i]['shoulder'] = $postValue['post']->shoulder;
            $post_array[$i]['other_language'] = $postValue['post']->other_language;

            $post_array[$i]['post_type'] = $postValue['post']->post_type;
            $post_array[$i]['sub_head'] = $postValue['post']->sub_head;
            $post_array[$i]['lead_material'] = $postValue['post']->lead_material;

            $post_array[$i]['lead_caption'] = $postValue['post']->lead_caption;
            $post_array[$i]['is_breaking'] = $postValue['post']->is_breaking;
            $post_array[$i]['breaking_expire'] = $postValue['post']->breaking_expire;
            $post_array[$i]['is_exclusive'] = $postValue['post']->is_exclusive;
            $post_array[$i]['exclusive_expired'] = $postValue['post']->exclusive_expired;


            $post_array[$i]['language'] = $postValue['post']->language;
            $post_array[$i]['lead_link'] = $postValue['post']->lead_link;
            $post_array[$i]['view_count'] = $postValue['post']->view_count;

            $post_array[$i]['user_view_count'] = $postValue['post']->user_view_count;
            $post_array[$i]['embedded'] = $postValue['post']->embedded;
            
            $post_array[$i]['embedded_url'] = "";
            if($postValue['post']->embedded)
            $post_array[$i]['embedded_url'] = Settings::get_embeded_url($postValue['post']->embedded);
          
            $post_array[$i]['layout_color'] = $postValue['post']->layout_color;

            $post_array[$i]['referance_id'] = $postValue['post']->referance_id;
            $post_array[$i]['attach'] = $postValue['post']->attach;
            $post_array[$i]['layout'] = $postValue['post']->layout;
         

            
            
           
            //get all images
            //$post_array[$i]['images'] = Settings::content_images($postValue['post']->content,true,$postValue['post']->lead_material);
            
            $post_array[$i]['images'] = array();
            $post_array[$i]['add_images'] = array();
            $post_array[$i]['web_images'] = array();
            if ($postValue['post']['postGalleries'])
            {
                $j = 0;
                $k = 0;
                foreach ($postValue['post']['postGalleries'] as $value)
                {
                    if (trim($value['material']->material_url) && $value->type==2)
                    {
                        $post_array[$i]['images'][] = Settings::get_mobile_image(Settings::$image_path . $value['material']->material_url);
                    
                        $post_array[$i]['add_images'][$j]['ad_image'] = Settings::get_mobile_image(Settings::$image_path . $value['material']->material_url);
                        $post_array[$i]['add_images'][$j]['ad_image_link'] = $value->source;
                        $post_array[$i]['add_images'][$j]['ad_image_caption'] = $value->caption;
                        $j++;
                    }
                    else if(trim($value['material']->material_url) && $value->type==1)
                    {
                        $post_array[$i]['web_images'][$k]['image'] = Settings::$image_path . $value['material']->material_url;
                        $post_array[$i]['web_images'][$k]['source'] = $value->source;
                        $post_array[$i]['web_images'][$k]['caption'] =  $value->caption;
                        $k++;
                    }    
                }
            }
            
            $post_array[$i]['summary'] = "";
            //$post_array[$i]['content'] = $postValue['post']->content;
            if ($postValue['post']->summary)
            {
                $post_array[$i]['has_summary'] = 1;
                $post_array[$i]['summary'] = $postValue['post']->summary;
            }
            else
            {
                $post_array[$i]['has_summary'] = 0;
                $post_array[$i]['summary'] = Settings::substr_with_unicode($postValue['post']->content);
            }
            
            //new update
            //$post_array[$i]['add_images'] = Settings::add_caption_and_link($postValue['post']);

            $post_array[$i]['share_link'] = Settings::get_post_link_url($postValue['post']);
            //new update
            
            
            $post_array[$i]['mobile_image'] = "";
            if ($postValue['post']->mobile_image)
                $post_array[$i]['mobile_image'] = Settings::get_mobile_image(Settings::$image_path . $postValue['post']->mobile_image);
            
            $datestring = Settings::get_post_time($postValue['post']->published_date);
            
            
            $post_array[$i]['published_date'] = $postValue['post']->published_date;
            $post_array[$i]['current_date'] = date("Y-m-d H:i:s");
            $post_array[$i]['published_date_string'] = $datestring;

            $post_array[$i]['category_menu_icon'] = "";
            $post_array[$i]['category_icon'] = "";
            $post_array[$i]['category_name'] = "";
            $post_array[$i]['category_id'] = "";

            if(isset($postValue['post']['postCategories'][0]))
            {
                if ($postValue['post']['postCategories'][0]['category']->menu_icon)
                    $post_array[$i]['category_menu_icon'] = Settings::$image_path . $postValue['post']['postCategories'][0]['category']->menu_icon;

                if ($postValue['post']['postCategories'][0]['category']->icon)
                    $post_array[$i]['category_icon'] = Settings::$image_path . $postValue['post']['postCategories'][0]['category']->icon;

                $post_array[$i]['category_name'] = $postValue['post']['postCategories'][0]['category']->name;
                $post_array[$i]['category_id'] = $postValue['post']['postCategories'][0]['category']->id;
            }
            
            $i++;
        }
        return $post_array;
    }

    

}
