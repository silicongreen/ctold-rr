<?php

/**
 * This is the model class for table "tds_post".
 *
 * The followings are the available columns in table 'tds_post':
 * @property string $id
 * @property string $shoulder
 * @property string $headline
 * @property string $headline_color
 * @property string $sub_head
 * @property integer $byline_id
 * @property string $summary
 * @property string $embedded
 * @property string $short_title
 * @property string $google_short_url
 * @property string $content
 * @property integer $can_comment
 * @property string $lead_material
 * @property string $mobile_image
 * @property integer $status
 * @property integer $for_all
 * @property string $type
 * @property integer $is_featured
 * @property integer $is_breaking
 * @property string $breaking_expire
 * @property integer $is_developing
 * @property integer $is_exclusive
 * @property string $exclusive_expired
 * @property string $latitude
 * @property string $longitude
 * @property string $view_count
 * @property string $published_date
 * @property string $publish_date_only
 * @property integer $priority_type
 * @property integer $priority
 * @property string $created
 * @property string $updated
 * @property integer $has_image
 * @property integer $has_video
 * @property integer $has_pdf
 * @property integer $has_related_news
 * @property string $news_expire_date
 * @property string $lead_caption
 * @property string $lead_source
 * @property string $meta_description
 * @property string $ip_address
 *
 * The followings are the available model relations:
 * @property PostCategory[] $postCategories
 * @property PostGallery[] $postGalleries
 * @property PostTags[] $postTags
 * @property PostUserActivity[] $postUserActivities
 * @property RelatedNews[] $relatedNews
 */
class Post extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public $total;

    public function tableName()
    {
        return 'tds_post';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('headline', 'required'),
     //       array('byline_id, can_comment, status, for_all, is_featured, is_breaking, is_developing, is_exclusive, priority_type, priority, has_image, has_video, has_pdf, has_related_news', 'numerical', 'integerOnly' => true),
//            array('shoulder', 'length', 'max' => 100),
//            array('headline', 'length', 'max' => 150),
//            array('headline_color, type', 'length', 'max' => 6),
//            array('sub_head, google_short_url, lead_material, mobile_image, latitude, longitude, lead_source', 'length', 'max' => 255),
//            array('short_title', 'length', 'max' => 80),
//            array('view_count', 'length', 'max' => 20),
//            array('ip_address', 'length', 'max' => 15),
            array('summary, embedded, breaking_expire, exclusive_expired, published_date, publish_date_only, created, updated, news_expire_date, lead_caption, meta_description', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, shoulder, headline, headline_color, sub_head, byline_id, summary, embedded, short_title, google_short_url, content, can_comment, lead_material, mobile_image, status, for_all, type, is_featured, is_breaking, breaking_expire, is_developing, is_exclusive, exclusive_expired, latitude, longitude, view_count, published_date, publish_date_only, priority_type, priority, created, updated, has_image, has_video, has_pdf, has_related_news, news_expire_date, lead_caption, lead_source, meta_description, ip_address', 'safe', 'on' => 'search'),
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
            'postCategories' => array(self::HAS_MANY, 'PostCategory', 'post_id'),
            'postAttachment' => array(self::HAS_MANY, 'PostAttachment', 'post_id'),
            'postAuthor' => array(self::BELONGS_TO, 'Bylines', 'byline_id'),
            'freeUser' => array(self::BELONGS_TO, 'Freeusers', 'user_id'),
            'postAssessment' => array(self::BELONGS_TO, 'Cassignments', 'assessment_id'),
            'postSchools' => array(self::BELONGS_TO, 'School', 'school_id'),
            'postGalleries' => array(self::HAS_MANY, 'PostGallery', 'post_id'),
            'postTags' => array(self::HAS_MANY, 'PostTags', 'post_id'),
            'postUserActivities' => array(self::HAS_MANY, 'PostUserActivity', 'post_id'),
            'postClass' => array(self::HAS_MANY, 'PostClass', 'post_id'),
            'postType' => array(self::HAS_MANY, 'PostType', 'post_id'),
            'postKeyword' => array(self::HAS_MANY, 'PostKeyword', 'post_id'),
            'postSchool' => array(self::HAS_MANY, 'PostSchoolShare', 'post_id'),
            'relatedNews' => array(self::HAS_MANY, 'RelatedNews', 'post_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'shoulder' => 'Shoulder',
            'headline' => 'Headline',
            'headline_color' => 'Headline Color',
            'sub_head' => 'Sub Head',
            'byline_id' => 'Byline',
            'summary' => 'Summary',
            'embedded' => 'Embedded',
            'short_title' => 'Short Title',
            'google_short_url' => 'Short URL generated by google',
            'content' => 'Content',
            'can_comment' => 'Whether User can comment or not',
            'lead_material' => 'For carrosel News, Lead Material image is required',
            'mobile_image' => 'Mobile Image',
            'status' => '1 - Draft
, 2 - Created, 
3 - Updated
, 4 - Reviewed
, 5 - Published
, 6 - Delete',
            'for_all' => 'For All',
            'type' => 'Type',
            'is_featured' => 'Is Featured',
            'is_breaking' => 'Is Breaking',
            'breaking_expire' => 'Breaking Expire',
            'is_developing' => 'Is Developing',
            'is_exclusive' => 'Is Exclusive',
            'exclusive_expired' => 'Exclusive Expired',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'view_count' => 'View Count',
            'published_date' => 'Published Date',
            'publish_date_only' => 'Publish Date Only',
            'priority_type' => 'Priorirty Types are:
1 - Carrosel News
2 - Main News
3 - Other Homepage News
4 - More News
5 - All other news',
            'priority' => 'Priority',
            'created' => 'Created',
            'updated' => 'Updated',
            'has_image' => 'Has Image',
            'has_video' => 'Has Video',
            'has_pdf' => 'Has Pdf',
            'has_related_news' => 'Has Related News',
            'news_expire_date' => 'News Expire Date',
            'lead_caption' => 'Lead Caption',
            'lead_source' => 'Lead Source',
            'meta_description' => 'Meta Description',
            'ip_address' => 'Ip Address',
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

        $criteria->compare('id', $this->id, true);
        $criteria->compare('shoulder', $this->shoulder, true);
        $criteria->compare('headline', $this->headline, true);
        $criteria->compare('headline_color', $this->headline_color, true);
        $criteria->compare('sub_head', $this->sub_head, true);
        $criteria->compare('byline_id', $this->byline_id);
        $criteria->compare('summary', $this->summary, true);
        $criteria->compare('embedded', $this->embedded, true);
        $criteria->compare('short_title', $this->short_title, true);
        $criteria->compare('google_short_url', $this->google_short_url, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('can_comment', $this->can_comment);
        $criteria->compare('lead_material', $this->lead_material, true);
        $criteria->compare('mobile_image', $this->mobile_image, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('for_all', $this->for_all);
        $criteria->compare('type', $this->type, true);
        $criteria->compare('is_featured', $this->is_featured);
        $criteria->compare('is_breaking', $this->is_breaking);
        $criteria->compare('breaking_expire', $this->breaking_expire, true);
        $criteria->compare('is_developing', $this->is_developing);
        $criteria->compare('is_exclusive', $this->is_exclusive);
        $criteria->compare('exclusive_expired', $this->exclusive_expired, true);
        $criteria->compare('latitude', $this->latitude, true);
        $criteria->compare('longitude', $this->longitude, true);
        $criteria->compare('view_count', $this->view_count, true);
        $criteria->compare('published_date', $this->published_date, true);
        $criteria->compare('publish_date_only', $this->publish_date_only, true);
        $criteria->compare('priority_type', $this->priority_type);
        $criteria->compare('priority', $this->priority);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('updated', $this->updated, true);
        $criteria->compare('has_image', $this->has_image);
        $criteria->compare('has_video', $this->has_video);
        $criteria->compare('has_pdf', $this->has_pdf);
        $criteria->compare('has_related_news', $this->has_related_news);
        $criteria->compare('news_expire_date', $this->news_expire_date, true);
        $criteria->compare('lead_caption', $this->lead_caption, true);
        $criteria->compare('lead_source', $this->lead_source, true);
        $criteria->compare('meta_description', $this->meta_description, true);
        $criteria->compare('ip_address', $this->ip_address, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Post the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function getSchoolSharePost($school_id,$id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        $criteria->compare("t.school_id", $school_id);
        $criteria->compare("t.share_post_id", $id);
        $criteria->limit = 1;
        $obj_post = $this->find($criteria);
        if($obj_post)
        {
            return true;
        }
        else
        {
            return false;
        }    
        
    }        
    public function getSearchPost($term)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id,t.headline,t.published_date,t.view_count';
        $criteria->together = true;
        $criteria->compare("t.status", 5);
        $criteria->addCondition("DATE(t.published_date) <= '" . date("Y-m-d") . "'");
        $criteria->addCondition("category.id > 0");
        $criteria->addCondition("t.post_type != 2");
        $criteria->addCondition("t.headline LIKE '%" . $term . "%'");
        $criteria->addCondition("category.name LIKE '" . $term . "%'", "OR");
        $criteria->addCondition("tag.tags_name LIKE '" . $term . "%'", "OR");
        $criteria->addCondition("postAuthor.title LIKE '" . $term . "%'", "OR");

        $criteria->order = 'DATE(t.published_date) DESC';
        $criteria->with = array(
            'postCategories' => array(
                'select' => '',
                'joinType' => "LEFT JOIN",
                'with' => array(
                    "category" => array(
                        "select" => "category.id,category.menu_icon,category.icon,category.name"
                    )
                )
            ),
            'postGalleries' => array(
                'select' => 'postGalleries.type',
                'joinType' => "LEFT OUTER JOIN",
                'with' => array(
                    "material" => array(
                        "select" => "material.material_url",
                    )
                )
            ),
            'postTags' => array(
                'select' => 'postTags.id',
                'joinType' => "LEFT OUTER JOIN",
                'with' => array(
                    "tag" => array(
                        "select" => "tag.id,tag.tags_name",
                    )
                )
            ),
            'postAuthor' => array(
                'select' => 'postAuthor.title'
            )
        );
        $criteria->limit = 20;



        $obj_post = $this->findAll($criteria);
        $post_date = array();
        foreach ($obj_post as $value)
        {
            $merge['id'] = $value->id;
            $merge['title'] = $value->headline;
            if (isset($value['postCategories'][0]))
            {
                $merge['category_id'] = $value['postCategories'][0]['category']->id;
                $merge['category_name'] = $value['postCategories'][0]['category']->name;

                $merge['category_icon'] = "";
                if ($value['postCategories'][0]['category']->icon)
                    $merge['category_icon'] = Settings::$image_path . $value['postCategories'][0]['category']->icon;
            }
            if (isset($value['postAuthor']->title))
                $merge['author'] = $value['postAuthor']->title;

            $datestring = Settings::get_post_time($value->published_date);

            $merge['published_date_string'] = $datestring;

            $merge['image'] = "";

            if ($value['postGalleries'])
            {
                foreach ($value['postGalleries'] as $values)
                {
                    if (trim($values['material']->material_url) && $values->type == 2)
                    {
                        $merge['image'] = Settings::get_mobile_image(Settings::$image_path . $values['material']->material_url);
                        break;
                    }
                }
            }
            $merge['views'] = $value->view_count;


            $post_date[] = $merge;
        }

        return $post_date;
    }

    public function getCategoryId($id)
    {
        $criteria = new CDbCriteria;
        $criteria->compare("t.id", $id);
        $criteria->select = 't.id';
        $criteria->order = "postCategories.category_id ASC";
        $criteria->with = array(
            'postCategories' => array(
                'select' => 'postCategories.category_id',
                'joinType' => "LEFT JOIN"
            )
        );
        $obj_post = $this->find($criteria);
        return $obj_post['postCategories'][0]->category_id;
    }
    public function getLanguage($id)
    {
        $criteria = new CDbCriteria;
        $criteria->compare("t.referance_id", $id);
        $criteria->addCondition("t.id = $id ","OR");
        $criteria->compare("t.status", 5);
        $criteria->select = 't.id,t.language';
        $obj_post = $this->findAll($criteria);
        
        $olanguage = array();
        
        $i = 0;
        foreach($obj_post as $value)
        {
            $olanguage[$i]['language'] = $value->language;
            $olanguage[$i]['id'] = $value->id;
            $i++;
        }  
        return $olanguage;
        
        
    } 

    public function getSinglePost($id)
    {
        $criteria = new CDbCriteria;
        $criteria->compare("t.id", $id);
        $criteria->select = 't.*';
        $criteria->order = "category.id ASC";
        $criteria->with = array(
            'postCategories' => array(
                'select' => 'postCategories.id,postCategories.inner_priority',
                'joinType' => "LEFT OUTER JOIN",
                'with' => array(
                    "category" => array(
                        "select" => "category.id,category.menu_icon,category.icon,category.name,category.display_name"
                    )
                )
            ),
            'postAttachment' => array(
                'select' => 'postAttachment.file_name,postAttachment.show,postAttachment.caption'
            ),
            'postGalleries' => array(
                'select' => 'postGalleries.type,postGalleries.caption,postGalleries.source,postGalleries.category_id,postGalleries.subcategory_id',
                'joinType' => "LEFT OUTER JOIN",
                'with' => array(
                    "material" => array(
                        "select" => "material.material_url",
                    )
                )
            ),
            'postTags' => array(
                'select' => 'postTags.id',
                'joinType' => "LEFT OUTER JOIN",
                'with' => array(
                    "tag" => array(
                        "select" => "tag.id,tag.tags_name",
                    )
                )
            ),
            'postAuthor' => array(
                'select' => 'postAuthor.title,postAuthor.image,postAuthor.designation'
            )
            ,
            'freeUser' => array(
                'select' => 'freeUser.first_name,freeUser.middle_name,freeUser.last_name,freeUser.email,freeUser.profile_image,freeUser.designation',
                'joinType' => "LEFT JOIN"
            ),
            'postAssessment' => array(
                'select' => 'postAssessment.title,postAssessment.played',
                'joinType' => "LEFT JOIN"
            )
        );
        $obj_post = $this->find($criteria);


        $formated_post = Settings::formatData($obj_post);

        return $formated_post;
    }

    ///School
    public function getPostTotal($id, $user_type, $target = "school")
    {

        $criteria = new CDbCriteria();
        $criteria->select = 'count(t.id) as total';
        $criteria->compare("t.status", 5);
        $criteria->together = true;
        $criteria->compare("postType.type_id", $user_type);
        if ($target == "school")
        {
            $criteria->addCondition('(t.school_id='.$id.' OR postSchool.school_id='.$id.')');
            
            //$criteria->compare("t.school_id", $id);
            $criteria->compare("t.teacher_id", 0);
        }
        else if ($target == "teacher")
        {
            $criteria->addInCondition('t.teacher_id', explode(",",$id));
            
            //$criteria->compare("t.teacher_id", $id);
            $criteria->compare("t.school_id", 0);
        }
        else
        {
            $criteria->compare("t.byline_id", $id);
            
        }
        $criteria->addCondition("DATE(t.published_date) <= '" . date("Y-m-d") . "'");
        
        $criteria->with = array(
            'postType' => array(
                'select' => '',
                'joinType' => "INNER JOIN"
            ),
            "postSchool" =>array(
                'select' => ''
            )
        );
        $criteria->group = "t.school_id";
        $criteria->order = 't.published_date DESC';

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

    public function getPosts($id, $user_type, $target = "school", $page = 1, $page_size = 10)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        $criteria->together = true;
        $criteria->compare("t.status", 5);

        if ($target == "school")
        {
            $criteria->addCondition('(t.school_id='.$id.' OR postSchool.school_id='.$id.')');
            //$criteria->compare("t.school_id", $id);
            $criteria->compare("t.teacher_id", 0);
        }
        else if ($target == "teacher")
        {
            $criteria->addInCondition('t.teacher_id', explode(",",$id));
            //$criteria->compare("t.teacher_id", $id);
            $criteria->compare("t.school_id", 0);
        }
        else
        {
            $criteria->compare("t.byline_id", $id);
        }

        $criteria->compare("postType.type_id", $user_type);
        $criteria->addCondition("DATE(t.published_date) <= '" . date("Y-m-d") . "'");
        $criteria->order = 't.published_date DESC';

        $criteria->with = array(
            "postType" => array(
                "select" => "",
                'joinType' => "INNER JOIN",
            ),
            "postSchool" =>array(
                'select' => '',
                'joinType' => "LEFT JOIN",
                'with' =>array(
                      'freeUser' => array(
                            'select' => 'freeUser.first_name,freeUser.middle_name,freeUser.last_name,freeUser.email,freeUser.profile_image,freeUser.designation',
                            'joinType' => "LEFT JOIN"
                     )
                )    
            )
        );
        $start = ($page - 1) * $page_size;
        $criteria->limit = $page_size;

        $criteria->offset = $start;


        $obj_post = $this->findAll($criteria);




        $formated_post = $this->formatPostAll($obj_post);

        return $formated_post;
    }

    private function formatPostAll($obj_post)
    {
        $post_array = array();
        $i = 0;
        if($obj_post)
        foreach ($obj_post as $postValue)
        {
            $post_array[$i]['id']             = $postValue->id; 
            $post_array[$i]['postSchool']     = $postValue['postSchool'];
            $i++;
        }
        return $post_array;
    }

    public function formatpost($postValue)
    {
        
        $post_array = array();
        if ($postValue)
        {
            $post_array['title'] = $postValue->headline;
            $post_array['title_color'] = $postValue->headline_color;
            $post_array['short_title'] = $postValue->short_title;
            $post_array['language'] = $postValue->language;
            $post_array['referance_id'] = $postValue->referance_id;
            


            $post_array['video_file'] = "";

            if ($postValue->video_file)
                $post_array['video_file'] = Settings::$image_path . $postValue->video_file;


            $post_array['seen'] = $postValue->view_count;
            $post_array['attach'] = "";
            $post_array['attach_content'] = "";
            $post_array['attach_download_link'] = "";
            $post_array['attachment'] = array();

            if ($postValue['postAttachment'] && count($postValue['postAttachment']) > 0)
            {
                $ai = 0;
                foreach ($postValue['postAttachment'] as $avalue)
                {
                    $post_array['attachment'][$ai]['attach'] = Settings::$image_path . $avalue->file_name;

                    $post_array['attachment'][$ai]['content'] = '<iframe frameborder="0" style="width: 100%; height: 500px;" src="http://docs.google.com/gview?url=' . Settings::$image_path . $avalue->file_name . '&embedded=true"></iframe>';
                    $post_array['attachment'][$ai]['download_link'] = 'http://www.champs21.com/download?f_path=' . $avalue->file_name;

                    $post_array['attachment'][$ai]['caption'] = $avalue->caption;
                    $post_array['attachment'][$ai]['show'] = $avalue->show;
                    $ai++;
                }
            }
            $post_array['post_layout'] = $postValue->post_layout;

            $post_array['sort_title_type'] = $postValue->sort_title_type;
            $post_array['inside_image'] = "";

            if ($postValue->inside_image)
                $post_array['inside_image'] = Settings::get_mobile_image(Settings::$image_path . $postValue->inside_image);


            $post_array['normal_post_type'] = Settings::get_simple_post_layout($postValue);


            $post_array['author'] = "";
            $post_array['author_image'] = "";
            if (isset($postValue['postAuthor']))
            {
                $post_array['author'] = $postValue['postAuthor']->title;
                if ($postValue['postAuthor']->image)
                    $post_array['author_image'] = Settings::$image_path . $postValue['postAuthor']->image;
            }




            $post_array['id'] = $postValue->id;

            if ($postValue->mobile_view_type == 1)
            {
                $post_array['post_type'] = $postValue->mobile_view_type;
                if (isset($postValue->mobile_content) && strlen(Settings::substr_with_unicode($postValue->mobile_content, true)) > 0)
                {
                    $post_array['content'] = $postValue->mobile_content;
                    $post_array['full_content'] = Settings::substr_with_unicode($postValue->mobile_content, true);
                    $post_array['solution'] = Settings::get_solution($postValue->mobile_content);
                }
                else
                {
                    $post_array['content'] = $postValue->content;
                    $post_array['full_content'] = Settings::substr_with_unicode($postValue->content, true);
                    $post_array['solution'] = Settings::get_solution($postValue->content);
                }
                $post_array['summary'] = "";
                //$post_array[$i]['content'] = $postValue['post']->content;
                if ($postValue->summary)
                {

                    $post_array['summary'] = $postValue->summary;
                }
                else
                {

                    $post_array['summary'] = Settings::substr_with_unicode($postValue->content);
                }

                $post_array['images'] = array();

                if ($postValue['postGalleries'])
                {
                    foreach ($postValue['postGalleries'] as $value)
                    {
                        if (trim($value['material']->material_url) && $value->type == 2)
                        {
                            $post_array['images'][] = Settings::get_mobile_image(Settings::$image_path . $value['material']->material_url);
                        }
                    }
                }
            }
            else
            {
                $post_array['post_type'] = $postValue->mobile_view_type;
                if (isset($postValue->mobile_content) && strlen(Settings::substr_with_unicode($postValue->mobile_content, true)) > 0)
                {
                    $post_array['content'] = $postValue->mobile_content;
                    $post_array['full_content'] = Settings::substr_with_unicode($postValue->mobile_content, true);
                    $post_array['solution'] = Settings::get_solution($postValue->mobile_content);
                }
                else
                {
                    $post_array['content'] = $postValue->content;
                    $post_array['full_content'] = Settings::substr_with_unicode($postValue->content, true);
                    $post_array['solution'] = Settings::get_solution($postValue->content);
                }
                $post_array['summary'] = "";
                //$post_array[$i]['content'] = $postValue['post']->content;
                if ($postValue->summary)
                {

                    $post_array['summary'] = $postValue->summary;
                }
                else
                {

                    $post_array['summary'] = Settings::substr_with_unicode($postValue->content);
                }
                $post_array['images'] = array();

                if ($postValue['postGalleries'])
                {
                    foreach ($postValue['postGalleries'] as $value)
                    {
                        if (trim($value['material']->material_url) && $value->type == 2)
                        {
                            $post_array['images'][] = Settings::get_mobile_image(Settings::$image_path . $value['material']->material_url);
                        }
                    }
                }
            }

            $post_array['tags'] = array();

            $j = 0;
            if ($postValue['postTags'])
                foreach ($postValue['postTags'] as $value)
                {
                    $post_array['tags'][$j]['name'] = $value['tag']->tags_name;
                    $post_array['tags'][$j]['id'] = $value['tag']->id;
                    $j++;
                }


            $post_array['share_link'] = Settings::get_post_link_url($postValue);

            $post_array['mobile_image'] = "";
            if ($postValue->mobile_image)
                $post_array['mobile_image'] = Settings::$image_path . $postValue->mobile_image;

            $datestring = Settings::get_post_time($postValue->published_date);

            $post_array['published_date'] = $postValue->published_date;
            //$post_array['attachment'] = $postValue->attach_file;
            $post_array['current_date'] = date("Y-m-d H:i:s");
            $post_array['published_date_string'] = $datestring;

            $post_array['category_menu_icon'] = "";
            $post_array['category_icon'] = "";


            if ($postValue['postCategories'][0]['category']->menu_icon)
                $post_array['category_menu_icon'] = Settings::$image_path . $postValue['postCategories'][0]['category']->menu_icon;

            if ($postValue['postCategories'][0]['category']->icon)
                $post_array['category_icon'] = Settings::$image_path . $postValue['postCategories'][0]['category']->icon;

            $post_array['category_name'] = $postValue['postCategories'][0]['category']->name;
            $post_array['category_id'] = $postValue['postCategories'][0]['category']->id;

            $post_array['inner_priority'] = $postValue['postCategories'][0]->inner_priority;

            $post_array['second_category_name'] = "";
            $post_array['second_category_id'] = $postValue['postCategories'][0]['category']->id;

            if (isset($postValue['postCategories'][1]['category']->name))
            {
                $post_array['second_category_name'] = $postValue['postCategories'][1]['category']->name;
                $post_array['second_category_id'] = $postValue['postCategories'][1]['category']->id;
            }
        }

        return $post_array;
    }

}
