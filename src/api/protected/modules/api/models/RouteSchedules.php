<?php

/**
 * This is the model class for table "transports".
 *
 * The followings are the available columns in table 'transports':
 * @property integer $id
 * @property integer $route_id
 * @property integer $weekday_id
 * @property string $home_pickup_time
 * @property string $school_pickup_time
 * @property integer $school_id
 */
class RouteSchedules extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'route_schedules';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('route_id, weekday_id, school_id', 'numerical', 'integerOnly' => true),
            array('home_pickup_time, school_pickup_time', 'length', 'max' => 8),
            array('home_pickup_time, school_pickup_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, route_id, weekday_id, home_pickup_time, school_pickup_time, school_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'routeDetails' => array(self::BELONGS_TO, 'Routes', 'route_id',
                'joinType' => 'INNER JOIN',
            ),
            'transportDetails' => array(self::HAS_MANY, 'Transports', '',
                'on' => 'transportDetails.route_id = t.route_id',
                'joinType' => 'INNER JOIN',
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'route_id' => 'Route',
            'weekday_id' => 'Weekday',
            'home_pickup_time' => 'Home Pickup Time',
            'school_pickup_time' => 'School Pickup Time',
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
        $criteria->compare('route_id', $this->route_id);
        $criteria->compare('weekday_id', $this->weekday_id);
        $criteria->compare('home_pickup_time', $this->home_pickup_time, true);
        $criteria->compare('school_pickup_time', $this->school_pickup_time, true);
        $criteria->compare('school_id', $this->school_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Transports the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getRouteSchedule($receiver_type, $receiver_id = null) {
        
        $receiver_id = (!empty($receiver_id)) ? $receiver_id : Yii::app()->user->profileId;
        
        $criteria = new CDbCriteria;
        
        $criteria->select = 't.id, t.home_pickup_time, t.school_pickup_time, t.weekday_id';
        $criteria->with = array(
            'routeDetails' => array(
                'select' => 'routeDetails.destination, routeDetails.updated_at',
            ),
            'transportDetails' => array(
                'select' => 'transportDetails.id',
            ),
        );
        
        $criteria->compare('transportDetails.receiver_id', $receiver_id);
        $criteria->compare('transportDetails.receiver_type', $receiver_type);
        
        $data = $this->findAll($criteria);
        
        return (!empty($data)) ? $this->formatRouteSchedule($data) : false;
    }
    
    public function formatRouteSchedule($obj_route_schedule) {
        
        $formatted_tansports = array();
        
        $pickup_location = $obj_route_schedule[0]['routeDetails']->destination;
        $drop_location = $obj_route_schedule[0]['routeDetails']->destination;
        
        $formatted_tansports['location']['pickup'] = $pickup_location;
        $formatted_tansports['location']['drop'] = $drop_location;
        $formatted_tansports['location']['last_updated'] = date('d/m/Y', strtotime($obj_route_schedule[0]['routeDetails']->updated_at));
        
        foreach ($obj_route_schedule as $row) {
            
            $_data['transport_weekday_id'] = $row->weekday_id;
            $_data['transport_weekday'] = Settings::$ar_weekdays[$row->weekday_id];
            $_data['transport_home_pickup'] = date('h:i a', strtotime($row->home_pickup_time));
            $_data['transport_school_pickup'] = date('h:i a', strtotime($row->school_pickup_time));
            
            $formatted_tansports['schedule'][] = $_data; 
        }
        
        return $formatted_tansports;
    }

}
