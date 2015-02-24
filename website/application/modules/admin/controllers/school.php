<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class school extends MX_Controller
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

        $this->table->set_heading('Name','Location', 'District','Medium','Is paid','Action');
        $this->render('admin/school/index');
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
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        
        $this->datatables->set_buttons("feeds", false,true);
        $this->datatables->set_buttons("add_feed", false,true);
        $this->datatables->set_buttons("members");
        $this->datatables->set_buttons("assign_as_paid","model2",false,array("field"=>"is_paid","value"=>0));
        $this->datatables->set_controller_name("school");
        $this->datatables->set_primary_key("id");

        $this->datatables->select('id,name,location,district,medium,is_paid')
                ->unset_column('id')
                ->from('school');

        echo $this->datatables->generate();
    }
    function feeds($id)
    {
        redirect("admin/news/index/".$id);
    }
    
    function add_feed($id)
    {
        redirect("admin/news/add/".$id);
    }
    
    private function getPaidSchool($id)
    {
       $schools = array();
       $this->db->dbprefix = "";
       $this->db->select("name,code,id");
       $this->db->where("is_deleted",0);
       $this->db->where("access_locked",0);
       $pschools = $this->db->get("schools")->result();
       
       
       $this->db->dbprefix = "tds_";
       $this->db->select("paid_school_id");
       $this->db->where("is_paid",1);
       $upschools = $this->db->get("school")->result();
       
       $apschools = array();
       
       if($upschools && count($upschools)>0)
       foreach($upschools as $value)
       {
           $apschools[] = $value->paid_school_id;
       }
       
       if($pschools && count($pschools)>0)
       foreach($pschools as $pvalue)
       {
           if(!in_array($pvalue->id, $apschools))
           {
                $schools[$pvalue->id."::".$pvalue->code] = $pvalue->name;
           }
       }
       
       return $schools;
    }
    
    function assign_as_paid($id)
    {
        $obj_school = new Schools($id);
        if($obj_school->is_paid==1)
        {
           echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
           
        } 
        else
        {
            if ($_POST)
            {
               
                if($_POST['school'])
                {
                    $schools_inofrmation = $this->input->post("school");
                    $a_school = explode("::", $schools_inofrmation);
                    $obj_school->is_paid = 1;
                    $obj_school->paid_school_id = $a_school[0];
                    $obj_school->code = $a_school[1];
                    
                }
                
                
            }

            $data['paid_school'] = $this->getPaidSchool($id);
            $data['model'] = $obj_school;
            if (!$obj_school->save() || !$_POST)
            {
                $this->render('admin/school/assign_as_paid', $data);
            }
            else
            {
                echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
            }
        }
    }

    /**
     * add function
     * @param none
     * @defination use for insert byline
     * @author Fahim
     */
    function add()
    {
        $obj_school = new Schools();
        $edit = FALSE;
        
        if ($_POST)
        {
            $user_created_school_id =  $this->input->post('user_created_school_id');
            
            unset($_POST['user_created_school_id']);
            
            foreach ($this->input->post() as $key => $value)
            {
                if($value)
                $obj_school->$key = $value;
            }
        }
        
        $data['model'] = $obj_school;
        $data['user_schools'] = array(NULL => 'Select School');
        
        $obj_user_school = new userschools();
        $obj_user_school = $obj_user_school->get_user_submitted_school();
        
        foreach ($obj_user_school as $row) {
            $data['user_schools'][$row->id] = $row->school_name;
        }
        
        if (!$obj_school->save())
        {
            $this->render('admin/school/insert', $data);
        }
        else
        {
            $obj_user_school = new userschools();
            $obj_user_school = $obj_user_school->get_user_submitted_school($user_created_school_id);
            
            if($obj_user_school !== FALSE) {
                $obj_free_user = new Free_users($obj_user_school[0]->freeuser_id);
            
                $user_school = new User_school();
                $user_school->user_id = $obj_user_school[0]->freeuser_id;
                $user_school->school_id = $obj_school->id;
                $user_school->approved_date = date('Y-m-d');
                $user_school->approved_by = 'admin';
                $user_school->is_approved = '0';
                $user_school->grade = $obj_free_user->grade_ids;
                $user_school->type = $obj_free_user->user_type;
                $user_school->save();

                $temp_user_school_score = new Assessment_school_mark_temp();
                $temp_user_school_score = $temp_user_school_score->find_assessment_school_mark_all(0, 0, $user_created_school_id);
                
                if($temp_user_school_score !== FALSE) {
                    $assessment_school_mark_temp = array();
                    foreach($temp_user_school_score as $am) {

                        $assessment_school_mark_temp_data['user_id'] = $am->user_id;
                        $assessment_school_mark_temp_data['assessment_id'] = $am->assessment_id;
                        $assessment_school_mark_temp_data['mark'] = $am->mark;
                        $assessment_school_mark_temp_data['level'] = $am->level;
                        $assessment_school_mark_temp_data['school_id'] = $obj_school->id;
                        $assessment_school_mark_temp_data['created_date'] = $am->created_date;
                        $assessment_school_mark_temp_data['time_taken'] = $am->time_taken;
                        $assessment_school_mark_temp_data['avg_time_per_ques'] = $am->avg_time_per_ques;
                        $assessment_school_mark_temp_data['no_played'] = $am->no_played;

                        $assessment_school_mark_temp[] = $assessment_school_mark_temp_data;
                    }

                    $this->db->insert_batch('assessment_school_mark', $assessment_school_mark_temp);

                    $this->db->where('temp_school_id', $user_created_school_id);
                    $this->db->delete('assessment_school_mark_temp');
                }
            }
            
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    
    function get_user_school_data() {
        
        $response = array();
        
        $school_id = $this->input->post('school_id');
        
        if(!$this->input->is_ajax_request() || empty($school_id)){
            $response['error'] = 'Bad Request';
            echo json_encode($response);
            exit;
        }
        
        $obj_user_school = new userschools();
        $obj_user_school = $obj_user_school->get_user_submitted_school($school_id);
        
        if($obj_user_school !== FALSE) {
            
            foreach ($obj_user_school[0] as $k => $v) {
                $response[$k] = $v;
            }
        }
        
        echo json_encode($response);
        exit;
        
    }

    /**
     * edit function
     * @param none
     * @defination use for Update Byline
     * @author Fahim
     */
    function edit($id)
    {
        $obj_school = new Schools($id);
        $edit = TRUE;
        
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                if($value)
                $obj_school->$key = $value;
            }
        }

        $data['model'] = $obj_school;
        if (!$obj_school->save() || !$_POST)
        {
            $this->render('admin/school/insert', $data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * delete function
     * @param none
     * @defination use for delete a byline
     * @author Fahim
     */
    function delete()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_school = new Schools($this->input->post('primary_id'));
        $obj_school->delete();
        echo 1;
    }
    
    
    public function sort_schools()
    {
        $obj_school = new Schools();
        $obj_school->order_by('priority');
        $obj_school->where("is_columnist", "1");
        $data['schools'] = $obj_school->get();
        
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_val'] = $this->security->get_csrf_hash();
        $this->render('admin/school/sort', $data);
    }
    
    public function save_priorities()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $ar_priority_sets = array();
       
        
        $s_school_data = $this->input->post("category_ids");
        
        $obj_school = new Schools();
        $ar_schools = explode(",", $s_school_data);
        $i = 1;
        foreach( $ar_schools as $school_id )
        {
            if ( stripos($school_id, "_") === FALSE )
            {
                $obj_school->where('id', $school_id);
                $obj_school->update("priority", $i);
                $i++;
            }
            
        }
    }
    
    public function members($school_id)
    {
        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="members_table">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Full Name', 'Photo', 'Type', 'Class', 'Status', 'Action');
        
        $this->load->config('user_register');
        
        $ar_member_type = array(NULL => 'Select');
        $ar_config_member_type = $this->config->config['join_user_types'];
        
        $data['school_id'] = $school_id;
        $data['member_type'] = array_merge($ar_member_type, $ar_config_member_type);
        $data['member_status'] = array( NULL => 'Select', '0' => 'Pending', '1' => 'Approved', '2' => 'Denied');
        
        $this->render('admin/school/members', $data);
    }
    
    public function datatable_members($school_id)
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $this->load->config('user_register');
        
        $this->datatables->set_buttons("approve", 'ajax');
        $this->datatables->set_buttons("deny", 'ajax');
        
        $this->datatables->set_controller_name("school");
        $this->datatables->set_primary_key("primary_id");
        $this->datatables->set_image_field_position(2);
        
        $this->datatables->set_custom_string(3, $this->config->config['join_user_types']);
        $this->datatables->set_custom_string(5, array(0 => 'Pending', 1 => 'Approved', 2 => 'Denied'));

        $this->datatables->select('user_school.id as primary_id, CONCAT_WS(" ", free_users.first_name, free_users.middle_name, free_users.last_name) AS full_name, free_users.profile_image, user_school.type, user_school.grade, user_school.is_approved', FALSE)
                ->unset_column('primary_id')
                ->from('user_school')
                ->join('free_users', 'free_users.id = user_school.user_id', 'INNER')
                ->where('user_school.school_id', $school_id);
         
        echo $this->datatables->generate();
    }
    
    public function approve(){
        
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $id = $this->input->post('primary_id');
        
        $user_school = new User_school($id);
        $user_school->approved_date = date('Y-m-d');
        $user_school->approved_by = 'admin';
        $user_school->is_approved = '1';
        
        if (!$user_school->save() || !$_POST)
        {
            $this->render('admin/school/members');
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    
    public function deny($id){
        
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        
        $id = $this->input->post('primary_id');
        
        $user_school = new User_school($id);
        
        /*$user_school->deny_date = date('Y-m-d');
        $user_school->deny_by = 'admin';
        $user_school->is_approved = '2';
        */
        
        if (!$user_school->delete() || !$_POST)
        {
            $this->render('admin/school/members');
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    
}

?>
