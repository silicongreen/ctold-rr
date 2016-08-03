<?php

/**
 * This is the model class for table "discipline_attachments".
 *
 * The followings are the available columns in table 'discipline_attachments':
 * @property integer $id
 * @property integer $school_id
 * @property integer $discipline_participation_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $attachment_file_name
 * @property string $attachment_content_type
 * @property integer $attachment_file_size
 * @property string $attachment_updated_at
 */
class DisciplineAttachments extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'discipline_attachments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('school_id, discipline_participation_id, attachment_file_size', 'numerical', 'integerOnly'=>true),
			array('attachment_file_name, attachment_content_type', 'length', 'max'=>255),
			array('created_at, updated_at, attachment_updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, school_id, discipline_participation_id, created_at, updated_at, attachment_file_name, attachment_content_type, attachment_file_size, attachment_updated_at', 'safe', 'on'=>'search'),
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
			'school_id' => 'School',
			'discipline_participation_id' => 'Discipline Participation',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'attachment_file_name' => 'Attachment File Name',
			'attachment_content_type' => 'Attachment Content Type',
			'attachment_file_size' => 'Attachment File Size',
			'attachment_updated_at' => 'Attachment Updated At',
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
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('discipline_participation_id',$this->discipline_participation_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('attachment_file_name',$this->attachment_file_name,true);
		$criteria->compare('attachment_content_type',$this->attachment_content_type,true);
		$criteria->compare('attachment_file_size',$this->attachment_file_size);
		$criteria->compare('attachment_updated_at',$this->attachment_updated_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DisciplineAttachments the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
