<?php

class AmfController extends Controller {

	public function actionIndex() {

		$server = new Zend_Amf_Server();

        // Enable production mode if environment is 'production'.
//		if (Yii::app()->params['environment'] == 'production') {
//			$server->setProduction(true);
//		} else {
//			$server->setProduction(false);
//		}
                
//                $cookies = Yii::app()->request->cookies;
//                if (isset($cookies['c21_session'])) 
//                {
//                    echo $cookies['c21_session']->value;
//                }
//                if(isset(Yii::app()->user->free_id_flash))
//                {
//                    $data = Yii::app()->user->free_id_flash;
//                }
//                echo $data;
                $cookies = Yii::app()->request->cookies;
                $server->setRequest($cookies['c21_session']->value);

                #$server->set();
		// Add our class to Zend AMF Server.
		$server->setClass("Service");

		// Mapping the ActionScript VO to the PHP VO. You don't have to add the package name.
		//$server->setClassMap("VOApplication", "Application");

		$handle = $server->handle();
		echo $handle;
	}
}