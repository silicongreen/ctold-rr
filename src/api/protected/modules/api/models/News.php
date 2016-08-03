<?php

/**
 * This is the model class for table "news".
 *
 * The followings are the available columns in table 'news':
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $author_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 * @property integer $category_id
 */
class News extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public $total;

    public function tableName() {
        return 'news';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('author_id, school_id', 'numerical', 'integerOnly' => true),
            array('title', 'length', 'max' => 255),
            array('content, created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, title, content, author_id, created_at, updated_at, school_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'newsBatch' => array(self::HAS_MANY, 'BatchNews', 'news_id',
                'select' => 'newsBatch.id',
                'joinType' => 'LEFT JOIN',
            ),
            'newsDepartment' => array(self::HAS_MANY, 'DepartmentNews', 'news_id',
                'select' => 'newsBatch.id',
                'joinType' => 'LEFT JOIN',
            ),
            'authorDetails' => array(self::BELONGS_TO, 'Users', 'author_id',
                'select' => 'authorDetails.id, authorDetails.first_name, authorDetails.last_name',
                'joinType' => 'INNER JOIN',
            ),
            'commentDetails' => array(self::HAS_MANY, 'NewsComments', 'news_id',
                'select' => 'commentDetails.id, commentDetails.content, commentDetails.author_id',
                'joinType' => 'LEFT JOIN',
            ),
            'newsAcknowledge' => array(self::HAS_MANY, 'NewsAcknowledges', 'news_id',
                'select' => 'newsAcknoledge.id, newsAcknoledge.status, newsAcknoledge.acknowledged_by, newsAcknoledge.acknowledged_by_id',
                'joinType' => 'LEFT JOIN',
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'title' => 'Title',
            'content' => 'Content',
            'author_id' => 'Author',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'school_id' => 'School',
            'category_id' => 'Notice Type',
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
        $criteria->compare('title', $this->title, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('author_id', $this->author_id);
        $criteria->compare('created_at', $this->created_at, true);
        $criteria->compare('updated_at', $this->updated_at, true);
        $criteria->compare('school_id', $this->school_id);
        $criteria->compare('category_id', $this->category_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return News the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getSingleNews($id) {
        $criteria = new CDbCriteria;

        $criteria->select = 't.id, t.category_id, t.title, t.content, t.created_at, t.updated_at, t.attachment_file_name, t.attachment_content_type, t.attachment_file_size, t.attachment_updated_at';
        $criteria->compare('t.id', $id);

        $data = $this->with('authorDetails', 'newsAcknowledge')->find($criteria);

        return (!empty($data)) ? $this->formatSingleNotice($data) : \FALSE;
    }

    public function formatSingleNotice($row) {
        $_data['notice_id'] = $row->id;
        $_data['notice_type_id'] = $row->category_id;
        $_data['notice_type_text'] = ucfirst(Settings::$ar_notice_type[$row->category_id]);
        $_data['notice_title'] = $row->title;
        $_data['notice_content'] = $row->content;
        $_data['file_name'] = (!empty($row->attachment_file_name)) ? $row->attachment_file_name : '';
        $_data['file_type'] = (!empty($row->attachment_content_type)) ? $row->attachment_content_type : '';
        $_data['file_size'] = (!empty($row->attachment_file_size)) ? $row->attachment_file_size : '';
        $_data['file_updated_at'] = (!empty($row->attachment_updated_at)) ? $row->attachment_updated_at : '';
        $_data['published_at'] = date('Y-m-d H:i:s', strtotime($row->created_at));
        $_data['updated_at'] = date('Y-m-d H:i:s', strtotime($row->updated_at));
        $_data['author_id'] = rtrim($row['authorDetails']->id);
        $_data['author_first_name'] = rtrim($row['authorDetails']->first_name);
        $_data['author_full_name'] = rtrim($row['authorDetails']->first_name . ' ' . $row['authorDetails']->last_name);

        $_data['acknowledge'] = array();
        if (sizeof($row['newsAcknowledge']) > 0) {
            $_data['acknowledge'] = $this->formatAcknowledge($row['newsAcknowledge']);
        }

        return $_data;
    }
    
    public function getNoticeCountSchool($school_id) 
    { 
        $criteria = new CDbCriteria;
        $criteria->select = 'count(t.id) as total';
        $criteria->compare('t.school_id', $school_id);
        $criteria->compare('t.is_published', 1);
        $data = $this->find($criteria);
        return $data->total;
    }

    public function getNoticeSchool($school_id, $page_number = 1, $page_size = 10) 
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.category_id, t.title, t.content, t.created_at, t.updated_at, t.attachment_file_name, t.attachment_content_type, t.attachment_file_size, t.attachment_updated_at';
        $criteria->compare('t.school_id', $school_id);
        $criteria->order = 't.id DESC';
        $start = ($page_number - 1) * $page_size;
        $criteria->limit = $page_size;
        $criteria->offset = $start;
        $data = $this->with('authorDetails', 'newsAcknowledge')->findAll($criteria);
        return (!empty($data)) ? $this->formatNotice($data) : array();
    }
    
    public function getNoticeTerm($term) 
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id, t.category_id, t.title, t.content, t.created_at, t.updated_at, t.attachment_file_name, t.attachment_content_type, t.attachment_file_size, t.attachment_updated_at';
        $criteria->compare('t.school_id',  Yii::app()->user->schoolId);
        $criteria->addCondition("t.title like '%".$term."%'");
        $criteria->order = 't.id DESC';
        $criteria->limit = 5;
        $data = $this->with('authorDetails', 'newsAcknowledge')->findAll($criteria);
        return (!empty($data)) ? $this->formatNotice($data) : array();
    }

    public function getNoticeCount($notice_type = 1, $from_date="", $to_date="",$batch_id="") {

        $school_id = Yii::app()->user->schoolId;
        $criteria = new CDbCriteria;
        $criteria->select = 'count(t.id) as total';
        $criteria->compare('t.school_id', $school_id);
        $criteria->compare('t.is_published', 1);
        
        $with = array('authorDetails');
        if($from_date && $to_date)
        $criteria->addCondition("(DATE(t.created_at) <= '" . $to_date . "' AND DATE(t.created_at) >= '" . $from_date . "' ) OR (DATE(t.updated_at) <= '" . $to_date . "' AND DATE(t.updated_at) >= '" . $from_date . "')");
        
        if (Yii::app()->user->isStudent) {
            $with[] = 'newsBatch';
            $criteria->addCondition("(newsBatch.batch_id = '" . Yii::app()->user->batchId  . "' or t.is_common=1)");
        }
        else if(Yii::app()->user->isTeacher) 
        {
            $employee = new Employees;
            $employeeData = $employee->getEmployeeDepartment();
            
            if($employeeData)
            {
                $with[] = 'newsDepartment';
                $criteria->addCondition("(newsDepartment.department_id = '" . $employeeData->employee_department_id . "' or t.is_common=1 or author_id=".Yii::app()->user->id.")");
            }
            else
            {
                $criteria->compare('t.is_common', 1);
            }    
           
        }
        else if (Yii::app()->user->isParent && $batch_id)
        {
            $with[] = 'newsBatch';
            $criteria->addCondition("(newsBatch.batch_id = '" . Yii::app()->user->batchId  . "' or t.is_common=1)");
        }
        else 
        {
            $criteria->compare('t.is_common', 1);
        }   

        /**
         * DONT CHANGE THE LOGIC. APP SEND THE WRONG PARAMITER SO HAVE TO CHANGE LOGIC
         * 1=ALL
         * 2=ACADEMIC (1)
         * 3=EXTRA ACADAMIC (2)
         */
        if ($notice_type != 1) {
            if ($notice_type == 2) {
                $criteria->compare('category_id', 1);
            } else {
                $criteria->compare('category_id', 2);
            }
        }

        $data = $this->with($with)->find($criteria);

        if($data)
        {
            return $data->total;
        }
        return 0;
    }

    public function getNotice($notice_type = 1, $page_number = 1, $page_size = 10,$batch_id="") {

        $school_id = Yii::app()->user->schoolId;
        $criteria = new CDbCriteria;
        $criteria->together = true;
        $criteria->select = 't.id, t.category_id, t.title, t.content, t.created_at, t.updated_at, t.attachment_file_name, t.attachment_content_type, t.attachment_file_size, t.attachment_updated_at';
        $criteria->compare('t.school_id', $school_id);
        $criteria->compare('t.is_published', 1);
        
        $with = array('authorDetails', 'newsAcknowledge');
        if (Yii::app()->user->isStudent) {
            $with[] = 'newsBatch';
            $criteria->addCondition("(newsBatch.batch_id = '" . Yii::app()->user->batchId  . "' or t.is_common=1)");
        }
        else if(Yii::app()->user->isTeacher) 
        {
            $employee = new Employees;
            $employeeData = $employee->getEmployeeDepartment();
            
            if($employeeData)
            {
                $with[] = 'newsDepartment';
                $criteria->addCondition("(newsDepartment.department_id = '" . $employeeData->employee_department_id . "' or t.is_common=1 or author_id=".Yii::app()->user->id.")");
            }
            else
            {
                $criteria->compare('t.is_common', 1);
            }    
           
        }
        else if (Yii::app()->user->isParent && $batch_id)
        {
            $with[] = 'newsBatch';
            $criteria->addCondition("(newsBatch.batch_id = '" . Yii::app()->user->batchId  . "' or t.is_common=1)");
        }
        else 
        {
            $criteria->compare('t.is_common', 1);
        }

        /**
         * DONT CHANGE THE LOGIC. APP SEND THE WRONG PARAMITER SO HAVE TO CHANGE LOGIC
         * 1=ALL
         * 2=ACADEMIC (1)
         * 3=EXTRA ACADAMIC (2)
         */
        if ($notice_type != 1) {
            if ($notice_type == 2) {
                $criteria->compare('t.category_id', 1);
            } else {
                $criteria->compare('t.category_id', 2);
            }
        }
        $criteria->order = 't.id DESC';
        $start = ($page_number - 1) * $page_size;
        $criteria->limit = $page_size;
        $criteria->offset = $start;
    
        $data = $this->with($with)->findAll($criteria);

        return (!empty($data)) ? $this->formatNotice($data) : array();
    }

    public function getNews($school_id, $from_date, $to_date, $notice_type = '', $author_id = '') {

        $criteria = new CDbCriteria;

        $criteria->select = 't.id, t.category_id, t.title, t.content, t.created_at, t.updated_at';
        $criteria->compare('t.school_id', $school_id);

        if (!empty($author_id)) {
            $criteria->compare('author_id', $author_id);
        }

        if (!empty($notice_type)) {
            $criteria->compare('category_id', $notice_type);
        }

        $criteria->addCondition("(DATE(t.created_at) <= '" . $to_date . "' AND DATE(t.created_at) >= '" . $from_date . "' ) OR (DATE(t.updated_at) <= '" . $to_date . "' AND DATE(t.updated_at) >= '" . $from_date . "')");
        $criteria->order = 't.id DESC';

        $data = $this->with('authorDetails', 'newsAcknowledge')->findAll($criteria);

        return (!empty($data)) ? $this->formatNotice($data) : \FALSE;
    }

    public function formatNotice($obj_data) {

        $ar_formatted_data = array();

        foreach ($obj_data as $row) {

            $_data['notice_id'] = $row->id;
            $_data['notice_type_id'] = $row->category_id;
            $_data['notice_type_text'] = ucfirst(Settings::$ar_notice_type[$row->category_id]);
            $_data['notice_title'] = $row->title;
            $_data['notice_content'] = $row->content;
            $_data['file_name'] = (!empty($row->attachment_file_name)) ? $row->attachment_file_name : '';
            $_data['file_type'] = (!empty($row->attachment_content_type)) ? $row->attachment_content_type : '';
            $_data['file_size'] = (!empty($row->attachment_file_size)) ? $row->attachment_file_size : '';
            $_data['file_updated_at'] = (!empty($row->attachment_updated_at)) ? $row->attachment_updated_at : '';
            $_data['published_at'] = date('Y-m-d H:i:s', strtotime($row->created_at));
            $_data['updated_at'] = date('Y-m-d H:i:s', strtotime($row->updated_at));
            $_data['author_id'] = rtrim($row['authorDetails']->id);
            $_data['author_first_name'] = rtrim($row['authorDetails']->first_name);
            $_data['author_full_name'] = rtrim($row['authorDetails']->first_name . ' ' . $row['authorDetails']->last_name);

            $_data['acknowledge'] = array();
            if (sizeof($row['newsAcknowledge']) > 0) {
                $_data['acknowledge'] = $this->formatAcknowledge($row['newsAcknowledge']);
            }

            $ar_formatted_data[] = $_data;
        }

        return $ar_formatted_data;
    }

    public function formatAcknowledge($ar_acknowledge) {

        $ar_formatted_ack = array();
        foreach ($ar_acknowledge as $row) {
            $_data['acknowledge_status'] = $row->status;
            $_data['acknowledged_by'] = Settings::$ar_notice_acknowledge_by[$row->acknowledged_by];
            $_data['acknowledged_by_id'] = $row->acknowledged_by_id;
            $_data['acknowledge_msg'] = Settings::$ar_notice_acknowledge_status[$row->status];
            $ar_formatted_ack[] = $_data;
        }
        return $ar_formatted_ack;
    }

}
