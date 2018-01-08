<?php

/**
 * This is the model class for table "tds_post_statistic".
 *
 * The followings are the available columns in table 'tds_post_statistic':
 * @property integer $id
 * @property integer $news_id
 * @property string $ip_address
 * @property integer $home_or_abroad
 * @property string $country
 * @property string $city
 * @property string $date
 */
class PostStatistic extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tds_post_statistic';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('news_id, ip_address, date', 'required'),
			array('news_id, home_or_abroad', 'numerical', 'integerOnly'=>true),
			array('ip_address, country, city', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, news_id, ip_address, home_or_abroad, country, city, date', 'safe', 'on'=>'search'),
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
			'news_id' => 'News',
			'ip_address' => 'Ip Address',
			'home_or_abroad' => 'Home Or Abroad',
			'country' => 'Country',
			'city' => 'City',
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
		$criteria->compare('news_id',$this->news_id);
		$criteria->compare('ip_address',$this->ip_address,true);
		$criteria->compare('home_or_abroad',$this->home_or_abroad);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('date',$this->date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PostStatistic the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
