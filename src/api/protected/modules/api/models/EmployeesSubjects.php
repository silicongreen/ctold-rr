<?php

/**
 * This is the model class for table "employees_subjects".
 *
 * The followings are the available columns in table 'employees_subjects':
 * @property integer $id
 * @property integer $employee_id
 * @property integer $subject_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class EmployeesSubjects extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'employees_subjects';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('employee_id, subject_id, school_id', 'numerical', 'integerOnly'=>true),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, employee_id, subject_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
                        'employee' => array(self::BELONGS_TO, 'Employees', 'employee_id'),
                        'subject' => array(self::BELONGS_TO, 'Subjects', 'subject_id'),
                );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'employee_id' => 'Employee',
			'subject_id' => 'Subject',
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
		$criteria->compare('employee_id',$this->employee_id);
		$criteria->compare('subject_id',$this->subject_id);
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
	 * @return EmployeesSubjects the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getSubject($employee_id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.*';
            $criteria->compare("t.employee_id", $employee_id);
           
            $criteria->with = array(
                'subject' => array(
                    'select' => 'subject.id,subject.name',
                    'joinType' => "INNER JOIN",
                    'with' => array(
                        "Subjectbatch" => array(
                            "select" => "Subjectbatch.name",
                            'joinType' => "INNER JOIN",
                            'with' => array(
                                "courseDetails" => array(
                                    "select" => "courseDetails.course_name",
                                    'joinType' => "INNER JOIN",
                                )
                            )
                        )
                    )
                )
            );
           


            
            $obj_subject = $this->findAll($criteria);
        
            $subject = array();
            $i = 0; 
            foreach ($obj_subject as $value)
            {
               $subject[$i]['id'] = $value['subject']->id;
               $subject[$i]['name'] = $value['subject']->name." ".$value['subject']['Subjectbatch']->name." ".$value['subject']['Subjectbatch']['courseDetails']->course_name;
               $i++; 
            }

            return $subject;
        }
        
}
