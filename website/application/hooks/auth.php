<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 class auth
 {
      
    

     public function check()
    {

        $CI = & get_instance();
        
        if ($CI->uri->segment(1) == 'admin')
        {
            
            if ($CI->session->userdata("admin"))
            {


                if ($CI->router->fetch_class() == 'login')
                {
                    if ($CI->router->fetch_method() != 'logout')
                    {
                        redirect("admin/manage");
                    }
                }
                else
                {
                    ///ACL CODE

                    $user_data = $CI->session->userdata("admin");
                    $controller_name = $CI->router->fetch_class();
                    $method_name = $CI->router->fetch_method();
                    $controllerExists = $CI->db->get_where('controllers', array('controller' => $controller_name))->row();
                    if (count($controllerExists) > 0 && $controllerExists->allow_from_all!=1)
                    {
                        $hasAccess = $CI->db->get_where('groups_controllers', array('group_id' => $user_data['group_id'], 'controller_id' => $controllerExists->id))->row();
                        if (count($hasAccess) > 0)
                        {
                            $methodExists = $CI->db->get_where('functions', array('controller_id' => $controllerExists->id, 'function' => $method_name))->row();
                            if (count($methodExists) > 0 && $methodExists->allow_from_all!=1)
                            {
                                $hasAccessMethod = $CI->db->get_where('groups_functions', array('group_id' => $user_data['group_id'], 'function_id' => $methodExists->id))->row();
                                if (count($hasAccessMethod) > 0)
                                {
                                    //method had access
                                }
                                else
                                {
                                    redirect("admin/manage/access_denied");
                                }
                            }
                            else
                            {
                                //Method is common for this controller so no need for check it's access
                            }
                        }
                        else
                        {
                            redirect("admin/manage/access_denied");
                        }
                    }
                    else
                    {
                        //Do nothing Acl for controller must be insert into menu database
                    }
                }
            }
            else
            {
                if ($CI->router->fetch_class() != 'login')
                {
                   
                    redirect("admin/login");
                }
            }
        }
    }
}
  
