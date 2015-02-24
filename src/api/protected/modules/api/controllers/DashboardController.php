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
                'actions'=>array('index','gethome'),
                'users'=>array('*'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }
    public function actionGetHome()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $date = Yii::app()->request->getPost('date');
        $date = (!empty($date)) ? $date : \date('Y-m-d', \time());
        $student_id = Yii::app()->request->getPost('student_id');
        $student_user_id = Yii::app()->request->getPost('student_user_id');
        if(Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent && $student_id && $student_user_id)))
        {
            if (Yii::app()->user->isStudent)
            {
                $student_id = Yii::app()->user->profileId;
            }
            $stdObject = new Students();
            $std_data = $stdObject->findByPk($student_id);
            $response = array();
            $school_id = Yii::app()->request->getPost('school');
            $response['data']['current_date'] = date("Y-m-d");
            
            
            
            $lastvisited = new LastVisited();
            $last_visited = $lastvisited->getLastVisited();
            $lastvisited->addLastVisited();
            
            if(!$last_visited)
            {
              $last_visited = "";  
            }
            $response['data']['last_visited'] = $last_visited;
            
            
            //attendence start
            $objattendence = new Attendances();
            $today_text = "Today";
            if($date!=date("Y-m-d"))
            {
                $today_text = "At ".$date;
            }
            $attendence_return = false;
            $holiday = new Events();
            $holiday_array = $holiday->getHolidayMonth($date, $date, $school_id);
            if($holiday_array)
            {
               if($date!=date("Y-m-d"))
               {
                  $today_text = $date;
               }
               $response['data']['attendence'] = $today_text." is Holiday (".$holiday_array[0]['title'].")"; 
               $attendence_return = true;
            }
            
            if(!$attendence_return)
            {
                $weekend_array = $objattendence->getWeekend($school_id);
                $weekday = date("w",  strtotime($date));
                if (in_array($weekday, $weekend_array))
                {
                    if($date!=date("Y-m-d"))
                    {
                        $today_text = $weekday;
                    } 
                    $response['data']['attendence'] = $today_text." is weekend";
                    $attendence_return = true;
                }
            }
            if(!$attendence_return)
            {
                $leave = new ApplyLeaveStudents();
                $leave_array = $leave->getleaveStudentMonth($date, $date, $student_id);
                if($leave_array)
                {
                   $response['data']['attendence'] = $today_text." ".$std_data->first_name." ".$std_data->last_name." on leave"; 
                   $attendence_return = true;
                }
                if(!$attendence_return)
                {
                    
                    $attendance_array = $attendance->getAbsentStudentMonth($date, $date, $student_id);
                    if($attendance_array['late'])
                    {
                       $response['data']['attendence'] =  $today_text." ".$std_data->first_name." ".$std_data->last_name." is Late"; 
                       $attendence_return = true; 
                    }
                    else if($attendance_array['absent'])
                    {
                       $response['data']['attendence'] =  $today_text." ".$std_data->first_name." ".$std_data->last_name." is Absent"; 
                       $attendence_return = true; 
                    }
                    else
                    {
                       $response['data']['attendence'] =  $today_text." ".$std_data->first_name." ".$std_data->last_name." is Present"; 
                       $attendence_return = true; 
                        
                    }
                    
                }
            } 
            
            //attendence end
            
            
            
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";   
        }
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