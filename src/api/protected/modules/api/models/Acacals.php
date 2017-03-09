<?php

/**
 * This is the model class for table "acacals".
 *
 * The followings are the available columns in table 'acacals':
 * @property integer $id
 * @property string $title
 * @property integer $author_id
 * @property integer $is_published
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 * @property string $attachment_file_name
 * @property string $attachment_content_type
 * @property integer $attachment_file_size
 * @property string $attachment_updated_at
 * @property integer $is_common
 */
class Acacals extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'acacals';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('author_id, is_published, school_id, attachment_file_size, is_common', 'numerical', 'integerOnly'=>true),
			array('title, attachment_file_name, attachment_content_type', 'length', 'max'=>255),
			array('created_at, updated_at, attachment_updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, author_id, is_published, created_at, updated_at, school_id, attachment_file_name, attachment_content_type, attachment_file_size, attachment_updated_at, is_common', 'safe', 'on'=>'search'),
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
                        'acaBatch' => array(self::HAS_MANY, 'BatchAcacals', 'acacal_id',
                            'select' => 'acaBatch.id',
                            'joinType' => 'LEFT JOIN',
                        ),
                        'acaDepartment' => array(self::HAS_MANY, 'DepartmentAcacals', 'acacal_id',
                            'select' => 'acaDepartment.id',
                            'joinType' => 'LEFT JOIN',
                        )
                    );
	
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Title',
			'author_id' => 'Author',
			'is_published' => 'Is Published',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'school_id' => 'School',
			'attachment_file_name' => 'Attachment File Name',
			'attachment_content_type' => 'Attachment Content Type',
			'attachment_file_size' => 'Attachment File Size',
			'attachment_updated_at' => 'Attachment Updated At',
			'is_common' => 'Is Common',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('is_published',$this->is_published);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('attachment_file_name',$this->attachment_file_name,true);
		$criteria->compare('attachment_content_type',$this->attachment_content_type,true);
		$criteria->compare('attachment_file_size',$this->attachment_file_size);
		$criteria->compare('attachment_updated_at',$this->attachment_updated_at,true);
		$criteria->compare('is_common',$this->is_common);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Acacals the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getAcacal($batch_id="") 
        {
            $return = new stdClass();
            $school_id = Yii::app()->user->schoolId;
            $criteria = new CDbCriteria;
            $criteria->together = true;
            $criteria->select = 't.id,t.title,t.attachment_file_name';
            $criteria->compare('t.school_id', $school_id);
            $criteria->compare('t.is_published', 1);

            if (Yii::app()->user->isStudent) {
                $with[] = 'acaBatch';
                $criteria->addCondition("(acaBatch.batch_id = '" . Yii::app()->user->batchId  . "' or t.is_common=1)");
            }
            else if(Yii::app()->user->isTeacher) 
            {
                $employee = new Employees;
                $employeeData = $employee->getEmployeeDepartment();

                if($employeeData)
                {
                    $with[] = 'acaDepartment';
                    $criteria->addCondition("(acaDepartment.department_id = '" . $employeeData->employee_department_id . "' or t.is_common=1)");
                }
                else
                {
                    $criteria->compare('t.is_common', 1);
                }    

            }
            else if (Yii::app()->user->isParent && $batch_id)
            {
                $with[] = 'acaBatch';
                $criteria->addCondition("(acaBatch.batch_id = '" . Yii::app()->user->batchId  . "' or t.is_common=1)");
            }
            else 
            {
                $criteria->compare('t.is_common', 1);
            }
            $criteria->order = 't.updated_at DESC';
            $criteria->limit = 1;

            $data = $this->with($with)->find($criteria);

            return (!empty($data)) ? $data : $return;
        }
}
