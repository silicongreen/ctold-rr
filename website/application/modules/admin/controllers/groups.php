<?php

/*
 * Group Controller
 * Admin User Group management
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class groups extends MX_Controller
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
     * @defination use for showing table header and setting table id for group
     * @author Fahim
     */
    function index()
    {

        //set table id in table open tag
        $tmpl = array('table_open' => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">');
        $this->table->set_template($tmpl);

        $this->table->set_heading('Name', 'Description', 'Action');


        $this->render('admin/group/index');
    }

    /**
     * Datable function
     * @param none
     * @defination use for showing datatable for group callback function
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
        $this->datatables->set_buttons("settings");
        $this->datatables->set_controller_name("groups");
        $this->datatables->set_primary_key("id");

        $this->datatables->select('id,name,description')
                ->unset_column('id')
                ->from('groups');

        echo $this->datatables->generate();
    }

    /**
     * add function
     * @param none
     * @defination use for insert group
     * @author Fahim
     */
    function add()
    {
        $obj_group = new Group();
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_group->$key = $value;
            }
        }

        $data['model'] = $obj_group;
        if (!$obj_group->save())
        {
            $this->render('admin/group/insert', $data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * edit function
     * @param none
     * @defination use for Update group
     * @author Fahim
     */
    function edit($id)
    {

        $obj_group = new Group($id);
        if ($_POST)
        {
            foreach ($this->input->post() as $key => $value)
            {
                $obj_group->$key = $value;
            }
        }

        $data['model'] = $obj_group;
        if (!$obj_group->save() || !$_POST)
        {
            $this->render('admin/group/insert', $data);
        }
        else
        {
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }
    }

    /**
     * delete function
     * @param none
     * @defination use for delete a admin group
     * @author Fahim
     */
    function delete()
    {
        if (!$this->input->is_ajax_request())
        {
            exit('No direct script access allowed');
        }
        $obj_group = new Group($this->input->post('primary_id'));
        $obj_group->delete();
        echo 1;
    }

    /**
     * setting function
     * @param none
     * @defination use for Acl management for each group
     * @author Fahim
     */
    function settings($id)
    {
        $arTemp = array(); // array('title' => 'Choose Links'); 

        $c = new Controller();
        $controllerMenu = $c->get();

        if ($_POST)
        {
            $this->db->where('group_id', $id);
            $this->db->delete('groups_controllers');
            $a_group_acl = array();
            foreach ($this->input->post('controller') as $value)
            {
                $a_group_acl['group_id'] = $id;
                $a_group_acl['controller_id'] = $value;

                $this->db->insert('groups_controllers', $a_group_acl);
            }

            $this->db->where('group_id', $id);
            $this->db->delete('groups_functions');
            $a_group_acl = array();
            foreach ($this->input->post('function') as $value)
            {
                $a_group_acl['group_id'] = $id;
                $a_group_acl['function_id'] = $value;

                $this->db->insert('groups_functions', $a_group_acl);
            }
            echo "<script>parent.oTable.fnClearTable(true); parent.$.fancybox.close();</script>";
        }

        if (count($controllerMenu) > 0)
        {
            foreach ($controllerMenu as $value)
            {
                $has_access = $this->db->get_where('groups_controllers', array('controller_id' => $value->id, 'group_id' => $id))->row();

                $arTemps['title'] = $value->name;
                $arTemps['id'] = $value->id;
                $arTemps['checked'] = (count($has_access) > 0) ? true : false;
                $arTemps['children'] = array();

                $arChildrens = $this->db->get_where('functions', array('controller_id' => $value->id))->result();


                $arChildrenTemp = array();
                if (count($arChildrens > 0))
                {
                    foreach ($arChildrens as $objChildren)
                    {
                        $has_access = $this->db->get_where('groups_functions', array('function_id' => $objChildren->id, 'group_id' => $id))->row();
                        $arChildrenTemp['title'] = $objChildren->name;
                        $arChildrenTemp['checked'] = (count($has_access) > 0) ? true : false;
                        $arChildrenTemp['id'] = $objChildren->id;
                        $arTemps['children'][] = $arChildrenTemp;
                    }
                }
                $arTemp[] = $arTemps;
            }
        }

        $arData['arLinkData'] = $arTemp;

        $this->render('admin/group/settings', $arData);
    }

}

?>
