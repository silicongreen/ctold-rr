<?php

/**
 * This is the model class for table "online_exam_groups".
 *
 * The followings are the available columns in table 'online_exam_groups':
 * @property integer $id
 * @property string $name
 * @property string $start_date
 * @property string $end_date
 * @property string $maximum_time
 * @property string $pass_percentage
 * @property integer $option_count
 * @property integer $batch_id
 * @property integer $is_deleted
 * @property integer $is_published
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class OnlineExamGroups extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'online_exam_groups';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('option_count, batch_id, is_deleted, is_published, school_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('maximum_time', 'length', 'max'=>7),
			array('pass_percentage', 'length', 'max'=>6),
			array('start_date, end_date, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, start_date, end_date, maximum_time, pass_percentage, option_count, batch_id, is_deleted, is_published, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
                    'questions' => array(self::HAS_MANY, 'OnlineExamQuestions', 'online_exam_group_id'),
                    'examgiven' => array(self::HAS_MANY, 'OnlineExamAttendances', 'online_exam_group_id')
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
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
			'maximum_time' => 'Maximum Time',
			'pass_percentage' => 'Pass Percentage',
			'option_count' => 'Option Count',
			'batch_id' => 'Batch',
			'is_deleted' => 'Is Deleted',
			'is_published' => 'Is Published',
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
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);
		$criteria->compare('maximum_time',$this->maximum_time,true);
		$criteria->compare('pass_percentage',$this->pass_percentage,true);
		$criteria->compare('option_count',$this->option_count);
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('is_published',$this->is_published);
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
	 * @return OnlineExamGroups the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}       
        public function getOnlineExam($id,$batch_id,$student_id)
        {   
            $cur_date = date("Y-m-d");
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.id', $id);
            $criteria->compare('t.batch_id', $batch_id);
            $criteria->compare('t.is_published', 1);
            $criteria->addCondition("DATE(start_date) <= '".$cur_date."' ");
            $criteria->addCondition("DATE(end_date) >= '".$cur_date."' ");
            $criteria->addCondition("examgiven.student_id != '".$student_id."' ");
            $criteria->with = array(
                'questions' => array(
                    'select' => 'questions.id,questions.question,questions.mark,questions.created_at',
                    'order' => "RAND()",
                    'with' => array(
                        "option" => array(
                            "select" => "option.id,option.option,option.is_answer"
                        )
                    )
                ),
                'examgiven' => array(
                    'select' => ''
                )
            );
            
            
            $data = $this->find($criteria);
             
            
            
            $response_array = array();
            $assesment_valid = false;
            if($data != NULL)
            {
                if(isset($data['questions']) && count($data['questions']>0))
                {
                    foreach($data['questions'] as $questions)
                    {
                       if(isset($questions['option']) && count($questions['option']>1))
                       {
                           $assesment_valid = true;
                           break;
                       }    
                    }    
                }
                
                if($assesment_valid)
                {
                    
                    $response_array['id'] = $data->id;
                    $response_array['title'] = $data->name;
                    $response_array['use_time'] = 1;
                    $response_array['time'] = intval($data->maximum_time);
                    $response_array['start_date'] = $data->start_date;
                    $response_array['end_date'] = $data->end_date;
                    
                    $response_array['question'] = array();
                    
                    $i = 0;
                    
                    $total_question = count($data['questions']);
                    
                    $time_par_question =  intval($data->maximum_time)*60/$total_question;
                    
                    foreach($data['questions'] as $questions)
                    {
                        if(isset($questions['option']) && count($questions['option']>1))
                        {
                            $q_image = "";
                            $qimages = Settings::content_images($questions->question);
                            if(count($qimages)>0)
                            {
                                $q_image = $qimages[0];
                            }    
                             
                            $response_array['question'][$i]['id'] = $questions->id;
                            $response_array['question'][$i]['question'] = Settings::substr_with_unicode($questions->question);
                            $response_array['question'][$i]['image'] = $q_image;
                            
                            $response_array['question'][$i]['mark'] = $questions->mark;
                            $response_array['question'][$i]['time'] = $time_par_question;
                            $response_array['question'][$i]['style'] = 1;
                            $response_array['question'][$i]['created_date'] = $questions->created_at;
                            
                            $response_array['question'][$i]['option'] = array();
                            
                            $j = 0;
                            foreach($questions['option'] as $options)
                            {
                               $a_image = "";
                               $images = Settings::content_images($options->option);
                               if(count($images)>0)
                               {
                                  $a_image = $images[0];
                               }    
                               
                               $response_array['question'][$i]['option'][$j]['id'] = $options->id;
                               $response_array['question'][$i]['option'][$j]['answer'] = Settings::substr_with_unicode($options->option);
                               $response_array['question'][$i]['option'][$j]['answer_image'] = $a_image;
                               
                               $response_array['question'][$i]['option'][$j]['correct'] = $options->is_answer;
                               
                               $j++;
                            } 
                            
                            $i++;
                            
                        }    
                    }
                    
                    
                }
                    
            }
            return $response_array;
            
        }
        
        
        public function getOnlineExamList($batch_id,$student_id)
        {
            $cur_date = date("Y-m-d");
            $criteria = new CDbCriteria();
            $criteria->select = 't.id,t.name,t.start_date,t.end_date,t.maximum_time,t.pass_percentage';
            $criteria->compare('t.batch_id', $batch_id);
            $criteria->compare('t.is_published', 1);
            //$criteria->addCondition("DATE(start_date) <= '".$cur_date."' ");
            //$criteria->addCondition("DATE(end_date) >= '".$cur_date."' ");
            //$criteria->addCondition("examgiven.student_id != '".$student_id."' ");
            $criteria->with = array(
                'examgiven' => array(
                    'select' => ''
                )
            );
            
            $data = $this->findAll($criteria);
            
            $exam_array = array();
            
            if($data)
            {
                $i = 0;
                foreach($data as $value)
                {
                   $exam_array[$i]['id'] = $value->id;
                   $exam_array[$i]['name'] = $value->name;
                   $exam_array[$i]['start_date'] = $value->start_date;
                   $exam_array[$i]['end_date'] = $value->end_date;
                   $exam_array[$i]['maximum_time'] = $value->maximum_time;
                   $exam_array[$i]['pass_percentage'] = $value->pass_percentage;
                   $i++;
                }    
            } 
            return $exam_array;
        }
}
