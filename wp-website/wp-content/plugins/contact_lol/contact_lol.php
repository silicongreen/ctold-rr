<?php
/**
 * Plugin Name: Contact LOL
 * Plugin URI: http://dhakawall.com
 * Description: Allows users to contact with you
 * Version: 1.0.0
 * Author: Rezoanul Arefin
 * Author URI: http://www.dhakawall.com
 * License: GPL2
 */

add_action( 'wp_enqueue_scripts', 'contact_lol_enqueue_scripts' );

if (!function_exists('contact_lol_enqueue_scripts')) {
	function contact_lol_enqueue_scripts() {
		if( is_single() ) {
			wp_enqueue_style( 'contact_lol', plugins_url( '/contact_lol.css', __FILE__ ) );
		}

		wp_enqueue_script( 'contact_lol', plugins_url( '/contact_lol.js', __FILE__ ), array('jquery'), '1.0', true );
	}
}

add_action( 'wp_ajax_nopriv_post_love_add_love', 'lol_send_mail' );
add_action( 'wp_ajax_post_love_add_love', 'lol_send_mail' );

if (!function_exists('lol_send_mail')) {
	function lol_send_mail() {
		
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) { 
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
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

					$sent = lol_email($to, $email, $name, $subject, $message);
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

						$sent2 = lol_autoreply_email($email, $to, $auto_name, $auto_subject, $auto_message);
						if ($sent2)
						{
							echo 'Message sent! Our team will communicate with you.';
						}
						else
						{
							echo 'Message sent!';
						}    
						
					} else {
						echo "0";
					}
				} else {
					echo "1";
				}
				return;
			}
			die();
		}
		else {
			
			exit();
		}
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
        echo $subject;
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