<?php

/**
 * This is the model class for table "assignment_answers".
 *
 * The followings are the available columns in table 'assignment_answers':
 * @property integer $id
 * @property integer $assignment_id
 * @property integer $student_id
 * @property string $status
 * @property string $title
 * @property string $content
 * @property string $attachment_file_name
 * @property string $attachment_content_type
 * @property integer $attachment_file_size
 * @property string $attachment_updated_at
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class AssignmentAnswers extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'assignment_answers';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assignment_id, student_id, attachment_file_size, school_id', 'numerical', 'integerOnly'=>true),
			array('status, title, attachment_file_name, attachment_content_type', 'length', 'max'=>255),
			array('content, attachment_updated_at, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, assignment_id, student_id, status, title, content, attachment_file_name, attachment_content_type, attachment_file_size, attachment_updated_at, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'status' => 'Status',
			'title' => 'Title',
			'content' => 'Content',
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
		$criteria->compare('assignment_id',$this->assignment_id);
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('content',$this->content,true);
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
        function isAlreadyDone($assignment_id, $student_id)
        {
             $criteria = new CDbCriteria();
             $criteria->select = 't.id,t.status';
             $criteria->compare('t.student_id', $student_id);
             $criteria->compare('t.assignment_id', $assignment_id);
             
             $criteria->order = "created_at ASC";
             $data = $this->findAll($criteria);
             $return = "";
             foreach($data as $value)
             {
                 if($value->status=="ACCEPTED")
                 {
                    return "ACCEPTED"; 
                    break;
                 }
                 else if($value->status=="REJECTED")
                 {
                     $return = "REJECTED";
                 }
                 else 
                 {
                     $return = "SUBMITTED";
                 }
             }    
             
             if($return != "")
             {
                 return $return;
             }  
             else 
             {
                 return "NOT DONE";
             }
        }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AssignmentAnswers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
