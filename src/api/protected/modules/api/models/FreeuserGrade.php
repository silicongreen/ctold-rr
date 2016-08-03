<?php

/**
 * This is the model class for table "exam_groups".
 *
 * The followings are the available columns in table 'exam_groups':
 * @property integer $id
 * @property string $name
 * @property integer $batch_id
 * @property string $exam_type
 * @property integer $is_published
 * @property integer $result_published
 * @property string $exam_date
 * @property integer $is_final_exam
 * @property integer $cce_exam_category_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class FreeuserGrade extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'freeuser_grade';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
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
            'freeuser' => array(self::BELONGS_TO, 'freeuser', 'freeuser_id',
                'joinType' => 'LEFT JOIN'
            )
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'freeuser_id' => 'Free User ID',
            'grade_id' => 'Grade Id'
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
        $criteria->compare('batch_id', $this->batch_id);
        $criteria->compare('exam_type', $this->exam_type, true);
        $criteria->compare('is_published', $this->is_published);
        $criteria->compare('result_published', $this->result_published);
        $criteria->compare('exam_date', $this->exam_date, true);
        $criteria->compare('is_final_exam', $this->is_final_exam);
        $criteria->compare('cce_exam_category_id', $this->cce_exam_category_id);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('school_id', $this->school_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ExamGroups the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    

}
