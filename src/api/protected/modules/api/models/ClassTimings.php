<?php

/**
 * This is the model class for table "class_timings".
 *
 * The followings are the available columns in table 'class_timings':
 * @property integer $id
 * @property integer $batch_id
 * @property string $name
 * @property string $start_time
 * @property string $end_time
 * @property integer $is_break
 * @property integer $is_deleted
 * @property integer $class_timing_set_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class ClassTimings extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'class_timings';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('batch_id, is_break, is_deleted, class_timing_set_id, school_id', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>255),
            array('start_time, end_time, created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, batch_id, name, start_time, end_time, is_break, is_deleted, class_timing_set_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
            'classTimingSetDetails' => array(self::BELONGS_TO, 'ClassTimingSets', 'class_timing_set_id',
                'select' => 'classTimingSetDetails.id, classTimingSetDetails.name',
                'joinType' => 'INNER JOIN',
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'batch_id' => 'Batch',
            'name' => 'Name',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'is_break' => 'Is Break',
            'is_deleted' => 'Is Deleted',
            'class_timing_set_id' => 'Class Timing Set',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'school_id' => 'School',
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

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('batch_id',$this->batch_id);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('start_time',$this->start_time,true);
        $criteria->compare('end_time',$this->end_time,true);
        $criteria->compare('is_break',$this->is_break);
        $criteria->compare('is_deleted',$this->is_deleted);
        $criteria->compare('class_timing_set_id',$this->class_timing_set_id);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('updated_at',$this->updated_at,true);
        $criteria->compare('school_id',$this->school_id);

        return new CActiveDataProvider($this, array(
                'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ClassTimings the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
