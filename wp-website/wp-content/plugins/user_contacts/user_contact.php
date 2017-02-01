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

add_action('wp_ajax_nopriv_send_mail_classtune', 'send_mail_classtune');
add_action('wp_ajax_send_mail_classtune', 'send_mail_classtune');

/*if (!function_exists("rsvp_video_background_frontend_script")) {

    function rsvp_video_background_frontend_script() {
        wp_enqueue_script('ajax-script', plugin_dir_url(__FILE__) . 'js/user_contact.js');  
        wp_localize_script( 'ajax-script', 'contact_lol',
                array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
          		

    }
    

}
add_action('wp_enqueue_scripts', 'rsvp_video_background_frontend_script');*/


//wp_enqueue_script('ajax-script', plugin_dir_url(__FILE__) . 'js/user_contact.js');  
// wp_localize_script( 'ajax-script', 'contact_lol', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );



    
}
if (!function_exists('send_mail_classtune')) {
    function send_mail_classtune() {
        //check_ajax_referer("login_security","login_security_field");
        if (isset($_POST['name']) AND isset($_POST['email']) AND isset($_POST['subject']) AND isset($_POST['massage'])) {
            $to = 'rlikhon@gmail.com';
            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $phone = filter_var($_POST['phone'], FILTER_SANITIZE_EMAIL);
            $user_type = filter_var($_POST['user_type'], FILTER_SANITIZE_EMAIL);
            $school_name = filter_var($_POST['school_name'], FILTER_SANITIZE_EMAIL);
            $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
            $message_content = filter_var($_POST['massage'], FILTER_SANITIZE_STRING);

            $subject = $subject. "(Classtune contact)";

            $message = "<b>Subject: " . $subject . "</b><br/>";
            $message .= "Name: " . $name . "<br/>";
            $message .= "User Type: " . $user_type . "<br/>";
            $message .= "School Name: " . $school_name . "<br/>";
            $message .= "Contact Number: " . $phone . "<br/>";
            $message .= "E-mail: " . $email . "<br/>";
            $message .= "Comment: " . $message_content . "<br/><br /><br />";
            echo "OOOOOOOOOOOO";
            $sent = send_email($to, $email, $name, $subject, $message);
            if ($sent) {

                $auto_name = "classtune.com";
                $auto_subject = "Greetings from Classtune team";
                $auto_message = "Dear " . $name . ",<br /><br />";
                $auto_message .= "Greetings from Classtune team." . "<br /><br />";
                $auto_message .= "Thank you very much for contacting with us. Our team will communicate with you.  <br/><br />";
                $auto_message .= "Your Contact Details: <br/><br />";
                $auto_message .= "Name: " . $name . "<br/>";
                $auto_message .= "User Type: " . $user_type . "<br/>";
                $auto_message .= "School Name: " . $school_name . "<br/>";
                $auto_message .= "Contact Number: " . $phone . "<br/>";
                $auto_message .= "E-mail: " . $email . "<br/>";
                $auto_message .= "Comment: " . $message_content . "<br/><br /><br />";


                $auto_message .= "Regards,<br/>";
                $auto_message .= "Customer Service Team<br/>";
                $auto_message .= "<img src='http://www.classtune.dev/images/logo/classtune.png'>";

                $sent2 = autoreply_email($email, $to, $auto_name, $auto_subject, $auto_message);
                if ($sent2)
                {
                        echo 'Message sent! Our team will communicate with you.';
                }
                else
                {
                        echo 'Message sent!';
                }
            } else {
                echo "20";die();exit;
            }
        } else {
            echo "1";die();exit;
        }
        die();exit;
    }
}
if (!function_exists('lol_email')) {
	function lol_email($to, $from_mail, $from_name, $subject, $message) {
        $header = array();
        $header[] = "MIME-Version: 1.0";
        $header[] = "From: {$from_name}<{$from_mail}>";
        /* Set message content type HTML */
        $header[] = "Content-type:text/html; charset=iso-8859-1";
        $header[] = "Content-Transfer-Encoding: 7bit";
        
        //if (wp_mail($to, $subject, $message, implode("\r\n", $header)))
        if (wp_mail($to, $subject, $message))
        {
            return true;
        }
            
    }
}
if (!function_exists('lol_autoreply_email')) {
	function lol_autoreply_email($to, $from_mail, $from_name, $subject, $message) {
        $header = array();
        $header[] = "MIME-Version: 1.0";
        $header[] = "From: {$from_name}<{$from_mail}>";
        /* Set message content type HTML */
        $header[] = "Content-type:text/html; charset=iso-8859-1";
        $header[] = "Content-Transfer-Encoding: 7bit";
        //if (wp_mail($to, $subject, $message, implode("\r\n", $header)))
        
        if (wp_mail($to, $subject, $message))
        {
            return true;
        }
    }
}
if (!function_exists('contact_support_user')) {
    function contact_support_user() {
        $page_cat = add_menu_page('Support Contact From Users', 'Support Contact', 'delete_pages', 'support_contact_from_users', 'support_contact_from_users', plugins_url('images/support.png', __FILE__));
        $catalogs = add_submenu_page('support_contact_from_users', 'Preffered Time Statistics', 'Preffered Time Statistics', 'delete_pages', 'support_preffered_from_users', 'support_preffered_from_users');
    }
}
if (!function_exists('login_user_classtune')) {
    function login_user_classtune() {
        check_ajax_referer("login_security","login_security_field");
        $username = $_POST['username'];
        $password = $_POST['password'];
        if ($username && $password) {
            $url = check_login_paid($username, $password);
            if ($url) {
                echo $url;
            } else {
                echo "0";
            }
        } else {
            echo "0";
        }
        die();
    }
}
if (!function_exists('check_login_paid')) {
    function check_login_paid($user_name, $password) {
        $mydb = new wpdb('champs21_champ', '1_84T~vADp2$', 'champs21_school', 'localhost');
        $users = $mydb->get_row($mydb->prepare("select * from users where (username=%s AND is_approved=1) AND (is_deleted=0 OR parent=1)", $user_name));




        if ($users) {
            $hashed_password = sha1($users->salt . $password);
            if ($hashed_password == $users->hashed_password) {
                $domain = $mydb->get_row("select * from school_domains where linkable_id='" . $users->school_id . "'");

                if ($domain) {
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
}
if (!function_exists('support_preffered_from_users')) {
    function support_preffered_from_users() {
        global $wpdb;

        $time_array = array(
            "12am-4am" => array("00:00:00", "04:00:00"),
            "4am-8am" => array("04:00:00", "08:00:00"),
            "8am-12pm" => array("08:00:00", "12:00:00"),
            "12pm-4pm" => array("12:00:00", "16:00:00"),
            "4pm-8pm" => array("16:00:00", "20:00:00"),
            "8pm-12am" => array("20:00:00", "23:59:59")
        );

        $j_array = array();
        $i = 0;
        foreach ($time_array as $key => $value) {
            $total = $wpdb->get_var($wpdb->prepare("SELECT COUNT( id ) FROM mirrormx_customer_contact where (start_time>='%s' and start_time<='%s') or (end_time>='%s' and end_time<='%s')", $value[0], $value[1], $value[0], $value[1]));

            $j_array[] = array($key, (int) $total);
        }


        require 'views/chart.php';
    }
}
if (!function_exists('support_contact_from_users')) {
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
}