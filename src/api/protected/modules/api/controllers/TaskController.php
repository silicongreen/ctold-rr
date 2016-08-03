<?php

class TaskController extends Controller {

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
                'actions' => array('index', 'details'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex() {

        if (isset($_POST) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->request->getPost('school');
            $start_date = Yii::app()->request->getPost('start_date');
            $due_date = Yii::app()->request->getPost('due_date');
            $status = Yii::app()->request->getPost('status');

            $page_number = 1;
            $page_size = 10;

            $optional_params = array();
            if (!empty($start_date)) {
                $optional_params['start_date'] = $start_date;
            }

            if (!empty($due_date)) {
                $optional_params['due_date'] = $due_date;
            }

            if (!empty($status)) {
                $status = 'Assigned';
                if ($status == 1) {
                    $status = 'Completed';
                }
                $optional_params['status'] = $status;
            }

            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {

                $response['data'] = array();

                $tasks_assignee = new TaskAssignees;
                $response['data']['task_for_me'] = $tasks_assignee->getTasksToMe($school_id, $page_number, $page_size, $optional_params);

                $tasks = new Tasks;
                $response['data']['task_by_me'] = $tasks->getTasksByMe($school_id, $page_number, $page_size, $optional_params);

                if (empty($response['data']['task_for_me']) && empty($response['data']['task_by_me'])) {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "TASK_NOT_FOUND";
                } else {
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "ROUTINE_FOUND";
                }

                echo CJSON::encode($response);
                Yii::app()->end();
            }
        }
    }

    public function actionDetails() {

        if (isset($_POST) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->request->getPost('school');
            $bacth_id = Yii::app()->request->getPost('batch_id');
            $date = Yii::app()->request->getPost('date');
            $daily = Yii::app()->request->getPost('daily');


            $date = (!empty($date)) ? $date : \date('Y-m-d', \time());

            $weekly = ($daily == 1) ? false : true;

            if (Yii::app()->user->isParent && empty($bacth_id)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "BAD_REQUEST";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->isStudent) {
                $bacth_id = Yii::app()->user->batchId;
            }

            $response = array();
            if (Yii::app()->user->user_secret === $user_secret) {
                $time_table = new TimetableEntries;
                $time_table = $time_table->getTimeTables($school_id, $date, $weekly, $bacth_id);



                $response['data']['time_table'] = array();
                if ($time_table) {
                    $response['data']['time_table'] = $time_table;
                }
                $response['status']['code'] = 200;
                $response['status']['msg'] = "ROUTINE_FOUND";


                echo CJSON::encode($response);
                Yii::app()->end();
            }
        }
    }

}
