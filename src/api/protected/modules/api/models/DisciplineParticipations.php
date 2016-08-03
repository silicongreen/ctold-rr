<?php

/**
 * This is the model class for table "discipline_participations".
 *
 * The followings are the available columns in table 'discipline_participations':
 * @property integer $id
 * @property string $type
 * @property integer $action_taken
 * @property integer $school_id
 * @property integer $user_id
 * @property integer $discipline_complaint_id
 * @property string $created_at
 * @property string $updated_at
 */
class DisciplineParticipations extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'discipline_participations';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('action_taken, school_id, user_id, discipline_complaint_id', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, action_taken, school_id, user_id, discipline_complaint_id, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'type' => 'Type',
			'action_taken' => 'Action Taken',
			'school_id' => 'School',
			'user_id' => 'User',
			'discipline_complaint_id' => 'Discipline Complaint',
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
		$criteria->compare('type',$this->type,true);
		$criteria->compare('action_taken',$this->action_taken);
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('discipline_complaint_id',$this->discipline_complaint_id);
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
	 * @return DisciplineParticipations the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
