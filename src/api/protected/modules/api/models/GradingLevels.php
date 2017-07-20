<?php

/**
 * This is the model class for table "grading_levels".
 *
 * The followings are the available columns in table 'grading_levels':
 * @property integer $id
 * @property string $name
 * @property integer $batch_id
 * @property integer $min_score
 * @property integer $order
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $credit_points
 * @property string $description
 * @property integer $school_id
 */
class GradingLevels extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
       
	public function tableName()
	{
		return 'grading_levels';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('batch_id, min_score, order, is_deleted, school_id', 'numerical', 'integerOnly'=>true),
			array('name, description', 'length', 'max'=>255),
			array('credit_points', 'length', 'max'=>15),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, batch_id, min_score, order, is_deleted, created_at, updated_at, credit_points, description, school_id', 'safe', 'on'=>'search'),
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
			'batch_id' => 'Batch',
			'min_score' => 'Min Score',
			'order' => 'Order',
			'is_deleted' => 'Is Deleted',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'credit_points' => 'Credit Points',
			'description' => 'Description',
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
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('min_score',$this->min_score);
		$criteria->compare('order',$this->order);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('credit_points',$this->credit_points,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GradingLevels the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getAllGrade($school_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->compare('t.school_id', $school_id);
            $criteria->order = "credit_points DESC";
            $data = $this->findAll($criteria);
            return $data;
        }
        public function getGrade($cradit_point,$school_id)
        {
            $criteria = new CDbCriteria();
            $criteria->select = 't.*';
            $criteria->addCondition("t.credit_points <= '".$cradit_point."'");
            $criteria->compare('t.school_id', $school_id);
            $criteria->order = "credit_points DESC";
            $criteria->limit = 1;
            $data = $this->find($criteria);
            
            return $data->name;
            
        }
}
