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
                'actions' => array('index','getsingleevent', 'readreminder', 'getuserreminder', 'acknowledge', 'meetingrequest', 'meetingstatus',
                    'getstudentparent', 'addmeetingrequest', 'addmeetingparent', 'getteacherparent',
                    'addleaveteacher', 'leavetype', 'teacherleaves', 'studentleaves', 'fees'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
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

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
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

    public function actionAddLeaveTeacher()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $reason = Yii::app()->request->getPost('reason');
        $start_date = Yii::app()->request->getPost('start_date');
        $end_date = Yii::app()->request->getPost('end_date');
        $employee_leave_types_id = Yii::app()->request->getPost('employee_leave_types_id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $reason && $start_date && $end_date && $employee_leave_types_id)
        {

            $leave = new ApplyLeaves();
            $leave->school_id = Yii::app()->user->schoolId;
            $leave->reason = $reason;
            $leave->start_date = $start_date;
            $leave->end_date = $end_date;
            $leave->is_half_day = 0;
            $leave->employee_leave_types_id = $employee_leave_types_id;
            $leave->employee_id = Yii::app()->user->profileId;
            $leave->created_at = date("Y-m-d H:i:s");
            $leave->updated_at = date("Y-m-d H:i:s");
            $leave->save();
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

    public function actionAddMeetingParent()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $batch_id = Yii::app()->request->getPost('batch_id');
        $description = Yii::app()->request->getPost('description');
        $datetime = Yii::app()->request->getPost('datetime');
        $parent_id = Yii::app()->request->getPost('parent_id');
        $student_id = Yii::app()->request->getPost('student_id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isParent && $batch_id && $description && $datetime && $parent_id)
        {

            $meetingreq = new Meetingrequest();
            $meetingreq->description = $description;
            $meetingreq->datetime = $datetime;
            $meetingreq->teacher_id = $parent_id;
            $meetingreq->parent_id = $student_id;
            $meetingreq->type = 2;
            $meetingreq->save();

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
        $batch_id = Yii::app()->request->getPost('batch_id');
        $description = Yii::app()->request->getPost('description');
        $datetime = Yii::app()->request->getPost('datetime');
        $parent_id = Yii::app()->request->getPost('parent_id');
        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $batch_id && $description && $datetime && $parent_id)
        {

            $meetingreq = new Meetingrequest();
            $meetingreq->description = $description;
            $meetingreq->datetime = $datetime;
            $meetingreq->teacher_id = Yii::app()->user->profileId;
            $meetingreq->parent_id = $parent_id;
            $meetingreq->save();

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
            if (( isset($updatemeeting->parent_id) && Yii::app()->user->isParent && $student_id == $updatemeeting->parent_id && $updatemeeting->type == 1) ||
                    ( isset($updatemeeting->teacher_id) && Yii::app()->user->isTeacher && Yii::app()->user->profileId == $updatemeeting->teacher_id && $updatemeeting->type == 2))
            {

                $updatemeeting->status = $status;

                $updatemeeting->save(false);
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
            $school_id = Yii::app()->request->getPost('school');

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

                $events = new Events;
                $events = $events->getEvents($school_id, $from_date, $to_date, $page_no, $page_size, $category_id, false, false, $archive);

                if (!$events)
                {
                    $response['status']['code'] = 404;
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
            $school_id = Yii::app()->request->getPost('school');
            $event_id = Yii::app()->request->getPost('event_id');
            $status = Yii::app()->request->getPost('status');

            if (Yii::app()->user->user_secret === $user_secret)
            {

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
