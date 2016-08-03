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
        $controller_widthout_session = array("user", "freeuser", "freeschool", "spelltv","sciencerocks", 'notice','paid','cardatt');
        if (!in_array($controller->id, $controller_widthout_session) && !isset(Yii::app()->user->user_secret)) {
            $temp = (int) isset(Yii::app()->user->user_secret);

            $response['status']['code'] = 406;
            $response['status']['msg'] = "SESSION_TIMEOUT" . $temp . ",cid = " . $controller->id;
            echo CJSON::encode($response);
            Yii::app()->end();
        } else if (parent::beforeControllerAction($controller, $action)) {
            
            $call_from_web = Yii::app()->request->getPost('call_from_web');

            if ((!isset($call_from_web) || $call_from_web!=1) && !in_array($controller->id, $controller_widthout_session) && isset(Yii::app()->user->user_secret) && Yii::app()->user->user_secret && isset(Yii::app()->user->schoolId) && Yii::app()->user->schoolId &&
                    isset(Yii::app()->user->id) && Yii::app()->user->id) {
                $array_to_search = array();
                foreach (Settings::$change_name_cm as $key => $value) {
                    $array_to_search[strtolower($key)] = strtolower($value);
                }
                if (isset($action->id) && $action->id) {
                    $action_name = $action->id;
                } else {
                    $action_name = "index";
                }
                $controller_method = $controller->id . " " . $action->id;
                if (array_key_exists($controller_method, $array_to_search)) {
                    $controller_method_proper_name = explode(" ", $array_to_search[$controller_method]);
                    $controller_name = $controller_method_proper_name[0];
                    if (isset($controller_method_proper_name[1])) {
                        $action_name = $controller_method_proper_name[1];
                    } else {
                        $action_name = "index";
                    }
                    $session_end_time_diff = 15;
                    $sesstion_time = 0;
                    $activityLog = new ActiveLogs();
                    $last_log = $activityLog->find(array('order' => 'created_at DESC', 'condition' => 'user_id=:x', 'params' => array(':x' => Yii::app()->user->id)));
                    if ($last_log) {
                        $now = time();
                        $from_time = ( isset($last_log->created_at) && !empty($last_log->created_at) ) ? strtotime($last_log->created_at) : $now;
                        $time_last_log = round(($now - $from_time) / 60);
                      
                        if ($time_last_log > $session_end_time_diff and $last_log->session_end != 1) {
                            $last_session_log = $activityLog->find(array('order' => 'created_at DESC', 'condition' => 'user_id=:x and session_end=1', 'params' => array(':x' => Yii::app()->user->id)));

                            if ($last_session_log) {
                                $session_start_log = $activityLog->find(array('order' => 'created_at ASC', 'condition' => 'user_id=:x and created_at>:y', 'params' => array(':x' => Yii::app()->user->id, 'y' => $last_session_log->created_at)));
                                $from_time = ( isset($session_start_log->created_at) && !empty($session_start_log->created_at) ) ? strtotime($session_start_log->created_at) : $now;
                                $sesstion_time = $now - $from_time;
                                $activity_log_update = $activityLog->findByPk($last_log->id);
                                $activity_log_update->session_end = 1;
                                $activity_log_update->session_time = $sesstion_time;
                                $activity_log_update->save();
                            } else {
                                $last_session_log = $activityLog->find(array('order' => 'created_at ASC', 'condition' => 'user_id=:x', 'params' => array(':x' => Yii::app()->user->id)));
                                if ($last_session_log) {
                                    $from_time = ( isset($session_start_log->created_at) && !empty($session_start_log->created_at) ) ? strtotime($session_start_log->created_at) : $now;
                                    $sesstion_time = $now - $from_time;
                                    $activity_log_update = $activityLog->findByPk($last_log->id);
                                    $activity_log_update->session_end = 1;
                                    $activity_log_update->session_time = $sesstion_time;
                                    $activity_log_update->save();
                                }
                            }
                        }
                    }

                    $activelog = new ActiveLogs();
                    $activelog->user_id = Yii::app()->user->id;
                    $activelog->school_id = Yii::app()->user->schoolId;
                    $activelog->controller = $controller_name;
                    $activelog->action = $action_name;
                    $activelog->ip = "From Mobile";
                    $activelog->using_web = 0;
                    $activelog->user_agent = "Android";
                    $activelog->created_at = date("Y-m-d H:i:s");
                    $activelog->updated_at = date("Y-m-d H:i:s");
                    if (Yii::app()->user->isAdmin) {
                        $activelog->user_type_paid = 4;
                    } else if (Yii::app()->user->isTeacher) {
                        $activelog->user_type_paid = 3;
                    } else if (Yii::app()->user->isParent) {
                        $activelog->user_type_paid = 2;
                    } else if (Yii::app()->user->isStudent) {
                        $activelog->user_type_paid = 1;
                    }
                    $activelog->save();
                }
                $controller_name = 0;
            }

            // echo $action->name;
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        } else
            return false;
    }

}
