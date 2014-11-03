<?php

/**
 * This is the model class for table "news_comments".
 *
 * The followings are the available columns in table 'news_comments':
 * @property integer $id
 * @property integer $news_id
 * @property integer $acknowledged_by
 * @property integer $acknowledged_by_id
 * @property integer $school_id
 * @property integer $status
 */
class NewsAcknowledges extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'news_acknowledges';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('news_id, acknowledged_by, acknowledged_by_id, school_id, status', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, news_id, acknowledged_by, acknowledged_by_id, school_id, status', 'safe', 'on' => 'search'),
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
            'news_id' => 'News',
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
        $criteria->compare('news_id', $this->news_id);
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

    public function acknowledgeNotice($notice_id, $school_id = '') {

        $school_id = (!empty($school_id)) ? $school_id : Yii::app()->user->schoolId;

        $criteria = new CDbCriteria;
        $criteria->compare('news_id', $notice_id);

        if (Yii::app()->user->isStudent) {
            $ack_by = '0';
        }

        if (Yii::app()->user->isParent) {
            $ack_by = '1';
        }

        $ack_by_id = Yii::app()->user->profileId;

        $criteria->compare('acknowledged_by', $ack_by);
        $criteria->compare('acknowledged_by_id', $ack_by_id);
        $criteria->compare('school_id', $school_id);

        $notice = $this->find($criteria);

        if (empty($notice)) {
            $notice = new NewsAcknowledges;
            $notice->news_id = $notice_id;
            $notice->acknowledged_by = $ack_by;
            $notice->acknowledged_by_id = $ack_by_id;
            $notice->school_id = $school_id;
            $notice->status = 1;

            if ($notice->insert()) {
                $_data['notice_id'] = $notice->news_id;
                $_data['acknowledged_by'] = Settings::$ar_notice_acknowledge_by[$notice->acknowledged_by];
                $_data['acknowledged_by_id'] = $notice->acknowledged_by_id;
                $_data['acknowledge_status'] = $notice->status;
                $_data['acknowledge_msg'] = Settings::$ar_notice_acknowledge_status[$notice->status];
                return $_data;
            }
            return false;
        }
        return false;
    }

}
