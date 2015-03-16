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
                'actions' => array('index', 'gethome'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

   

    private function getSingleNewsFromCache($id)
    {
        $cache_name = "YII-SINGLE-POST-CACHE-" . $id;
        if (!$singlepost = Yii::app()->cache->get($cache_name))
        {
            $postModel = new Post();
            $singlepost = $postModel->getSinglePost($id);
            Yii::app()->cache->set($cache_name, $singlepost, 5184000);
        }
        return $singlepost;
    }

    public function actionGetHome()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');

        $student_id = Yii::app()->request->getPost('student_id');
        $batch_id = Yii::app()->request->getPost('batch_id');

        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        $id = Yii::app()->request->getPost('id');
        $target = Yii::app()->request->getPost('target');
        $user_id = Yii::app()->request->getPost('user_id');

        if ($target && $id && Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isStudent || (Yii::app()->user->isParent && $student_id && $batch_id)))
        {

            $post_data = array();
            if (empty($page_number))
            {
                $page_number = 1;
            }
            if (empty($page_size))
            {
                $page_size = 10;
            }

            $user_id = Yii::app()->request->getPost('user_id');
            if (!$user_id)
            {
                $user_type = 1;
            }
            else
            {
                $freeuserObj = new Freeusers();
                $user_info = $freeuserObj->getUserInfo($user_id);
                $user_type = $user_info['user_type'];
            }
            if ($page_number == 1 && !Yii::app()->user->isTeacher)
            {
                
                if (Yii::app()->user->isStudent)
                {
                    $student_id = Yii::app()->user->profileId;
                    $batch_id = Yii::app()->user->batchId;
                }
                $user_id = Yii::app()->user->id;
                $school_id = Yii::app()->user->schoolId;


                //school info

                $school_obj = new Schools();
                $school_details = $school_obj->findByPk($school_id);
                $response['maindata']['post_type'] = "20";
                $response['maindata']['school_name'] = $school_details->name;
                $response['maindata']['school_picture'] = "";

                $freeschool = new School();
                $freeschoolid = $freeschool->getSchoolPaid($school_id);
                if ($freeschoolid)
                {
                    $freeschooldetails = $freeschool->findByPk($freeschoolid);
                    if (isset($freeschooldetails->cover) && $freeschooldetails->cover)
                    {
                        $response['maindata']['school_picture'] = Settings::$image_path . $freeschooldetails->cover;
                    }
                }

                //end school info
                //student profile

                $response['maindata']['profile_picture'] = "";
                $freobj = new Freeusers();
                $profile_image = $freobj->getUserImage($user_id);
                if (isset($profile_image['profile_image']) && $profile_image['profile_image'])
                {
                    $response['maindata']['profile_picture'] = $profile_image['profile_image'];
                }

                //student profile end
                //current day

                $response['maindata']['current_date'] = date("D d-m-Y");

                //current day
                //Last Visited
                $response['maindata']['last_visited']['first'] = "";
                $response['maindata']['last_visited']['number'] = "Today";
                $response['maindata']['last_visited']['type'] = "";

                $lastvisited = new LastVisited();
                $last_visited = $lastvisited->getLastVisited();
                $lastvisited->addLastVisited();

                if ($last_visited)
                {
                    $visitedarray = explode(" ", $last_visited);
                    $response['maindata']['last_visited']['first'] = "Last Visited";
                    $response['maindata']['last_visited']['number'] = $visitedarray[0];
                    $response['maindata']['last_visited']['type'] = $visitedarray[1];
                }


                //Last Visited

                $date_today = date('d');
                $date_month_name = date('F');

                $date_array = array();

                $date_array[0]['index'] = 0;
                $date_array[0]['number'] = $date_today;
                $date_array[0]['name'] = $date_month_name;
                $date_array[0]['dateformat'] = date("Y-m-d");
                for ($i = 1; $i < 6; $i++)
                {
                    $date_array[$i]['index'] = $i;
                    $date_array[$i]['number'] = date('d', strtotime("-" . $i . " days"));
                    $date_array[$i]['name'] = date('F', strtotime("-" . $i . " days"));
                    $date_array[$i]['dateformat'] = date('Y-m-d', strtotime("-" . $i . " days"));
                }
                $response['maindata']['dates'] = $date_array;

                foreach ($date_array as $dvalue)
                {
                    
                    $date = $dvalue['dateformat'];
                    
                    $cache_name = "DASHBOARD-" . $date . "-" .  $user_id;
                    $merging_data = Yii::app()->cache->get($cache_name);

                    if(!$merging_data)
                    {
                        $merging_data = array();
                        $tommmorow = date('Y-m-d', strtotime($date . "+1 days"));


                        //attendence start
                        $stdObject = new Students();
                        $std_data = $stdObject->findByPk($student_id);

                        $merging_data['student_name'] = $std_data->first_name;

                        $objattendence = new Attendances();

                        $attendence_return = false;
                        $holiday = new Events();
                        $holiday_array = $holiday->getHolidayMonth($date, $date, $school_id);
                        if ($holiday_array)
                        {
                            $merging_data['attendence'] = "Today is Holiday"; //(".$holiday_array[0]['title'].")
                            $attendence_return = true;
                        }

                        if (!$attendence_return)
                        {
                            $weekend_array = $objattendence->getWeekend($school_id,$batch_id);
                            $weekday = date("w", strtotime($date));
                            if (in_array($weekday, $weekend_array))
                            {
                                $merging_data['attendence'] = "Today is weekend";
                                $attendence_return = true;
                            }
                        }
                        if (!$attendence_return)
                        {
                            $leave = new ApplyLeaveStudents();
                            $leave_array = $leave->getleaveStudentMonth($date, $date, $student_id);
                            if ($leave_array)
                            {
                                $merging_data['attendence'] = "was on Leave Today";
                                $attendence_return = true;
                            }
                            if (!$attendence_return)
                            {

                                $attendance_array = $objattendence->getAbsentStudentMonth($date, $date, $student_id);
                                if ($attendance_array['late'])
                                {
                                    $merging_data['attendence'] = "was Late Today";
                                    $attendence_return = true;
                                }
                                else if ($attendance_array['absent'])
                                {
                                    $merging_data['attendence'] = "was Absent Today";
                                    $attendence_return = true;
                                }
                                else
                                {
                                    $timetableobj = new TimetableEntries();
                                    $class_started = $timetableobj->classStarted($batch_id);
                                    if($class_started)
                                    {
                                        $merging_data['attendence'] = "was Present Today";
                                    }
                                    else if($date==date("Y-m-d"))
                                    {
                                        $merging_data['attendence'] = "Class yet not started";
                                    }    
                                    $attendence_return = true;
                                }
                            }
                        }

                        //attendence end
                        //tomomorow class
                        $cur_day_name = Settings::getCurrentDay($tommmorow);
                        $day_id = Settings::$ar_weekdays_key[$cur_day_name];
                        $time_table = new TimetableEntries;
                        $time_table = $time_table->getTimeTables($school_id, $tommmorow, true, $batch_id, $day_id);
                        $merging_data['class_tommrow'] = false;
                        if ($time_table)
                        {
                            $merging_data['class_tommrow'] = true;
                        }

                        //tomomorow class end
                        //homework start

                        $assignment = new Assignments();
                        $homework_data = $assignment->getAssignmentSubject($batch_id, $student_id, $tommmorow);

                        $merging_data['homework_subject'] = array();
                        $sub_array = array();
                        if ($homework_data)
                        {
                            $i = 0;
                            foreach ($homework_data as $value)
                            {
                                if (!in_array($value['subjects_id'], $sub_array))
                                {
                                    $merging_data['homework_subject'][$i]['id'] = $value['subjects_id'];
                                    $merging_data['homework_subject'][$i]['name'] = $value['subjects'];
                                    $merging_data['homework_subject'][$i]['icon'] = $value['subjects_icon'];
                                    $i++;
                                    $sub_array[] = $value['subjects_id'];
                                }
                            }
                        }

                        //homework end
                        //Result Published
                        $merging_data['result_publish'] = "";


                        $objReminder = new Reminders();
                        $result = $objReminder->getUserReminderNew($user_id, 1, 10, $date, 3);
                        if ($result && isset($result[0]['rid']) && $result[0]['rid'])
                        {
                            $exam = new ExamGroups();
                            $examsdata = $exam->findByPk($result[0]['rid']);
                            if ($examsdata)
                            {
                                $merging_data['result_publish'] = $examsdata->name;
                            }
                        }

                        //Result Published end
                        //Routine Published
                        $merging_data['routine_publish'] = "";


                        $objReminder = new Reminders();
                        $result = $objReminder->getUserReminderNew($user_id, 1, 10, $date, 2);
                        if ($result && isset($result[0]['rid']) && $result[0]['rid'])
                        {
                            $exam = new ExamGroups();
                            $examsdata = $exam->findByPk($result[0]['rid']);
                            if ($examsdata)
                            {
                                $merging_data['routine_publish'] = $examsdata->name;
                            }
                        }

                        //Routine Published end
                        //start_event

                        $objEvent = new Events();
                        $event_data = $objEvent->getAcademicCalendar($school_id, $tommmorow, $tommmorow, $batch_id, 0, 1, 10, false, true);
                        $merging_data['event_tommorow'] = false;
                        if ($event_data)
                        {
                            $merging_data['event_tommorow'] = true;
                            $merging_data['event_id'] = $event_data[0]['event_id'];
                            $merging_data['event_name'] = $event_data[0]['event_title'];
                        }

                        //end_event
                        //exam start
                        $objExam = new Exams();
                        $examdata = $objExam->getExamTimeTable($school_id, $batch_id, $student_id, NULL, $tommmorow);
                        $merging_data['exam_tommorow'] = false;
                        if ($examdata)
                        {
                            $merging_data['exam_tommorow'] = true;
                        }
                        //end exam

                        if (Yii::app()->user->isParent)
                        {
                            $objReminder = new Reminders();
                            $meeting_request = $objReminder->getUserReminderNew($user_id, 1, 10, $date, 13);
                            $merging_data['meeting_request'] = $meeting_request;

                            $objReminder = new Reminders();
                            $leave = $objReminder->getUserReminderNew($user_id, 1, 10, $date, 10);
                            $merging_data['leave'] = $leave;

                            $objFees = new FinanceFees();
                            $fees = $objFees->feesStudentDue($student_id);
                            $merging_data['fees'] = false;
                            if ($fees)
                            {
                                $merging_data['fees'] = true;
                            }
                        }



                        //Notice start
                        $newsobj = new News();
                        $notice = $newsobj->getNews($school_id, $date, $date);
                        $merging_data['notice'] = false;
                        if ($notice)
                        {
                            $merging_data['notice'] = true;
                        }
                        //Notice end
                        //Quiz start
                        $onlineExamObj = new OnlineExamGroups();
                        $onlineexamData = $onlineExamObj->getOnlineExamSubject($batch_id,$date);

                        $merging_data['quiz'] = array();
                        $sub_array = array();
                        if ($onlineexamData)
                        {
                            $i = 0;
                            foreach ($onlineexamData as $value)
                            {
                                if (!in_array($value['subjects_id'], $sub_array))
                                {
                                    $merging_data['quiz'][$i]['id'] = $value['subjects_id'];
                                    $merging_data['quiz'][$i]['name'] = $value['subjects'];
                                    $merging_data['quiz'][$i]['icon'] = $value['subjects_icon'];
                                    $i++;
                                    $sub_array[] = $value['subjects_id'];
                                }
                            }
                        }
                        if($date<date("Y-m-d"))
                        {
                            Yii::app()->cache->set($cache_name, $merging_data, 2592000);
                        }    

                    }

                    $response['maindata']['datefeed'][] = $merging_data;
                }
                $post_data[0] = $response['maindata'];
                unset($response['maindata']);
            }
            $postObj = new Post();
            $post = $postObj->getPosts($id, $user_type, $target, $page_number, $page_size);

            $response['data']['total'] = $postObj->getPostTotal($id, $user_type, $target);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }
            $response['data']['has_next'] = $has_next;
            $response['data']['post'] = $post;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
            
            if (isset($response['data']['post']) && count($response['data']['post']) > 0)
            {

                $wow = array();
                if ($user_id)
                {
                    $obj_wow = new Wow();
                    $wow = $obj_wow->userwow($user_id);
                }

                
                if ($page_number == 1 && !Yii::app()->user->isTeacher)
                {
                    $i = 1;
                }
                else
                {
                    $i = 0;
                }
                foreach ($response['data']['post'] as $value)
                {
                    $post_data[$i] = $this->getSingleNewsFromCache($value['id']);
                    $post_data[$i]['can_wow'] = 1;
                    $post_data[$i]['can_share'] = 0;
                    $shared_user_name = "";
                    $shared_user_image = "";

                    if (isset($value['postSchool'][0]['freeUser']->profile_image))
                    {
                        $shared_user_image = $value['postSchool'][0]['freeUser']->profile_image;
                    }
                    if (isset($value['postSchool'][0]['freeUser']->first_name) && $value['postSchool'][0]['freeUser']->first_name)
                    {
                        $shared_user_name .= $value['postSchool'][0]['freeUser']->first_name . " ";
                    }
                    if (isset($value['postSchool'][0]['freeUser']->middle_name) && $value['postSchool'][0]['freeUser']->middle_name)
                    {
                        $shared_user_name .= $value['postSchool'][0]['freeUser']->middle_name . " ";
                    }
                    if (isset($value['postSchool'][0]['freeUser']->last_name) && $value['postSchool'][0]['freeUser']->last_name)
                    {
                        $shared_user_name .= $value['postSchool'][0]['freeUser']->last_name;
                    }
                    if (!$shared_user_name)
                    {
                        if (isset($value['postSchool'][0]['freeUser']->email))
                            $shared_user_name = $value['postSchool'][0]['freeUser']->email;
                    }

                    $post_data[$i]['shared_user_name'] = $shared_user_name;
                    $post_data[$i]['shared_user_image'] = $shared_user_image;

                    if (in_array($value['id'], $wow) && Settings::$wow_login == true)
                    {
                        $post_data[$i]['can_wow'] = 0;
                    }
                    $i++;
                }
                
            }
            $response['data']['post'] = $post_data;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Data Found";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionIndex()
    {

        if (isset($_POST) && !empty($_POST))
        {

            $user_secret = Yii::app()->request->getPost('user_secret');
            $date = Yii::app()->request->getPost('date');
            $date = (!empty($date)) ? $date : \date('Y-m-d', \time());
            $school_id = Yii::app()->request->getPost('school');

            if (Yii::app()->user->user_secret === $user_secret)
            {

                $users = new Users;

                if (Yii::app()->user->isParent)
                {

                    $children = $users->studentList(Yii::app()->user->profileId);
                    $ar_children_ids = Settings::extractIds($children, 'profile_id');

                    $student_attendance = new Attendances;
                    $attendances = $student_attendance->getAbsentStudentMonth($date, $date, $ar_children_ids);

                    if (!empty($attendances['absent']) || !empty($attendances['late']))
                    {
                        $response['data']['attendance'] = $attendances;
                    }
                }

                $birthdays = $users->getBirthDays($date, $school_id);

                if (Yii::app()->user->isStudent)
                {

                    $assignment = new Assignments();
                    $assignments_data = $assignment->getAssignment(Yii::app()->user->batchId, Yii::app()->user->profileId, $date);

                    if (!empty($assignments_data))
                    {
                        $response['data']['homework'] = $assignments_data;
                    }
                }

                if ($birthdays)
                {
                    $response['data']['birthday'] = $birthdays;
                }

                # Check: if student then batch ID will not be available and will be fixed
                # Check: if teacher or parent then batch ID will be available and will be changeable

                $school_id = ( (Yii::app()->user->isAdmin || Yii::app()->user->isParent) && !empty($school_id) ) ? $school_id = $school_id : Yii::app()->user->schoolId;

                $time_table = new TimetableEntries;
                $time_table = $time_table->getTimeTables($school_id, $date);

                if ($time_table)
                {
                    $response['data']['time_table'] = $time_table;
                    $response['data']['current_weekday'] = Settings::getCurrentDay($date);
                }

                $news = new News;
                $news = $news->getNews($school_id, $date, $date);

                if ($news !== FALSE)
                {
                    $response['data']['notice'] = $news;
                }

                $events = new Events;
                $events = $events->getEvents($school_id, $date, $date);

                if ($events !== false)
                {
                    $response['data']['events'] = $events;
                }
            }
            else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
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
