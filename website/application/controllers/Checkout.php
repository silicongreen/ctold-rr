<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Checkout extends CI_Controller {

    public $icnt = 0;

    public function __construct() {
        parent::__construct();
        $this->load->model('tmp');
    }

    public function index() {
        $this->load_view('checkout/index');
    }

    public function have_code() {

        $data = array();
        if (isset($_POST) && !empty($_POST['payment_code'])) {

            $payment_code = $this->input->post('payment_code');
            $payment_code_data = $this->checkPaymentCode(array('code' => $payment_code, 'status' => 1));

            if ($payment_code_data === 502 || $payment_code_data !== FALSE) {

                if ($this->validatePaymentCode($payment_code_data)) {
                    $data = $this->paidSchoolProcess();

                    if (isset($data['success']) && $data['success'] === TRUE) {

                        $data['school_type'] = 'paid';
                        $school_id = $data['returned_school_info']['school']['id'];

                        $this->updatePaymentCode(array(
                            'code' => $payment_code,
                            'school_id' => $school_id
                        ));

                        $data['success'] = TRUE;
                        $data['message'] = '<strong>Congratulation!!!</strong> School Successfuly Created.';

                        $this->load_view('success', $data);
                    } else {
                        $data['message'] = '<strong>Sorry!!!</strong> Something went wrong.';
                        $data['error'] = TRUE;
                        $this->load_view('checkout/code', $data);
                    }
                } else {
                    $data['message'] = '<strong>Sorry!!!</strong> Purchase Code Expired.';
                    $data['error'] = TRUE;
                }
            } else {
                $data['message'] = '<strong>Sorry!!!</strong> Invalid Purchase Code.';
                $data['error'] = TRUE;
            }
        }

        $this->load_view('checkout/code', $data);
    }

    public function purchase_code() {
        $now = date('Y-m-d H:i:s');
        $this->load->library('school');
        $payment_code = $this->getPaymentCode(12);

        $data['code'] = $payment_code;
        $data['school_id'] = 0;
        $data['expire_at'] = date('Y-m-d H:i:s', strtotime($now . "+3 days"));

        $this->db->set_dbprefix('');

        $data = array_merge($data, $this->before_insert());
        $this->db->insert("school_codes", $data);

        echo 'Last Generated COde is: ' . $data['code'];
        exit;
    }
    
    public function payment($i_tmp_school_creation_data_id, $i_tmp_free_user_data_id) {
        $payment_gateway = 'stripe';
        $paid = true;
        $charges_ar_to_load = ($paid) ? 'gold' : 'free';
        $b_all_done = false;
        $message = '';
        $school_type = 'paid';
        $error = false;
        
        $this->load->config("payment");
        $a_paymentModules = $this->config->config['PaymentModules'];
        $s_enable_modules = "";
        foreach($a_paymentModules as $k => $v)
        {
            if ( $v )
            {
                $payment_gateway = $k;
                break;
            }
        }
        
        switch ($payment_gateway)
        {
            case 'stripe': 
                require_once( APPPATH . 'libraries/stripe/vendor/autoload.php');
                $stripe = array(
                    "secret_key" => "sk_test_002dJofp6PF2BC6B0lMkDb0j",
                    "publishable_key" => "pk_test_9TfEoBZhdGU1iBx521GMjild"
                );
                
                \Stripe\Stripe::setApiKey($stripe['secret_key']);
                
                $data['publishable_key'] = $stripe['publishable_key'];
                
                if (isset($_POST) && !empty($_POST['stripeToken'])) {

                    if (isset($_POST['i_tmp_free_user_data_id']) && !empty($_POST['i_tmp_free_user_data_id'])) {
                        $i_tmp_free_user_data_id = $_POST['i_tmp_free_user_data_id'];
                    }

                    if (isset($_POST['i_tmp_school_creation_data_id']) && !empty($_POST['i_tmp_school_creation_data_id'])) {
                        $i_tmp_school_creation_data_id = $_POST['i_tmp_school_creation_data_id'];
                    }

                    if (isset($_POST['school_type']) && !empty($_POST['school_type'])) {
                        $school_type = $_POST['school_type'];
                    }

                    $token = $this->input->post('stripeToken');
                    $email = $this->input->post('email');

                    $this->load->config('checkout');
                    $charges_config = $this->config->config['charges'];

                    $charges = $charges_config[$payment_gateway][$charges_ar_to_load];

                    $i_customer_id = 0;
                    $customer = $this->check_customer(array('email' => $email));
                    $b_new_customer = FALSE;
                    $b_card_accepted = FALSE;
            
                    if (!$customer) {

                        try {
                            $customer = \Stripe\Customer::create(array(
                                    'email' => $email,
                                    'card' => $token
                            ));
                            $b_card_accepted = TRUE;
                        } catch (\Stripe\Error\Card $ex) {
                            $message = '<strong>Sorry!!!</strong> ' . $ex->getJsonBody()['error']['message'];
                            $error = TRUE;
                        }

                        if ($b_card_accepted) {
                            $customer_id = $customer->id;
                            $b_new_customer = TRUE;
                        }
                    } 
                    else 
                    {
                        $b_card_accepted = TRUE;
                        $i_customer_id = $customer->id;
                        $customer_id = $customer->customer_id;
                    }

                    if (isset($customer_id) && !empty($customer_id) && $b_card_accepted) 
                    {
                        if ($b_new_customer) 
                        {
                            $i_customer_id = $this->save_customer($customer);
                        }
                        
                        $ar_tmp_free_user_data = $this->tmp->getData($i_tmp_free_user_data_id);
                        $i_free_user_id = $ar_tmp_free_user_data['free_user_id'];

                        try {
                            $charge = \Stripe\Charge::create(array(
                                'customer' => $customer_id,
                                'amount' => (int) $charges['price'] * 100,
                                'currency' => ($charges_config['override_currency']) ?
                                        $charges['currency'] : $charges_config['default_currency'],
                            ));
                    
                            if ($charge->status == 'succeeded') 
                            {
                                $data = $this->paidSchoolProcess($i_tmp_school_creation_data_id, $i_tmp_free_user_data_id);

                                $data['i_tmp_school_created_data_id'] = $this->tmp->create(array(
                                    'key' => 'school_created_data',
                                    'value' => json_encode($data)
                                ));

                                $transaction_id = $this->save_transaction($charge, $i_customer_id);
                                $this->save_school_transaction($data['i_tmp_school_created_data_id'], $transaction_id, $i_tmp_free_user_data_id);

                                if (isset($data['success']) && $data['success'] === TRUE) 
                                {
                                    $b_all_done = true;
                                }

                                redirect('/createschool/success/' . $school_type . '/' . $data['i_tmp_school_created_data_id'] . '/' . $i_free_user_id);
                            } 
                            else 
                            {
                                $message = '<strong>Sorry!!!</strong> Payment failed.';
                                $error = TRUE;
                            }
                        } catch (\Stripe\Error\Card $ex) {
                            $message = '<strong>Sorry!!!</strong> ' . $ex->getJsonBody()['error']['message'];
                            $error = TRUE;
                        }
                    }
                }
                break;
            case '2Checkout':
                require_once( APPPATH . 'libraries/2Checkout/Twocheckout.php');
                $data['public_key'] = $this->config->config['PaymentParams']['2Checkout']['public_key'];
                $data['seller_id'] = $this->config->config['PaymentParams']['2Checkout']['sellerID'];
                $data['payment_type'] = ( $this->config->config['PaymentParams']['2Checkout']['SandBox'] ) ? 'sandbox'  : 'production';
                if (isset($_POST['token_request']) && $_POST['token_request'] != "0") 
                {
                    $ar_tmp_school_creation_raw_data = $this->tmp->getRawData($i_tmp_school_creation_data_id);
                    $a_tmp_school_data = json_decode($ar_tmp_school_creation_raw_data['value'], true);
                    $no_of_student = $a_tmp_school_data['school']['number_of_student'];
                    $unit_price = $this->config->config['PaymentRules']['unit_price'];
                    Twocheckout::privateKey($this->config->config['PaymentParams']['2Checkout']['private_key']);
                    Twocheckout::sellerId($this->config->config['PaymentParams']['2Checkout']['sellerID']);
                    Twocheckout::verifySSL(false);  // this is set to true by default

                    // To use your sandbox account set sandbox to true
                    Twocheckout::sandbox(true);
                    try {
                        $a_request = array(
                            "sellerId" => $this->config->config['PaymentParams']['2Checkout']['sellerID'],
                            "merchantOrderId" => uniqid(),
                            "token" => $_POST['token_request'],
                            "currency" => 'USD',
                            "total" => $no_of_student * $unit_price,
                            'recurrence'    => $this->config->config['PaymentRules']['recurrence_unit'] . " " . $this->config->config['PaymentRules']['recurrence_type']
                        );
                        print_r($a_request);
                        exit;
                        $o_charge = Twocheckout_Charge::auth($a_request);
                        print_r($o_charge);
                        exit;
                        if ( $o_charge['response']['responseCode'] == 'APPROVED' )
                        {
                            $data = $this->paidSchoolProcess($i_tmp_school_creation_data_id, $i_tmp_free_user_data_id);

                            $data['i_tmp_school_created_data_id'] = $this->tmp->create(array(
                                'key' => 'school_created_data',
                                'value' => json_encode($data)
                            ));

                            $transaction_id = $this->save_transaction($charge, $i_customer_id);
                            $this->save_school_transaction($data['i_tmp_school_created_data_id'], $transaction_id, $i_tmp_free_user_data_id);

                            if (isset($data['success']) && $data['success'] === TRUE) 
                            {
                                $b_all_done = true;
                            }

                            redirect('/createschool/success/' . $school_type . '/' . $data['i_tmp_school_created_data_id'] . '/' . $i_free_user_id);
                        }
                        
                    } catch (Twocheckout_Error $e) {
                        $message = '<strong>Sorry!!!</strong> ' . $e->getMessage();
                        $error = TRUE;
                    }
                    
                }
                break;
                
        }
        $data['school_type'] = $school_type;
        $data['i_tmp_free_user_data_id'] = $i_tmp_free_user_data_id;
        $data['i_tmp_school_creation_data_id'] = $i_tmp_school_creation_data_id;
        $data['error'] = $error;
        $data['message'] = $message;
        
        $this->load_view('checkout/' . $payment_gateway . '/_form', $data);
    }

    //PRIVATE FUNCTION
    private function load_view($view_name, $data = array()) {
        $this->load->view('layout/header');
        $this->load->view($view_name, $data);
        $this->load->view('layout/footer_other');
    }

    private function check_customer($ar_params, $gateway = 'stripe') {
        $fnc_name = 'check_' . $gateway . '_customer';
        return $this->$fnc_name($ar_params);
    }

    private function check_stripe_customer($ar_params = array()) {
        $this->db->select('*');
        if (!empty($ar_params['id'])) {
            $this->db->where('customer_id', $ar_params['id']);
        }
        if (!empty($ar_params['email'])) {
            $this->db->where('customer_email', $ar_params['email']);
        }
        $this->db->from('stripe_customer');
        $data = $this->db->get()->row();
        return (!empty($data)) ? $data : false;
    }

    private function save_customer($obj_to_save, $gateway = 'stripe') {
        $fnc_name = 'save_' . $gateway . '_customer';
        return $this->$fnc_name($obj_to_save);
    }

    private function save_stripe_customer($obj_to_save) {

        $data = array(
            'customer_id' => $obj_to_save->id,
            'customer_email' => $obj_to_save->email
        );

        $data = array_merge($data, $this->before_insert());
        $this->db->insert('stripe_customer', $data);

        return $this->db->insert_id();
    }

    private function save_transaction($obj_to_save, $i_customer_id = 0) {

        $data = array(
            'customer_id' => $i_customer_id,
            'amount' => number_format(($obj_to_save->amount / 100), 2),
            'type' => 1,
            'payment_gateway' => 1,
            'gateway_transaction_id' => $obj_to_save->id,
        );

        $data = array_merge($data, $this->before_insert());

        $this->db->insert('tds_transaction', $data);

        return $this->db->insert_id();
    }

    private function save_school_transaction($i_tmp_school_created_data_id, $transaction_id, $i_tmp_free_user_data_id) {

        $this->load->library('school');

        $ar_temp_free_user_data = $this->tmp->getData($i_tmp_free_user_data_id);
        $free_user_data = $this->school->getFreeUserDataById($ar_temp_free_user_data['free_user_id']);

        $ar_school_created_data = $this->tmp->getData($i_tmp_school_created_data_id);
        $paid_user_data = $ar_school_created_data['paid_user_data'];

        $data = array(
            'paid_school_id' => $ar_school_created_data['returned_school_info']['school']['id'],
            'transaction_id' => $transaction_id,
            'free_user_id' => $free_user_data['id'],
            'paid_user_id' => $paid_user_data['paid_id'],
        );

        $this->db->set_dbprefix('tds_');
        return $this->db->insert('school_transaction', $data);
    }

    private function before_insert() {
        $now = date('Y-m-d H:i:s');
        return array(
            'created_date' => $now,
            'update_date' => $now,
            'status' => 1
        );
    }

    private function paidSchoolProcess($i_tmp_school_creation_data_id, $i_tmp_free_user_data_id) {

        $this->load->config('create_school');
        $config = $this->config->config['create_school'];

        $this->load->library('school');

        $ar_tmp_free_user_data = $this->tmp->getData($i_tmp_free_user_data_id);
        $ar_tmp_school_creation_raw_data = $this->tmp->getRawData($i_tmp_school_creation_data_id);

        $ar_data = json_decode($ar_tmp_school_creation_raw_data['value'], true);

        $school_domain = $ar_data['school']['code'] . '.' . $config['main_domain'];
        $ar_data['school']['school_domains_attributes'][0]['domain'] = $school_domain;
        $ar_data['package'] = [1];

        $ar_tmp_school_creation_data_update = $this->tmp->update($i_tmp_school_creation_data_id, array(
            'key' => 'school_creation_data',
            'value' => json_encode($ar_data)
        ));

        $this->school->init($i_tmp_school_creation_data_id, $ar_tmp_free_user_data);
        return $this->school->create();
    }

    private function getPaymentCode($length) {
        $token = "";
        $codeAlphabet = '';
//        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
//        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max)];
        }

        $p_code_status = $this->checkPaymentCode(array('code' => $token));

        if ($p_code_status === 502 || $p_code_status !== FALSE) {
            if ($this->icnt == 5) {
                return FALSE;
            } else {
                $this->icnt + 1;
                $this->getPaymentCode(12);
            }
        } else {
            return $token;
        }
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

    private function checkPaymentCode($params = array()) {
        $this->db->set_dbprefix('');

        if (!isset($params['code']) && empty($params['code'])) {
            return 502;
        }

        foreach ($params as $key => $value) {
            $this->db->where($key, $params[$key]);
        }

        $this->db->from('school_codes');
        $data = $this->db->get()->result_array();

        return ( empty($data) || empty($data[0]) ) ? FALSE : $data[0];
        exit;
    }

    private function updatePaymentCode($ar_code) {
        $this->db->set_dbprefix('');

        if (!isset($ar_code['code']) && empty($ar_code['code'])) {
            return FALSE;
        }

        $data['status'] = 0;
        $data['school_id'] = $ar_code['school_id'];
        $this->db->where('code', $ar_code['code']);

        return $this->db->update('school_codes', $data);
    }

    private function validatePaymentCode($ar_code) {
        $now = time();
        $payment_code_expire = strtotime($ar_code['expire_at']);

        return ($now <= $payment_code_expire);
    }

}
