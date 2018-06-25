<?php

class EventController extends Controller
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
                'actions' => array('index','addleavestudent','studentleavesparent','getsingleevent', 'readreminder', 'getuserreminder', 'acknowledge', 'meetingrequest', 'meetingstatus',
                    'getstudentparent', 'addmeetingrequest','meetingrequestsingle', 'addmeetingparent', 'getteacherparent',
                    'addleaveteacher','reportmanagerteacher', 'leavetype', 'teacherleaves', 'studentleaves', 'fees','eventjoin','checkimportantnotice','globalsearch'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    public function actioncheckImportantNotice()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
            $reminder = new Reminders();
            
            $all_reminder = $reminder->getImportantReminder();
            $notice = new stdClass();;
            if($all_reminder)
            {  
                $notice = $all_reminder;
            }  
            $response['data']['notice'] = $notice;
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
    public function actionglobalSearch()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $student_id = Yii::app()->request->getPost('student_id');
        $batch_id = Yii::app()->request->getPost('batch_id');
        $term = Yii::app()->request->getPost('term');
        if ($term && Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin || Yii::app()->user->isStudent || (Yii::app()->user->isParent && $student_id && $batch_id)))
        {
            $students = array();
            $employees = array();
            if(Yii::app()->user->isAdmin)
            {
                $user = new Users();
                $employees = $user->getEmployeeTerm($term);
                $students = $user->getStudentTerm($term);
                
            }
            if(Yii::app()->user->isTeacher)
            {
                $user = new Users();
                $students = $user->getStudentTerm($term);
            }
            if(Yii::app()->user->isStudent)
            {
                $batch_id = Yii::app()->user->batchId;
                $student_id = Yii::app()->user->profileId;
            }
            $employee_id = false;
            if(Yii::app()->user->isTeacher)
            {
                $employee_id = Yii::app()->user->profileId;
            }
            
            
            
            $assignment = new Assignments();
            
            $homeworks = $assignment->getAssignmentTerm($term,$batch_id,$student_id,$employee_id);
            
            $eventobj = new Events();
            $events = $eventobj->getEventsTerm($term, $batch_id, $student_id);
            
            $noticeObj = new News();
            
            $notice = $noticeObj->getNoticeTerm($term);
            
            
            $response['data']['employees'] = $employees;
            $response['data']['students'] = $students;
            $response['data']['homeworks'] = $homeworks;
            $response['data']['events'] = $events;
            $response['data']['notice'] = $notice;
            
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
    
    public function actionEventJoin()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $id = Yii::app()->request->getPost('id');
        if (Yii::app()->user->user_secret === $user_secret && $id  && Yii::app()->user->isAdmin)
        {
            $evenack = new EventAcknowledges();       
            
            $data = $evenack->getTotalEventJoin($id);

            $data_details = $evenack->getParentStudentEmployeeList($id);
            
            $response['data']['count'] = $data;
            $response['data']['event'] = $data_details;
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

    public function actionReadReminder()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $id = Yii::app()->request->getPost('id');
        $rtype = Yii::app()->request->getPost('rtype');
        $rid = Yii::app()->request->getPost('rid');
        if (Yii::app()->user->user_secret === $user_secret && ( $id || $rtype))
        {
            $objreminder = new Reminders();
            if (!$id)
            {
                $id = 0;
            }
            if (!$rtype)
            {
                $rtype = 0;
            }
            if (!$rid)
            {
                $rid = 0;
            }
            $objreminder->ReadReminderNew(Yii::app()->user->id, $id, $rtype, $rid);
            $response['data']['unread_total'] = $objreminder->getReminderTotalUnread(Yii::app()->user->id);
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

    public function actionGetuserReminder()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        if (Yii::app()->user->user_secret === $user_secret)
        {
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
            
            $response['data']['reminder'] = $objreminder->getUserReminderNew(Yii::app()->user->id,$page_number,$page_size);
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

    public function actionFees()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');

        $student_id = Yii::app()->request->getPost('student_id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isParent && $student_id)
        {
            $objFees = new FinanceFees();

            $response['data']['due'] = $objFees->feesStudentDue($student_id);
            $response['data']['history'] = $objFees->feesStudentDueHistory($student_id);
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

    public function actionStudentLeaves()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
        {

            $leave = new ApplyLeaveStudents();
            $leaveobj = $leave->getStudentLeave(Yii::app()->user->profileId);
            $response['data']['today'] = date("Y-m-d");
            $response['data']['leaves'] = $leaveobj;
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

    public function actionTeacherLeaves()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $student_id = Yii::app()->request->getPost('student_id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isParent && $student_id)
        {

            $leave = new ApplyLeaveStudents();
            $leaveobj = $leave->getStudentLeaveParent($student_id);

            $leave = array();
            $i = 0;
            if ($leaveobj)
                foreach ($leaveobj as $value)
                {
                   
                    $leave[$i]['leave_start_date'] = $value->start_date;
                    $leave[$i]['leave_end_date'] = $value->end_date;
                    $leave[$i]['leave_subject']  = "";
                    if($value->leave_subject)
                    {
                        $leave[$i]['leave_subject'] = $value->leave_subject;
                    }
                    $leave[$i]['reason'] = $value->reason;
                    if (!$value->viewed_by_teacher && !$value->approved)
                    {
                        $leave[$i]['status'] = 2;
                    }
                    else if ($value->approved == 1)
                    {
                        $leave[$i]['status'] = 1;
                    }
                    else
                    {
                        $leave[$i]['status'] = 0;
                    }
                    $leave[$i]['created_at'] = date("Y-m-d", strtotime($value->created_at));
                    $i++;
                }
             
            $response['data']['today'] = date("Y-m-d");
            $response['data']['leaves'] = $leave;
            $response['status']['code'] = 200;
            $response['status']['msg'] = "Success";
        }
        else if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
        {

            $leave = new ApplyLeaves();
            $leaveobj = $leave->getTeacherLeave(Yii::app()->user->profileId);

            $leave = array();
            $i = 0;
            if ($leaveobj)
                foreach ($leaveobj as $value)
                {
                    $leave[$i]['leave_type'] = $value['leavetype']->name;
                    $leave[$i]['leave_start_date'] = $value->start_date;
                    $leave[$i]['leave_end_date'] = $value->end_date;
                    $leave[$i]['attachment_file_name'] = ""; 
                    if($value->attachment_file_name)
                    {
                        $leave[$i]['attachment_file_name'] = $value->attachment_file_name;
                    }
                    if (!$value->approving_manager)
                    {
                        $leave[$i]['status'] = 2;
                    }
                    else if ($value->approved == 1)
                    {
                        $leave[$i]['status'] = 1;
                    }
                    else
                    {
                        $leave[$i]['status'] = 0;
                    }
                    $leave[$i]['created_at'] = date("Y-m-d", strtotime($value->created_at));
                    $i++;
                }
            $response['data']['today'] = date("Y-m-d");
            $response['data']['leaves'] = $leave;
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

    public function actionLeaveType()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');

        if (Yii::app()->user->user_secret === $user_secret)
        {

            $leave = new EmployeeLeaveTypes();
            $leaveobj = $leave->findAll("school_id=" . Yii::app()->user->schoolId . " AND status=1");

            $leaveType = array();
            $i = 0;
            if ($leaveobj)
                foreach ($leaveobj as $value)
                {
                    $leaveType[$i]['type'] = $value->name;
                    $leaveType[$i]['id'] = $value->id;
                    $i++;
                }
            $response['data']['type'] = $leaveType;
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
    public function actionReportManagerTeacher()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
        {
            $id = Yii::app()->user->profileId;
            $objemployee = new Employees();
            $employeedata = $objemployee->findByPk($id);
            if(isset($employeedata->reporting_manager_id) && $employeedata->reporting_manager_id>0)
            {
                $response['data']['has_reporting_manager'] = 1;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success";
            }
            else
            {
                $response['data']['has_reporting_manager'] = 0;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success";
            }    
            
            
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }    
    }
    public function actionStudentLeavesParent()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $student_id = Yii::app()->request->getPost('student_id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isParent && $student_id)
        {

            $leave = new ApplyLeaveStudents();
            $leaveobj = $leave->getStudentLeaveParent($student_id);

            $leave = array();
            $i = 0;
            if ($leaveobj)
                foreach ($leaveobj as $value)
                {
                   
                    $leave[$i]['leave_start_date'] = $value->start_date;
                    $leave[$i]['leave_end_date'] = $value->end_date;
                    $leave[$i]['leave_subject']  = "";
                    if($value->leave_subject)
                    {
                        $leave[$i]['leave_subject'] = $value->leave_subject;
                    }
                    $leave[$i]['attachment_file_name'] = ""; 
                    if($value->attachment_file_name)
                    {
                        $leave[$i]['attachment_file_name'] = $value->attachment_file_name;
                    }
                    $leave[$i]['reason'] = $value->reason;
                    if (!$value->viewed_by_teacher && !$value->approved)
                    {
                        $leave[$i]['status'] = 2;
                    }
                    else if ($value->approved == 1)
                    {
                        $leave[$i]['status'] = 1;
                    }
                    else
                    {
                        $leave[$i]['status'] = 0;
                    }
                    $leave[$i]['created_at'] = date("Y-m-d", strtotime($value->created_at));
                    $i++;
                }
             
            $response['data']['today'] = date("Y-m-d");
            $response['data']['leaves'] = $leave;
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
    
    private function upload_attachment($file, $obj, $folder = "applyleavestudent" )
    {
        $obj->attachment_updated_at = date("Y-m-d H:i:s");
        $obj->updated_at = date("Y-m-d H:i:s");
        $uploads_dir = Settings::$paid_image_path . "uploads/".$folder."/attachments/" . $obj->id . "/original/";
        $file_name = str_replace(" ", "+", $file['attachment_file_name']['name']);
        $tmp_name = $file["attachment_file_name"]["tmp_name"];

        if (!is_dir($uploads_dir))
        {
            @mkdir($uploads_dir, 0777, true);
        }

        $uploads_dir = $uploads_dir . $file_name;


        if (@move_uploaded_file($tmp_name, "$uploads_dir"))
        {
            $obj->attachment_file_name = $file['attachment_file_name']['name'];
            $obj->save();
        }
        return $uploads_dir;
    }
    
    public function actionAddLeaveStudent()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $leave_subject = Yii::app()->request->getPost('leave_subject');
        $reason = Yii::app()->request->getPost('reason');
        $start_date = Yii::app()->request->getPost('start_date');
        $end_date = Yii::app()->request->getPost('end_date');
        $student_id = Yii::app()->request->getPost('student_id');
        if ( (Yii::app()->user->user_secret === $user_secret && $reason && $student_id && $start_date && $end_date) && (Yii::app()->user->isParent || Yii::app()->user->isTeacher) )
        {
            $leave = new ApplyLeaveStudents();
            
            if($leave->checkLeaveOk($student_id,$start_date,$end_date))
            {
                $leave->school_id = Yii::app()->user->schoolId;
                $leave->reason = $reason;
                $leave->start_date = $start_date;
                $leave->end_date = $end_date;
                $leave->student_id = $student_id;
                if($leave_subject)
                {
                    $leave->leave_subject = $leave_subject;
                }
                $leave->created_at = date("Y-m-d H:i:s");
                $leave->updated_at = date("Y-m-d H:i:s");
                if ($leave->save()) {
                    $leave_id = $leave->id;
                    if (isset($_FILES['attachment_file_name']['name']) && !empty($_FILES['attachment_file_name']['name'])) {
                        $leave->updated_at = date("Y-m-d H:i:s");
                        $leave->attachment_content_type = Yii::app()->request->getPost('mime_type');
                        $leave->attachment_file_size = Yii::app()->request->getPost('file_size');
                        $this->upload_attachment($_FILES, $leave);
                    }
                }

                //reminder code
                $std = new Students();
                $stddata = $std->findByPk($student_id);

                $emsubject = new EmployeesSubjects();

                $employess = $emsubject->getEmployee($stddata->batch_id);
                if($employess && !Yii::app()->user->isTeacher)
                {
                    $notification_ids = array();
                    $reminderrecipients = array();
                    foreach ($employess as $value)
                    {

                        $reminder = new Reminders();
                        $reminder->sender = Yii::app()->user->id;
                        $reminder->subject = "Student Leave Apply Notice";
                        $reminder->body = $stddata->first_name . "  apply for leave from " . $leave->start_date . " to ".$leave->end_date;
                        $reminder->recipient = $value['employee']->user_id;
                        $reminder->school_id = Yii::app()->user->schoolId;
                        $reminder->rid = $leave->id;
                        $reminder->rtype = 9;
                        $reminder->created_at = date("Y-m-d H:i:s");

                        $reminder->updated_at = date("Y-m-d H:i:s");
                        $reminder->save();
                        $reminderrecipients[] = $value['employee']->user_id;
                        $notification_ids[] = $reminder->id;
                    }
                    if($notification_ids)
                    {
                        $notification_id = implode(",", $notification_ids);
                        $user_id = implode(",", $reminderrecipients);
                        shell_exec("php pushnoti.php $notification_id $user_id  > /dev/null 2>/dev/null &");
                    }
                }

                //reminder code  

                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success";
                $response['data']['leave_id'] = $leave_id;
            }
            else
            {
               $response['status']['code'] = 404;
               $response['status']['msg'] = "Some dates are already approved or Applied.Please check the application date range"; 
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

    public function actionAddLeaveTeacher()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $reason = Yii::app()->request->getPost('reason');
        $start_date = Yii::app()->request->getPost('start_date');
        $end_date = Yii::app()->request->getPost('end_date');
        $employee_leave_types_id = Yii::app()->request->getPost('employee_leave_types_id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $reason && $start_date && $end_date && $employee_leave_types_id)
        {
            
            $objemployee = new Employees();
            $employeedata = $objemployee->findByPk(Yii::app()->user->profileId);
            if(isset($employeedata->reporting_manager_id) && $employeedata->reporting_manager_id>0)
            {
                $leave = new ApplyLeaves();
                if($leave->checkLeaveOk(Yii::app()->user->profileId,$start_date,$end_date))
                {
                    $leave->school_id = Yii::app()->user->schoolId;
                    $leave->reason = $reason;
                    $leave->start_date = $start_date;
                    $leave->end_date = $end_date;
                    $leave->is_half_day = 0;
                    $leave->approving_manager = $employeedata->reporting_manager_id;
                    $leave->employee_leave_types_id = $employee_leave_types_id;
                    $leave->employee_id = Yii::app()->user->profileId;
                    $leave->created_at = date("Y-m-d H:i:s");
                    $leave->updated_at = date("Y-m-d H:i:s");
                    $leave->save();
                    

                    //reminder
                    if(isset($leave->id))
                    {
                        if (isset($_FILES['attachment_file_name']['name']) && !empty($_FILES['attachment_file_name']['name'])) 
                        {
                            $leave->updated_at = date("Y-m-d H:i:s");
                            $leave->attachment_content_type = Yii::app()->request->getPost('mime_type');
                            $leave->attachment_file_size = Yii::app()->request->getPost('file_size');
                            $this->upload_attachment($_FILES, $leave,"applyleave");
                        }

                        $reminder = new Reminders();
                        $reminder->sender = Yii::app()->user->id;
                        $reminder->subject = "Employee Leave Apply Notice";
                        $reminder->body = $employeedata->first_name . "  apply for leave from " . $leave->start_date . " to ".$leave->end_date;
                        $reminder->recipient = $employeedata->reporting_manager_id;
                        $reminder->school_id = Yii::app()->user->schoolId;
                        $reminder->rid = $leave->id;
                        $reminder->rtype = 7;
                        $reminder->created_at = date("Y-m-d H:i:s");

                        $reminder->updated_at = date("Y-m-d H:i:s");
                        $reminder->save();

                        Settings::sendCurlNotification($employeedata->reporting_manager_id, $reminder->id);

                    }

                    //reminder

                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Success";
                }
                else
                {
                   $response['status']['code'] = 404;
                   $response['status']['msg'] = "Some dates are already approved or Applied.Please check the application date range"; 
                }    
            }
            else
            {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "ADD_REPORING_MANAGER";
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
    private function checkMeetingPublisher()
    {
        $configuration = new Configurations();
        $meeting_config = (int)$configuration->getValue("ParentMeetingRequestNeedApproval");
        $empObj = new Employees();
        $emp_data = $empObj->findByPk(Yii::app()->user->profileId);
        if($meeting_config == 0 || ($emp_data && $emp_data->meeting_forwarder==1))
        {
            return true;
        }
        return false;
    } 
    public function actionAddMeetingParent()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        //$batch_id = Yii::app()->request->getPost('batch_id');
        $description = Yii::app()->request->getPost('description');
        $datetime = Yii::app()->request->getPost('datetime');
        $parent_id = Yii::app()->request->getPost('parent_id');
        $student_id = Yii::app()->request->getPost('student_id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isParent && $student_id && $description && $datetime && $parent_id)
        {

            $meetingreq = new Meetingrequest();
            $meetingreq->description = $description;
            $meetingreq->datetime = $datetime;
            $meetingreq->teacher_id = $parent_id;
            $meetingreq->parent_id = $student_id;
            $meetingreq->meeting_type = 2;
            if($this->checkMeetingPublisher()==false)
            {
                $meetingreq->forward = 0;
            }
            
            $meetingreq->save();
            
            $guardian = new Guardians();
            $guardianData = $guardian->findByPk(Yii::app()->user->profileId);
            $employee = new Employees();
            $techer_profile = $employee->findByPk($parent_id);
            
            if(isset($techer_profile->user_id) && isset($guardianData->first_name) && $meetingreq->forward == 1)
            {
                $reminder = new Reminders();
                $reminder->sender = Yii::app()->user->id;
                $reminder->subject = "New Meeting Request";
                $reminder->body = "New Meeting Request Send from " . $guardianData->first_name . " at ".$datetime;
                $reminder->recipient = $techer_profile->user_id;
                $reminder->school_id = Yii::app()->user->schoolId;
                $reminder->rid = $meetingreq->id;
                $reminder->rtype = 12;
                $reminder->created_at = date("Y-m-d H:i:s");

                $reminder->updated_at = date("Y-m-d H:i:s");
                $reminder->save();
                
                
                Settings::sendCurlNotification($techer_profile->user_id, $reminder->id);
                
                ///push notification
            }

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

    public function actionGetTeacherParent()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $batch_id = Yii::app()->request->getPost('batch_id');


        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isParent && $batch_id)
        {
            $employeesubject = new EmployeesSubjects();
            $employees = $employeesubject->getEmployee($batch_id);
            $em_array = array();
            $i = 0;
            if ($employees)
                foreach ($employees as $value)
                {
                    $fullname = ($value['employee']->first_name) ? $value['employee']->first_name . " " : "";
                    $fullname.= ($value['employee']->middle_name) ? $value['employee']->middle_name . " " : "";
                    $fullname.= ($value['employee']->last_name) ? $value['employee']->last_name : "";
                    $em_array[$i]['id'] = $value['employee']->id;
                    $em_array[$i]['name'] = $fullname;
                    $i++;
                }
            $response['data']['student'] = $em_array;
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

    public function actionAddMeetingRequest()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        //$batch_id = Yii::app()->request->getPost('batch_id');
        $description = Yii::app()->request->getPost('description');
        $datetime = Yii::app()->request->getPost('datetime');
        $parent_id = Yii::app()->request->getPost('parent_id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher  && $description && $datetime && $parent_id)
        {

            $meetingreq = new Meetingrequest();
            $meetingreq->description = $description;
            $meetingreq->datetime = $datetime;
            $meetingreq->teacher_id = Yii::app()->user->profileId;
            $meetingreq->parent_id = $parent_id;
            $meetingreq->save();
            
            $employee = new Employees();
            $techer_profile = $employee->findByPk(Yii::app()->user->profileId);
            
            $student = new Students();
            $studentdata = $student->findByPk($parent_id);
            
            $gstudent = new GuardianStudent(); 
        
            $all_g = $gstudent->getGuardians($parent_id);

        
            
            if($all_g && isset($techer_profile->first_name))
            {
                foreach ($all_g as $value)
                {
                    if(isset($value['guardian']) && isset($value['guardian']->id))
                    {
                        $gr = new Guardians();
                        $grdata = $gr->findByPk($value['guardian']->id);
                        if($grdata && $grdata->user_id)
                        {
                            $receptionist_id = $grdata->user_id;
                            $batch_id = $studentdata->batch_id;
                            $student_id = $studentdata->id;
                        }
                        if($receptionist_id)
                        {
                            $reminder = new Reminders();
                            $reminder->sender = Yii::app()->user->id;
                            $reminder->subject = "New Meeting Request";
                            $reminder->body = "New Meeting Request Send from " . $techer_profile->first_name . " at ".$datetime;
                            $reminder->recipient = $receptionist_id;
                            $reminder->school_id = Yii::app()->user->schoolId;
                            $reminder->rid = $meetingreq->id;
                            $reminder->rtype = 11;
                            $reminder->batch_id = $batch_id;
                            $reminder->student_id = $student_id;
                            $reminder->created_at = date("Y-m-d H:i:s");

                            $reminder->updated_at = date("Y-m-d H:i:s");
                            $reminder->save();

                            Settings::sendCurlNotification($receptionist_id, $reminder->id);
                        }
                    }    
                }
                ///push notification
            }
            
            

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

    public function actionGetStudentParent()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $batch_id = Yii::app()->request->getPost('batch_id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $batch_id)
        {

            $stdobj = new Students();
            $students = $stdobj->getStudentByBatchFull($batch_id);

            $st_array = array();
            $i = 0;
            if ($students)
                foreach ($students as $value)
                {
                    if ($value->immediate_contact_id)
                    {
                        $fullname = ($value->first_name) ? $value->first_name . " " : "";
                        $fullname.= ($value->middle_name) ? $value->middle_name . " " : "";
                        $fullname.= ($value->last_name) ? $value->last_name : "";
                        $st_array[$i]['id'] = $value->id;
                        $st_array[$i]['name'] = $fullname;
                        $i++;
                    }
                }
            $response['data']['student'] = $st_array;
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

    public function actionMeetingStatus()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $meeting_id = Yii::app()->request->getPost('meeting_id');
        $status = Yii::app()->request->getPost('status');
        $student_id = Yii::app()->request->getPost('student_id');
        if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isTeacher || Yii::app()->user->isParent ) && $meeting_id && $status)
        {
            $meetingreq = new Meetingrequest();
            $updatemeeting = $meetingreq->findByPk($meeting_id);
            if (( isset($updatemeeting->parent_id) && Yii::app()->user->isParent && $student_id == $updatemeeting->parent_id && $updatemeeting->meeting_type == 1) ||
                    ( isset($updatemeeting->teacher_id) && Yii::app()->user->isTeacher && Yii::app()->user->profileId == $updatemeeting->teacher_id && $updatemeeting->meeting_type == 2))
            {

                $updatemeeting->status = $status;

                $updatemeeting->save();
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success";
                
                $status_text = "Declined";
                if($status==1)
                {
                    $status_text = "Accepted"; 
                }
                $name = "";
                $rtype = 13;
//                $recipient = 0;
//                $batch_id = 0;
//                $student_id = 0;
                $reminderrecipients = array();
                if(Yii::app()->user->isParent)
                {
                    $gr = new Guardians();
                    $grdata = $gr->findByPk(Yii::app()->user->profileId);
                    $name = $grdata->first_name." ".$grdata->last_name;
                    
                    $employee = new Employees();
                    $emdata = $employee->findByPk($updatemeeting->teacher_id);
                    $rtype = 14;
                    $reminderrecipients[] = $emdata->user_id;
                    $batch_ids[$emdata->user_id] = 0;
                    $student_ids[$emdata->user_id] = 0;
                    
                    
                    //$recipient = $emdata->user_id;
                    
                }
                else
                {
                    $employee = new Employees();
                    $emdata = $employee->findByPk(Yii::app()->user->profileId);
                    $name = $emdata->first_name." ".$emdata->last_name;
                    
                    $std = new Students();
                    $studentdata = $std->findByPk($updatemeeting->parent_id);
                    
                    $gstudent = new GuardianStudent(); 
        
                    $all_g = $gstudent->getGuardians($updatemeeting->parent_id);

                    if ($all_g)
                    {
                        
                        foreach($all_g as $value)
                        {
                            if(isset($value['guardian']) && isset($value['guardian']->id))
                            {
                                $gr = new Guardians();
                                $grdata = $gr->findByPk($value['guardian']->id);
                                if($grdata && $grdata->user_id && !in_array($grdata->user_id, $reminderrecipients))
                                {
                                    $reminderrecipients[] = $grdata->user_id;
                                    $batch_ids[$grdata->user_id] = $studentdata->batch_id;
                                    $student_ids[$grdata->user_id] = $studentdata->id;
                                }
                            }            
                        }    

                    }
                    
                    
//                    if($std_data->immediate_contact_id)
//                    {
//                        $gr = new Guardians();
//                        $grdata = $gr->findByPk($std_data->immediate_contact_id);
//                        if($grdata->user_id)
//                        {
//                            $recipient = $grdata->user_id;
//                            $batch_id = $std_data->batch_id;
//                            $student_id = $std_data->id;
//                        }
//                    }
                }   
                if ($reminderrecipients)
                {
                    $notification_ids = array();
                    foreach ($reminderrecipients as $value)
                    {
                        $reminder = new Reminders();
                        $reminder->sender = Yii::app()->user->id;
                        $reminder->recipient = $value;
                        $reminder->subject = "Your Meeting Request is ".$status_text;
                        $reminder->body = "Your meeting request with " . $name . " have been  ".$status_text." for ".date('l jS \of F Y h:i:s A',  strtotime($updatemeeting->datetime));
                        $reminder->created_at = date("Y-m-d H:i:s");
                        $reminder->rid = $updatemeeting->id;
                        $reminder->rtype = $rtype;
                        $reminder->batch_id = $batch_ids[$value];
                        $reminder->student_id = $student_ids[$value];

                        $reminder->updated_at = date("Y-m-d H:i:s");
                        $reminder->school_id = Yii::app()->user->schoolId;
                        $reminder->save();
                        $notification_ids[] = $reminder->id;
                    }
                    $notification_id = implode(",", $notification_ids);
                    $user_id = implode(",", $reminderrecipients);
                    shell_exec("php pushnoti.php $notification_id $user_id  > /dev/null 2>/dev/null &");
                }
//                if($recipient)
//                {
//                    
//                    $reminder = new Reminders();
//                    $reminder->sender = Yii::app()->user->id;
//                    $reminder->subject = "Your Meeting Request is ".$status_text;
//                    $reminder->body = "Your meeting request with " . $name . " have been  ".$status_text." for ".date('l jS \of F Y h:i:s A',  strtotime($updatemeeting->datetime));
//                    $reminder->recipient = $recipient;
//                    $reminder->school_id = Yii::app()->user->schoolId;
//                    $reminder->rid = $updatemeeting->id;
//                    $reminder->rtype = $rtype;
//                    $reminder->batch_id = $batch_id;
//                    $reminder->student_id = $student_id;
//                    $reminder->created_at = date("Y-m-d H:i:s");
//
//                    $reminder->updated_at = date("Y-m-d H:i:s");
//                    $reminder->save();
//                    
//                    Settings::sendCurlNotification($recipient, $reminder->id);
//                   
//
//                    ///push notification
//                }
                
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
    
    public function actionMeetingRequestSingle()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $id = Yii::app()->request->getPost('id');
        if(Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isTeacher || Yii::app()->user->isParent))
        {
            $meetingreq = new Meetingrequest();
            $meetings = $meetingreq->singleMetting($id);
            if($meetings)
            {
                $response['data']['meetings'] = $meetings;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Success";
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

    public function actionMeetingRequest()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $type = Yii::app()->request->getPost('type');
        $start_date = Yii::app()->request->getPost('start_date');
        $end_date = Yii::app()->request->getPost('end_date');
        $page_number = Yii::app()->request->getPost('page_number');
        $page_size = Yii::app()->request->getPost('page_size');
        $student_id = Yii::app()->request->getPost('student_id');

        if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isTeacher || Yii::app()->user->isParent))
        {
            $meetingreq = new Meetingrequest();


            if (!$type)
            {
                $type = 1;
            }

            if (Yii::app()->user->isTeacher)
            {
                $type2 = 1;
                if ($type == 1)
                {
                    $type = 2;
                }
                else
                {
                    $type = 1;
                }
            }
            else
            {
                $type2 = 2;
            }


            if (!$start_date)
            {
                $start_date = "";
            }
            if (!$end_date)
            {
                $end_date = "";
            }

            if (empty($page_number))
            {
                $page_number = 1;
            }
            if (empty($page_size))
            {
                $page_size = 10;
            }

            if (Yii::app()->user->isTeacher)
            {
                $main_id = Yii::app()->user->profileId;
            }
            else
            {
                $main_id = $student_id;
            }

            $meetings = $meetingreq->getInboxOutbox($main_id, $type, $type2, $start_date, $end_date, $page_number, $page_size);

            $response['data']['total'] = $meetingreq->getall($main_id, $type, $type2, $start_date, $end_date);
            $has_next = false;
            if ($response['data']['total'] > $page_number * $page_size)
            {
                $has_next = true;
            }

            $response['data']['has_next'] = $has_next;
            $response['data']['meetings'] = $meetings;
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
    public function actionGetSingleEvent()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $id = Yii::app()->request->getPost('id');
        if ($id && Yii::app()->user->user_secret === $user_secret)
        {
            $events = new Events;
            $events_data = $events->getSingleEvents($id);
            if($events_data)
            {
               $response['data']['events'] = $events_data;
               $response['status']['code'] = 200;
               $response['status']['msg'] = 'EVENT_FOUND.'; 
            } 
            else
            {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request.";
            }    
        }
        else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request.";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionIndex()
    {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST))
        {

            $user_secret = Yii::app()->request->getPost('user_secret');
            

            $from_date = Yii::app()->request->getPost('from_date');
            $from_date = (!empty($from_date)) ? $from_date : \date('Y-m-d', \time());

            $to_date = Yii::app()->request->getPost('to_date');
            $to_date = (!empty($to_date)) ? $to_date : null;

            $page_no = Yii::app()->request->getPost('page_number');
            $page_no = (!empty($page_no)) ? $page_no : 1;

            $page_size = Yii::app()->request->getPost('page_size');
            $page_size = (!empty($page_size)) ? $page_size : 10;

            $category_id = Yii::app()->request->getPost('category');
            $category_id = (!empty($category_id)) ? $category_id : null;

            $archive = Yii::app()->request->getPost('archive');
            $archive = (!empty($archive) && ($archive == 'true')) ? true : false;

            $response = array();
            if (Yii::app()->user->user_secret === $user_secret)
            {
                $school_id = Yii::app()->user->schoolId;

                $events = new Events;
                $events = $events->getEvents($school_id, $from_date, $to_date, $page_no, $page_size, $category_id, false, false, $archive);

                if (!$events)
                {
                    $events_cnt = new Events;
                    $response['data']['total'] = $events_cnt->getEvents($school_id, $from_date, $to_date, $page_no, $page_size, $category_id, false, true);
                    if(!$response['data']['total'])
                    {
                        $response['data']['total'] = 0;
                    }
                    $response['data']['has_next'] =  false;
                    $response['data']['events'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = 'NO_EVENT_FOUND.';
                }
                else
                {

                    $response['data']['events'] = $events;

                    $events_cnt = new Events;
                    $response['data']['total'] = $events_cnt->getEvents($school_id, $from_date, $to_date, $page_no, $page_size, $category_id, false, true);

                    $has_next = false;
                    if ($response['data']['total'] > ($page_no * $page_size))
                    {
                        $has_next = true;
                    }

                    $response['data']['has_next'] = $has_next;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = 'EVENT_FOUND.';
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
            $response['status']['msg'] = "Bad Request.";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionAcknowledge()
    {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST))
        {

            $user_secret = Yii::app()->request->getPost('user_secret');
            
            $event_id = Yii::app()->request->getPost('event_id');
            $status = Yii::app()->request->getPost('status');

            if (Yii::app()->user->user_secret === $user_secret)
            {
                $school_id = Yii::app()->user->schoolId;
                if (empty($event_id) || !isset($status) || $status == '')
                {
                    $response['status']['code'] = 400;
                    $response['status']['msg'] = "Bad Request.";
                    echo CJSON::encode($response);
                    Yii::app()->end();
                }

                $event = new EventAcknowledges;
                $event = $event->acknowledgeEvent($event_id, $status, $school_id);

                if ($event === false)
                {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "NO_EVENT_ACKNOWLEDGED.";
                }
                else
                {
                    $response['data']['event_ack'] = $event;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EVENT_ACKNOWLEDGED.";
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
            $response['status']['msg'] = "Bad Request.";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }

}
