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
                'actions' => array('getbanner','create','assign','getschool'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    
    public function actionGetSchool()
    {
        $term = Yii::app()->request->getPost('term');
        if(!$term)
        {
          $term = ""; 
        }    
        $school = new School();
        $response['data']['schools'] = $school->getSchoolNotPaid($term);
        $response['status']['code'] = 200;
        $response['status']['msg'] = "SCHOOL_SAVED";
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    public function actionAssign()
    {
        $name = Yii::app()->request->getPost('name');
        $code = Yii::app()->request->getPost('code');
        $paid_school_id = Yii::app()->request->getPost('paid_school_id');
        $free_school_id = Yii::app()->request->getPost('free_school_id');
        
        
        
        if (!$name && !$code && $paid_school_id && !$free_school_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }
        else
        {       
            $schoolobj = new School();
            
            $school = $schoolobj->findByPk($free_school_id);
           
            $school->paid_school_id = $paid_school_id;
            $school->name = $name;
            $school->code = $code;
            $school->is_paid = 1;
            
            $school->save();
            $response['status']['code'] = 200;
            $response['status']['msg'] = "SCHOOL_SAVED";
            
        }
        
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    public function actioncreate()
    {
        $name = Yii::app()->request->getPost('name');
        $code = Yii::app()->request->getPost('code');
        $location = Yii::app()->request->getPost('location');
        $paid_school_id = Yii::app()->request->getPost('paid_school_id');
        
        
        
        if (!$name && !$code && $paid_school_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }
        else
        {       
            $school = new School();
           
            $school->paid_school_id = $paid_school_id;
            $school->name = $name;
            $school->location = $location;
            $school->code = $code;
            $school->is_paid = 1;
            
            $school->save();
            $response['status']['code'] = 200;
            $response['status']['msg'] = "SCHOOL_SAVED";
            
        }
        
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    public function actionGetbanner()
    {
        $school_id = Yii::app()->request->getPost('school_id');
        
        if (!$school_id)
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        }
        else
        {       
            $school = new School();
            $school = $school->getFreeSchoolByPaidId($school_id, array('t.id, t.logo, t.cover'));
            
            if(!$school)
            {
                $response['data'] = NULL;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "NO_SCHOOL_FOUND";
            }
            else
            {
                $response['data'] = (!empty($school['cover'])) ? $school : NULL;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "SUCCESS";
            }
        }
        
        echo CJSON::encode($response);
        Yii::app()->end();
    }        

}
