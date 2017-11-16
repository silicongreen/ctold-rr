<?php

/**
 * This is the model class for table "assignment_defaulter_lists".
 *
 * The followings are the available columns in table 'assignment_defaulter_lists':
 * @property integer $id
 * @property integer $assignment_id
 * @property integer $student_id
 * @property integer $msg_send
 */
class AssignmentDefaulterLists extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'assignment_defaulter_lists';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assignment_id, student_id', 'required'),
			array('assignment_id, student_id, msg_send', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, assignment_id, student_id, msg_send', 'safe', 'on'=>'search'),
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
			'assignment_id' => 'Assignment',
			'student_id' => 'Student',
			'msg_send' => 'Msg Send',
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
		$criteria->compare('assignment_id',$this->assignment_id);
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('msg_send',$this->msg_send);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AssignmentDefaulterLists the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function findAllByAssignmentId($assignment_id,$register = 0,$only_defaulter = 0)
        {
            $std_list = [];
            $assignmentObj = new Assignments();
            $assData = $assignmentObj->findByPk($assignment_id);
            if($assData)
            {
                $std_ids = explode(",",$assData['student_list']);
                $stdObj = new Students();
                $assignment_students = $stdObj->getFindAllByStdIds($std_ids);
                
                if($assignment_students)
                {
                    $d_list = [];
                    if($register == 1)
                    {
                        $criteria=new CDbCriteria;
                        $criteria->select = 't.student_id';
                        $criteria->compare('assignment_id',$assignment_id);
                        $data = $this->findAll($criteria);
                        if($data)
                        {
                            foreach($data as $value)
                            {
                                $d_list[] = $value->student_id;
                            }    
                        }    
                    }
                    $i_loop = 0;
                    foreach($assignment_students as $svalue)
                    {
                        if($only_defaulter==0 || in_array($svalue->id, $d_list))
                        {
                            $std_list[$i_loop]['student_id'] = $svalue->id;
                            $std_list[$i_loop]['student_name'] = $svalue->first_name." ".$svalue->first_name." ".$svalue->last_name;
                            $std_list[$i_loop]['student_name'] = str_replace("  "," ", $std_list[$i_loop]['student_name']);
                            $std_list[$i_loop]['class_roll_no'] = $svalue->class_roll_no;
                            $std_list[$i_loop]['defaulter'] = 0;
                            if(in_array($svalue->id, $d_list))
                            {
                                $std_list[$i_loop]['defaulter'] = 1;
                            }
                            $i_loop++;
                        }
                        
                    }
                    
                    
                    
                }
            }
            return $std_list;  
        } 
}
