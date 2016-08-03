<?php

/**
 * This is the model class for table "tds_categories".
 *
 * The followings are the available columns in table 'tds_categories':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $embedded
 * @property string $cover
 * @property string $icon
 * @property string $menu_icon
 * @property integer $status
 * @property integer $parent_id
 * @property integer $category_type_id
 * @property integer $priority
 * @property string $background_color
 * @property integer $enable_sort
 * @property integer $weekly_priority
 *
 * The followings are the available model relations:
 * @property CategoryType $categoryType
 * @property PostCategory[] $postCategories
 * @property WhatsOn[] $whatsOns
 */
class Categories extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tds_categories';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, description, embedded', 'required'),
            array('status, parent_id, category_type_id, priority, enable_sort, weekly_priority', 'numerical', 'integerOnly' => true),
            array('name, cover, icon, menu_icon', 'length', 'max' => 255),
            array('background_color', 'length', 'max' => 7),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, description, embedded, cover, icon, menu_icon, status, parent_id, category_type_id, priority, background_color, enable_sort, weekly_priority', 'safe', 'on' => 'search'),
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
            'categoryType' => array(self::BELONGS_TO, 'CategoryType', 'category_type_id'),
            'postCategories' => array(self::HAS_MANY, 'PostCategory', 'category_id'),
            'whatsOns' => array(self::HAS_MANY, 'WhatsOn', 'category_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'embedded' => 'Embedded',
            'cover' => 'Cover',
            'icon' => 'Icon',
            'menu_icon' => 'Menu Icon',
            'status' => '0- disable 1= display',
            'parent_id' => 'Parent',
            'category_type_id' => 'Category Type',
            'priority' => 'For Category sorting',
            'background_color' => 'Background Color',
            'enable_sort' => 'Enable Sort',
            'weekly_priority' => 'Weekly Priority',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('embedded', $this->embedded, true);
        $criteria->compare('cover', $this->cover, true);
        $criteria->compare('icon', $this->icon, true);
        $criteria->compare('menu_icon', $this->menu_icon, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('parent_id', $this->parent_id);
        $criteria->compare('category_type_id', $this->category_type_id);
        $criteria->compare('priority', $this->priority);
        $criteria->compare('background_color', $this->background_color, true);
        $criteria->compare('enable_sort', $this->enable_sort);
        $criteria->compare('weekly_priority', $this->weekly_priority);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Categories the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    public function getParentCategory($user_id = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->addCondition("t.parent_id IS NULL OR t.parent_id = ''");
        $criteria->compare("t.status", 1);
        if($user_id == 0)
            $criteria->compare("t.category_type", 1);
        
        $criteria->select = "t.id,t.name,t.display_name";
        
        $criteria->order = "t.priority ASC";
        $obj_category = $this->findAll($criteria);
        $categories = array();
        $i = 0;
        foreach ($obj_category as $value)
        {
            $categories[$i]['name'] = $value->name;
            $categories[$i]['display_name'] = $value->display_name;
            $categories[$i]['id'] = $value->id;
            $i++;
        }
        return $categories;
    }

    public function getSubcategory($parent_id)
    {
        $criteria = new CDbCriteria;
        $criteria->compare("t.parent_id", $parent_id);
        $criteria->select = "t.id,t.name";
        $criteria->compare("t.status", 1);
        $criteria->order = "t.priority ASC";
        $obj_category = $this->findAll($criteria);
        $categories = array();
        if(count($obj_category)>0)
        {
            $categories[0]['name'] = "All";
            $categories[0]['id'] = $parent_id;
        }
        $i = 1;
        foreach ($obj_category as $value)
        {
            $categories[$i]['name'] = $value->name;
            $categories[$i]['id'] = $value->id;
            $i++;
        }
        return $categories;
    }
     public function getParentString($parent_id)
    {
        $criteria = new CDbCriteria;
        
        $criteria->select = "t.parent_id,t.name,t.id";
        $criteria->compare("t.id", $parent_id);
        $obj_category = $this->find($criteria);
        $string = "";
        if(isset($obj_category->name))
        {
            if($obj_category->parent_id!=NULL)
            $string = $this->getParentString($obj_category->parent_id);
            
            $string .= $obj_category->name." > ";
        }
        return $string;
    }

    public function getSearchCategory($term)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.parent_id,t.name,t.id';
        $criteria->together = true;
        $criteria->addCondition("t.name LIKE '%" . $term . "%'");
        $criteria->limit = 3;
        $obj_category = $this->findAll($criteria);
        $category = array();
        foreach ($obj_category as $value)
        {
            $merge['id'] = $value->id;
            $merge['title'] = "";
            if($value->parent_id!=NULL)
            $merge['title'] = $this->getParentString($value->parent_id);
            
            $merge['title'] .=$value->name;
            $category[] = $merge;
        }

        return $category;
    }
    
    public function all_cats_in_relative_manner($parent_id = 0)
    {
        
        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.parent_id, t.name, t.display_name, t.menu_icon';
        
        $criteria->compare('t.status', 1);
        $criteria->compare('t.show', 1);
        
        if (empty($parent_id)) {
            $criteria->addCondition("t.parent_id IS NULL OR t.parent_id = ''");
        } else {
            //echo $parent_id . '<br />';
            $criteria->compare('t.parent_id', $parent_id);
        }
        
        $criteria->order = "t.id ASC";
        
        $obj_category = $this->findAll($criteria);
        
        $ar_caterogies = array();
        
        $icon_url = '';
        
        if (!empty($obj_category)) {
            
            foreach ($obj_category as $key => $value) {
                
                $ar_category = array();
                
                $cat_name = (!empty($value->display_name)) ? $value->display_name : $value->name;

                if (empty($parent_id)) {
                    $icon_url = (!empty($value->menu_icon)) ? Settings::$image_path . $value->menu_icon : Settings::$image_path . 'styles/layouts/tdsfront/image/C.png';
                    $ar_category['icon_url'] = $icon_url;
                }

                $ar_category['id'] = $value->id;
                $ar_category['name'] = $cat_name;
                $ar_category['sub_categories'] = $this->all_cats_in_relative_manner($value->id);
                
                $ar_caterogies[] = $ar_category;

            }
            
        }

        return $ar_caterogies;
    }

}
