<?php

/**
 * This is the model class for table "imports".
 *
 * The followings are the available columns in table 'imports':
 * @property integer $id
 * @property integer $export_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 * @property string $csv_file_file_name
 * @property string $csv_file_content_type
 * @property integer $csv_file_file_size
 * @property string $csv_file_updated_at
 */
class Imports extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'imports';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('export_id, school_id, csv_file_file_size', 'numerical', 'integerOnly'=>true),
			array('status, csv_file_file_name, csv_file_content_type', 'length', 'max'=>255),
			array('created_at, updated_at, csv_file_updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, export_id, status, created_at, updated_at, school_id, csv_file_file_name, csv_file_content_type, csv_file_file_size, csv_file_updated_at', 'safe', 'on'=>'search'),
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
			'export_id' => 'Export',
			'status' => 'Status',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'school_id' => 'School',
			'csv_file_file_name' => 'Csv File File Name',
			'csv_file_content_type' => 'Csv File Content Type',
			'csv_file_file_size' => 'Csv File File Size',
			'csv_file_updated_at' => 'Csv File Updated At',
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
		$criteria->compare('export_id',$this->export_id);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('csv_file_file_name',$this->csv_file_file_name,true);
		$criteria->compare('csv_file_content_type',$this->csv_file_content_type,true);
		$criteria->compare('csv_file_file_size',$this->csv_file_file_size);
		$criteria->compare('csv_file_updated_at',$this->csv_file_updated_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Imports the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
