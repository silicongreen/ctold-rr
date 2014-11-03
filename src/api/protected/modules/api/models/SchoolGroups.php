<?php

/**
 * This is the model class for table "school_groups".
 *
 * The followings are the available columns in table 'school_groups':
 * @property integer $id
 * @property string $name
 * @property integer $admin_user_id
 * @property integer $parent_group_id
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 * @property integer $whitelabel_enabled
 * @property integer $license_count
 * @property integer $inherit_sms_settings
 * @property integer $inherit_smtp_settings
 * @property integer $is_deleted
 */
class SchoolGroups extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'school_groups';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('admin_user_id, parent_group_id, whitelabel_enabled, license_count, inherit_sms_settings, inherit_smtp_settings, is_deleted', 'numerical', 'integerOnly'=>true),
			array('name, type', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, admin_user_id, parent_group_id, type, created_at, updated_at, whitelabel_enabled, license_count, inherit_sms_settings, inherit_smtp_settings, is_deleted', 'safe', 'on'=>'search'),
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
			'admin_user_id' => 'Admin User',
			'parent_group_id' => 'Parent Group',
			'type' => 'Type',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'whitelabel_enabled' => 'Whitelabel Enabled',
			'license_count' => 'License Count',
			'inherit_sms_settings' => 'Inherit Sms Settings',
			'inherit_smtp_settings' => 'Inherit Smtp Settings',
			'is_deleted' => 'Is Deleted',
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
		$criteria->compare('admin_user_id',$this->admin_user_id);
		$criteria->compare('parent_group_id',$this->parent_group_id);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('whitelabel_enabled',$this->whitelabel_enabled);
		$criteria->compare('license_count',$this->license_count);
		$criteria->compare('inherit_sms_settings',$this->inherit_sms_settings);
		$criteria->compare('inherit_smtp_settings',$this->inherit_smtp_settings);
		$criteria->compare('is_deleted',$this->is_deleted);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SchoolGroups the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
