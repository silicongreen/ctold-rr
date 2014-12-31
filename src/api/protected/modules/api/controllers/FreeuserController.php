<?php

class FreeuserController extends Controller
{

    /**
     * @return array action filters
    */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('index', 'create', 'getcategorypost', 'getsinglenews', 'search', "getkeywordpost"
                    , "gettagpost", "getbylinepost", "getmenu","getassesment","addmark",
                    "getuserinfo", "goodread", "readlater", "goodreadall", "goodreadfolder", "removegoodread"
                    , "schoolsearch", "school", "createschool", "schoolpage", "schoolactivity", "candle"
                    , "garbagecollector","getschoolteacherbylinepost","createcachesinglenews","addwow", 
                    'set_preference','addcomments','getcomments', 'get_preference','addgcm','getallgcm','getschoolinfo','joinschool','candleschool','leaveschool'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionAddMark()
    {
        $assessment_id = Yii::app()->request->getPost('assessment_id');
        $user_id = Yii::app()->request->getPost('user_id');
        $mark = Yii::app()->request->getPost('mark');
        if (!$assessment_id || !$mark )
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            
            $assesmentObj = new Cassignments();
            
            $objassessment = $assesmentObj->findByPk($assessment_id);
            $objassessment->played = $objassessment->played+1;
            $objassessment->save();
            if($user_id)
            {
                $objcmark = new Cmark();
                $objassessment = $objcmark->getUserMarkAssessment($user_id,$assessment_id);
                $add = false;
                if($objassessment)
                {
                    if($objassessment->mark<$mark)
                    {
                        $marksobj = $objcmark->findByPk($objassessment->id);
                        $marksobj->delete();
                        $add = true;
                    }
                }
                else
                {
                    $add = true;
                }  
                if($add)
                {
                    $objcmark->mark = $mark;
                    $objcmark->user_id = $user_id;
                    $objcmark->assessment_id = $assessment_id;
                    $objcmark->save();
                    
                }
            }    
            
            
            $response['status']['code'] = 200;
            $response['status']['msg'] = "success";
                
               
        } 
        echo CJSON::encode($response);
        Yii::app()->end();

    }
    public function actionGetAssesment()
    {
        $assesment_id = Yii::app()->request->getPost('assesment_id');
        if (!$assesment_id )
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            
            $assesmentObj = new Cassignments();
            $response['data']['assesment'] = $assesmentObj->getAssessment($assesment_id);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "success";
                
               
        } 
        echo CJSON::encode($response);
        Yii::app()->end();

    }
    public function actionAddWow()
    {
        $post_id = Yii::app()->request->getPost('post_id');
        $user_id = Yii::app()->request->getPost('user_id');
        if (!$post_id || (!$user_id && Settings::$wow_login==true) )
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $objwow = new Wow();
            if(Settings::$wow_login==false || !$objwow->wowexists($post_id, $user_id))
            {
                if(Settings::$wow_login==true)
                {
                    $objwow->post_id = $post_id;
                    $objwow->user_id = $user_id;
                    $objwow->save();
                }
                
                $postModel = new Post();
                $postobj = $postModel->findByPk($post_id);
                $postobj->wow_count = $postobj->wow_count+1;
                $postobj->save();
                
                $cache_name = "YII-SINGLE-POST-CACHE-".$post_id;
                $cache_data = Yii::app()->cache->get($cache_name);
                if ($cache_data !== false)
                {  
                    $cache_data['wow_count'] = $cache_data['wow_count']+1;
                    $singlepost = $cache_data;
                    Yii::app()->cache->set($cache_name, $singlepost, 5184000);
                }
               
                
                $response['data']['wow_count'] = $postobj->wow_count;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "success";
                
            } 
            else
            {
                $postModel = new Post();
                $postobj = $postModel->findByPk($post_id);
                $response['data']['wow_count'] = $postobj->wow_count;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "success";
            }    
        } 
        echo CJSON::encode($response);
        Yii::app()->end();
    }        
    public function actionLeaveSchool()
    {
        $school_id = Yii::app()->request->getPost('school_id');
        $user_id = Yii::app()->request->getPost('user_id');
        if (!$school_id || !$user_id )
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $schooluser = new SchoolUser();
            $userschool = $schooluser->userSchoolSingle($user_id, $school_id);
            if($userschool)
            {
                $schooluser->deleteByPk($userschool->id);
            }
            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);

            $response['data']['userinfo'] = $user_info;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "success";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }        
    public function actionJoinSchool()
    {
        $school_id = Yii::app()->request->getPost('school_id');
        $user_id = Yii::app()->request->getPost('user_id');
        $type = Yii::app()->request->getPost('type');
        $information = Yii::app()->request->getPost('information');
        $grade = Yii::app()->request->getPost('grade');
        
        if (!$school_id || !$user_id || !$type || !$grade || !$information || !isset(Settings::$school_join_approved[$type]))
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {

            $is_approved = 0;
            
            if(Settings::$school_join_approved[$type]===false)
            {
                $is_approved = 1;
            }    
            $school_join = array();
            
            $schooluser = new SchoolUser();
            $school_user = $schooluser->userSchool($user_id,$school_id);
            if(count($school_user)>0)
            {
                foreach($school_user as $value)
                {
                   $school_join[$value['school_id']] = $value['status'];
                }    
            } 
            
            if(isset($school_join[$school_id]))
            {
                //do nothing
            }
            else
            {
               $schooluser->user_id = $user_id;  
               $schooluser->school_id = $school_id;
               $schooluser->is_approved = $is_approved;
               $schooluser->type = $type;
               $schooluser->grade = $grade;
               $schooluser->information = $information;
               $schooluser->save();
            } 
            
            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);
             
            
            

            $response['data']['userinfo'] = $user_info;
        

            $response['status']['code'] = 200;
            $response['status']['msg'] = "SUCCESSFULLY-SAVED";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    public function actionGetAllGcm()
    {
        $cache_name = "YII-RESPONSE-GCM";
        $request_llicence = Yii::app()->request->getPost('request_llicence');
        if(Settings::$api_llicence_key == $request_llicence)
        {
            $response = Yii::app()->cache->get($cache_name);
            if ($response === false)
            {

                $gcmobj   = new Gcm();
                $response = $gcmobj->getAllGcm();         
                Yii::app()->cache->set($cache_name, $response);
            }
            echo CJSON::encode($response);
            Yii::app()->end();
        }

    }        
    public function actionAddGcm()
    {
        $gcm_id = Yii::app()->request->getPost('gcm_id');
        $device_id = Yii::app()->request->getPost('device_id');
        if($gcm_id)
        {
            $gcmobj = new Gcm();

            $gcm_added = $gcmobj->getGcm($gcm_id);

            if(!$gcm_added)
            {
                $gcmobj->gcm_id = $gcm_id;
                $gcmobj->device_id = $device_id;
                $gcmobj->save();  
                $cache_name = "YII-RESPONSE-GCM";
                Yii::app()->cache->delete($cache_name);
            } 
            $response['data']['id'] = $gcm_id;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "SUCCESFULLY_SAVED";
        }
        else
        {
            $response['data']['id']     = 0;
            $response['status']['code'] = 400;
            $response['status']['msg']  = "Bad Request";
        }    
        echo CJSON::encode($response);
        Yii::app()->end();
    }        

   

    public function actionCreateSchool()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $school_name = Yii::app()->request->getPost('school_name');
        $contact = Yii::app()->request->getPost('contact');
        $about = Yii::app()->request->getPost('about');
        $address = Yii::app()->request->getPost('address');
        $zip_code = Yii::app()->request->getPost('zip_code');
        if (!$user_id || !$school_name || !$contact || !$address || !$about)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $userCretedSchool = new UserCreatedSchool();
            $userCretedSchool->freeuser_id = $user_id;
            $userCretedSchool->school_name = $school_name;
            $userCretedSchool->contact = $contact;
            $userCretedSchool->about = $about;
            $userCretedSchool->address = $address;
            $userCretedSchool->zip_code = $zip_code;
            if (!empty($_FILES['picture']['name']))
            {
                $main_dir = 'upload/user_submitted_image/';
                $uploads_dir = Settings::$main_path . 'upload/user_submitted_image/';
                $tmp_name = $_FILES["picture"]["tmp_name"];
                $name = "school_picture_" . time() . "_" . str_replace(" ", "-", $_FILES["picture"]["name"]);

                move_uploaded_file($tmp_name, "$uploads_dir/$name");
                $userCretedSchool->picture = $main_dir . $name;
            }
            if (!empty($_FILES['logo']['name']))
            {
                $main_dir = 'upload/user_submitted_image/';
                $uploads_dir = Settings::$main_path . 'upload/user_submitted_image/';
                $tmp_name = $_FILES["logo"]["tmp_name"];
                $name = "school_logo_" . time() . "_" . str_replace(" ", "-", $_FILES["logo"]["name"]);

                move_uploaded_file($tmp_name, "$uploads_dir/$name");
                $userCretedSchool->logo = $main_dir . $name;
            }

            $userCretedSchool->save();

            $response['status']['code'] = 200;
            $response['status']['msg'] = "Successfully Saved";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    public function actionCandleSchool()
    {
        $username = Yii::app()->request->getPost('username');
        $headline = Yii::app()->request->getPost('headline');
        $content = Yii::app()->request->getPost('content');
        $category_id = Settings::$school_category_id;
        $show_comment_to_all = Yii::app()->request->getPost('show_comment_to_all');
        $can_comment = Yii::app()->request->getPost('can_comment');
        $school_id = Yii::app()->request->getPost('school_id');
        $user_id = Yii::app()->request->getPost('user_id');
        $candle_type = Yii::app()->request->getPost('candle_type');
        
        if (!$username || !$headline || !$content || !$category_id || !$school_id || !$user_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $user_school = new SchoolUser();
            $userschool = $user_school->userSchoolSingle($user_id, $school_id);
            if(isset($userschool->is_approved) && $userschool->is_approved==1)
            {
            
                $postobj = new Post();
                $postobj->headline = $headline;
                $postobj->content = $content;
                $postobj->published_date = date("Y-m-d H:i:s");
                $postobj->status = 1;
                if(Settings::$school_candle_publish[$userschool->type]===true)
                {
                    $postobj->status = 5;
                }
                if($show_comment_to_all)
                {
                    $postobj->show_comment_to_all = $show_comment_to_all;
                } 
                
                if($can_comment)
                {
                    $postobj->can_comment = $can_comment;
                }
                
                $postobj->user_id = $user_id;
                
                $postobj->type = "Print";
                $postobj->user_type = 2;
                $postobj->language = "en";
                $postobj->school_id = $school_id;
                
                $objbyline = new Bylines();
                $postobj->byline_id = $objbyline->generate_byline_id($username);

                if (!empty($_FILES['leadimage']['name']))
                {
                    $main_dir = 'upload/user_submitted_image/';
                    $uploads_dir = Settings::$main_path . 'upload/user_submitted_image/';
                    $tmp_name = $_FILES["leadimage"]["tmp_name"];
                    $name = "image_" . time() . "_" . str_replace(" ", "-", $_FILES["leadimage"]["name"]);

                    move_uploaded_file($tmp_name, "$uploads_dir/$name");
                    $postobj->lead_material = $main_dir . $name;
                }
                $postobj->save();

                if (!empty($_FILES['attach_file']['name']))
                {
                    $main_dir = 'upload/user_submitted_image/';
                    $uploads_dir = Settings::$main_path . 'upload/user_submitted_image/';
                    $tmp_name = $_FILES["attach_file"]["tmp_name"];
                    $name = "file_" . time() . "_" . str_replace(" ", "-", $_FILES["attach_file"]["name"]);

                    move_uploaded_file($tmp_name, "$uploads_dir/$name");

                    $postAttachmentObj = new PostAttachment();

                    $postAttachmentObj->file_name = $main_dir . $name;
                    $postAttachmentObj->post_id = $postobj->id;
                    $postAttachmentObj->show = 1;
                    $postAttachmentObj->save();
                }

                $objpostcategory = new PostCategory();

                $objpostcategory->post_id = $postobj->id;
                $objpostcategory->category_id = $category_id;

                $objpostcategory->save();
                
                

                for($i = 1; $i<=Settings::$allclass; $i++)
                {
                    $objpostclass = new PostClass();
                    $objpostclass->post_id = $postobj->id;
                    $objpostclass->class_id = $i;
                    $objpostclass->save();
                }


                if (Yii::app()->request->getPost('type'))
                {
                    foreach (Yii::app()->request->getPost('type') as $value)
                    {
                        $objposttype = new PostType();
                        $objposttype->post_id = $postobj->id;
                        $objposttype->type_id = $value;
                        $objposttype->save();
                    }
                }

                $response['status']['code'] = 200;
                $response['status']['msg'] = "Successfully Saved";
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            }    
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    

    public function actionCandle()
    {
        $username = Yii::app()->request->getPost('username');
        $headline = Yii::app()->request->getPost('headline');
        $content = Yii::app()->request->getPost('content');
        $category_id = Yii::app()->request->getPost('category_id');
        if (!$username || !$headline || !$content || !$category_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $postobj = new Post();
            $postobj->headline = $headline;
            $postobj->content = $content;
            $postobj->published_date = date("Y-m-d H:i:s");
            $postobj->status = 1;
            $postobj->type = "Print";
            $postobj->user_type = 2;
            $postobj->language = "en";
            $objbyline = new Bylines();
            $postobj->byline_id = $objbyline->generate_byline_id($username);

            if (!empty($_FILES['leadimage']['name']))
            {
                $main_dir = 'upload/user_submitted_image/';
                $uploads_dir = Settings::$main_path . 'upload/user_submitted_image/';
                $tmp_name = $_FILES["leadimage"]["tmp_name"];
                $name = "image_" . time() . "_" . str_replace(" ", "-", $_FILES["leadimage"]["name"]);

                move_uploaded_file($tmp_name, "$uploads_dir/$name");
                $postobj->lead_material = $main_dir . $name;
            }
            $postobj->save();

            if (!empty($_FILES['attach_file']['name']))
            {
                $main_dir = 'upload/user_submitted_image/';
                $uploads_dir = Settings::$main_path . 'upload/user_submitted_image/';
                $tmp_name = $_FILES["attach_file"]["tmp_name"];
                $name = "file_" . time() . "_" . str_replace(" ", "-", $_FILES["attach_file"]["name"]);

                move_uploaded_file($tmp_name, "$uploads_dir/$name");

                $postAttachmentObj = new PostAttachment();

                $postAttachmentObj->file_name = $main_dir . $name;
                $postAttachmentObj->post_id = $postobj->id;
                $postAttachmentObj->show = 1;
                $postAttachmentObj->save();
            }

            $objpostcategory = new PostCategory();

            $objpostcategory->post_id = $postobj->id;
            $objpostcategory->category_id = $category_id;

            $objpostcategory->save();

            for($i = 1; $i<=Settings::$allclass; $i++)
            {
                $objpostclass = new PostClass();
                $objpostclass->post_id = $postobj->id;
                $objpostclass->class_id = $i;
                $objpostclass->save();
            }

            if (Yii::app()->request->getPost('type'))
            {
                foreach (Yii::app()->request->getPost('type') as $value)
                {
                    $objposttype = new PostType();
                    $objposttype->post_id = $postobj->id;
                    $objposttype->type_id = $value;
                    $objposttype->save();
                }
            }

            $response['status']['code'] = 200;
            $response['status']['msg'] = "Successfully Saved";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSchoolActivity()
    {
        $school_id = Yii::app()->request->getPost('school_id');
        $page_size = Yii::app()->request->getPost('page_size');
        $page_number = Yii::app()->request->getPost('page_number');
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 10;
        }
        if (!$school_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {

            $schoolActivity = new SchoolActivities();

            $response['data']['total'] = $schoolActivity->getActivityTotal($school_id);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }
            $response['data']['has_next'] = $has_next;

            $response['data']['activity'] = $schoolActivity->getActivity($school_id, $page_size, $page_number);

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSchoolPage()
    {
        $school_id = Yii::app()->request->getPost('school_id');
        $page_id = Yii::app()->request->getPost('page_id');
        $menu_id = Yii::app()->request->getPost('menu_id');



        if (!$school_id || !$page_id || !$menu_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $school_page = new SchoolPage();

            $response['data']['page_details'] = $school_page->pageDetails($page_id);
            $response['data']['activity'] = array();
            if ($menu_id == 1)
            {
                $schoolActivity = new SchoolActivities();
                $response['data']['activity'] = $schoolActivity->getActivity($school_id);
            }

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    public function actionGetSchoolInfo()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $school_id = Yii::app()->request->getPost('school_id');
        if(!$user_id)
        {
            $user_id = 0;
        }
        if(!$school_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $schoolobj = new School();
            $response['data']['schools'] = $schoolobj->getSchoolInfo($school_id, $user_id);

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }    
        echo CJSON::encode($response);
        Yii::app()->end(); 
    }

    public function actionSchool()
    {

        $page_size = Yii::app()->request->getPost('page_size');
        $page_number = Yii::app()->request->getPost('page_number');
        $user_id = Yii::app()->request->getPost('user_id');
        $myschool = Yii::app()->request->getPost('myschool');
        $userschool = false;
        if($myschool==1)
        {
            $userschool = true;
            
        }    
        if(!$user_id)
        {
            $user_id = 0;
        }
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 10;
        }


        $schoolobj = new School();

        if($user_id && $userschool)
        {
           $response['data']['total'] = $schoolobj->getSchoolTotal($user_id,$userschool); 
        }
        else
        {    
            $response['data']['total'] = $schoolobj->getSchoolTotal();
        }
        $has_next = false;
        if ($response['data']['total'] > $page_number * $page_size)
        {
            $has_next = true;
        }
        $response['data']['has_next'] = $has_next;

        $response['data']['schools'] = $schoolobj->Schools($page_size, $page_number, $user_id, $userschool);

        $response['status']['code'] = 200;
        $response['status']['msg'] = "DATA_FOUND";

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSchoolSearch()
    {
        $name = Yii::app()->request->getPost('name');
        $division = Yii::app()->request->getPost('division');
        $medium = Yii::app()->request->getPost('medium');
        $location = Yii::app()->request->getPost('location');
        if (!$name && !$division && !$location && !$medium)
        {
            $response['data']['schools'] = array();
            $response['status']['code'] = 200;
            $response['status']['msg'] = "NOT_FOUND";
        }
        else
        {
            $schoolobj = new School();

            $response['data']['schools'] = $schoolobj->getSchhool($name, $division, $medium, $location);

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetMenu()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $categoryObj = new Categories();
        if (!$user_id)
            $user_id = 0;

        $response['data']['menu'] = $categoryObj->getParentCategory($user_id);
        $response['status']['code'] = 200;
        $response['status']['msg'] = "DATA_FOUND";

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionRemoveGoodRead()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $post_id = Yii::app()->request->getPost('post_id');
        $folder_name = Yii::app()->request->getPost('folder_name');
        $folder_id = Yii::app()->request->getPost('folder_id');
        
        if (!$user_id || (!$folder_name && !$folder_id)  || !$post_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $obj_good_read = new UserGoodRead();
            $obj_good_read->removeGoodRead($post_id, $user_id, $folder_name, $folder_id);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGoodReadFolder()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $folderObj = new UserFolder();
        if (!$user_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $response['data']['folder'] = $folderObj->getAllFolder($user_id);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGoodReadAll()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $folderObj = new UserFolder();
        if (!$user_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $response['data']['post'] = $folderObj->getPost($user_id);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        if(isset($response['data']['post']) && count($response['data']['post'])>0)
        {
           
            $post_data = array();
            $j = 0;
            foreach($response['data']['post'] as $value)
            {
                $i = 0;
                foreach($value['post'] as $postvalue)
                {
                   $response['data']['post'][$j]['post'][$i] = $this->getSingleNewsFromCache($postvalue['id']); 
                   $i++;
                }   
                $j++;
            } 
            
        } 
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGoodRead()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $post_id = Yii::app()->request->getPost('post_id');
        $folder_id = Yii::app()->request->getPost('folder_id');

        $folderObj = new UserFolder();
        $goodreadObj = new UserGoodRead();




        if (!$folder_id && Yii::app()->request->getPost('folder_name'))
        {
            if (!$folder = $folderObj->getFolder(Yii::app()->request->getPost('folder_name'), $user_id))
            {
                if (!$folderObj->getFolder("unread", $user_id))
                {
                    $folderObj->title = "unread";
                    $folderObj->user_id = $user_id;
                    $folderObj->visible = 0;
                    $folderObj->save();
                }
                $folderObj->title = Yii::app()->request->getPost('folder_name');
                $folderObj->user_id = $user_id;
                $folderObj->save();
                $folder_id = $folderObj->id;
            }
            else
            {
                $folder_id = $folder->id;
            }
        }
        if (!$folder_id || !$user_id || !$post_id || Yii::app()->request->getPost('folder_name') == "unread" || Yii::app()->request->getPost('folder_name') == "Unread")
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        if ($folder_id && !$goodreadObj->getGoodRead($folder_id, $post_id))
        {
            $goodreadObj->folder_id = $folder_id;
            $goodreadObj->user_id = $user_id;
            $goodreadObj->post_id = $post_id;
            $goodreadObj->save();
            
            $response['data']['folder_id'] = $folder_id;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        else if($folder_id)
        {
            $response['data']['folder_id'] = $folder_id;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }


        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionReadlater()
    {
        $user_id = Yii::app()->request->getPost('user_id');
        $post_id = Yii::app()->request->getPost('post_id');

        if (!$user_id || !$post_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {
            $folderObj = new UserFolder();
            $goodreadObj = new UserGoodRead();

            if (!$folder = $folderObj->getFolder("unread", $user_id))
            {
                $folderObj->title = "unread";
                $folderObj->user_id = $user_id;
                $folderObj->visible = 0;
                $folderObj->save();

                $folder_id = $folderObj->id;
            }
            else
            {
                $folder_id = $folder->id;
            }

            if (!$goodreadObj->getGoodRead($folder_id, $post_id))
            {
                $goodreadObj->folder_id = $folder_id;
                $goodreadObj->user_id = $user_id;
                $goodreadObj->post_id = $post_id;
                $goodreadObj->save();
            }

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetBylinePost()
    {
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        $id = Yii::app()->request->getPost('id');
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 10;
        }

        $user_id = Yii::app()->request->getPost('user_id');
        if (!$user_id)
        {
            $user_type = 1;
        }
        else
        {
            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);
            $user_type = $user_info['user_type'];
        }


        if ($id)
        {
            $postbylineObj = new Post();
            $post = $postbylineObj->getPosts($id, $user_type, $page_number, $page_size);

            $response['data']['total'] = $postbylineObj->getPostTotal($id, $user_type);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }
            $response['data']['has_next'] = $has_next;

            $response['data']['post'] = $post;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetKeywordPost()
    {
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        $id = Yii::app()->request->getPost('id');
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 10;
        }

        $user_id = Yii::app()->request->getPost('user_id');
        if (!$user_id)
        {
            $user_type = 1;
        }
        else
        {
            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);
            $user_type = $user_info['user_type'];
        }

        if ($id)
        {
            $postKeywordObj = new PostKeyword();
            $post = $postKeywordObj->getPost($id, $user_type, $page_number, $page_size);

            $response['data']['total'] = $postKeywordObj->getPostTotal($id, $user_type);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }
            $response['data']['has_next'] = $has_next;

            $response['data']['post'] = $post;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetTagPost()
    {
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        $id = Yii::app()->request->getPost('id');
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 10;
        }

        $user_id = Yii::app()->request->getPost('user_id');
        if (!$user_id)
        {
            $user_type = 1;
        }
        else
        {
            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);
            $user_type = $user_info['user_type'];
        }

        if ($id)
        {
            $postTagObj = new PostTags();
            $post = $postTagObj->getPost($id, $user_type, $page_number, $page_size);

            $response['data']['total'] = $postTagObj->getPostTotal($id, $user_type);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }
            $response['data']['has_next'] = $has_next;

            $response['data']['post'] = $post;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionSearch()
    {
        $term = Yii::app()->request->getPost('term');
        if ($term)
        {
            $postObj = new Post();
            $response['data']['post'] = $postObj->getSearchPost($term);
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }
        else
        {
            $response['data']['post'] = array();

            $response['data']['categories'] = array();

            $response['data']['tags'] = array();

            $response['data']['keywords'] = array();

            $response['data']['authors'] = array();

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        }

        //echo json_encode($response, JSON_HEX_QUOT | JSON_HEX_TAG);
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    public function actionCreateCacheSingleNews()
    {
       $id = Yii::app()->request->getPost('id');
       $user_view_count = Yii::app()->request->getPost('user_view_count');
       $view_count = Yii::app()->request->getPost('user_view_count');
       $delete_cache = Yii::app()->request->getPost('delete_cache');
       
       $cache_name = "YII-SINGLE-POST-CACHE-".$id;
       $cache_data = Yii::app()->cache->get($cache_name);
       if ($cache_data !== false && $delete_cache=="yes")
       {   
           $postModel = new Post();
           Yii::app()->cache->delete($cache_name);
           $singlepost = $postModel->getSinglePost($id);
       }
       else if($cache_data !== false && $delete_cache=="no" && $user_view_count && $view_count)
       {
           $cache_data['seen'] = $cache_data['seen']+$view_count;
           $cache_data['view_count'] = $cache_data['seen']+$view_count;
           $cache_data['user_view_count'] = $cache_data['user_view_count']+$user_view_count;
           $singlepost = $cache_data;
       }
       else if($cache_data !== false)
       {
           $postModel = new Post();
           Yii::app()->cache->delete($cache_name);
           $singlepost = $postModel->getSinglePost($id);
       } 
       else
       {
           $postModel = new Post();
           $singlepost = $postModel->getSinglePost($id);
       }    
       
       Yii::app()->cache->set($cache_name, $singlepost, 5184000);
    }        
    
    private function getSingleNewsFromCache($id)
    {
       $cache_name = "YII-SINGLE-POST-CACHE-".$id; 
       if(!$singlepost = Yii::app()->cache->get($cache_name))
       {
          $postModel = new Post();
          $singlepost = $postModel->getSinglePost($id);
          Yii::app()->cache->set($cache_name, $singlepost, 5184000); 
       }
       return $singlepost;
    }
    public function actionGetComments()
    {
        $post_id = Yii::app()->request->getPost('post_id');
        $user_id = Yii::app()->request->getPost('user_id');
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 9;
        }
        
        
        if($post_id)
        {
            $post_value = $this->getSingleNewsFromCache($post_id);
            if($post_value['can_comment']==1)
            { 
                if($post_value['show_comment_to_all'] || ($user_id && $user_id = $post_value['user_id']))
                {
                    $coments_obj_for_all = new Postcomments();
                    if(($user_id && $user_id = $post_value['user_id']))
                    {
                        $comments_total = $coments_obj_for_all->getCommentsTotal($post_id,true);
                        $comments_data = $coments_obj_for_all->getCommentsPost($post_id,$page_number,$page_size,true);
                    } 
                    else
                    {
                        $comments_total = $coments_obj_for_all->getCommentsTotal($post_id);
                        $comments_data = $coments_obj_for_all->getCommentsPost($post_id,$page_number,$page_size);
                    }    
                }
                
                
                $response['data']['total'] = $comments_total;
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size)
                {
                    $has_next = true;
                }

                $response['data']['has_next'] = $has_next;
                
                $response['data']['comments'] = $comments_data;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success"; 
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Success";  
            }    
        }
        else
        {
           $response['status']['code'] = 400;
           $response['status']['msg'] = "BAD_REQUEST"; 
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }   
    
    public function actionAddComments()
    {
        $post_id = Yii::app()->request->getPost('post_id');
        $user_id = Yii::app()->request->getPost('user_id');
        //$title = Yii::app()->request->getPost('title');
        $details = Yii::app()->request->getPost('details');
        
        if($post_id && $user_id  && $details)
        {
            $post_value = $this->getSingleNewsFromCache($post_id);
            if($post_value['can_comment']==1)
            {
                $coments_obj = new Postcomments();
                $coments_obj->post_id = $post_id;
                $coments_obj->user_id = $user_id;
                //$coments_obj->title = $title;
                $coments_obj->details = $details;
                $coments_obj->save(); 
                if($post_value['show_comment_to_all'] || ($user_id = $post_value['user_id']))
                {
                    $coments_obj_for_all = new Postcomments();
                    if(($user_id && $user_id = $post_value['user_id']))
                    {
                        $comments_data = $coments_obj_for_all->getCommentsTotal($post_id,true);
                    } 
                    else
                    {
                        $comments_data = $coments_obj_for_all->getCommentsTotal($post_id);
                    }    
                }
                $response['data']['total'] = $comments_data;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success"; 
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";  
            }    
        }
        else
        {
           $response['status']['code'] = 400;
           $response['status']['msg'] = "BAD_REQUEST"; 
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }        

    public function actionGetSingleNews()
    {
        $id = Yii::app()->request->getPost('id');
        $user_id = Yii::app()->request->getPost('user_id');
        if($id)
        {
            //update view count
            $postModel = new Post();
            $postobj = $postModel->findByPk($id);
            $postobj->user_view_count = $postobj->user_view_count + Settings::$count_update_by;
            $postobj->view_count = $postobj->view_count + Settings::$count_update_by;
            $postobj->save();
            
            //CREATE CACHE FOR SINGLE NEWS
            $cache_name = "YII-SINGLE-POST-CACHE-".$id;
            
            $cache_data = Yii::app()->cache->get($cache_name);
            if ($cache_data !== false)
            { 
                
                $cache_data['seen'] = $cache_data['seen']+Settings::$count_update_by;
                $cache_data['view_count'] = $cache_data['seen']+Settings::$count_update_by;
                $cache_data['user_view_count'] = $cache_data['user_view_count']+Settings::$count_update_by;
                $singlepost = $cache_data;
            }
            else
            {
                $singlepost = $postModel->getSinglePost($id);
            }     
            
            
            
            Yii::app()->cache->set($cache_name, $singlepost, 5184000);
            
            $comments_data = array();
            if($singlepost['can_comment']==1)
            {
                if($singlepost['show_comment_to_all'] || ($user_id && $user_id = $singlepost['user_id']))
                {
                    $coments_obj = new Postcomments();
                    if(($user_id && $user_id = $singlepost['user_id']))
                    {
                        $comments_data = $coments_obj->getCommentsTotal($id,true);
                    } 
                    else
                    {
                        $comments_data = $coments_obj->getCommentsTotal($id);
                    }    
                }
            }
            //CREATE CACHE FOR SINGLE NEWS
            
            
            $singlepost['content'] = $singlepost['mobile_content'];
            $singlepost['post_type'] = $singlepost['post_type_mobile'];
            
            unset($singlepost['mobile_content']);
            unset($singlepost['post_type_mobile']);
            
            
            
            $user_id = Yii::app()->request->getPost('user_id');
            if (!$user_id)
            {
                $user_type = 1;
            }
            else
            {
                $freeuserObj = new Freeusers();
                $user_info = $freeuserObj->getUserInfo($user_id);
                $user_type = $user_info['user_type'];
            }
            $main_id = Yii::app()->request->getPost('main_id');

            $good_read = "";

            if ($user_id)
            {
                $goodreadObj = new UserGoodRead();
                $goodreadObj->removeGoodRead($id, $user_id);

                $all_good_read_folder = $goodreadObj->getGoodReadUser($id, $user_id);

                if($all_good_read_folder)
                {

                    $good_read = $all_good_read_folder->folder_id;

                }
            }



            $category_id = Yii::app()->request->getPost('category_id');
            
            if (!$category_id)
            {
                if(!$main_id)
                $category_id = $postModel->getCategoryId($id);
                else
                $category_id = $postModel->getCategoryId($main_id);   
            }

            $postcategoryObj = new PostCategory();
            //$allpostid = $postcategoryObj->getPostAll($category_id, $user_type);




            
            
            

            if(!$main_id)
            {
                $next_id = $postcategoryObj->nextpreviousid($category_id, $user_type, $id, $singlepost['published_date'], $singlepost['inner_priority']);

                $previous_id = $postcategoryObj->nextpreviousid($category_id, $user_type, $id, $singlepost['published_date'], $singlepost['inner_priority'], "previous");

                if ($next_id == $previous_id)
                {
                    $next_id = $postcategoryObj->nextpreviousid($category_id, $user_type, $next_id, $singlepost['published_date'], $singlepost['inner_priority'], "next", $id);
                }
            }
            else
            {
                $next_id = $postcategoryObj->nextpreviousid($category_id, $user_type, $main_id, $singlepost['published_date'], $singlepost['inner_priority']);

                $previous_id = $postcategoryObj->nextpreviousid($category_id, $user_type, $main_id, $singlepost['published_date'], $singlepost['inner_priority'], "previous");

                if ($next_id == $previous_id)
                {
                    $next_id = $postcategoryObj->nextpreviousid($category_id, $user_type, $next_id, $singlepost['published_date'], $singlepost['inner_priority'], "next", $id);
                }
            }    

           

            $categoryModel = new Categories();

            $can_wow = 1;
            if($user_id && Settings::$wow_login==true)
            {
                $obj_wow = new Wow();
                $wowexists = $obj_wow->wowexists($id, $user_id);
                if($wowexists)
                {
                    $can_wow = 0;
                }
            }    
            //$subcategory = $categoryModel->getSubcategory($category_id);
            //$response['data']['subcategory'] = $subcategory;
            //$response['data']['allpostid'] = $allpostid;
            $response['data']['good_read'] = $good_read;
            $response['data']['previous_id'] = $previous_id;
            $response['data']['next_id'] = $next_id;
            $response['data']['can_wow'] = $can_wow;

            if($main_id)
            {
                $response['data']['language'] = $postModel->getLanguage($main_id);
            }
            else
            {
                $response['data']['language'] = $postModel->getLanguage($id);
            }   

            if($main_id)
            {
                $response['data']['main_id'] = $main_id;
            }
            else
            {
                $response['data']['main_id'] = $id;
            }    


            $response['data']['post']           = $singlepost;
            $response['data']['comments_total']  = $comments_data;
            $response['status']['code']         = 200;
            $response['status']['msg']          = "DATA_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }    

        //echo json_encode($response, JSON_HEX_QUOT | JSON_HEX_TAG);
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    private function createAllCache($cache_name)
    {
        $cache_all = "CACHE-KEYS";
        $cache_values = Yii::app()->cache->get($cache_all);
        $new_cache = array();
        if ($cache_values === false)
        {
            $new_cache[] = $cache_name;
            Yii::app()->cache->set($cache_all, $new_cache);
        }
        else if (!in_array($cache_name, $cache_values))
        {
            array_push($cache_values, $cache_name);
            Yii::app()->cache->set($cache_all, $cache_values);
        }
    }

    public function actionIndex()
    {
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        $user_id = Yii::app()->request->getPost('user_id');
       

        $already_showed = Yii::app()->request->getPost('already_showed');
        $from_main_site = Yii::app()->request->getPost('from_main_site');
        $callded_for_cache = Yii::app()->request->getPost('callded_for_cache');

        $content_showed_for_caching = "top";
        if ($already_showed)
            $content_showed_for_caching = md5(str_replace(",", "-", $already_showed));

        $category_filter = "none";
        $category_not_to_show = false;
        if (!$user_id)
        {
            $user_type = 1;
        }
        else
        {
            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);
            $user_type = $user_info['user_type'];

            $user_pref = FreeUserPreference::model()->findByAttributes(array('free_user_id' => $user_id));
            if ($user_pref)
            {
                if ($user_pref->category_ids)
                {
                    $category_not_to_show = $user_pref->category_ids;
                    $category_filter = md5(str_replace(",", "-", $user_pref->category_ids));
                }
            }
        }
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 9;
        }



        $cache_name = "YII-RESPONSE-HOME-" . $page_number . "-" . $page_size . "-" . $content_showed_for_caching . "-" . $category_filter . "-" . $user_type;
        $this->createAllCache($cache_name);
        $response = Yii::app()->cache->get($cache_name);
        
        if ($response === false)
        {

             
            $homepageObj = new HomepageData();

            if ($already_showed)
            {
                $homepage_post = $homepageObj->getHomePagePost($user_type, $page_number, $page_size, false, $already_showed, $from_main_site, $category_not_to_show);
            }
            else
            {
                $homepage_post = $homepageObj->getHomePagePost($user_type, $page_number, $page_size, false, false, $from_main_site, $category_not_to_show);
            }

            $response['data']['total'] = $homepageObj->getPostTotal($user_type, false, $category_not_to_show);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }
           
            $response['data']['has_next'] = $has_next;

            $response['data']['post'] = $homepage_post;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";


            Yii::app()->cache->set($cache_name, $response, 86400);
            
        }
        if($page_number==1)
        {
            $pinpostobj = new Pinpost();
            $all_pinpost = $pinpostobj->getPinPost(0);
            $new_post = array();
            $i = 0;
            foreach($response['data']['post'] as $value)
            {
                for($k=$i; $k<10; $k++)
                {
                    if(isset($all_pinpost[$k+1]))
                    {
                       $new_post[]['id'] = $all_pinpost[$k+1]; 
                    }
                    else
                    {
                        break;
                    }
                }
                if(!in_array($value['id'],$all_pinpost))
                {
                    $new_post[]['id'] = $value['id'];
                }  
                $i++;
                
            }
            $response['data']['post'] = $new_post;
        }    
        
        if(isset($response['data']['post']) && count($response['data']['post'])>0)
        {
            $wow = array();
            if($user_id)
            {    
                $obj_wow = new Wow();
                $wow = $obj_wow->userwow($user_id);
            }
           
            $post_data = array();
            $i = 0;
            foreach($response['data']['post'] as $value)
            {
                $post_data[$i] = $this->getSingleNewsFromCache($value['id']);
                $post_data[$i]['can_wow'] = 1;
                if(in_array($value['id'], $wow) && Settings::$wow_login==true)
                {
                   $post_data[$i]['can_wow'] = 0; 
                }        
                $i++;
            } 
            $response['data']['post'] = $post_data;
        }    

        if (!$callded_for_cache)
            echo CJSON::encode($response);
        Yii::app()->end();
    }
    
          

    public function actionGetSchoolTeacherBylinePost()
    {
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        $id = Yii::app()->request->getPost('id');
        $target = Yii::app()->request->getPost('target');
        $user_id = Yii::app()->request->getPost('user_id');


        if($target && $id)
        {
            if (empty($page_number))
            {
                $page_number = 1;
            }
            if (empty($page_size))
            {
                $page_size = 10;
            }

            $user_id = Yii::app()->request->getPost('user_id');
            if (!$user_id)
            {
                $user_type = 1;
            }
            else
            {
                $freeuserObj = new Freeusers();
                $user_info = $freeuserObj->getUserInfo($user_id);
                $user_type = $user_info['user_type'];
            }

            //$cache_name = "YII-RESPONSE-STB-" . $id . "-" . $target . "-" . $page_number . "-" . $page_size . "-" . $user_type;
            //$this->createAllCache($cache_name);
            //$response = Yii::app()->cache->get($cache_name);
//            if ($response === false)
//            {

                $postObj = new Post();
                $post = $postObj->getPosts($id, $user_type, $target, $page = 1, $page_size = 10);

                $response['data']['total'] = $postObj->getPostTotal($id, $user_type, $target);
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size)
                {
                    $has_next = true;
                }
                $response['data']['has_next'] = $has_next;
                $response['data']['post'] = $post;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "DATA_FOUND";
                //Yii::app()->cache->set($cache_name, $response, 86400);
//            }
            if(isset($response['data']['post']) && count($response['data']['post'])>0)
            {

                $wow = array();
                if($user_id)
                {    
                    $obj_wow = new Wow();
                    $wow = $obj_wow->userwow($user_id);
                }

                $post_data = array();
                $i = 0;
                foreach($response['data']['post'] as $value)
                {
                    $post_data[$i] = $this->getSingleNewsFromCache($value['id']);
                    $post_data[$i]['can_wow'] = 1;
                    if(in_array($value['id'], $wow) && Settings::$wow_login==true)
                    {
                       $post_data[$i]['can_wow'] = 0; 
                    }        
                    $i++;
                } 
                $response['data']['post'] = $post_data;
            }
            
            
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetCategoryPost()
    {
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        $category_id = Yii::app()->request->getPost('category_id');
        $popular_sort = Yii::app()->request->getPost('popular_sort');
        $fetaured = Yii::app()->request->getPost('fetaured');
        $game_type = Yii::app()->request->getPost('game_type');
        $callded_for_cache = Yii::app()->request->getPost('callded_for_cache');

        $extra = "";
        if ($popular_sort)
        {
            $extra.= "-popular";
        }
        if ($game_type)
        {
            $extra.= "-" . $game_type;
        }
        if ($fetaured == 1)
        {
            $extra.= "-featured";
        }
        else if ($fetaured == 2)
        {
            $extra.= "-notfeatured";
        }
        if (empty($page_number))
        {
            $page_number = 1;
        }
        if (empty($page_size))
        {
            $page_size = 10;
        }

        $user_id = Yii::app()->request->getPost('user_id');
        if (!$user_id)
        {
            $user_type = 1;
        }
        else
        {
            $freeuserObj = new Freeusers();
            $user_info = $freeuserObj->getUserInfo($user_id);
            $user_type = $user_info['user_type'];
        }

        $cache_name = "YII-RESPONSE-CATEGORY-" . $category_id . "-" . $page_number . "-" . $page_size . "-" . $user_type . $extra;
        $this->createAllCache($cache_name);
        $response = Yii::app()->cache->get($cache_name);
        if ($response === false)
        {

            $postcategoryObj = new PostCategory();
            $post = $postcategoryObj->getPost($category_id, $user_type, $page_number, $page_size, $popular_sort, $game_type, $fetaured);

            $response['data']['total'] = $postcategoryObj->getPostTotal($category_id, $user_type);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }

            $categoryObj = new Categories();

            $response['data']['has_next'] = $has_next;


            $response['data']['subcategory'] = $categoryObj->getSubcategory($category_id);
            $response['data']['post'] = $post;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
            Yii::app()->cache->set($cache_name, $response, 86400);
        }
        if(isset($response['data']['post']) && count($response['data']['post'])>0)
        {
           
            $wow = array();
            if($user_id)
            {    
                $obj_wow = new Wow();
                $wow = $obj_wow->userwow($user_id);
            }

            $post_data = array();
            $i = 0;
            foreach($response['data']['post'] as $value)
            {
                $post_data[$i] = $this->getSingleNewsFromCache($value['id']);
                $post_data[$i]['can_wow'] = 1;
                if(in_array($value['id'], $wow)  && Settings::$wow_login==true)
                {
                   $post_data[$i]['can_wow'] = 0; 
                }        
                $i++;
            } 
            $response['data']['post'] = $post_data;
        }
        
        if (!$callded_for_cache)
            echo CJSON::encode($response);
        Yii::app()->end();
    }

    private function encrypt($field, $salt)
    {
        return hash('sha512', $salt . $field);
    }

    public function actionGetuserinfo()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_id = Yii::app()->request->getPost('user_id');
            if ($user_id)
            {
                $freeuserObj = new Freeusers();
                if ($freeuserObj->getUserInfo($user_id))
                {
                    $countryObj = new Countries();
                    $response['data']['countries'] = $countryObj->getCountryies();
                    $response['data']['user'] = $freeuserObj->getUserInfo($user_id);
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "DATA_FOUND";
                }
                else
                {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request";
                }
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            }
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionCreate()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $countryObj = new Countries();
            $email = Yii::app()->request->getPost('email');
            $password = Yii::app()->request->getPost('password');
            $user_id = Yii::app()->request->getPost('user_id');
            $fb_profile_id = Yii::app()->request->getPost('fb_profile_id');
            $gl_profile_id = Yii::app()->request->getPost('gl_profile_id');
            $first_name = Yii::app()->request->getPost('first_name');

            $response = array();

            $freeuserObj = new Freeusers();
            if ($fb_profile_id)
            {
                if ($user = $freeuserObj->getFreeuserFb($fb_profile_id))
                {
                    $folderObj = new UserFolder();

                    $folderObj->createGoodReadFolder($user->id);

                    $response['data']['user_type'] = 0;
                    $response['data']['free_id'] = $user->id;
                    $response['data']['user'] = $freeuserObj->getUserInfo($user->id);
                    $response['status']['code'] = 200;
                    $response['data']['is_register'] = true;
                    $response['data']['is_login'] = true;
                    $response['status']['msg'] = "Successfully Saved";
                }
                else if (!Yii::app()->request->getPost('user_type'))
                {
                    $response['status']['code'] = 200;
                    $response['data']['is_register'] = false;
                    $response['data']['is_login'] = false;
                    $response['status']['msg'] = "Successfully Saved";
                }
                else
                {
                    $freeuserObj->fb_profile_id = $fb_profile_id;
                    $freeuserObj->first_name = $first_name;
                    $freeuserObj->email = $email;
                    $freeuserObj->last_name = Yii::app()->request->getPost('last_name');
                    $freeuserObj->middle_name = Yii::app()->request->getPost('middle_name');
                    $freeuserObj->gender = Yii::app()->request->getPost('gender');
                    $freeuserObj->nick_name = Yii::app()->request->getPost('nick_name');
                    $freeuserObj->user_type = Yii::app()->request->getPost('user_type');
                    $freeuserObj->medium = Yii::app()->request->getPost('medium');
                    $freeuserObj->tds_country_id = Yii::app()->request->getPost('country');
                    $freeuserObj->district = Yii::app()->request->getPost('district');
                    $freeuserObj->grade_ids = Yii::app()->request->getPost('grade_ids');
                    $freeuserObj->mobile_no = Yii::app()->request->getPost('mobile_no');
                    $freeuserObj->dob = Yii::app()->request->getPost('dob');
                    $freeuserObj->school_name = Yii::app()->request->getPost('school_name');
                    $freeuserObj->location = Yii::app()->request->getPost('location');
                    $freeuserObj->teaching_for = Yii::app()->request->getPost('teaching_for');
                    $freeuserObj->occupation = Yii::app()->request->getPost('occupation');
                    $freeuserObj->profile_image = Yii::app()->request->getPost('profile_image');
                    $freeuserObj->save();

                    $folderObj = new UserFolder();

                    $folderObj->createGoodReadFolder($freeuserObj->id);

                    $response['data']['user_type'] = 0;
                    $response['data']['free_id'] = $freeuserObj->id;
                    $response['data']['user'] = $freeuserObj->getUserInfo($freeuserObj->id);
                    $response['data']['countries'] = $countryObj->getCountryies();
                    $response['status']['code'] = 200;
                    $response['data']['is_register'] = true;
                    $response['data']['is_login'] = false;
                    $response['status']['msg'] = "Successfully Saved";
                }
            }
            else if ($gl_profile_id)
            {
                if ($user = $freeuserObj->getFreeuserGmail($gl_profile_id))
                {
                    $folderObj = new UserFolder();

                    $folderObj->createGoodReadFolder($user->id);
                    $response['data']['user_type'] = 0;
                    $response['data']['free_id'] = $user->id;
                    $response['data']['user'] = $freeuserObj->getUserInfo($user->id);
                    $response['status']['code'] = 200;
                    $response['data']['is_register'] = true;
                    $response['data']['is_login'] = true;
                    $response['status']['msg'] = "Successfully Saved";
                }
                else if (!Yii::app()->request->getPost('user_type'))
                {
                    $response['status']['code'] = 200;
                    $response['data']['is_register'] = false;
                    $response['data']['is_login'] = false;
                    $response['status']['msg'] = "Successfully Saved";
                }
                else
                {
                    $freeuserObj->gl_profile_id = $gl_profile_id;
                    $freeuserObj->first_name = $first_name;
                    $freeuserObj->email = $email;
                    $freeuserObj->last_name = Yii::app()->request->getPost('last_name');
                    $freeuserObj->middle_name = Yii::app()->request->getPost('middle_name');
                    $freeuserObj->gender = Yii::app()->request->getPost('gender');
                    $freeuserObj->nick_name = Yii::app()->request->getPost('nick_name');
                    $freeuserObj->user_type = Yii::app()->request->getPost('user_type');
                    $freeuserObj->medium = Yii::app()->request->getPost('medium');
                    $freeuserObj->tds_country_id = Yii::app()->request->getPost('country');
                    $freeuserObj->district = Yii::app()->request->getPost('district');
                    $freeuserObj->grade_ids = Yii::app()->request->getPost('grade_ids');
                    $freeuserObj->mobile_no = Yii::app()->request->getPost('mobile_no');
                    $freeuserObj->dob = Yii::app()->request->getPost('dob');
                    $freeuserObj->school_name = Yii::app()->request->getPost('school_name');
                    $freeuserObj->location = Yii::app()->request->getPost('location');
                    $freeuserObj->teaching_for = Yii::app()->request->getPost('teaching_for');
                    $freeuserObj->occupation = Yii::app()->request->getPost('occupation');
                    $freeuserObj->profile_image = Yii::app()->request->getPost('profile_image');
                    $freeuserObj->save();
                    $folderObj = new UserFolder();

                    $folderObj->createGoodReadFolder($freeuserObj->id);

                    $response['data']['user_type'] = 0;
                    $response['data']['free_id'] = $freeuserObj->id;
                    $response['data']['user'] = $freeuserObj->getUserInfo($freeuserObj->id);
                    $response['data']['countries'] = $countryObj->getCountryies();
                    $response['status']['code'] = 200;
                    $response['data']['is_register'] = true;
                    $response['data']['is_login'] = false;
                    $response['status']['msg'] = "Successfully Saved";
                }
            }
            else if ($user_id)
            {
                $freeuserObj = $freeuserObj->findByPk($user_id);

                if ($password)
                {
                    $freeuserObj->salt = md5(uniqid(rand(), true));
                    $freeuserObj->password = $this->encrypt($password, $freeuserObj->salt);
                }

                if (isset($_FILES['profile_image']['name']) && !empty($_FILES['profile_image']['name']))
                {
                    $main_dir = Settings::$image_path . 'upload/free_user_profile_images/';
                    $uploads_dir = Settings::$main_path . 'upload/free_user_profile_images/';
                    $tmp_name = $_FILES["profile_image"]["tmp_name"];
                    $name = "file_" . $user_id . "_" . time() . "_" . str_replace(" ", "-", $_FILES["profile_image"]["name"]);

                    move_uploaded_file($tmp_name, "$uploads_dir/$name");
                    $freeuserObj->profile_image = $main_dir . $name;
                }


                if (!$password)
                {
                    if ($first_name)
                        $freeuserObj->first_name = $first_name;

                    if (Yii::app()->request->getPost('last_name'))
                        $freeuserObj->last_name = Yii::app()->request->getPost('last_name');

                    if (Yii::app()->request->getPost('middle_name'))
                        $freeuserObj->middle_name = Yii::app()->request->getPost('middle_name');

                    if (Yii::app()->request->getPost('gender'))
                        $freeuserObj->gender = Yii::app()->request->getPost('gender');

                    if (Yii::app()->request->getPost('nick_name'))
                        $freeuserObj->nick_name = Yii::app()->request->getPost('nick_name');

                    if (Yii::app()->request->getPost('user_type'))
                        $freeuserObj->user_type = Yii::app()->request->getPost('user_type');

                    if (Yii::app()->request->getPost('medium'))
                        $freeuserObj->medium = Yii::app()->request->getPost('medium');

                    if (Yii::app()->request->getPost('country'))
                        $freeuserObj->tds_country_id = Yii::app()->request->getPost('country');

                    if (Yii::app()->request->getPost('district'))
                        $freeuserObj->district = Yii::app()->request->getPost('district');

                    if (Yii::app()->request->getPost('grade_ids'))
                        $freeuserObj->grade_ids = Yii::app()->request->getPost('grade_ids');

                    if (Yii::app()->request->getPost('mobile_no'))
                        $freeuserObj->mobile_no = Yii::app()->request->getPost('mobile_no');

                    if (Yii::app()->request->getPost('dob'))
                        $freeuserObj->dob = Yii::app()->request->getPost('dob');

                    if (Yii::app()->request->getPost('school_name'))
                        $freeuserObj->school_name = Yii::app()->request->getPost('school_name');

                    if (Yii::app()->request->getPost('location'))
                        $freeuserObj->location = Yii::app()->request->getPost('location');

                    if (Yii::app()->request->getPost('teaching_for'))
                        $freeuserObj->teaching_for = Yii::app()->request->getPost('teaching_for');

                    if (Yii::app()->request->getPost('occupation'))
                        $freeuserObj->occupation = Yii::app()->request->getPost('occupation');
                }

                $freeuserObj->save();



                $response['data']['user_type'] = 0;
                $response['data']['free_id'] = $freeuserObj->id;
                $response['data']['user'] = $freeuserObj->getUserInfo($freeuserObj->id);
                $response['data']['is_register'] = true;
                $response['data']['is_login'] = true;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Successfully Saved";
            }
            else if (Yii::app()->request->getPost('user_type') && $email && !$freeuserObj->getFreeuser($email) && $password)
            {

                $freeuserObj->salt = md5(uniqid(rand(), true));
                $freeuserObj->password = $this->encrypt($password, $freeuserObj->salt);
                $freeuserObj->email = Yii::app()->request->getPost('email');
                $freeuserObj->user_type = Yii::app()->request->getPost('user_type');



                $freeuserObj->save();
                $folderObj = new UserFolder();

                $folderObj->createGoodReadFolder($freeuserObj->id);

                $response['data']['user_type'] = 0;
                $response['data']['free_id'] = $freeuserObj->id;
                $response['data']['countries'] = $countryObj->getCountryies();
                $response['data']['user'] = $freeuserObj->getUserInfo($freeuserObj->id);
                $response['data']['is_register'] = true;
                $response['data']['is_login'] = false;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Successfully Saved";
            }
            else if ($email && $password && $freeuserObj->getFreeuser($email))
            {
                $response['status']['code'] = 404;
                $response['status']['msg'] = "Username Exists";
            }
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
            }
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGarbageCollector()
    {
        $keys_to_match = Yii::app()->request->getPost('keys_to_match');
        $cache_all = "CACHE-KEYS";
        $cache_keys = Yii::app()->cache->get($cache_all);
        if ($cache_keys !== false)
        {
            foreach ($cache_keys as $value)
            {
                if (strpos($value, $keys_to_match) !== false)
                {
                    Yii::app()->cache->delete($value);
                }
            }
        }
    }

    public function actionSet_preference()
    {

        $response = array();
        if (isset($_POST) && !empty($_POST))
        {

            $user_id = Yii::app()->request->getPost('user_id');
            $category_ids = Yii::app()->request->getPost('category_ids');

            if (empty($user_id) || empty($category_ids))
            {

                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";

                echo CJSON::encode($response);
                Yii::app()->end();
            }

            $category_ids = trim($category_ids, ',');

            $user_pref_mod = FreeUserPreference::model()->findByAttributes(array('free_user_id' => $user_id));

            if (!empty($user_pref_mod))
            {

                $user_pref_mod->category_ids = $category_ids;

                if ($user_pref_mod->update())
                {

                    $response['data']['preferred_categories'] = $user_pref_mod->category_ids;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "PREFERENCE_SAVED";
                }
                else
                {

                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "USER_PREFERENCE_NOT_SAVED";
                }
            }
            else
            {

                $user_pref_mod = new FreeUserPreference;

                $user_pref_mod->free_user_id = $user_id;
                $user_pref_mod->category_ids = $category_ids;

                if ($user_pref_mod->insert())
                {

                    $response['data']['preferred_categories'] = $user_pref_mod->category_ids;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "USER_PREFERENCE_SAVED";
                }
                else
                {

                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "BAD_REQUEST";
                }
            }
        }
        else
        {

            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGet_preference()
    {

        $response = array();
        if (isset($_POST) && !empty($_POST))
        {

            $user_id = Yii::app()->request->getPost('user_id');

            if (empty($user_id))
            {

                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";

                echo CJSON::encode($response);
                Yii::app()->end();
            }

            $user_id = Yii::app()->request->getPost('user_id');

            $category_mod = new Categories;
            $all_categoires = $category_mod->all_cats_in_relative_manner();

            $user_pref_mod = FreeUserPreference::model()->findByAttributes(array('free_user_id' => $user_id));

            $response['data']['all_categories'] = $all_categoires;

            $response['data']['preferred_categories'] = (!empty($user_pref_mod)) ? $user_pref_mod->category_ids : '';
            $response['status']['code'] = (!empty($user_pref_mod) && !empty($all_categoires) ) ? 200 : (!empty($all_categoires)) ? 202 : 404;
            $response['status']['msg'] = (!empty($user_pref_mod)) ? "USER_PREFERENCE_FOUND" : "USER_PREFERENCE_NOT_FOUND";
        }
        else
        {

            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }


        echo CJSON::encode($response);
        Yii::app()->end();
    }

}
