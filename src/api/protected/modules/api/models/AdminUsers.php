<?php

/**
 * This is the model class for table "admin_users".
 *
 * The followings are the available columns in table 'admin_users':
 * @property integer $id
 * @property string $username
 * @property string $password_salt
 * @property string $crypted_password
 * @property string $email
 * @property string $full_name
 * @property string $created_at
 * @property string $updated_at
 * @property string $type
 * @property integer $higher_user_id
 * @property integer $is_deleted
 * @property string $description
 * @property string $contact_no
 * @property string $reset_password_code
 * @property string $reset_password_code_until
 */
class AdminUsers extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'admin_users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('higher_user_id, is_deleted', 'numerical', 'integerOnly'=>true),
			array('username, password_salt, crypted_password, email, full_name, type, contact_no, reset_password_code', 'length', 'max'=>255),
			array('created_at, updated_at, description, reset_password_code_until', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, username, password_salt, crypted_password, email, full_name, created_at, updated_at, type, higher_user_id, is_deleted, description, contact_no, reset_password_code, reset_password_code_until', 'safe', 'on'=>'search'),
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
			'username' => 'Username',
			'password_salt' => 'Password Salt',
			'crypted_password' => 'Crypted Password',
			'email' => 'Email',
			'full_name' => 'Full Name',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'type' => 'Type',
			'higher_user_id' => 'Higher User',
			'is_deleted' => 'Is Deleted',
			'description' => 'Description',
			'contact_no' => 'Contact No',
			'reset_password_code' => 'Reset Password Code',
			'reset_password_code_until' => 'Reset Password Code Until',
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password_salt',$this->password_salt,true);
		$criteria->compare('crypted_password',$this->crypted_password,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('full_name',$this->full_name,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('higher_user_id',$this->higher_user_id);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('contact_no',$this->contact_no,true);
		$criteria->compare('reset_password_code',$this->reset_password_code,true);
		$criteria->compare('reset_password_code_until',$this->reset_password_code_until,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AdminUsers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
