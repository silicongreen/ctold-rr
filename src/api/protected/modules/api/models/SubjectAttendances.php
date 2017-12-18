<?php

/**
 * This is the model class for table "subject_attendances".
 *
 * The followings are the available columns in table 'subject_attendances':
 * @property integer $id
 * @property integer $student_id
 * @property integer $subject_id
 * @property integer $batch_id
 * @property string $reason
 * @property string $attendance_date
 * @property string $updated_at
 * @property string $created_at
 * @property integer $school_id
 * @property integer $is_late
 */
class SubjectAttendances extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public $total = 0;
    public function tableName()
    {
        return 'subject_attendances';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('subject_id, batch_id', 'required'),
            array('student_id, subject_id, batch_id, school_id, is_late', 'numerical', 'integerOnly' => true),
            array('reason', 'length', 'max' => 255),
            array('attendance_date, updated_at, created_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, student_id, subject_id, batch_id, reason, attendance_date, updated_at, created_at, school_id, is_late', 'safe', 'on' => 'search'),
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
            'student_id' => 'Student',
            'subject_id' => 'Subject',
            'batch_id' => 'Batch',
            'reason' => 'Reason',
            'attendance_date' => 'Attendance Date',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'school_id' => 'School',
            'is_late' => 'Is Late',
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
        $criteria->compare('student_id', $this->student_id);
        $criteria->compare('subject_id', $this->subject_id);
        $criteria->compare('batch_id', $this->batch_id);
        $criteria->compare('reason', $this->reason, true);
        $criteria->compare('attendance_date', $this->attendance_date, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('school_id', $this->school_id);
        $criteria->compare('is_late', $this->is_late);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SubjectAttendances the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function getAllStdAttname($student_id,$subject_id ,$batch_id, $is_late = 0,$date_start=false,$date_end=false)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "t.student_id,count(t.id) as total";
        $criteria->addInCondition('t.student_id', $student_id);
        $subjectObj = new Subjects();
        $subjects = $subjectObj->getSubjectIdsBySubId($subject_id);
        $criteria->addInCondition("t.subject_id", $subjects);
        $criteria->compare("t.batch_id", $batch_id);
        if($date_start && $date_end)
        {
            $criteria->addCondition("attendance_date>='" . $date_start . "' and attendance_date<='" . $date_end . "'");
        }
        $criteria->compare('t.is_late', $is_late);
        $criteria->group = 't.student_id';
        $data = $this->findAll($criteria);

        $return = array();
        if ($data)
        {
            foreach($data as $value)
            {
                $return[$value->student_id] = $value->total;
            }    
        } 
       
        return $return;
        
    }
    
    public function getAllStdAtt($student_id,$subject_id ,$batch_id, $is_late = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "t.student_id,count(t.id) as total";
        $criteria->addInCondition('t.student_id', $student_id);
        $criteria->compare("t.subject_id", $subject_id);
        $criteria->compare("t.batch_id", $batch_id);
        $criteria->compare('t.is_late', $is_late);
        $criteria->group = 't.student_id';
        $data = $this->findAll($criteria);

        $return = array();
        if ($data)
        {
            foreach($data as $value)
            {
                $return[$value->student_id] = $value->total;
            }    
        } 
       
        return $return;
        
    }
    
    

    public function getAllattendence($student_id, $subject_id = "",$batch_id="",$type = "", $is_late = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "count(t.id) as total";
        $criteria->compare('t.student_id', $student_id);
        if ($subject_id)
        {
            $criteria->compare("t.subject_id", $subject_id);
        }
        if ($batch_id)
        {
            $criteria->compare("t.batch_id", $batch_id);
        }
        if ($type == 2)
        {
            $start_date = date("Y-m-d", strtotime("-1 month"));
            $criteria->addCondition("attendance_date>='" . $start_date . "' and attendance_date<='" . date("Y-m-d") . "'");
        } else if ($type == 3)
        {
            $start_date = date("Y-m-d", strtotime("-1 year"));
            $criteria->addCondition("attendance_date>='" . $start_date . "' and attendance_date<='" . date("Y-m-d") . "'");
        }

        $criteria->compare('t.is_late', $is_late);
        $data = $this->find($criteria);

        if ($data)
        {
            return $data->total;
        } else
        {
            return 0;
        }
    }

    public function getAttendence($subject_id, $date, $batch_id = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "t.*";
        $criteria->compare('t.attendance_date', $date);
        $criteria->compare('t.subject_id', $subject_id);
        if($batch_id)
        {
            $criteria->compare('t.batch_id', $batch_id);
        }
        $data = $this->findAll($criteria);

        return $data;
    }
    
    public function getAttendenceTimeTableSubName($subject_id, $date, $batch_id = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "t.student_id,is_late";
        $criteria->compare('t.attendance_date', $date);
        $subjectObj = new Subjects();
        $subjects = $subjectObj->getSubjectIdsBySubId($subject_id);
        $criteria->addInCondition("t.subject_id", $subjects);
        
        if($batch_id)
        {
            $criteria->compare('t.batch_id', $batch_id);
        }
        $data = $this->findAll($criteria);
        $att_data = array();
        if ($data)
        {
            foreach ($data as $value)
            {
                $att_data[$value->student_id] = $value->is_late;
            }
        }
        return $att_data;
    }

    public function getAttendenceTimeTable($subject_id, $date, $batch_id = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select = "t.student_id,is_late";
        $criteria->compare('t.attendance_date', $date);
        $criteria->compare('t.subject_id', $subject_id);
        if($batch_id)
        {
            $criteria->compare('t.batch_id', $batch_id);
        }
        $data = $this->findAll($criteria);
        $att_data = array();
        if ($data)
        {
            foreach ($data as $value)
            {
                $att_data[$value->student_id] = $value->is_late;
            }
        }
        return $att_data;
    }

}
