<?php
class ReportController extends Controller
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
                'actions' => array('index','addauthforexam','getconnectexam','tabulation','continues','groupexamsubject','getexamclass','subjectreport','getsectionreport','groupedexamreport','getTermReportAll','progressall','attendence','getsubject','progress','classtestreport','allexam', 'Getfullreport','getexamreport','acknowledge'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    public function actionAddAuthForExam()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || Yii::app()->user->isParent))
            {
                $class_pay = 0;
                $class_pay_key = "";
                $class_pay_id = 0;
                $userobj = new Users();
                $user_data = $userobj->findByPk(Yii::app()->user->id);
                $userauth = new Userauth();
                $userauth->user_id = Yii::app()->user->id;
                $userauth->expire = date("Y-m-d H:i:s", strtotime("+5 minutes"));
                $userauth->auth_id = mt_rand();
                $userauth->save();
                
                $schoolObj = new Schools();
                $school_data = $schoolObj->findByPk(Yii::app()->user->schoolId);
                
                if($user_data->new_id  && $school_data && $school_data->classpay_active == 1)
                {
                   $class_pay_id = $user_data->new_id;
                   $class_pay_key = mt_rand();
                   $class_pay = 1;
                   $userkey = new Userkey(); 
                   $userkey->user_id = $user_data->new_id;
                   $userkey->expiry_date = date("Y-m-d H:i:s", strtotime("+12 hours"));
                   $userkey->has_key = $class_pay_key;
                   $userkey->save();
                }
                
                $school_domain = new SchoolDomains();
                $school_domain = $school_domain->getSchoolDomainBySchoolId(Yii::app()->user->schoolId);
                
                if($school_domain)
                {
                    $response['data']['domain'] = $school_domain->domain;
                    $response['data']['auth_id'] = $userauth->auth_id;
                    $response['data']['class_pay'] = $class_pay;
                    $response['data']['class_pay_key'] = $class_pay_key;
                    $response['data']['class_pay_id'] = $class_pay_id;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "GO_FOR_EXAM";
                }
                else
                {
                    $response['status']['code'] = 403;
                    $response['status']['msg'] = "Access Denied.";
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
    
    public function actiongetConnectExam()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || 
                    ( Yii::app()->user->isParent && Yii::app()->request->getPost('batch_id') ))
                )
            {
                
                if(Yii::app()->user->isParent)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                } 
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }   
                 
                $examConnect = new ExamConnect();
                $exam_report = $examConnect->getConnectExam($batch_id,$student_id);
                if ($exam_report) {
                    
                    $response['data']['exams'] = $exam_report;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_FOUND";
                } else {
                    $response['data']['exams'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_FOUND";
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
    
    public function actionGroupExamSubject()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $subject_id = Yii::app()->request->getPost('subject_id');
        $all_section = Yii::app()->request->getPost('all_section');
        $data_type = 4;
        if(isset($all_section) && $all_section == "1")
        {
           $data_type = 5;
        }
        $is_finished = Yii::app()->request->getPost('is_finished');
        if(!$is_finished)
        {
            $is_finished = 0;
        }
        
        $id = Yii::app()->request->getPost('id');
        if($id && $subject_id && Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
            $objPreviousExam = new PreviousExams();
            $finish_exam = $objPreviousExam->getFinishExam($id,$data_type,$subject_id);
            if($finish_exam)
            {
                echo $finish_exam;
                Yii::app()->end();
                exit;
            }
            
            $examcon = new ExamConnect();
            $subject_ids = [];
            
            if(isset($all_section) && $all_section == "1")
            {
                $find_all_batches = $batchData->getBatchsByName(false,$courseData->course_name);
                $new_connect_exam_id = array(); 
                $new_subject_id = array();
                $subjectObj = new Subject();
                $subject_data = $subjectObj->findByPk($subject_id);
                if($find_all_batches)
                {
                    foreach($find_all_batches as $value)
                    {
                        $new_exam = $connectexmObj->getConnectExamByBatch($value->id,$data->result_type,$data->name);
                        $subject_id = $subjectObj->getSubjectByBatchCode($subject_data,$value->id);
                        if($new_exam && $subject_id)
                        {
                            $result[] = $examcon->getConnectExamReportAll($new_exam,$subject_id);
                            $subject_ids[] = $subject_id;
                        }
                    }    
                }
                
            }
            else
            {    
                $result = $examcon->getConnectExamReportAll($id,$subject_id);
            }
           
            $response['data']['result']       = $result;
            $response['data']['subject_ids']  = $subject_ids;
            $response['status']['code']       = 200;
            $response['status']['msg']        = "Data Found"; 
            
            $objPreviousExam->saveExam($id,CJSON::encode($response),$is_finished,$data_type,$subject_id);
        }
        else
        {
           $response['status']['code'] = 404;
           $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }  
    public function actiongetExamClass()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $class_name = Yii::app()->request->getPost('class_name');
        if($class_name && Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
            $examgroup = new ExamGroups();
            $all_exam = $examgroup->getAllExamsClass($class_name);
            
          
            $exam_section = array();
            
            foreach ($all_exam as $value) 
            {
                if(!in_array($value->name, $exam_section))
                {
                    $exam_section[] = $value->name;
                }
            }
            $response['data']['exams']       = $exam_section;
            $response['status']['code']       = 200;
            $response['status']['msg']        = "Data Found"; 
        }
        else
        {
           $response['status']['code'] = 404;
           $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
//    public function actionContinuesTest()
//    {
//        $user_secret = Yii::app()->request->getPost('user_secret');
//        $connect_exam_id = Yii::app()->request->getPost('connect_exam_id');
//        $batch_id = Yii::app()->request->getPost('batch_id');
//        $response = array();
//        if ($connect_exam_id && Yii::app()->user->user_secret === $user_secret &&  (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin ))
//        {
//
//            $groupexam = new GroupedExams();
//            $exam_report = $groupexam->getContinuesResultTest($batch_id,$connect_exam_id);
//            //$attandence = new Attendances();
//            //$adata = $attandence->getTotalPrsent($batch_id, $connect_exam_id,$exam_report['students']);
//
//
//            if ($exam_report) {
//
//                $response['data']['report'] = $exam_report;
//                //$response['data']['total'] = $adata[0];
//                //$response['data']['present_all'] = $adata[1];
//                $response['status']['code'] = 200;
//                $response['status']['msg'] = "EXAM_REPORT_FOUND";
//            } else {
//                $response['data']['report'] = array();
//                $response['status']['code'] = 200;
//                $response['status']['msg'] = "EXAM_REPORT_FOUND";
//            }  
//        }
//        else
//        {
//            $response['status']['code'] = 403;
//            $response['status']['msg'] = "Access Denied.";
//        }
//         
//        
//        echo CJSON::encode($response);
//        Yii::app()->end();
//    }        
    public function actionContinues()
    {
       
        $user_secret = Yii::app()->request->getPost('user_secret');
        $connect_exam_id = Yii::app()->request->getPost('connect_exam_id');
        $is_finished = Yii::app()->request->getPost('is_finished');
        $is_unsolved = Yii::app()->request->getPost('is_unsolved');
        if(!$is_finished)
        {
            $is_finished = 0;
        }
        if(!$is_unsolved)
        {
            $is_unsolved = 0;
        }
        $batch_id = Yii::app()->request->getPost('batch_id');
        $response = array();
        if ($connect_exam_id && Yii::app()->user->user_secret === $user_secret)
        {
            $objPreviousExam = new PreviousExams();
            $finish_exam = $objPreviousExam->getFinishExam($connect_exam_id);
            if($finish_exam)
            {
                echo $finish_exam;
                Yii::app()->end();
                exit;
            }
            $cont_exam = new ExamConnect();
            $first_term_id = $cont_exam->getConnectExamFirstTerm($batch_id);
            $this_term = $cont_exam->findByPk($connect_exam_id);

            $previous_exam = 0;
            if($first_term_id && $first_term_id!=$connect_exam_id)
            {
                $previous_exam = $first_term_id;
            }
            $first_term_id_kg = 0;
            if($this_term->result_type == 6)
            {
                $first_term_id_kg = $cont_exam->getConnectExamKgFirstTerm($batch_id);
            } 
            

            $groupexam = new GroupedExams();
            $exam_report = $groupexam->getContinuesResult($batch_id,$connect_exam_id,$previous_exam,$is_unsolved);
            $attandence = new Attendances();
            $adata = $attandence->getTotalPrsent($batch_id, $connect_exam_id,$exam_report['students']);
            
            $adata_first_term = array();
            if($first_term_id && $first_term_id!=$connect_exam_id)
            {
                $adata_first_term = $attandence->getTotalPrsent($batch_id, $first_term_id, $exam_report['students']);
            }

            if($first_term_id_kg)
            {
                $adata_first_term = $attandence->getTotalPrsent($batch_id, $first_term_id_kg, $exam_report['students']);
            }
            
            
        
                
                
            


            if ($exam_report) {

                $response['data']['report'] = $exam_report;
                $response['data']['total'] = $adata[0];
                $response['data']['present_all'] = $adata[1];
                $response['data']['absent_all'] = $adata[3];
                $response['data']['total_new'] = $adata[2];
                $response['data']['first_term_id'] = 0;
                if($adata_first_term)
                {
                    $response['data']['first_term_total'] = $adata_first_term[0];
                    $response['data']['first_term_present_all'] = $adata_first_term[1];
                    $response['data']['first_term_absent_all'] = $adata_first_term[3];
                    $response['data']['first_term_total_new'] = $adata_first_term[2]; 
                    $response['data']['first_term_id'] = $first_term_id;
                }
                $response['status']['code'] = 200;
                $response['status']['msg'] = "EXAM_REPORT_FOUND";
                $objPreviousExam->saveExam($connect_exam_id,CJSON::encode($response),$is_finished);
            } else {
                $response['data']['report'] = array();
                $response['status']['code'] = 200;
                $response['status']['msg'] = "EXAM_REPORT_FOUND";
            }  
        }
        else
        {
            $response['status']['code'] = 403;
            $response['status']['msg'] = "Access Denied.";
        }
         
        
        echo CJSON::encode($response);
        Yii::app()->end();
    } 
    public function actionTabulation()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $connect_exam_id = Yii::app()->request->getPost('connect_exam_id');
            
            $batch_id = Yii::app()->request->getPost('batch_id');
            $all_class_report = Yii::app()->request->getPost('all_class_report');
            $failed_list = Yii::app()->request->getPost('failed_list');
            $data_type = 2;
            if($all_class_report)
            {
               $data_type = 3;
            }
            $is_finished = Yii::app()->request->getPost('is_finished');
            if(!$is_finished)
            {
               $is_finished = 0;
            }
            $response = array();
            $new_connect_exam_id = array();
            if ($connect_exam_id && Yii::app()->user->user_secret === $user_secret &&  (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin || Yii::app()->user->isStudent || Yii::app()->user->isParent ))
            {
                $connectexmObj = new ExamConnect();
                $data = $connectexmObj->findByPk($connect_exam_id);
                
                $objPreviousExam = new PreviousExams();
                if($data->is_published == 1)
                {
                    $finish_exam = $objPreviousExam->getFinishExamALL($connect_exam_id,$data_type);
                }
                else {
                    $finish_exam = $objPreviousExam->getFinishExam($connect_exam_id,$data_type);
                }
                if($finish_exam)
                {
                    echo $finish_exam;
                    Yii::app()->end();
                    exit;
                }
                if($all_class_report)
                {
                   
                    $objBatch = new Batches();
                    $batchData = $objBatch->findByPk($data->batch_id);
                    
                    $courseObj = new Courses();
                    $courseData = $courseObj->findByPk($batchData->course_id);
                    
                    if($failed_list)
                    {
                        $find_all_batches = $batchData->getBatchsByName();
                    }
                    else
                    {
                        $find_all_batches = $batchData->getBatchsByName(false,$courseData->course_name); 
                    }    
                    
                    $new_connect_exam_id = array(); 
                    if($find_all_batches)
                    {
                        foreach($find_all_batches as $value)
                        {
                            $new_exam = $connectexmObj->getConnectExamByBatch($value->id,$data->result_type,$data->name);
                            if($new_exam)
                            {
                                $new_connect_exam_id[] = $new_exam;
                            }
                        }    
                    }
                    
                    
                }    
                
                $groupexam = new GroupedExams();
                $cont_exam = new ExamConnect();
                $batch_ids =  array();
                $adata = [];
                $adata_first_term = [];
                $first_term_id = 0;
                if($all_class_report)
                {
                   $exam_report = array();
                   
                   if($new_connect_exam_id)
                   {
                       foreach($new_connect_exam_id as $value)
                       {
                            $examData = $connectexmObj->findByPk($value);
                            $exam_report_main = $exam_report[] = $groupexam->getTabulation($examData->batch_id,$value);
                            if(isset($exam_report_main['students']))
                            {
                                $batch_ids[] = $examData->batch_id;
                                $first_term_id = $cont_exam->getConnectExamFirstTerm($examData->batch_id);

                                $attandence = new Attendances();
                                $adata[$examData->batch_id] = $attandence->getTotalPrsent($examData->batch_id, $value,$exam_report_main['students']);

                                if($first_term_id && $first_term_id!=$value)
                                {
                                   $adata_first_term[$examData->batch_id] = $attandence->getTotalPrsent($examData->batch_id, $first_term_id, $exam_report_main['students']);
                                }
                            }
                       }    
                   }
                   
                }
                else 
                {
                  
                   $exam_report = $groupexam->getTabulation($batch_id,$connect_exam_id);
                   
                   $first_term_id = $cont_exam->getConnectExamFirstTerm($batch_id);
                   
                   $attandence = new Attendances();
                  
                   $adata = $attandence->getTotalPrsent($batch_id, $connect_exam_id,$exam_report['students']);
                   
                   $adata_first_term = array();
                   if($first_term_id && $first_term_id!=$connect_exam_id)
                   {
                      $adata_first_term = $attandence->getTotalPrsent($batch_id, $first_term_id, $exam_report['students']);
                   }
                   
                }
                
                if ($exam_report) {
                    $response['data']['report'] = $exam_report;
                    $response['data']['batches'] = $batch_ids;
                    $response['data']['connect_exams'] = $new_connect_exam_id;
                    $response['data']['adata'] = $adata;
                    $response['data']['adata_first_term'] = $adata_first_term;
                    
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_REPORT_FOUND";
                    $objPreviousExam->saveExam($connect_exam_id,CJSON::encode($response),$is_finished,$data_type);
                    
                } else {
                    $response['data']['report'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_REPORT_FOUND";
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
    public function actiongroupedExamReport()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $connect_exam_id = Yii::app()->request->getPost('connect_exam_id');
            $response = array();
            if ($connect_exam_id && Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || ( (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin )  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                $batch_id_student_send = Yii::app()->request->getPost('batch_id');
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher || Yii::app()->user->isAdmin)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else if($batch_id_student_send)
                {
                    $batch_id   = $batch_id_student_send;
                    $student_id = Yii::app()->user->profileId;
                } 
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }   
                
                $cont_exam = new ExamConnect();
                $first_term_id = $cont_exam->getConnectExamFirstTerm($batch_id);
                $this_term = $cont_exam->findByPk($connect_exam_id);
                
                $previous_exam = 0;
                if($first_term_id && $first_term_id!=$connect_exam_id)
                {
                    $previous_exam = $first_term_id;
                }
                
                $first_term_id_kg = 0;
                if($this_term->result_type == 6)
                {
                    $first_term_id_kg = $cont_exam->getConnectExamKgFirstTerm($batch_id);
                }    
                
                
                $groupexam = new GroupedExams();
                $exam_report = $groupexam->getGroupedExamReport($batch_id, $student_id, $connect_exam_id, $previous_exam);
                
                
                
                
                
                
                $attandence = new Attendances();
                $adata = $attandence->getStudentTotalPrsent($batch_id, $student_id, $connect_exam_id);
                
                $adata_first_term = array();
                if($first_term_id && $first_term_id!=$connect_exam_id)
                {
                    $adata_first_term = $attandence->getStudentTotalPrsent($batch_id, $student_id, $first_term_id);
                }
                
                if($first_term_id_kg)
                {
                    $adata_first_term = $attandence->getStudentTotalPrsent($batch_id, $student_id, $first_term_id_kg);
                }
                
                if ($exam_report) {
                    
                    $response['data']['report'] = $exam_report;
                    $response['data']['total'] = $adata[0];
                    $response['data']['present'] = $adata[1];
                    $response['data']['total_new_std'] = $adata[2];
                    $response['data']['absent'] = $adata[3];
                    if($adata_first_term)
                    {
                        $response['data']['total_first_term'] = $adata_first_term[0];
                        $response['data']['present_first_term'] = $adata_first_term[1];
                        $response['data']['total_new_std_first_term'] = $adata_first_term[2];
                        $response['data']['absent_first_term'] = $adata_first_term[3];
                    }
                    
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_REPORT_FOUND";
                } else {
                    $response['data']['report'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_REPORT_FOUND";
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
    public function actiongetSectionReport()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $class_name = Yii::app()->request->getPost('class_name'); 
        $exam_name = Yii::app()->request->getPost('exam_name'); 
        if($class_name && $exam_name && Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
            $examgroup = new ExamGroups();
            $all_exam = $examgroup->getAllExamsPublishClass($class_name, $exam_name);
            
            $report_section_shift = array();
            
            $check_batch_name = false;
            $batch_name = "";
            foreach ($all_exam as $value) 
            {
                if(!$batch_name)
                {
                    $batch_name = $value['Batches']->name;
                }
                else if($batch_name!=$value['Batches']->name)
                {
                    $check_batch_name = true;
                    break;
                }
            }
            
            foreach ($all_exam as $value) 
            {
             
                $subjects = new Subjects();
                if($check_batch_name === true)
                {
                    if($value['Batches']->name=="General")
                    {
                        $report_section_shift[$value['Batches']['courseDetails']->section_name] = $subjects->getTotalMarkPercent($value->id);
                    }
                    else
                    {    
                        $report_section_shift[$value['Batches']->name."-".$value['Batches']['courseDetails']->section_name] = $subjects->getTotalMarkPercent($value->id);
                    }
                }
                else 
                {
                     $report_section_shift[$value['Batches']['courseDetails']->section_name] = $subjects->getTotalMarkPercent($value->id);
                }
            }
            $alter_report = array();
            
            foreach($report_section_shift as $key=>$value)
            {
                foreach($value as $vkey=>$vvalue)
                {
                    $alter_report[$vkey][$key] = $vvalue;
                }
            }
            $response['data']['result']       = $report_section_shift;
            $response['status']['code']       = 200;
            $response['status']['msg']        = "Data Found"; 
        }
        else
        {
           $response['status']['code'] = 404;
           $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }        
    public function actiongetTermReportAll()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $id = Yii::app()->request->getPost('id');
        $mark_sheet = Yii::app()->request->getPost('mark_sheet');
        
        if(Yii::app()->user->user_secret === $user_secret && (Yii::app()->user->isTeacher || Yii::app()->user->isAdmin))
        {
           $subjects = new Subjects();
           $result = $subjects->getTermReportAll($id,$mark_sheet);
           $response['data']['result']       = $result;
           $response['status']['code']       = 200;
           $response['status']['msg']        = "Data Found"; 
        }
        else
        {
           $response['status']['code'] = 404;
           $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    public function actionsubjectReport()
    {
        $user_secret = Yii::app()->request->getPost('user_secret');
        $subject_id = Yii::app()->request->getPost('subject_id');
        $student_id = Yii::app()->request->getPost('student_id');
        $exam_group_id = Yii::app()->request->getPost('exam_group_id');
        $exam_id = Yii::app()->request->getPost('exam_id');
        if(Yii::app()->user->user_secret === $user_secret && $subject_id && ($exam_id || $exam_group_id)  && $student_id)
        {
           $subjects = new Subjects();
           $sub_details = $subjects->findByPk($subject_id);
           if(!$exam_group_id)
           {
              $exam_group_id = 0;
           }
           if(!$exam_id)
           {
              $exam_id = 0; 
           }    
           $result = $subjects->getSubjectReport($subject_id, $student_id,$exam_group_id,$exam_id);
           $response['data']['result']                       = $result;
           $response['data']['result']['subject_id']         = $sub_details->id;
           $response['data']['result']['subject_name']       = $sub_details->name;
           $response['data']['result']['subject_icon']       = $sub_details->icon_number;
           $response['data']['result']['no_exams']           = $sub_details->no_exams;
           $response['status']['code']       = 200;
           $response['status']['msg']        = "Data Found"; 
        }
        else
        {
           $response['status']['code'] = 404;
           $response['status']['msg'] = "Bad Request";
        }
        echo CJSON::encode($response);
        Yii::app()->end();
    }        
    public function actionattendence()
    {
       $user_secret = Yii::app()->request->getPost('user_secret');
       if(Yii::app()->user->user_secret === $user_secret)
       {
            $school_id = Yii::app()->user->schoolId;
            $studentobj = new Students();
            if(Yii::app()->user->isParent)
            {
                $batch_id   = Yii::app()->request->getPost('batch_id');
                $student_id = Yii::app()->request->getPost('student_id');
                
                $stddata = $studentobj->findByPk($student_id);
                $user_id = $stddata->user_id;
            }
            else if(Yii::app()->user->isStudent)
            {
                $batch_id   = Yii::app()->user->batchId;
                $student_id = Yii::app()->user->profileId;
                $user_id = Yii::app()->user->id;
            }
            else
            {
                $batch_id = 0;
                
                $student_id = Yii::app()->user->profileId;
                $user_id = Yii::app()->user->id;
            } 
         
            $stddata = $studentobj->findByPk($student_id);
            $response['data']['profile_picture'] = "";
            
            $freobj = new Freeusers();
            $profile_image = Settings::getProfileImagePaid($user_id);
            if (isset($profile_image) && $profile_image)
            {
                $response['data']['profile_picture'] = $profile_image;
            }
            
            $text = "";
            $date = date("Y-m-d");
            $objattendence = new Attendances();
            $holiday = new Events();
            $holiday_array = $holiday->getHolidayMonth($date, $date, $school_id);
            if ($holiday_array)
            {
                $text = "Today is Holiday";
            }
            if(!$text)
            {
                $weekend_array = $objattendence->getWeekend($school_id,$batch_id);
                $weekday = date("w", strtotime($date));
                if (in_array($weekday, $weekend_array))
                {
                    $text = "Today is weekend";
                } 
            }
            if(!$text)
            {
                if(Yii::app()->user->isParent || Yii::app()->user->isStudent)
                {
                    $leave = new ApplyLeaveStudents();
                    $leave_array = $leave->getleaveStudentMonth($date, $date, $student_id);
                    if ($leave_array)
                    {
                        $text = "was on Leave Today";
                    }
                    if(!$text)
                    {
                        
                        $attendance_array = $objattendence->getAbsentStudentMonth($date, $date, $student_id);
                        if ($attendance_array['late'])
                        {
                            $text = "was Late Today";
                        }
                        else if ($attendance_array['absent'])
                        {
                            $text = "was Absent Today";
                        }
                        else
                        {
                            $timetableobj = new TimetableEntries();
                            $class_started = $timetableobj->classStarted($batch_id);
                            $card_att = true;
                            if(in_array($school_id,Settings::$card_attendence_school_employee_only))
                            {
                                $card_att = new CardAttendance();
                                $std_att = $card_att->getEmpAttExists($stddata->user_id,$date);
                                if($std_att == false)
                                {
                                   $card_att = false; 
                                }
                            }
                            if($class_started && $card_att)
                            {
                                $merging_data['attendence'] = "was Present Today";
                            }
                            else if($class_started && $card_att == false)
                            {
                                $merging_data['attendence'] = "Attendance Not Synced Yet";
                            }
                            else if($date==date("Y-m-d"))
                            {
                                $text = "Class yet not started";
                            }    
                            
                        }
                    }
                }
                else
                {
                    $leave = new ApplyLeaves();
                    $leave_array = $leave->getleaveTeacher($student_id);
                    if($leave_array)
                    {
                        $text = "was on Leave Today";
                    }
                    if(!$text)
                    {
                        $attendence = new EmployeeAttendances();
                        $leave_array = $attendence->getAttTeacher($student_id);
                        if($leave_array)
                        {
                            $text = "was Absent Today";
                        }
                        else
                        {
                            $text = "was Present Today";
                        }
                    }
                }
                
            }
            
            
           $response['data']['text']    = $text;
           $response['status']['code']       = 200;
           $response['status']['msg']        = "Data Found"; 
       }
       else
       {
           $response['status']['code'] = 403;
           $response['status']['msg'] = "Access Denied."; 
       }
       
       echo CJSON::encode($response);
       Yii::app()->end();
    }
    
    public function actionProgressAll()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $exam_category = Yii::app()->request->getPost('exam_category');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                $batch_id_student_send = Yii::app()->request->getPost('batch_id');
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else if($batch_id_student_send)
                {
                    $batch_id   = $batch_id_student_send;
                    $student_id = Yii::app()->user->profileId;
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                } 
                if(!$exam_category)
                {
                    $exam_category = 0;
                }
                $subjects = new Subjects();
                $progress = $subjects->getPrograssAll($batch_id, $student_id, $exam_category);
                
                if ($progress)
                {
                    $response['data']['progress']    = $progress;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $response['data']['progress']    = (object) null;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Not Found";
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
    
    public function actionProgress()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $subject_id = Yii::app()->request->getPost('subject_id');
            $exam_category = Yii::app()->request->getPost('exam_category');
            $response = array();
            if ($subject_id && Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                $batch_id_student_send = Yii::app()->request->getPost('batch_id');
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else if($batch_id_student_send)
                {
                    $batch_id   = $batch_id_student_send;
                    $student_id = Yii::app()->user->profileId;
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                } 
                if(!$exam_category)
                {
                    $exam_category = 0;
                }
                $subjects = new Subjects();
                $progress = $subjects->getPrograss($batch_id, $student_id, $subject_id, $exam_category);
                
                if ($progress)
                {
                    $response['data']['progress']    = $progress;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $progress['exam'] = array();
                    $response['data']['progress']    = $progress;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Not Found";
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
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                $batch_id_student_send = Yii::app()->request->getPost('batch_id');
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else if($batch_id_student_send)
                {
                    $batch_id   = $batch_id_student_send;
                    $student_id = Yii::app()->user->profileId;
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }    
                $subjects = new Subjects();
                $std_subjects = $subjects->getSubject($batch_id, $student_id);
                
                if ($std_subjects)
                {
                    $response['data']['subjects']    = $std_subjects;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $response['data']['subjects']    = array();
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Not Found";
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
    
    public function actionGetExamReport() {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $category_id = Yii::app()->request->getPost('category_id');
            $no_exams = Yii::app()->request->getPost('no_exams');
            $id = Yii::app()->request->getPost('id');
            $response = array();
            if ($id && Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                $batch_id_student_send = Yii::app()->request->getPost('batch_id');
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else if($batch_id_student_send)
                {
                    $batch_id   = $batch_id_student_send;
                    $student_id = Yii::app()->user->profileId;
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }   
                if(!$no_exams)
                {
                    $no_exams = 0;
                }  
                $subjects = new Subjects();
                $term_report = $subjects->getTermReport($batch_id, $student_id, $id, $no_exams);
                if ($term_report) {
                    $robject = new Reminders();
                    $robject->ReadReminderNew(Yii::app()->user->id, 0 ,3, $id);
                    
                    $response['data']['report'] = $term_report[0];
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_FOUND";
                } else {
                    $response['data']['report'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_NOT_FOUND";
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
    
    public function actionAllExam() {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $category_id = Yii::app()->request->getPost('category_id');
            $no_exams = Yii::app()->request->getPost('no_exams');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                $batch_id_student_send = Yii::app()->request->getPost('batch_id');
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else if($batch_id_student_send)
                {
                    $batch_id   = $batch_id_student_send;
                    $student_id = Yii::app()->user->profileId;
                }
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }   
                if(!$category_id)
                {
                    $category_id = 3;
                } 
                else if($category_id=="all")
                {
                    $category_id = 0;
                }  
                if(!$no_exams)
                {
                    $no_exams = 0;
                }
                $time_table = new ExamGroups();
                $time_table = $time_table->getAllExamsResultPublish($batch_id,$category_id,$no_exams);
                if ($time_table) {
                    $response['data']['all_exam'] = $time_table;
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_FOUND";
                } else {
                    $response['data']['all_exam'] = array();
                    $response['status']['code'] = 200;
                    $response['status']['msg'] = "EXAM_ROUTINE_FOUND";
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
    
    public function actionClassTestReport()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $exam_group = Yii::app()->request->getPost('exam_group');
            $no_exams = Yii::app()->request->getPost('no_exams');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                $batch_id_student_send = Yii::app()->request->getPost('batch_id');
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else if($batch_id_student_send)
                {
                    $batch_id   = $batch_id_student_send;
                    $student_id = Yii::app()->user->profileId;
                }    
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }  
                if(!$exam_group)
                {
                    $exam_group = 0;
                }   
                if(!$no_exams)
                {
                    $no_exams = 0;
                }
                
                $subjects = new Subjects();
                $exam_data = $subjects->getBatchSubjectClassTestProjectReport($batch_id, $student_id, $exam_group, $no_exams);
                
                if ($exam_data)
                {
                    $exam_ids = array();
                    foreach($exam_data as $exam_value)
                    {
                        foreach($exam_value['subject_exam']['class_test'] as $cvalue)
                        {
                           if(!in_array($cvalue['exam_id'], $exam_ids))
                           {
                               $exam_ids[] =  $cvalue['exam_group_id'];
                           }
                           
                        }
                        foreach($exam_value['subject_exam']['project'] as $pvalue)
                        {
                           if(!in_array($pvalue['exam_id'], $exam_ids))
                           {
                               $exam_ids[] =  $pvalue['exam_group_id'];
                           } 
                        }
                        
                    }    
                    
                  
                    $robject = new Reminders();
                    $robject->ReadReminderNew(Yii::app()->user->id, 0 ,3, $exam_ids);
                    
                    
                    $response['data']['class_test_report']    = $exam_data;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $response['data']['class_test_report']    = array();
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Not Found";
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
    
    public function actionGetfullreport()
    {
        if (isset($_POST) && !empty($_POST))
        {
            $user_secret = Yii::app()->request->getPost('user_secret');
            $no_exams = Yii::app()->request->getPost('no_exams');
            $response = array();
            if (Yii::app()->user->user_secret === $user_secret && ( Yii::app()->user->isStudent || (Yii::app()->user->isParent 
                   && Yii::app()->request->getPost('batch_id') && Yii::app()->request->getPost('student_id') )
                    || (Yii::app()->user->isTeacher  && Yii::app()->request->getPost('batch_id')  && Yii::app()->request->getPost('student_id'))))
            {
                $batch_id_student_send = Yii::app()->request->getPost('batch_id');
                if(Yii::app()->user->isParent || Yii::app()->user->isTeacher)
                {
                    $batch_id   = Yii::app()->request->getPost('batch_id');
                    $student_id = Yii::app()->request->getPost('student_id');
                }
                else if($batch_id_student_send)
                {
                    $batch_id   = $batch_id_student_send;
                    $student_id = Yii::app()->user->profileId;
                } 
                else
                {
                    $batch_id   = Yii::app()->user->batchId;
                    $student_id = Yii::app()->user->profileId;
                }
                if(!$no_exams)
                {
                    $no_exams = 0;
                }
                $subjects = new Subjects();
                $term_report = $subjects->getTermReport($batch_id, $student_id,$no_exams);
                
               
                $exam_data = $subjects->getBatchSubjectClassTestProjectReport($batch_id, $student_id,$no_exams);
                
                if ($exam_data || $term_report)
                {
                  
                    $response['data']['term_report']          = $term_report;
                    $response['data']['class_test_report']    = $exam_data;
                    $response['status']['code']       = 200;
                    $response['status']['msg']        = "Data Found";
                }
                else
                {
                    $response['status']['code']       = 404;
                    $response['status']['msg']        = "Data Not Found";
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
    
     public function actionAcknowledge() {

        if ((Yii::app()->request->isPostRequest) && !empty($_POST)) {

            $user_secret = Yii::app()->request->getPost('user_secret');
            
            $exam_id = Yii::app()->request->getPost('exam_id');

            if (empty($exam_id)) {
                $response['status']['code'] = 400;
                $response['status']['msg'] = "Bad Request.";
                echo CJSON::encode($response);
                Yii::app()->end();
            }

            if (Yii::app()->user->user_secret === $user_secret) {
                $school_id = Yii::app()->user->schoolId;
                $exam = new UserExamAcknowledge;
                $exam_data = $exam->acknowledgeExam($exam_id);

                if ($exam_data) {
                    $response['data']['exam_ack'] = $exam_data;
                } else {
                    $response['status']['code'] = 404;
                    $response['status']['msg'] = "NO_EXAM_ACKNOWLEDGED.";
                }
            } else {
                $response['status']['code'] = 403;
                $response['status']['msg'] = "Access Denied.";
            }
        } else {
            $response['status']['code'] = 400;
            $response['status']['msg'] = "Bad Request.";
        }

        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
}
