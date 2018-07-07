<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Superadmin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('general_model', 'general', true);
        $this->load->model('login_model', 'login', true);
    }

    function cek_captcha() {
        $input = $this->input->post('security_code');
        $sessionku = $this->session->userdata('mycaptcha');
        if ($input == $sessionku) {
            return true;
        } else {
            $this->form_validation->set_message('cek_captcha', 'Captcha Tidak Sesuai');
            return false;
        }
    }

    public function index() {
        $this->masuk();
    }

    function masuk() {
        if ($this->access->is_login_superadmin()) {
            redirect('superadmin/stokTersedia', 'refresh');
        } else if ($this->access->is_login_gudang()) {
            $this->login->cekStatus();
        } else if ($this->access->is_login_toko()) {
            $this->login->cekStatus();
        } else {
            $this->form_validation->set_rules('username', 'username', 'trim|required|strip_tags');
            $this->form_validation->set_rules('password', 'password', 'trim|required|strip_tags');
            $this->form_validation->set_rules('security_code', 'Captcha', 'required|callback_cek_captcha');
            $this->form_validation->set_rules('token', 'token', 'callback_check_masuk');

            if ($this->form_validation->run() == false) {

                $this->load->helper('captcha');
                $vals = array(
                    'img_path' => './assets/captcha/',
                    'img_url' => base_url() . 'assets/captcha/',
                    'img_width' => 271,
                    'img_height' => 60,
                    'font_size' => 27,
                    'font_path' => FCPATH . 'assets/captcha/font/times.ttf',
                    'expiration' => 7200
                );
                $cap = create_captcha($vals);
                $this->session->set_userdata('mycaptcha', $cap['word']);
                $data = array('image' => $cap['image']);
                $this->load->view('login/superadmin', $data);
            } else {
                $this->login->cekStatus();
            }
        }
    }

    function keluar_superadmin() {
        $this->access->logout_superadmin();
        $this->masuk();
    }

    function keluar_gudangadmin() {
        $this->access->logout_gudang();
        $this->masuk();
    }

    function keluar_gudangaccounting() {
        $this->access->logout_gudang();
        $this->masuk();
    }

    function keluar_toko() {
        $this->access->logout_toko();
        $this->masuk();
    }

    function check_masuk() {
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', TRUE);
        $status = $this->input->post('status', TRUE);

        if ($status == 'superadmin') {
            $login = $this->access->login_superadmin($username, $password);
        } else if ($status == 'gudang') {
            $login = $this->access->login_gudang($username, $password);
        } else {
            $login = $this->access->login_toko($username, $password);
        }

        if ($login) {
            return true;
        } else {
            $this->form_validation->set_message('check_masuk', 'Username Atau Password Anda Salah');
            return false;
        }
    }

    function login() {
        $this->load->view('login/superadmin2');
    }

}
