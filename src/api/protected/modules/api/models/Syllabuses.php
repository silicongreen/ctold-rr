<?php

/**
 * This is the model class for table "features".
 *
 * The followings are the available columns in table 'features':
 * @property integer $id
 * @property string $feature_key
 * @property integer $is_enabled
 * @property string $created_at
 * @property string $updated_at
 */
class Syllabuses extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'syllabuses';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('batch_id, exam_group_id, is_yearly, subject_id, school_id', 'numerical', 'integerOnly' => true),
            array('created_at, updated_at, content', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, batch_id, exam_group_id, is_yearly, subject_id, content, school_id, created_at, updated_at', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'termDetails' => array(self::BELONGS_TO, 'ExamGroups', 'exam_group_id',
                'joinType' => 'INNER JOIN',
            ),
            'subjectDetails' => array(self::BELONGS_TO, 'Subjects', 'subject_id',
                'joinType' => 'INNER JOIN',
                'select' => 'subjectDetails.name, subjectDetails.icon_number'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'batch_id' => 'Batch',
            'exam_group_id' => 'Term',
            'is_yearly' => 'Yearly ?',
            'subject_id' => 'Subject',
            'content' => 'Syllabus',
            'school_id' => 'School',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('batch_id', $this->batch_id);
        $criteria->compare('exam_group_id', $this->exam_group_id);
        $criteria->compare('is_yearly', $this->is_yearly);
        $criteria->compare('subject_id', $this->subject_id);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('school_id', $this->school_id);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Features the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function getSingleSyllabus($id) {
        $criteria = new CDbCriteria;

        $criteria->select = 't.id, t.content,t.title, t.subject_id, t.updated_at';
        $criteria->compare('t.id', $id);
        
        $data = $this->with('subjectDetails')->findAll($criteria);
        
        if(!empty($data)){
            $data = $this->formatSyllabus($data);
            return $data[0];
        }
        return false;
    }

    public function getSyllabus($term_id=0, $batch_id = null, $b_yearly = false) {

        if (Yii::app()->user->isStudent) {
            $batch_id = Yii::app()->user->batchId;
        }

        $criteria = new CDbCriteria;

        $criteria->select = 't.id, t.content,t.title, t.subject_id, t.updated_at';
        $criteria->compare('t.batch_id', $batch_id);
        $criteria->compare('t.exam_group_id', $term_id);
     
        
        $data = $this->with('subjectDetails')->findAll($criteria);
        
        if(!empty($data)){
            $data = $this->formatSyllabus($data);
            return $data;
        }
        return false;
    }
    
    public function formatSyllabus($obj_syllabus) {
        
        $ar_formatted_data = array();
        foreach($obj_syllabus as $row){
            
            $_data['id'] = $row->id;
            $_data['subject_id'] = $row->subject_id;
            $_data['subject_name'] = $row['subjectDetails']->name." (".$row->title.") ";
            $_data['subject_icon_name'] = $row['subjectDetails']->icon_number;
            $_data['subject_icon_path'] = (!empty($row['subjectDetails']->icon_number)) ? Settings::$domain_name . '/images/icons/subjects/' . $row['subjectDetails']->icon_number : null;
            
            
            #Yii::app()->user->setState('school_code',null);
            
            #unset(Yii::app()->user->school_code);
            
            #$school_url = "http://".Yii::app()->user->school_code.".champs21.com";

            $_data['syllabus_text'] = "";
            if($row->content)
            $_data['syllabus_text'] = $row->content; 
            $_data['last_updated'] = $row->updated_at;
            
            $ar_formatted_data[] = $_data;
        }
        
        return $ar_formatted_data;
    }

}
