<?php

/**
 * This is the model class for table "period_entries".
 *
 * The followings are the available columns in table 'period_entries':
 * @property integer $id
 * @property string $month_date
 * @property integer $batch_id
 * @property integer $subject_id
 * @property integer $class_timing_id
 * @property integer $employee_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class PeriodEntries extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'period_entries';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                array('batch_id, subject_id, class_timing_id, employee_id, school_id', 'numerical', 'integerOnly'=>true),
                array('month_date, created_at, updated_at', 'safe'),
                // The following rule is used by search().
                // @todo Please remove those attributes that should not be searched.
                array('id, month_date, batch_id, subject_id, class_timing_id, employee_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
            'batchDetails' => array(self::BELONGS_TO, 'Batches', 'batch_id',
                'select' => 'batchDetails.id, batchDetails.name, batchDetails.course_id',
                'joinType' => 'INNER JOIN',
                'with' => array('courseDetails'),
            ),
            'subjectDetails' => array(self::BELONGS_TO, 'Subjects', 'subject_id',
                'select' => 'subjectDetails.id, subjectDetails.name, subjectDetails.code',
                'joinType' => 'INNER JOIN',
            ),
            'classTimingDetails' => array(self::BELONGS_TO, 'ClassTimings', 'class_timing_id',
                'select' => 'classTimingDetails.id, classTimingDetails.batch_id, classTimingDetails.name, classTimingDetails.start_time, classTimingDetails.end_time, classTimingDetails.class_timing_set_id, classTimingDetails.school_id',
                'joinType' => 'INNER JOIN',
                'with' => array('classTimingSetDetails'),
            ),
            'employeesDetails' => array(self::BELONGS_TO, 'Employees', 'employee_id',
                'select' => 'employeesDetails.id, employeesDetails.first_name, employeesDetails.middle_name, employeesDetails.last_name',
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
            'month_date' => 'Month Date',
            'batch_id' => 'Batch',
            'subject_id' => 'Subject',
            'class_timing_id' => 'Class Timing',
            'employee_id' => 'Employee',
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
        $criteria->compare('month_date',$this->month_date,true);
        $criteria->compare('batch_id',$this->batch_id);
        $criteria->compare('subject_id',$this->subject_id);
        $criteria->compare('class_timing_id',$this->class_timing_id);
        $criteria->compare('employee_id',$this->employee_id);
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
     * @return PeriodEntries the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
