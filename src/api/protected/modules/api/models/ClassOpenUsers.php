<?php

/**
 * This is the model class for table "class_open_users".
 *
 * The followings are the available columns in table 'class_open_users':
 * @property integer $id
 * @property integer $class_open_id
 * @property integer $user_id
 * @property integer $batch_id
 * @property integer $department_id
 * @property integer $school_id
 */
class ClassOpenUsers extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'class_open_users';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('class_open_id, school_id', 'required'),
            array('class_open_id, user_id, batch_id, department_id, school_id', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, class_open_id, user_id, batch_id, department_id, school_id', 'safe', 'on'=>'search'),
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
            'class_open_id' => 'Class Open',
            'user_id' => 'User',
            'batch_id' => 'Batch',
            'department_id' => 'Department',
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
        $criteria->compare('class_open_id',$this->class_open_id);
        $criteria->compare('user_id',$this->user_id);
        $criteria->compare('batch_id',$this->batch_id);
        $criteria->compare('department_id',$this->department_id);
        $criteria->compare('school_id',$this->school_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ClassOpenUsers the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function get_class_open_id($batch_id = 0, $department_id = 0)
    {
        $class_open_ids = [];
        if($batch_id == 0 && $department_id == 0)
        {
            return $class_open_ids;
        }
        
        $criteria=new CDbCriteria;
        $criteria->compare('batch_id', $batch_id);
        //$criteria->compare('department_id', $department_id);
       
        $all_class_open = $this->findAll($criteria);
        if($all_class_open)
        {
            foreach($all_class_open as $value)
            {
                $class_open_ids[] = $value->class_open_id;
            }    
        }
        return $class_open_ids;
       
        
    }        
}