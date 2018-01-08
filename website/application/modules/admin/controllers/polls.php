<?php

/*
 * Polls Controller
 * Admin Poll Management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class polls extends MX_Controller
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
     * @defination use for showing table header and setting table id for admin Controller
     * @author Fahim
     */
    function index()
    {

        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Question', 'Options', 'Created','Action');
        
        $this->render('admin/polls/index', $data);
    }

    /**
     * Datable function
     * @param none
     * @defination use for showing datatable for Controller callback function
     * @author Fahim
     */
    function datatable()
    {
        if (!$this->input->is_ajax_request()) {
                exit('No direct script access allowed');
        }
        $this->datatables->set_buttons("edit");
        $this->datatables->set_buttons("delete");
        $this->datatables->set_buttons("result","model2");
        $this->datatables->set_controller_name("polls");
        $this->datatables->set_primary_key("id");
      
        
        $this->datatables->select('questions.id as id,questions.ques,GROUP_CONCAT(DISTINCT pre_options.value),created')
                ->unset_column('id')
                ->from('questions')->join("options as pre_options", "questions.id=pre_options.ques_id", 'LEFT')
                ->group_by("questions.id");
     

        echo $this->datatables->generate();
    }
    
    function result($id)
    {
        $obj_poll = new Poll($id);
        $data['id'] = $id;
        $data['model'] = $obj_poll;
        $this->render('admin/polls/result',$data);   
    }
    
    function showresults($poll_id)
    {
	$obj_total_count=$this->db->query("SELECT COUNT(*) as totalvotes FROM tds_votes WHERE option_id IN(SELECT id FROM tds_options WHERE ques_id='$poll_id')")->row();
	$total=$obj_total_count->totalvotes;
	$query=$this->db->query("SELECT options.id, options.value, COUNT(*) as votes FROM tds_votes, tds_options as options WHERE tds_votes.option_id=options.id AND tds_votes.option_id IN(SELECT id FROM tds_options WHERE ques_id='$poll_id') GROUP BY tds_votes.option_id")->result();
	foreach($query as $row)
        {
		$percent=round(($row->votes*100)/$total);
		echo '<div class="option" ><p>'.$row->value.' (<em>'.$percent.'%, '.$row->votes.' votes</em>)</p>';
		echo '<div class="bar ';
		
		echo '" style="width: '.$percent.'%; " ></div></div>';
	}
	echo '<p>Total Votes: '.$total.'</p>';
    }
    
    private function insert_related_options($post,$id)
    {
       $reletad_options = array();
       $obj_poll = new Poll();
       
       $this->db->where("ques_id",$id);
       $this->db->delete("options");
        
       for($i=0;$i<$obj_poll->maxNumber;$i++)
       {
           
            $post_key = $i+1;
            if(isset($post['value_'.$post_key]) && $post['value_'.$post_key]!="")
            {
                    $reletad_options[$i]['value'] = $post['value_'.$post_key];
                    if(isset($post['value_id_'.$post_key]) && $post['value_id_'.$post_key]!="")
                    {
                        $reletad_options[$i]['id'] = $post['value_id_'.$post_key];
                    }
                    else
                    {
                         $reletad_options[$i]['id'] = null;
                    }    
                    $reletad_options[$i]['ques_id'] = $id;
                
             }
              

        }
      
        if($reletad_options)
        $this->db->insert_batch('options', $reletad_options); 
        
        return $reletad_options;
     
        
    }
    /**
     * add function
     * @param none
     * @defination use for insert admin controller
     * @author Fahim
     */
    function add()
    {
        $this->cache->delete( "POLL_INFO" );
        $this->cache->delete( "POLL" );
        $this->cache->delete( "POLL_RESULT" );
        
        $obj_poll = new Poll();
        if($_POST)
        {    
            foreach($this->input->post() as $key=>$value)
            {
                $obj_poll->$key= $value; 
            }  
        }
        
       

        $data['model'] = $obj_poll;
        
        $data['maxNumber'] = $obj_poll->maxNumber;
        
        $data['options']=$obj_poll->create_option();
        
        if (!$obj_poll->save())
        {
           $this->render('admin/polls/insert',$data);
        }
        else
        {
            $cache_name_poll_id = "POLL_INFO";
            $i_poll_id = $obj_poll->id;
            $poll_ques = $obj_poll->ques;
            $this->cache->save($cache_name_poll_id, array($i_poll_id, $poll_ques), 60 * 60 * 24);
            $this->insert_related_options($this->input->post(), $obj_poll->id);
           
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * edit function
     * @param none
     * @defination use for Update admin controller
     * @author Fahim
     */
    function edit($id)
    {
        $obj_poll = new Poll($id);
        if($_POST)
        {    
            foreach($this->input->post() as $key=>$value)
            {
                $obj_poll->$key= $value; 
            }  
        }

        $data['model'] = $obj_poll;
        
        $data['maxNumber'] = $obj_poll->maxNumber;
        
        $data['options']=$obj_poll->create_option($id);
        
        if (!$obj_poll->save())
        {
           $this->render('admin/polls/insert',$data);
        }
        else
        {
            $this->insert_related_options($this->input->post(), $obj_poll->id);
           
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
        
        
        
    }

    /**
     * delete function
     * @param none
     * @defination use for delete a admin controller
     * @author Fahim
     */
    function delete()
    {
        if (!$this->input->is_ajax_request()) {
                exit('No direct script access allowed');
        }
        
        $sql_delete_votes = "delete from  tds_votes where option_id in (select id from  tds_options where ques_id=".$this->input->post('primary_id').")";
        
        $this->db->query($sql_delete_votes);
        
        
        $sql_delete_options = "delete  from  tds_options where ques_id=".$this->input->post('primary_id');
        
        $this->db->query($sql_delete_options);
        
        
        $obj_poll = new Poll($this->input->post('primary_id') );
            
        $obj_poll->delete(); 
        echo 1;
    }

    

}

?>
