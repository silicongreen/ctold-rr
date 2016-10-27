<?php

class SyllabusController extends Controller {

    /**
     * @return array action filters
     */
    public function filters() {
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
    public function accessRules() {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('index','lessonplansstd','lsubjects', 'terms','single','addlessonplan','singlelessonplans','lessonplandelete','lessoncategory','getsubject','lessonplanedit','lessonplans','assignlesson'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    public function actionlessonplansStd()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $subject_id = Yii::app()->request->getPost('subject_id');
            $response = array();
            if ($subject_id && Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )) )
            {
                if (Yii::app()->user->isParent)
                {
                    $batch_id = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                
                $page_number = Yii::app()->request->getPost('page_number');
                $page_size = Yii::app()->request->getPost('page_size');
                if (empty($page_number))
                {
                    $page_number = 1;
                }
                if (empty($page_size))
                {
                    $page_size = 10;
                }
                
                
                $lessonplan = new Lessonplan();


                $lessonplans = $lessonplan->getLessonPlanStudent($subject_id, $batch_id, $page_number, $page_size);
                $response['data']['total'] = $lessonplan->getLessonPlanTotalStudent($subject_id, $batch_id);
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size)
                {
                    $has_next = true;
                }
                $response['data']['has_next'] = $has_next;
                $response['data']['lessonplans'] = $lessonplans;
                $response['status']['code'] = 200;
                $response['status']['msg'] = ($response['data']['total'] > 0) ? "Data Found" : "No Data Found";
            }
            else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
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
    
    public function actionLsubjects()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )))
            {
                if (Yii::app()->user->isParent)
                {
                    $batch_id = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                
                $subject = new Subjects();
                $all_subject = $subject->getSubject($batch_id, $student_id);
                
                $objlessonplan = new Lessonplan();
                
                $lsubjects = array();
                $i = 0;
                foreach($all_subject as $value)
                {
                    $total = $objlessonplan->getLessonPlanTotalStudent($value['id'], $batch_id);
                    if($total>0)
                    {
                        $lastupdated = $objlessonplan->getLessonPlanLastUpdated($value['id'], $batch_id);
                        $lsubjects[$i] = $value;
                        $lsubjects[$i]['total'] = $total;
                        $lsubjects[$i]['lastupdated'] = $lastupdated;
                        $i++;
                    }    
                    
                }    
                
                $response['data']['subject'] = $lsubjects;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Found";
                
            }
            else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
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
    public function actionSinglelessonplans()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $id = Yii::app()->request->getPost('id');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret  && $id)
            {
                
                $lessonplan = new Lessonplan();
                $lessonplans = $lessonplan->getLessonPlanSingle($id);
                if($lessonplans)
                {
                    $response['data']['lessonplan'] = $lessonplans;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                }
                else
                {
                   $response['status']['code'] = 400;
                   $response['status']['msg'] = "Access Denied."; 
                }    
                
                
                
            }
            else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
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
    public function actionlessonplans()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $batch_id = Yii::app()->request->getPost('batch_id');
            $subject_id = Yii::app()->request->getPost('subject_id');
            $lessonplan_category_id = Yii::app()->request->getPost('lessonplan_category_id');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher )
            {
                
                $page_number = Yii::app()->request->getPost('page_number');
                $page_size = Yii::app()->request->getPost('page_size');
                if (empty($page_number))
                {
                    $page_number = 1;
                }
                if (empty($page_size))
                {
                    $page_size = 10;
                }
                
                if(!$lessonplan_category_id)
                {
                    $lessonplan_category_id = 0;
                }
                
                if(!$batch_id)
                {
                    $batch_id = 0;
                }
                
                if(!$subject_id)
                {
                    $subject_id = 0;
                }
                
                $lessonplan = new Lessonplan();


                $lessonplans = $lessonplan->getLessonPlan($subject_id, $batch_id, $lessonplan_category_id, $page_number, $page_size);
                $response['data']['total'] = $lessonplan->getLessonPlanTotal($subject_id, $batch_id, $lessonplan_category_id);
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size)
                {
                    $has_next = true;
                }
                $response['data']['has_next'] = $has_next;
                $response['data']['lessonplans'] = $lessonplans;
                $response['status']['code'] = 200;
                $response['status']['msg'] = ($response['data']['total'] > 0) ? "Data Found" : "No Data Found";
            }
            else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
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
    public function actionlessonPlanDelete()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        
        $id = Yii::app()->request->getPost('id');
        if ($user_secret && Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $id)
        {
                $ids = explode(",", $id);
                $lessonplan = new Lessonplan();
                $delete_done = false;
                foreach($ids as $value)
                {
                    $lessonplan = $lessonplan->findByPk($value); 

                    if($lessonplan && $lessonplan->author_id==Yii::app()->user->id)
                    {
                        $lessonplan->delete();
                        $delete_done = true;
                        
                    }
                }
                
                if($delete_done)
                {
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Success"; 
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
    public function actionGetSubject()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $id = Yii::app()->request->getPost('id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
        {
            if(!$id)
            {
               $id = 0;
            } 
            $emplyee_subject = new EmployeesSubjects();
            $subjects = $emplyee_subject->getSubject(Yii::app()->user->profileId,$id);
            $response['data']['subjects'] = $subjects;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "EVENTS_FOUND";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    public function actionAssignLesson()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $is_show = Yii::app()->request->getPost('is_show');
        $id = Yii::app()->request->getPost('id');
        
        
        if ($user_secret && Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $id)
        {
            if(!$is_show)
            {
                $is_show = 0;
            }
            $ids = explode(",", $id);
            $lessonplan = new Lessonplan();
            foreach($ids as $value)
            {
                $lesson = $lessonplan->findByPk($value);
                if($lesson)
                {
                    $lesson->is_show = $is_show;
                    $lesson->save();
                }
            }    
             
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Success";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    public function actionlessonplanedit()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        
        $id = Yii::app()->request->getPost('id');
        if ($user_secret && Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $id)
        {
                $lessonCategory = new LessonplanCategory();
                $lessonplan = new Lessonplan();
                $lessonplan = $lessonplan->findByPk($id); 
            
                if($lessonplan && $lessonplan->author_id==Yii::app()->user->id)
                {
                    foreach($lessonplan as $key=>$value)
                    {
                       $lessonplanarray[$key] = $value;
                    }
                    $emplyee_subject = new EmployeesSubjects();
                    $subjects = $emplyee_subject->getSubject(Yii::app()->user->profileId,$id,true);
                    $response['data']['category'] = $lessonCategory->getUserCategory($id,true);
                    $response['data']['subjects'] = $subjects;
                    
                    $response['data']['lessonplan'] = $lessonplanarray;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Success";
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
    public function actionLessonCategory()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        
        $id = Yii::app()->request->getPost('id');
        if ($user_secret && Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
        {
                $lessonCategory = new LessonplanCategory();
                
                if(!$id)
                {
                    $id = 0;
                }    
            
                $response['data']['category'] = $lessonCategory->getUserCategory($id);
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    public function actionAddLessonPlan()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $subject_ids = Yii::app()->request->getPost('subject_ids');
        $lessonplan_category_id = Yii::app()->request->getPost('lessonplan_category_id');
        $title = Yii::app()->request->getPost('title');
        $publish_date = Yii::app()->request->getPost('publish_date');
        $is_show = Yii::app()->request->getPost('is_show');
        $content = Yii::app()->request->getPost('content');
        $id = Yii::app()->request->getPost('id');
        
        if ($user_secret && Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher  && $lessonplan_category_id
                 && $title && $content)
        {
            if(!$is_show)
            {
                $is_show = 0;
            } 
            $subjects = array();
            $batches = array();
            $subjects_array = array();
            if($subject_ids)
            $subjects_array = explode(",", $subject_ids);
            
          
            if($subjects_array)
            {
                foreach($subjects_array as $value)
                {
                    $subjectObj = new Subjects();
                    $sub = $subjectObj->findByPk($value);
                    if($sub)
                    {
                        $subjects[] = $sub->id;
                        if(!in_array($sub->batch_id, $batches))
                        {
                            $batches[] = $sub->batch_id;
                        }
                    }    
                    
                }
            }
            
                if($subjects)
                {
                    $subject_ids = implode(",", $subjects);
                    $batch_ids = implode(",", $batches);
                }
                $author_id = Yii::app()->user->id;
                $school_id = Yii::app()->user->schoolId;
                $created_at = $updated_at = date("Y-m-d H:i:s");
                
                $lessonplan = new Lessonplan();
                if($id)
                {
                   $lessonplan = $lessonplan->findByPk($id); 
                } 
                if($subjects)
                {
                    $lessonplan->subject_ids = $subject_ids;
                    $lessonplan->batch_ids = $batch_ids;
                }
                else 
                {
                    $lessonplan->subject_ids = null;
                    $lessonplan->batch_ids = null; 
                }    
                $lessonplan->lessonplan_category_id = $lessonplan_category_id;
                $lessonplan->title = $title;
                $lessonplan->is_show = $is_show;
                
                if($publish_date)
                {
                    $lessonplan->publish_date = $publish_date;
                }
                
                $lessonplan->content = $content;
                $lessonplan->school_id = $school_id;
                $lessonplan->author_id = $author_id;
                $lessonplan->created_at = $created_at;
                if(!$id)
                {
                   $lessonplan->updated_at = $updated_at; 
                }
//                
                $lessonplan->save();
                if (isset($_FILES['attachment_file_name']['name']) && !empty($_FILES['attachment_file_name']['name']))
                {
                    $lessonplan->attachment_content_type = Yii::app()->request->getPost('mime_type');
                    $lessonplan->attachment_file_size = Yii::app()->request->getPost('file_size');
                    $this->upload_lessonplan($_FILES, $lessonplan);
                }  
                
                
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success";
                
               
                
            

        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    
    public function actionSingle() 
    {
        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');

            $id = Yii::app()->request->getPost('id');


            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {
                $syllabus = new Syllabuses;
                $syllabus = $syllabus->getSingleSyllabus($id);

                if (!$syllabus) {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = 'NO_SYLLABUS_FOUND';
                } else {
                    $response['data']['syllabus'] = $syllabus;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = 'SYLLABUS_FOUND';
                }
            } else {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request.";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionIndex() {

        

            $user_secret = Yii::app()->request->getPost('user_secret');
            $term_id = Yii::app()->request->getPost('term');
            $batch_id = Yii::app()->request->getPost('batch_id');

            $response = array();
            if ($user_secret && Yii::app()->user->user_secret === $user_secret) {
                
                if (!Yii::app()->user->isStudent && !$batch_id) {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request.";
                    echo CJSON::encode($response);
                    Yii::app()->end();
                    exit;
                }
                if(!$term_id)
                {
                    $term_id = 0;
                }
                
                if (Yii::app()->user->isStudent) {
                    $batch_id = Yii::app()->user->batchId;
                }
                
               
                $syllabus = new Syllabuses();    
                $syllabus = $syllabus->getSyllabus($term_id, $batch_id);
               

                if (!$syllabus) {
                    $response['data']['syllabus'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = 'NO_SYLLABUS_FOUND';
                } else {
                    $response['data']['syllabus'] = $syllabus;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = 'SYLLABUS_FOUND';
                }
            } else {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
         

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionTerms() {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $batch_id = Yii::app()->request->getPost('batch_id');
            $category_id = Yii::app()->request->getPost('category_id');

            if (Yii::app()->user->user_secret === $user_secret) {
                
                $school_id = Yii::app()->user->schoolId;

                if (!Yii::app()->user->isStudent && empty($batch_id)) {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request.";
                    echo CJSON::encode($response);
                    Yii::app()->end();
                }

                if (Yii::app()->user->isStudent) {
                    $batch_id = Yii::app()->user->batchId;
                }
                if(!$category_id)
                {
                    $category_id = 3;
                }

                $exam_category = new ExamGroups;
                $exam_category = $exam_category->getExamCategory($school_id, $batch_id, $category_id);

                if (!empty($exam_category)) {
                    $response['data']['terms'] = $exam_category;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "TERMS_FOUND";
                } else {
                    $response['data']['terms'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "NO_TERMS_FOUND";
                }
            } else {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request.";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    private function upload_lessonplan($file,$lessonplan)
    {
        $lessonplan->attachment_updated_at = date("Y-m-d H:i:s");
        $lessonplan->updated_at = date("Y-m-d H:i:s");
        
                    
        $attachment_datetime_chunk = explode(" ", $lessonplan->updated_at);

        $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
        $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

        $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
        $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];

        $uploads_dir = Settings::$paid_image_path . "uploads/lessonplans/attachments/".$lessonplan->id."/original/";
        $file_name =  str_replace(" ", "+",$file['attachment_file_name']['name']) . "?" .$attachment_extra;
        $tmp_name = $file["attachment_file_name"]["tmp_name"];
        
        if(!is_dir($uploads_dir))
        {
            @mkdir($uploads_dir, 0777, true);
        }
        
        $uploads_dir = $uploads_dir.$file_name;
        

        if(@move_uploaded_file($tmp_name, "$uploads_dir"))
        {
            $lessonplan->attachment_file_name = $file['attachment_file_name']['name'];
            $lessonplan->save();
        } 
    }

}
