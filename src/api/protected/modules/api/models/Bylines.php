<?php

/**
 * This is the model class for table "tds_bylines".
 *
 * The followings are the available columns in table 'tds_bylines':
 * @property integer $id
 * @property string $title
 * @property string $created
 * @property string $updated
 * @property integer $is_columnist
 * @property integer $priority
 * @property string $image
 * @property integer $is_feature
 */
class Bylines extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tds_bylines';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title', 'required')
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
            'created' => 'Created',
            'updated' => 'Updated',
            'is_columnist' => 'Whether the Byliner is a columnist',
            'priority' => 'Priority',
            'image' => 'Image',
            'is_feature' => 'Is Feature',
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
        $criteria->compare('created', $this->created, true);
        $criteria->compare('updated', $this->updated, true);
        $criteria->compare('is_columnist', $this->is_columnist);
        $criteria->compare('priority', $this->priority);
        $criteria->compare('image', $this->image, true);
        $criteria->compare('is_feature', $this->is_feature);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Bylines the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function generate_byline_id($term)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        $criteria->compare("title", trim($term));
        $criteria->limit = 1;
        $obj_bylines = $this->find($criteria);
        if($obj_bylines)
        {
            return $obj_bylines->id;
        }
        else
        {
            $obj_byline = new Bylines();
            $obj_byline->title = $term;
            $obj_byline->save();
            return $obj_byline->id;
        }    
        
    }

    public function getSearchBylines($term)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.title,t.id';
        $criteria->together = true;
        $criteria->addCondition("t.title LIKE '%" . $term . "%'");
        $criteria->limit = 3;
        $obj_category = $this->findAll($criteria);
        $category = array();
        foreach ($obj_category as $value)
        {
            $merge['id'] = $value->id;
            $merge['title'] = $value->title;
            $category[] = $merge;
        }

        return $category;
    }

    

}
