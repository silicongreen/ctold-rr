<?php

/**
 * This is the model class for table "tds_free_user_preference".
 *
 * The followings are the available columns in table 'tds_free_user_preference':
 * @property integer $id
 * @property integer $free_user_id
 * @property string $category_ids
 */
class FreeUserPreference extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'tds_free_user_preference';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('free_user_id', 'required'),
            array('free_user_id', 'numerical', 'integerOnly' => true),
            array('category_ids', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, free_user_id, category_ids', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'free_user_id' => 'Free User',
            'category_ids' => 'Category Ids',
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
        $criteria->compare('free_user_id', $this->free_user_id);
        $criteria->compare('category_ids', $this->category_ids, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return FreeUserPreference the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

}
