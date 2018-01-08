<?php

/**
 * This is the model class for table "courses".
 *
 * The followings are the available columns in table 'courses':
 * @property integer $id
 * @property string $course_name
 * @property string $code
 * @property string $section_name
 * @property integer $is_deleted
 * @property string $created_at
 * @property string $updated_at
 * @property string $grading_type
 * @property integer $school_id
 */
class Courses extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'courses';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_deleted, school_id', 'numerical', 'integerOnly'=>true),
			array('course_name, code, section_name, grading_type', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, course_name, code, section_name, is_deleted, created_at, updated_at, grading_type, school_id', 'safe', 'on'=>'search'),
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
			'course_name' => 'Course Name',
			'code' => 'Code',
			'section_name' => 'Section Name',
			'is_deleted' => 'Is Deleted',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'grading_type' => 'Grading Type',
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
		$criteria->compare('course_name',$this->course_name,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('section_name',$this->section_name,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('grading_type',$this->grading_type,true);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Courses the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getClassNameSchool()
        {
            $criteria = new CDbCriteria;
            $criteria->select="DISTINCT t.course_name";
            $criteria->compare('t.is_deleted', 0);
            $criteria->compare('t.school_id', Yii::app()->user->schoolId);
            $data = $this->findAll($criteria);
            return $data;
        }  
        public function getSectionNameClass($class_name)
        {
            $criteria = new CDbCriteria;
            $criteria->select="DISTINCT t.section_name";
            $criteria->compare('t.is_deleted', 0);
            $criteria->compare('t.course_name', $class_name);
            $criteria->compare('t.school_id', Yii::app()->user->schoolId);
            $data = $this->findAll($criteria);
            return $data;
        }
}
