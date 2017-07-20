<?php

/**
 * This is the model class for table "menu_links".
 *
 * The followings are the available columns in table 'menu_links':
 * @property integer $id
 * @property string $name
 * @property string $target_controller
 * @property string $target_action
 * @property integer $higher_link_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $icon_class
 * @property string $link_type
 * @property string $user_type
 * @property integer $menu_link_category_id
 */
class MenuLinks extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'menu_links';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('higher_link_id, menu_link_category_id', 'numerical', 'integerOnly'=>true),
			array('name, target_controller, target_action, icon_class, link_type, user_type', 'length', 'max'=>255),
			array('created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, target_controller, target_action, higher_link_id, created_at, updated_at, icon_class, link_type, user_type, menu_link_category_id', 'safe', 'on'=>'search'),
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
			'target_controller' => 'Target Controller',
			'target_action' => 'Target Action',
			'higher_link_id' => 'Higher Link',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'icon_class' => 'Icon Class',
			'link_type' => 'Link Type',
			'user_type' => 'User Type',
			'menu_link_category_id' => 'Menu Link Category',
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
		$criteria->compare('target_controller',$this->target_controller,true);
		$criteria->compare('target_action',$this->target_action,true);
		$criteria->compare('higher_link_id',$this->higher_link_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('icon_class',$this->icon_class,true);
		$criteria->compare('link_type',$this->link_type,true);
		$criteria->compare('user_type',$this->user_type,true);
		$criteria->compare('menu_link_category_id',$this->menu_link_category_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MenuLinks the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
