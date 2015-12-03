<?php

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;

class School {

    private $_ci;
    private $school_domain;
    private $_ar_toekn_params;
    private $school_code;
    private $_ar_data = array();
    private $_returned_school_info;
    private $_ar_tmp_free_user_data;
    private $_school_type;

    public function __construct() {
        $this->_ci = & get_instance();
        $this->_ci->load->database();
        $this->_ci->load->model('error_logs');
    }

    public function init($i_tmp_school_creation_data_id, $ar_tmp_free_user_data) {

        $this->_ci->load->model('tmp');
        $this->_ar_data = $param = $this->_ci->tmp->getData($i_tmp_school_creation_data_id);
        $this->_ci->tmp->delete($i_tmp_school_creation_data_id);

        $this->_ar_tmp_free_user_data = $ar_tmp_free_user_data;

        $ar_menu_data = $this->getMenuDataByPackageId($param['package'][0], 'menu_id');
        $this->_ar_data['menu_data'] = $ar_menu_data;
        $this->_school_type = ($param['package'][0] == 2) ? 'free' : 'paid';

        $this->school_domain = $this->_ar_data['school']['school_domains_attributes'][0]['domain'];
        $this->school_code = $this->_ar_data['school']['code'];
    }

    public function setCode($code) {
        $this->school_code = $code;
    }

    public function create() {

        if ($this->setToken()) {

            $this->_ci->load->library('plus_api');
            $this->_ci->plus_api->init();

            $userEndpoint = 'create_school';

            $this->_ar_data['token'] = $this->_ar_toekn_params;

            $this->_returned_school_info = $this->_ci->plus_api->call__('post', $userEndpoint, $this->_ar_data);

            if ($this->_returned_school_info !== FALSE) {
                $paid_user_data = $this->createDiaryUserForSchool();
                if (!empty($paid_user_data)) {

                    $this->mergePaidUserWithFreeUser($paid_user_data);

                    $data['school_type'] = $this->_school_type;
                    $data['returned_school_info'] = $this->_returned_school_info;
                    $data['paid_user_data'] = $paid_user_data;
                    $data['success'] = TRUE;
                    return $data;
                } else {
                    $data['ar_error']['message'] = 'Please try later. Too many user.';
                    return $data;
                }
            } else {
                $data['ar_data'] = $this->_ar_data;
                $data['ar_error']['code'] = $this->_ci->plus_api->_error_code;
                $data['ar_error']['message'] = $this->_ci->plus_api->_error_message;
                return $data;
            }
        } else {
            $data['ar_error']['message'] = 'Please try later. Server is too busy now.';
            return $data;
        }
    }

    public function createSubdomains($school_type = 'free') {

        require_once 'vendor/autoload.php';

        $this->_ci->load->config('create_school');
        $config = $this->_ci->config->config['create_school'];

        if ($school_type == 'paid') {
            $school_code = $this->school_code;
        } else {
            $school_code = $this->school_code . '.' . $school_type;
        }

        $url_param = array(
            'subdomain' => $school_code
        );

        $url = 'http://cp-api.champs21.com/cp3.php?' . http_build_query($url_param);
        $client = new Client();

        try {
            $response = $client->get($url);
            $res_code = $response->getStatusCode();

            if ($res_code == 200 || $res_code == 201 || $res_code == 502) {
                return TRUE;
            }
        } catch (Exception $ex) {
            return TRUE;
        }
    }

    public function getToken($length) {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max)];
        }
        return $token;
    }

    /*     * *## PRIVATE FUNCTIONS ##** */

    Private function setToken() {

        $now = date('Y-m-d H:i:s');

        $data = array(
//            'token' => '5855041fd706757f0ddca85bf642208ee7705f6249f30f2ac440fc52ca1daa1581257869fe682c5cdd2adb0543419bf292c129df5f2c694a4979fed9c59a486a',
            'token' => $this->createToken(),
            'expire_at' => date('Y-m-d H:i:s', strtotime($now . "+30 min")),
            'token_purpose' => 'create_school',
            'token_domain' => $this->school_domain,
            'created_at' => $now,
            'updated_at' => $now,
            'status' => 1,
        );

        $this->_ci->db->set_dbprefix('');
        return ($this->_ci->db->insert("tokens", $data)) ? $this->_ar_toekn_params = $data : FALSE;
    }

    Private function createToken() {
        return hash('sha512', $this->getToken(128));
    }

    Private function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 1)
            return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    private function createDiaryUserForSchool() {
        $now = date('Y-m-d H:i:s');
        $ar_free_user = $this->getFreeUserDataById($this->_ar_tmp_free_user_data['free_user_id']);
        $ar_free_user['rp'] = (!isset($this->_ar_tmp_free_user_data['rp'])) ? '123456' : $this->_ar_tmp_free_user_data['rp'];

        $paid_salt = $this->getToken(8);

        $data = array(
//            'username' => $this->school_code . '-dadmin',
            'username' => $ar_free_user['email'],
            'first_name' => $ar_free_user['first_name'],
            'last_name' => $ar_free_user['last_name'],
            'email' => $ar_free_user['email'],
            'admin' => 1,
            'student' => 0,
            'employee' => 0,
            'created_at' => $now,
            'updated_at' => $now,
            'parent' => 0,
            'is_first_login' => 0,
            'is_deleted' => 0,
            'school_id' => $this->_returned_school_info['school']['id'],
            'is_visible' => 1,
            'is_approved' => 1,
            'free_user_id' => $ar_free_user['id'],
            'hashed_password' => sha1($paid_salt . $ar_free_user['rp']),
            'salt' => $paid_salt,
        );

        $this->_ci->db->set_dbprefix('');
        $ar_res_data = array();
        if ($this->_ci->db->insert("users", $data)) {

            $paid_user_id = $this->_ci->db->insert_id();

            $emp_category_data = $this->createEmployeeCategory();
            $emp_position_data = $this->createEmployeePosition($emp_category_data);
            $emp_department_data = $this->createEmployeeDepartment();
            $emp_grade_data = $this->createEmployeeGrade();
            $emp_data = $this->createEmployee($paid_user_id, $emp_category_data, $emp_position_data, $emp_department_data, $emp_grade_data);

            $ar_res_data['paid_id'] = $paid_user_id;
            $ar_res_data['paid_username'] = $data['username'];
            $ar_res_data['paid_password'] = $ar_free_user['rp'];
            $ar_res_data['paid_school_id'] = $data['school_id'];
            $ar_res_data['paid_school_code'] = $this->school_code;

            $emp_palettes = $this->createPalettes($ar_res_data);
            $emp_palettes = $this->createMenuLinks($ar_res_data);
        } else {
            $_ar_errors['message'] = $this->_ci->db->_error_message();
            $_ar_errors['code'] = $this->_ci->db->_error_number();
            $_ar_errors['type'] = 'createDiaryUserForSchool';

            $this->_ci->error_logs->record($_ar_errors);
        }
        return $ar_res_data;
    }

    private function createMenuLinks($ar_paid_user_data = array()) {
        $this->_ci->db->set_dbprefix('');

        $this->_ci->db->select('user_menu_links.id, user_menu_links.menu_link_id');
        $this->_ci->db->from('user_menu_links');

        $this->_ci->db->where('user_menu_links.user_id', $ar_paid_user_data['paid_id']);
        $this->_ci->db->where('user_menu_links.school_id', $ar_paid_user_data['paid_school_id']);
        $data = $this->_ci->db->get()->result_array();

        if (empty($data)) {

            foreach ($this->_ar_data['menu_data'] as $menu_id) {
                $menu_data['menu_link_id'] = $menu_id;
                $menu_data['user_id'] = $ar_paid_user_data['paid_id'];

                $menu_data = array_merge($menu_data, $this->before_insert());
                unset($menu_data['status']);

                if (!$this->_ci->db->insert("user_menu_links", $menu_data)) {
                    $_ar_errors['message'] = $this->_ci->db->_error_message();
                    $_ar_errors['code'] = $this->_ci->db->_error_number();
                    $_ar_errors['type'] = 'createUserMenuLink';

                    $this->_ci->error_logs->record($_ar_errors);
                }
            }
            return TRUE;
        } else {
            return TRUE;
        }

        return FALSE;
    }

    private function createPalettes($ar_paid_user_data = array()) {
        $this->_ci->db->set_dbprefix('');

        $this->_ci->db->select('user_palettes.palette_id, user_palettes.position, user_palettes.column_number');
        $this->_ci->db->from('user_palettes');
        $this->_ci->db->join('users', 'user_palettes.user_id = users.id', 'inner');

        $this->_ci->db->where('users.id', $ar_paid_user_data['paid_id']);
        $this->_ci->db->where('user_palettes.school_id', $ar_paid_user_data['paid_school_id']);

        $data = $this->_ci->db->get()->result_array();

        if (empty($data)) {
            $this->_ci->db->select('user_palettes.palette_id, user_palettes.position, user_palettes.column_number');
            $this->_ci->db->from('user_palettes');
            $this->_ci->db->join('users', 'user_palettes.user_id = users.id', 'inner');
            $this->_ci->db->where('users.username', $ar_paid_user_data['paid_school_code'] . '-' . 'admin');
            $this->_ci->db->where('user_palettes.school_id', $ar_paid_user_data['paid_school_id']);

            $data = $this->_ci->db->get()->result_array();

            foreach ($data as $row) {
                $palette_data['palette_id'] = $row['palette_id'];
                $palette_data['position'] = $row['position'];
                $palette_data['column_number'] = $row['column_number'];
                $palette_data['user_id'] = $ar_paid_user_data['paid_id'];

                $palette_data = array_merge($palette_data, $this->before_insert());
                unset($palette_data['status']);

                if (!$this->_ci->db->insert("user_palettes", $palette_data)) {
                    $_ar_errors['message'] = $this->_ci->db->_error_message();
                    $_ar_errors['code'] = $this->_ci->db->_error_number();
                    $_ar_errors['type'] = 'createUserPalletes';

                    $this->_ci->error_logs->record($_ar_errors);
                }
            }
//            echo '<pre>';
//            var_dump($palette_data);
//            exit;
            return TRUE;
        } else {
            return TRUE;
        }

        return FALSE;
    }

    public function getFreeUserDataById($id) {
        $this->_ci->db->set_dbprefix('tds_');
        $this->_ci->db->select('*');
        $this->_ci->db->from('free_users');
        $this->_ci->db->where('id', $id);
        $data = $this->_ci->db->get()->result_array();
        return (!empty($data)) ? $data[0] : FALSE;
    }

    private function getMenuDataByPackageId($package_id = 2, $str_field = '*') {
        $this->_ci->db->set_dbprefix('');
        $this->_ci->db->select('*');
        $this->_ci->db->from('package_menus');
        $this->_ci->db->where('package_id', $package_id);
        $this->_ci->db->where('menu_id >', 0);
        $this->_ci->db->where('is_active', 1);
        $data = $this->_ci->db->get()->result_array();

        $ar_menus = array();
        if ($str_field != '*') {

            foreach ($data as $menu) {
                $ar_menus[] = $menu[$str_field];
            }
        } else {
            $ar_menus = $data;
        }

        return (!empty($ar_menus)) ? $ar_menus : FALSE;
    }

    private function mergePaidUserWithFreeUser($ar_paid_user_data) {
        $free_user_data = $this->getFreeUserDataById($this->_ar_tmp_free_user_data['free_user_id']);
        $this->_ci->db->set_dbprefix('tds_');
        $this->_ci->db->where('id', $free_user_data['id']);

        if (!$this->_ci->db->update('free_users', $ar_paid_user_data)) {
            $_ar_errors['message'] = $this->_ci->db->_error_message();
            $_ar_errors['code'] = $this->_ci->db->_error_number();
            $_ar_errors['type'] = 'updateFreeUser';

            $this->_ci->error_logs->record($_ar_errors);
            return FALSE;
        } else {
            return TRUE;
        }
    }

    private function createEmployeeCategory() {
        $this->_ci->db->set_dbprefix('');
        $this->_ci->db->select('*');
        $this->_ci->db->from('employee_categories');
        $this->_ci->db->where('name', 'System Admin');
        $this->_ci->db->where('prefix', 'Admin');
        $this->_ci->db->where('school_id', $this->_returned_school_info['school']['id']);
        $this->_ci->db->where('status', '1');
        $data = $this->_ci->db->get()->result_array();

        if (!empty($data)) {
            return $data[0];
        } else {

            $data['name'] = 'System Admin';
            $data['prefix'] = 'Admin';
            $data = array_merge($data, $this->before_insert());

            if ($this->_ci->db->insert("employee_categories", $data)) {
                $data['id'] = $this->_ci->db->insert_id();
                return $data;
            } else {
                $_ar_errors['message'] = $this->_ci->db->_error_message();
                $_ar_errors['code'] = $this->_ci->db->_error_number();
                $_ar_errors['type'] = 'createEmployeeCategory';

                $this->_ci->error_logs->record($_ar_errors);
                return FALSE;
            }
        }

        return FALSE;
    }

    private function createEmployeePosition($ar_category) {
        $this->_ci->db->set_dbprefix('');
        $this->_ci->db->select('*');
        $this->_ci->db->from('employee_positions');
        $this->_ci->db->where('name', 'System Admin');
        $this->_ci->db->where('employee_category_id', $ar_category['id']);
        $this->_ci->db->where('status', '1');
        $this->_ci->db->where('school_id', $this->_returned_school_info['school']['id']);
        $data = $this->_ci->db->get()->result_array();

        if (!empty($data)) {
            return $data[0];
        } else {

            $data['name'] = 'System Admin';
            $data['employee_category_id'] = $ar_category['id'];
            $data = array_merge($data, $this->before_insert());

            if ($this->_ci->db->insert("employee_positions", $data)) {
                $data['id'] = $this->_ci->db->insert_id();
                return $data;
            } else {
                $_ar_errors['message'] = $this->_ci->db->_error_message();
                $_ar_errors['code'] = $this->_ci->db->_error_number();
                $_ar_errors['type'] = 'createEmployeePosition';

                $this->_ci->error_logs->record($_ar_errors);
                return FALSE;
            }
        }
        return FALSE;
    }

    private function createEmployeeDepartment() {
        $this->_ci->db->set_dbprefix('');
        $this->_ci->db->select('*');
        $this->_ci->db->from('employee_departments');
        $this->_ci->db->where('name', 'System Admin');
        $this->_ci->db->where('code', 'Admin');
        $this->_ci->db->where('status', '1');
        $this->_ci->db->where('school_id', $this->_returned_school_info['school']['id']);
        $data = $this->_ci->db->get()->result_array();

        if (!empty($data)) {
            return $data[0];
        } else {

            $data['name'] = 'System Admin';
            $data['code'] = 'Admin';
            $data = array_merge($data, $this->before_insert());

            if ($this->_ci->db->insert("employee_departments", $data)) {
                $data['id'] = $this->_ci->db->insert_id();
                return $data;
            } else {
                $_ar_errors['message'] = $this->_ci->db->_error_message();
                $_ar_errors['code'] = $this->_ci->db->_error_number();
                $_ar_errors['type'] = 'createEmployeeDepartment';

                $this->_ci->error_logs->record($_ar_errors);
                return FALSE;
            }
        }
        return FALSE;
    }

    private function createEmployeeGrade() {
        $this->_ci->db->set_dbprefix('');
        $this->_ci->db->select('*');
        $this->_ci->db->from('employee_grades');
        $this->_ci->db->where('name', 'System Admin');
        $this->_ci->db->where('priority', '0');
        $this->_ci->db->where('max_hours_day', NULL);
        $this->_ci->db->where('max_hours_week', NULL);
        $this->_ci->db->where('status', '1');
        $this->_ci->db->where('school_id', $this->_returned_school_info['school']['id']);
        $data = $this->_ci->db->get()->result_array();

        if (!empty($data)) {
            return $data[0];
        } else {

            $data['name'] = 'System Admin';
            $data['priority'] = '0';
            $data['max_hours_day'] = NULL;
            $data['max_hours_week'] = NULL;
            $data = array_merge($data, $this->before_insert());

            if ($this->_ci->db->insert("employee_grades", $data)) {
                $data['id'] = $this->_ci->db->insert_id();
                return $data;
            } else {
                $_ar_errors['message'] = $this->_ci->db->_error_message();
                $_ar_errors['code'] = $this->_ci->db->_error_number();
                $_ar_errors['type'] = 'createEmployeeGrade';

                $this->_ci->error_logs->record($_ar_errors);
                return FALSE;
            }
        }
        return FALSE;
    }

    private function createEmployee($paid_user_id, $ar_emp_category, $ar_emp_position, $ar_emp_department, $ar_emp_grade) {
        $now = date('Y-m-d H:i:s');

        $ar_free_user = $this->getFreeUserDataById($this->_ar_tmp_free_user_data['free_user_id']);

        $ar_emp_data['employee_number'] = $ar_free_user['email'];
        $ar_emp_data['joining_date'] = $now;
        $ar_emp_data['first_name'] = $ar_free_user['first_name'];
        $ar_emp_data['last_name'] = $ar_free_user['last_name'];
        $ar_emp_data['employee_department_id'] = $ar_emp_department['id'];
        $ar_emp_data['employee_grade_id'] = $ar_emp_grade['id'];
        $ar_emp_data['employee_position_id'] = $ar_emp_position['id'];
        $ar_emp_data['employee_category_id'] = $ar_emp_category['id'];
        $ar_emp_data['nationality_id'] = 14;
        $ar_emp_data['date_of_birth'] = date('Y-m-d H:i:s', strtotime($now . "-365 days"));
        $ar_emp_data['email'] = $ar_free_user['email'];
        $ar_emp_data['gender'] = 'm';
        $ar_emp_data['status'] = 1;
        $ar_emp_data['user_id'] = $paid_user_id;
        $ar_emp_data['school_id'] = $this->_returned_school_info['school']['id'];

        $this->_ci->db->set_dbprefix('');
        $this->_ci->db->select('*');
        $this->_ci->db->from('employees');

        foreach ($ar_emp_data as $key => $value) {
            $this->_ci->db->where($key, $value);
        }

        $data = $this->_ci->db->get()->result_array();

        if (!empty($data)) {
            return $data[0];
        } else {

            unset($ar_emp_data['status']);
            $ar_emp_data = array_merge($ar_emp_data, $this->before_insert());
            if ($this->_ci->db->insert("employees", $ar_emp_data)) {
                $data['id'] = $this->_ci->db->insert_id();
                return $data;
            } else {
                $_ar_errors['message'] = $this->_ci->db->_error_message();
                $_ar_errors['code'] = $this->_ci->db->_error_number();
                $_ar_errors['type'] = 'createEmployee';

                $this->_ci->error_logs->record($_ar_errors);
                return FALSE;
            }
        }
        return FALSE;
    }

    private function before_insert($b_school = TRUE) {
        $now = date('Y-m-d H:i:s');
        $data = array(
            'created_at' => $now,
            'updated_at' => $now,
            'status' => 1
        );

        if ($b_school) {
            $data['school_id'] = $this->_returned_school_info['school']['id'];
        }

        return $data;
    }

}
