<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class topicandquestion extends MX_Controller {

    public function __construct() {
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
    function index() {

        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">');
        $this->table->set_template($tmpl);
        
        $obj_cccategory = new Sccategory();
        $obj_cccategory->where("science_rocks_category.status", "1");
        $obj_cccategory->order_by('name');
        $obj_cccategory->get();


        $select_category[NULL] = "Select";
        foreach ($obj_cccategory as $value)
        {
            $select_category[$value->name] = $value->name;
        }
        
        $data['category'] = $select_category;

        $this->table->set_heading('Name','Category', 'Mark', 'Time', 'Played', 'Status', 'Action');
        $this->render('admin/topicandquestion/index',$data);
    }

    /**
     * Datable function
     * @param none
     * @defination use for showing datatable for byline callback function
     * @author Fahim
     */
    function datatable() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        
        $this->datatables->set_buttons("delete");
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("change_status","ajax");
        $this->datatables->set_buttons("question","category_model");
        $this->datatables->set_controller_name("topicandquestion");
        $this->datatables->set_primary_key("id");
        
        $this->datatables->set_custom_string(6, array(1 => 'Active', 0 => 'Inactive'));
        
        $this->datatables->select('science_rocks_topics.id, science_rocks_topics.name,pre_cat.name as cname, mark, time, total_played, science_rocks_topics.status')
                ->unset_column('science_rocks_topics.id')
                ->from('science_rocks_topics')
                ->join("science_rocks_category as pre_cat", "pre_cat.id=science_rocks_topics.category_id", 'LEFT');

        echo $this->datatables->generate();
    }

    function add() {
       
        
        $obj_topic = new Sctopic();
        
        if ($_POST) {
            foreach ($this->input->post() as $key => $value) {
                $obj_topic->$key = $value;
            }
        }
        $obj_catgory = $this->db->get_where('science_rocks_category', array('status' => 1))->result();
        
        $select_category = array();
        foreach ($obj_catgory as $value)
        {
            $select_category[$value->id] = $value->name;
        }
        
        $data['category'] = $select_category;
        
        $data['model'] = $obj_topic;
        if (!$obj_topic->save()) {
            $this->render('admin/topicandquestion/insert', $data);
        } else {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * edit function
     * @param none
     * @defination use for Update Byline
     * @author Fahim
     */
    function edit($id) {
        
    
        $obj_topic = new Sctopic($id);
        if ($_POST) {
            foreach ($this->input->post() as $key => $value) {
                $obj_topic->$key = $value;
            }
        }
        
        $obj_catgory = $this->db->get_where('science_rocks_category', array('status' => 1))->result();
        
        $select_category = array();
        foreach ($obj_catgory as $value)
        {
            $select_category[$value->id] = $value->name;
        }
        
        $data['category'] = $select_category;
        

        $data['model'] = $obj_topic;
        if (!$obj_topic->save() || !$_POST) {
            $this->render('admin/topicandquestion/insert', $data);
        } else {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }
    function delete()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_sctopic = new Sctopic($this->input->post('primary_id'));
        $obj_sctopic->delete();
        echo 1;
    }
    
    function change_status()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_sctopic = new Sctopic($this->input->post('primary_id'));
       
        if($obj_sctopic->status)
        {
            $status = 0;
        }    
        else
        {
            $status = 1;
        }    
        
        $data  = array('status' =>$status);
        $where = "id = ".$this->input->post('primary_id');
        $str   = $this->db->update_string('tds_science_rocks_topics', $data, $where);
        $this->db->query($str);
        echo 1;
    }

    

    public function question($topic_id) {
        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="question_table_science">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Question', 'Mark', 'Time', 'Action');

        $data['topic_id'] = $topic_id;
        
        $obj_topic = new Sctopic($topic_id);
        $data['topic'] = $obj_topic;

        $this->render('admin/topicandquestion/question', $data);
    }
    

    public function datatable_question($topic_id) {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        
        
        $this->datatables->set_buttons("edit_question");
        $this->datatables->set_buttons("delete_question", 'ajax');

        $this->datatables->set_controller_name("topicandquestion");
        $this->datatables->set_primary_key("primary_id");
        
        $this->datatables->select('science_rocks_question.id as primary_id, science_rocks_question.question, science_rocks_question.mark, science_rocks_question.time')
                ->unset_column('primary_id')
                ->from('science_rocks_question')
                ->where('science_rocks_question.topic_id', $topic_id);

        echo $this->datatables->generate();
    }
    
    function delete_question()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_question = new Assessment_questions_science($this->input->post('primary_id'));
        $obj_question->delete();
        echo 1;
    }

    function add_question($topic_id) 
    {
       
        $obj_topic = new Sctopic($topic_id);
        
        
        $obj_assesment_que = new Assessment_questions_science();
        
        
        $obj_assesment_que->mark = $obj_topic->mark;
        $obj_assesment_que->time = $obj_topic->time;
        $topic_id = $topic_id;
        $saved = false;

        $data['custom_error'] = '';
        
        $data['topic'] = $obj_topic;

        if ($_POST) {
            $loop_limit = count($_POST['answer']);

            for ($i = 0; $i <= $loop_limit; $i++) {

                if (empty($_POST['answer'][$i])) {
                    unset($_POST['answer'][$i]);
                    unset($_POST['en_answer'][$i]);
                }
            }

            $answers = $this->input->post('answer');
            $en_answers = $this->input->post('en_answer');
            $loop_limit = count($answers);

            if (($loop_limit != 2) && ($loop_limit != 4)) {
                $data['custom_error'] = 'Invalid number of answers. There should be two or four answers.';
            }

            if (empty($data['custom_error'])) {
               
                $obj_assesment_que->topic_id = $topic_id;
                $obj_assesment_que->question = $this->input->post('question');
                $obj_assesment_que->explanation = $this->input->post('explanation');
                $obj_assesment_que->en_question = $this->input->post('en_question');
                $obj_assesment_que->en_explanation = $this->input->post('en_explanation');
                $obj_assesment_que->mark = $this->input->post('mark');
                $obj_assesment_que->time = $this->input->post('time');

                if ($obj_assesment_que->save()) {
                    
                   
                    for ($i = 0; $i <= $loop_limit; $i++) {
                        $obj_assesment_ans = new Assessment_options_science();
                        $obj_assesment_ans->question_id = $obj_assesment_que->id;
                        $obj_assesment_ans->answer = $answers[$i];
                        $obj_assesment_ans->en_answer = $en_answers[$i];
                        
                        $correct = $this->input->post('correct');
                        $obj_assesment_ans->correct = ($i == $correct[0]) ? 1 : 0;

                        if ($obj_assesment_ans->save()) {
                            $saved = true;
                        }
                    }
                }
            }
        }

        $data['question'] = $obj_assesment_que;
        $data['answers'] = $obj_assesment_ans;  
        $data['edit'] = false;

        if (!$saved) {
            $this->render('admin/topicandquestion/_question_form', $data);
        } else {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    function edit_question($question_id) {
 
        
        $obj_assesment_que = new Assessment_questions_science($question_id);
        $topic_id = $obj_assesment_que->topic_id;
        $saved = false;
        $obj_topic = new Sctopic($topic_id);
        
        $data['topic'] = $obj_topic;

        $data['custom_error'] = '';

        if ($_POST) {
            $loop_limit = count($_POST['answer']);

            for ($i = 0; $i <= $loop_limit; $i++) {

                if (empty($_POST['answer'][$i])) {
                    unset($_POST['answer'][$i]);
                    unset($_POST['en_answer'][$i]);
                }
            }
            
            $answers = $this->input->post('answer');
            $en_answers = $this->input->post('en_answer');
            
            $loop_limit = count($answers);

            if (($loop_limit != 2) && ($loop_limit != 4)) {
                $data['custom_error'] = 'Invalid number of answers. There should be two or four answers.';
            }
            
            if (empty($data['custom_error'])) {

                $obj_assesment_que->topic_id = $topic_id;
                $obj_assesment_que->question = $this->input->post('question');
                $obj_assesment_que->explanation = $this->input->post('explanation');
                $obj_assesment_que->en_question = $this->input->post('en_question');
                $obj_assesment_que->en_explanation = $this->input->post('en_explanation');
                $obj_assesment_que->mark = $this->input->post('mark');
                $obj_assesment_que->time = $this->input->post('time');
                
                if ($obj_assesment_que->save()) {
                    
                    $obj_assesment_ans = new Assessment_options_science();
                    $del_answers = $obj_assesment_ans->del_assessment_option_by_q_id($question_id);
                    
                    if ($del_answers) {
                        $i = 0;
                        foreach ($answers as $answer) {

                            $obj_assesment_ans = new Assessment_options_science();

                            $obj_assesment_ans->question_id = $obj_assesment_que->id;
                            $obj_assesment_ans->answer = $answers[$i];
                            $obj_assesment_ans->en_answer = $en_answers[$i];
                            
                            $correct = $this->input->post('correct');
                            $obj_assesment_ans->correct = ($i == $correct[0]) ? 1 : 0;

                            if ($obj_assesment_ans->save()) {
                                $i++;
                            }
                        }
                        
                        if ($i == $loop_limit) {
                            $saved = TRUE;
                        }
                    }
                }
            }
        }
        
        $obj_assesment_ans = new Assessment_options_science();
        $obj_assesment_ans = $obj_assesment_ans->get_assessment_option_by_q_id($question_id);

        $data['question'] = $obj_assesment_que;
        $data['answers'] = $obj_assesment_ans;
        $data['edit'] = true;

        if (!$saved) {
            $this->render('admin/topicandquestion/_question_form', $data);
        } else {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

}

?>
