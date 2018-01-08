<?php

/**
 * This is the model class for table "fa_groups".
 *
 * The followings are the available columns in table 'fa_groups':
 * @property integer $id
 * @property string $name
 * @property string $desc
 * @property integer $cce_exam_category_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $cce_grade_set_id
 * @property double $max_marks
 * @property integer $is_deleted
 * @property integer $school_id
 */
class FaGroups extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'fa_groups';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cce_exam_category_id, cce_grade_set_id, is_deleted, school_id', 'numerical', 'integerOnly'=>true),
			array('max_marks', 'numerical'),
			array('name', 'length', 'max'=>255),
			array('desc, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, desc, cce_exam_category_id, created_at, updated_at, cce_grade_set_id, max_marks, is_deleted, school_id', 'safe', 'on'=>'search'),
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
			'desc' => 'Desc',
			'cce_exam_category_id' => 'Cce Exam Category',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'cce_grade_set_id' => 'Cce Grade Set',
			'max_marks' => 'Max Marks',
			'is_deleted' => 'Is Deleted',
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
		$criteria->compare('desc',$this->desc,true);
		$criteria->compare('cce_exam_category_id',$this->cce_exam_category_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('cce_grade_set_id',$this->cce_grade_set_id);
		$criteria->compare('max_marks',$this->max_marks);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return FaGroups the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
