<?php

/**
 * This is the model class for table "tds_user_good_read".
 *
 * The followings are the available columns in table 'tds_user_good_read':
 * @property integer $id
 * @property integer $folder_id
 * @property integer $post_id
 * @property integer $is_read
 * @property integer $user_id
 */
class UserGoodRead extends CActiveRecord
{

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tds_user_good_read';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('folder_id, post_id, user_id', 'required'),
            array('folder_id, post_id, is_read, user_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, folder_id, post_id, is_read, user_id', 'safe', 'on' => 'search'),
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
            'folder' => array(self::BELONGS_TO, 'UserFolder', 'folder_id'),
            'post' => array(self::BELONGS_TO, 'Post', 'post_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'folder_id' => 'Folder',
            'post_id' => 'Post',
            'is_read' => 'Is Read',
            'user_id' => 'User',
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

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('folder_id', $this->folder_id);
        $criteria->compare('post_id', $this->post_id);
        $criteria->compare('is_read', $this->is_read);
        $criteria->compare('user_id', $this->user_id);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return UserGoodRead the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getGoodRead($folder_id, $post_id, $user_id =0)
    {
        if(!$folder_id)
        {
            $folderObj = new UserFolder();
            $folder = $folderObj->getFolder("unread", $user_id);
            $folder_id = $folder->id;
        }
        $criteria = new CDbCriteria;
        $criteria->select = 't.id';
        //$criteria->compare("folder_id !", $folder_id);
        $criteria->compare("folder_id", $folder_id);
        $criteria->compare("post_id", $post_id);
        $criteria->limit = 1;
        $obj_goodread = $this->find($criteria);
        return $obj_goodread;
    }
    public function getGoodReadUser($post_id,$user_id)
    {
        $folderObj = new UserFolder();
        $folder = $folderObj->getFolder("unread", $user_id);
        $criteria = new CDbCriteria;
        $criteria->select = 't.folder_id';
        $criteria->compare("user_id", $user_id);
        $criteria->compare("post_id", $post_id);
        $criteria->compare("folder_id !", $folder->id);
        $criteria->limit = 1;
        $obj_goodread = $this->find($criteria);
        return $obj_goodread;
    }

    public function removeGoodRead($post_id,$user_id,$folder = "unread",$folder_id="")
    {
        if(!$folder_id)
        {
            $folderObj = new UserFolder();
            $folder = $folderObj->getFolder($folder, $user_id);
            $folder_id = $folder->id;
        }    
        
        
        if ($folder_id)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id';
            $criteria->compare("folder_id", $folder_id);
            $criteria->compare("post_id", $post_id);
            $criteria->limit = 1;
            $obj_goodread = $this->find($criteria);
            if ($obj_goodread)
            {
                $obj_goodread->deleteByPk($obj_goodread->id);
            }
        }
    }

    

    

}
