<?php

/**
 * This is the model class for table "tasks".
 *
 * The followings are the available columns in table 'tasks':
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $description
 * @property string $status
 * @property string $start_date
 * @property string $due_date
 * @property string $attachment_file_name
 * @property string $attachment_content_type
 * @property integer $attachment_file_size
 * @property string $attachment_updated_at
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Tasks extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'tasks';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, attachment_file_size, school_id', 'numerical', 'integerOnly' => true),
            array('title, status, attachment_file_name, attachment_content_type', 'length', 'max' => 255),
            array('description, start_date, due_date, attachment_updated_at, created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_id, title, description, status, start_date, due_date, attachment_file_name, attachment_content_type, attachment_file_size, attachment_updated_at, created_at, updated_at, school_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'title' => 'Title',
            'description' => 'Description',
            'status' => 'Status',
            'start_date' => 'Start Date',
            'due_date' => 'Due Date',
            'attachment_file_name' => 'Attachment File Name',
            'attachment_content_type' => 'Attachment Content Type',
            'attachment_file_size' => 'Attachment File Size',
            'attachment_updated_at' => 'Attachment Updated At',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('start_date', $this->start_date, true);
        $criteria->compare('due_date', $this->due_date, true);
        $criteria->compare('attachment_file_name', $this->attachment_file_name, true);
        $criteria->compare('attachment_content_type', $this->attachment_content_type, true);
        $criteria->compare('attachment_file_size', $this->attachment_file_size);
        $criteria->compare('attachment_updated_at', $this->attachment_updated_at, true);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('school_id', $this->school_id);

        return new CActiveDataProvider($this, array(
                    'criteria' => $criteria,
                ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Tasks the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }
    
    public function getTasksByMe($school_id, $page_number, $page_size, $ar_param = array()) {

        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.title, t.description, t.status, t.start_date, t.due_date';

        if (!empty($ar_param) && isset($ar_param['start_date']) && !empty($ar_param['start_date'])) {
            $criteria->compare('t.start_date', $ar_param['start_date']);
        }

        if (!empty($ar_param) && isset($ar_param['due_date']) && !empty($ar_param['due_date'])) {
            $criteria->compare('t.due_date', $ar_param['due_date']);
        }

        if (!empty($ar_param) && isset($ar_param['status']) && !empty($ar_param['status'])) {
            $criteria->compare('t.status', $ar_param['status']);
        }

        $criteria->compare('t.user_id', Yii::app()->user->id);
        $criteria->compare('t.school_id', $school_id);

        $criteria->order = 't.due_date DESC';

        $start = ($page_number - 1) * $page_size;
        $criteria->limit = $page_size;

        $criteria->offset = $start;

        $data = $this->findAll($criteria);

        $res = array();
        if (!empty($data)) {
            $i = 0;
            foreach ($data as $row) {
                $res[$i]['id'] = $row->id;
                $res[$i]['title'] = $row->title;
                $res[$i]['description'] = $row->description;
                $res[$i]['start_date'] = $row->start_date;
                $res[$i]['due_date'] = $row->due_date;
                $res[$i]['status'] = $row->status;
                $i++;
            }
        }

        return $res;
    }

}
