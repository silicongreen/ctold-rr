<?php

/**
 * This is the model class for table "tds_school_page".
 *
 * The followings are the available columns in table 'tds_school_page':
 * @property integer $id
 * @property integer $school_id
 * @property integer $menu_id
 * @property string $title
 * @property string $content
 * @property string $date
 */
class SchoolUser extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tds_user_school';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('school_id, user_id', 'required'),
            array('school_id, user_id', 'numerical', 'integerOnly' => true),
            array('id, school_id, user_id, is_approved, information, type, grade, approved_date', 'safe', 'on' => 'search'),
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
            'school' => array(self::BELONGS_TO, 'School', 'school_id'),
            'Freeusers' => array(self::HAS_MANY, 'Freeusers', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function userSchool($user_id, $school_id = 0, $paid_school_id = 0, $type = 0)
    {
        $criteria = new CDbCriteria();
        $criteria->select = "t.id,t.is_approved,t.school_id,t.type";
        if ($school_id)
        {
            $criteria->compare("t.school_id", $school_id);
        }
        $criteria->compare("t.user_id", $user_id);
        $userschools = $this->findAll($criteria);
        
        $paid_school = array();
        if($paid_school_id > 0 && $type > 0)
        {
            $schoolobj = new School();
            $paid_school = $schoolobj->getSchoolPaid($paid_school_id);
        }
        if ($userschools && $paid_school)
        {
            if($userschools[0]->school_id != $paid_school || $userschools[0]->type!=$type || $userschools[0]->is_approved!=1)
            {
               foreach ($userschools as $svalue)
               { 
                   $schooluser = new SchoolUser();
                   $school_del = $schooluser->findByPk($svalue->id);
                   $school_del->delete();
               }
               $userschools = array();
            }    
        }

        $user_schools = array();
        $i = 0;
        if ($userschools)
        {
            foreach ($userschools as $value)
            {
                if($i == 0)
                {
                    $user_schools[$i]['school_id'] = $value->school_id;
                    $user_schools[$i]['status'] = $value->is_approved;
                    $user_schools[$i]['type'] = $value->type;
                    
                }
                else
                {
                   $schooluser = new SchoolUser();
                   $school_del = $schooluser->findByPk($value->id);
                   $school_del->delete();
                }  
                $i++;
            }
        }
        else if ($paid_school_id > 0 && $type > 0)
        {
            
            if ($paid_school)
            {
                $userschool = new SchoolUser();
                $userschool->school_id = $paid_school;
                $userschool->user_id = $user_id;
                $userschool->is_approved = 1;
                $userschool->type = $type;
                $userschool->save();
                
                $criteria = new CDbCriteria();
                $criteria->select = "t.is_approved,t.school_id,t.type";
                if ($school_id)
                {
                    $criteria->compare("t.school_id", $school_id);
                }
                $criteria->compare("t.user_id", $user_id);
                $userschools = $this->findAll($criteria);

                $user_schools = array();
                $i = 0;
                if ($userschools)
                {
                    foreach ($userschools as $value)
                    {
                        $user_schools[$i]['school_id'] = $value->school_id;
                        $user_schools[$i]['status'] = $value->is_approved;
                        $user_schools[$i]['type'] = $value->type;
                        $i++;
                    }
                }
            }
        }

        return $user_schools;
    }

    public function userSchoolSingle($user_id, $school_id)
    {
        $criteria = new CDbCriteria();
        $criteria->select = "t.id,t.is_approved,t.school_id,t.type";
        if ($school_id)
        {
            $criteria->compare("t.school_id", $school_id);
        }
        $criteria->compare("t.user_id", $user_id);
        $userschools = $this->find($criteria);


        return $userschools;
    }

}
