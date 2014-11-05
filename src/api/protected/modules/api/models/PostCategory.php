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

    public function getPostTotal($category_id, $user_type)
    {

        $criteria = new CDbCriteria();
        $criteria->select = 'count(t.id) as total';
        $criteria->compare("post.status", 5);
        $criteria->together = true;
        $criteria->compare("postType.type_id", $user_type);
        $criteria->compare("t.category_id", $category_id);
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

    public function getPostAll($category_id, $user_type)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        $criteria->together = true;
        $criteria->compare("post.status", 5);
        $criteria->compare("t.category_id", $category_id);
        $criteria->compare("postType.type_id", $user_type);
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

    public function getPost($category_id, $user_type, $page = 1, $page_size = 10,$popular_sort = false,$game_type = false,$fetaured=false)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        $criteria->together = true;
        $criteria->compare("post.status", 5);
        $criteria->compare("t.category_id", $category_id);
        $criteria->compare("postType.type_id", $user_type);
        $criteria->addCondition("DATE(post.published_date) <= '" . date("Y-m-d") . "'");
        
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
                'select' => 'post.id,post.content,post.is_featured,post.attach,post.show_byline_image,post.shoulder,post.other_language,'
                . 'post.lead_link,post.lead_caption,post.embedded,post.layout_color,post.post_layout,post.sort_title_type,post.inside_image,post.video_file,'
                . 'post.lead_source,post.post_type,post.referance_id,post.layout,post.language,post.sub_head,post.headline,post.view_count,post.user_view_count,post.headline_color,post.summary,post.short_title,post.lead_material,post.mobile_image,post.is_breaking,post.breaking_expire,post.is_exclusive,post.exclusive_expired,post.published_date',
                'joinType' => "INNER JOIN",
                'with' => array(
                    "postType" => array(
                        "select" => "",
                        'joinType' => "INNER JOIN",
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
                ),
                
            ),
            'category' => array(
                'select' => 'category.menu_icon,category.icon,category.name,category.id',
                'joinType' => "INNER JOIN"
            )
        );
        $start = ($page - 1) * $page_size;
        $criteria->limit = $page_size;

        $criteria->offset = $start;


        $obj_post = $this->findAll($criteria);


        $formated_post = $this->formatPost($obj_post);

        return $formated_post;
    }

    private function formatPost($obj_post)
    {
        $post_array = array();
        $i = 0;
        if ($obj_post)
            foreach ($obj_post as $postValue)
            {
                //array_map('utf8_encode', (array)$postValue['post']);
                $post_array[$i]['title'] = $postValue['post']->headline;

                $post_array[$i]['post_type'] = $postValue['post']->post_type;
                $post_array[$i]['seen'] = $postValue['post']->view_count;
                $post_array[$i]['title_color'] = $postValue['post']->headline_color;
                
                $post_array[$i]['video_file'] = "";
            
                if($postValue['post']->video_file)
                $post_array[$i]['video_file'] = Settings::$image_path.$postValue['post']->video_file;

                $post_array[$i]['id'] = $postValue['post']->id;
                
                
                
                $post_array[$i]['post_layout']     = $postValue['post']->post_layout;
            
                $post_array[$i]['sort_title_type'] = $postValue['post']->sort_title_type;
                $post_array[$i]['inside_image'] = "";

                if ($postValue['post']->inside_image)
                $post_array[$i]['inside_image'] = Settings::get_mobile_image(Settings::$image_path . $postValue['post']->inside_image);


                $post_array[$i]['normal_post_type'] = Settings::get_simple_post_layout($postValue['post']);


                $post_array[$i]['author'] = "";
                $post_array[$i]['author_image'] = "";
                if (isset($postValue['post']['postAuthor']))
                {
                    $post_array[$i]['author'] = $postValue['post']['postAuthor']->title;
                    if($postValue['post']['postAuthor']->image)
                    $post_array[$i]['author_image'] = Settings::$image_path.$postValue['post']['postAuthor']->image;
                }


                $post_array[$i]['post_id'] = $postValue['post']->id;
                $post_array[$i]['headline'] = $postValue['post']->headline;
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
                //$post_array[$i]['images'] = Settings::content_images($postValue['post']->content,true,$postValue['post']->lead_material);
                if ($postValue['post']['postGalleries'])
                {
                    $j = 0;
                    $k = 0;
                    foreach ($postValue['post']['postGalleries'] as $value)
                    {
                        if (trim($value['material']->material_url) && $value->type == 2)
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

                if ($postValue['category']->menu_icon)
                    $post_array[$i]['category_menu_icon'] = Settings::$image_path . $postValue['category']->menu_icon;

                if ($postValue['category']->icon)
                    $post_array[$i]['category_icon'] = Settings::$image_path . $postValue['category']->icon;

                $post_array[$i]['category_name'] = $postValue['category']->name;
                $post_array[$i]['category_id'] = $postValue['category']->id;

                $i++;
            }
        return $post_array;
    }

}
