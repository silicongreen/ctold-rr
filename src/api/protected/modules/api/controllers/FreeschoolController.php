<?php

class FreeschoolController extends Controller
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
                'actions' => array('getbanner'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    public function actionGetbanner()
    {
        $school_id = Yii::app()->request->getPost('school_id');
        
        if (!$school_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        else
        {       
            $school = new School();
            $school = $school->getFreeSchoolByPaidId($school_id, array('t.id, t.logo, t.cover'));
            
            if(!$school)
            {
                $response['status']['code'] = 404;
                $response['status']['msg'] = "NO_DATA_FOUND";
            }
            else
            {
                $response['data'] = (!empty($school['cover'])) ? $school : NULL;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "success";
            }
        }
        
        echo CJSON::encode($response);
        Yii::app()->end();
    }        

}
