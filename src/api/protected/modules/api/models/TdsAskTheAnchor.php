<?php

/**
 * This is the model class for table "tds_ask_the_anchor".
 *
 * The followings are the available columns in table 'tds_ask_the_anchor':
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $question
 * @property string $answer
 * @property integer $status
 * @property string $date
 */
class TdsAskTheAnchor extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total;
	public function tableName()
	{
		return 'tds_ask_the_anchor';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, question, date', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('name, phone, email', 'length', 'max'=>255),
			array('answer', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, phone, email, question, answer, status, date', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'phone' => 'Phone',
			'email' => 'Email',
			'question' => 'Question',
			'answer' => 'Answer',
			'status' => 'Status',
			'date' => 'Date',
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
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('question',$this->question,true);
		$criteria->compare('answer',$this->answer,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('date',$this->date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdsAskTheAnchor the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getQuestionCount()
        {
            $criteria=new CDbCriteria;
            $criteria->select = 'count(t.id) as total';
            $criteria->compare('status',1);
            $data = $this->find($criteria);
            if($data)
            {
                return $data->total;
            }
            else
            {
                return 0;
            }
            
        }
        
        
        public function getQuestion($page = 1, $page_size = 10)
        {
            $criteria=new CDbCriteria;
            $criteria->select = 't.id, t.name, t.question, t.answer, t.date';
            $criteria->compare('status',1);
            $criteria->order = 't.date DESC';
            $start = ($page - 1) * $page_size;
            $criteria->limit = $page_size;
            $criteria->offset = $start;
            $data = $this->findAll($criteria);
            return $data;
        }
}
