<?php

/**
 * This is the model class for table "tds_science_rocks_user".
 *
 * The followings are the available columns in table 'tds_science_rocks_user':
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property string $auth_id
 */
class TdsScienceRocksUser extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tds_science_rocks_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, email, auth_id', 'required'),
			array('name, phone, email, auth_id', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, phone, email, auth_id', 'safe', 'on'=>'search'),
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
			'auth_id' => 'Auth',
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
		$criteria->compare('auth_id',$this->auth_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TdsScienceRocksUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getUserId($auth_id,$name,$email,$phone="",$profile_image="")
        {
             $criteria=new CDbCriteria;
             $criteria->select = 't.id';
             $criteria->compare('auth_id',$auth_id);
             $user = $this->find($criteria);
             if($user)
             {
                 $user_id = $user->id;
             }
             else
             {
                 $userobj = new TdsScienceRocksUser();
                 $userobj->name = $name;
                 $userobj->email = $email;
                 $userobj->auth_id = $auth_id;
                 if($phone)
                 {
                    $userobj->phone = $phone;
                 }
                 if($profile_image)
                 {
                    $userobj->profile_image = $profile_image;
                 }
                 $userobj->save();
                 $user_id = $userobj->id;
             } 
             return $user_id;
        }
}
