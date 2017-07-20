<?php

/**
 * This is the model class for table "books".
 *
 * The followings are the available columns in table 'books':
 * @property integer $id
 * @property string $title
 * @property string $author
 * @property string $book_number
 * @property integer $book_movement_id
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Books extends CActiveRecord {

    public $tag_name;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'books';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('book_movement_id, school_id', 'numerical', 'integerOnly' => true),
            array('title, author, book_number, status', 'length', 'max' => 255),
            array('created_at, updated_at', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, title, author, book_number, book_movement_id, status, created_at, updated_at, school_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'bookMovement' => array(self::BELONGS_TO, 'BookMovements', 'book_movement_id',
                'joinType' => 'LEFT JOIN',
            ),
            'bookReservation' => array(self::HAS_ONE, 'BookReservations', 'book_id',
                'joinType' => 'LEFT JOIN',
            ),
            'tagging' => array(self::HAS_MANY, 'Taggings', '',
                'on' => 'tagging.taggable_id = t.id',
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
            'author' => 'Author',
            'book_number' => 'Book Number',
            'book_movement_id' => 'Book Movement',
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
        $criteria->compare('title', $this->title, true);
        $criteria->compare('author', $this->author, true);
        $criteria->compare('book_number', $this->book_number, true);
        $criteria->compare('book_movement_id', $this->book_movement_id);
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
     * @return Books the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getBookDetails($school_id, $title = NULL, $author = NULL, $tag = NULL, $page_no = 1, $page_size = 10, $b_count = false) {

        $criteria = new CDbCriteria;

        $criteria->select = 't.id, t.title, t.author, t.`status`, tagDetails.name AS tag_name';

        $criteria->with = array(
            'bookMovement' => array(
                'select' => 'bookMovement.id',
            ),
            'bookReservation' => array(
                'select' => 'bookReservation.id',
            ),
        );

        $tagging = new Taggings;

        $criteria_1 = new CDbCriteria;
        $criteria_1->select = 't.taggable_id, tag_id';
        $criteria_1->addCondition("t.taggable_type = 'Book'");

        $taggingSql = $tagging->getCommandBuilder()->createFindCommand($tagging->getTableSchema(), $criteria_1)->getText();

        $criteria->join = 'LEFT JOIN (' . $taggingSql . ') AS tagging ON tagging.taggable_id = t.id';
        $criteria->join .= ' LEFT JOIN tags AS tagDetails ON tagDetails.id = tagging.tag_id';

        if (!empty($title)) {
            $criteria->compare('LOWER(t.title)', $title, TRUE);
        }

        if (!empty($author)) {
            $criteria->compare('LOWER(t.author)', $author, TRUE);
        }

        if (!empty($tag)) {
            $criteria->compare('LOWER(tagDetails.name)', $tag, TRUE);
        }

        $criteria->compare('t.school_id', $school_id);

        if (!$b_count) {
            $criteria->order = 't.id DESC';
            $criteria->together = true;
            $start = ($page_no - 1) * $page_size;
            $criteria->offset = $start;
            $criteria->limit = $page_size;
        }

        $criteria->group = 't.id';

        $obj_books = $this->findAll($criteria);

        if (!empty($obj_books)) {

            if (!$b_count) {
                $formatted_books = $this->formatBookDetails($obj_books);
            } else {
                $formatted_books = sizeof($obj_books);
            }

            return $formatted_books;
        }

        return FALSE;
    }

    public function formatBookDetails($obj_books) {

        $formatted_books = array();

        foreach ($obj_books as $row) {

            $_data['book_id'] = $row->id;
            $_data['book_title'] = $row->title;
            $_data['book_author'] = $row->author;

            $_data['book_only_to_read'] = FALSE;
            if ($row->tag_name == 'Reference Book') {
                $_data['book_only_to_read'] = TRUE;
            }

            $_data['book_reserve_able'] = TRUE;

            if ($row->status == 'Borrowed' || $row->status == 'Reserved' || $row->tag_name == 'Reference Book') {
                $_data['book_reserve_able'] = FALSE;
            }

            $formatted_books[] = $_data;
        }

        return $formatted_books;
    }

    public function reserveBook($book_id) {

        $book = $this->findByPk($book_id);

        if ($book->status == 'Reserved' || $book->status == 'Borrowed') {
            
            $_data['book_id'] = $book_id;
            $_data['book_reserved'] = TRUE;

            $formatted_books[] = $_data;
            return $formatted_books;
        }

        $now = date('Y-m-d H:i:s', time());

        $book->status = 'Reserved';
        $book->updated_at = $now;

        if ($book->update()) {
            
            $reserve = new BookReservations;
            $reserve->book_id = $book_id;
            $reserve->user_id = Yii::app()->user->id;
            $reserve->reserved_on = $now;
            $reserve->created_at = $now;
            $reserve->updated_at = $now;
            $reserve->school_id = Yii::app()->user->schoolId;
            $reserve->insert();
        }
        
        $_data['book_id'] = $book_id;
        $_data['book_reserved'] = TRUE;
        
        $formatted_books[] = $_data;
        return $formatted_books;
    }

}
