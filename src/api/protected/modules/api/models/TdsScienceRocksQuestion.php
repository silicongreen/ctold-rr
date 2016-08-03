<?php

/**
 * This is the model class for table "tds_science_rocks_question".
 *
 * The followings are the available columns in table 'tds_science_rocks_question':
 * @property integer $id
 * @property integer $topic_id
 * @property string $question
 * @property string $explanation
 * @property integer $mark
 * @property integer $time
 * @property integer $style
 * @property integer $status
 */
class TdsScienceRocksQuestion extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total_question;
        public $total_mark;
	public function tableName()
	{
		return 'tds_science_rocks_question';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('topic_id, question', 'required'),
			array('topic_id, mark, time, style, status', 'numerical', 'integerOnly'=>true),
			array('explanation', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, topic_id, question, explanation, mark, time, style, status', 'safe', 'on'=>'search'),
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
                    'Option' => array(self::HAS_MANY, 'TdsScienceRocksOption', 'question_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'topic_id' => 'Topic',
			'question' => 'Question',
			'explanation' => 'Explanation',
			'mark' => 'Mark',
			'time' => 'Time',
			'style' => 'Style',
			'status' => 'Status',
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
		$criteria->compare('topic_id',$this->topic_id);
		$criteria->compare('question',$this->question,true);
		$criteria->compare('explanation',$this->explanation,true);
		$criteria->compare('mark',$this->mark);
		$criteria->compare('time',$this->time);
		$criteria->compare('style',$this->style);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdsScienceRocksQuestion the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getTotalQuesTionAndMark($topic_id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 'count(t.id) as total_question,SUM(t.mark) as total_mark';
            $criteria->compare('t.topic_id',$topic_id);
            $criteria->compare('t.status',1);
            $data = $this->find($criteria);
            
            
            return $data;
            
        }  
        
        public function getQuesTionAndAnswer($topic_id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id, t.en_question, t.en_explanation, t.question, t.explanation, t.mark, t.time, t.style';
            $criteria->compare('t.topic_id',$topic_id);
            $criteria->compare('t.status',1);
            $criteria->with = array(
                'Option' => array(
                    'select' => 'Option.answer,Option.en_answer,Option.correct'
                )
             ); 
            $data = $this->findAll($criteria);
            
            $questions = array();
            
            $i = 0;
            foreach($data as $value)
            {
                if(isset($value['Option']) && count($value['Option']) == 4)
                {
                    $questions[$i]['question'] = $questions[$i]['en_question']  = $value->question;
                    $questions[$i]['explanation'] = $questions[$i]['en_explanation']  = $value->explanation;
                    
                    if($value->en_question)
                    {
                        $questions[$i]['en_question']  = $value->en_question;
                    }
                    if($value->en_explanation)
                    {
                        $questions[$i]['en_explanation']  = $value->en_explanation;
                    }
                    
                    $questions[$i]['mark'] = $value->mark;
                    $questions[$i]['time'] = $value->time;
                    $questions[$i]['style'] = $value->style;
                    
                    $questions[$i]['options'] = array();
                    foreach($value['Option'] as $key=>$ovalue)
                    {
                        $questions[$i]['options'][$key]['answer'] = $questions[$i]['options'][$key]['en_answer']  = $ovalue->answer;
                        if($ovalue->en_answer)
                        {
                            $questions[$i]['options'][$key]['en_answer']  = $ovalue->en_answer;
                        }
                        $questions[$i]['options'][$key]['correct'] = $ovalue->correct;
                    }
                    $i++;
                    
                }    
            }  
            return $questions;
            
        }        
}
