<?php

/**
 * This is the model class for table "task_assignees".
 *
 * The followings are the available columns in table 'task_assignees':
 * @property integer $id
 * @property integer $task_id
 * @property integer $assignee_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class TaskAssignees extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'task_assignees';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('task_id, assignee_id, school_id', 'numerical', 'integerOnly' => true),
            array('created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, task_id, assignee_id, created_at, updated_at, school_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'taskDetails' => array(self::BELONGS_TO, 'Tasks', 'task_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'task_id' => 'Task',
            'assignee_id' => 'Assignee',
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
        $criteria->compare('task_id', $this->task_id);
        $criteria->compare('assignee_id', $this->assignee_id);
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
     * @return TaskAssignees the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function getTasksToMe($school_id, $page_number, $page_size, $ar_param = array()) {

        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.task_id';

        if (!empty($ar_param) && isset($ar_param['start_date']) && !empty($ar_param['start_date'])) {
            $criteria->compare('start_date', $ar_param['start_date']);
        }

        if (!empty($ar_param) && isset($ar_param['due_date']) && !empty($ar_param['due_date'])) {
            $criteria->compare('due_date', $ar_param['due_date']);
        }

        if (!empty($ar_param) && isset($ar_param['status']) && !empty($ar_param['status'])) {
            $criteria->compare('status', $ar_param['status']);
        }

        $criteria->with = array(
            'taskDetails' => array(
                'select' => 'taskDetails.id, taskDetails.user_id, taskDetails.title, taskDetails.description, taskDetails.status, taskDetails.start_date, taskDetails.due_date',
                'joinType' => 'INNER JOIN'
            )
        );

        $criteria->compare('t.assignee_id', Yii::app()->user->id);
        $criteria->compare('t.school_id', $school_id);

        $criteria->order = 'taskDetails.due_date DESC';
        $criteria->group = 't.task_id';

        $start = ($page_number - 1) * $page_size;
        $criteria->limit = $page_size;

        $criteria->offset = $start;

        $data = $this->findAll($criteria);

        $res = array();
        if (!empty($data)) {
            $i = 0;
            foreach ($data as $row) {
                $res[$i]['id'] = $row->task_id;
                $res[$i]['title'] = $row->taskDetails->title;
                $res[$i]['description'] = $row->taskDetails->description;
                $res[$i]['start_date'] = $row->taskDetails->start_date;
                $res[$i]['due_date'] = $row->taskDetails->due_date;
                $res[$i]['status'] = $row->taskDetails->status;
                $i++;
            }
        }

        return $res;
    }

}
