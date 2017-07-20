<?php

class AcacalController extends Controller {

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
                'actions' => array('index', 'downloadattachment'),
                'users' => array('@'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('downloadattachment'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

   

    public function actionDownloadAttachment() {
        $id = Yii::app()->request->getParam('id');
        
        if ($id) {
            $acacal = new Acacals();
            $acacalObj = $acacal->findByPk($id);
            if ($acacalObj && $acacalObj->attachment_file_name) {
                $attachment_datetime_chunk = explode(" ", $acacalObj->updated_at);

                $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
                $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

                $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
                $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];
                
                $url = Settings::$acacal_attachment_path . $id . "/original/" . str_replace(")", "%29", str_replace("(", "%28", str_replace(" ", "+", $acacalObj->attachment_file_name))) . "?" . $attachment_extra;
                
                $url = str_replace("&", "%26",$url);
                if (file_exists($url)) {
                    return Yii::app()->getRequest()->sendFile($acacalObj->attachment_file_name, @file_get_contents($url));
                }
                else
                {
                    echo $url;
                }    

            }
        }
    }
    
    

    public function actionIndex() {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            
            $batch_id = Yii::app()->request->getPost('batch_id');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {
                $acacal = new Acacals();
                $response['data']['acacal'] = $acacal->getAcacal($batch_id);
                $response['status']['code'] = 200;
                $response['status']['msg'] = 'NOTICE_FOUND.';
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
