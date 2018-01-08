<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class paidstatictis extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->form_validation->CI = & $this;
        $this->load->library('Datatables');
        $this->load->library('table');
    }

    /**
     * Index function
     * @param None
     * @defination use for showing table header and setting table id for byline
     * @author Fahim
     */
    function index()
    {

        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('School Name','User Name','User Type','Action','Ip','Agent','Web','Date','Time');
        
        $this->db->dbprefix = '';
        $this->db->where("is_deleted",0);
        
        $obj_schools = $this->db->get('schools')->result();
        
        $this->db->dbprefix = 'tds_';

        $select_schools[NULL] = "Select";
        foreach ($obj_schools as $value)
        {
            $select_schools[$value->name] = $value->name;
        }
        
        $data['schools'] = $select_schools;
        $data['has_daterange'] = true;
        
        $data['user_type'] = array(NULL=>'Select',1 => 'Student', 2 => 'Parent', 3 => 'Teacher', 4=> 'Admin');
        $data['users_from'] = array(NULL=>'Select',1 => 'Web', 0 => 'Mobile');
        
        $this->render('admin/paidstatictis/index',$data);
    }

    /**
     * Datable function
     * @param none
     * @defination use for showing datatable for byline callback function
     * @author Fahim
     */
    function datatable()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $this->db->dbprefix = '';
        $this->datatables->set_controller_name("paidstatictis");
        $this->datatables->set_primary_key("id");
        
        
        $this->datatables->extra_string_replace_array(array(" index"=>""," show"=>"","employee_attendance"=>"Leave","assignments"=>"Homework"));
        $this->datatables->set_uc_word(true);
        
      
        
        $this->datatables->set_custom_string(4, array("user login" => 'Login', 
            "user logout" => 'Logout','reports index'=>'Reports','attendances index'=>'Attendances','attendances class_report'=>'View Attendances','employee_attendance index'=>'Leaves',
            'dashboards index'=>'Home','attendances student_report'=>'View Student Attendances','employee_attendance leaves'=>'Leaves','employee_attendance show'=>'Leaves',
            'data_palettes index'=>'Home','employee_attendance leave_history'=>'Leaves','assignments index'=>'Homework','assignments new'=>'Create Homework',
            'dashboards employee_task_data'=>'Task','exam create_exam'=>'Create Exam','student class_test_report'=>'Class Test Report','student term_test_report'=>'Term Report','student progress_report'=>'Progress Report',
            'dashboards employee_homework_data'=>'Homework',"dashboards exam_result_data_student"=>"Report Card","syllabus syllabus_view"=>"Syllabus","syllabus syllabus_by_term"=>"Syllabus",
            'dashboards employee_quiz_data'=>'Quize',"dashboards quize_data"=>"Quize","dashboards class_routine_data_student"=>"Class Routine","dashboards exam_routine_data_student"=>"Exam Routine",
            'dashboards employee_exam_routine_data'=>'Exam Routine',"student_attendance month_report"=>"Month Attendance Report","student_attendance year_report"=>"Yearly Attendance Report",
            'dashboards routine_data'=>'Class Routine',"user change_password"=>"Change Password",'dashboards homework_data'=>'Homework',
            'syllabus classes_view'=>'Syllabus',
            'syllabus show_syllabus'=>'Syllabus',
            'syllabus all'=>'Syllabus',
            'syllabus new'=>'Create syllabus',
            'calendar Index'=>'Calendar',
            'calendar holiday'=>'Calendar',
            'calendar others'=>'Calendar',
            ));
        
        
        
        $this->datatables->set_custom_string(3, array(1 => 'Student', 2 => 'Parent', 3 => 'Teacher', 4=> 'Admin'));
        $this->datatables->set_custom_string(7, array(1 => 'Yes', 0 => 'No'));
        $this->datatables->set_date_string(8);
        
        $this->datatables->select("activity_logs.id as primary_id,schools.name,CONCAT_WS(' ',users.first_name,users.last_name) as username"
                . ",activity_logs.user_type_paid,CONCAT_WS(' ',activity_logs.controller,"
                . "activity_logs.action) as actions,activity_logs.ip,activity_logs.user_agent,activity_logs.using_web"
                . ",activity_logs.created_at,TIME(activity_logs.created_at) as time_value", false)
                ->unset_column('primary_id')
                ->from('activity_logs')->join("schools", "schools.id=activity_logs.school_id", 'LEFT')
                ->join("users", "users.id=activity_logs.user_id", 'LEFT')
                ->where("free_site",0)
                ->where("ip !=",'182.160.115.228');
        

        echo $this->datatables->generate();
        $this->db->dbprefix = 'tds_';
    }
    function new_stat()
    {
        $school_id = $this->input->post("school");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $this->finishSession($school_id);
        $data['stat'] = $this->getinfo($school_id,$start_date,$end_date);
        $data['user_type'] = array(1 => 'Student', 2 => 'Parent', 3 => 'Teacher', 4=> 'Admin');
        $this->load->view("admin/paidstatictis/_partialstat",$data);
    }
     function new_stat_feature()
    {
        $first_school = $this->input->post("school");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        
        $hdetect = array("assignments","dashboards employee_homework_data",'dashboards homework_data');
        $attendence = array('attendances','student_attendance','attendance_reports');
        $syllabus = array('syllabus');
        $exam_reports = array('exams','reports index','dashboards exam_result_data_student','exam_reports','student class_test_report','student term_test_report','dashboards employee_exam_routine_data','dashboards exam_routine_data_student','exam student_exam_schedule');
        $class_routines = array('dashboards routine_data','dashboards class_routine_data_student','timetable student_view','class_timings','class_timing_sets');
        $leave = array('employee_attendance');
        $quize = array('online_student_exam');
        $lesson_plan = array('lesson_plan');
        $events = array('calendar');
        $notice = array('notice','reminder');
        $mettings = array('meetings');
        
        $data['stat_homework'] = $this->getinfo_feature($first_school,$start_date,$end_date,$hdetect);
        $data['stat_attendence'] = $this->getinfo_feature($first_school,$start_date,$end_date,$attendence);
        $data['stat_syllabus'] = $this->getinfo_feature($first_school,$start_date,$end_date,$syllabus);
        
        $data['stat_exams'] = $this->getinfo_feature($first_school,$start_date,$end_date,$exam_reports);
        $data['stat_class_routines'] = $this->getinfo_feature($first_school,$start_date,$end_date,$class_routines);
        
        $data['stat_leave'] = $this->getinfo_feature($first_school,$start_date,$end_date,$leave);
        $data['stat_quize'] = $this->getinfo_feature($first_school,$start_date,$end_date,$quize);
        $data['stat_lesson_plan'] = $this->getinfo_feature($first_school,$start_date,$end_date,$lesson_plan);
        $data['stat_events'] = $this->getinfo_feature($first_school,$start_date,$end_date,$events);
        $data['stat_notice'] = $this->getinfo_feature($first_school,$start_date,$end_date,$notice);
        $data['stat_mettings'] = $this->getinfo_feature($first_school,$start_date,$end_date,$mettings);
        
        $data['user_type'] = array(1 => 'Student', 2 => 'Parent', 3 => 'Teacher', 4=> 'Admin');
        $this->load->view("admin/paidstatictis/_partialstat_feature",$data);
    }
    function full_stat_feature($school_id,$user_type,$start_date,$end_date,$type)
    {
        $hm = array("assignments","dashboards employee_homework_data",'dashboards homework_data');
        $at = array('attendances','student_attendance','attendance_reports');
        $sy = array('syllabus');
        $ex = array('exams','reports index','dashboards exam_result_data_student','exam_reports','student class_test_report','student term_test_report','dashboards employee_exam_routine_data','dashboards exam_routine_data_student','exam student_exam_schedule');
        $cr = array('dashboards routine_data','dashboards class_routine_data_student','timetable student_view','class_timings','class_timing_sets');
        $le = array('employee_attendance');
        $qu = array('online_student_exam');
        $lp = array('lesson_plan');
        $ev = array('calendar');
        $no = array('notice','reminder');
        $me = array('meetings');
        
        $data['has_daterange_stat'] = true;
        $data['user_type_array'] = array(1 => 'Student', 2 => 'Parent', 3 => 'Teacher', 4=> 'Admin');
        //$user_type = array_search($user_type, $data['user_type_array']);
        $data['stat'] = $this->getinfo_full_feature($user_type,$school_id,$start_date,$end_date,$$type);
        $this->render("admin/paidstatictis/full_stat",$data);
    }
    function full_session_stat($school_id,$user_type,$start_date,$end_date)
    {
        $data['has_daterange_stat'] = true;
        $data['user_type_array'] = array(1 => 'Student', 2 => 'Parent', 3 => 'Teacher', 4=> 'Admin');
       // $user_type = array_search($user_type, $data['user_type_array']);
        $data['stat'] = $this->getinfo_full_session($user_type,$school_id,$start_date,$end_date);
        $this->render("admin/paidstatictis/full_stat_session",$data);
    }
    function full_stat($school_id,$user_type,$start_date,$end_date)
    {
        $data['has_daterange_stat'] = true;
        $data['user_type_array'] = array(1 => 'Student', 2 => 'Parent', 3 => 'Teacher', 4=> 'Admin');
        //$user_type = array_search($user_type, $data['user_type_array']);
        $data['stat'] = $this->getinfo_full($user_type,$school_id,$start_date,$end_date);
        $this->render("admin/paidstatictis/full_stat",$data);
    }
    function overall_feature()
    {
        $data['has_daterange_stat'] = true;
        $this->db->dbprefix = '';
        $this->db->where("is_deleted",0);
        
        $obj_schools = $this->db->get('schools')->result();
        
        $this->db->dbprefix = 'tds_';
        $select_schools = array();
        //$select_schools[0] = "Select School";
        $i = 0;
        foreach ($obj_schools as $value)
        {
            if($i==0)
            {
                $first_school = $value->id;
            }
            $select_schools[$value->id] = $value->name;
            $i++;
        }
        
        $data['schools'] = $select_schools;
        $hdetect = array("assignments","dashboards employee_homework_data",'dashboards homework_data');
        $attendence = array('attendances','student_attendance','attendance_reports');
        $syllabus = array('syllabus');
        $exam_reports = array('exams','reports index','dashboards exam_result_data_student','exam_reports','student class_test_report','student term_test_report','dashboards employee_exam_routine_data','dashboards exam_routine_data_student','exam student_exam_schedule');
        $class_routines = array('dashboards routine_data','dashboards class_routine_data_student','timetable student_view','class_timings','class_timing_sets');
        $leave = array('employee_attendance');
        $quize = array('online_student_exam');
        $lesson_plan = array('lesson_plan');
        $events = array('calendar');
        $notice = array('notice','reminder');
        $mettings = array('meetings');
        
        $data['stat_homework'] = $this->getinfo_feature($first_school,"","",$hdetect);
        $data['stat_attendence'] = $this->getinfo_feature($first_school,"","",$attendence);
        $data['stat_syllabus'] = $this->getinfo_feature($first_school,"","",$syllabus);
        
        $data['stat_exams'] = $this->getinfo_feature($first_school,"","",$exam_reports);
        $data['stat_class_routines'] = $this->getinfo_feature($first_school,"","",$class_routines);
        
        $data['stat_leave'] = $this->getinfo_feature($first_school,"","",$leave);
        $data['stat_quize'] = $this->getinfo_feature($first_school,"","",$quize);
        $data['stat_lesson_plan'] = $this->getinfo_feature($first_school,"","",$lesson_plan);
        $data['stat_events'] = $this->getinfo_feature($first_school,"","",$events);
        $data['stat_notice'] = $this->getinfo_feature($first_school,"","",$notice);
        $data['stat_mettings'] = $this->getinfo_feature($first_school,"","",$mettings);
        
        $data['user_type'] = array(1 => 'Student', 2 => 'Parent', 3 => 'Teacher', 4=> 'Admin');
        $this->render('admin/paidstatictis/overall_feature',$data);
    }
    function overall()
    {
        $data['has_daterange_stat'] = true;
        $this->db->dbprefix = '';
        $this->db->where("is_deleted",0);
        
        $obj_schools = $this->db->get('schools')->result();
        
        $this->db->dbprefix = 'tds_';
        $select_schools = array();
        //$select_schools[0] = "Select School";
        $i = 0;
        foreach ($obj_schools as $value)
        {
            if($i==0)
            {
                $first_school = $value->id;
            }
            $select_schools[$value->id] = $value->name;
            $i++;
        }
        
        $data['schools'] = $select_schools;
        
        $this->finishSession($first_school);
        $data['stat'] = $this->getinfo($first_school);
        $data['user_type'] = array(1 => 'Student', 2 => 'Parent', 3 => 'Teacher', 4=> 'Admin');
        $this->render('admin/paidstatictis/overall',$data);
    }
    private function finishSession($school_id, $start_date="", $end_date="")
    {
        if(!$start_date)
        {
            if($end_date)
            {
                $start_date = $end_date;
            }
            else 
            {
                $start_date = date("Y-m-d");
            }
            
        }
        if(!$end_date)
        {
            $end_date = $start_date;
        }
        
        $time_fifteen = date('Y-m-d H:i:s', strtotime('-15 minutes'));
        $this->db->dbprefix = '';
        $sql  ="SELECT act.*
        FROM activity_logs act
        where id in
            (SELECT MAX(id) as main_id
            FROM activity_logs where free_site=0 and ip != '182.160.115.228'  and school_id=".$school_id." 
            GROUP BY user_id) 
        and act.session_end!=1 and act.created_at < '".$time_fifteen."'";
        
        $query = $this->db->query($sql);
        $statistics_info = $query->result(); 
        
        foreach($statistics_info as $value)
        {
            $sql_2 = "SELECT MIN(created_at) as mcreated from activity_logs where id > "
                    . "(select MAX(id) as main_id from activity_logs where session_end=1 "
                    . "and free_site=0 and ip != '182.160.115.228'  and user_id=".$value->user_id."  and school_id=".$school_id.") and "
                    . "free_site=0 and ip != '182.160.115.228'  and user_id=".$value->user_id."  and school_id=".$school_id." and session_end!=1";
            $query2 = $this->db->query($sql_2);
            $statistics_info2 = $query2->row(); 
            
            if($statistics_info2)
            {
                $now = strtotime($value->created_at);
                $from_time = strtotime($statistics_info2->mcreated);
                $session_time = $now - $from_time;
                $data = array(
                       'session_end' => 1,
                       'session_time' => $session_time
                );
                $this->db->where('id', $value->id);
                $this->db->update('activity_logs', $data); 
            }
               
        }    
        $this->db->dbprefix = 'tds_';
    }  
    
    private function getinfo_full_session($user_type_paid=0,$school_id, $start_date="", $end_date="")
    {
        if(!$start_date)
        {
            if($end_date)
            {
                $start_date = $end_date;
            }
            else 
            {
                $start_date = date("Y-m-d");
            }
            
        }
        if(!$end_date)
        {
            $end_date = $start_date;
        } 
        
        $this->db->dbprefix = '';
        $this->db->select("schools.name,activity_logs.user_id,CONCAT_WS(' ',users.first_name,users.last_name) as username"
                . ",activity_logs.user_type_paid,,SUM(activity_logs.session_time) as session_time,SUM(activity_logs.session_end) as snumber", false);
        $this->db->from("activity_logs");
        $this->db->join("users", "users.id=activity_logs.user_id", 'LEFT');
        $this->db->join("schools", "schools.id=activity_logs.school_id", 'LEFT');
        $this->db->where("activity_logs.free_site",0);
        $this->db->where("activity_logs.session_end",1);
        $this->db->where("activity_logs.ip !=",'182.160.115.228');
        $this->db->where("activity_logs.ip !=",'182.160.115.228');
        if($school_id>0)
        {
            $this->db->where("activity_logs.school_id",$school_id);
        }
        if($user_type_paid>0)
        {
            $this->db->where("activity_logs.user_type_paid",$user_type_paid);
        }
        $this->db->where("DATE(activity_logs.created_at) <=",$end_date);
        $this->db->where("DATE(activity_logs.created_at) >=",$start_date);
        $this->db->group_by("activity_logs.user_id"); 
         
        $statistics_info = $this->db->get()->result(); 
        $this->db->dbprefix = 'tds_';
        return $statistics_info;
    }
    
    private function getinfo_full_feature($user_type_paid=0,$school_id, $start_date="", $end_date="",$actions = array())
    {
        if(!$start_date)
        {
            if($end_date)
            {
                $start_date = $end_date;
            }
            else 
            {
                $start_date = date("Y-m-d");
            }
            
        }
        if(!$end_date)
        {
            $end_date = $start_date;
        } 
        
        $this->db->dbprefix = '';
        $this->db->select("schools.name,activity_logs.user_id,CONCAT_WS(' ',users.first_name,users.last_name) as username"
                . ",activity_logs.user_type_paid", false);
        $this->db->from("activity_logs");
        $this->db->join("users", "users.id=activity_logs.user_id", 'LEFT');
        $this->db->join("schools", "schools.id=activity_logs.school_id", 'LEFT');
        $this->db->where("activity_logs.free_site",0);
        $this->db->where("activity_logs.ip !=",'182.160.115.228');
        if($school_id>0)
        {
            $this->db->where("activity_logs.school_id",$school_id);
        }
        if($user_type_paid>0)
        {
            $this->db->where("activity_logs.user_type_paid",$user_type_paid);
        }
        $this->db->where("DATE(activity_logs.created_at) <=",$end_date);
        $this->db->where("DATE(activity_logs.created_at) >=",$start_date);
        if($actions)
        {
            $i = 1;
            $total_a = count($actions);
            $w_string = "";
            foreach($actions as $value)
            {
                $mc = explode(" ", $value);
                
                if($i == 1)
                {
                   $w_string.= "( "; 
                }     
                
                $w_string.= "( ";
                
                $w_string.=" activity_logs.controller='".$mc[0]."'";
                if(isset($mc[1]))
                {
                   $w_string.=" AND activity_logs.action='".$mc[1]."'"; 
                }
                
                $w_string.= " )";
                if($total_a != $i)
                {
                    $w_string.= " OR "; 
                }
                
                if($total_a == $i)
                {
                    $w_string.= " ) "; 
                }
                $i++;
                
            } 
            if($w_string)
            {
                $this->db->where($w_string);
            }
        }
        
        $this->db->group_by("activity_logs.user_id"); 
        $statistics_info = $this->db->get()->result(); 
        $this->db->dbprefix = 'tds_';
        return $statistics_info;
    }
   
    private function getinfo_full($user_type_paid=0,$school_id, $start_date="", $end_date="")
    {
        if(!$start_date)
        {
            if($end_date)
            {
                $start_date = $end_date;
            }
            else 
            {
                $start_date = date("Y-m-d");
            }
            
        }
        if(!$end_date)
        {
            $end_date = $start_date;
        } 
        
        $this->db->dbprefix = '';
        $this->db->select("schools.name,activity_logs.user_id,CONCAT_WS(' ',users.first_name,users.last_name) as username"
                . ",activity_logs.user_type_paid", false);
        $this->db->from("activity_logs");
        $this->db->join("users", "users.id=activity_logs.user_id", 'LEFT');
        $this->db->join("schools", "schools.id=activity_logs.school_id", 'LEFT');
        $this->db->where("activity_logs.free_site",0);
        $this->db->where("activity_logs.ip !=",'182.160.115.228');
        if($school_id>0)
        {
            $this->db->where("activity_logs.school_id",$school_id);
        }
        if($user_type_paid>0)
        {
            $this->db->where("activity_logs.user_type_paid",$user_type_paid);
        }
        $this->db->where("DATE(activity_logs.created_at) <=",$end_date);
        $this->db->where("DATE(activity_logs.created_at) >=",$start_date);
        
        
        
        $this->db->group_by("activity_logs.user_id"); 
        $statistics_info = $this->db->get()->result(); 
        $this->db->dbprefix = 'tds_';
        return $statistics_info;
    }
    
    private function getinfo_feature($school_id=0, $start_date="", $end_date="",$actions=array())
    {
        if(!$start_date)
        {
            if($end_date)
            {
                $start_date = $end_date;
            }
            else 
            {
                $start_date = date("Y-m-d");
            }
            
        }
        if(!$end_date)
        {
            $end_date = $start_date;
        } 
        
        $this->db->dbprefix = '';
        $this->db->select("Count(Distinct user_id) As countUsers,user_type_paid");
        $this->db->where("free_site",0);
        $this->db->where("ip !=",'182.160.115.228');
        if($school_id>0)
        {
            $this->db->where("school_id",$school_id);
        }
        if($actions)
        {
            $i = 1;
            $total_a = count($actions);
            $w_string = "";
            foreach($actions as $value)
            {
                $mc = explode(" ", $value);
                
                if($i == 1)
                {
                   $w_string.= "( "; 
                }     
                
                $w_string.= "( ";
                
                $w_string.=" controller='".$mc[0]."'";
                if(isset($mc[1]))
                {
                   $w_string.=" AND action='".$mc[1]."'"; 
                }
                
                $w_string.= " )";
                if($total_a != $i)
                {
                    $w_string.= " OR "; 
                }
                
                if($total_a == $i)
                {
                    $w_string.= " ) "; 
                }
                $i++;
                
            } 
            if($w_string)
            {
                $this->db->where($w_string);
            }
        }    
        $this->db->where("DATE(created_at) <=",$end_date);
        $this->db->where("DATE(created_at) >=",$start_date);
        $this->db->group_by("user_type_paid"); 
        
        $statistics_info = $this->db->get("activity_logs")->result(); 
        $this->db->dbprefix = 'tds_';
        return $statistics_info;
    } 
    
    private function getinfo($school_id=0, $start_date="", $end_date="")
    {
        if(!$start_date)
        {
            if($end_date)
            {
                $start_date = $end_date;
            }
            else 
            {
                $start_date = date("Y-m-d");
            }
            
        }
        if(!$end_date)
        {
            $end_date = $start_date;
        } 
        
        $this->db->dbprefix = '';
        $this->db->select("Count(Distinct user_id) As countUsers,SUM(session_time) as stime,SUM(session_end) as snumber,user_type_paid");
        $this->db->where("free_site",0);
        $this->db->where("ip !=",'182.160.115.228');
        if($school_id>0)
        {
            $this->db->where("school_id",$school_id);
        }
         
        $this->db->where("DATE(created_at) <=",$end_date);
        $this->db->where("DATE(created_at) >=",$start_date);
        $this->db->group_by("user_type_paid"); 
        $statistics_info = $this->db->get("activity_logs")->result(); 
        $this->db->dbprefix = 'tds_';
        return $statistics_info;
    }        
   

    

}

?>
