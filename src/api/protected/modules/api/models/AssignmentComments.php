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
class AssignmentComments extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total = 0;
	public function tableName()
	{
		return 'assignment_comments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('assignment_id, student_id,school_id', 'numerical', 'integerOnly'=>true),
			array('content, attachment_updated_at, created_at, updated_at', 'safe'),
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
                                'Students' => array(self::BELONGS_TO, 'Students', 'student_id',
                                    'joinType' => 'INNER JOIN',
                                )
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
		$criteria->compare('content',$this->content,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
        function totalComments($assignment_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 'count(DISTINCT t.student_id) as total';
            $criteria->compare('t.assignment_id', $assignment_id);
            $data = $this->find($criteria);
            return $data->total;
            
        }
        function hasComment($assignment_id,$student_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 'count(DISTINCT t.student_id) as total';
            $criteria->compare('t.assignment_id', $assignment_id);
            $criteria->compare('t.student_id', $student_id);
            $data = $this->find($criteria);
            return $data->total;
            
        }
        function getComments( $assignment_id, $student_id )
        {
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.assignment_id', $assignment_id);
            $criteria->compare('t.student_id', $student_id);
            $criteria->order = "t.created_at asc";
            $data = $this->findAll($criteria);
            $free_user = new Freeusers();
            $user = new Users();
            $comments = array();
            if( $data )
            {
                foreach( $data as $value )
                {
                    $merge = [];
                    $userdata = $user->findByPk($value->author_id);
                    if( $userdata )
                    {
                        $merge['is_author'] = 0;
                        if( Yii::app()->user->id == $value->author_id )
                        {
                            $merge['is_author'] = 1;
                        }
                        $merge['comments'] = $value->content;
                        $merge['created_at'] = $value->created_at;
                        $merge['user_name'] = trim($userdata->first_name." ".$userdata->last_name);
                        $free_user_id = $free_user->getFreeuserPaid($userdata->id,$userdata->school_id);
                        if($free_user_id)
                        {
                            $merge['profile_image'] = Settings::getProfileImage($free_user_id);
                        }
                        $comments[] =  $merge;       
                      
                    }  
                            
                }    
            }
            return $comments;
            
        }
        
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
