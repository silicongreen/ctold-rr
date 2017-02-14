<?php

/**
 * This is the model class for table "subject_attendance_registers".
 *
 * The followings are the available columns in table 'subject_attendance_registers':
 * @property integer $id
 * @property string $attendance_date
 * @property integer $subject_id
 * @property integer $batch_id
 * @property integer $employee_id
 * @property integer $present
 * @property integer $absent
 * @property integer $late
 * @property integer $leave
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class SubjectAttendanceRegisters extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public $sumpresent = 0;
    public $sumabsent = 0;
    public $sumlate = 0;
    public $total = 0;
    public $total_class = 0;

    public function tableName()
    {
        return 'subject_attendance_registers';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('attendance_date, subject_id, batch_id, employee_id, present, created_at, updated_at, school_id', 'required'),
            array('subject_id, batch_id, employee_id, present, absent, late, leave, school_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, attendance_date, subject_id, batch_id, employee_id, present, absent, late, leave, created_at, updated_at, school_id', 'safe', 'on' => 'search'),
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
            'attendance_date' => 'Attendance Date',
            'subject_id' => 'Subject',
            'batch_id' => 'Batch',
            'employee_id' => 'Employee',
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'leave' => 'Leave',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('attendance_date', $this->attendance_date, true);
        $criteria->compare('subject_id', $this->subject_id);
        $criteria->compare('batch_id', $this->batch_id);
        $criteria->compare('employee_id', $this->employee_id);
        $criteria->compare('present', $this->present);
        $criteria->compare('absent', $this->absent);
        $criteria->compare('late', $this->late);
        $criteria->compare('leave', $this->leave);
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
     * @return SubjectAttendanceRegisters the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getRegisterDataAll($subject_id, $date)
    {
        $criteria = new CDbCriteria;
        $criteria->addInCondition("subject_id", $subject_id);
        $criteria->compare("attendance_date", $date);
        $data = $this->findAll($criteria);
        return $data;
    }

    public function getRegisterData($subject_id, $date)
    {
        $criteria = new CDbCriteria;
        $criteria->compare("subject_id", $subject_id);
        $criteria->compare("attendance_date", $date);
        $data = $this->find($criteria);
        return $data;
    }

    public function getTotalRegisterStudent($subject_id = "",$batch_id = "", $type = "")
    {
        $criteria = new CDbCriteria;
        $criteria->select = "count(t.id) as total";
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
            $start_date = date("Y-m-d", strtotime("-7 day"));
            $criteria->addCondition("attendance_date>='" . $start_date . "' and attendance_date<='" . date("Y-m-d") . "'");
        }
        if ($type == 3)
        {
            $start_date = date("Y-m-d", strtotime("-1 month"));
            $criteria->addCondition("attendance_date>='" . $start_date . "' and attendance_date<='" . date("Y-m-d") . "'");
        } else if ($type == 3)
        {
            $start_date = date("Y-m-d", strtotime("-1 year"));
            $criteria->addCondition("attendance_date>='" . $start_date . "' and attendance_date<='" . date("Y-m-d") . "'");
        }
       
        $data = $this->find($criteria);
        if ($data)
        {
            return $data->total;
        } else
        {
            return 0;
        }
    }

    public function getReportData($subject_id = "", $type = "", $employee_subjects = array())
    {
        $criteria = new CDbCriteria;
        $criteria->select = "count(id) as total_class,SUM(present) as sumpresent,SUM(absent) as sumabsent,SUM(late) as sumlate";
        if ($type == 1)
        {
            $criteria->compare("attendance_date", date("Y-m-d"));
        } else if ($type == 2)
        {
            $start_date = date("Y-m-d", strtotime("-7 day"));
            $criteria->addCondition("attendance_date>='" . $start_date . "' and attendance_date<='" . date("Y-m-d") . "'");
        } else if ($type == 3)
        {
            $start_date = date("Y-m-d", strtotime("-1 month"));
            $criteria->addCondition("attendance_date>='" . $start_date . "' and attendance_date<='" . date("Y-m-d") . "'");
        } else if ($type == 4)
        {
            $start_date = date("Y-m-d", strtotime("-1 year"));
            $criteria->addCondition("attendance_date>='" . $start_date . "' and attendance_date<='" . date("Y-m-d") . "'");
        }
        if ($subject_id)
        {
            $criteria->compare("subject_id", $subject_id);
        }
        if ($employee_subjects)
        {
            $criteria->addInCondition("subject_id", $employee_subjects);
        }
        $data = $this->find($criteria);

        $att = array();
        if ($data)
        {
            $att['sumpresent'] = $data->sumpresent - $data->sumlate;
            $att['sumabsent'] = (int) $data->sumabsent;
            $att['sumlate'] = (int) $data->sumlate;
            $att['total_class'] = (int) $data->total_class;
        }
        return $att;
    }

    

}
