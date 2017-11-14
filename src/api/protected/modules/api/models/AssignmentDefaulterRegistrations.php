<?php

/**
 * This is the model class for table "assignment_defaulter_registrations".
 *
 * The followings are the available columns in table 'assignment_defaulter_registrations':
 * @property integer $id
 * @property integer $employee_id
 * @property integer $assignment_id
 * @property integer $assignment_given
 * @property integer $assignment_not_given
 * @property integer $school_id
 * @property string $created_at
 * @property string $updated_at
 */
class AssignmentDefaulterRegistrations extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'assignment_defaulter_registrations';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('employee_id, assignment_id, school_id', 'required'),
			array('employee_id, assignment_id, assignment_given, assignment_not_given, school_id', 'numerical', 'integerOnly'=>true),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, employee_id, assignment_id, assignment_given, assignment_not_given, school_id, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'employee_id' => 'Employee',
			'assignment_id' => 'Assignment',
			'assignment_given' => 'Assignment Given',
			'assignment_not_given' => 'Assignment Not Given',
			'school_id' => 'School',
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
		$criteria->compare('employee_id',$this->employee_id);
		$criteria->compare('assignment_id',$this->assignment_id);
		$criteria->compare('assignment_given',$this->assignment_given);
		$criteria->compare('assignment_not_given',$this->assignment_not_given);
		$criteria->compare('school_id',$this->school_id);
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
	 * @return AssignmentDefaulterRegistrations the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function findByAssignmentId($assignment_id)
        {
            $criteria=new CDbCriteria;
            $criteria->compare('assignment_id',$assignment_id);
            $data = $this->find($criteria);
            if($data)
            {
                return $data;
            }
            return false;  
        }        
}
