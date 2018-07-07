<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->model('general_model', 'general', true);
    }

    function get_login_gudang($username) {
        $this->load->database();
        $query=$this->db->select("a.*,b.nama as nama_gudang, b.foto as foto_gudang")->limit(1)->join("mst_gudang b","a.id_gudang=b.id_gudang")->get_where('user_gudang a',array('a.username'=>$username));
        $this->db->limit(1);
        return ($query->num_rows() > 0) ? $query->row() : false;
    }

    function get_login_toko($username) {
        $this->load->database();
        $query=$this->db->select("a.*,b.id_gudang,b.nama as nama_toko, b.foto as foto_toko")->limit(1)->join("mst_toko b","a.id_toko=b.id_toko")->get_where('user_toko a',array('a.username'=>$username));
        return ($query->num_rows() > 0) ? $query->row() : false;
    }

    function get_login_superadmin($username) {
        $this->load->database();
        $this->db->where('username', $username);
        $this->db->limit(1);
        $query = $this->db->get('user');
        return ($query->num_rows() > 0) ? $query->row() : false;
    }

    function cekStatus() {
        //cek session hak akses dan status

        $cek = $this->session->userdata('hak_user');
        //cek gudang, superadmin, toko
        if ($cek == 'superadmin') {
            redirect('superadmin', 'refresh');
        } else if ($cek == 'gudang') {
            $status = $this->session->userdata('status');
            if ($status == 'accounting') {
                redirect('gudang_accounting/stokTersedia', 'refresh');
            } else {
                redirect('gudang_admin/stokTersedia', 'refresh');
            }
        } else {
            $status = $this->session->userdata('status');
            if ($status == 'kepala_toko') {
                redirect('kepala_toko/stokTersedia', 'refresh');
            } else if ($status == 'kasir') {
                redirect('kasir/stokTersedia', 'refresh');
            } else {
                redirect('sales/stokTersedia', 'refresh');
            }
        }
    }

}
