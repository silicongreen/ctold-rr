<?php

/**
 * This is the model class for table "previous_exams".
 *
 * The followings are the available columns in table 'previous_exams':
 * @property integer $id
 * @property integer $connect_exam_id
 * @property integer $date_type
 * @property string $data
 * @property integer $is_finished
 * @property string $created_at
 * @property string $updated_at
 */
class PreviousExams extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'previous_exams';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('connect_exam_id, data, created_at, school_id', 'required'),
            array('connect_exam_id, data_type, is_finished, school_id', 'numerical', 'integerOnly'=>true),
            array('updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, connect_exam_id, data_type, data, is_finished, school_id, created_at, updated_at', 'safe', 'on'=>'search'),
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

    public function getFinishExamALL($connect_exam_id,$data_type=1,$subject_id=0,$is_finished=1)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.data';
        $criteria->compare('t.data_type',$data_type);
        $criteria->compare('t.connect_exam_id',$connect_exam_id);
        $data = $this->find($criteria);
        if($data)
        {
            return $data->data;
        }        
        return false;
    }
    
    public function getFinishExam($connect_exam_id,$data_type=1,$subject_id=0,$is_finished=1)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.data';
        $criteria->compare('t.data_type',$data_type);
        $criteria->compare('t.is_finished',$is_finished);
        $criteria->compare('t.subject_id',$subject_id);
        $criteria->compare('t.connect_exam_id',$connect_exam_id);
        $data = $this->find($criteria);
        if($data)
        {
            return $data->data;
        }        
        return false;
    }
    
    public function saveExam($connect_exam_id,$data,$is_finished=0,$data_type=1,$subject_id = 0)
    {
        $school_id = Yii::app()->user->schoolId;
        $criteria = new CDbCriteria();
        $criteria->select = 't.id';
        $criteria->compare('t.data_type',$data_type);
        $criteria->compare('t.connect_exam_id',$connect_exam_id);
        $data_has = $this->find($criteria);
        $objThis = new PreviousExams();
        if($data_has)
        {
            $objThis = $objThis->findByPk($data_has->id);
            $objThis->data = $data;
            $objThis->is_finished = $is_finished;
            $objThis->updated_at = date("Y-m-d H:i:s");
            $objThis->save();
        }
        else 
        {
            
            $objThis->is_finished = $is_finished;
            $objThis->data = $data;
            $objThis->subject_id = $subject_id;
            $objThis->connect_exam_id = $connect_exam_id;
            $objThis->school_id = $school_id;
            $objThis->data_type = $data_type;
            $objThis->updated_at = date("Y-m-d H:i:s");
            $objThis->created_at = date("Y-m-d H:i:s");
            $objThis->save();
        }
        return true;
        
        
    }         

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'connect_exam_id' => 'Connect Exam',
            'date_type' => 'Date Type',
            'data' => 'Data',
            'is_finished' => 'Is Finished',
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
        $criteria->compare('connect_exam_id',$this->connect_exam_id);
        $criteria->compare('date_type',$this->date_type);
        $criteria->compare('data',$this->data,true);
        $criteria->compare('is_finished',$this->is_finished);
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
     * @return PreviousExams the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}