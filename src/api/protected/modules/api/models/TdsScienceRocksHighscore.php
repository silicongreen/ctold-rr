<?php

/**
 * This is the model class for table "tds_science_rocks_highscore".
 *
 * The followings are the available columns in table 'tds_science_rocks_highscore':
 * @property integer $id
 * @property integer $user_id
 * @property integer $level_id
 * @property integer $score
 * @property integer $time
 * @property integer $total_time
 * @property string $date
 */
class TdsScienceRocksHighscore extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tds_science_rocks_highscore';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, level_id, score, time, total_time, date', 'required'),
			array('user_id, level_id, score, time, total_time', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, level_id, score, time, total_time, date', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'level_id' => 'Level',
			'score' => 'Score',
			'time' => 'Time',
			'total_time' => 'Total Time',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('level_id',$this->level_id);
		$criteria->compare('score',$this->score);
		$criteria->compare('time',$this->time);
		$criteria->compare('total_time',$this->total_time);
		$criteria->compare('date',$this->date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdsScienceRocksHighscore the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getHighscore($level_id)
        {
             $criteria=new CDbCriteria;
             $criteria->select = 't.score';
             $criteria->compare('level_id',$level_id);
             $criteria->order = "score DESC";
             $criteria->limit = 1;
             $score_data = $this->find($criteria); 
             if($score_data)
             {
                 return $score_data->score;
             }
             return 0;
        }        
        
        public function savescoreAndReturn($user_id,$score,$time,$level_id)
        {
             $criteria=new CDbCriteria;
             $criteria->select = 't.*';
             $criteria->compare('user_id',$user_id);
             $criteria->compare('level_id',$level_id);
             $score_data = $this->find($criteria);
             $extra_score = 0;
             if($score_data)
             {
                 $scoreobj = new TdsScienceRocksHighscore();
                 $scoreobjdata = $scoreobj->findByPk($score_data->id);
                 if($scoreobjdata->score<$score || ($scoreobjdata->score == $score && $scoreobjdata->time > $time))
                 {
                     $extra_score = $score-$scoreobjdata->score;
                     $scoreobjdata->score = $score;
                     $scoreobjdata->time = $time;
                     $scoreobjdata->total_time = $scoreobjdata->total_time+$time;
                     $scoreobjdata->date = date("Y-m-d H:i:s");
                     
                     
                     
                 }
                 else
                 {
                     $scoreobjdata->total_time = $score_data->total_time+$time;
                     $scoreobjdata->date = date("Y-m-d H:i:s");
                 }    
                 $scoreobjdata->save();
             }
             else
             {
                $scoreobjdata = new TdsScienceRocksHighscore();
                $scoreobjdata->score = $score;
                $scoreobjdata->level_id = $level_id;
                $scoreobjdata->user_id = $user_id;
                $scoreobjdata->time = $time;
                $scoreobjdata->total_time = $time;
                $scoreobjdata->date = date("Y-m-d H:i:s");
                $scoreobjdata->save();
                $extra_score = $score;
             } 
             return array($scoreobjdata,$extra_score);
        }
}
