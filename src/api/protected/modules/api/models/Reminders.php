<?php

/**
 * This is the model class for table "reminders".
 *
 * The followings are the available columns in table 'reminders':
 * @property integer $id
 * @property integer $sender
 * @property integer $recipient
 * @property string $subject
 * @property string $body
 * @property integer $is_read
 * @property integer $is_deleted_by_sender
 * @property integer $is_deleted_by_recipient
 * @property string $created_at
 * @property string $updated_at
 * @property integer $school_id
 */
class Reminders extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total;
	public function tableName()
	{
		return 'reminders';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sender, recipient, is_read, is_deleted_by_sender, is_deleted_by_recipient, school_id', 'numerical', 'integerOnly'=>true),
			array('subject', 'length', 'max'=>255),
			array('body, created_at, updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, sender, recipient, subject, body, is_read, is_deleted_by_sender, is_deleted_by_recipient, created_at, updated_at, school_id', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sender' => 'Sender',
			'recipient' => 'Recipient',
			'subject' => 'Subject',
			'body' => 'Body',
			'is_read' => 'Is Read',
			'is_deleted_by_sender' => 'Is Deleted By Sender',
			'is_deleted_by_recipient' => 'Is Deleted By Recipient',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('sender',$this->sender);
		$criteria->compare('recipient',$this->recipient);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('body',$this->body,true);
		$criteria->compare('is_read',$this->is_read);
		$criteria->compare('is_deleted_by_sender',$this->is_deleted_by_sender);
		$criteria->compare('is_deleted_by_recipient',$this->is_deleted_by_recipient);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updated_at',$this->updated_at,true);
		$criteria->compare('school_id',$this->school_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Reminders the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getImportantReminder()
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.*';
            $criteria->addCondition("(rtype=20 OR rtype=21)");
            $criteria->compare('recipient', Yii::app()->user->id);
            $criteria->compare('is_deleted_by_sender', 0);
            $criteria->compare('is_deleted_by_recipient', 0);
            $criteria->compare('is_read', 0);
            
            $criteria->order = "created_at DESC";
            $criteria->limit = 1;
            $obj_reminder = $this->find($criteria);
            if($obj_reminder)
            {
                return $obj_reminder;
            }
            return false;
        }        
        public function getReminder($rid,$rtype=6)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id';
            $criteria->compare('rid', $rid);
            $criteria->compare('rtype', $rtype);
            $criteria->compare('is_deleted_by_sender', 0);
            $criteria->compare('is_deleted_by_recipient', 0);
            
            $criteria->order = "created_at DESC";
            $obj_reminder = $this->findAll($criteria);
            return $obj_reminder;
            
        } 
        public function getReminderTotalUnread($user_id)
        {
            
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            $criteria->compare('recipient', $user_id);
            $criteria->compare('is_read', 0);
            $criteria->compare('is_deleted_by_sender', 0);
            $criteria->compare('is_deleted_by_recipient', 0);
            $data = $this->find($criteria);
            return $data->total;
        } 
        public function getReminderTotal($user_id)
        {
            
            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            $criteria->compare('recipient', $user_id);
            $criteria->compare('is_deleted_by_sender', 0);
            $criteria->compare('is_deleted_by_recipient', 0);
            $data = $this->find($criteria);
            return $data->total;
        } 
        public function getUserReminderNew($user_id,$page_number,$page_size,$created_at="",$rtype="")
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id,t.subject,t.body,t.rtype,t.rid,t.is_read,t.batch_id,t.student_id,t.created_at';
            $criteria->compare('recipient', $user_id);
            $criteria->compare('is_deleted_by_sender', 0);
            $criteria->compare('is_deleted_by_recipient', 0);
            if($created_at && $rtype)
            {
               $criteria->compare('DATE(created_at)', $created_at); 
               $criteria->compare('rtype', $rtype);
            }
            $criteria->order = "created_at DESC";
            $start = ($page_number-1)*$page_size;
            $criteria->limit = $page_size;
            $criteria->offset = $start;
            $obj_reminder = $this->findAll($criteria);
            $reminder = array();
            
            if($obj_reminder)
            {
                $i = 0;
                foreach($obj_reminder as $value)
                {
                   $reminder[$i]['id'] = $value->id;
                   $reminder[$i]['subject'] = "";
                   if($value->subject)
                   {
                        $reminder[$i]['subject'] = trim($value->subject);
                   }
                   $reminder[$i]['is_read'] = $value->is_read;
                   
                   $reminder[$i]['batch_id'] = $value->batch_id;
                   $reminder[$i]['student_id'] = $value->student_id;
                   $reminder[$i]['created_at'] = $value->created_at;
                   
                   $reminder[$i]['body'] = "";
                   if($value->body)
                   {
                        $reminder[$i]['body'] = trim($value->body);
                   }
                   $reminder[$i]['rtype'] = $value->rtype;
                   $reminder[$i]['rid'] = $value->rid;
                   $i++;
                }
               
            }    

            return $reminder;
        } 
        public function FindUnreadData($rtype,$rids=array())
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.rid';
            $criteria->compare('is_read', 0);
            $criteria->compare('recipient', Yii::app()->user->id);  
            $criteria->compare('rtype', $rtype);
            $criteria->compare('rid', $rids);
            $obj_reminder = $this->findAll($criteria);
            $reminder_data = array();
            if($obj_reminder)
            {
                foreach($obj_reminder as $value)
                {
                   $reminder_data[] = $value->rid;
                }
               
            }
            return $reminder_data;
            
            
        }        
        public function ReadReminderNew($user_id,$id=0,$rtype=0,$rid=0)
        {
            if(Yii::app()->user->isParent && $id)
            {
                $reminder = new Reminders();
                $r_data = $reminder->findByPk($id);
                if($r_data)
                {
                    $criteria = new CDbCriteria;
                    $criteria->select = 't.id';
                    $criteria->compare('is_read', 0);
                    $criteria->compare('is_deleted_by_sender', 0);
                    $criteria->compare('is_deleted_by_recipient', 0);
                    $criteria->compare('recipient', $user_id);
                    $criteria->compare('rtype',$r_data->rtype);
                    $criteria->compare('rtype',$r_data->rid);
                }
                else 
                {
                    $criteria = new CDbCriteria;
                    $criteria->select = 't.id';
                    $criteria->compare('is_read', 0);
                    $criteria->compare('is_deleted_by_sender', 0);
                    $criteria->compare('is_deleted_by_recipient', 0);
                    $criteria->compare('recipient', $user_id);
                    if($id)
                    {
                        $criteria->compare('id', $id);
                    }
                    else if($rtype)
                    {
                        $criteria->compare('rtype', $rtype);
                        if($rid)
                        {
                            $criteria->compare('rid', $rid);
                        }
                    }  
                }
                
            }
            else
            {    
                $criteria = new CDbCriteria;
                $criteria->select = 't.id';
                $criteria->compare('is_read', 0);
                $criteria->compare('is_deleted_by_sender', 0);
                $criteria->compare('is_deleted_by_recipient', 0);
                $criteria->compare('recipient', $user_id);
                if($id)
                {
                    $criteria->compare('id', $id);
                }
                else if($rtype)
                {
                    $criteria->compare('rtype', $rtype);
                    if($rid)
                    {
                        $criteria->compare('rid', $rid);
                    }
                }
            }
            $obj_reminder = $this->findAll($criteria);
            if($obj_reminder)
            {
                foreach($obj_reminder as $value)
                {
                   $robject = new Reminders();
                   $robjchange = $robject->findByPk($value->id);
                   $robjchange->is_read = 1;
                   $robjchange->save();
                }
               
            }
            
        }
}
