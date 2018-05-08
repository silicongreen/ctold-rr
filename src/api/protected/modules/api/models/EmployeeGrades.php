<?php

/**
 * This is the model class for table "employee_grades".
 *
 * The followings are the available columns in table 'employee_grades':
 * @property integer $id
 * @property string $name
 * @property integer $priority
 * @property integer $status
 * @property integer $max_hours_day
 * @property integer $max_hours_week
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class EmployeeGrades extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'employee_grades';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('priority, status, max_hours_day, max_hours_week, school_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, priority, status, max_hours_day, max_hours_week, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'priority' => 'Priority',
			'status' => 'Status',
			'max_hours_day' => 'Max Hours Day',
			'max_hours_week' => 'Max Hours Week',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('priority',$this->priority);
		$criteria->compare('status',$this->status);
		$criteria->compare('max_hours_day',$this->max_hours_day);
		$criteria->compare('max_hours_week',$this->max_hours_week);
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
	 * @return EmployeeGrades the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getByName($name,$school_id)
        {
            $criteria = new CDbCriteria;
            $criteria->addCondition("t.name='".$name."' and t.school_id='".$school_id."'");
            $data = $this->find($criteria);
            return (!empty($data)) ? $data : false;
        }
        
        public function getForSchool($school_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = "t.id,t.name";
            $criteria->compare("t.school_id", $school_id);
            
            $criteria->compare("t.status", 1);
            $grades = $this->findAll($criteria);
            
            $array = array();
            $i = 0; 
            foreach ($grades as $value)
            {
               
                    $array[$i]['id'] = $value->id;
                    $array[$i]['name'] = $value->name;
                    $i++; 
           
            }

            return $array;
            
            
            
        }  
}
