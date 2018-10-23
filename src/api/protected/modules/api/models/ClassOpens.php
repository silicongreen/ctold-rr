<?php

/**
 * This is the model class for table "class_opens".
 *
 * The followings are the available columns in table 'class_opens':
 * @property integer $id
 * @property string $date
 * @property string $details
 * @property string $created_at
 * @property string $updated_at
 * @property integer $is_common
 * @property integer $school_id
 */
class ClassOpens extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'class_opens';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date, created_at, school_id', 'required'),
            array('is_common, school_id', 'numerical', 'integerOnly'=>true),
            array('details, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, date, details, created_at, updated_at, is_common, school_id', 'safe', 'on'=>'search'),
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
            'date' => 'Date',
            'details' => 'Details',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_common' => 'Is Common',
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
        $criteria->compare('date',$this->date,true);
        $criteria->compare('details',$this->details,true);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('updated_at',$this->updated_at,true);
        $criteria->compare('is_common',$this->is_common);
        $criteria->compare('school_id',$this->school_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ClassOpens the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function get_class_open($start_date,$end_date,$batch_id=0,$department_id = 0)
    {
        $extra_condition = "";
        $classOpenUserObj = new ClassOpenUsers();
        $classopen_ids = $classOpenUserObj->get_class_open_id($batch_id,$department_id);
        if($classopen_ids)
        {
           $extra_condition = " OR (is_common = 0 and id in (".implode(',',$classopen_ids)."))"; 
        }    
        $criteria=new CDbCriteria;
        $criteria->addCondition("(is_common = 1".$extra_condition.") and date >= '$start_date' and date <= '$end_date' and school_id=".Yii::app()->user->schoolId);
        $all_common = $this->findAll($criteria);
        
        $class_open_dates = [];
        if($all_common)
        {
            foreach($all_common as $value)
            {
               $class_open_dates[] = $value->date;
            }  
        }
        
       return $class_open_dates;
    }
}