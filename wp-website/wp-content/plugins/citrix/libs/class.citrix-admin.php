<?php

class Citrix_admin {

    const NONCE = 'citrix-update-key';

    private static $initiated = false;

    public static function init() {
        if (!self::$initiated) {
            self::init_hooks();
        }

        if (isset($_POST['action']) && $_POST['action'] == 'enter-key') {
            self::enter_api_key();
        }
    }

    public static function init_hooks() {
        // The standalone stats page was removed in 3.0 for an all-in-one config and stats page.
        // Redirect any links that might have been bookmarked or in browser history.
        if (isset($_GET['page']) && 'citrix-stats-display' == $_GET['page']) {
            wp_safe_redirect(esc_url_raw(self::get_page_url('stats')), 301);
            die;
        }

        self::$initiated = true;

        add_action('admin_init', array('Citrix_admin', 'admin_init'));
        add_action('admin_menu', array('Citrix_admin', 'admin_menu'), 5); # Priority 5, so it's called before Jetpack's admin_menu.
//        add_action('admin_notices', array('Citrix_admin', 'display_notice'));
        add_action('admin_enqueue_scripts', array('Citrix_admin', 'load_resources'));
//        add_action('activity_box_end', array('Citrix_admin', 'dashboard_stats'));
//        add_action('rightnow_end', array('Citrix_admin', 'rightnow_stats'));
//        add_action('manage_comments_nav', array('Citrix_admin', 'check_for_spam_button'));
        add_action('wp_ajax_citrix_save_base_info', array('Citrix_admin', 'save_base_info'));

        add_action('wp_ajax_citrix_create_webinar', array('Citrix_admin', 'create_webinar'));
        add_action('wp_ajax_citrix_create_webinar_registrant', array('Citrix_admin', 'create_webinar_registrant'));
        add_action('wp_ajax_citrix_webinar_list', array('Citrix_admin', 'webinar_list'));

        add_action('wp_ajax_citrix_create_meeting', array('Citrix_admin', 'create_meeting'));
        add_action('wp_ajax_citrix_meeting_list', array('Citrix_admin', 'meeting_list'));
        add_action('wp_ajax_citrix_start_meeting', array('Citrix_admin', 'start_meeting'));
//        add_action('wp_ajax_comment_author_deurl', array('Citrix_admin', 'remove_comment_author_url'));
//        add_action('wp_ajax_comment_author_reurl', array('Citrix_admin', 'add_comment_author_url'));
//        add_action('jetpack_auto_activate_akismet', array('Citrix_admin', 'connect_jetpack_user'));
//        add_filter('plugin_action_links', array('Citrix_admin', 'plugin_action_links'), 10, 2);
//        add_filter('comment_row_actions', array('Citrix_admin', 'comment_row_action'), 10, 2);
//
//        add_filter('plugin_action_links_' . plugin_basename(plugin_dir_path(__FILE__) . 'akismet.php'), array('Citrix_admin', 'admin_plugin_settings_link'));
//
//        add_filter('wxr_export_skip_commentmeta', array('Citrix_admin', 'exclude_commentmeta_from_export'), 10, 3);
    }

    public static function admin_init() {
        load_plugin_textdomain('citrix');
    }

    public static function admin_menu() {
        self::load_menu();
    }

    public static function admin_head() {
        if (!current_user_can('manage_options'))
            return;
    }

    public static function admin_plugin_settings_link($links) {
        $settings_link = '<a href="' . esc_url(self::get_page_url()) . '">' . __('Settings', 'citrix') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public static function load_menu() {
        $hook = add_menu_page(
                'Citrix', 'Citrix', 'manage_options', 'citrix', array(
            'Citrix_admin',
            'display_page'
                ), '', 5
        );
        if (version_compare($GLOBALS['wp_version'], '3.3', '>=')) {
            add_action("load-$hook", array('Citrix_admin', 'admin_help'));
        }

//        $hook = add_submenu_page('citrix', 'Create Organizer', 'Create Organizer', 'manage_options', 'create-organizer', array('Citrix_admin', 'display_page'));
//        if (version_compare($GLOBALS['wp_version'], '3.3', '>=')) {
//            add_action("load-$hook", array('Citrix_admin', 'admin_help'));
//        }

        $hook = add_submenu_page('citrix', 'Upcoming Webinars', 'Upcoming Webinars', 'manage_options', 'upcoming-webinars', array('Citrix_admin', 'display_page'));
        if (version_compare($GLOBALS['wp_version'], '3.3', '>=')) {
            add_action("load-$hook", array('Citrix_admin', 'admin_help'));
        }

        $hook = add_submenu_page('citrix', 'Create Webinar', 'Create Webinar', 'manage_options', 'create-webinar', array('Citrix_admin', 'display_page'));
        if (version_compare($GLOBALS['wp_version'], '3.3', '>=')) {
            add_action("load-$hook", array('Citrix_admin', 'admin_help'));
        }

        $hook = add_submenu_page('citrix', 'Upcoming Meetings', 'Upcoming Meetings', 'manage_options', 'upcoming-meetings', array('Citrix_admin', 'display_page'));
        if (version_compare($GLOBALS['wp_version'], '3.3', '>=')) {
            add_action("load-$hook", array('Citrix_admin', 'admin_help'));
        }

        $hook = add_submenu_page('citrix', 'Create Meeting', 'Create Meeting', 'manage_options', 'create-meeting', array('Citrix_admin', 'display_page'));
        if (version_compare($GLOBALS['wp_version'], '3.3', '>=')) {
            add_action("load-$hook", array('Citrix_admin', 'admin_help'));
        }

//        $hook = add_submenu_page('citrix', 'Go To Webinar', 'Webinar Add Registrant', 'manage_options', 'go-to-webinar', array('Citrix_admin', 'display_page'));
//        if (version_compare($GLOBALS['wp_version'], '3.3', '>=')) {
//            add_action("load-$hook", array('Citrix_admin', 'admin_help'));
//        }
//        $hook = add_submenu_page('citrix', 'Go To Meeting', 'Go To Meeting', 'manage_options', 'go-to-meeting', array('Citrix_admin', 'display_page'));
//        if (version_compare($GLOBALS['wp_version'], '3.3', '>=')) {
//            add_action("load-$hook", array('Citrix_admin', 'admin_help'));
//        }
    }

    public static function load_resources() {
        global $hook_suffix;

        wp_register_style('citrix.css', CITRIX__PLUGIN_URL . '_inc/citrix.css', array(), CITRIX__MEETING_VERSION);
        wp_enqueue_style('citrix.css');

        wp_register_script('citrix.js', CITRIX__PLUGIN_URL . '_inc/citrix.js', array('jquery'), CITRIX__MEETING_VERSION);
        wp_enqueue_script('citrix.js');

        wp_register_script('bootstrap.min.js', 'http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js', array('jquery'), null, false);
        wp_enqueue_script('bootstrap.min.js');

        wp_register_script('moment.js', 'http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js', array('jquery'), null, false);
        wp_enqueue_script('moment.js');

        wp_register_script('datetimepicker.js', CITRIX__PLUGIN_URL . '_inc/bootstrap-datetimepicker.min.js', array('jquery'), CITRIX__MEETING_VERSION);
        wp_enqueue_script('datetimepicker.js');

        wp_register_style('datetimepicker.css', CITRIX__PLUGIN_URL . '_inc/bootstrap-datetimepicker.min.css', array(), CITRIX__MEETING_VERSION);
        wp_enqueue_style('datetimepicker.css');
    }

    public static function save_base_info() {

        if (isset($_POST['citrix_base_nonce'])) {
            check_ajax_referer('citrix_base_info', 'citrix_base_nonce');
        }

        unset($_POST['action']);
        unset($_POST['undefined']);
        unset($_POST['citrix_base_nonce']);

        update_option('citrix_base_info', $_POST, 'yes');

        if (defined('DOING_AJAX') && DOING_AJAX) {
            wp_send_json(array(
                'saved' => TRUE,
            ));
        } else {
            $redirect_to = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : admin_url('edit-comments.php');
            wp_safe_redirect($redirect_to);
            exit;
        }
    }

    public static function webinar_list() {

        if (isset($_POST['citrix_webinar_list_nonce'])) {
            check_ajax_referer('citrix_webinar_list', 'citrix_webinar_list_nonce');
        }

        unset($_POST['action']);
        unset($_POST['undefined']);
        unset($_POST['citrix_webinar_list_nonce']);

        require_once __DIR__ . '/vendor/autoload.php';

        $citrix_user = self::get_citrix_user();

        $client = new \Citrix\Authentication\Direct($citrix_user['g2w_consumer_key']);
        $client->setAuthorizeUrl($citrix_user['citrix_base_url'] . $citrix_user['citrix_oauth_url']);
        $client->auth($citrix_user['citrix_client_email'], $citrix_user['citrix_client_pwd']);

        $goToWebinar = new \Citrix\GoToWebinar($client);
        $response = $goToWebinar->getWebinars();

        include_once CITRIX__PLUGIN_DIR . 'views/partials/_webinar_list.php';
    }

    public static function create_webinar() {

        if (isset($_POST['citrix_webinar_nonce'])) {
            check_ajax_referer('citrix_webinar_info', 'citrix_webinar_nonce');
        }

        unset($_POST['action']);
        unset($_POST['undefined']);
        unset($_POST['citrix_webinar_nonce']);

        $post_data['subject'] = $_POST['g2w_subject'];
        $post_data['description'] = $_POST['g2w_description'];
        $post_data['times'][0]['startTime'] = date('Y-m-d\TH:i:s\Z', strtotime($_POST['g2w_startDateTime']));
        $post_data['times'][0]['endTime'] = date('Y-m-d\TH:i:s\Z', strtotime($_POST['g2w_endDateTime']));
        $post_data['timeZone'] = 'Asia/Dhaka';

        require_once __DIR__ . '/vendor/autoload.php';

        $citrix_user = self::get_citrix_user();

        $client = new \Citrix\Authentication\Direct($citrix_user['g2w_consumer_key']);
        $client->setAuthorizeUrl($citrix_user['citrix_base_url'] . $citrix_user['citrix_oauth_url']);
        $client->auth($citrix_user['citrix_client_email'], $citrix_user['citrix_client_pwd']);

        $webinars = new \Citrix\GoToWebinar($client); //@see $client definition above
        $webinars->setUri($citrix_user['citrix_base_url'] . $citrix_user['g2w_api_uri']);
        $response = $webinars->createWebinar($post_data);

        if (defined('DOING_AJAX') && DOING_AJAX) {

            if (isset($response['int_err_code'])) {
                wp_send_json(array(
                    'saved' => false,
                    'msg' => $response['msg']
                ));
            } elseif (isset($response['errorCode'])) {
                wp_send_json(array(
                    'saved' => false,
                    'msg' => $response['errorCode'] . ': ' . $response['description']
                ));
            } else {
                wp_send_json(array(
                    'saved' => TRUE,
                    'msg' => 'Webinar Successfully added.'
                ));
            }
        }
    }

    public static function create_webinar_registrant() {

        if (isset($_POST['citrix_webinar_reg_nonce'])) {
            check_ajax_referer('citrix_webinar_reg_info', 'citrix_webinar_reg_nonce');
        }

        unset($_POST['action']);
        unset($_POST['undefined']);
        unset($_POST['citrix_webinar_reg_nonce']);

        require_once __DIR__ . '/vendor/autoload.php';

        $citrix_user = self::get_citrix_user();

        $client = new \Citrix\Authentication\Direct($citrix_user['g2w_consumer_key']);
        $client->setAuthorizeUrl($citrix_user['citrix_base_url'] . $citrix_user['citrix_oauth_url']);
        $client->auth($citrix_user['citrix_client_email'], $citrix_user['citrix_client_pwd']);

        $registrantData = array('email' => $_POST['g2w_email'], 'firstName' => $_POST['g2w_fname'], 'lastName' => $_POST['g2w_lname']);
        $goToWebinar = new \Citrix\GoToWebinar($client); //@see $client definition above
        $response = $goToWebinar->register($_POST['webinar_id'], $registrantData);

        if (defined('DOING_AJAX') && DOING_AJAX) {

            if ($response->hasErrors()) {
                wp_send_json(array(
                    'saved' => false,
                    'msg' => $response->getErrors()[0]
                ));
            } else {
                wp_send_json(array(
                    'saved' => TRUE,
                    'msg' => 'Webinar registrant successfully added.'
                ));
            }
        }
    }

    public static function meeting_list() {

        if (isset($_POST['citrix_meeting_list_nonce'])) {
            check_ajax_referer('citrix_meeting_list', 'citrix_meeting_list_nonce');
        }

        unset($_POST['action']);
        unset($_POST['undefined']);
        unset($_POST['citrix_meeting_list_nonce']);

        require_once __DIR__ . '/vendor/autoload.php';

        $citrix_user = self::get_citrix_user();

        $client = new \Citrix\Authentication\Direct($citrix_user['g2m_consumer_key']);
        $client->setAuthorizeUrl($citrix_user['citrix_base_url'] . $citrix_user['citrix_oauth_url']);
        $client->auth($citrix_user['citrix_client_email'], $citrix_user['citrix_client_pwd']);

        $goToMeeting = new \Citrix\GoToMeeting($client);
        $response = $goToMeeting->getUpcoming();

        include_once CITRIX__PLUGIN_DIR . 'views/partials/_meeting_list.php';
    }

    public static function create_meeting() {

        if (isset($_POST['citrix_create_meeting_nonce'])) {
            check_ajax_referer('citrix_create_meeting_info', 'citrix_create_meeting_nonce');
        }

        unset($_POST['action']);
        unset($_POST['undefined']);
        unset($_POST['citrix_create_meeting_nonce']);

        $post_data['subject'] = $_POST['g2m_subject'];
        $post_data['meetingtype'] = 'scheduled';
        $post_data['passwordrequired'] = false;
        $post_data['conferencecallinfo'] = 'Free';
        $post_data['timezonekey'] = '';

        require_once __DIR__ . '/vendor/autoload.php';

        $citrix_user = self::get_citrix_user();

        $clientDateTimeZone = new DateTimeZone($citrix_user['citrix_client_tz']);
        $clientDateTime = new DateTime($_POST['g2m_startDateTime'], $clientDateTimeZone);

        $timeOffset = $clientDateTime->getOffset();
        $sign = ($timeOffset > 0) ? '+' : '-';

        $post_data['starttime'] = date('Y-m-d\TH:i:s\Z', strtotime($_POST['g2m_startDateTime'] . $sign . $timeOffset . ' seconds'));
        $post_data['endtime'] = date('Y-m-d\TH:i:s\Z', strtotime($_POST['g2m_endDateTime'] . $sign . $timeOffset . ' seconds'));

        $client = new \Citrix\Authentication\Direct($citrix_user['g2m_consumer_key']);
        $client->setAuthorizeUrl($citrix_user['citrix_base_url'] . $citrix_user['citrix_oauth_url']);
        $client->auth($citrix_user['citrix_client_email'], $citrix_user['citrix_client_pwd']);

        $goToMeeting = new \Citrix\GoToMeeting($client); //@see $client definition above
        $response = $goToMeeting->createMeeting($post_data);

        if (defined('DOING_AJAX') && DOING_AJAX) {

            if (isset($response['int_err_code'])) {
                wp_send_json(array(
                    'saved' => false,
                    'msg' => $response['msg']
                ));
            } else {
                wp_send_json(array(
                    'saved' => TRUE,
                    'msg' => 'Successfully added.'
                ));
            }
        }
    }

    public static function start_meeting() {

        if (isset($_POST['citrix_meeting_nonce'])) {
            check_ajax_referer('citrix_meeting_info', 'citrix_meeting_nonce');
        }

        unset($_POST['action']);
        unset($_POST['undefined']);
        unset($_POST['citrix_meeting_nonce']);

        require_once __DIR__ . '/vendor/autoload.php';

        $citrix_user = self::get_citrix_user();

        $client = new \Citrix\Authentication\Direct($citrix_user['g2m_consumer_key']);
        $client->setAuthorizeUrl($citrix_user['citrix_base_url'] . $citrix_user['citrix_oauth_url']);
        $client->auth($citrix_user['citrix_client_email'], $citrix_user['citrix_client_pwd']);

        $goToMeeting = new \Citrix\GoToMeeting($client); //@see $client definition above
        $response = $goToMeeting->startMeeting($_POST['meeting_id']);
        
        if (defined('DOING_AJAX') && DOING_AJAX) {
            
            if ($goToMeeting->hasErrors()) {
                wp_send_json(array(
                    'saved' => false,
                    'msg' => $goToMeeting->getErrors()[0]
                ));
            } else {

                global $phpmailer;

                if (!( $phpmailer instanceof PHPMailer )) {
                    require_once ABSPATH . WPINC . '/class-phpmailer.php';
                    require_once ABSPATH . WPINC . '/class-smtp.php';
                    $phpmailer = new PHPMailer(true);
                }

                $phpmailer->isSMTP();
                $phpmailer->Host = 'host.champs21.com';
                $phpmailer->SMTPAuth = true;
                $phpmailer->Username = 'info@classtune.com';
                $phpmailer->Password = '174097@hM&^256';
                $phpmailer->SMTPSecure = 'ssl';
                $phpmailer->Port = 465;
                $phpmailer->CharSet = 'utf-8';
                
                $phpmailer->setFrom('info@classtune.com', 'Classtune');
                $phpmailer->addAddress($_POST['g2m_email'], $_POST['g2m_fname'] . ' ' . $_POST['g2m_lname']);
                $phpmailer->Subject = 'Classtune: Request for online meeting.';
                
                include_once CITRIX__PLUGIN_DIR . 'views/email/_meeting_start.php';
                
                $phpmailer->Body = $str_content;
                $phpmailer->send();
                
                wp_send_json(array(
                    'saved' => TRUE,
                    'msg' => 'Meeting invitation successfully sent to the email.'
                ));
            }
        }
    }

    /**
     * Add help to the Citrix page
     *
     * @return false if not the Citrix page
     */
    public static function admin_help() {
        $current_screen = get_current_screen();

        // Screen Content
        if (current_user_can('manage_options')) {
            //configuration page
            $current_screen->add_help_tab(
                    array(
                        'id' => 'overview',
                        'title' => __('Overview', 'citrix'),
                        'content' =>
                        '<p><strong>' . esc_html__('Citrix Configuration', 'citrix') . '</strong></p>' .
                        '<p>' . esc_html__('Citrix filters out spam, so you can focus on more important things.', 'citrix') . '</p>' .
                        '<p>' . esc_html__('On this page, you are able to enter/remove an API key, view account information and view spam stats.', 'citrix') . '</p>',
                    )
            );

            $current_screen->add_help_tab(
                    array(
                        'id' => 'settings',
                        'title' => __('Settings', 'citrix'),
                        'content' =>
                        '<p><strong>' . esc_html__('Citrix Configuration', 'citrix') . '</strong></p>' .
                        '<p><strong>' . esc_html__('API Key', 'citrix') . '</strong> - ' . esc_html__('Enter/remove an API key.', 'akismet') . '</p>' .
                        '<p><strong>' . esc_html__('Comments', 'citrix') . '</strong> - ' . esc_html__('Show the number of approved comments beside each comment author in the comments list page.', 'akismet') . '</p>' .
                        '<p><strong>' . esc_html__('Strictness', 'citrix') . '</strong> - ' . esc_html__('Choose to either discard the worst spam automatically or to always put all spam in spam folder.', 'akismet') . '</p>',
                    )
            );

            $current_screen->add_help_tab(
                    array(
                        'id' => 'account',
                        'title' => __('Account', 'citrix'),
                        'content' =>
                        '<p><strong>' . esc_html__('Citrix Configuration', 'citrix') . '</strong></p>' .
                        '<p><strong>' . esc_html__('Subscription Type', 'citrix') . '</strong> - ' . esc_html__('The Akismet subscription plan', 'akismet') . '</p>' .
                        '<p><strong>' . esc_html__('Status', 'citrix') . '</strong> - ' . esc_html__('The subscription status - active, cancelled or suspended', 'akismet') . '</p>',
                    )
            );
        }

        // Help Sidebar
        $current_screen->set_help_sidebar(
                '<p><strong>' . esc_html__('For more information:', 'citrix') . '</strong></p>' .
                '<p><a href="https://akismet.com/faq/" target="_blank">' . esc_html__('Citrix FAQ', 'citrix') . '</a></p>' .
                '<p><a href="https://akismet.com/support/" target="_blank">' . esc_html__('Citrix Support', 'citrix') . '</a></p>'
        );
    }

    public static function get_citrix_user() {
        return get_option('citrix_base_info');
    }

    public static function display_invalid_version() {
        Citrix::view('notice', array('type' => 'version'));
    }

    public static function display_api_key_warning() {
        Citrix::view('notice', array('type' => 'plugin'));
    }

    public static function display_page() {
        $page = $_GET['page'];

        $page_load_fnc = 'display_';
        $page_load_fnc .= ($page == 'citrix') ? 'configuration' : 'action';
        $page_load_fnc .= '_page';

        call_user_func_array(array('self', $page_load_fnc), array($page, array()));
    }

    public static function display_configuration_page($page, $data = []) {
        $citrix_user = self::get_citrix_user();
        $time_zone_list = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        Citrix::log(compact('citrix_user'));
        Citrix::view($page, compact('citrix_user', 'time_zone_list'));
    }

    public static function display_action_page($page, $data = []) {

        $consumer_key_and_secret = '';
        $citrix_user = '';

        Citrix::log(compact('citrix_user'));
        Citrix::view($page, compact('consumer_key_and_secret', 'citrix_user'));
    }

}
