<?php

/**
 * This is the model class for table "tds_post_category".
 *
 * The followings are the available columns in table 'tds_post_category':
 * @property integer $id
 * @property string $post_id
 * @property integer $category_id
 * @property integer $inner_priority
 *
 * The followings are the available model relations:
 * @property Categories $category
 * @property Post $post
 */
class GuardianStudent extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public $total = 0;

    public function tableName()
    {
        return 'guardian_students';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('student_id, guardian_id', 'required'),
            array('student_id, guardian_id', 'numerical', 'integerOnly' => true)
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
            'students' => array(self::BELONGS_TO, 'Students', 'student_id'),
            'guardian' => array(self::BELONGS_TO, 'Guardians', 'guardian_id'),
        );
    }
    
    public function data_exists($student_id,$guardian_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        $criteria->compare("t.student_id", $student_id);
        $criteria->compare("t.guardian_id", $guardian_id);
        
        $obj = $this->find($criteria);

        return $obj;
    }
    
    public function getGuardians($student_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        $criteria->compare("t.student_id", $student_id);
        $criteria->with = array(
            'guardian' => array(
                'select' => 'guardian.id',
                'joinType' => "INNER JOIN"
            )
        );

        $obj_post = $this->findAll($criteria);

        return $obj_post;
    }

   
          

    public function getChildren($guardian_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        $criteria->compare("t.guardian_id", $guardian_id);
        $criteria->with = array(
            'students' => array(
                'select' => 'students.*',
                'joinType' => "INNER JOIN",
                'with' => array(
                        'batchDetails' => array(
                        'select' => 'batchDetails.name',
                        'joinType' => "INNER JOIN",
                        'with' => array(
                            "courseDetails" => array(
                                "select" => "courseDetails.course_name,courseDetails.section_name",
                                'joinType' => "INNER JOIN",
                            )
                        )
                    )
                )
            )
        );

        $obj_post = $this->findAll($criteria);

        return $obj_post;
    }

    

}
