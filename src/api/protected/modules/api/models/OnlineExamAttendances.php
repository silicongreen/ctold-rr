<?php

/**
 * This is the model class for table "online_exam_attendances".
 *
 * The followings are the available columns in table 'online_exam_attendances':
 * @property integer $id
 * @property integer $online_exam_group_id
 * @property integer $student_id
 * @property string $start_time
 * @property string $end_time
 * @property string $total_score
 * @property integer $is_passed
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class OnlineExamAttendances extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total;
        public $maxmin;
	public function tableName()
	{
		return 'online_exam_attendances';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('online_exam_group_id, student_id, is_passed, school_id', 'numerical', 'integerOnly'=>true),
			array('total_score', 'length', 'max'=>7),
			array('start_time, end_time, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, online_exam_group_id, student_id, start_time, end_time, total_score, is_passed, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'online_exam_group_id' => 'Online Exam Group',
			'student_id' => 'Student',
			'start_time' => 'Start Time',
			'end_time' => 'End Time',
			'total_score' => 'Total Score',
			'is_passed' => 'Is Passed',
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
		$criteria->compare('online_exam_group_id',$this->online_exam_group_id);
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('start_time',$this->start_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('total_score',$this->total_score,true);
		$criteria->compare('is_passed',$this->is_passed);
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
	 * @return OnlineExamAttendances the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getScore($type="MAX",$online_exam_group_id) 
        {

            $criteria = new CDbCriteria();

            $criteria->select = $type.'(t.total_score) as maxmin';
            $criteria->compare('t.online_exam_group_id',$online_exam_group_id);
            $attendance = $this->find($criteria);

            return $attendance->maxmin;
        }
        
        public function getAttendanceCount($online_exam_group_id) 
        {

            $criteria = new CDbCriteria();

            $criteria->select = 'count(t.id) as total';
            $criteria->compare('online_exam_group_id',$online_exam_group_id);
            $attendance = $this->find($criteria);

            return $attendance->total;
        }
}
