<?php

class ClassworkController extends Controller
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
                'actions' => array('index','intelligence','teacherintelligence','subjects','publishclasswork','singleteacher','singleclasswork', 'getsubject', 'addclasswork', 'teacherclasswork'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    public function actionTeacherIntelligence()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $date = Yii::app()->request->getPost('date');
        $department_id = Yii::app()->request->getPost('department_id');

        $sort_by = Yii::app()->request->getPost('sort_by');
        $sort_type = Yii::app()->request->getPost('sort_type');
        $time_range = Yii::app()->request->getPost('time_range');

        if (!$sort_by)
        {
            $sort_by = "frequency";
        }
        if (!$sort_type)
        {
            $sort_type = "1";
        }

        if (!$time_range)
        {
            $time_range = "day";
        }

        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
            if (!$date)
            {
                $date = date("Y-m-d");
            }

            if (!$department_id)
            {
                $department_id = FALSE;
            }
            $attendence = new Attendances();

            $day_type = $attendence->check_date($date);
            $classworks = new Classworks();

            $employee_data = $classworks->getClassworkEmployee($date, $sort_by, $sort_type, $time_range, $department_id);

            $response['data']['day_type'] = $day_type;
            $response['data']['employee_data'] = $employee_data;

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }

    public function actionIntelligence()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $date = Yii::app()->request->getPost('date');
        $batch_name = Yii::app()->request->getPost('batch_name');
        $class_name = Yii::app()->request->getPost('class_name');
        $batch_id = Yii::app()->request->getPost('batch_id');

        $number_of_day = Yii::app()->request->getPost('number_of_day');
        if (!$number_of_day)
        {
            $number_of_day = 10;
        }
        $type = Yii::app()->request->getPost('type');
        if (!$type)
        {
            $type = "days";
        }


        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
            if (!$date)
            {
                $date = date("Y-m-d");
            }
            if (!$batch_name)
            {
                $batch_name = FALSE;
            }
            if (!$class_name)
            {
                $class_name = FALSE;
            }
            if (!$batch_id)
            {
                $batch_id = FALSE;
            }
            $attendence = new Attendances();

            $day_type = $attendence->check_date($date);


            $classworks = new Classworks();
            $timetable = new TimetableEntries();
            $classwork_given = 0;
            $total_class = 0;
            $frequency = "N/A";

            if ($day_type == "1")
            {
                $classwork_given = $classworks->getClassworkTotalAdmin($date, $batch_name, $class_name, $batch_id);
                $total_class = $timetable->getTotalClass($date, $batch_name, $class_name, $batch_id);
                if ($classwork_given > 0)
                {
                    $frequency = round($total_class / $classwork_given);
                }
            }

            $graph_data = $classworks->getClassworkGraph($number_of_day, $type, $batch_name, $class_name, $batch_id);

            $response['data']['day_type'] = $day_type;
            $response['data']['total_class'] = $total_class;
            $response['data']['classwork_given'] = $classwork_given;
            $response['data']['frequency'] = $frequency;

            $response['data']['att_graph'] = $graph_data[0];
            $response['data']['att_graph_date'] = $graph_data[1];

            $response['status']['code'] = 200;
            $response['status']['msg'] = "DATA_FOUND";
        } else
        {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
   
    public function actionSingleTeacher()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $employee_id = Yii::app()->user->profileId;
        $id = Yii::app()->request->getPost('id');
        if ($id && Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
        {
            $classwork = new Classworks();
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
            $classwork_data = $classwork->getClassworkTeacher($employee_id, $page_number, $page_size,1, $id);
            if ($classwork_data)
            {

                $response['data']['classwork'] = $classwork_data[0];
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Found";
            }
            else
            {
                $response['data']['total'] = 0;
                $response['data']['has_next'] = false;
                $response['data']['classwork'] = array();
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Not Found";
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
    
    
   
     
    public function actionSingleClasswork()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $id = Yii::app()->request->getPost('id');
            $response = array();
            if ($id && Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )))
            {
                if (Yii::app()->user->isParent)
                {
                    $batch_id = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                $classwork = new Classworks();
                $classwork_data = $classwork->getClasswork($batch_id, $student_id, "", 1, null, 1, 1,$id);
                if ($classwork_data)
                {
                    $robject = new Reminders();
                    $robject->ReadReminderNew(Yii::app()->user->id, 0 ,31, $id);
                    
                    $response['data']['classwork'] = $classwork_data[0];
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                }
                else
                {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "Data Not Found";
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
    
    public function actionSubjects()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )))
            {
                if (Yii::app()->user->isParent)
                {
                    $batch_id = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                
                $subject = new Subjects();
                $all_subject = $subject->getSubject($batch_id, $student_id);
                $response['data']['subject'] = $all_subject;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Found";
                
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

    public function actionIndex()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )))
            {
                if (Yii::app()->user->isParent)
                {
                    $batch_id = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else
                {
                    $batch_id = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                $classwork = new Classworks();

                $page_number = Yii::app()->request->getPost('page_number');

                $page_size = Yii::app()->request->getPost('page_size');

                $subject_id = Yii::app()->request->getPost('subject_id');
                
                

                if (empty($page_number))
                {
                    $page_number = 1;
                }
                if (empty($page_size))
                {
                    $page_size = 10;
                }

                if (empty($subject_id))
                {
                    $subject_id = NULL;
                }
                
                
                $classwork_data = $classwork->getClasswork($batch_id, $student_id, "", $page_number, $subject_id, $page_size, 1, 0);
                if ($classwork_data)
                {

                    $response['data']['total'] = $classwork->getClassworkTotal($batch_id, $student_id, "", $subject_id, 1);
                    $has_next = false;
                    if ($response['data']['total'] > $page_number * $page_size)
                    {
                        $has_next = true;
                    }
                    $response['data']['has_next'] = $has_next;
                    $response['data']['classwork'] = $classwork_data;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Found";
                }
                else
                {
                    $response['data']['total'] = 0;
                    $response['data']['has_next'] = false;
                    $response['data']['classwork'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "Data Not Found";
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

    public function actionGetSubject()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher)
        {
            $emplyee_subject = new EmployeesSubjects();
            $subjects = $emplyee_subject->getSubject(Yii::app()->user->profileId);
            $response['data']['subjects'] = $subjects;
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

    public function actionTeacherClasswork()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $draft = Yii::app()->request->getPost('draft');
        $employee_id = Yii::app()->user->profileId;
        if (Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin) )
        {
            $classwork = new Classworks();
            $page_number = Yii::app()->request->getPost('page_number');
            $page_size = Yii::app()->request->getPost('page_size');
            $subject_id = Yii::app()->request->getPost('subject_id');
            
                
            if (empty($page_number))
            {
                $page_number = 1;
            }
            if (empty($page_size))
            {
                $page_size = 10;
            }
            if (empty($subject_id))
            {
                    $subject_id = NULL;
            }
            $is_published = 1;
            if($draft)
            {
                $is_published = 0;
            }    
            $classwork_data = $classwork->getClassworkTeacher($employee_id, $page_number, $page_size,$is_published,0,$subject_id);
            if ($classwork_data)
            {

                $response['data']['total'] = $classwork->getClassworkTotalTeacher($employee_id,$is_published,$subject_id);
                $has_next = false;
                if ($response['data']['total'] > $page_number * $page_size)
                {
                    $has_next = true;
                }
                $response['data']['has_next'] = $has_next;
                $response['data']['classwork'] = $classwork_data;
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Found";
            }
            else
            {
                $response['data']['total'] = 0;
                $response['data']['has_next'] = false;
                $response['data']['classwork'] = array();
                $response['status']['code'] = 200;
                $response['status']['msg'] = "Data Not Found";
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
    
    public function actionPublishClasswork()
    {
       $id = Yii::app()->request->getPost('id'); 
       $user_secret = Yii::app()->request->getPost('user_secret');
       if(Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $id)
       {
           $classwork = new Classworks();
           $eclasswork = $classwork->findByPk($id);
           if($eclasswork)
           {
                $eclasswork->is_published = 1; 
                $eclasswork->created_at = date("Y-m-d H:i:s"); 
                $eclasswork->save();
                $studentsubjectobj = new StudentsSubjects();

                $subobj = new Subjects();
                $subject_details = $subobj->findByPk($eclasswork->subject_id);



                $stdobj = new Students();

                $students1 = $stdobj->getStudentByBatch($subject_details->batch_id);
                $students2 = $studentsubjectobj->getSubjectStudent($eclasswork->subject_id);
                $students = array_unique(array_merge($students1, $students2));
                
                $notification_ids = array();
                $reminderrecipients = array();
                foreach ($students as $value)
                {
                    $studentsobj = $stdobj->findByPk($value);
                    $reminderrecipients[] = $studentsobj->user_id;
                    $batch_ids[$studentsobj->user_id] = $studentsobj->batch_id;
                    $student_ids[$studentsobj->user_id] = $studentsobj->id;
                    
                    $gstudent = new GuardianStudent(); 
                    $all_g = $gstudent->getGuardians($studentsobj->id);

                    if ($all_g)
                    {
                        foreach($all_g as $value)
                        {
                            $gr = new Guardians();
                            if (isset($value['guardian']) && isset($value['guardian']->id))
                            {
                                $grdata = $gr->findByPk($value['guardian']->id);
                                if($grdata && $grdata->user_id && !in_array($grdata->user_id, $reminderrecipients))
                                {
                                    $reminderrecipients[] = $grdata->user_id;
                                    $batch_ids[$grdata->user_id] = $studentsobj->batch_id;
                                    $student_ids[$grdata->user_id] = $studentsobj->id;
                                }
                            }
                        }    

                    }
                } 
                foreach ($reminderrecipients as $value)
                {
                    $reminder = new Reminders();
                    $reminder->sender = Yii::app()->user->id;
                    $reminder->subject = Settings::$ClassworkText . ":" . $eclasswork->title;
                    $reminder->body = Settings::$ClassworkText . " Added for " . $subject_details->name . " Please check the classwork For details";
                    $reminder->recipient = $value;
                    $reminder->school_id = Yii::app()->user->schoolId;
                    $reminder->rid = $eclasswork->id;
                    $reminder->rtype = 31;
                    $reminder->batch_id = $batch_ids[$value];
                    $reminder->student_id = $student_ids[$value];
                    $reminder->created_at = date("Y-m-d H:i:s");
                    $reminder->updated_at = date("Y-m-d H:i:s");
                    $reminder->save();
                    $notification_ids[] = $reminder->id;
                    //Settings::sendCurlNotification($value, $reminder->id);
                }
                if($notification_ids)
                {
                    $notification_id = implode("*", $notification_ids);
                    $user_id = implode("*", $reminderrecipients);
                    shell_exec("php pushnoti.php $notification_id $user_id  > /dev/null 2>/dev/null &");
                    //Settings::sendCurlNotification($user_id, $notification_id);
                }
                $response['status']['code'] = 200;
                $response['status']['msg'] = "SUCCESS";
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
    private function copy_classwork($origin,$file_name,$classwork)
    {
        $classwork->attachment_updated_at = date("Y-m-d H:i:s");
        $classwork->updated_at = date("Y-m-d H:i:s");
                    
        $attachment_datetime_chunk = explode(" ", $classwork->updated_at);

        $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
        $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

        $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
        $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];

        $uploads_dir = Settings::$paid_image_path . "uploads/classworks/attachments/".$classwork->id."/original/";
        $file_name_new =  str_replace(" ", "+",$file_name) . "?" .$attachment_extra;
        
        
        if(!is_dir($uploads_dir))
        {
            @mkdir($uploads_dir, 0777, true);
        }
        
        $uploads_dir = $uploads_dir.$file_name_new;
        

        if(@copy($origin, "$uploads_dir"))
        {
            $classwork->attachment_file_name = $file_name;
            $classwork->save();
        }
        return $uploads_dir;
    }
    private function upload_classwork($file,$classwork)
    {
        $classwork->attachment_updated_at = date("Y-m-d H:i:s");
        $classwork->updated_at = date("Y-m-d H:i:s");
                    
        $attachment_datetime_chunk = explode(" ", $classwork->updated_at);

        $attachment_date_chunk = explode("-", $attachment_datetime_chunk[0]);
        $attachment_time_chunk = explode(":", $attachment_datetime_chunk[1]);

        $attachment_extra = $attachment_date_chunk[0] . $attachment_date_chunk[1] . $attachment_date_chunk[2];
        $attachment_extra.= $attachment_time_chunk[0] . $attachment_date_chunk[1] . $attachment_time_chunk[2];

        $uploads_dir = Settings::$paid_image_path . "uploads/classworks/attachments/".$classwork->id."/original/";
        $file_name =  str_replace(" ", "+",$file['attachment_file_name']['name']) . "?" .$attachment_extra;
        $tmp_name = $file["attachment_file_name"]["tmp_name"];
        
        if(!is_dir($uploads_dir))
        {
            @mkdir($uploads_dir, 0777, true);
        }
        
        $uploads_dir = $uploads_dir.$file_name;
        

        if(@move_uploaded_file($tmp_name, "$uploads_dir"))
        {
            $classwork->attachment_file_name = $file['attachment_file_name']['name'];
            $classwork->save();
        }
        return $uploads_dir;
    }

    public function actionAddClasswork()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $subject_ids = Yii::app()->request->getPost('subject_id');
        $content = Yii::app()->request->getPost('content');
        $title = Yii::app()->request->getPost('title');
        $is_draft = Yii::app()->request->getPost('is_draft');
        $classwork_type = Yii::app()->request->getPost('type');
       
        $school_id = Yii::app()->user->schoolId;
        $id = Yii::app()->request->getPost('id');

        if (Yii::app()->user->user_secret === $user_secret && Yii::app()->user->isTeacher && $subject_ids && $content && $title && $school_id && $classwork_type)
        {
            
            
            $subject_id_array = explode(",", $subject_ids);
            if($subject_id_array)
            {
                foreach($subject_id_array as $subject_id)
                {
                    if($id)
                    {
                        $objclasswork = new Classworks();
                        $classwork = $objclasswork->findByPk($id);
                    }
                    else
                    {
                        $classwork = new Classworks();
                    }
             
                    $classwork->subject_id = $subject_id;
                    $classwork->content = $content;
                    $classwork->title = $title;
                    $classwork->school_id = Yii::app()->user->schoolId;
                    $classwork->employee_id = Yii::app()->user->profileId;
                    $classwork->classwork_type = $classwork_type;

                    if($is_draft)
                    {
                        $classwork->is_published = 0; 
                    }


                    $classwork->created_at = date("Y-m-d H:i:s");
                    if(!$id)
                    {
                        $classwork->updated_at = date("Y-m-d H:i:s");
                    }




                    $studentsubjectobj = new StudentsSubjects();

                    $subobj = new Subjects();
                    $subject_details = $subobj->findByPk($subject_id);



                    $stdobj = new Students();

                    $students1 = $stdobj->getStudentByBatch($subject_details->batch_id);
                    $students2 = $studentsubjectobj->getSubjectStudent($subject_id);

                    $students = array_unique(array_merge($students1, $students2));
                    $classwork->student_list = implode(",", $students);
                    $classwork->save();
                    

                    if (isset($_FILES['attachment_file_name']['name']) && !empty($_FILES['attachment_file_name']['name']))
                    {
                        if(!isset($file_name_main) && !isset($origin) )
                        {
                            $file_name_main = $_FILES['attachment_file_name']['name'];

                            $classwork->updated_at = date("Y-m-d H:i:s");
                            $classwork->attachment_content_type = Yii::app()->request->getPost('mime_type');
                            $classwork->attachment_file_size = Yii::app()->request->getPost('file_size');
                            $origin = $this->upload_classwork($_FILES, $classwork);
                        }
                        else
                        {
                            $classwork->updated_at = date("Y-m-d H:i:s");
                            $classwork->attachment_content_type = Yii::app()->request->getPost('mime_type');
                            $classwork->attachment_file_size = Yii::app()->request->getPost('file_size');
                            $origin = $this->copy_classwork($origin,$file_name_main, $classwork);
                        }    
                        
                    }



                    if(!$is_draft)
                    {
                        $notification_ids = array();
                        $reminderrecipients = array();
                        foreach ($students as $value)
                        {
                            $studentsobj = $stdobj->findByPk($value);
                            $reminderrecipients[] = $studentsobj->user_id;
                            $batch_ids[$studentsobj->user_id] = $studentsobj->batch_id;
                            $student_ids[$studentsobj->user_id] = $studentsobj->id;

                            $gstudent = new GuardianStudent(); 
                            $all_g = $gstudent->getGuardians($studentsobj->id);

                            if ($all_g)
                            {
                                foreach($all_g as $value)
                                {
                                    $gr = new Guardians();
                                    if (isset($value['guardian']) && isset($value['guardian']->id))
                                    {
                                        $grdata = $gr->findByPk($value['guardian']->id);
                                        if($grdata && $grdata->user_id && !in_array($grdata->user_id, $reminderrecipients))
                                        {
                                            $reminderrecipients[] = $grdata->user_id;
                                            $batch_ids[$grdata->user_id] = $studentsobj->batch_id;
                                            $student_ids[$grdata->user_id] = $studentsobj->id;
                                        }
                                    }    
                                }    

                            }
                        }    
                        foreach ($reminderrecipients as $value)
                        {
                            $reminder = new Reminders();
                            $reminder->sender = Yii::app()->user->id;
                            $reminder->subject = Settings::$ClassworkText . ":" . $title;
                            $reminder->body = Settings::$ClassworkText . " Added for " . $subject_details->name . " Please check the classwork For details";
                            $reminder->recipient = $value;
                            $reminder->school_id = Yii::app()->user->schoolId;
                            $reminder->rid = $classwork->id;
                            $reminder->rtype = 31;
                            $reminder->batch_id = $batch_ids[$value];
                            $reminder->student_id = $student_ids[$value];
                            $reminder->created_at = date("Y-m-d H:i:s");
                            $reminder->updated_at = date("Y-m-d H:i:s");
                            $reminder->save();
                            $notification_ids[] = $reminder->id;
                            //Settings::sendCurlNotification($value, $reminder->id);
                        }
                        if($notification_ids)
                        {
                            $notification_id = implode("*", $notification_ids);
                            $user_id = implode("*", $reminderrecipients);
                            shell_exec("php pushnoti.php $notification_id $user_id  > /dev/null 2>/dev/null &");
                            //Settings::sendCurlNotification($user_id, $notification_id);
                        }
                    }
                }
                
            }    


            $response['status']['code'] = 200;
            $response['status']['msg'] = "SUCCESS";
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
