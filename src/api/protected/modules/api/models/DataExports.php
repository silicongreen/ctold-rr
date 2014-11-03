<?php

/**
 * This is the model class for table "data_exports".
 *
 * The followings are the available columns in table 'data_exports':
 * @property integer $id
 * @property integer $export_structure_id
 * @property string $file_format
 * @property string $status
 * @property string $export_file_file_name
 * @property string $export_file_content_type
 * @property integer $export_file_file_size
 * @property string $export_file_updated_at
 * @property integer $school_id
 * @property string $created_at
 * @property string $updated_at
 */
class DataExports extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'data_exports';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('export_structure_id, export_file_file_size, school_id', 'numerical', 'integerOnly'=>true),
			array('file_format, status, export_file_file_name, export_file_content_type', 'length', 'max'=>255),
			array('export_file_updated_at, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, export_structure_id, file_format, status, export_file_file_name, export_file_content_type, export_file_file_size, export_file_updated_at, school_id, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'export_structure_id' => 'Export Structure',
			'file_format' => 'File Format',
			'status' => 'Status',
			'export_file_file_name' => 'Export File File Name',
			'export_file_content_type' => 'Export File Content Type',
			'export_file_file_size' => 'Export File File Size',
			'export_file_updated_at' => 'Export File Updated At',
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
		$criteria->compare('export_structure_id',$this->export_structure_id);
		$criteria->compare('file_format',$this->file_format,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('export_file_file_name',$this->export_file_file_name,true);
		$criteria->compare('export_file_content_type',$this->export_file_content_type,true);
		$criteria->compare('export_file_file_size',$this->export_file_file_size);
		$criteria->compare('export_file_updated_at',$this->export_file_updated_at,true);
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
	 * @return DataExports the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
