<?php

/**
 * This is the model class for table "oauth_tokens".
 *
 * The followings are the available columns in table 'oauth_tokens':
 * @property integer $id
 * @property string $user_id
 * @property integer $oauth_client_id
 * @property string $access_token
 * @property string $refresh_token
 * @property integer $expires_at
 * @property string $created_at
 * @property string $updated_at
 */
class OauthTokens extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'oauth_tokens';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('oauth_client_id, expires_at', 'numerical', 'integerOnly'=>true),
			array('user_id, access_token, refresh_token', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, oauth_client_id, access_token, refresh_token, expires_at, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'oauth_client_id' => 'Oauth Client',
			'access_token' => 'Access Token',
			'refresh_token' => 'Refresh Token',
			'expires_at' => 'Expires At',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
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
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('oauth_client_id',$this->oauth_client_id);
		$criteria->compare('access_token',$this->access_token,true);
		$criteria->compare('refresh_token',$this->refresh_token,true);
		$criteria->compare('expires_at',$this->expires_at);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OauthTokens the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
