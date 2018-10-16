<?php

/**
 * This is the model class for table "batch_tutors".
 *
 * The followings are the available columns in table 'batch_tutors':
 * @property integer $employee_id
 * @property integer $batch_id
 */
class BatchTutors extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'batch_tutors';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('employee_id, batch_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('employee_id, batch_id', 'safe', 'on'=>'search'),
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
			'employee_id' => 'Employee',
			'batch_id' => 'Batch',
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

		$criteria->compare('employee_id',$this->employee_id);
		$criteria->compare('batch_id',$this->batch_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BatchTutors the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function get_employees($batch_id)
        {
            $criteria=new CDbCriteria;
            $criteria->select = 't.employee_id';
            $criteria->compare('t.batch_id',$batch_id);
            $criteria->group = "t.employee_id";
            $obj_tutor = $this->findAll($criteria); ;
            $obj_employee = array();
            if($obj_tutor)
            {
                $emp_id = array();
                foreach($obj_tutor as $value)
                {
                   $emp_id[] = $value->employee_id; 
                } 
                $employess = new Employees();
                $obj_employee = $employess->getUserByEmpIds($emp_id);
            }
            return $obj_employee;
        }
        
        public function get_batch_id()
        {
            $criteria=new CDbCriteria;
            $criteria->compare('employee_id',Yii::app()->user->profileId);
            
            $all_batch = $this->findAll($criteria);
            $batch_ids = array();
            $sub_ids = array();
            if($all_batch)
            { 
                foreach($all_batch as $value)
                {
                    
                    $batch_ids[] = $value->batch_id;
                }
                $sub = new Subjects();
                $sub_ids = $sub->getAllSubByBatchId($batch_ids);
            }
            
            
            return $sub_ids;
        }        
}
