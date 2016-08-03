<?php

/**
 * This is the model class for table "group_files".
 *
 * The followings are the available columns in table 'group_files':
 * @property integer $id
 * @property integer $group_id
 * @property integer $user_id
 * @property string $file_description
 * @property integer $group_post_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $doc_file_name
 * @property string $doc_content_type
 * @property integer $doc_file_size
 * @property string $doc_updated_at
 * @property integer $school_id
 */
class GroupFiles extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'group_files';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('group_id, user_id, group_post_id, doc_file_size, school_id', 'numerical', 'integerOnly'=>true),
			array('file_description, doc_file_name, doc_content_type', 'length', 'max'=>255),
			array('created_at, updated_at, doc_updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, group_id, user_id, file_description, group_post_id, created_at, updated_at, doc_file_name, doc_content_type, doc_file_size, doc_updated_at, school_id', 'safe', 'on'=>'search'),
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
			'group_id' => 'Group',
			'user_id' => 'User',
			'file_description' => 'File Description',
			'group_post_id' => 'Group Post',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'doc_file_name' => 'Doc File Name',
			'doc_content_type' => 'Doc Content Type',
			'doc_file_size' => 'Doc File Size',
			'doc_updated_at' => 'Doc Updated At',
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
		$criteria->compare('group_id',$this->group_id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('file_description',$this->file_description,true);
		$criteria->compare('group_post_id',$this->group_post_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('doc_file_name',$this->doc_file_name,true);
		$criteria->compare('doc_content_type',$this->doc_content_type,true);
		$criteria->compare('doc_file_size',$this->doc_file_size);
		$criteria->compare('doc_updated_at',$this->doc_updated_at,true);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GroupFiles the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
