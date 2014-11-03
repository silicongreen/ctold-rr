<?php

/**
 * This is the model class for table "tally_export_files".
 *
 * The followings are the available columns in table 'tally_export_files':
 * @property integer $id
 * @property integer $download_no
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 * @property string $export_file_file_name
 * @property string $export_file_content_type
 * @property integer $export_file_file_size
 * @property string $export_file_updated_at
 */
class TallyExportFiles extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tally_export_files';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('download_no, school_id, export_file_file_size', 'numerical', 'integerOnly'=>true),
			array('export_file_file_name, export_file_content_type', 'length', 'max'=>255),
			array('created_at, updated_at, export_file_updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, download_no, created_at, updated_at, school_id, export_file_file_name, export_file_content_type, export_file_file_size, export_file_updated_at', 'safe', 'on'=>'search'),
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
			'download_no' => 'Download No',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'school_id' => 'School',
			'export_file_file_name' => 'Export File File Name',
			'export_file_content_type' => 'Export File Content Type',
			'export_file_file_size' => 'Export File File Size',
			'export_file_updated_at' => 'Export File Updated At',
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
		$criteria->compare('download_no',$this->download_no);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('school_id',$this->school_id);
		$criteria->compare('export_file_file_name',$this->export_file_file_name,true);
		$criteria->compare('export_file_content_type',$this->export_file_content_type,true);
		$criteria->compare('export_file_file_size',$this->export_file_file_size);
		$criteria->compare('export_file_updated_at',$this->export_file_updated_at,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TallyExportFiles the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
