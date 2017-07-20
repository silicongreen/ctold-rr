<?php

/**
 * This is the model class for table "tds_tags".
 *
 * The followings are the available columns in table 'tds_tags':
 * @property integer $id
 * @property string $tags_name
 * @property string $hit_count
 *
 * The followings are the available model relations:
 * @property PostTags[] $postTags
 */
class Postcomments extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
        public $total = 0;
	public function tableName()
	{
		return 'tds_post_comments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id,post_id,details', 'required'),
			array('user_id,post_id,title,details,created_date,show_comment', 'safe', 'on'=>'search'),
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
                    'post' => array(self::BELONGS_TO, 'Post', 'post_id'),
                    'user' => array(self::BELONGS_TO, 'Freeusers', 'user_id')
                 );
	}

	
	
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Tag the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        public function getCommentsTotal($post_id,$user_post=false)
        {

            $criteria = new CDbCriteria();
            $criteria->select = 'count(t.id) as total';
            $criteria->compare('post_id', $post_id);
            if($user_post==false)
            {
                $criteria->compare('show_comment', 1);
            }  
            $criteria->with = array(
                        'post' => array(
                            'select' => '',
                            'joinType' => "INNER JOIN"
                        )
            );
            $criteria->group = "t.post_id";
            $data = $this->find($criteria);
            if ($data)
            {
                return $data->total;
            }
            else
            {
                return "0";
            }
        }
        public function getCommentsPost($post_id,$page = 1, $page_size = 10,$user_post=false)
        {
            $criteria = new CDbCriteria;
            $criteria->select = 't.id,t.title,t.details,t.created_date';
            $criteria->order = "t.created_date DESC";
            $criteria->compare('post_id', $post_id);
            if($user_post==false)
            {
                $criteria->compare('show_comment', 1);
            }  
            $criteria->with = array(
                        'post' => array(
                            'select' => '',
                            'joinType' => "INNER JOIN"
                        ),
                        'user' => array(
                            'select' => 'user.first_name,user.middle_name,user.last_name.user.email',
                            'joinType' => "INNER JOIN"
                        )
            );
            
            $start = ($page - 1) * $page_size;
            $criteria->limit = $page_size;

            $criteria->offset = $start;
            $obj_comments_post = $this->findAll($criteria);
            $comments_post = array();
            $i = 0;
            if($obj_comments_post)
            {
                $i = 0;
                foreach($obj_comments_post as $value)
                {
                    $comments_post[$i]['id'] = $value->id;
                    $comments_post[$i]['title'] = $value->title;
                    $comments_post[$i]['details'] = $value->details;
                    $comments_post[$i]['created_date'] = $value->created_date;
                    
                    $username = ($value['user']->first_name)?$value['user']->first_name." ":"";
                    $username.= ($value['user']->middle_name)?$value['user']->middle_name." ":"";
                    $username.= ($value['user']->last_name)?$value['user']->last_name:"";
                    if($username == "")
                    {
                       $username =  $value['user']->email;
                    }    
                    $comments_post[$i]['username'] = $username;
                    $i++;
                } 
            }
            return $comments_post;
            
        }
        
        
        
}
