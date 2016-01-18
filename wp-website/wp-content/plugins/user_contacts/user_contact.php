<?php
/*
  Plugin Name: User Contact Support
  Plugin URI: http://www.classtune.com
  Description: Plugin for displaying Support contact from user
  Author: Fahim
  Version: 1.0
  Author URI: http://www.champs21.com
 */
add_action('admin_menu', 'contact_support_user');
add_action('wp_ajax_nopriv_login_user_classtune', 'login_user_classtune');
add_action('wp_ajax_login_user_classtune', 'login_user_classtune');


function contact_support_user() {
    $page_cat = add_menu_page('Support Contact From Users', 'Support Contact', 'delete_pages', 'support_contact_from_users', 'support_contact_from_users', plugins_url('images/support.png', __FILE__));
    $catalogs = add_submenu_page('support_contact_from_users', 'Preffered Time Statistics', 'Preffered Time Statistics', 'delete_pages', 'support_preffered_from_users', 'support_preffered_from_users');
}
function login_user_classtune() 
{
    $username = $_POST['username'];
    $password = $_POST['password'];
    if($username && $password)
    {
        $url = check_login_paid($username, $password);
        if($url)
        {
            echo $url;
        }
        else
        {
            echo "0";
        }    
    }
    else
    {
        echo "0";
    } 
    die();
}
function check_login_paid($user_name,$password) 
{
    $mydb = new wpdb('champs21_champ','1_84T~vADp2$','champs21_school','localhost');
    $users = $mydb->get_row($mydb->prepare("select * from users where (username=%s AND is_approved=1) AND (is_deleted=0 OR parent=1)",$user_name));




    if($users)
    {
        $hashed_password = sha1($users->salt . $password);
        if ($hashed_password == $users->hashed_password) 
        {
            $domain = $mydb->get_row("select * from school_domains where linkable_id='".$users->school_id."'");

            if($domain)
            {
                 $random = md5(rand());
                 $insert['auth_id'] = $random;
                 $insert['user_id'] = $users->id;
                 $insert['expire'] = date("Y-m-d H:i:s", strtotime("+1 Day"));
                 $mydb->insert("tds_user_auth", $insert);
                 $params = "?username=" . $user_name . "&password=" . $password . "&auth_id=" . $random . "&user_id=" . $users->id;
                 $url = "http://" . $domain->domain . $params;

                 return $url;
            }
        }
    }
    return false;
}

function support_preffered_from_users() {
    global $wpdb;
    
    $time_array = array(
        "12am-4am"=>array("00:00:00","04:00:00"),
        "4am-8am"=>array("04:00:00","08:00:00"),
        "8am-12pm"=>array("08:00:00","12:00:00"),
        "12pm-4pm"=>array("12:00:00","16:00:00"),
        "4pm-8pm"=>array("16:00:00","20:00:00"),
        "8pm-12am"=>array("20:00:00","23:59:59")
    );
    
    $j_array = array();
    $i = 0;
    foreach($time_array as $key=>$value)
    {
        $total = $wpdb->get_var($wpdb->prepare("SELECT COUNT( id ) FROM mirrormx_customer_contact where (start_time>='%s' and start_time<='%s') or (end_time>='%s' and end_time<='%s')",$value[0],$value[1],$value[0],$value[1]));
    
        $j_array[] = array($key,(int)$total); 
    }    
    
    
    require 'views/chart.php';
}

function support_contact_from_users() {
    require_once("pagination.class.php");
    global $wpdb;

    $items = $wpdb->get_results("SELECT * FROM mirrormx_customer_contact order by id DESC");


    
    if (count($items) > 0) {
        $p = new pagination;
        $p->items(count($items));
        $p->limit(10); // Limit entries per page
        $p->target("admin.php?page=support_contact_from_users");
        $p->currentPage($_GET[$p->paging]); // Gets and validates the current page
        $p->calculate(); // Calculates what to show
        $p->parameterName('paging');
        $p->changeClass("meneame");
        $p->adjacents(1); //No. of page away from the current page

        if (!isset($_GET['paging'])) {
            $p->page = 1;
        } else {
            $p->page = $_GET['paging'];
        }

        //Query for limit paging
        $limit = "LIMIT " . ($p->page - 1) * $p->limit . ", " . $p->limit;
    } else {
        
    }
    require 'views/contacts.php';
}