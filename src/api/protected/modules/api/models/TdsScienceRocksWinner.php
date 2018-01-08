<?php

/**
 * This is the model class for table "tds_science_rocks_winner".
 *
 * The followings are the available columns in table 'tds_science_rocks_winner':
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $date
 * @property string $winner1
 * @property string $winner1_district
 * @property string $winner2
 * @property string $winner2_district
 * @property string $winner3
 * @property string $winner3_district
 * @property string $question1
 * @property string $ans1
 * @property string $question2
 * @property string $ans2
 * @property string $winner1_occupation
 * @property string $winner2_occupation
 * @property string $winner3_occupation
 */
class TdsScienceRocksWinner extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total;
	public function tableName()
	{
		return 'tds_science_rocks_winner';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, date, winner1, winner1_district, winner2, winner2_district, winner3, winner3_district, question1, ans1, question2, ans2, winner1_occupation, winner2_occupation, winner3_occupation', 'required'),
			array('name, winner1, winner1_district, winner2, winner2_district, winner3, winner3_district, question1, ans1, question2, ans2, winner1_occupation, winner2_occupation, winner3_occupation', 'length', 'max'=>255),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, description, date, winner1, winner1_district, winner2, winner2_district, winner3, winner3_district, question1, ans1, question2, ans2, winner1_occupation, winner2_occupation, winner3_occupation', 'safe', 'on'=>'search'),
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
			'description' => 'Description',
			'date' => 'Date',
			'winner1' => 'Winner1',
			'winner1_district' => 'Winner1 District',
			'winner2' => 'Winner2',
			'winner2_district' => 'Winner2 District',
			'winner3' => 'Winner3',
			'winner3_district' => 'Winner3 District',
			'question1' => 'Question1',
			'ans1' => 'Ans1',
			'question2' => 'Question2',
			'ans2' => 'Ans2',
			'winner1_occupation' => 'Winner1 Occupation',
			'winner2_occupation' => 'Winner2 Occupation',
			'winner3_occupation' => 'Winner3 Occupation',
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
		$criteria->compare('description',$this->description,true);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('winner1',$this->winner1,true);
		$criteria->compare('winner1_district',$this->winner1_district,true);
		$criteria->compare('winner2',$this->winner2,true);
		$criteria->compare('winner2_district',$this->winner2_district,true);
		$criteria->compare('winner3',$this->winner3,true);
		$criteria->compare('winner3_district',$this->winner3_district,true);
		$criteria->compare('question1',$this->question1,true);
		$criteria->compare('ans1',$this->ans1,true);
		$criteria->compare('question2',$this->question2,true);
		$criteria->compare('ans2',$this->ans2,true);
		$criteria->compare('winner1_occupation',$this->winner1_occupation,true);
		$criteria->compare('winner2_occupation',$this->winner2_occupation,true);
		$criteria->compare('winner3_occupation',$this->winner3_occupation,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdsScienceRocksWinner the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getEpisodeCount()
        {
            $criteria=new CDbCriteria;
            $criteria->select = 'count(t.id) as total';
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
        
        
        public function getEpisode($page = 1, $page_size = 10)
        {
            $criteria=new CDbCriteria;
            $criteria->select = 't.*';
            $criteria->order = 't.date DESC';
            $start = ($page - 1) * $page_size;
            $criteria->limit = $page_size;
            $criteria->offset = $start;
            $data = $this->findAll($criteria);
            return $data;
        }
}
