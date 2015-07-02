<?php

class ApiModule extends CWebModule {

    public function init() {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'api.models.*',
            'api.components.*',
        ));
    }

    public function beforeControllerAction($controller, $action) {

        $controller_widthout_session = array("user","freeuser","freeschool","calender");
        if (!in_array($controller->id,$controller_widthout_session) && !isset(Yii::app()->user->user_secret)) {
            $response['status']['code'] = 406;
            $response['status']['msg'] = "SESSION_TIMEOUT";
            echo CJSON::encode($response);
            Yii::app()->end();
        } else if (parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else
            return false;
    }

}
