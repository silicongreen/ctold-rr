<?php

/*
 * bylines Controller
 * Admin Byline management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class assessment extends MX_Controller {

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

        $this->table->set_heading('Title', 'Type', 'Time', 'Played', 'Topic', 'Created Date', 'Action');
        $this->render('admin/assessment/index');
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
        
        $this->load->config('huffas');
        
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_buttons("question");
        $this->datatables->set_buttons("get_link");
        $this->datatables->set_controller_name("assessment");
        $this->datatables->set_primary_key("id");
        
        $this->datatables->set_custom_string(2, $this->config->config['assessment']['types']);
        
        $this->datatables->select('id, title, type, time, played, topic, created_date')
                ->unset_column('id')
                ->from('assessment');

        echo $this->datatables->generate();
    }

    function add() {
        $this->load->config('huffas');
        
        $obj_assesment = new Assessments();
        $data['assessment_types'] = $this->config->config['assessment']['types'];
        
        if ($_POST) {
            foreach ($this->input->post() as $key => $value) {
                $obj_assesment->$key = $value;
            }
        }

        $data['model'] = $obj_assesment;
        if (!$obj_assesment->save()) {
            $this->render('admin/assessment/insert', $data);
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
        
        $this->load->config('huffas');
        
        $data['assessment_types'] = $this->config->config['assessment']['types'];
        
        $obj_assesment = new Assessments($id);
        if ($_POST) {
            foreach ($this->input->post() as $key => $value) {
                $obj_assesment->$key = $value;
            }
        }

        $data['model'] = $obj_assesment;
        if (!$obj_assesment->save() || !$_POST) {
            $this->render('admin/assessment/insert', $data);
        } else {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * delete function
     * @param none
     * @defination use for delete a byline
     * @author Fahim
     */
    function delete() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        $obj_assessments = new Assessments($this->input->post('primary_id'));
        $obj_assessments->delete();
        echo 1;
    }

    public function question($assessment_id) {
        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="question_table">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Question', 'Mark', 'Level', 'Style', 'Action');

        $data['assessment_id'] = $assessment_id;
        $data['style'] = array(NULL => 'Select', '1' => 'Boxed', '2' => 'List');

        $this->render('admin/assessment/question', $data);
    }
    
    public function get_link($assessment_id) {
        //set table id in table open tag
        
        $this->load->config('huffas');
        $assess_config = $this->config->config['assessment'];
        $assessment = new Assessments($assessment_id);
        
        $str_level = '';
        if($assessment->type == 2) {
            $str_level = '/1';
        }
        
        $url_prefix = $assess_config['url_prefix'][$assessment->type];
        
        $data['assess_url'] = base_url($url_prefix . sanitize($assessment->title) . '-' . $assessment->type . '-' . $assessment->id) . $str_level;
        
        $this->render('admin/assessment/_assessment_link', $data);
    }

    public function datatable_question($assessment_id) {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
        
        $this->load->config('huffas');
        
        $this->datatables->set_buttons("edit_question");
        $this->datatables->set_buttons("delete_question", 'ajax');

        $this->datatables->set_controller_name("assessment");
        $this->datatables->set_primary_key("primary_id");

        $this->datatables->set_custom_string(3, $this->config->config['assessment']['levels']);
        $this->datatables->set_custom_string(4, array(NULL => 'Select', 1 => 'Boxed', 2 => 'List'));
        
        $this->datatables->select('assessment_question.id as primary_id, assessment_question.question, assessment_question.mark, assessment_question.level, assessment_question.style')
                ->unset_column('primary_id')
                ->from('assessment_question')
                ->where('assessment_question.assesment_id', $assessment_id);

        echo $this->datatables->generate();
    }

    function add_question($assessment_id) {
        
        $this->load->config('huffas');
        
        $obj_assesment_que = new Assessment_questions();
        $obj_assesment_ans = new Assessment_options();
        $assessment_id = $assessment_id;
        $saved = false;

        $data['custom_error'] = '';

        if ($_POST) {
            $loop_limit = count($_POST['answer']);

            for ($i = 0; $i <= $loop_limit; $i++) {

                if (empty($_POST['answer'][$i])) {
                    unset($_POST['answer'][$i]);
                }
            }

            $answers = $this->input->post('answer');
            $loop_limit = count($answers);

            if (($loop_limit != 2) && ($loop_limit != 4)) {
                $data['custom_error'] = 'Invalid number of answers. There should be two or four answers.';
            }

            if (empty($data['custom_error'])) {

                $obj_assesment_que->assesment_id = $assessment_id;
                $obj_assesment_que->question = $this->input->post('question');
                $obj_assesment_que->explanation = $this->input->post('explanation');
                $obj_assesment_que->level = $this->input->post('level');
                $obj_assesment_que->mark = $this->input->post('mark');
                $obj_assesment_que->style = $this->input->post('style');
                $obj_assesment_que->time = $this->input->post('time');

                if ($obj_assesment_que->save()) {

                    for ($i = 0; $i <= $loop_limit; $i++) {

                        $obj_assesment_ans = new Assessment_options();

                        $obj_assesment_ans->question_id = $obj_assesment_que->id;
                        $obj_assesment_ans->answer = $answers[$i];
                        
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
        $data['level'] = $this->config->config['assessment']['levels'];
        $data['edit'] = false;

        $data['style'] = array(NULL => 'Select', '1' => 'Boxed', '2' => 'List');
        $data['ans_type'] = array(NULL => 'Select', '0' => 'True-Flase', '1' => 'MCQ');

        if (!$saved) {
            $this->render('admin/assessment/_question_form', $data);
        } else {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    function edit_question($question_id) {
        
        $this->load->config('huffas');
        
        $obj_assesment_que = new Assessment_questions($question_id);
        $assessment_id = $obj_assesment_que->assesment_id;
        $saved = false;

        $data['custom_error'] = '';

        if ($_POST) {
            $loop_limit = count($_POST['answer']);

            for ($i = 0; $i <= $loop_limit; $i++) {

                if (empty($_POST['answer'][$i])) {
                    unset($_POST['answer'][$i]);
                }
            }
            
            $answers = $this->input->post('answer');
            
            $loop_limit = count($answers);

            if (($loop_limit != 2) && ($loop_limit != 4)) {
                $data['custom_error'] = 'Invalid number of answers. There should be two or four answers.';
            }
            
            if (empty($data['custom_error'])) {

                $obj_assesment_que->assesment_id = $assessment_id;
                $obj_assesment_que->question = $this->input->post('question');
                $obj_assesment_que->explanation = $this->input->post('explanation');
                $obj_assesment_que->level = $this->input->post('level');
                $obj_assesment_que->mark = $this->input->post('mark');
                $obj_assesment_que->style = $this->input->post('style');
                $obj_assesment_que->time = $this->input->post('time');
                
                if ($obj_assesment_que->save()) {
                    
                    $obj_assesment_ans = new Assessment_options();
                    $del_answers = $obj_assesment_ans->del_assessment_option_by_q_id($question_id);
                    
                    if ($del_answers) {
                        $i = 0;
                        foreach ($answers as $answer) {

                            $obj_assesment_ans = new Assessment_options();

                            $obj_assesment_ans->question_id = $obj_assesment_que->id;
                            $obj_assesment_ans->answer = $answers[$i];
                            
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
        
        $obj_assesment_ans = new Assessment_options();
        $obj_assesment_ans = $obj_assesment_ans->get_assessment_option_by_q_id($question_id);

        $data['question'] = $obj_assesment_que;
        $data['answers'] = $obj_assesment_ans;
        $data['level'] = $this->config->config['assessment']['levels'];
        $data['edit'] = true;

        $data['style'] = array(NULL => 'Select', '1' => 'Boxed', '2' => 'List');
        $data['ans_type'] = array(NULL => 'Select', '0' => 'True-Flase', '1' => 'MCQ');

        if (!$saved) {
            $this->render('admin/assessment/_question_form', $data);
        } else {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

}

?>
