<?php

/**
 * This is the model class for table "placement_registrations".
 *
 * The followings are the available columns in table 'placement_registrations':
 * @property integer $id
 * @property integer $student_id
 * @property integer $placementevent_id
 * @property integer $is_applied
 * @property integer $is_approved
 * @property integer $is_attended
 * @property integer $is_placed
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class PlacementRegistrations extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'placement_registrations';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('student_id, placementevent_id, is_applied, is_approved, is_attended, is_placed, school_id', 'numerical', 'integerOnly'=>true),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, student_id, placementevent_id, is_applied, is_approved, is_attended, is_placed, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'student_id' => 'Student',
			'placementevent_id' => 'Placementevent',
			'is_applied' => 'Is Applied',
			'is_approved' => 'Is Approved',
			'is_attended' => 'Is Attended',
			'is_placed' => 'Is Placed',
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
		$criteria->compare('student_id',$this->student_id);
		$criteria->compare('placementevent_id',$this->placementevent_id);
		$criteria->compare('is_applied',$this->is_applied);
		$criteria->compare('is_approved',$this->is_approved);
		$criteria->compare('is_attended',$this->is_attended);
		$criteria->compare('is_placed',$this->is_placed);
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
	 * @return PlacementRegistrations the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
