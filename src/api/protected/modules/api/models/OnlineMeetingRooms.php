<?php

/**
 * This is the model class for table "online_meeting_rooms".
 *
 * The followings are the available columns in table 'online_meeting_rooms':
 * @property integer $id
 * @property integer $server_id
 * @property integer $user_id
 * @property string $meetingid
 * @property string $name
 * @property string $attendee_password
 * @property string $moderator_password
 * @property string $welcome_msg
 * @property string $logout_url
 * @property string $voice_bridge
 * @property string $dial_number
 * @property integer $max_participants
 * @property integer $private
 * @property integer $randomize_meetingid
 * @property integer $external
 * @property string $param
 * @property string $scheduled_on
 * @property integer $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class OnlineMeetingRooms extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'online_meeting_rooms';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('server_id, user_id, max_participants, private, randomize_meetingid, external, is_active, school_id', 'numerical', 'integerOnly'=>true),
			array('meetingid, name, attendee_password, moderator_password, welcome_msg, logout_url, voice_bridge, dial_number, param', 'length', 'max'=>255),
			array('scheduled_on, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, server_id, user_id, meetingid, name, attendee_password, moderator_password, welcome_msg, logout_url, voice_bridge, dial_number, max_participants, private, randomize_meetingid, external, param, scheduled_on, is_active, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'server_id' => 'Server',
			'user_id' => 'User',
			'meetingid' => 'Meetingid',
			'name' => 'Name',
			'attendee_password' => 'Attendee Password',
			'moderator_password' => 'Moderator Password',
			'welcome_msg' => 'Welcome Msg',
			'logout_url' => 'Logout Url',
			'voice_bridge' => 'Voice Bridge',
			'dial_number' => 'Dial Number',
			'max_participants' => 'Max Participants',
			'private' => 'Private',
			'randomize_meetingid' => 'Randomize Meetingid',
			'external' => 'External',
			'param' => 'Param',
			'scheduled_on' => 'Scheduled On',
			'is_active' => 'Is Active',
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
		$criteria->compare('server_id',$this->server_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('meetingid',$this->meetingid,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('attendee_password',$this->attendee_password,true);
		$criteria->compare('moderator_password',$this->moderator_password,true);
		$criteria->compare('welcome_msg',$this->welcome_msg,true);
		$criteria->compare('logout_url',$this->logout_url,true);
		$criteria->compare('voice_bridge',$this->voice_bridge,true);
		$criteria->compare('dial_number',$this->dial_number,true);
		$criteria->compare('max_participants',$this->max_participants);
		$criteria->compare('private',$this->private);
		$criteria->compare('randomize_meetingid',$this->randomize_meetingid);
		$criteria->compare('external',$this->external);
		$criteria->compare('param',$this->param,true);
		$criteria->compare('scheduled_on',$this->scheduled_on,true);
		$criteria->compare('is_active',$this->is_active);
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
	 * @return OnlineMeetingRooms the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
