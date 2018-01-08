<?php

/**
 * This is the model class for table "taggings".
 *
 * The followings are the available columns in table 'taggings':
 * @property integer $id
 * @property integer $tag_id
 * @property integer $taggable_id
 * @property string $taggable_type
 * @property string $created_at
 * @property integer $school_id
 * @property string $updated_at
 */
class Taggings extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'taggings';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('tag_id, taggable_id, school_id', 'numerical', 'integerOnly' => true),
            array('taggable_type', 'length', 'max' => 255),
            array('created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, tag_id, taggable_id, taggable_type, created_at, school_id, updated_at', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'tagDetails' => array(self::BELONGS_TO, 'Tags', 'tag_id',
                'joinType' => 'LEFT JOIN',
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'tag_id' => 'Tag',
            'taggable_id' => 'Taggable',
            'taggable_type' => 'Taggable Type',
            'created_at' => 'Created At',
            'school_id' => 'School',
            'updated_at' => 'Updated At',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('tag_id', $this->tag_id);
        $criteria->compare('taggable_id', $this->taggable_id);
        $criteria->compare('taggable_type', $this->taggable_type, true);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('school_id', $this->school_id);
        $criteria->compare('updated_at', $this->updated_at, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Taggings the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
