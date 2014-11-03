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
                'actions' => array('index', 'Getfullreport','acknowledge'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actionGetfullreport()
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
