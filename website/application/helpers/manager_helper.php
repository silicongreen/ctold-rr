<?php
/**
 * All custom helper function is go there
 */

/**
 * Take a filter array as param and make filtering for datatable
 */
if (!function_exists('create_html_td'))
{
    function create_html_td($td_obj,$index,$type="hm")
    {
        $td_string = "";
        $user_type = $index;
        $alread_showed = false;
        if($td_obj)
        {
            
            foreach($td_obj as $value)
            {
                if($value->user_type_paid==$index)
                {
                    $td_string = "<td><a href='javascript:void(0)' class='user_full_stat' id='".$user_type."_".$type."'>".$value->countUsers."</a></td>";
                    $alread_showed = true;
                    break;
                }    
            }
               
        
        }
        if(!$alread_showed)
        {
            $td_string = "<td>0</td>";
        }
        return $td_string;
                                            
    }
}
if (!function_exists('create_filter'))
{

    function create_filter($filter_array)
    {
        $filterString = "";
       
        foreach ($filter_array as $key => $value)
        {
            if (is_array($value) && $value[1] == 'input')
            {
                $filterString .='<div class="col_25">
                        <fieldset class="label_top top">
                        <label for="text_field_inline">' . str_replace("_", " ", $value[0]) . '</label>
                            <div>
                                <input class="text filter" type="text" id="filter_' . $key . '">
                            </div>   
                        </fieldset>
                     </div>';
            }
            else if(is_array($value) && $value[1] == 'input_date')
            {
             
                $filterString .='<div class="col_25">
                        <fieldset class="label_top top">
                        <label for="text_field_inline">' . str_replace("_", " ", $value[0]) . '</label>
                            <div>
                                <input type="text" class="datepicker filter_datepicker" style="width:100px;"  id="filter_' . $key . '">
                                
                            </div>   
                        </fieldset>
                     </div>';
            } 
            else if(is_array($value) && $value[1] == 'input_daterange')
            {
             
                $filterString .='<div class="col_50">
                        <fieldset class="label_top top">
                        <label for="text_field_inline">' . str_replace("_", " ", $value[0]) . '</label>
                            <div style="float:left;">
                                <div class="dateranger" id="filter_' . $key . '"  style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                      <i class="glyphicon glyphicon-calendar icon-calendar icon-large"></i>
                                      <span id="range_data"></span> <b class="caret"></b>
                                </div>
                                
                            </div>   
                        </fieldset>
                     </div>';
            }
            else if(is_array($value) && $value[1] == 'input_daterange2')
            {
             
                $filterString .='<div class="col_50">
                        <fieldset class="label_top top">
                        <label for="text_field_inline">' . str_replace("_", " ", $value[0]) . '</label>
                            <div style="float:left;">
                                <div class="dateranger2" id="filter_' . $key . '"  style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                      <i class="glyphicon glyphicon-calendar icon-calendar icon-large"></i>
                                      <span id="range_data"></span> <b class="caret"></b>
                                </div>
                                
                            </div>   
                        </fieldset>
                     </div>';
            }
            else if (is_array($value) && $value[1] == 'form_dropdown')
            {

                if(isset($value[3]) && $value[3])
                    $classId = 'id="filter_' . $key . '" class="uniform '.$value[3].'"';
                else
                    $classId = 'id="filter_' . $key . '" class="uniform filter"';
                $filterString .='<div class="col_25">
                    <fieldset class="label_top top">
                    <label for="text_field_inline">' . str_replace("_", " ", $value[0]) . '</label>
                        <div>
                            ' . form_dropdown('filter_' . $key, $value[2], '', $classId) . '
                        </div>   
                    </fieldset>
                    </div>';
            }
        }
        echo $filterString;
    }

}
if (!function_exists('createDatePlus'))
{
    function createDatePlus($date_string,$amount,$type="day")
    {
        if($type=="month")
        $date_return = date('Y-m-d',strtotime($date_string) + (30*24*3600*$amount));
        if($type=="day")
        $date_return = date('Y-m-d',strtotime($date_string) + (24*3600*$amount));
        if($type=="hour")
        $date_return = date('Y-m-d',strtotime($date_string) + (3600*$amount));
        if($type=="min")
        $date_return = date('Y-m-d',strtotime($date_string) + (60*$amount));
        
        return $date_return;
    }
}    

/**
 * Create password by using salt and sha512 hash function
 */
if (!function_exists('create_password'))
{

    function create_password($password,$salt=null)
    {
        if(!$salt)
        {
            $salt = md5(uniqid(rand(), true));
        }    
        $haas_password = hash( 'sha512' , $salt . $password);
        
        return array('password'=>$haas_password,'salt'=>$salt);
    }
    
}
/**
 * Create the validation error take an object for datamapper validation or a array for ci validation as param
 */

if (!function_exists('create_validation'))
{

    function create_validation($object)
    {
        $validDiv = '<div class="alert dismissible alert_red" style="width:90%; margin: 5px auto;"><img height="24" width="24" src="' . base_url() . 'images/icons/small/white/alarm_bell.png">';
        if(is_object($object))
        {
            
            foreach ($object->error->all as $e)
            {
                echo $validDiv.$e.'</div>';
            }
        }
        else
        {
           foreach ($object as $value)
            {
                echo form_error($value, $validDiv, '</div>');
            } 
        }
        
    }

}
/**
 * Check current url for make selected a menu in admin panel
 */
if (!function_exists('check_current'))
{
   function check_current($a_group)
   {
        foreach($a_group as $key=>$value)
        {
            $str_url_string = "";
            if(isset($value[1]))
            {
                $string_to_check ="admin/".$value[0]."/".$value[1];
            }
            else
            {
                $string_to_check ="admin/".$value[0];  
            }
            if(strpos(uri_string(), "edit"))
            {
               $ar_url = explode("/",uri_string());
               
               foreach($ar_url as $value)
               {
                   if($value =="edit")
                       break;
                   $str_url_string.= $value."/";
               }    
            }
            if(uri_string()==$string_to_check)
            {
                return true;
                break;
            } 
            
            else if($str_url_string==$string_to_check."/")
            {
                return true;
                break;
            } 
           
        } 
        
            
        return false;
    
   } 
    
}
/**
 * Access check for a group of menu
 */
if (!function_exists('group_check'))
{
   function group_check($a_group)
   {
        foreach($a_group as $key=>$value)
        {
           
                        
            $controller_to_check=$value[0];
            if(isset($value[1]))
            {
                $function_to_check =$value[1];
            }
            else
            {
              $function_to_check ="";  
            }
            if(access_check($controller_to_check,$function_to_check))
            {
                return true;
                break;
            }    
        } 
        return false;
    
   } 
    
}
/**
 * Access check for a  menu
 */
if (!function_exists('access_check'))
{

    function access_check($controller_name, $method_name = "")
    {

        $CI = & get_instance();

        if ($CI->session->userdata("admin"))
        {
            
            $user_data = $CI->session->userdata("admin");

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
                            return true;
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else
                    {
                        return true;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return true;
            }
        }
        else
        {
            return false;
        }
    }

}    