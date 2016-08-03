<?php

/**
 * This is the model class for table "tds_school".
 *
 * The followings are the available columns in table 'tds_school':
 * @property integer $id
 * @property string $name
 * @property string $location
 * @property string $district
 * @property integer $gender
 * @property string $medium
 * @property string $level
 * @property string $shift
 * @property string $logo
 * @property string $cover
 * @property integer $views
 */
class School extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total = 0;
	public function tableName()
	{
		return 'tds_school';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('gender, views', 'numerical', 'integerOnly'=>true),
			array('name, location, district, medium, level, shift, logo, cover', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, location, district, gender, medium, level, shift, logo, cover, views', 'safe', 'on'=>'search'),
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
                        'schooluser' => array(self::HAS_MANY, 'SchoolUser', 'school_id'),
                );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'location' => 'Location',
			'district' => 'District',
			'gender' => 'Gender',
			'medium' => 'Medium',
			'level' => 'Level',
			'shift' => 'Shift',
			'logo' => 'Logo',
			'cover' => 'Cover',
			'views' => 'Views',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('location',$this->location,true);
		$criteria->compare('district',$this->district,true);
		$criteria->compare('gender',$this->gender);
		$criteria->compare('medium',$this->medium,true);
		$criteria->compare('level',$this->level,true);
		$criteria->compare('shift',$this->shift,true);
		$criteria->compare('logo',$this->logo,true);
		$criteria->compare('cover',$this->cover,true);
		$criteria->compare('views',$this->views);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return School the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
        public function getSchoolInfo($id,$user_id=0)
        {
            $school_join = array();
            if($user_id)
            {
                $schooluser = new SchoolUser();
                $school_user = $schooluser->userSchool($user_id);
                if(count($school_user)>0)
                {
                    foreach($school_user as $value)
                    {
                       $school_join[$value['school_id']] = $value['status'];
                    }    
                }    
            }    
            $criteria = new CDbCriteria();
            $criteria->compare("status",1);
            $criteria->compare("id",$id);
            
            $criteria->limit = 1;
            
            $value = $this->find($criteria);
            
            $school_array = array();
          
            if($value)
            {
                 
                $pageObject = new SchoolPage();
                $school_pages = $pageObject->getSchoolPages($value->id);
                if($school_pages)
                {
                    $school_array["school_pages"]       = $school_pages;
                    $school_array["is_join"] = 0;

                    if(isset($school_join[$value->id]))
                    {
                        if($school_join[$value->id] == 0)
                        {
                           $school_array["is_join"] = 1; 
                        }    
                        else
                        {
                            $school_array["is_join"] = 2; 
                        }    

                    }

                    $school_array["id"]                 = $value->id;
                    $school_array["name"]               = $value->name;
                    $school_array["division"]           = $value->district;
                    $school_array["location"]           = $value->location;
                    $school_array["views"]              = $value->views;
                    $school_array["boys"]               = $value->boys;
                    $school_array["girls"]              = $value->girls;
                    $school_array["logo"]               = "";
                    if($value->logo)
                    $school_array["logo"]               = Settings::$image_path.$value->logo;

                    $school_array["cover"]              = "";

                    if($value->cover)
                    $school_array["cover"]              = Settings::$image_path.$value->cover; 

                    $school_array["picture"]            = "";
                    if($value->picture)
                    {
                        $school_array["picture"]        = Settings::$image_path.$value->picture;
                    }
                    else
                    {
                        $school_array["picture"]        = Settings::$image_path.$value->logo;
                    }

                    $school_array["like_link"]          = Settings::$image_path."schools".str_replace(" ","-", $value->name);

                    
                }
                
            }
            
            return $school_array;
        }
        
        public function getSchoolTotal($user_id=0, $user_school = false)
        {

            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            $criteria->compare("status",1);
            if($user_school && $user_id!=0)
            {
                $criteria->together = true;
                $criteria->with = array(
                    'schooluser' => array(
                        'select' => ''
                    ),
                );
                $criteria->compare("schooluser.user_id",$user_id);
                $criteria->compare("schooluser.is_approved",1);
            }

            $criteria->group = "t.status";
            $data = $this->find($criteria);
            if($data)
            {
                return $data->total;
            }
            else
            {
                return 0;

            }
        }
        
        public function Schools($page_size = 10,$page = 1, $user_id=0, $user_school = false)
        {
            $school_join = array();
            if($user_id)
            {
                $schooluser = new SchoolUser();
                $school_user = $schooluser->userSchool($user_id);
                if(count($school_user)>0)
                {
                    foreach($school_user as $value)
                    {
                       $school_join[$value['school_id']] = $value['status'];
                    }    
                }    
            }
            
            $criteria = new CDbCriteria();
            $criteria->compare("status",1);
            if($user_school && $user_id!=0)
            {
                $criteria->together = true;
                $criteria->with = array(
                    'schooluser' => array(
                        'select' => ''
                    ),
                );
                $criteria->compare("schooluser.user_id",$user_id);
                $criteria->compare("schooluser.is_approved",1);
            }
            $start = ($page - 1) * $page_size;
            $criteria->limit = $page_size;

            $criteria->offset = $start;
            
            $criteria->order = "name asc";
            
            $schools = $this->findAll($criteria);
            
            $school_array = array();
            $i = 0;
            if($schools)
            {
                foreach($schools as $value)
                {  
                    $pageObject = new SchoolPage();
                    $school_pages = $pageObject->getSchoolPages($value->id);
                    if($school_pages)
                    {
                        $school_array[$i]["school_pages"]       = $school_pages;
                        $school_array[$i]["is_join"] = 0;
                        
                        if(isset($school_join[$value->id]))
                        {
                            if($school_join[$value->id] == 0)
                            {
                               $school_array[$i]["is_join"] = 1; 
                            }    
                            else if($school_join[$value->id] == 1)
                            {
                                $school_array[$i]["is_join"] = 2; 
                            }
                            else
                            {
                                $school_array[$i]["is_join"] = 3; 
                            }
                            
                        }
                        
                        $school_array[$i]["id"]                 = $value->id;
                        $school_array[$i]["name"]               = $value->name;
                        $school_array[$i]["division"]           = $value->district;
                        $school_array[$i]["location"]           = $value->location;
                        $school_array[$i]["views"]              = $value->views;
                        $school_array[$i]["boys"]               = $value->boys;
                        $school_array[$i]["girls"]              = $value->girls;
                        $school_array[$i]["logo"]               = "";
                        if($value->logo)
                        $school_array[$i]["logo"]               = Settings::$image_path.$value->logo;

                        $school_array[$i]["cover"]              = "";
                        
                        if($value->cover)
                        $school_array[$i]["cover"]              = Settings::$image_path.$value->cover; 
                        
                        $school_array[$i]["picture"]            = "";
                        if($value->picture)
                        {
                            $school_array[$i]["picture"]        = Settings::$image_path.$value->picture;
                        }
                        else
                        {
                            $school_array[$i]["picture"]        = Settings::$image_path.$value->logo;
                        }

                        $school_array[$i]["like_link"]          = Settings::$image_path."schools".str_replace(" ","-", $value->name);

                        $i++;
                    }
                }
            }
            
            return $school_array;
            
        }
        
        public function getSchoolPaid($paid_school_id)
        {
            $criteria = new CDbCriteria(); 
            $criteria->select = "id";
            $criteria->compare("paid_school_id",$paid_school_id);
            $schools = $this->find($criteria);
           
           
            if($schools)
            {
               return $schools->id;
            } 
            return false;
            
           
            
        }
        public function getSchoolPaidCoverLogo($paid_school_id)
        {
            $criteria = new CDbCriteria(); 
            $criteria->select = "*";
            $criteria->compare("paid_school_id",$paid_school_id);
            $schools = $this->find($criteria);
           
            $school_array["school_logo"]    = "";
            $school_array["school_picture"] = "";
            $school_array["school_cover"]   = "";
            
            if($schools)
            {
                if($schools->logo)
                $school_array["school_logo"]               = Settings::$image_path.$schools->logo;
                if($schools->cover)
                $school_array["school_cover"]              = Settings::$image_path.$schools->cover; 
                if($schools->picture)
                {
                    $school_array["school_picture"]        = Settings::$image_path.$schools->picture;
                }
                else if($schools->logo)
                {
                    $school_array["school_picture"]        = Settings::$image_path.$schools->logo;
                }
            }
            return $school_array;
            
           
            
        }
        
        
        public function getSchoolNotPaid($term="")
        {
            $criteria = new CDbCriteria(); 
            $criteria->select = "id,name";
            $criteria->addCondition ("is_paid != 1");
            if($term)
            {
                $countletter = strlen($term);
                if($countletter<4)
                {
                    $criteria->addCondition ("name like '".$term."%'");
                }
                else
                {
                    $criteria->addCondition ("name like '%".$term."%'");
                }    
            }    
            $schools = $this->findAll($criteria);
            $school_array = array();
           
            if($schools)
            {
                $i = 0; 
                foreach($schools as $value)
                { 
                    $school_array[$i]['id'] = $value->id;

                    $school_array[$i]['name'] = $value->name;
                    $i++;
                }
            } 
            return $school_array;
            
           
            
        }
               
        
        public function getSchhool($name = "",$division = "",$medium = "",$location="")
        {
            $criteria = new CDbCriteria();
            $criteria->select = "id,name,location,district,picture,boys,girls,logo,cover,views";
            $criteria->compare("status",1);
            if($name)
                $criteria->addCondition ("name like '".$name."%'");
            if($division)
                $criteria->addCondition ("district like '".$division."%'","AND");
            if($location)
                $criteria->addCondition ("location like '%".$location."%'","AND");
            if($medium)
                $criteria->addCondition ("medium like '".$medium."%'","AND");
            
            
            $schools = $this->findAll($criteria);
            
            $school_array = array();
            $i = 0;
            if($schools)
            {
                foreach($schools as $value)
                {  
                    $pageObject = new SchoolPage();
                    $school_pages = $pageObject->getSchoolPages($value->id);
                    if($school_pages)
                    {
                        $school_array[$i]["school_pages"]       = $school_pages;
                        $school_array[$i]["id"]                 = $value->id;
                        $school_array[$i]["name"]               = $value->name;
                        $school_array[$i]["division"]           = $value->district;
                        $school_array[$i]["location"]           = $value->location;
                        $school_array[$i]["views"]              = $value->views;
                        $school_array[$i]["boys"]               = $value->boys;
                        $school_array[$i]["girls"]              = $value->girls;
                        $school_array[$i]["logo"]               = "";
                        if($value->logo)
                        $school_array[$i]["logo"]               = Settings::$image_path.$value->logo;

                        $school_array[$i]["cover"]              = "";
                        
                        if($value->cover)
                        $school_array[$i]["cover"]              = Settings::$image_path.$value->cover; 
                        
                        $school_array[$i]["picture"]            = "";
                        if($value->picture)
                        {
                            $school_array[$i]["picture"]        = Settings::$image_path.$value->picture;
                        }
                        else
                        {
                            $school_array[$i]["picture"]        = Settings::$image_path.$value->logo;
                        }

                        $school_array[$i]["like_link"]          = Settings::$image_path."schools".str_replace(" ","-", $value->name);

                        $i++;
                    }
                }
            }
            
            return $school_array;
            
        }
        
    public function getFreeSchoolByPaidId($paid_id, $ar_fields = array()){
        
        $select = '*';
        if(!empty($ar_fields)) {
            $select = implode(',', $ar_fields);
        }
        
        $criteria = new CDbCriteria();
        $criteria->select = $select;
        $criteria->compare('t.paid_school_id', $paid_id);
        
        $data_obj = $this->find($criteria);
        
        return (!empty($data_obj)) ? $this->foramtFreeSchoolData($data_obj) : false;
    }
    
    public function foramtFreeSchoolData($obj){
        
        return $obj->attributes;
    }
}
