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
                'actions' => array('index', 'gethome','getstudentinfo','getemployeeinfo','getuserfeed'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    private function atttext($school_id,$batch_id,$student_id)
    {
        $date = date('Y-m-d');
        $objattendence = new Attendances();
        $attendence_return = false;
        $holiday = new Events();
        $holiday_array = $holiday->getHolidayMonth($date, $date, $school_id);
        if ($holiday_array)
        {
            $attendence_ret = 1; //(".$holiday_array[0]['title'].")
            $attendence_return = true;
        }

        if (!$attendence_return)
        {
            $weekend_array = $objattendence->getWeekend($school_id,$batch_id);
            $weekday = date("w", strtotime($date));
            if (in_array($weekday, $weekend_array))
            {
                $attendence_ret = 2;
                $attendence_return = true;
            }
        }
        if (!$attendence_return)
        {
            $leave = new ApplyLeaveStudents();
            $leave_array = $leave->getleaveStudentMonth($date, $date, $student_id);
            if ($leave_array)
            {
                $attendence_ret = 3;
                $attendence_return = true;
            }
            if (!$attendence_return)
            {

                $attendance_array = $objattendence->getAbsentStudentMonth($date, $date, $student_id);
                if ($attendance_array['late'])
                {
                    $attendence_ret = 4;
                    $attendence_return = true;
                }
                else if ($attendance_array['absent'])
                {
                    $attendence_ret = 5;
                    $attendence_return = true;
                }
                else
                {
                    $timetableobj = new TimetableEntries();
                    $class_started = $timetableobj->classStarted($batch_id);
                    if($class_started)
                    {
                        $attendence_ret = 6;
                    }
                    else if($date==date("Y-m-d"))
                    {
                        $attendence_ret = 7;
                    }    
                    $attendence_return = true;
                }
            }
        }
        return $attendence_ret;
    }        
    
    public function actionGetuserFeed()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $student_id = Yii::app()->request->getPost('student_id');
        $batch_id = Yii::app()->request->getPost('batch_id');
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isStudent || (Yii::app()->user->isParent && $student_id && $batch_id)))
        {
            if (Yii::app()->user->isStudent)
            {
                $student_id = Yii::app()->user->profileId;
                $batch_id = Yii::app()->user->batchId;
            }
            $user_id = Yii::app()->user->id;
            $school_id = Yii::app()->user->schoolId;
            
            if(!Yii::app()->user->isTeacher)
            {
                $time_table = new TimetableEntries;
                $time_table = $time_table->getNextStudent($batch_id);
                $response['data']['time_table'] = $time_table;
                
                $studentsobj = new Students();
                $students = $studentsobj->getStudentById($student_id);
                $response['data']['user_details'] =  "";
                if(isset($students['batchDetails']) && isset($students['batchDetails']->name) && isset($students['batchDetails']['courseDetails']) && isset($students['batchDetails']['courseDetails']->course_name))
                {    
                    $response['data']['user_details'] = $students['batchDetails']->name." ".$students['batchDetails']['courseDetails']->course_name;
                }
                $response['data']['attandence'] = $this->atttext($school_id, $batch_id, $student_id);
            }  
            else
            {
                $time_table = new TimetableEntries;
                $time_table = $time_table->getNextTeacher($school_id, Yii::app()->user->profileId);
                $response['data']['time_table'] = $time_table;
                
                $employee_id = Yii::app()->user->profileId;
           
                $employeeobj = new Employees();
                $employees = $employeeobj->getEmployeeById($employee_id);
                $response['data']['user_details'] = $employees['department']->name;
                $response['data']['attandence'] = 6;
            }  
            
            $response['data']['last_visited']['first'] = "";
            $response['data']['last_visited']['number'] = "Today";
            $response['data']['last_visited']['type'] = "";

            $lastvisited = new LastVisited();
            $last_visited = $lastvisited->getLastVisited();
            $lastvisited->addLastVisited();

            if ($last_visited)
            {
                $visitedarray = explode(" ", $last_visited);
                $response['data']['last_visited']['first'] = "Last Visited";
                $response['data']['last_visited']['number'] = $visitedarray[0];
                $response['data']['last_visited']['type'] = $visitedarray[1];
            }
           

            //school info

            $school_obj = new Schools();
            $school_details = $school_obj->findByPk($school_id);
            $response['data']['school_name'] = $school_details->name;
          
            
            $userObj = new Users();
            $user_data = $userObj->findByPk($user_id);
            if(isset($user_data) && isset($user_data->first_name))
            {
                $response['data']['user_name'] = $user_data->first_name.' '.$user_data->last_name;
            }
            
           
            $freobj = new Freeusers();
            $fUserInfo = $freobj->getFreeuserPaid($user_id,$school_id);
            $response['data']['profile_picture'] = Settings::getProfileImage($fUserInfo);
            
            $page_number = Yii::app()->request->getPost('page_number');
            $page_size = Yii::app()->request->getPost('page_size');
            if (empty($page_number))
            {
                $page_number = 1;
            }
            if (empty($page_size))
            {
                $page_size = 10;
            }
            $objreminder = new Reminders();
            $response['data']['unread_total'] = $objreminder->getReminderTotalUnread(Yii::app()->user->id);
            $response['data']['total'] = $objreminder->getReminderTotal(Yii::app()->user->id);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }
            $response['data']['has_next'] = $has_next;
            
            $feeds = $objreminder->getUserReminderNew(Yii::app()->user->id,$page_number,$page_size);
            
            $response['data']['feeds'] = $this->formatFeeds($feeds);
            
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Success";
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
      
    private function formatFeeds($feeds)
    {
        $formated_feed = array();
        $i = 0;
        if($feeds)
        foreach($feeds as $value)
        {
            if($value['rtype'] && $value['rid'])
            {
                $id = $value['rid'];
                if($value['rtype']==4)
                {
                    $assignment = new Assignments();
                    $homework_data = $assignment->getAssignment("",array(), "", 1, null, 1, 1,$id);
                    if($homework_data)
                    {
                        
                        $formated_feed[$i]['title'] = "<b>".$homework_data[0]['subjects']."</b> Homework assigned by <b>".$homework_data[0]['teacher_name']."</b>";
                        $formated_feed[$i]['created'] = date("Y-m-d", strtotime($homework_data[0]['assign_date']));
                        $formated_feed[$i]['body1'] = strip_tags($homework_data[0]['name']);
                        $formated_feed[$i]['body2'] = strip_tags($homework_data[0]['content']);
                        $formated_feed[$i]['body3'] = "<b>Due Date : </b>".$homework_data[0]['duedate'];
                        $formated_feed[$i]['attachment_file_name'] = $homework_data[0]['attachment_file_name'];
                        $formated_feed[$i]['is_read'] = $value['is_read'];  
                        $formated_feed[$i]['rtype'] = $value['rtype'];
                        $formated_feed[$i]['rid'] = $value['rid'];
                        $i++;
                        
                    }
                    
                    
                } 
                if($value['rtype']==31)
                {
                    $classwork = new Classworks();
                    $classwork_data = $classwork->getClasswork("",array(), "", 1, null, 1, 1,$id);
                    if($classwork_data)
                    {
                        
                        $formated_feed[$i]['title'] = "<b>".$classwork_data[0]['subjects']."</b> Classwork assigned by <b>".$classwork_data[0]['teacher_name']."</b>";
                        $formated_feed[$i]['created'] = date("Y-m-d", strtotime($classwork_data[0]['assign_date']));
                        $formated_feed[$i]['body1'] = strip_tags($classwork_data[0]['name']);
                        $formated_feed[$i]['body2'] = strip_tags($classwork_data[0]['content']);
                        $formated_feed[$i]['body3'] = "";
                        $formated_feed[$i]['attachment_file_name'] = $classwork_data[0]['attachment_file_name'];
                        $formated_feed[$i]['is_read'] = $value['is_read'];  
                        $formated_feed[$i]['rtype'] = $value['rtype'];
                        $formated_feed[$i]['rid'] = $value['rid'];
                        $i++;
                        
                    }
                    
                    
                }
                else if($value['rtype']==5)
                {
                    $news = new News;
                    $news = $news->getSingleNews($id);
                    if($news)
                    {
                        $formated_feed[$i]['title'] = "<b>".$news['notice_title']."</b>";
                        $formated_feed[$i]['created'] = date("Y-m-d", strtotime($news['published_at']));
                        $formated_feed[$i]['body1'] = strip_tags($news['notice_content']);
                        $formated_feed[$i]['body2'] = "";
                        $formated_feed[$i]['body3'] = "";
                        $formated_feed[$i]['attachment_file_name'] = $news['file_name'];
                       
                        $formated_feed[$i]['is_read'] = $value['is_read']; 
                        $formated_feed[$i]['rtype'] = $value['rtype'];
                        $formated_feed[$i]['rid'] = $value['rid'];
                        $i++;
                    }
                } 
                else if($value['rtype']==2)
                {
                    $exam = new ExamGroups();
                    $examsdata = $exam->findByPk($id);
                    if($examsdata)
                    {
                        $formated_feed[$i]['title'] = "<b>".$examsdata->name."</b>";
                        $formated_feed[$i]['created'] = date("Y-m-d", strtotime($examsdata->created_at));
                        $formated_feed[$i]['body1'] = "<b>".$examsdata->name."</b> Exam Routine Publish";
                        if($examsdata->exam_date != "1979-01-01")
                        {
                            $formated_feed[$i]['body2'] = "<b>Start Date : </b>".$examsdata->exam_date;
                        }
                        else
                        {
                            $formated_feed[$i]['body2'] = "";
                        }    
                        $formated_feed[$i]['body3'] = "";
                        $formated_feed[$i]['attachment_file_name'] = "";
                       
                        $formated_feed[$i]['is_read'] = $value['is_read']; 
                        $formated_feed[$i]['rtype'] = $value['rtype'];
                        $formated_feed[$i]['rid'] = $value['rid'];
                        $i++;
                    }
                } 
                else if($value['rtype']==3)
                {
                    $exam = new ExamGroups();
                    $examsdata = $exam->findByPk($id);
                    if($examsdata)
                    {
                        $formated_feed[$i]['title'] = "<b>".$examsdata->name."</b>";
                        $formated_feed[$i]['created'] = date("Y-m-d", strtotime($examsdata->created_at));
                        $formated_feed[$i]['body1'] = "<b>".$examsdata->name."</b> Exam Result Publish";
                        $formated_feed[$i]['body2'] = "";
                        $formated_feed[$i]['body3'] = "";
                        $formated_feed[$i]['attachment_file_name'] = "";
                       
                        $formated_feed[$i]['is_read'] = $value['is_read']; 
                        $formated_feed[$i]['rtype'] = $value['rtype'];
                        $formated_feed[$i]['rid'] = $value['rid'];
                        $i++;
                    }
                } 
                else if($value['rtype']==6)
                {
                    
                    $formated_feed[$i]['title'] = "<b>Attendance</b>";
                    $formated_feed[$i]['created'] = date("Y-m-d", strtotime($value['created_at']));
                    $formated_feed[$i]['body1'] = "Attendance Notice";
                    $formated_feed[$i]['body2'] = strip_tags($value['body']);
                    $formated_feed[$i]['body3'] = "";
                    $formated_feed[$i]['attachment_file_name'] = "";

                    $formated_feed[$i]['is_read'] = $value['is_read']; 
                    $formated_feed[$i]['rtype'] = $value['rtype'];
                    $formated_feed[$i]['rid'] = $value['rid'];
                    $i++;
                    
                } 
                else if($value['rtype']==45)
                {
                    $subjectAtt = new SubjectAttendances();
                    $sub_data = $subjectAtt->findByPk($value['rid']);
                    if($sub_data)
                    {
                        $formated_feed[$i]['title'] = "<b>Attendance</b>";
                        $formated_feed[$i]['created'] = date("Y-m-d", strtotime($value['created_at']));
                        $formated_feed[$i]['body1'] = "Attendance Notice";
                        $formated_feed[$i]['body2'] = strip_tags($value['body']);
                        $formated_feed[$i]['body3'] = "";
                        $formated_feed[$i]['attachment_file_name'] = "";

                        $formated_feed[$i]['is_read'] = $value['is_read']; 
                        $formated_feed[$i]['rtype'] = $value['rtype'];
                        $formated_feed[$i]['rid'] = $sub_data->subject_id;
                        $i++;
                    }
                    
                } 
                else if($value['rtype']==13 && $value['rtype']==11 && $value['rtype']==12 && $value['rtype']==14)
                {
                    $meeting_request = new Meetingrequest();
                    $mdata = $meeting_request->singleMetting($id);
                    if($mdata)
                    {
                        $formated_feed[$i]['title'] = "<b>Meeting Request</b>";
                        $formated_feed[$i]['created'] =date("Y-m-d",  strtotime($mdata['date']));
                        $formated_feed[$i]['body1'] = strip_tags($value['subject']);
                        $formated_feed[$i]['body2'] = "<b>Meeting Date : </b>".$mdata['date'];
                        $formated_feed[$i]['body3'] = "";
                        $formated_feed[$i]['attachment_file_name'] = "";
                       
                        $formated_feed[$i]['is_read'] = $value['is_read']; 
                        $formated_feed[$i]['rtype'] = $value['rtype'];
                        $formated_feed[$i]['rid'] = $value['rid'];
                        $i++;
                    }
                } 
                else if($value['rtype']==7 && $value['rtype']==8)
                {
                    $leave = new ApplyLeaves();
                    $ldata = $leave->getSingleLeave($id);
                    if($ldata)
                    {
                        $formated_feed[$i]['title'] = "<b>Leave Application</b>";
                        $formated_feed[$i]['created'] =date("Y-m-d", strtotime($ldata['created_at']));
                        $formated_feed[$i]['body1'] = strip_tags($value['subject']);
                        $formated_feed[$i]['body2'] = "<b>Reason : </b>".$ldata['leave_type'];
                        
                        $formated_feed[$i]['body3'] = "<b>Duration : </b> From ".$ldata['leave_start_date']." to ".$ldata['leave_end_date'];
                        $formated_feed[$i]['attachment_file_name'] = "";
                       
                        $formated_feed[$i]['is_read'] = $value['is_read']; 
                        $formated_feed[$i]['rtype'] = $value['rtype'];
                        $formated_feed[$i]['rid'] = $value['rid'];
                        
                        $i++;
                    }
                } 
                else if($value['rtype']==9 && $value['rtype']==10)
                {
                    $leave = new ApplyLeaveStudents();
                    $ldata = $leave->getSingleleave($id);
                    if($ldata)
                    {
                        $formated_feed[$i]['title'] = "<b>Leave Application</b>";
                        $formated_feed[$i]['created'] =date("Y-m-d", strtotime($ldata['created_at']));
                        $formated_feed[$i]['body1'] = strip_tags($value['subject']);
                        $formated_feed[$i]['body2'] = "<b>Reason : </b>".$ldata['reason'];
                        
                        $formated_feed[$i]['body3'] = "<b>Duration : </b> From ".$ldata['start_date']." to ".$ldata['end_date'];
                        $formated_feed[$i]['attachment_file_name'] = "";
                       
                        $formated_feed[$i]['is_read'] = $value['is_read']; 
                        $formated_feed[$i]['rtype'] = $value['rtype'];
                        $formated_feed[$i]['rid'] = $value['rid'];
                        $i++;
                    }
                }
                else if($value['rtype']==1)
                {
                    $events = new Events();
                    $ldata = $events->getSingleEvents($id);
                    if($ldata)
                    {
                        $formated_feed[$i]['title'] = "<b>".$ldata['event_title']."</b>";
                        $formated_feed[$i]['created'] =date("Y-m-d", strtotime($ldata['created_at']));
                        $formated_feed[$i]['body1'] = strip_tags($ldata['event_description']);
                        $formated_feed[$i]['body2'] = "<b>Duration : </b> From ".$ldata['event_start_date']." to ".$ldata['event_end_date'];
                        $formated_feed[$i]['body3'] = "";
                        $formated_feed[$i]['attachment_file_name'] = "";
                       
                        $formated_feed[$i]['is_read'] = $value['is_read']; 
                        $formated_feed[$i]['rtype'] = $value['rtype'];
                        $formated_feed[$i]['rid'] = $value['rid'];
                        $i++;
                    }
                }
                else if($value['rtype']==15)
                {
                    $oe = new OnlineExamGroups();
                    $ldata = $oe->getOnlineExam($id,"","",true);
                    if($ldata)
                    {
                        $formated_feed[$i]['title'] = "<b>".$ldata['subject_name']."</b>";
                        $formated_feed[$i]['created'] =date("Y-m-d", strtotime($ldata['created_at']));
                        $formated_feed[$i]['body1'] = strip_tags($ldata['title']);
                        $formated_feed[$i]['body2'] = "<b>Date : </b> From ".$ldata['start_date']." to ".$ldata['end_date'];
                        $formated_feed[$i]['body3'] = "";
                        $formated_feed[$i]['attachment_file_name'] = "";
                       
                        $formated_feed[$i]['is_read'] = $value['is_read']; 
                        $formated_feed[$i]['rtype'] = $value['rtype'];
                        $formated_feed[$i]['rid'] = $value['rid'];
                        $i++;
                    }
                }
                else if($value['rtype']==20)
                {
                    
                    $formated_feed[$i]['title'] = "<b>Class Swapped</b>";
                    $formated_feed[$i]['created'] = date("Y-m-d", strtotime($value['created_at']));
                    $formated_feed[$i]['body1'] = $value['subject'];
                    $formated_feed[$i]['body2'] = strip_tags($value['body']);
                    $formated_feed[$i]['body3'] = "";
                    $formated_feed[$i]['attachment_file_name'] = "";

                    $formated_feed[$i]['is_read'] = $value['is_read']; 
                    $formated_feed[$i]['rtype'] = $value['rtype'];
                    $formated_feed[$i]['rid'] = $value['rid'];
                    $i++;
                }
                else if($value['rtype']==21)
                {
                    
                    $formated_feed[$i]['title'] = "<b>New Task</b>";
                    $formated_feed[$i]['created'] = date("Y-m-d", strtotime($value['created_at']));
                    $formated_feed[$i]['body1'] = $value['subject'];
                    $formated_feed[$i]['body2'] = strip_tags($value['body']);
                    $formated_feed[$i]['body3'] = "";
                    $formated_feed[$i]['attachment_file_name'] = "";

                    $formated_feed[$i]['is_read'] = $value['is_read']; 
                    $formated_feed[$i]['rtype'] = $value['rtype'];
                    $formated_feed[$i]['rid'] = $value['rid'];
                    $i++;
                }
                else if($value['rtype']==160 || $value['rtype']==159)
                {
                    
                    $formated_feed[$i]['title'] = "<b>Birthday</b>";
                    $formated_feed[$i]['created'] = date("Y-m-d", strtotime($value['created_at']));
                    $formated_feed[$i]['body1'] = $value['subject'];
                    $formated_feed[$i]['body2'] = strip_tags($value['body']);
                    $formated_feed[$i]['body3'] = "";
                    $formated_feed[$i]['attachment_file_name'] = "";

                    $formated_feed[$i]['is_read'] = $value['is_read']; 
                    $formated_feed[$i]['rtype'] = $value['rtype'];
                    $formated_feed[$i]['rid'] = $value['rid'];
                    $i++;
                }
                
                
                
                
            }    
        }  
        return $formated_feed;
    }        


    public function actionGetEmployeeInfo()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher )
        {
          
            $employee_id = Yii::app()->user->profileId;
           
            $employeeobj = new Employees();
            $employees = $employeeobj->getEmployeeById($employee_id);
            if($employees)
            {
                $response['data']['employee'] = $this->formatEmployee($employees);
                $response['status']['code'] = 200;
                $response['status']['msg'] = "EVENTS_FOUND";
            }
            else 
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request";
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
    
    public function actionGetStudentInfo()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $student_id = Yii::app()->request->getPost('student_id');

        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isStudent || ( Yii::app()->user->isParent && $student_id ))  )
        {
            if(Yii::app()->user->isStudent)
            {
                $student_id = Yii::app()->user->profileId;
            }
            $studentsobj = new Students();
            $students = $studentsobj->getStudentById($student_id);

            $response['data']['student'] = $this->formatStudent($students);
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
    
    private function formatEmployee($emp)
    {
        $fullname = ($emp->first_name) ? $emp->first_name . " " : "";
        $fullname.= ($emp->middle_name) ? $emp->middle_name . " " : "";
        $fullname.= ($emp->last_name) ? $emp->last_name : "";
        $employee['name'] = $fullname;
        $employee['joining_date'] = $emp->joining_date;
        $employee['date_of_birth'] = $emp->date_of_birth;
        $employee['gender'] = $emp->gender;
        $employee['employee_number'] = $emp->employee_number;
        $employee['department'] = "";
        $employee['category'] = "";
        $employee['position'] = "";
        $employee['grade'] = "";
        if(isset($emp['department']->name) && $emp['department']->name)
        {
            $employee['department'] = $emp['department']->name;
        }
        if(isset($emp['category']->name) && $emp['category']->name)
        {
            $employee['category'] = $emp['category']->name;
        }
        if(isset($emp['position']->name) && $emp['position']->name)
        {
            $employee['position'] = $emp['position']->name;
        }
        if(isset($emp['grade']->name) && $emp['grade']->name)
        {
            $employee['grade'] = $emp['grade']->name;
        }
        
        
        
        
        $employee['phone'] = "";
        if($emp->mobile_phone)
        {
           $employee['phone'] =  $emp->mobile_phone;
        }
        else if($emp->office_phone1)
        {
           $employee['phone'] =  $emp->office_phone1;
        }
        else if($emp->office_phone2)
        {
           $employee['phone'] =  $emp->office_phone2;
        }
        else if($emp->home_phone)
        {
           $employee['phone'] =  $emp->home_phone;
        }
        $free = new Freeusers();
        $fUserInfo = $free->getFreeuserPaid($emp->user_id,$emp->school_id);
        $employee['user_image'] = "";

        if ($fUserInfo)
        {
            $employee['user_image'] = Settings::getProfileImage($fUserInfo);
        }
       
        return $employee;
    }

    private function formatStudent($std)
    {
        $fullname = ($std->first_name) ? $std->first_name . " " : "";
        $fullname.= ($std->middle_name) ? $std->middle_name . " " : "";
        $fullname.= ($std->last_name) ? $std->last_name : "";
        $student['name'] = $fullname;
        $student['roll'] = $std->class_roll_no;
        $student['admission_no'] = $std->admission_no;
        $student['date_of_birth'] = $std->date_of_birth;
        $student['gender'] = $std->gender;
        $student['admission_no'] = $std->admission_no;
        $student['class'] = $std['batchDetails']['courseDetails']->course_name;
        $student['batch'] = $std['batchDetails']->name;
        $student['contact'] = "";
        
        if($std->address_line1)
        {
           $student['contact'] .=  $std->address_line1;
        }
        if($std->address_line2)
        {
            if($student['contact'])
            {
               $student['contact'] .=", "; 
            }
            $student['contact'] .=  $std->address_line2;
        }
        
        $student['phone'] = "";
        if($std->phone1)
        {
           $student['phone'] =  $std->phone1;
        }
        else if($std->phone2)
        {
           $student['phone'] =  $std->phone2; 
        }    
        
        
        
       
        $fullname = "";
        if (isset($std['guradianDetails']->first_name))
        {
            $fullname = ($std['guradianDetails']->first_name) ? $std['guradianDetails']->first_name . " " : "";
            $fullname.= ($std['guradianDetails']->last_name) ? $std['guradianDetails']->last_name : "";
        }
        $free = new Freeusers();

        $fUserInfo = $free->getFreeuserPaid($std->user_id,$std->school_id);
        $student['user_image'] = "";

        if ($fUserInfo)
        {
            $student['user_image'] = Settings::getProfileImage($fUserInfo);
        }

        $student['guradian'] = $fullname;
        return $student;
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
                $fUserInfo = $freobj->getFreeuserPaid($user_id,$school_id);
//                $profile_image = $freobj->getUserImage($user_id);
//                if (isset($profile_image['profile_image']) && $profile_image['profile_image'])
//                {
                    $response['maindata']['profile_picture'] = Settings::getProfileImage($fUserInfo);
//                }

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

                    if(!$merging_data or $merging_data)
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
                        

                        $merging_data['homework'] = array();
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
                        $merging_data['homework_total'] = $assignment->getAssignmentTotal($batch_id, $student_id, "",null,1,$tommmorow);

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
                        $merging_data['notice_total'] = $newsobj->getNoticeCount(1, $date, $date);
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
                        $merging_data['not-from-cache'] = 1;

                    }

                    $response['maindata']['datefeed'][] = $merging_data;
                }
                $post_data[0] = $response['maindata'];
                unset($response['maindata']);
            }
            else if($page_number == 1 && Yii::app()->user->isTeacher)
            {
                
                $user_id = Yii::app()->user->id;
                $school_id = Yii::app()->user->schoolId;
                $profile_id = Yii::app()->user->profileId;


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
                $fUserInfo = $freobj->getFreeuserPaid($user_id,$school_id);
                //$profile_image = $freobj->getUserImage($user_id);
                //if (isset($profile_image['profile_image']) && $profile_image['profile_image'])
                //{
                    $response['maindata']['profile_picture'] = Settings::getProfileImage($fUserInfo);
                            //$profile_image['profile_image'];
                //}

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


                        
                        //Next Class
                  
                        $time_table = new TimetableEntries;
                        $next_class = $time_table->getNextTeacherMulti($school_id, $profile_id);
                        $merging_data['next_class'] = $next_class;
                       
                        
                        //Teacher Assignment
                        $assignment = new Assignments();
                        $homework_data = $assignment->getAssignmentTeacher($profile_id,1,5,1);
                        
                        $merging_data['homework'] = array();

                        $merging_data['homework_subject'] = $homework_data;
                        
                        $merging_data['homework_total'] =  $assignment->getAssignmentTotalTeacher($profile_id,1,NULL,$tommmorow);
                        

                        
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
                        $meetingobj = new Meetingrequest();
                        $merging_data['meeting_tommorow'] =  $meetingobj->meetingTommorow($profile_id);
                        


                        //Notice start
                        $newsobj = new News();
                        $notice = $newsobj->getNews($school_id, $date, $date);
                        $merging_data['notice'] = false;
                        if ($notice)
                        {
                            $merging_data['notice'] = true;
                        }
                        $merging_data['notice_total'] = $newsobj->getNoticeCount(1, $date, $date);
                        //Notice end
                        //Quiz start

                        $merging_data['quiz'] = array();
                        
                        if($date<date("Y-m-d"))
                        {
                            Yii::app()->cache->set($cache_name, $merging_data, 2592000);
                        }
                        $merging_data['not-from-cache'] = 1;

                    }

                    $response['maindata']['datefeed'][] = $merging_data;
                }
                $post_data[0] = $response['maindata'];
                unset($response['maindata']);
                
            }
    

            $response['data']['total'] = 1;
            $response['data']['has_next'] = false;
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
            

            if (Yii::app()->user->user_secret === $user_secret)
            {
                $school_id = Yii::app()->user->schoolId;

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
