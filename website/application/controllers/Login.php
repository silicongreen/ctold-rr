<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    private function redirect_parent_url($url) 
    {
        $return = "<script>window.top.location.href = '" . $url . "'</script>";
        return $return;
    }  
    public function iframelogin() {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        $data['error'] = "";
        if ($this->form_validation->run() == FALSE) {
            $this->load_view('login', $data);
        } else {
            $username = $this->input->post("username");
            $password = $this->input->post("password");
            $this->db->set_dbprefix('');
            $where = "(username='" . $username . "' AND is_approved=1) and (is_deleted=0 OR parent=1)";
            $this->db->where($where);
            $users = $this->db->get("users")->row();
            if ($users) {
                $hashed_password = sha1($users->salt . $password);
                if ($hashed_password == $users->hashed_password) {
                    $this->db->where("linkable_id", $users->school_id);
                    $domain = $this->db->get("school_domains")->row();
                    if ($domain) {
                        $random = md5(rand());
                        $insert['auth_id'] = $random;
                        $insert['user_id'] = $users->id;
                        $insert['expire'] = date("Y-m-d H:i:s", strtotime("+1 Day"));
                        $this->db->insert("tds_user_auth", $insert);


                        $params = "?username=" . $username . "&password=" . $password . "&auth_id=" . $random . "&user_id=" . $users->id;
                        $url = "http://" . $domain->domain . $params;
                        $this->redirect_parent_url($url);
                    } else {
                        $data['error'] = "Wrong Username or Password";
                        $this->load_view_headless('login', $data);
                    }
                } else {
                    $data['error'] = "Wrong Username or Password";
                    $this->load_view_headless('login', $data);
                }
            } else {

                $data['error'] = "Wrong Username or Password";
                $this->load_view_headless('login', $data);
            }

            $this->db->set_dbprefix('');
        }
    }

    public function index() {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        $data['error'] = "";
        if ($this->form_validation->run() == FALSE) {
            $this->load_view('login', $data);
        } else {
            $username = $this->input->post("username");
            $password = $this->input->post("password");
            $this->db->set_dbprefix('');
            $where = "(username='" . $username . "' AND is_approved=1) and (is_deleted=0 OR parent=1)";
            $this->db->where($where);
            $users = $this->db->get("users")->row();
            if ($users) {
                $hashed_password = sha1($users->salt . $password);
                if ($hashed_password == $users->hashed_password) {
                    $this->db->where("linkable_id", $users->school_id);
                    $domain = $this->db->get("school_domains")->row();
                    if ($domain) {
                        $random = md5(rand());
                        $insert['auth_id'] = $random;
                        $insert['user_id'] = $users->id;
                        $insert['expire'] = date("Y-m-d H:i:s", strtotime("+1 Day"));
                        $this->db->insert("tds_user_auth", $insert);


                        $params = "?username=" . $username . "&password=" . $password . "&auth_id=" . $random . "&user_id=" . $users->id;
                        $url = "http://" . $domain->domain . $params;
                        header("Location: " . $url);
                    } else {
                        $data['error'] = "Wrong Username or Password";
                        $this->load_view('login', $data);
                    }
                } else {
                    $data['error'] = "Wrong Username or Password";
                    $this->load_view('login', $data);
                }
            } else {

                $data['error'] = "Wrong Username or Password";
                $this->load_view('login', $data);
            }

            $this->db->set_dbprefix('');
        }
    }

    public function ajax() {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        $data['error'] = "";
        if ($this->form_validation->run() == FALSE) {
            echo "0";
        } else {
            $username = $this->input->post("username");
            $password = $this->input->post("password");
            $this->db->set_dbprefix('');
            $where = "(username='" . $username . "' AND is_approved=1) and (is_deleted=0 OR parent=1)";
            $this->db->where($where);
            $users = $this->db->get("users")->row();
            if ($users) {
                $hashed_password = sha1($users->salt . $password);
                if ($hashed_password == $users->hashed_password) {
                    $this->db->where("linkable_id", $users->school_id);
                    $domain = $this->db->get("school_domains")->row();
                    if ($domain) {
                        $random = md5(rand());
                        $insert['auth_id'] = $random;
                        $insert['user_id'] = $users->id;
                        $insert['expire'] = date("Y-m-d H:i:s", strtotime("+1 Day"));
                        $this->db->insert("tds_user_auth", $insert);


                        $params = "?username=" . $username . "&password=" . $password . "&auth_id=" . $random . "&user_id=" . $users->id;
                        $url = "http://" . $domain->domain . $params;
                        echo $url;
                    } else {
                        $data['error'] = "Wrong Username or Password";
                        echo "0";
                    }
                } else {
                    $data['error'] = "Wrong Username or Password";
                    echo "0";
                }
            } else {

                $data['error'] = "Wrong Username or Password";
                echo "0";
            }

            $this->db->set_dbprefix('');
        }
    }

    public function reset_password() {

        $data = array();

        $token = $this->input->get("token");

        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('cnf_password', 'Confirm Password', 'trim|required|matches[password]');

        if (isset($_POST['token']) && !empty($_POST['token'])) {
            $token = $this->input->post("token");
        }

        if ($this->form_validation->run() !== FALSE) {

            $password = $this->input->post("password");
            $cnf_password = $this->input->post("cnf_password");

            $this->load->library('yii_api');
            $this->yii_api->init();
            $response = $this->yii_api->call__('post', 'user/resetpassword', array(
                'password' => $password,
                'token' => $token,
            ));
            
            if ( !empty($this->yii_api->_error_code) ) {
                $data['error'] = $this->yii_api->_error_message;
            } else {
                $this->session->set_flashdata('success', $response['status']['msg']);
                redirect('/login/reset_password_success');
            }
        }

        $data['token'] = $token;

        $this->load_view('reset_password', $data);
    }
    
    public function reset_password_success() {
        
        if ($this->session->flashdata('success')) {
            $this->load_view('reset_password_success');
        } else {
            redirect('/');
        }
        
    }

    //PRIVATE FUNCTION
    private function load_view($view_name, $data = array()) {
        $this->load->view('layout/header');
        $this->load->view($view_name, $data);
        $this->load->view('layout/footer_other');
    }
    private function load_view_headless($view_name, $data = array()) {
        $this->load->view('layout/headless/header');
        $this->load->view($view_name, $data);
        $this->load->view('layout/headless/footer');
    }

}
