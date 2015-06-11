<?php

class AmfController extends Controller {

	public function actionIndex() {

		$server = new Zend_Amf_Server();

                
                $session_id = "";
                $cookies = Yii::app()->request->cookies;
                if (isset($cookies['c21_session'])) 
                {
                    $session_id =  $cookies['c21_session']->value;
                }
		$server->setClass("Service","",$session_id);


		$handle = $server->handle();
		echo $handle;
	}
}