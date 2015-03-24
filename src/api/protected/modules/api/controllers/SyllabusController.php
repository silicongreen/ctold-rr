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
                'actions' => array('index', 'terms','single','addlessonplan','singlelessonplans','lessonplandelete','lessoncategory','getsubject','lessonplanedit','lessonplans','assignlesson'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
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
            $lessonplan_category_id = Yii::app()->request->getPost('lessonplan_category_id');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $lessonplan_category_id && $batch_id)
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
                
                $lessonplan = new Lessonplan();


                $lessonplans = $lessonplan->getLessonPlan($batch_id, $lessonplan_category_id,$page_number,$page_size);
                $response['data']['total'] = $lessonplan->getLessonPlanTotal($batch_id, $lessonplan_category_id);
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size)
                {
                    $has_next = true;
                }
                $response['data']['has_next'] = $has_next;
                $response['data']['lessonplans'] = $lessonplans;
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
    public function actionlessonPlanDelete()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        
        $id = Yii::app()->request->getPost('id');
        if ($user_secret && Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $id)
        {
                $lessonCategory = new LessonplanCategory();
                $lessonplan = new Lessonplan();
                $lessonplan = $lessonplan->findByPk($id); 
            
                if($lessonplan && $lessonplan->author_id==Yii::app()->user->profileId)
                {
                    $lessonplan->delete();
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
        
        if ($user_secret && Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $subject_ids && $lessonplan_category_id
                 && $title && $publish_date && $content)
        {
            if(!$is_show)
            {
                $is_show = 0;
            } 
            $subjects = array();
            $batches = array();
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
                        $batches[] = $sub->batch_id;
                    }    
                    
                }
            }
            
            if($subjects)
            {
                $subject_ids = implode(",", $subjects);
                $batch_ids = implode(",", $batches);
                $author_id = Yii::app()->user->id;
                $school_id = Yii::app()->user->schoolId;
                $created_at = $updated_at = date("Y-m-d H:i:s");
                
                $lessonplan = new Lessonplan();
                if($id)
                {
                   $lessonplan = $lessonplan->findByPk($id); 
                }    
                $lessonplan->subject_ids = $subject_ids;
                $lessonplan->batch_ids = $batch_ids;
                $lessonplan->lessonplan_category_id = $lessonplan_category_id;
                $lessonplan->title = $title;
                $lessonplan->is_show = $is_show;
                $lessonplan->publish_date = $publish_date;
                $lessonplan->content = $content;
                $lessonplan->school_id = $school_id;
                $lessonplan->author_id = $author_id;
                $lessonplan->created_at = $created_at;
                $lessonplan->updated_at = $updated_at;
                $lessonplan->save();
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

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->request->getPost('school');

            $term_id = Yii::app()->request->getPost('term');
            $batch_id = Yii::app()->request->getPost('batch_id');

            

            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {

                if (empty($school_id) || !isset($school_id) || $school_id == '') {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request.";
                    echo CJSON::encode($response);
                    Yii::app()->end();
                }

                if (!Yii::app()->user->isStudent && empty($batch_id)) {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request.";
                    echo CJSON::encode($response);
                    Yii::app()->end();
                }
                if(!$term_id)
                {
                    $term_id = 0;
                }

                $syllabus = new Syllabuses;
                $syllabus = $syllabus->getSyllabus($school_id, $term_id, $batch_id);

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
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request.";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionTerms() {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->request->getPost('school');
            $batch_id = Yii::app()->request->getPost('batch_id');
            $category_id = Yii::app()->request->getPost('category_id');

            if (Yii::app()->user->user_secret === $user_secret) {

                if (empty($school_id) || !isset($school_id) || $school_id == '') {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request.";
                    echo CJSON::encode($response);
                    Yii::app()->end();
                }

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

}
