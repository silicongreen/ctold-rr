<?php

class CreateSchool {

    private static $initiated = false;

    public static function init() {

        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    private static function init_hooks() {
        self::$initiated = true;
        add_action('admin_post_school_admin_register', array('CreateSchool', 'school_admin_register'));
    }

    public static function plugin_activation() {
        if (version_compare($GLOBALS['wp_version'], CITRIX__MINIMUM_WP_VERSION, '<')) {
            load_plugin_textdomain('create_school');

            $message = '<strong>' . sprintf(esc_html__('Create School %s requires WordPress %s or higher.', 'citrix'), CITRIX__MEETING_VERSION, CITRIX__MINIMUM_WP_VERSION) . '</strong> ' . sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Akismet plugin</a>.', 'create_school'), '', '');

            self::$bail_on_activation($message);
        }
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
            $create_school = plugin_basename(CREATE_SCHOOL_PLUGIN_DIR . 'create_school.php');
            $update = false;
            foreach ($plugins as $i => $plugin) {
                if ($plugin === $create_school) {
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

    public static function plugin_deactivation() {
        return self::deactivate_key(self::get_consumer_key());
    }

    public static function deactivate_key($key) {
        return TRUE;
    }

    public static function get_consumer_key($key_api_consumer_key = 'create_school') {
        return get_option($key_api_consumer_key);
    }

    public static function school_admin_register() {
        
        unset($_POST['action']);
        
        var_dump($_POST);
        exit;
        status_header(200);
        die("Server received '{$_REQUEST['data']}' from your browser.");
        //request handlers should die() when they complete their task
    }

    private static function premiumSchoolPrecess($param) {
        
        $rp = $this->input->post('password');
        $p = $this->generate_passowrd_and_salt($rp);

        $user_data['nick_name'] = 1;
        $user_data['password'] = $p['password'];
        $user_data['salt'] = $p['salt'];

        $user_data['email'] = $this->input->post('email');
        $user_data['first_name'] = $this->input->post('first_name');
        $user_data['last_name'] = $this->input->post('last_name');
        $user_data['user_type'] = 3;

        if ($school_type == "paid") { //***** This condition is set by rlikhon********//
            $i_tmp_free_user_data_id = $this->tmp->create(array(
                'key' => 'paid_school_data',
                'value' => json_encode(array('admin_data' => $user_data))
            ));

            redirect("createschool/newschool/" . $school_type . '/' . $i_tmp_free_user_data_id);
        }
        
    }

}
