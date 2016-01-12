<?php

class Citrix {

    const API_HOST_BASE_URI = 'https://api.citrixonline.com';
    const API_CLIENT_EMAIL = 'nurul.islam@teamworkbd.com';
    const API_CLIENT_FNAME = 'Nurul';
    const API_CLIENT_LNAME = 'Islam';
    const API_CLIENT_PWD = '1234567asd#';
    
    const G2M_API_CONSUMER_KEY = 'siAMWIM39mHOf75aeniIOfG23Cc8jqWT';
    const API_PORT = 443;
    
    private static $initiated = false;
    
    public static function init() {

        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    /**
     * Initializes WordPress hooks
     */
    private static function init_hooks() {
        self::$initiated = true;
    }

    public static function get_consumer_key($key_api_consumer_key = 'citrix_meeting_info') {
        return get_option($key_api_consumer_key);
    }

    public static function deactivate_key($key) {
        return TRUE;
    }

    public static function get_ip_address() {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }

    private static function get_user_agent() {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }

    private static function get_referer() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }

    // return a comma-separated list of role names for the given user
    public static function get_user_roles($user_id) {
        $roles = false;

        if (!class_exists('WP_User'))
            return false;

        if ($user_id > 0) {
            $comment_user = new WP_User($user_id);
            if (isset($comment_user->roles))
                $roles = join(',', $comment_user->roles);
        }

        if (is_multisite() && is_super_admin($user_id)) {
            if (empty($roles)) {
                $roles = 'super_admin';
            } else {
                $comment_user->roles[] = 'super_admin';
                $roles = join(',', $comment_user->roles);
            }
        }

        return $roles;
    }

    public static function _cmp_time($a, $b) {
        return $a['time'] > $b['time'] ? -1 : 1;
    }

    public static function _get_microtime() {
        $mtime = explode(' ', microtime());
        return $mtime[1] + $mtime[0];
    }

    /**
     * Make a POST request to the Citrix API.
     *
     * @param string $request The body of the request.
     * @param string $path The path for the request.
     * @param string $ip The specific IP address to hit.
     * @return array A two-member array consisting of the headers and the response body, both empty in the case of a failure.
     */
    public static function http_post($request, $path, $ip = null) {

        $citrix_ua = sprintf('WordPress/%s | Citrix/%s', $GLOBALS['wp_version'], constant('CITRIX__MEETING_VERSION'));
        $citrix_ua = apply_filters('citrix_ua', $citrix_ua);

        $content_length = strlen($request);

        $api_key = self::get_consumer_key();
        $host = self::API_HOST;

        if (!empty($api_key))
            $host = $api_key . '.' . $host;

        $http_host = $host;
        // use a specific IP if provided
        if ($ip && long2ip(ip2long($ip))) {
            $http_host = $ip;
        }

        $http_args = array(
            'body' => $request,
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
                'Host' => $host,
                'User-Agent' => $citrix_ua,
            ),
            'httpversion' => '1.0',
            'timeout' => 15
        );

        $citrix_url = $http_citrix_url = "http://{$http_host}/1.1/{$path}";

        /**
         * Try SSL first; if that fails, try without it and don't try it again for a while.
         */
        $ssl = $ssl_failed = false;

        // Check if SSL requests were disabled fewer than X hours ago.
        $ssl_disabled = get_option('citrix_ssl_disabled');

        if ($ssl_disabled && $ssl_disabled < ( time() - 60 * 60 * 24 )) { // 24 hours
            $ssl_disabled = false;
            delete_option('citrix_ssl_disabled');
        } else if ($ssl_disabled) {
            do_action('citrix_ssl_disabled');
        }

        if (!$ssl_disabled && function_exists('wp_http_supports') && ( $ssl = wp_http_supports(array('ssl')) )) {
            $citrix_url = set_url_scheme($citrix_url, 'https');

            do_action('citrix_https_request_pre');
        }

        $response = wp_remote_post($citrix_url, $http_args);

        Citrix::log(compact('citrix_url', 'http_args', 'response'));

        if ($ssl && is_wp_error($response)) {
            do_action('citrix_https_request_failure', $response);

            // Intermittent connection problems may cause the first HTTPS
            // request to fail and subsequent HTTP requests to succeed randomly.
            // Retry the HTTPS request once before disabling SSL for a time.
            $response = wp_remote_post($citrix_url, $http_args);

            Citrix::log(compact('citrix_url', 'http_args', 'response'));

            if (is_wp_error($response)) {
                $ssl_failed = true;

                do_action('citrix_https_request_failure', $response);

                do_action('citrix_http_request_pre');

                // Try the request again without SSL.
                $response = wp_remote_post($http_citrix_url, $http_args);

                Citrix::log(compact('http_citrix_url', 'http_args', 'response'));
            }
        }

        if (is_wp_error($response)) {
            do_action('citrix_request_failure', $response);

            return array('', '');
        }

        if ($ssl_failed) {
            // The request failed when using SSL but succeeded without it. Disable SSL for future requests.
            update_option('citrix_ssl_disabled', time());

            do_action('citrix_https_disabled');
        }

        $simplified_response = array($response['headers'], $response['body']);

        self::update_alert($simplified_response);

        return $simplified_response;
    }

    // given a response from an API call like check_key_status(), update the alert code options if an alert is present.
    private static function update_alert($response) {
        $code = $msg = null;
        if (isset($response[0]['x-citrix-alert-code'])) {
            $code = $response[0]['x-citrix-alert-code'];
            $msg = $response[0]['x-citrix-alert-msg'];
        }

        // only call update_option() if the value has changed
        if ($code != get_option('citrix_alert_code')) {
            if (!$code) {
                delete_option('citrix_alert_code');
                delete_option('citrix_alert_msg');
            } else {
                update_option('citrix_alert_code', $code);
                update_option('citrix_alert_msg', $msg);
            }
        }
    }

    public static function load_form_js() {
        // WP < 3.3 can't enqueue a script this late in the game and still have it appear in the footer.
        // Once we drop support for everything pre-3.3, this can change back to a single enqueue call.
        wp_register_script('citrix-form', CITRIX__PLUGIN_URL . '_inc/form.js', array(), CITRIX__MEETING_VERSION, true);
        add_action('wp_footer', array('Citrix', 'print_form_js'));
        add_action('admin_footer', array('Citrix', 'print_form_js'));
    }

    public static function print_form_js() {
        wp_print_scripts('citrix-form');
    }

    public static function inject_ak_js($fields) {
        echo '<p style="display: none;">';
        echo '<input type="hidden" id="ak_js" name="ak_js" value="' . mt_rand(0, 250) . '"/>';
        echo '</p>';
    }

    private static function bail_on_activation($message, $deactivate = true) {
        ?>
        <!doctype html>
        <html>
            <head>
                <meta charset="<?php bloginfo('charset'); ?>">
                <style>
                    * {
                        text-align: center;
                        margin: 0;
                        padding: 0;
                        font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
                    }
                    p {
                        margin-top: 1em;
                        font-size: 18px;
                    }
                </style>
            <body>
                <p><?php echo esc_html($message); ?></p>
            </body>
        </html>
        <?php
        if ($deactivate) {
            $plugins = get_option('active_plugins');
            $citrix = plugin_basename(CITRIX__PLUGIN_DIR . 'citrix.php');
            $update = false;
            foreach ($plugins as $i => $plugin) {
                if ($plugin === $citrix) {
                    $plugins[$i] = false;
                    $update = true;
                }
            }

            if ($update) {
                update_option('active_plugins', array_filter($plugins));
            }
        }
        exit;
    }

    public static function view($name, array $args = array()) {
        $args = apply_filters('critix_view_arguments', $args, $name);
        
        foreach ($args AS $key => $val) {
            ${$key} = $val;
        }

        load_plugin_textdomain('critix');

        $file = CITRIX__PLUGIN_DIR . 'views/' . $name . '.php';

        include( $file );
    }

    /**
     * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
     * @static
     */
    public static function plugin_activation() {
        if (version_compare($GLOBALS['wp_version'], CITRIX__MINIMUM_WP_VERSION, '<')) {
            load_plugin_textdomain('citrix');

            $message = '<strong>' . sprintf(esc_html__('Citrix %s requires WordPress %s or higher.', 'citrix'), CITRIX__MEETING_VERSION, CITRIX__MINIMUM_WP_VERSION) . '</strong> ' . sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Akismet plugin</a>.', 'citrix'), 'https://codex.wordpress.org/Upgrading_WordPress', 'http://wordpress.org/extend/plugins/citrix/download/');

            Citrix::bail_on_activation($message);
        }
    }

    /**
     * Removes all connection options
     * @static
     */
    public static function plugin_deactivation() {
        return self::deactivate_key(self::get_consumer_key());
    }

    /**
     * Essentially a copy of WP's build_query but one that doesn't expect pre-urlencoded values.
     *
     * @param array $args An array of key => value pairs
     * @return string A string ready for use as a URL query string.
     */
    public static function build_query($args) {
        return _http_build_query($args, '', '&');
    }

    /**
     * Log debugging info to the error log.
     *
     * Enabled when WP_DEBUG_LOG is enabled, but can be disabled via the citrix_debug_log filter.
     *
     * @param mixed $citrix_debug The data to log.
     */
    public static function log($citrix_debug) {
        if (apply_filters('citrix_debug_log', defined('WP_DEBUG_LOG') && WP_DEBUG_LOG)) {
            error_log(print_r(compact('citrix_debug'), true));
        }
    }


}
