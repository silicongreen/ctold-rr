<?php
class HomeworkController extends Controller
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
                'actions' => array('index', 'Done', 'getproject'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    public function actiongetproject()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )))
            {
                if(Yii::app()->user->isParent)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }    
                $assignment = new Assignments();
                
                $page_number = Yii::app()->request->getPost('page_number');
                
                $page_size  = Yii::app()->request->getPost('page_size');
                
                $subject_id  = Yii::app()->request->getPost('subject_id');
                
                if(empty($page_number))
                {
                    $page_number = 1;
                }
                if(empty($page_size))
                {
                    $page_size = 10;
                }
                
                if(empty($subject_id))
                {
                    $subject_id = NULL;
                }
                
                $homework_data = $assignment->getAssignment($batch_id, $student_id, "", $page_number, $subject_id, $page_size,2);
                if ($homework_data)
                {
                  
                    $response['data']['total']       = $assignment->getAssignmentTotal($batch_id, $student_id,"",$subject_id,2);
                    $has_next = false;
                    if($response['data']['total']>$page_number*$page_size)
                    {
                        $has_next = true;
                    }
                    $response['data']['has_next']    = $has_next;
                    $response['data']['homework']    = $homework_data;
                    $response['status']['code']      = 200;
                    $response['status']['msg']       = "Data Found";
                    
                }
                else
                {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "Data Not Found";
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
    public function actionIndex()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )))
            {
                if(Yii::app()->user->isParent)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }    
                $assignment = new Assignments();
                
                $page_number = Yii::app()->request->getPost('page_number');
                
                $page_size = Yii::app()->request->getPost('page_size');
                
                $subject_id  = Yii::app()->request->getPost('subject_id');
                
                if(empty($page_number))
                {
                    $page_number = 1;
                }
                if(empty($page_size))
                {
                    $page_size = 10;
                }
                
                if(empty($subject_id))
                {
                    $subject_id = NULL;
                }
                
                $homework_data = $assignment->getAssignment($batch_id, $student_id, "", $page_number, $subject_id, $page_size,1);
                if ($homework_data)
                {
                  
                    $response['data']['total']       = $assignment->getAssignmentTotal($batch_id, $student_id,"",$subject_id,1);
                    $has_next = false;
                    if($response['data']['total']>$page_number*$page_size)
                    {
                        $has_next = true;
                    }
                    $response['data']['has_next']    = $has_next;
                    $response['data']['homework']    = $homework_data;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                }
                else
                {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "Data Not Found";
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
    public function actionDone()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $assignment_id = Yii::app()->request->getPost('assignment_id');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && $assignment_id != "" && Yii::app()->user->isStudent)
            {
                $assignment_answer = new AssignmentAnswers();
                $assignment_answer->assignment_id = $assignment_id;
                $assignment_answer->student_id = Yii::app()->user->profileId;
                $assignment_answer->title = "Done";
                $assignment_answer->content = "Please Accept";
                $assignment_answer->status = "ACCEPTED";
                $assignment_answer->created_at = date("Y-m-d H:i:s");
                $assignment_answer->school_id = Yii::app()->user->schoolId;
                $assignment_answer->insert();
                $assignment = new Assignments();
               
                //$homework_data = $assignment->getAssignment(Yii::app()->user->batchId, Yii::app()->user->profileId);
                //if ($homework_data)
                //{
                    
                   
                    $response['status']['code']     = 200;
                    $response['status']['msg']      = "Data Found";
                //}
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
}
