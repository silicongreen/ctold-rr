<?php

/**
 * This is the model class for table "tds_tags".
 *
 * The followings are the available columns in table 'tds_tags':
 * @property integer $id
 * @property string $tags_name
 * @property string $hit_count
 *
 * The followings are the available model relations:
 * @property PostTags[] $postTags
 */
class Meetingrequest extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total = 0;
	public function tableName()
	{
		return 'meeting_request';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('teacher_id,parent_id,description,datetime', 'required'),
			array('type,teacher_id,parent_id,description,datetime,student_ids,status', 'safe', 'on'=>'search'),
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
                        'employee' => array(self::BELONGS_TO, 'Employees', 'teacher_id'),
                        'students' => array(self::BELONGS_TO, 'Students', 'parent_id'),
                );
	}

	

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Tag the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getall($id,$type=1,$type2=1,$start_date="",$end_date="")
        {
            $criteria = new CDbCriteria;
            $criteria->select = 'count(t.id) as total';
            if($type2==1)
            {
                $criteria->compare('teacher_id', $id);
            }
            else
            {
                $criteria->compare('parent_id', $id); 
            }
            $criteria->compare('type', $type);
            if($start_date)
            {
                $criteria->addCondition('DATE(datetime) >="'.$start_date.'"');
            }
            if($end_date)
            {
                $criteria->addCondition('DATE(datetime) <="'.$end_date.'"');
            }
            
            if($type2==1)
            $criteria->group = "t.teacher_id";
            else
            $criteria->group = "t.parent_id";    
           

            $data = $this->find($criteria);
            if ($data)
            {
                return $data->total;
            }
            else
            {
                return 0;
            }
        }
        public function singleMetting($id)
        {
           $criteria = new CDbCriteria; 
           $criteria->select = 't.id,t.description,t.datetime,t.status,t.type';
           $criteria->compare('t.id', $id);
           $criteria->with = array(
                    'employee' => array(
                        'select' => 'employee.first_name,employee.middle_name,employee.last_name',
                        'joinType' => "LEFT JOIN"
                    ),
                    'students' => array(
                        'select' => 'students.immediate_contact_id,students.id,students.first_name,students.middle_name,students.last_name',
                        'joinType' => "LEFT JOIN"
                    )
            );
           $value = $this->find($criteria);
           $meeting = array();
           if($value)
           {
                if(Yii::app()->user->isTeacher)
                {                           
                    $student_model = new Students();
                    $student_batch = $student_model->getStudentById($value['students']->id);                    
                    $full_name = ($value['students']->first_name)?$value['students']->first_name." ":"";
                    $full_name.= ($value['students']->middle_name)?$value['students']->middle_name." ":"";
                    $full_name.= ($value['students']->last_name)?$value['students']->last_name:"";
                    $meeting['show_for'] = 1;
                    $meeting['name'] = $full_name;
                    $meeting['batch'] = $student_batch['batchDetails']['courseDetails']->course_name." ".$student_batch['batchDetails']->name;
                }
                else
                {
                    $full_name = ($value['employee']->first_name)?$value['employee']->first_name." ":"";
                    $full_name.= ($value['employee']->middle_name)?$value['employee']->middle_name." ":"";
                    $full_name.= ($value['employee']->last_name)?$value['employee']->last_name:"";
                    $meeting['show_for'] = 0;
                    $meeting['name'] = $full_name;
                    $meeting['batch'] = "";
                } 
                $meeting['type'] = $value->type;
                $meeting['id'] = $value->id;
                $meeting['date'] = $value->datetime;
                $datevalue = date("Y-m-d",  strtotime($value->datetime));
                $meeting['timeover']  = 0;
                if($today>$datevalue)
                {
                    $meeting['timeover'] = 1;
                }
                $meeting['status'] = $value->status;
           }
           return $meeting;
           
        }        
        public function getInboxOutbox($id,$type=1,$type2=1,$start_date="",$end_date="", $page = 1, $page_size = 10)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id,t.description,t.datetime,t.status';
            $today = date("Y-m-d");
            if($type2==1)
            {
                $criteria->compare('teacher_id', $id);
            }
            else
            {
                $criteria->compare('parent_id', $id); 
            }
            $criteria->compare('type', $type);
            
           
            
            if($start_date)
            {
                $criteria->addCondition('DATE(datetime) >="'.$start_date.'"');
            }
            if($end_date)
            {
                $criteria->addCondition('DATE(datetime) <="'.$end_date.'"');
            }
            $criteria->with = array(
                    'employee' => array(
                        'select' => 'employee.first_name,employee.middle_name,employee.last_name',
                        'joinType' => "LEFT JOIN"
                    ),
                    'students' => array(
                        'select' => 'students.immediate_contact_id,students.id,students.first_name,students.middle_name,students.last_name',
                        'joinType' => "LEFT JOIN"
                    )
            );
            $start = ($page - 1) * $page_size;
            $criteria->limit = $page_size;

            $criteria->offset = $start;
            
            $criteria->order = "t.id desc";
            
            $obj_metting = $this->findAll($criteria);
            $meeting = array();
            
            if($obj_metting)
            {
                $i = 0;
                foreach($obj_metting as $value)
                {
                    if($type2==1)
                    {                           
                        $student_model = new Students();
                        $student_batch = $student_model->getStudentById($value['students']->id);                    
                        $full_name = ($value['students']->first_name)?$value['students']->first_name." ":"";
                        $full_name.= ($value['students']->middle_name)?$value['students']->middle_name." ":"";
                        $full_name.= ($value['students']->last_name)?$value['students']->last_name:"";                       
                        $meeting[$i]['name'] = $full_name;
                        $meeting[$i]['batch'] = $student_batch['batchDetails']['courseDetails']->course_name." ".$student_batch['batchDetails']->name;
                    }
                    else
                    {
                        $full_name = ($value['employee']->first_name)?$value['employee']->first_name." ":"";
                        $full_name.= ($value['employee']->middle_name)?$value['employee']->middle_name." ":"";
                        $full_name.= ($value['employee']->last_name)?$value['employee']->last_name:"";
                        $meeting[$i]['name'] = $full_name;
                        $meeting[$i]['batch'] = "";
                    } 
                    $meeting[$i]['id'] = $value->id;
                    $meeting[$i]['date'] = $value->datetime;
                    $datevalue = date("Y-m-d",  strtotime($value->datetime));
                    $meeting[$i]['timeover']  = 0;
                    if($today>$datevalue)
                    {
                        $meeting[$i]['timeover'] = 1;
                    }
                    $meeting[$i]['status'] = $value->status;
                    $i++;
                }    
            }
            
            return $meeting;
        }
        
        
        
}
