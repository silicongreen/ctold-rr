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
         public function getEmployeeSubject($subject_id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id';
            $criteria->compare("t.subject_id", $subject_id);
           
            $criteria->with = array(
                'employee' => array(
                    'select' => 'employee.id,employee.user_id,employee.first_name,employee.middle_name,employee.last_name,employee.short_code',
                    'joinType' => "INNER JOIN",
                    
                )
            );
            $criteria->group = "employee.id";
            
            $obj_employee = $this->findAll($criteria);
        
           

            return $obj_employee;
        }
        
        
        public function getEmployee($batch_id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id';
            $criteria->compare("subject.batch_id", $batch_id);
           
            $criteria->with = array(
                'subject' => array(
                    'select' => '',
                    'joinType' => "INNER JOIN",
                    
                ),
                'employee' => array(
                    'select' => 'employee.id,employee.user_id,employee.first_name,employee.middle_name,employee.last_name',
                    'joinType' => "INNER JOIN",
                    
                )
            );
            $criteria->group = "employee.id";
            
            $obj_employee = $this->findAll($criteria);
        
           

            return $obj_employee;
        }
        public function getBatchId($employee_id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.*';
            $criteria->compare("t.employee_id", $employee_id);
           
            $criteria->with = array(
                'subject' => array(
                    'select' => '',
                    'joinType' => "INNER JOIN",
                    'with' => array(
                        "Subjectbatch" => array(
                            "select" => "Subjectbatch.id",
                            'joinType' => "INNER JOIN"
                        )
                    )
                )
            );
            
            $criteria->compare("Subjectbatch.is_deleted", 0);
            $criteria->group = "Subjectbatch.id";

            
            $obj_subject = $this->findAll($criteria);
        
            $subject = array();
            $i = 0; 
            foreach ($obj_subject as $value)
            {
               $subject[$i] = $value['subject']['Subjectbatch']->id;
               $i++; 
            }

            return $subject;
        }
        
        public function getBatch($employee_id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.*';
            $criteria->compare("t.employee_id", $employee_id);
           
            $criteria->with = array(
                'subject' => array(
                    'select' => '',
                    'joinType' => "INNER JOIN",
                    'with' => array(
                        "Subjectbatch" => array(
                            "select" => "Subjectbatch.id,Subjectbatch.name",
                            'joinType' => "INNER JOIN",
                            'with' => array(
                                "courseDetails" => array(
                                    "select" => "courseDetails.id,courseDetails.course_name,courseDetails.section_name",
                                    'joinType' => "INNER JOIN",
                                )
                            )
                        )
                    )
                )
            );
            $criteria->compare("Subjectbatch.is_deleted", 0);
            $criteria->compare("courseDetails.is_deleted", 0);
           
            $criteria->group = "Subjectbatch.id";

            
            $obj_subject = $this->findAll($criteria);
        
            $subject = array();
            $i = 0; 
            foreach ($obj_subject as $value)
            {
               
                    $subject[$i]['id'] = $value['subject']['Subjectbatch']->id;
                    $subject[$i]['name'] = $value['subject']['Subjectbatch']->name." ".$value['subject']['Subjectbatch']['courseDetails']->course_name." ".$value['subject']['Subjectbatch']['courseDetails']->section_name;
                    $i++; 
           
            }

            return $subject;
        }
        public function getAllSubject($employee_id)
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
                                    "select" => "courseDetails.course_name, courseDetails.section_name",
                                    'joinType' => "INNER JOIN",
                                )
                            )
                        )
                    )
                )
            );
           

            $criteria->compare("Subjectbatch.is_deleted", 0);
            $criteria->compare("courseDetails.is_deleted", 0);
            
            $obj_subject = $this->findAll($criteria);
            $all_subject = array();
            $all_sub_id = array();
            $i = 0;
            foreach ($obj_subject as $value)
            {
                $all_sub_id[] = $value['subject']->id;
                $all_subject[$i]['id'] = $value['subject']->id;
                $all_subject[$i]['name'] = $value['subject']->name." - ".$value['subject']['Subjectbatch']->name." ".$value['subject']['Subjectbatch']['courseDetails']->course_name." ".$value['subject']['Subjectbatch']['courseDetails']->section_name;
                
                $i++;
                if($value['subject']->elective_group_id)
                {
                    $sub_obj = new Subjects();
                    $e_subject = $sub_obj->getSubjectElectiveGroup($value['subject']->elective_group_id);
                    if($e_subject)
                    {
                        foreach($e_subject as $e_sub)
                        {
                            if(!in_array($e_sub->id, $all_sub_id))
                            {
                                $all_sub_id[] = $e_sub->id;
                                $all_subject[$i]['id'] = $e_sub->id;
                                $all_subject[$i]['name'] = $sub_obj->getSubjectFullName($e_sub->id);
                                $i++;
                            }
                        }    
                    }
                    
                }
                   
                
            }
            return $all_subject;
            
            
        }
        public function getEmployeeSubjectElective($employee_id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.*';
            $criteria->compare("t.employee_id", $employee_id);
            $criteria->with = array(
                'subject' => array(
                    'select' => 'subject.id,subject.name,subject.elective_group_id,subject.code, subject.icon_number',
                    'joinType' => "INNER JOIN"
                )
            );
            $criteria->addCondition("subject.elective_group_id is not null");
            $criteria->compare("subject.is_deleted", 0);
            $obj_subject = $this->findAll($criteria);
            
            $all_subject = array();
            $emp_subject = array();
            
            if($obj_subject)
            {
                foreach($obj_subject as $value)
                {
                    if($value['subject']->elective_group_id)
                    {
                        $emp_subject[] = $value['subject'];
                        $sub_obj = new Subjects();
                        $e_subject = $sub_obj->getSubjectElectiveGroup($value['subject']->elective_group_id);
                        if($e_subject)
                        {
                            foreach($e_subject as $e_sub)
                            {
                                $all_subject[] = $e_sub->id;
                            }    
                        }
                        
                    }
                }    
            }
            return array($all_subject,$emp_subject);
        }        
        
        public function getSubject($employee_id,$lesson_id = 0,$return_selcted_subject_array=false)
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
                                    "select" => "courseDetails.course_name, courseDetails.section_name",
                                    'joinType' => "INNER JOIN",
                                )
                            )
                        )
                    )
                )
            );
           

            $criteria->compare("Subjectbatch.is_deleted", 0);
            $criteria->compare("courseDetails.is_deleted", 0);
            
            $obj_subject = $this->findAll($criteria);
        
            $subject = array();
            $i = 0; 
            $subject_selected = array();
            if($lesson_id>0)
            {
                $lessonplan = new Lessonplan();
                $lessonplan = $lessonplan->findByPk($lesson_id);
                if($lessonplan && $lessonplan->subject_ids)
                {
                    $subject_selected_string = $lessonplan->subject_ids;
                    $subject_selected = explode(",", $subject_selected_string);
                }
            }
            foreach ($obj_subject as $value)
            {
                if($return_selcted_subject_array)
                {
                   if(in_array($value['subject']->id, $subject_selected))
                   {
                        $subject[$i]['id'] = $value['subject']->id;
                        $subject[$i]['name'] = $value['subject']->name." - ".$value['subject']['Subjectbatch']->name." ".$value['subject']['Subjectbatch']['courseDetails']->course_name." ".$value['subject']['Subjectbatch']['courseDetails']->section_name;
                        $i++;
                   }
                     
                }
                else
                {
                    $subject[$i]['id'] = $value['subject']->id;
                    $subject[$i]['name'] = $value['subject']->name." - ".$value['subject']['Subjectbatch']->name." ".$value['subject']['Subjectbatch']['courseDetails']->course_name." ".$value['subject']['Subjectbatch']['courseDetails']->section_name;
                    $subject[$i]['selected'] = 0;
                    if(in_array($value['subject']->id, $subject_selected))
                    {
                        $subject[$i]['selected'] = 1;
                    }
                    $i++;  
                }    
                
            }

            return $subject;
        }
        
}
