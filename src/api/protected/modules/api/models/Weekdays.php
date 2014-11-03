<?php

/**
 * This is the model class for table "weekdays".
 *
 * The followings are the available columns in table 'weekdays':
 * @property integer $id
 * @property integer $batch_id
 * @property string $weekday
 * @property string $name
 * @property integer $sort_order
 * @property integer $day_of_week
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Weekdays extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'weekdays';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('batch_id, sort_order, day_of_week, is_deleted, school_id', 'numerical', 'integerOnly' => true),
            array('weekday, name', 'length', 'max' => 255),
            array('created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, batch_id, weekday, name, sort_order, day_of_week, is_deleted, created_at, updated_at, school_id', 'safe', 'on' => 'search'),
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
            'batch_id' => 'Batch',
            'weekday' => 'Weekday',
            'name' => 'Name',
            'sort_order' => 'Sort Order',
            'day_of_week' => 'Day Of Week',
            'is_deleted' => 'Is Deleted',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('batch_id', $this->batch_id);
        $criteria->compare('weekday', $this->weekday, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('sort_order', $this->sort_order);
        $criteria->compare('day_of_week', $this->day_of_week);
        $criteria->compare('is_deleted', $this->is_deleted);
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
     * @return Weekdays the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getWorkingDays($school_id) {
        
        $timetable = new TimeTableWeekdays();
        $week_day = $timetable->getWeekDaySet($school_id);
        $weekdays_set = new WeekdaySetsWeekdays();
        $weekdays_set->setAttribute("weekday_set_id", $week_day->weekday_set_id);
        $weekdays = $weekdays_set->getWeekDays();
        
        if(!empty($weekdays)){
            $weekday_ids = Settings::extractIds($weekdays, 'weekday_id');
            return $weekday_ids;
        }
        
        return false;
    }
}
