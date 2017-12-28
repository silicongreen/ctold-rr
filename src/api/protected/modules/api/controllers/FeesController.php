<?php

class FeesController extends Controller
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
                'actions' => array('fees'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    public function actionFees()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');

        $student_id = Yii::app()->request->getPost('student_id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isParent && $student_id)
        {
            $objFees = new FinanceFees();

            $response['data']['due'] = $objFees->feesStudentDue($student_id);
            $response['data']['history'] = $objFees->feesStudentDueHistory($student_id);
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

    

}
