<?php

class FreeschoolController extends Controller {

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
                'actions' => array('getbanner', 'create', 'assign', 'getschool', 'getassignschool','checkcard'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionCheckCard() {
        $card_number = array("11275186" => "71", "7848069" => "72");

        $card_num = Yii::app()->request->getPost('card_num');
        if (isset($card_number[$card_num])) {
            $student_id = $card_number[$card_num];
            $reminderrecipients = array();
            $batch_ids = array();
            $student_ids = array();
            $studentobj = new Students();
            $studentdata = $studentobj->findByPk($student_id);
            $reminderrecipients[] = $studentdata->user_id;
            $batch_ids[$studentdata->user_id] = $studentdata->batch_id;
            $student_ids[$studentdata->user_id] = $studentdata->id;
            $gstudent = new GuardianStudent();

            $all_g = $gstudent->getGuardians($student_id);

            if ($all_g) {
                foreach ($all_g as $value) {
                    if(isset($value['guardian']) && isset($value['guardian']->id))
                    {
                        $gr = new Guardians();
                        $grdata = $gr->findByPk($value['guardian']->id);
                        if ($grdata && $grdata->user_id && !in_array($grdata->user_id, $reminderrecipients)) {
                            $reminderrecipients[] = $grdata->user_id;
                            $batch_ids[$grdata->user_id] = $studentdata->batch_id;
                            $student_ids[$grdata->user_id] = $studentdata->id;
                        }
                        
                    }    
                }
            }


            if ($reminderrecipients) {
                $notification_ids = array();
                foreach ($reminderrecipients as $value) {
                    $reminder = new Reminders();
                    $reminder->sender = 314;
                    $reminder->recipient = $value;
                    $reminder->subject = "Card Test";
                    $reminder->body = "Card Test Successfull";
                    $reminder->created_at = date("Y-m-d H:i:s");
                    $reminder->rid = 102;
                    $reminder->rtype = 9;
                    $reminder->batch_id = $batch_ids[$value];
                    $reminder->student_id = $student_ids[$value];

                    $reminder->updated_at = date("Y-m-d H:i:s");
                    $reminder->school_id = $studentdata->school_id;
                    $reminder->save();
                    $notification_ids[] = $reminder->id;
                }
                $notification_id = implode(",", $notification_ids);
                $user_id = implode(",", $reminderrecipients);
                Settings::sendCurlNotification($user_id, $notification_id);

                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success";
            }
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetSchool() {
        $term = Yii::app()->request->getPost('term');
        if (!$term) {
            $term = "";
        }
        $school = new School();
        $response['data']['schools'] = $school->getSchoolNotPaid($term);
        $response['status']['code'] = 200;
        $response['status']['msg'] = "SCHOOL_DATA";
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionGetAssignSchool() {
        $paid_school_id = Yii::app()->request->getPost('paid_school_id');
        if (!$paid_school_id) {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        } else {
            $schoolobj = new School();
            $previous_school_id = $schoolobj->getSchoolPaid($paid_school_id);
            $assing = array();
            if ($previous_school_id) {
                $previous_school = $schoolobj->findByPk($previous_school_id);
                $assing['id'] = $previous_school->id;
                $assing['name'] = $previous_school->name;
            }
            $response['data']['assing'] = $assing;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "SCHOOL_DATA";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionAssign() {
        $name = Yii::app()->request->getPost('name');
        $code = Yii::app()->request->getPost('code');
        $paid_school_id = Yii::app()->request->getPost('paid_school_id');
        $free_school_id = Yii::app()->request->getPost('free_school_id');



        if (!$name || !$code || !$paid_school_id || !$free_school_id) {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        } else {

            $schoolobj = new School();


            $previous_school_id = $schoolobj->getSchoolPaid($paid_school_id);

            if ($previous_school_id) {
                $previous_school = $schoolobj->findByPk($previous_school_id);

                $previous_school->is_paid = 0;
                $previous_school->code = null;
                $previous_school->paid_school_id = null;
                $previous_school->save();
            }

            $school = $schoolobj->findByPk($free_school_id);

            if (isset($school->id) && $school->id) {
                $school->paid_school_id = $paid_school_id;
                $school->name = $name;
                $school->code = $code;
                $school->is_paid = 1;
                $school->save();
            }
            $response['status']['code'] = 200;
            $response['status']['msg'] = "SCHOOL_SAVED";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actioncreate() {
        $name = Yii::app()->request->getPost('name');
        $code = Yii::app()->request->getPost('code');
        $location = Yii::app()->request->getPost('location');
        $paid_school_id = Yii::app()->request->getPost('paid_school_id');



        if (!$name || !$code || !$paid_school_id) {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        } else {
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

    public function actionGetbanner() {
        $school_id = Yii::app()->request->getPost('school_id');

        if (!$school_id) {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "BAD_REQUEST";
        } else {
            $school = new School();
            $school = $school->getFreeSchoolByPaidId($school_id, array('t.id, t.logo, t.cover'));

            if (!$school) {
                $response['data'] = NULL;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "NO_SCHOOL_FOUND";
            } else {
                $response['data'] = (!empty($school['cover'])) ? $school : NULL;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "SUCCESS";
            }
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

}
