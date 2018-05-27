<?php

/**
 * This is the model class for table "employee_references".
 *
 * The followings are the available columns in table 'employee_references':
 * @property integer $id
 * @property string $name
 * @property string $mailing_address
 * @property string $land_phone
 * @property string $mobile
 * @property string $email
 * @property integer $employee_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class EmployeeReferences extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'employee_references';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, employee_id, created_at, updated_at, school_id', 'required'),
            array('employee_id, school_id', 'numerical', 'integerOnly'=>true),
            array('name, mailing_address, land_phone, mobile, email', 'length', 'max'=>255),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, mailing_address, land_phone, mobile, email, employee_id, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
            'mailing_address' => 'Mailing Address',
            'land_phone' => 'Land Phone',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'employee_id' => 'Employee',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
        $criteria->compare('name',$this->name,true);
        $criteria->compare('mailing_address',$this->mailing_address,true);
        $criteria->compare('land_phone',$this->land_phone,true);
        $criteria->compare('mobile',$this->mobile,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('employee_id',$this->employee_id);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('updated_at',$this->updated_at,true);
        $criteria->compare('school_id',$this->school_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return EmployeeReferences the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}