<?php

/**
 * This is the model class for table "employee_positions".
 *
 * The followings are the available columns in table 'employee_positions':
 * @property integer $id
 * @property string $name
 * @property integer $employee_category_id
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class EmployeePositions extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'employee_positions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('employee_category_id, status, school_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, employee_category_id, status, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
			'employee_category_id' => 'Employee Category',
			'status' => 'Status',
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
		$criteria->compare('employee_category_id',$this->employee_category_id);
		$criteria->compare('status',$this->status);
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
	 * @return EmployeePositions the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getForSchool($school_id,$category_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = "t.id,t.name";
            $criteria->compare("t.school_id", $school_id);
            $criteria->compare("t.employee_category_id", $category_id);
            
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
        public function getPositionBySyncId($sync_id,$name,$school_id)
        {
            $criteria = new CDbCriteria;
            $criteria->addCondition("(t.sync_id='".$sync_id."' OR t.name='".$name."') and t.school_id='".$school_id."'");
            $data = $this->find($criteria);
            return (!empty($data)) ? $data : false;
        }
}
