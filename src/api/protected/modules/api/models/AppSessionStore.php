<?php

/**
 * This is the model class for table "app_session_store".
 *
 * The followings are the available columns in table 'app_session_store':
 * @property string $id
 * @property string $session_id_crc
 * @property string $session_id
 * @property string $updated_at
 * @property string $data
 */
class AppSessionStore extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'app_session_store';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('session_id_crc, session_id, updated_at', 'required'),
			array('session_id_crc', 'length', 'max'=>10),
			array('session_id', 'length', 'max'=>32),
			array('data', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, session_id_crc, session_id, updated_at, data', 'safe', 'on'=>'search'),
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
			'session_id_crc' => 'Session Id Crc',
			'session_id' => 'Session',
			'updated_at' => 'Updated At',
			'data' => 'Data',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('session_id_crc',$this->session_id_crc,true);
		$criteria->compare('session_id',$this->session_id,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('data',$this->data,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AppSessionStore the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
