<?php

class DashboardController extends Controller
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
                'actions'=>array('index'),
                'users'=>array('*'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    
    public function actionIndex(){
        
        if(isset($_POST) && !empty($_POST)){
            
            $user_secret = Yii::app()->request->getPost('user_secret');
            $date = Yii::app()->request->getPost('date');
            $date = (!empty($date)) ? $date : \date('Y-m-d', \time());
            $school_id = Yii::app()->request->getPost('school');

            if(Yii::app()->user->user_secret === $user_secret){
                
                $users = new Users;
                
                if(Yii::app()->user->isParent){
                    
                    $children = $users->studentList(Yii::app()->user->profileId);
                    $ar_children_ids = Settings::extractIds($children, 'profile_id');
                    
                    $student_attendance = new Attendances;
                    $attendances = $student_attendance->getAbsentStudentMonth($date, $date, $ar_children_ids);
                    
                    if( !empty($attendances['absent']) || !empty($attendances['late']) ){
                        $response['data']['attendance'] = $attendances;
                    }
                }
                
                $birthdays = $users->getBirthDays($date, $school_id);
                
                if(Yii::app()->user->isStudent){
                    
                    $assignment = new Assignments();
                    $assignments_data = $assignment->getAssignment(Yii::app()->user->batchId, Yii::app()->user->profileId, $date);
                    
                    if(!empty($assignments_data)){
                        $response['data']['homework'] = $assignments_data;
                    }
                }
                
                if($birthdays){
                    $response['data']['birthday'] = $birthdays;
                }
                
                # Check: if student then batch ID will not be available and will be fixed
                # Check: if teacher or parent then batch ID will be available and will be changeable
        
                $school_id = ( (Yii::app()->user->isAdmin || Yii::app()->user->isParent) && !empty($school_id) ) ? $school_id = $school_id : Yii::app()->user->schoolId;
                
                $time_table = new TimetableEntries;
                $time_table = $time_table->getTimeTables($school_id, $date);

                if($time_table){
                    $response['data']['time_table'] = $time_table;
                    $response['data']['current_weekday'] = Settings::getCurrentDay($date);
                }
                
                $news = new News;
                $news = $news->getNews($school_id, $date, $date);
                
                if($news !== FALSE){
                    $response['data']['notice'] = $news;
                }
                
                $events = new Events;
                $events = $events->getEvents($school_id, $date, $date);
                
                if($events !== false){
                    $response['data']['events'] = $events;
                }
                
            }else{
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        }else{
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
}