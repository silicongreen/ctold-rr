<?php

/**
 * This is the model class for table "tds_user_folder".
 *
 * The followings are the available columns in table 'tds_user_folder':
 * @property integer $id
 * @property string $title
 * @property integer $user_id
 * @property integer $status
 * @property integer $visible
 */
class UserFolder extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tds_user_folder';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title, user_id', 'required'),
            array('user_id, status, visible', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, title, user_id, status, visible', 'safe', 'on' => 'search'),
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
            'Goodread' => array(self::HAS_MANY, 'UserGoodRead', 'folder_id'),
            'UserFree' => array(self::BELONGS_TO, 'Freeusers', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'title' => 'Title',
            'user_id' => 'User',
            'status' => 'Status',
            'visible' => 'Visible',
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
        $criteria->compare('title', $this->title, true);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('status', $this->status);
        $criteria->compare('visible', $this->visible);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return UserFolder the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getFolder($term, $user_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.title,t.id';
        $criteria->compare("title", trim($term));
        $criteria->compare("user_id", $user_id);
        $criteria->limit = 1;
        $obj_folder = $this->find($criteria);
        return $obj_folder;
    }

    public function getAllFolder($user_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.title,t.id';
        $criteria->addCondition("title!='unread'");
        $criteria->compare("user_id", $user_id);
        $obj_folder = $this->findAll($criteria);
        return $this->formatFolder($obj_folder);
    }

    private function formatFolder($obj_folder)
    {
        $folder = array();
        $i = 0;
        if ($obj_folder)
            foreach ($obj_folder as $value)
            {
                $folder[$i]['id'] = $value->id;
                $folder[$i]['title'] = $value->title;
                $i++;
            }
        return $folder;
    }

    public function getPost($user_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id,t.title';
        $criteria->compare("t.user_id", $user_id);
        $criteria->order = "t.id ASC";

        $criteria->with = array(
            'Goodread' => array(
                'select' => '',
                'joinType' => "LEFT JOIN",
                'with' => array(
                    'post' => array(
                        'select' => 'post.id,post.lead_link,post.lead_caption,post.lead_source,post.post_type,post.view_count,post.headline,post.content,post.headline_color,post.summary,post.short_title,post.lead_material,post.mobile_image,post.is_breaking,post.breaking_expire,post.is_exclusive,post.exclusive_expired,post.published_date',
                        'joinType' => "LEFT JOIN",
                        'with' => array(
                            "postCategories" => array(
                                "select" => "",
                                'joinType' => 'LEFT JOIN',
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
                )
            )
        );
        $obj_post_good_read = $this->findAll($criteria);
       
       
        $formated_post = $this->formatPost($obj_post_good_read);

        return $formated_post;
    }

    private function formatPost($obj_post_good_read)
    {
        $post_array = array();
        $j = 0;
       
        if ($obj_post_good_read)
            foreach ($obj_post_good_read as $obj_post)
            {
                
                $i = 0;
                $post_array[$j]['folder_name'] = $obj_post->title;
                $post_array[$j]['post'] = array();
                if (isset($obj_post['Goodread']) && $obj_post['Goodread'])
                    foreach ($obj_post['Goodread'] as $postValue)
                    {

                        if (isset($postValue['post']) && $postValue['post'])
                        {
                            $post_array[$j]['post'][$i]['title'] = $postValue['post']->headline;
                            $post_array[$j]['post'][$i]['post_type'] = $postValue['post']->post_type;



                            $post_array[$j]['post'][$i]['folder'] = $postValue['folder']->title;
                            $post_array[$j]['post'][$i]['title_color'] = $postValue['post']->headline_color;
                            $post_array[$j]['post'][$i]['seen'] = $postValue['post']->view_count;
                            $post_array[$j]['post'][$i]['id'] = $postValue['post']->id;
                            $post_array[$j]['post'][$i]['summary'] = "";


                            //get all images
                            
                            $post_array[$j]['post'][$i]['images'] = array();
                            $post_array[$j]['post'][$i]['add_images'] = array();
                            //$post_array[$i]['images'] = Settings::content_images($postValue['post']->content,true,$postValue['post']->lead_material);
                            if ($postValue['post']['postGalleries'])
                            {
                                $k = 0;
                                foreach ($postValue['post']['postGalleries'] as $value)
                                {
                                    if (trim($value['material']->material_url) && $value->type==2)
                                    {
                                        $post_array[$j]['post'][$i]['images'][] = Settings::get_mobile_image(Settings::$image_path . $value['material']->material_url);

                                         $post_array[$j]['post'][$i]['add_images'][$k]['ad_image'] = Settings::get_mobile_image(Settings::$image_path . $value['material']->material_url);
                                         $post_array[$j]['post'][$i]['add_images'][$k]['ad_image_link'] = $value->source;
                                         $post_array[$j]['post'][$i]['add_images'][$k]['ad_image_caption'] = $value->caption;
                                         $k++;
                                    }
                                }
                            }

                            
//                            $post_array[$j]['post'][$i]['images'] = Settings::content_images($postValue['post']->content,true,$postValue['post']->lead_material);
//                            if ($postValue['post']['postGalleries'])
//                            {
//                                foreach ($postValue['post']['postGalleries'] as $value)
//                                {
//                                    if (trim($value['material']->material_url))
//                                    {
//                                        $post_array[$j]['post'][$i]['images'][] = Settings::get_mobile_image(Settings::$image_path . $value['material']->material_url);
//                                    }
//                                }
//                            }
//                            
//                            //new update
//                            $post_array[$i]['add_images'] = Settings::add_caption_and_link($postValue['post']);

                            $post_array[$j]['post'][$i]['share_link'] = Settings::get_post_link_url($postValue['post']);
                            //new update

                         

                            if ($postValue['post']->summary)
                            {
                                $post_array[$j]['post'][$i]['summary'] = $postValue['post']->summary;
                            }
                            else
                            {

                                $post_array[$j]['post'][$i]['summary'] = Settings::substr_with_unicode($postValue['post']->content);
                            }
                            $post_array[$j]['post'][$i]['mobile_image'] = "";
                            if ($postValue['post']->mobile_image)
                                $post_array[$j]['post'][$i]['mobile_image'] = Settings::get_mobile_image(Settings::$image_path . $postValue['post']->mobile_image);

                            $datestring = Settings::get_post_time($postValue['post']->published_date);
                            
                            $post_array[$j]['post'][$i]['published_date'] = $postValue['post']->published_date;
                            $post_array[$j]['post'][$i]['current_date'] = date("Y-m-d H:i:s");
                            $post_array[$j]['post'][$i]['published_date_string'] = $datestring;

                            $post_array[$j]['post'][$i]['category_menu_icon'] = "";
                            $post_array[$j]['post'][$i]['category_icon'] = "";

                            if ($postValue['post']['postCategories'][0]['category']->menu_icon)
                                $post_array[$j]['post'][$i]['category_menu_icon'] = Settings::$image_path . $postValue['post']['postCategories'][0]['category']->menu_icon;

                            if ($postValue['post']['postCategories'][0]['category']->icon)
                                $post_array[$j]['post'][$i]['category_icon'] = Settings::$image_path . $postValue['post']['postCategories'][0]['category']->icon;

                            $post_array[$j]['post'][$i]['category_name'] = $postValue['post']['postCategories'][0]['category']->name;
                            $post_array[$j]['post'][$i]['category_id'] = $postValue['post']['postCategories'][0]['category']->id;
                            $i++;
                        }
                    }
                $j++;
            }
        return $post_array;
    }
    function createGoodReadFolder($user_id)
    {
        $folder_array = Settings::$ar_default_folder;
        
        foreach($folder_array as $value)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id';
            $criteria->compare("t.user_id", $user_id);
            $criteria->compare("t.title", $value);
            $obj_folder = $this->find($criteria);
            if(!$obj_folder || count($obj_folder)==0)
            {
                $obj_userFolder = new UserFolder();
                $obj_userFolder->title = $value;
                $obj_userFolder->user_id = $user_id;
                $obj_userFolder->save();
            }
            
        }
        return true;
    }
    

}
