<?php

/*
 * widget Controller
 * Admin Widget management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class widget_manager extends MX_Controller
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

        $this->table->set_heading('Id','Widget Name', 'Action');
        $this->render('admin/widget/index');
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
        $this->datatables->set_controller_name("widget");
        $this->datatables->set_custom_string(2, array(1 => 'Yes', 0 => 'No'));
        $this->datatables->set_primary_key("id");

        $this->datatables->select('id,name,type')
                ->unset_column('id')
                ->from('widget');

        echo $this->datatables->generate();
    }

    /**
     * add function
     * @param none
     * @defination use for insert Widget
     * @author Huffas
     */
    function add()
    {
        $obj_widget = new widget_model();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_widget->$key = $value;
            }
        }

        $ar_widget_type = array(
            ""                  => "Select Widget",
            "news"              => "News", 
            "tab"               => "Tab", 
            "text"              => "Text",
            "more"              => "More",
            "most_viewed"       => "Most Viewed",
            "most discussed"    => "Most Discussed"
        );
        
        $ar_tab_type = array(
            ""                  => "Select Widget",
            "news"              => "News", 
            "text"              => "Text",
            "more"              => "More",
            "most_viewed"       => "Most Viewed",
            "most_discussed"    => "Most Discussed",
            "cartoon"           => "Cartoon", 
        );
        
        $ar_ad_position = array(
            ""          => "Select Ad Position",
            "top"       => "Top", 
            "bottom"    => "Bottom", 
            "both"      => "Both"
        );
        
        $data['widget_type'] = $ar_widget_type;
        $data['ad_position'] = $ar_ad_position;
        $data['tab_type']    = $ar_tab_type;
        
        $obj_gallery = new Gallery_model();
        $ar_gallery_data = $obj_gallery->where('gallery_type','5')->get();
        $data['cartoon_gallery']    = $ar_gallery_data;
        
        $obj_post = new Posts();
        $data['category_tree'] = $obj_post->category_tree();
        
        $data['model'] = $obj_widget;
        
        if (!$obj_widget->save())
        {
            
            $this->render('admin/widget/insert', $data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * edit function
     * @param none
     * @defination use for Update Byline
     * @author Fahim
     */
    function edit($id)
    {

        
        $obj_byline = new Byline($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_byline->$key = $value;
            }
        }

        $data['model'] = $obj_byline;
        if (!$obj_byline->save() || !$_POST)
        {
            $this->render('admin/byline/insert', $data);
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
        $obj_byline = new Byline($this->input->post('primary_id'));
        $obj_byline->delete();
        echo 1;
    }

    

}

?>
