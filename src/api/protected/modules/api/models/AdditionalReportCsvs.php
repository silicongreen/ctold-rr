<?php

/**
 * This is the model class for table "additional_report_csvs".
 *
 * The followings are the available columns in table 'additional_report_csvs':
 * @property integer $id
 * @property string $model_name
 * @property string $method_name
 * @property string $parameters
 * @property string $created_at
 * @property string $updated_at
 * @property string $csv_report_file_name
 * @property string $csv_report_content_type
 * @property integer $csv_report_file_size
 * @property string $csv_report_updated_at
 * @property integer $school_id
 */
class AdditionalReportCsvs extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'additional_report_csvs';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('csv_report_file_size, school_id', 'numerical', 'integerOnly'=>true),
			array('model_name, method_name, csv_report_file_name, csv_report_content_type', 'length', 'max'=>255),
			array('parameters, created_at, updated_at, csv_report_updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, model_name, method_name, parameters, created_at, updated_at, csv_report_file_name, csv_report_content_type, csv_report_file_size, csv_report_updated_at, school_id', 'safe', 'on'=>'search'),
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
			'model_name' => 'Model Name',
			'method_name' => 'Method Name',
			'parameters' => 'Parameters',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'csv_report_file_name' => 'Csv Report File Name',
			'csv_report_content_type' => 'Csv Report Content Type',
			'csv_report_file_size' => 'Csv Report File Size',
			'csv_report_updated_at' => 'Csv Report Updated At',
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
		$criteria->compare('model_name',$this->model_name,true);
		$criteria->compare('method_name',$this->method_name,true);
		$criteria->compare('parameters',$this->parameters,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('csv_report_file_name',$this->csv_report_file_name,true);
		$criteria->compare('csv_report_content_type',$this->csv_report_content_type,true);
		$criteria->compare('csv_report_file_size',$this->csv_report_file_size);
		$criteria->compare('csv_report_updated_at',$this->csv_report_updated_at,true);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AdditionalReportCsvs the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
