<?php

class CreateSchoolAdmin {

    private static $initiated = false;
    
    public static function init() {

        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    private static function init_hooks() {
        self::$initiated = true;
    }
    
    public static function admin_init() {
        load_plugin_textdomain('create_school');
    }

}
