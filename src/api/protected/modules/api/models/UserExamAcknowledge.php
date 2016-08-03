<?php

/**
 * This is the model class for table "additional_exam_groups".
 *
 * The followings are the available columns in table 'additional_exam_groups':
 * @property integer $id
 * @property string $name
 * @property integer $batch_id
 * @property string $exam_type
 * @property integer $is_published
 * @property integer $result_published
 * @property string $students_list
 * @property string $exam_date
 */
class UserExamAcknowledge extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'user_exam_acknowledge';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('exam_id, acknowledge_by, acknowledge_by_id, school_id', 'numerical', 'integerOnly' => true),
            array('exam_id, acknowledge_by, acknowledge_by_id, school_id', 'required'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, exam_id, acknowledge_by, acknowledge_by_id, school_id', 'safe', 'on' => 'search'),
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
            'exam_id' => 'Exam',
            'acknowledge_by' => 'Acknowledged By',
            'acknowledge_by_id' => 'Acknowledged By Id',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('exam_id', $this->exam_id);
        $criteria->compare('acknowledge_by', $this->acknowledged_by);
        $criteria->compare('acknowledge_by_id', $this->acknowledged_by_id);
        $criteria->compare('school_id', $this->school_id);
      

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AdditionalExamGroups the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
     public function acknowledgeExam($exam_id, $school_id = '') {

        $school_id = (!empty($school_id)) ? $school_id : Yii::app()->user->schoolId;

        $criteria = new CDbCriteria;
        $criteria->compare('exam_id', $exam_id);

        if (Yii::app()->user->isStudent) {
            $ack_by = '0';
        }

        if (Yii::app()->user->isParent) {
            $ack_by = '1';
        }

        $ack_by_id = Yii::app()->user->profileId;

        $criteria->compare('acknowledge_by', $ack_by);
        $criteria->compare('acknowledge_by_id', $ack_by_id);
        $criteria->compare('school_id', $school_id);

        $notice = $this->find($criteria);

        if (empty($notice)) {
            $notice = new UserExamAcknowledge;
            $notice->exam_id = $exam_id;
            $notice->acknowledge_by = $ack_by;
            $notice->acknowledge_by_id = $ack_by_id;
            $notice->school_id = $school_id;

            if ($notice->insert()) {
                $_data['exam_id'] = $notice->exam_id;
                $_data['acknowledged_by'] = Settings::$ar_notice_acknowledge_by[$notice->acknowledge_by];
                $_data['acknowledged_by_id'] = $notice->acknowledge_by_id;
                return $_data;
            }
            return false;
        }
        return false;
    }

}
