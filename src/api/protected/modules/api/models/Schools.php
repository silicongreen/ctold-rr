<?php

/**
 * This is the model class for table "schools".
 *
 * The followings are the available columns in table 'schools':
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $created_at
 * @property string $updated_at
 * @property string $last_seeded_at
 * @property integer $is_deleted
 * @property integer $school_group_id
 * @property integer $creator_id
 * @property integer $inherit_sms_settings
 * @property integer $inherit_smtp_settings
 * @property integer $access_locked
 */
class Schools extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'schools';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_deleted, school_group_id, creator_id, inherit_sms_settings, inherit_smtp_settings, access_locked', 'numerical', 'integerOnly'=>true),
			array('name, code', 'length', 'max'=>255),
			array('created_at, updated_at, last_seeded_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, code, created_at, updated_at, last_seeded_at, is_deleted, school_group_id, creator_id, inherit_sms_settings, inherit_smtp_settings, access_locked', 'safe', 'on'=>'search'),
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
                    
                        'domain' => array(self::HAS_MANY, 'SchoolDomains', 'linkable_id' )
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
			'code' => 'Code',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'last_seeded_at' => 'Last Seeded At',
			'is_deleted' => 'Is Deleted',
			'school_group_id' => 'School Group',
			'creator_id' => 'Creator',
			'inherit_sms_settings' => 'Inherit Sms Settings',
			'inherit_smtp_settings' => 'Inherit Smtp Settings',
			'access_locked' => 'Access Locked',
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
		$criteria->compare('code',$this->code,true);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('last_seeded_at',$this->last_seeded_at,true);
		$criteria->compare('is_deleted',$this->is_deleted);
		$criteria->compare('school_group_id',$this->school_group_id);
		$criteria->compare('creator_id',$this->creator_id);
		$criteria->compare('inherit_sms_settings',$this->inherit_sms_settings);
		$criteria->compare('inherit_smtp_settings',$this->inherit_smtp_settings);
		$criteria->compare('access_locked',$this->access_locked);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Schools the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getschooltype($id)
        {
            $school_type = 1;
            $subsinfo = new Subscription();
            $s_subscription = $subsinfo->find_subscription($id);
            if($s_subscription)
            {
                $today = date("Y-m-d");
                if($today<=$s_subscription->end_date)
                {
                    $school_type = 1;
                }   
            }
//            $criteria = new CDbCriteria();
//            $criteria->select = "id";
//            $criteria->compare("t.id", $id);
//            $criteria->with = array(
//                'domain' => array(
//                    'select' => 'domain.domain'
//                )
//            );
//            $school_domains = $this->find($criteria);
//            
//            $school_type = 1;
//            if($school_domains['domain'])
//            {
//                foreach($school_domains['domain'] as $value)
//                {
//                    foreach(Settings::$free_domain_string as $fs)
//                    {
//                        if(strpos($value->domain, $fs)!==false)
//                        {
//                            $school_type = 0;
//                        }        
//                    }    
//                }    
//            }
            return $school_type;
            
        }
        
        public function getschoolbycode($schoolcode)
        {
            $criteria = new CDbCriteria();
            $criteria->select = "t.id,t.name,t.code";
            $criteria->compare("t.activation_code", $schoolcode);
            $userschools = $this->find($criteria);


            return $userschools;
        }
}
