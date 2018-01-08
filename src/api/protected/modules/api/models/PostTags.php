<?php

/**
 * This is the model class for table "tds_post_tags".
 *
 * The followings are the available columns in table 'tds_post_tags':
 * @property integer $id
 * @property string $post_id
 * @property integer $tag_id
 *
 * The followings are the available model relations:
 * @property Post $post
 * @property Tags $tag
 */
class PostTags extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public $total;
    public function tableName()
    {
        return 'tds_post_tags';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('post_id, tag_id', 'required'),
            array('tag_id', 'numerical', 'integerOnly' => true),
            array('post_id', 'length', 'max' => 20),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, post_id, tag_id', 'safe', 'on' => 'search'),
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
            'tag' => array(self::BELONGS_TO, 'Tag', 'tag_id'),
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
            'tag_id' => 'Tag',
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
        $criteria->compare('tag_id', $this->tag_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getPostTotal($id, $user_type)
    {

        $criteria = new CDbCriteria();
        $criteria->select = 'count(t.id) as total';
        $criteria->compare("post.status", 5);
        $criteria->together = true;
        $criteria->compare("postType.type_id", $user_type);
        $criteria->compare("tag.id", $id);
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
            'tag'=>array(
                'select' => "",
                "joinType" => "INNER JOIN"
            )
        );
        $criteria->group = "tag.id";
        $criteria->order = 'DATE(post.published_date) DESC';

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

    

    

    public function getPost($id, $user_type, $page = 1, $page_size = 10)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        $criteria->together = true;
        $criteria->compare("post.status", 5);
        $criteria->compare("tag.id", $id);
        $criteria->compare("postType.type_id", $user_type);
        $criteria->addCondition("DATE(post.published_date) <= '" . date("Y-m-d") . "'");
        $criteria->addCondition("category.parent_id IS NULL OR category.parent_id = ''");
        $criteria->order = 'DATE(post.published_date) DESC';

        $criteria->with = array(
            'post' => array(
                'select' => 'post.id,post.lead_link,post.lead_caption,post.lead_source,post.post_type,post.view_count,post.headline,post.content,post.headline_color,post.summary,post.short_title,post.lead_material,post.mobile_image,post.is_breaking,post.breaking_expire,post.is_exclusive,post.exclusive_expired,post.published_date',
                'joinType'=>"INNER JOIN",
                'with' => array(
                        "postType" => array(
                        "select" => "",
                        'joinType'=>"INNER JOIN",
                        ),
                        "postCategories" => array(
                            "select" => "",
                            'joinType' => 'INNER JOIN',
                            "with" => array(
                                "category" => array(
                                    "select" => "category.id,category.menu_icon,category.icon,category.name"
                                )
                            )
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
            'tag'=>array(
                'select' => "",
                "joinType" => "INNER JOIN"
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
                $post_array[$i]['title'] = $postValue['post']->headline;
                $post_array[$i]['post_type'] = $postValue['post']->post_type;
                $post_array[$i]['title_color'] = $postValue['post']->headline_color;
                $post_array[$i]['seen'] = $postValue['post']->view_count;
                $post_array[$i]['id'] = $postValue['post']->id;
                
                //get all images
                $post_array[$i]['images'] = array();
                $post_array[$i]['add_images'] = array();
                //$post_array[$i]['images'] = Settings::content_images($postValue['post']->content,true,$postValue['post']->lead_material);
                if ($postValue['post']['postGalleries'])
                {
                    $j = 0;
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
                    }
                }

                if ($postValue['post']->summary)
                {
                    $post_array[$i]['summary'] = $postValue['post']->summary;
                }
                else
                {
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

                if ($postValue['post']['postCategories'][0]['category']->menu_icon)
                    $post_array[$i]['category_menu_icon'] = Settings::$image_path . $postValue['post']['postCategories'][0]['category']->menu_icon;

                if ($postValue['post']['postCategories'][0]['category']->icon)
                    $post_array[$i]['category_icon'] = Settings::$image_path . $postValue['post']['postCategories'][0]['category']->icon;

                $post_array[$i]['category_name'] = $postValue['post']['postCategories'][0]['category']->name;
                $post_array[$i]['category_id'] = $postValue['post']['postCategories'][0]['category']->id;

                $i++;
            }
        return $post_array;
    }

    

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PostTags the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}
