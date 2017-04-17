<?php

/**
 * This is the model class for table "batches".
 *
 * The followings are the available columns in table 'batches':
 * @property integer $id
 * @property string $name
 * @property integer $course_id
 * @property string $start_date
 * @property string $end_date
 * @property integer $is_active
 * @property integer $is_deleted
 * @property string $employee_id
 * @property integer $weekday_set_id
 * @property integer $class_timing_set_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Batches extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $maxstartdate;
        public function tableName()
	{
		return 'batches';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('course_id, is_active, is_deleted, weekday_set_id, class_timing_set_id, school_id', 'numerical', 'integerOnly'=>true),
			array('name, employee_id', 'length', 'max'=>255),
			array('start_date, end_date, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, course_id, start_date, end_date, is_active, is_deleted, employee_id, weekday_set_id, class_timing_set_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
                    'courseDetails' => array(self::BELONGS_TO, 'Courses', 'course_id',
                        'select' => 'courseDetails.id, courseDetails.course_name, courseDetails.code, courseDetails.section_name',
                        'joinType' => 'INNER JOIN',
                    ),
//                    'eventBatchCourse' => array(self::BELONGS_TO, 'Courses', 'course_id',
//                        'select' => 'courseDetails.id, courseDetails.course_name, courseDetails.code, courseDetails.section_name',
//                        'joinType' => 'LEFT JOIN',
//                    ),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'course_id' => 'Course',
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
			'is_active' => 'Is Active',
			'is_deleted' => 'Is Deleted',
			'employee_id' => 'Employee',
			'weekday_set_id' => 'Weekday Set',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('course_id',$this->course_id);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('employee_id',$this->employee_id,true);
		$criteria->compare('weekday_set_id',$this->weekday_set_id);
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
	 * @return Batches the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
       
        public function getBatchStartMax($batch_name=false,$class_name=false,$batch_id=false) 
       {
                $criteria = new CDbCriteria();
                $criteria->select = 'max(date(t.start_date)) as maxstartdate';
                $criteria->compare('t.school_id', Yii::app()->user->schoolId);
                $criteria->compare('t.is_deleted', 0);
                if($batch_id)
                {
                   $criteria->compare('t.id', $batch_id); 
                }
                else
                {
                    if($batch_name)
                    {
                       $criteria->compare('t.name', $batch_name);  
                    }
                    if($class_name)
                    {
                        $criteria->compare('courseDetails.course_name', $class_name); 
                    }
                }
                $criteria->with = array(
                            "courseDetails" => array(
                                'joinType' => "LEFT JOIN",
                            )
                );
                $batch = $this->find($criteria);

                return $batch->maxstartdate;
        }
        
        public function getBatchsByName($batch_name=false,$class_name=false) 
       {
                $criteria = new CDbCriteria();
                $criteria->select = 't.*';
                $criteria->compare('t.school_id', Yii::app()->user->schoolId);
                $criteria->compare('t.is_deleted', 0);
                
                if($batch_name)
                {
                   $criteria->compare('t.name', $batch_name);  
                }
                if($class_name)
                {
                    $criteria->compare('courseDetails.course_name', $class_name); 
                }
               
                $criteria->with = array(
                            "courseDetails" => array(
                                'joinType' => "LEFT JOIN",
                            )
                );
                $batch = $this->findAll($criteria);
                return $batch;
        }
           
            
        
        public function checkSchoolBatch($school_id,$batch_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = "t.id";
            $criteria->compare("t.school_id", $school_id);
            $criteria->compare("t.id", $batch_id);
            $batch = $this->find($criteria);
            if($batch)
            {
                return TRUE;
            }
            return FALSE;
        }
        
        public function getSchoolBatches($school_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = "t.id,t.name";
            $criteria->compare("t.school_id", $school_id);
            $criteria->with = array(
                'courseDetails' => array(
                    'select' => 'courseDetails.id,courseDetails.course_name,courseDetails.section_name',
                    'joinType' => "INNER JOIN"
                )
            );
            $criteria->compare("t.is_deleted", 0);
            $criteria->compare("courseDetails.is_deleted", 0);
            $batches = $this->findAll($criteria);
            
            $b_array = array();
            $i = 0; 
            foreach ($batches as $value)
            {
               
                    $b_array[$i]['id'] = $value->id;
                    $b_array[$i]['name'] = $value->name." ".$value['courseDetails']->course_name." ".$value['courseDetails']->section_name;
                    $i++; 
           
            }

            return $b_array;
            
            
            
        }        
}
