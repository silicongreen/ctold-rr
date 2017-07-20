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
class OnlineExamGroups extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public $total;
    public $perticipated;

    public function tableName() {
        return 'online_exam_groups';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('option_count, batch_id, is_deleted, is_published, school_id', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 255),
            array('maximum_time', 'length', 'max' => 7),
            array('pass_percentage', 'length', 'max' => 6),
            array('start_date, end_date, created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, start_date, end_date, maximum_time, pass_percentage, option_count, batch_id, is_deleted, is_published, created_at, updated_at, school_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'questions' => array(self::HAS_MANY, 'OnlineExamQuestions', 'online_exam_group_id'),
            'subject' => array(self::BELONGS_TO, 'Subjects', 'subject_id'),
            'examgiven' => array(self::HAS_MANY, 'OnlineExamAttendances', 'online_exam_group_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('start_date', $this->start_date, true);
        $criteria->compare('end_date', $this->end_date, true);
        $criteria->compare('maximum_time', $this->maximum_time, true);
        $criteria->compare('pass_percentage', $this->pass_percentage, true);
        $criteria->compare('option_count', $this->option_count);
        $criteria->compare('batch_id', $this->batch_id);
        $criteria->compare('is_deleted', $this->is_deleted);
        $criteria->compare('is_published', $this->is_published);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('school_id', $this->school_id);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OnlineExamGroups the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function getOnlineExamScore($id, $batch_id, $student_id) {
        $cur_date = date("Y-m-d");
        $criteria = new CDbCriteria();
        $criteria->select = 't.*';
        $criteria->compare('t.id', $id);
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.is_published', 1);
        $criteria->compare('examgiven.student_id', $student_id);
        //$criteria->addCondition("examgiven.student_id != '".$student_id."' ");
        $criteria->with = array(
            'questions' => array(
                'select' => 'questions.mark'
            ),
            'subject' => array(
                'select' => 'subject.name,subject.icon_number',
                'joinType' => "LEFT JOIN"
            ),
            'examgiven' => array(
                'select' => 'examgiven.student_id,examgiven.start_time,examgiven.end_time,examgiven.is_passed,examgiven.total_score'
            )
        );
        $data = $this->find($criteria);
        $response_array = array();

        if ($data != NULL) {
            $total_mark = 0;
            if (isset($data['questions']) && count($data['questions'] > 0)) {
                foreach ($data['questions'] as $questions) {
                    $total_mark+=$questions->mark;
                }
            }


            $assesment_valid = false;
            if (isset($data['examgiven']) && count($data['examgiven']) > 0) {
                $assesment_valid = true;


                foreach ($data['examgiven'] as $evalue) {
                    if ($evalue->student_id == $student_id) {

                        break;
                    }
                }
            }


            if ($assesment_valid) {
                $response_array['name'] = $data->name;

                $std = new Students();

                $examattendence = new OnlineExamAttendances();

                $response_array['total_student'] = $std->getStudentByBatchCount($batch_id);
                $response_array['total_participated'] = $examattendence->getAttendanceCount($data->id);
                $response_array['max_score'] = $examattendence->getScore("MAX", $data->id);
                $response_array['min_score'] = $examattendence->getScore("MIN", $data->id);
                $subject = "";
                $subject_icon = "";
                if (isset($value['subject']->name) && $value['subject']->name) {
                    $subject = $value['subject']->name;
                }
                if (isset($value['subject']->icon_number) && $value['subject']->icon_number) {
                    $subject_icon = $value['subject']->icon_number;
                }

                $response_array['subject_name'] = $subject;
                $response_array['subject_icon'] = $subject_icon;


                $response_array['total_mark'] = $total_mark;
                $response_array['start_time'] = $data['examgiven'][0]->start_time;
                $response_array['end_time'] = $data['examgiven'][0]->end_time;
                $response_array['total_time_taken'] = Settings::get_post_time($data['examgiven'][0]->end_time, 6, false, $data['examgiven'][0]->start_time);
                $response_array['is_passed'] = $data['examgiven'][0]->is_passed;
                $response_array['total_score'] = $data['examgiven'][0]->total_score;
            }
        }
        return $response_array;
    }

    public function getOnlineExam($id, $batch_id="", $student_id="") {
        $cur_date = date("Y-m-d");
        $criteria = new CDbCriteria();
        $criteria->select = 't.*';
        $criteria->compare('t.id', $id);
        if($batch_id)
        $criteria->compare('t.batch_id', $batch_id);
        
        $criteria->compare('t.school_id', Yii::app()->user->schoolId);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.is_published', 1);
        $criteria->addCondition("DATE(start_date) <= '" . $cur_date . "' ");
        $criteria->addCondition("DATE(end_date) >= '" . $cur_date . "' ");
        //$criteria->addCondition("examgiven.student_id != '".$student_id."' ");
        $criteria->with = array(
            'questions' => array(
                'select' => 'questions.id,questions.explanation,questions.question,questions.mark,questions.created_at',
                'order' => "RAND()",
                'with' => array(
                    "option" => array(
                        "select" => "option.id,option.option,option.is_answer"
                    )
                )
            ),
            'subject' => array(
                'select' => 'subject.name,subject.icon_number',
                'joinType' => "LEFT JOIN"
            ),
            'examgiven' => array(
                'select' => 'examgiven.student_id'
            )
        );


        $data = $this->find($criteria);
        
      


        $response_array = array();
        $assesment_valid = false;
        if ($data != NULL) {
            if (isset($data['questions']) && count($data['questions'] > 0)) {
                foreach ($data['questions'] as $questions) {
                    if (isset($questions['option']) && count($questions['option'] > 1)) {
                        $assesment_valid = true;
                        break;
                    }
                }
            }

            if (isset($data['examgiven']) && count($data['examgiven']) > 0) {
                foreach ($data['examgiven'] as $evalue) {
                    if ($evalue->student_id == $student_id) {
                        $assesment_valid = false;
                        break;
                    }
                }
            }


            if ($assesment_valid) {

                $response_array['id'] = $data->id;

                $response_array['pass_percentage'] = intval($data->pass_percentage);
                $response_array['title'] = $data->name;

                $subject = "";
                $subject_icon = "";
                if (isset($value['subject']->name) && $value['subject']->name) {
                    $subject = $value['subject']->name;
                }
                if (isset($value['subject']->icon_number) && $value['subject']->icon_number) {
                    $subject_icon = $value['subject']->icon_number;
                }

                $response_array['subject_name'] = $subject;
                $response_array['subject_icon'] = $subject_icon;

                $response_array['use_time'] = 1;
                $response_array['time'] = intval($data->maximum_time);
                $response_array['created'] = $data->created_at;
                $response_array['created_at'] = $data->created_at;
                $response_array['start_date'] = $data->start_date;
                $response_array['end_date'] = $data->end_date;

                $response_array['question'] = array();

                $i = 0;

                $total_question = count($data['questions']);

                $time_par_question = intval($data->maximum_time) * 60 / $total_question;

                $time_par_question = (int) $time_par_question;

                foreach ($data['questions'] as $questions) {
                    if (isset($questions['option']) && count($questions['option'] > 1)) {
                        $q_image = "";
                        $qimages = Settings::content_images($questions->question);
                        if (count($qimages) > 0) {
                            $q_image = $qimages[0];
                        }

                        $response_array['question'][$i]['id'] = $questions->id;
                        $response_array['question'][$i]['question'] = Settings::substr_with_unicode($questions->question);
                        $response_array['question'][$i]['explanation'] = $questions->explanation;
                        $response_array['question'][$i]['image'] = $q_image;

                        $response_array['question'][$i]['mark'] = $questions->mark;
                        $response_array['question'][$i]['time'] = $time_par_question;
                        $response_array['question'][$i]['style'] = 1;
                        $response_array['question'][$i]['created_date'] = $questions->created_at;

                        $response_array['question'][$i]['option'] = array();

                        $j = 0;
                        foreach ($questions['option'] as $options) {
                            $a_image = "";
                            $images = Settings::content_images($options->option);
                            if (count($images) > 0) {
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

    public function getOnlineExamTotal($batch_id, $student_id, $subject_id=0,$not_started=0) {
        $cur_date = date("Y-m-d");
        $criteria = new CDbCriteria();
        $criteria->select = 'count(t.id) as total';
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.is_published', 1);
        $criteria->compare('t.school_id', Yii::app()->user->schoolId);
        if ($subject_id > 0) {
            $criteria->compare('t.subject_id', $subject_id);
        }
        if($not_started == 0)
        {
            $criteria->addCondition("DATE(start_date) <= '" . $cur_date . "' ");
        }
        $data = $this->find($criteria);
        return $data->total;
    }

    public function getOnlineExamSubject($batch_id, $duedate) {

        $criteria = new CDbCriteria();
        $criteria->select = 't.id';
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.is_published', 1);
        $criteria->compare('t.school_id', Yii::app()->user->schoolId);
        $criteria->compare('DATE(t.start_date)', $duedate);
        $criteria->order = "t.created_at DESC";

        $criteria->with = array(
            'subject' => array(
                'select' => 'subject.id,subject.name,subject.icon_number',
                'joinType' => "INNER JOIN"
            )
        );

        $data = $this->findAll($criteria);
        $response_array = array();
        if ($data != NULL)
            foreach ($data as $value) {

                $marge['subjects'] = $value["subject"]->name;
                $marge['subjects_id'] = $value["subject"]->id;
                $marge['subjects_icon'] = $value["subject"]->icon_number;

                $response_array[] = $marge;
            }
        return $response_array;
    }

    public function getOnlineExamList($batch_id, $student_id, $page_number, $page_size, $created_at="", $subject_id=0,$not_started=0) {
        $cur_date = date("Y-m-d");
        $criteria = new CDbCriteria();
        $criteria->select = 't.id,t.name,t.start_date,t.end_date,t.maximum_time,t.pass_percentage';
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.school_id', Yii::app()->user->schoolId);
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.is_published', 1);
        if ($created_at) {
            $criteria->compare('DATE(start_date)', $created_at);
        }
        if($not_started == 0)
        {
            $criteria->addCondition("DATE(start_date) <= '" . $cur_date . "' ");
        }


        //$criteria->addCondition("DATE(end_date) >= '".$cur_date."' ");
        //$criteria->addCondition("examgiven.student_id != '".$student_id."' ");
        $criteria->with = array(
            'examgiven' => array(
                'select' => ''
            ),
            'subject' => array(
                'select' => 'subject.name,subject.icon_number',
                'joinType' => "LEFT JOIN"
            )
        );
        if ($subject_id > 0) {
            $criteria->compare('t.subject_id', $subject_id);
        }

        $criteria->order = "t.created_at DESC";
        $start = ($page_number - 1) * $page_size;
        $criteria->limit = $page_size;

        $criteria->offset = $start;

        $data = $this->findAll($criteria);

        $exam_array = array();

        if ($data) {
            $i = 0;
            foreach ($data as $value) {
                foreach($data as $kvalue)
                {
                    $rid[]= $kvalue->id;
                }
                $robject = new Reminders();
                
                $new_data = $robject->FindUnreadData(15, $rid);
                
                $examGiven = false;
                if (isset($value['examgiven']) && count($value['examgiven']) > 0) {
                    foreach ($value['examgiven'] as $evalue) {
                        if ($evalue->student_id == $student_id) {
                            $examGiven = true;
                            break;
                        }
                    }
                }

                $subject = "";
                $subject_icon = "";
                if (isset($value['subject']->name) && $value['subject']->name) {
                    $subject = $value['subject']->name;
                }
                if (isset($value['subject']->icon_number) && $value['subject']->icon_number) {
                    $subject_icon = $value['subject']->icon_number;
                }
                
                $exam_array[$i]['is_new'] = 0;

                $exam_array[$i]['id'] = $value->id;
                $exam_array[$i]['timeover'] = 0;
                $exam_array[$i]['not_started'] = 0;
                if ($cur_date > date("Y-m-d", strtotime($value->end_date))) {
                    $exam_array[$i]['timeover'] = 1;
                }
                if ($cur_date < date("Y-m-d", strtotime($value->start_date))) {
                    $exam_array[$i]['not_started'] = 1;
                }
                $exam_array[$i]['examGiven'] = 0;
                if ($examGiven) {
                    $exam_array[$i]['examGiven'] = 1;
                }
                
                if(in_array($value->id, $new_data) && $exam_array[$i]['timeover']==0 && $exam_array[$i]['examGiven']==0)
                {
                    $exam_array[$i]['is_new'] = 1;
                }
                
                $exam_array[$i]['name'] = $value->name;
                $exam_array[$i]['subject_name'] = $subject;
                $exam_array[$i]['subject_icon'] = $subject_icon;
                $exam_array[$i]['start_date'] = $value->start_date;
                $exam_array[$i]['end_date'] = $value->end_date;
                $exam_array[$i]['maximum_time'] = $value->maximum_time;
                $exam_array[$i]['pass_percentage'] = $value->pass_percentage;
                $i++;
            }
        }
        return $exam_array;
    }
    
    public function getOnlineExamListTeacher($page_number, $page_size, $subject_ids = array(), $b_total = false, $created_at="") {
        
        $cur_date = date("Y-m-d");
        
        $criteria = new CDbCriteria();
        
        if ($b_total) {
            $criteria->select = 'COUNT(*) AS total';
        } else {
            $criteria->select = 't.id, t.name, t.start_date, t.end_date, t.maximum_time, t.pass_percentage, (SELECT COUNT(examgiven.id) FROM `online_exam_attendances` AS examgiven WHERE `examgiven`.`online_exam_group_id` = `t`.`id`) AS perticipated';
        }
        $criteria->compare('t.school_id', Yii::app()->user->schoolId);
        if (!empty($subject_ids)) {
            $criteria->compare('t.subject_id', $subject_ids);
        }
        
        $criteria->compare('t.is_deleted', 0);
        $criteria->compare('t.is_published', 1);
        if ($created_at) {
            $criteria->compare('DATE(start_date)', $created_at);
        }
        $criteria->addCondition("DATE(start_date) <= '" . $cur_date . "' ");

        $criteria->with = array(
            'subject' => array(
                'select' => 'subject.name, subject.icon_number',
                'joinType' => "LEFT JOIN"
            )
        );
        
        $criteria->order = "t.created_at DESC";
        $start = ($page_number - 1) * $page_size;
        $criteria->limit = $page_size;

        $criteria->offset = $start;

        if ($b_total) {
            $data = $this->find($criteria);
        } else {
            $data = $this->findAll($criteria);
        }
        
        $exam_array = array();
        if ($data && !$b_total) {
            $i = 0;
            foreach ($data as $value) {
                
                $subject = "";
                $subject_icon = "";
                if (isset($value['subject']->name) && $value['subject']->name) {
                    $subject = $value['subject']->name;
                }
                if (isset($value['subject']->icon_number) && $value['subject']->icon_number) {
                    $subject_icon = $value['subject']->icon_number;
                }

                $exam_array[$i]['id'] = $value->id;
                
                $exam_array[$i]['name'] = $value->name;
                $exam_array[$i]['subject_name'] = $subject;
                $exam_array[$i]['subject_icon'] = $subject_icon;
                $exam_array[$i]['start_date'] = $value->start_date;
                $exam_array[$i]['end_date'] = $value->end_date;
                $exam_array[$i]['maximum_time'] = $value->maximum_time;
                $exam_array[$i]['pass_percentage'] = $value->pass_percentage;
                $exam_array[$i]['done'] = $value->perticipated;
                $i++;
            }
        }
        
        return ($b_total && !empty($data)) ? $data->total : $exam_array;
    }

}
