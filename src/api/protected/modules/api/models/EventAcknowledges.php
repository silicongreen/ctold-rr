<?php

/**
 * This is the model class for table "news_comments".
 *
 * The followings are the available columns in table 'news_comments':
 * @property integer $id
 * @property integer $event_id
 * @property integer $acknowledged_by
 * @property integer $acknowledged_by_id
 * @property integer $school_id
 * @property integer $status
 */
class EventAcknowledges extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'event_acknowledges';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('event_id, acknowledged_by, acknowledged_by_id, school_id, status', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, event_id, acknowledged_by, acknowledged_by_id, school_id, status', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'eventDetails' => array(self::BELONGS_TO, 'Events', 'event_id',
                'select' => 'eventDetails.id, eventDetails.title, eventDetails.description, eventDetails.start_date, eventDetails.end_date, eventDetails.is_common, eventDetails.event_category_id',
                'joinType' => 'INNER JOIN',
                'with' => array('eventCategory'),
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'event_id' => 'Title',
            'acknowledged_by' => 'Acknowledged By',
            'acknowledged_by_id' => 'Acknowledged By Id',
            'school_id' => 'School',
            'status' => 'Status',
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
        $criteria->compare('event_id', $this->event_id);
        $criteria->compare('acknowledged_by', $this->acknowledged_by);
        $criteria->compare('acknowledged_by_id', $this->acknowledged_by_id);
        $criteria->compare('school_id', $this->school_id);
        $criteria->compare('status', $this->status);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return NewsComments the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function acknowledgeEvent($event_id, $status, $school_id = '') {

        $school_id = (!empty($school_id)) ? $school_id : Yii::app()->user->schoolId;

        $criteria = new CDbCriteria;
        $criteria->compare('event_id', $event_id);

        if (Yii::app()->user->isStudent) {
            $ack_by = 0;
        }

        if (Yii::app()->user->isParent) {
            $ack_by = 1;
        }
        if (Yii::app()->user->isTeacher) {
            $ack_by = 2;
        }
        if (Yii::app()->user->isAdmin) {
            $ack_by = 3;
        }

        $ack_by_id = Yii::app()->user->profileId;

        $criteria->compare('acknowledged_by', $ack_by);
        $criteria->compare('acknowledged_by_id', $ack_by_id);
        $criteria->compare('school_id', $school_id);

        $event = $this->find($criteria);

        if (empty($event)) {

            $this->event_id = $event_id;
            $this->acknowledged_by = $ack_by;
            $this->acknowledged_by_id = $ack_by_id;
            $this->school_id = $school_id;
            $this->status = $status;

            if ($this->insert()) {

                /*
                  $_data['event_id'] = $this->event_id;
                  $_data['acknowledged_by'] = Settings::$ar_notice_acknowledge_by[$this->acknowledged_by];
                  $_data['acknowledged_by_id'] = $this->acknowledged_by_id;
                  $_data['acknowledge_status'] = $this->status;
                  $_data['acknowledge_msg'] = Settings::$ar_event_status[$this->status];
                 */
                return $_data['acknowledge_status'] = (int) $this->status;
            }
            return false;
        } else {
            $event->status = $status;
            if ($event->update()) {

                /*
                  $_data['event_id'] = $event->event_id;
                  $_data['acknowledged_by'] = Settings::$ar_notice_acknowledge_by[$event->acknowledged_by];
                  $_data['acknowledged_by_id'] = $event->acknowledged_by_id;
                  $_data['acknowledge_status'] = $event->status;
                  $_data['acknowledge_msg'] = Settings::$ar_event_status[$event->status];
                 */
                return $_data['acknowledge_status'] = (int) $event->status;
            }
            return false;
        }
        return false;
    }

    public function acknowledgeClubJoin($event_id, $child_id, $school_id = '') {

        $school_id = (!empty($school_id)) ? $school_id : Yii::app()->user->schoolId;

        $status = '0';
        if (Yii::app()->user->isParent) {
            $status = '1';
        }
        
        $criteria = new CDbCriteria;
        $criteria->compare('event_id', $event_id);
        $criteria->compare('acknowledged_by', 0);
        $criteria->compare('acknowledged_by_id', $child_id);
        $criteria->compare('school_id', $school_id);

        $event = $this->find($criteria);

        if (!empty($event)) {
            $event->acknowledged_by = $status;
            $event->status = $status;
            if ($event->update()) {
                return $_data['acknowledge_status'] = $status;
            }
            return false;
        } else {
            $event = new self;
            $event->event_id = $event_id;
            $event->acknowledged_by = 0;
            $event->acknowledged_by_id = $child_id;
            $event->school_id = $school_id;
            $event->status = $status;

            if ($event->insert()) {
                return $_data['acknowledge_status'] = $status;
            }
            return false;
        }
        return false;
    }

    public function getClubJoinNotifications($ar_childern_id, $school_id = '') {

        $school_id = (!empty($school_id)) ? $school_id : Yii::app()->user->schoolId;

        $criteria = new CDbCriteria;

        $criteria->select = 't.id, t.acknowledged_by_id';
        $criteria->compare('t.acknowledged_by', 0);

        $criteria->addInCondition('t.acknowledged_by_id', $ar_childern_id);
        $criteria->compare('eventCategory.is_club', 1);
        $criteria->compare('t.school_id', $school_id);
        $criteria->compare('t.status', 0);
        $criteria->together = true;

        $event = $this->with('eventDetails')->findAll($criteria);

        if (!empty($event)) {
            return $this->formatClubNotifications($event);
        }
        return false;
    }

    public function formatClubNotifications($event) {

        $formatted_data = array();

        foreach ($event as $row) {

            $_data['club_id'] = $row['eventDetails']->id;
            $_data['club_title'] = $row['eventDetails']->title;
            $_data['club_category_id'] = $row['eventDetails']->event_category_id;
            $_data['club_category_name'] = $row['eventDetails']['eventCategory']->name;
            $_data['club_description'] = $row['eventDetails']->description;
            $_data['applied_by'] = $row->acknowledged_by_id;

            $formatted_events[] = $_data;
        }

        return $formatted_events;
    }

    public function getEventAcknowledgeData($school_id, $event_id) {

        if (Yii::app()->user->isStudent) {
            $ack_by = 0;
        }

        if (Yii::app()->user->isParent) {
            $ack_by = 1;
        }

        if (Yii::app()->user->isTeacher) {
            $ack_by = 2;
        }
        if (Yii::app()->user->isAdmin) {
            $ack_by = 3;
        }

        $ack_by_id = Yii::app()->user->profileId;

        $criteria = new CDbCriteria;
        $criteria->compare('t.event_id', $event_id);
        $criteria->compare('t.school_id', $school_id);
        $criteria->compare('t.acknowledged_by', $ack_by);
        $criteria->compare('t.acknowledged_by_id', $ack_by_id);

        $event_ack = $this->find($criteria);

        return (!empty($event_ack)) ? $event_ack->status : 2;
    }

    public function getClubAcknowledgeData($school_id, $event_id, $student_id = NULL) {

        if (Yii::app()->user->isStudent) {
            $student_id = Yii::app()->user->profileId;
        }

        if (Yii::app()->user->isParent) {
            $student_id = $student_id;
        }

        $criteria = new CDbCriteria;

        $criteria->compare('t.event_id', $event_id);
        $criteria->compare('t.acknowledged_by_id', $student_id);
        $criteria->compare('t.school_id', $school_id);

        $event_ack = $this->find($criteria);

        return (!empty($event_ack)) ? $event_ack->status : 2;
    }

}
