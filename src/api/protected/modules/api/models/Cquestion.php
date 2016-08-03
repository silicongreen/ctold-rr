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
class Cquestion extends CActiveRecord
{
	
	public function tableName()
	{
		return 'tds_assessment_question';
	}
        
	public function relations()
	{
          
		return array(
                    'option' => array(self::HAS_MANY, 'Coption', 'question_id')
		);
	}
        
        
       
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
