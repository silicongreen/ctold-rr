<?php

/**
 * This is the model class for table "tds_science_rocks_total_score".
 *
 * The followings are the available columns in table 'tds_science_rocks_total_score':
 * @property integer $id
 * @property integer $user_id
 * @property integer $score
 * @property integer $time
 * @property integer $total_time
 * @property string $last_date
 */
class TdsScienceRocksTotalScore extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tds_science_rocks_total_score';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, score, time, total_time, last_date', 'required'),
			array('user_id, score, time, total_time', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, score, time, total_time, last_date', 'safe', 'on'=>'search'),
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
                    'UserFree' => array(self::BELONGS_TO, 'TdsScienceRocksUser', 'user_id')
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
			'score' => 'Score',
			'time' => 'Time',
			'total_time' => 'Total Time',
			'last_date' => 'Last Date',
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
		$criteria->compare('score',$this->score);
		$criteria->compare('time',$this->time);
		$criteria->compare('total_time',$this->total_time);
		$criteria->compare('last_date',$this->last_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdsScienceRocksTotalScore the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getLeaderBoard($iLimit = 10)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.score,t.total_time';

            $criteria->with = array(
                'UserFree' => array(
                    'select' => 'UserFree.name,UserFree.email',
                    'joinType' => "INNER JOIN"
                )

            ); 

            $criteria->compare('UserFree.test', 0);
            $criteria->addCondition("t.score > 0 ");
            $criteria->order = "t.score DESC, total_time ASC";
            $criteria->limit = $iLimit;
            $data = $this->findAll($criteria);
            $arScoresData = array();
            if ($data)
            {

                foreach ($data as $value)
                {

                        $arUser = array();
                        $arUser['score'] = $value->score;
                        $arUser['time'] = $value->total_time;
                        
                        $arUser['name'] = $value['UserFree']->name;
                        $arUser['email'] = $value['UserFree']->email;

                        array_push( $arScoresData, ( object ) $arUser );


                }
            }

            return $arScoresData;
        }
        
        public function savescoreAndReturn($user_id,$extra,$time,$total_time)
        {
             $criteria=new CDbCriteria;
             $criteria->select = 't.id';
             $criteria->compare('user_id',$user_id);
             $score_data = $this->find($criteria);
             $extra_score = 0;
             if($score_data)
             {
                 $scoreobj = new TdsScienceRocksTotalScore();
                 $scoreobjdata = $scoreobj->findByPk($score_data->id);
                 
                 $scoreobjdata->time = $scoreobjdata->time+$time;
                 $scoreobjdata->score = $scoreobjdata->score+$extra;
                 $scoreobjdata->total_time = $scoreobjdata->total_time+$total_time;
                 $scoreobjdata->last_date = date("Y-m-d H:i:s");
                   
                 $scoreobjdata->save();
             }
             else
             {
                $scoreobjdata = new TdsScienceRocksTotalScore();
                $scoreobjdata->time = $time;
                $scoreobjdata->score = $extra;
                $scoreobjdata->total_time = $total_time;
                $scoreobjdata->user_id = $user_id;
                $scoreobjdata->last_date = date("Y-m-d H:i:s");
                
                $scoreobjdata->save();
             } 
             return $scoreobjdata;
        }
}
