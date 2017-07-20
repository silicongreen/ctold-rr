<?php

/**
 * This is the model class for table "tds_post_user_activity".
 *
 * The followings are the available columns in table 'tds_post_user_activity':
 * @property integer $id
 * @property integer $user_id
 * @property string $post_id
 * @property integer $operation_type
 * @property string $operation_date
 * @property string $ip_address
 * @property string $user_agent
 * @property string $os
 * @property string $latitude
 * @property string $longitude
 * @property string $session_id
 *
 * The followings are the available model relations:
 * @property Post $post
 * @property Users $user
 */
class PostUserActivity extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tds_post_user_activity';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, post_id', 'required'),
			array('user_id, operation_type', 'numerical', 'integerOnly'=>true),
			array('post_id, ip_address', 'length', 'max'=>20),
			array('user_agent, os', 'length', 'max'=>100),
			array('latitude, longitude', 'length', 'max'=>255),
			array('session_id', 'length', 'max'=>45),
			array('operation_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, post_id, operation_type, operation_date, ip_address, user_agent, os, latitude, longitude, session_id', 'safe', 'on'=>'search'),
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
			'post' => array(self::BELONGS_TO, 'Post', 'post_id'),
			'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
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
			'post_id' => 'Post',
			'operation_type' => 'Come from Codeigniter Config.
1 - Creation
2 - Modification
3 - Moderation
4 - Publication
5 - Deletion',
			'operation_date' => 'Operation Date',
			'ip_address' => 'Ip Address',
			'user_agent' => 'User Agent',
			'os' => 'Os',
			'latitude' => 'Latitude',
			'longitude' => 'Longitude',
			'session_id' => 'Session',
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
		$criteria->compare('post_id',$this->post_id,true);
		$criteria->compare('operation_type',$this->operation_type);
		$criteria->compare('operation_date',$this->operation_date,true);
		$criteria->compare('ip_address',$this->ip_address,true);
		$criteria->compare('user_agent',$this->user_agent,true);
		$criteria->compare('os',$this->os,true);
		$criteria->compare('latitude',$this->latitude,true);
		$criteria->compare('longitude',$this->longitude,true);
		$criteria->compare('session_id',$this->session_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PostUserActivity the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
