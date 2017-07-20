<?php

/**
 * This is the model class for table "tds_school_page".
 *
 * The followings are the available columns in table 'tds_school_page':
 * @property integer $id
 * @property integer $school_id
 * @property integer $menu_id
 * @property string $title
 * @property string $content
 * @property string $date
 */
class SchoolPage extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tds_school_page';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('school_id, menu_id, title, content, date', 'required'),
            array('school_id, menu_id', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, school_id, menu_id, title, content, date', 'safe', 'on' => 'search'),
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
            'schoolmenu' => array(self::BELONGS_TO, 'SchoolMenu', 'menu_id'),
            'pageGalleries' => array(self::HAS_MANY, 'SchoolPageGallery', 'page_id'),
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
            'menu_id' => 'Menu',
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
        $criteria->compare('menu_id', $this->menu_id);
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
     * @return SchoolPage the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function pageDetails($page_id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = "t.title,t.content,t.mobile_content";
        $criteria->compare("t.id", $page_id);

        $criteria->with = array(
            'pageGalleries' => array(
                'select' => '',
                'with' => array(
                    "material" => array(
                        "select" => "material.material_url",
                    )
                )
            ),
        );
        
        $scholl_page = $this->find($criteria);
        
        $page = array();
        
        if($scholl_page)
        {
            if(isset($scholl_page->mobile_content) && strlen(Settings::substr_with_unicode($scholl_page->mobile_content))>0)
            {
               $page['content'] = Settings::substr_with_unicode($scholl_page->mobile_content,true);
               $page['web-view'] = $scholl_page->mobile_content;
               $all_image = Settings::content_images($scholl_page->mobile_content);
            }
            else
            {
               $page['content'] = Settings::substr_with_unicode($scholl_page->content,true);
               $page['web-view'] = $scholl_page->content;
               $all_image = Settings::content_images($scholl_page->content);
            }
            $page['title'] = $scholl_page->title;
            $page['image'] = "";
            if(isset($all_image[0]))
            $page['image'] = $all_image[0];
            
            $page['gallery'] = array();
            if ($scholl_page['pageGalleries'])
            {
                foreach ($scholl_page['pageGalleries'] as $value)
                {
                    if (trim($value['material']->material_url))
                    {
                        $page['gallery'][] = Settings::get_mobile_image(Settings::$image_path . $value['material']->material_url);
                    }
                }
            }
        }
        
        return $page;
    }

    public function getSchoolPages($school_id)
    {
       
        
        $criteria = new CDbCriteria();
        $criteria->select = "t.title,t.content,t.mobile_content";
        $criteria->compare("t.school_id", $school_id);

        $criteria->with = array(
            "schoolmenu" => array(
                "select" => "schoolmenu.id,schoolmenu.title",
                'joinType' => "INNER JOIN"
            ),
            'pageGalleries' => array(
                'select' => '',
                'with' => array(
                    "material" => array(
                        "select" => "material.material_url",
                    )
                )
            ),
        );
        $criteria->order = "schoolmenu.id ASC";
        $scholl_pages = $this->findAll($criteria);

        $page_array = array();
        if ($scholl_pages)
        {
            $i = 0;
            foreach ($scholl_pages as $value)
            {
                $page_array[$i]['id'] = $value->id;
                $page_array[$i]['menu_id'] = $value['schoolmenu']->id;
                $page_array[$i]['name'] = $value['schoolmenu']->title;
                $page_array[$i]['gallery'] = array();
                if ($value['pageGalleries'])
                {
                    foreach ($value['pageGalleries'] as $gvalue)
                    {
                        if (trim($gvalue['material']->material_url))
                        {
                            $page[$i]['gallery'][] = Settings::get_mobile_image(Settings::$image_path . $gvalue['material']->material_url);
                        }
                    }
                }
                if(isset($value->mobile_content) && strlen(Settings::substr_with_unicode($value->mobile_content))>0)
                {
                   $page_array[$i]['content'] = Settings::substr_with_unicode($value->mobile_content,true);
                   $page_array[$i]['web-view'] = $value->mobile_content;
                   $all_image = Settings::content_images($value->mobile_content);
                }
                else
                {
                   $page['content'] = Settings::substr_with_unicode($value->content,true);
                   $page_array[$i]['web-view'] = $value->content;
                   $all_image = Settings::content_images($value->content);
                }
                $page_array[$i]['title'] = $value->title;
                $page_array[$i]['image'] = "";
                if(isset($all_image[0]))
                $page_array[$i]['image'] = $all_image[0];
                
                
                $i++;
            }
        }

        return $page_array;
    }

}
