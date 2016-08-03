<?php

/**
 * This is the model class for table "export_structures".
 *
 * The followings are the available columns in table 'export_structures':
 * @property integer $id
 * @property string $model_name
 * @property string $query
 * @property string $template
 * @property string $plugin_name
 * @property string $csv_header_order
 * @property string $created_at
 * @property string $updated_at
 */
class ExportStructures extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'export_structures';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('model_name, template, plugin_name', 'length', 'max'=>255),
			array('query, csv_header_order, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, model_name, query, template, plugin_name, csv_header_order, created_at, updated_at', 'safe', 'on'=>'search'),
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
			'query' => 'Query',
			'template' => 'Template',
			'plugin_name' => 'Plugin Name',
			'csv_header_order' => 'Csv Header Order',
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
		$criteria->compare('model_name',$this->model_name,true);
		$criteria->compare('query',$this->query,true);
		$criteria->compare('template',$this->template,true);
		$criteria->compare('plugin_name',$this->plugin_name,true);
		$criteria->compare('csv_header_order',$this->csv_header_order,true);
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
	 * @return ExportStructures the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
