<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Access {

    public $user;

    function __construct() {
        $this->CI = & get_instance();
        $auth = $this->CI->config->item('auth');
        $this->CI->load->helper('cookie');
        $this->CI->load->model('login_model', '', true);
        $this->CI->load->model('general_model', '', true);
        $this->general_model = & $this->CI->general_model;
        $this->login_model = & $this->CI->login_model;
    }

//=================================================================================		
    function login_superadmin($username, $password) {
        $result = $this->login_model->get_login_superadmin($username);
        if ($result) {
            $password = md5($password);
            if ($password === $result->password) {
                $data = array(
                    'id_user' => $result->id_user,
                    'hak_user' => 'superadmin',
                    'fullname' => $result->fullname,
                    'owner' => 'superadmin',
                    'status' => 'superadmin',
                    'foto'=>$result->foto,
                    'is_login'=>true,
                    'is_login_superadmin' => TRUE
                );
                $this->CI->session->set_userdata($data);
                return true;
            }
        }
        return false;
    }

    function is_login_superadmin() {
        return (($this->CI->session->userdata('is_login_superadmin')) ? true : false);
    }

    function logout_superadmin() {
        $data = array('id_user', 'hak_user', 'status', 'is_login_superadmin', 'owner','foto');
        $this->CI->session->unset_userdata($data);
    }

    //======================================================login gudang
    function login_gudang($username, $password) {
        $result = $this->login_model->get_login_gudang($username);
        if ($result) {
            $password = md5($password);
            if ($password === $result->password) {
                $data = array(
                    'id_user' => $result->id_usergudang,
                    'hak_user' => 'gudang',
                    'fullname' => $result->fullname,
                    'status' => $result->status,
                    'owner' => $result->id_gudang,
                    'foto'=>$result->foto,
                    'nama_gudang'=>$result->nama_gudang,
                    'foto_gudang'=>$result->foto_gudang,
                    'is_login'=>true,
                    'is_login_gudang' => TRUE
                );
                $this->CI->session->set_userdata($data);
                return true;
            }
        }
        return false;
    }

    function is_login_gudang() {
        return (($this->CI->session->userdata('is_login_gudang')) ? true : false);
    }

    function is_Gudang_accounting() {
        $status = $this->CI->session->userdata('status');
        return ($status == 'accounting') ? true : false;
    }

    function is_Gudang_admin() {
        $status = $this->CI->session->userdata('status');
        return ($status == 'admin') ? true : false;
    }

    function logout_gudang() {
        $data = array('id_user', 'status', 'is_login_gudang', 'owner', 'hak_user', 'fullname');
        $this->CI->session->unset_userdata($data);
    }

    function login_toko($username, $password) {
        $result = $this->login_model->get_login_toko($username);
        if ($result) {
            $password = md5($password);
            if ($password === $result->password) {
                $data = array(
                    'id_user' => $result->id_usertoko,
                    'hak_user' => 'toko',
                    'fullname' => $result->fullname,
                    'status' => $result->status,
                    'owner' => $result->id_toko,
                    'toko_segudang' => $result->id_gudang,
                    'nama_toko'=>$result->nama_toko,
                    'foto_toko'=>$result->foto_toko,
                    'foto'=>$result->foto,
                    'is_login'=>true,
                    'is_login_toko' => TRUE
                );
                $this->CI->session->set_userdata($data);
                return true;
            }
        }
        return false;
    }

    function is_login_toko() {
        return (($this->CI->session->userdata('is_login_toko')) ? true : false);
    }

    function is_kepala_toko() {
        $status = $this->CI->session->userdata('status');
        return ($status == 'kepala_toko') ? true : false;
    }

    function is_sales() {
        $status = $this->CI->session->userdata('status');
        return ($status == 'sales') ? true : false;
    }

    function is_kasir() {
        $status = $this->CI->session->userdata('status');
        return ($status == 'kasir') ? true : false;
    }

    function logout_toko() {
        $data = array('id_user', 'hak_user', 'status', 'is_login_toko', 'owner');
        $this->CI->session->unset_userdata($data);
    }

//==================================================================== akhir untuk dosen
}
