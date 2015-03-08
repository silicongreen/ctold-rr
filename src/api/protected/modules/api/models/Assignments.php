<?php

/**
 * This is the model class for table "assignments".
 *
 * The followings are the available columns in table 'assignments':
 * @property integer $id
 * @property integer $employee_id
 * @property integer $subject_id
 * @property string $student_list
 * @property string $title
 * @property string $content
 * @property string $duedate
 * @property string $attachment_file_name
 * @property string $attachment_content_type
 * @property integer $attachment_file_size
 * @property string $attachment_updated_at
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Assignments extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total;
	public function tableName()
	{
		return 'assignments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('employee_id, subject_id, attachment_file_size, school_id', 'numerical', 'integerOnly'=>true),
			array('title, attachment_file_name, attachment_content_type', 'length', 'max'=>255),
			array('student_list, content, duedate, attachment_updated_at, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, employee_id, subject_id, student_list, title, content, duedate, attachment_file_name, attachment_content_type, attachment_file_size, attachment_updated_at, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
                    'employeeDetails' => array(self::BELONGS_TO, 'Employees', 'employee_id',
                         "select"=>"employeeDetails.id, employeeDetails.first_name, employeeDetails.middle_name, employeeDetails.last_name ",
                    ),
                    'subjectDetails' => array(self::BELONGS_TO, 'Subjects', 'subject_id',
                       "select"=>"subjectDetails.id, subjectDetails.name, subjectDetails.icon_number", 
                    ),
                    'assignmentAnswerDetails' => array(self::HAS_MANY, 'AssignmentAnswers', 'assignment_id'
                       
                    ),
		);
	}
        
        public function getAssignmentTotalTeacher($employee_id,$subject_id=NULL,$duedate=NULL)
        {
            
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            if($subject_id!=NULL)
            {         
                $criteria->compare('t.subject_id', $subject_id);
            }
            if($duedate)
            {
                $criteria->compare('DATE(t.duedate)', $duedate);
            }
            $criteria->compare('t.employee_id', $employee_id);
            
            $data = $this->find($criteria);
            return $data->total;
        }        
        
        public function getAssignmentTeacher($employee_id,$page=1,$page_size=10,$id=0,$subject_id=NULL,$duedate=NULL)
        {
            
            
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.employee_id', $employee_id);
            if($subject_id!=NULL)
            {
                $criteria->compare('t.subject_id', $subject_id);
              
            }
            if($duedate)
            {
                $criteria->compare('DATE(t.duedate)', $duedate);
            }
            if($id>0)
            {
               $criteria->compare('t.id', $id); 
            } 
            $criteria->order = "duedate DESC";          
            if($id>0)
            {
                $criteria->limit = 1;
            }
            else
            {    
                $start = ($page-1)*$page_size;
                $criteria->limit = $page_size;

                $criteria->offset = $start;
     
            } 
            
            $criteria->with = array(
                'subjectDetails' => array(
                    'select' => 'subjectDetails.id,subjectDetails.name,subjectDetails.icon_number',
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
            
            $data = $this->findAll($criteria);
            $response_array = array();
            if($data != NULL)
            foreach($data as $value)
            {
                $marge = array();
              
                $marge['subjects'] = $value["subjectDetails"]->name;
                $marge['batch'] = $value["subjectDetails"]['Subjectbatch']->name;
                
                $marge['attachment_file_name'] = ""; 
                if($value->attachment_file_name)
                {
                    $marge['attachment_file_name'] = $value->attachment_file_name;
                }
                
                $marge['course'] = $value["subjectDetails"]['Subjectbatch']['courseDetails']->course_name;
                $marge['subjects_id'] = $value["subjectDetails"]->id;
                $marge['subjects_icon'] = $value["subjectDetails"]->icon_number;
                $marge['assign_date'] = date("Y-m-d", strtotime($value->created_at));
                $marge['duedate'] = date("Y-m-d", strtotime($value->duedate));
                $marge['name'] = $value->title;
                $marge['content'] = $value->content;
                $marge['type'] = $value->assignment_type;
                $marge['id'] = $value->id;
                $assignment_answer = new AssignmentAnswers();
                $marge['done'] = $assignment_answer->doneTotal($value->id);
                $response_array[] = $marge;     
                
            }
            return $response_array;
            
        }
        
        
        
        
        public function getAssignmentTotal($batch_id, $student_id, $date = '', $subject_id=NULL, $type, $duedate=null)
        {
            $date = (!empty($date)) ? $date : \date('Y-m-d', \time());
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            $criteria->compare('subjectDetails.batch_id', $batch_id);
            $criteria->compare('t.assignment_type', $type);
            if($subject_id!=NULL)
            {
                $criteria->compare('t.subject_id', $subject_id);
            }
            if($duedate)
            {
                $criteria->compare('DATE(t.duedate)', $duedate);
            }
            
            $criteria->addCondition("FIND_IN_SET(".$student_id.", student_list)");
            
            $criteria->order = "duedate ASC";
            
            $data = $this->with("subjectDetails")->find($criteria);
            return $data->total;
        } 
        
        public function getAssignmentSubject($batch_id, $student_id,$duedate)
        {
            
            $criteria = new CDbCriteria();
            $criteria->select = 't.id,t.attachment_file_name';
            $criteria->compare('subjectDetails.batch_id', $batch_id);
            $criteria->compare('DATE(t.duedate)', $duedate);
            $criteria->order = "t.created_at DESC";
            $criteria->addCondition("FIND_IN_SET(".$student_id.", student_list)");
            $data = $this->with("subjectDetails")->findAll($criteria);
            $response_array = array();
            if($data != NULL)
            foreach($data as $value)
            {
                $marge['attachment_file_name'] = "";      
                if($value->attachment_file_name)
                {
                    $marge['attachment_file_name'] = $value->attachment_file_name;
                } 
                $marge['subjects'] = $value["subjectDetails"]->name;
                $marge['subjects_id'] = $value["subjectDetails"]->id;
                $marge['subjects_icon'] = $value["subjectDetails"]->icon_number;
                
                $response_array[] = $marge;     
                
            }
            return $response_array;
            
        }
        
        public function getAssignment($batch_id, $student_id, $date = '',$page=1, $subject_id=NULL, $page_size,$type,$id=0,$duedate="")
        {
            $date = (!empty($date)) ? $date : \date('Y-m-d', \time());
            
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('subjectDetails.batch_id', $batch_id);
            
            if($duedate)
            {
                $criteria->compare('DATE(t.duedate)', $duedate);
            }
            
            if($id>0)
            {
               $criteria->compare('t.id', $id); 
            } 
            else
            {
                $criteria->compare('t.assignment_type', $type);
            }    
            if($subject_id!=NULL)
            {
                $criteria->compare('t.subject_id', $subject_id);
                
            }
            $criteria->order = "t.created_at DESC";
            $criteria->addCondition("FIND_IN_SET(".$student_id.", student_list)");
            
            
            if($id>0)
            {
                $criteria->limit = 1;
            }
            else
            {    
                $start = ($page-1)*$page_size;
                $criteria->limit = $page_size;

                $criteria->offset = $start;
            }
            
            $data = $this->with("subjectDetails","employeeDetails")->findAll($criteria);
            $response_array = array();
            if($data != NULL)
            foreach($data as $value)
            {
                $marge = array();
                $middle_name = (!empty($value["employeeDetails"]->middle_name)) ? $value["employeeDetails"]->middle_name.' ' : '';
                $marge['id']   = $value->id;
                
                $marge['attachment_file_name'] = "";
                
                if($value->attachment_file_name)
                {
                    $marge['attachment_file_name'] = $value->attachment_file_name;
                }
                
                $marge['teacher_name'] = rtrim($value["employeeDetails"]->first_name.' '.$middle_name.$value["employeeDetails"]->last_name);
                $marge['teacher_id']   = $value["employeeDetails"]->id;
                $marge['subjects'] = $value["subjectDetails"]->name;
                $marge['subjects_id'] = $value["subjectDetails"]->id;
                $marge['subjects_icon'] = $value["subjectDetails"]->icon_number;
                $marge['assign_date'] = date("Y-m-d", strtotime($value->created_at));
                $marge['duedate'] = date("Y-m-d",  strtotime($value->duedate));
                $marge['time_over'] = 0;
                if(date("Y-m-d")>date("Y-m-d",  strtotime($value->duedate)))
                {
                   $marge['time_over'] = 1; 
                }
                $marge['name'] = $value->title;
                $marge['content'] = $value->content;
                $marge['type'] = $value->assignment_type;
                $marge['id'] = $value->id;
                $assignment_answer = new AssignmentAnswers();
                $marge['is_done'] = $assignment_answer->isAlreadyDone($value->id, $student_id);
                $response_array[] = $marge;     
                
            }
            return $response_array;
            
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
			'student_list' => 'Student List',
			'title' => 'Title',
			'content' => 'Content',
			'duedate' => 'Duedate',
			'attachment_file_name' => 'Attachment File Name',
			'attachment_content_type' => 'Attachment Content Type',
			'attachment_file_size' => 'Attachment File Size',
			'attachment_updated_at' => 'Attachment Updated At',
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
		$criteria->compare('student_list',$this->student_list,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('duedate',$this->duedate,true);
		$criteria->compare('attachment_file_name',$this->attachment_file_name,true);
		$criteria->compare('attachment_content_type',$this->attachment_content_type,true);
		$criteria->compare('attachment_file_size',$this->attachment_file_size);
		$criteria->compare('attachment_updated_at',$this->attachment_updated_at,true);
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
	 * @return Assignments the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
