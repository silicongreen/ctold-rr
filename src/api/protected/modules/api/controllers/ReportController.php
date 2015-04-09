<?php
class ReportController extends Controller
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
                'actions' => array('index','getsubject','progress','classtestreport','allexam', 'Getfullreport','getexamreport','acknowledge'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionProgress()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $subject_id = Yii::app()->request->getPost('subject_id');
            $exam_category = Yii::app()->request->getPost('exam_category');
            $response = array();
            if ($subject_id && Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                } 
                if(!$exam_category)
                {
                    $exam_category = 0;
                }
                $subjects = new Subjects();
                $progress = $subjects->getPrograss($batch_id, $student_id, $subject_id, $exam_category);
                
                if ($progress)
                {
                    $response['data']['progress']    = $progress;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $response['data']['progress']    = array();
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Not Found";
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
    public function actionGetSubject()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }    
                $subjects = new Subjects();
                $std_subjects = $subjects->getSubject($batch_id, $student_id);
                
                if ($std_subjects)
                {
                    $response['data']['subjects']    = $std_subjects;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $response['data']['subjects']    = array();
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Not Found";
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
    
    public function actionGetExamReport() {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $category_id = Yii::app()->request->getPost('category_id');
            $id = Yii::app()->request->getPost('id');
            $response = array();
            if ($id && Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }   
                
                $subjects = new Subjects();
                $term_report = $subjects->getTermReport($batch_id, $student_id, $id);
                if ($term_report) {
                    $response['data']['report'] = $term_report[0];
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_FOUND";
                } else {
                    $response['data']['report'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_NOT_FOUND";
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
    
    public function actionAllExam() {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $category_id = Yii::app()->request->getPost('category_id');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }   
                if($category_id!=0 && !$category_id)
                {
                    $category_id = 3;
                }    
                $time_table = new ExamGroups();
                $time_table = $time_table->getAllExamsResultPublish($batch_id,$category_id);
                if ($time_table) {
                    $response['data']['all_exam'] = $time_table;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_FOUND";
                } else {
                    $response['data']['all_exam'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_FOUND";
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
    
    public function actionClassTestReport()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }    
                $subjects = new Subjects();
                $exam_data = $subjects->getBatchSubjectClassTestProjectReport($batch_id, $student_id);
                
                if ($exam_data)
                {
                    $response['data']['class_test_report']    = $exam_data;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $response['data']['class_test_report']    = array();
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Not Found";
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
    
    public function actionGetfullreport()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }    
                $subjects = new Subjects();
                $term_report = $subjects->getTermReport($batch_id, $student_id);
               
                $exam_data = $subjects->getBatchSubjectClassTestProjectReport($batch_id, $student_id);
                
                if ($exam_data || $term_report)
                {
                  
                    $response['data']['term_report']          = $term_report;
                    $response['data']['class_test_report']    = $exam_data;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $response['status']['code']       = 404;
                    $response['status']['msg']        = "Data Not Found";
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
    
     public function actionAcknowledge() {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->request->getPost('school');
            $exam_id = Yii::app()->request->getPost('exam_id');

            if (empty($exam_id)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request.";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->user_secret === $user_secret) {

                $exam = new UserExamAcknowledge;
                $exam_data = $exam->acknowledgeExam($exam_id);

                if ($exam_data) {
                    $response['data']['exam_ack'] = $exam_data;
                } else {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "NO_EXAM_ACKNOWLEDGED.";
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
