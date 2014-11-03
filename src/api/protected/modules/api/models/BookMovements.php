<?php

/**
 * This is the model class for table "book_movements".
 *
 * The followings are the available columns in table 'book_movements':
 * @property integer $id
 * @property integer $user_id
 * @property integer $book_id
 * @property string $issue_date
 * @property string $due_date
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class BookMovements extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'book_movements';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, book_id, school_id', 'numerical', 'integerOnly' => true),
            array('status', 'length', 'max' => 255),
            array('issue_date, due_date, created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_id, book_id, issue_date, due_date, status, created_at, updated_at, school_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'bookDetails' => array(self::BELONGS_TO, 'Books', 'book_id',
                'joinType' => 'INNER JOIN',
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'book_id' => 'Book',
            'issue_date' => 'Issue Date',
            'due_date' => 'Due Date',
            'status' => 'Status',
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
        $criteria->compare('book_id', $this->book_id);
        $criteria->compare('issue_date', $this->issue_date, true);
        $criteria->compare('due_date', $this->due_date, true);
        $criteria->compare('status', $this->status, true);
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
     * @return BookMovements the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getBorrowingHistory($history = false, $from_date = NULL, $to_date = NULL, $page_no = 0, $page_size = 10, $b_count = FALSE) {

        $criteria = new CDbCriteria;

        $criteria->select = 't.id, t.issue_date, t.due_date, t.status';
        $criteria->with = array(
            'bookDetails' => array(
                'select' => 'bookDetails.id, bookDetails.title'
            ),
        );

        if (!empty($from_date)) {
            $criteria->compare('DATE(t.issue_date) <', $from_date);
        }

        if (!empty($to_date)) {
            $criteria->compare('DATE(t.issue_date) >', $to_date);
        }
        
        if ($history) {
            $criteria->compare('t.status', 'Returned');
        }else{
            $criteria->compare('t.status', 'Issued');
        }
        
        if (!$b_count) {
            $criteria->order = 't.id DESC';
            $criteria->together = true;
            $start = ($page_no - 1) * $page_size;
            $criteria->offset = $start;
            $criteria->limit = $page_size;
        }
        
        $obj_data = $this->findAll($criteria);
        
        return (!empty($obj_data)) ? $this->formatBookHistory($obj_data) : FALSE;
    }
    
    public function formatBookHistory($obj_data) {
        
        $formatted_data = array();
        
        foreach ($obj_data as $row) {
            $_data['book_id'] = $row->bookDetails->id;
            $_data['book_title'] = $row->bookDetails->title;
            $_data['book_borrow_date'] = date('Y-m-d', strtotime($row->issue_date));
            $_data['book_return_date'] = date('Y-m-d', strtotime($row->due_date));
            
            $formatted_data[] = $_data;
        }
        return $formatted_data;
    }

}
