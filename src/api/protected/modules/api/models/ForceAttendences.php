<?php

/**
 * This is the model class for table "force_attendences".
 *
 * The followings are the available columns in table 'force_attendences':
 * @property integer $id
 * @property integer $student_id
 * @property integer $batch_id
 * @property string $date
 * @property integer $school_id
 * @property string $created_at
 * @property string $updated_at
 */
class ForceAttendences extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'force_attendences';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('student_id, batch_id, date, school_id, created_at, updated_at', 'required'),
			array('student_id, batch_id, school_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, student_id, batch_id, date, school_id, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'batch_id' => 'Batch',
			'date' => 'Date',
			'school_id' => 'School',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
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
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ForceAttendences the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getAll($school_id,$date)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.student_id';
            $criteria->compare('school_id',$school_id);
            $criteria->compare('date',$date);
            $obj_students = $this->findAll($criteria);
            
            $std_ids = array();
            
            if($obj_students)
            {
                foreach($obj_students as $value)
                {
                    $std_ids[] = $value->student_id;
                }    
            }    

            return $std_ids;
        }
}
