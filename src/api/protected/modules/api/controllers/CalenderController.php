<?php

class CalenderController extends Controller {

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
                'actions' => array('getAttendence', 'academic','getbatch'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionGetAttendence() {
        if (isset($_POST) && !empty($_POST)) {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $start_date = Yii::app()->request->getPost('start_date');
            $end_date = Yii::app()->request->getPost('end_date');
            $yearly = false;
            if (!$start_date || !$end_date) {
                $start_date = date("Y-01-01");
                $end_date = date("Y-12-31");
                $yearly = true;
            }
            
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && $start_date != "" && $end_date != "" &&
                    ( Yii::app()->user->isStudent ||
                    (Yii::app()->user->isParent && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') && Yii::app()->request->getPost('school')))) {
                if (Yii::app()->user->isParent) {
                    $batch_id = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                    $school_id = Yii::app()->request->getPost('school');
                } else {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                    $school_id = Yii::app()->user->schoolId;
                }

                $attendance = new Attendances();
                $attendance_array = $attendance->getAbsentStudentMonth($start_date, $end_date, $student_id);
                $holiday = new Events();
                $holiday_array = $holiday->getHolidayMonth($start_date, $end_date, $school_id);
                $leave = new ApplyLeaveStudents();

                $leave_array = $leave->getleaveStudentMonth($start_date, $end_date, $student_id);


                if ($yearly) {

                    $late_count = count($attendance_array['absent']);
                    $absent_count = count($attendance_array['late']);

                    $holiday_count = 0;
                    $holiday_array_for_count = array();
                    foreach ($holiday_array as $value) {
                        $start_holiday = new DateTime($value['start_date']);
                        $end_holiday = new DateTime($value['end_date']);
                        $holiday_interval = DateInterval::createFromDateString('1 day');
                        $holiday_period = new DatePeriod($start_holiday, $holiday_interval, $end_holiday);
                        $holiday_count++;
                        $holiday_array_for_count[] = $value['start_date'];
                        foreach ($holiday_period as $hdt) {
                            $holiday_count++;
                            $holiday_array_for_count[] = $hdt->format("Y-m-d");
                        }
                    }
                    $leave_count = 0;
                    foreach ($leave_array as $value) {
                        $start_holiday = new DateTime($value['start_date']);
                        $end_holiday = new DateTime($value['end_date']);
                        $holiday_interval = DateInterval::createFromDateString('1 day');
                        $holiday_period = new DatePeriod($start_holiday, $holiday_interval, $end_holiday);
                        $leave_count++;

                        foreach ($holiday_period as $hdt) {
                            $leave_count++;
                        }
                    }

                    $begin = new DateTime(date("Y-1-1"));
                    $end = new DateTime(date("Y-m-d"));

                    $interval = DateInterval::createFromDateString('1 day');
                    $period = new DatePeriod($begin, $interval, $end);
                    $i = 0;

                    $weekend_array = $attendance->getWeekend(Yii::app()->user->schoolId);
                    foreach ($period as $dt) {

                        if (in_array($dt->format("Y-m-d"), $holiday_array_for_count)) {
                            continue;
                        }
                        if (in_array($dt->format("w"), $weekend_array)) {
                            continue;
                        }
                        $i++;
                    }
                }

                if ($yearly) {
                    $response['data']['absent'] = $absent_count;
                    $response['data']['late'] = $late_count;
                    $response['data']['holiday'] = $holiday_count;
                    $response['data']['leave'] = $leave_count;
                    $response['data']['current_date'] = date("Y-m-d");
                    $response['data']['total'] = $i;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                } else {
                    $response['data'] = $attendance_array;
                    $response['data']['holiday'] = $holiday_array;
                    $response['data']['leave'] = $leave_array;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                }
            } else {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionAcademic() {

        $response = array();
        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $school_id = Yii::app()->request->getPost('school');

            $from_date = Yii::app()->request->getPost('from_date');
            $from_date = (!empty($from_date)) ? $from_date : date('Y-01-01');

            $origin = Yii::app()->request->getPost('origin');
            $origin = (!empty($origin)) ? $origin : 0;

            $to_date = Yii::app()->request->getPost('to_date');
            $to_date = (!empty($to_date)) ? $to_date : date('Y-12-31');

            $page_no = Yii::app()->request->getPost('page_number');
            $page_no = (!empty($page_no)) ? $page_no : 1;

            $page_size = Yii::app()->request->getPost('page_size');
            $page_size = (!empty($page_size)) ? $page_size : 10;

            $batch_id = Yii::app()->request->getPost('batch_id');
            $batch_id = (!empty($batch_id)) ? $batch_id : NULL;

            if (Yii::app()->user->isParent && empty($batch_id)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->user_secret === $user_secret) {

                if (Yii::app()->user->isStudent) {
                    $batch_id = Yii::app()->user->batchId;
                }

                $events = new Events;
                $events = $events->getAcademicCalendar($school_id, $from_date, $to_date, $batch_id, $origin, $page_no, $page_size, false);

                if (!$events) {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "EVENTS_NOT_FOUND";
                } else {
                    
                    $response['data']['events'] = $events;

                    $events_cnt = new Events;
                    $response['data']['total'] = $events_cnt->getAcademicCalendar($school_id, $from_date, $to_date, $batch_id, $origin, $page_no, $page_size, TRUE);

                    $has_next = false;
                    if ($response['data']['total'] > $page_no * $page_size) {
                        $has_next = true;
                    }

                    $response['data']['has_next'] = $has_next;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EVENTS_FOUND";
                }
            } else {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied";
            }
        } else {

            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }
    public function actionGetBatch() 
    {
       
        $user_secret = Yii::app()->request->getPost('user_secret');
        if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
        {
            $url_end = "api/batches";
            $data = array("search[]"=>"");
            $batches = Settings::getDataApi($data,$url_end);
            $response['data'] = $batches;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "EVENTS_FOUND";
            
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
        
        
    }
    public function actionGetBatchStudent() 
    {
       
        $user_secret = Yii::app()->request->getPost('user_secret');
        $user_secret = Yii::app()->request->getPost('user_secret');
        if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
        {
            $url_end = "api/batches";
            $data = array("search[]"=>"");
            $batches = Settings::getDataApi($data,$url_end);
            $response['data'] = $batches;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "EVENTS_FOUND";
            
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
