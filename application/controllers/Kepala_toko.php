<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Kepala_toko extends Kepala_toko_Controller {

    var $id_user;
    var $hak_user;
    var $owner;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper(array('form', 'url'));
        $this->load->library('grocery_CRUD');
        $this->load->model('general_model', 'general', true);
        $this->load->model('Master_model', 'master', true);
        $this->load->model('Transaksi_model', 'tr', true);
//        $this->session->set_userdata('id_user', 'dibsiss123');
        $this->id_user = $this->session->userdata('id_user');
        $this->hak_user = $this->session->userdata('hak_user');
        $this->owner = $this->session->userdata('owner');
        $this->load->helper('combo_helper');
    }


    function testing(){
        echo is_int(-1);
    }
    public function stokTersedia() {
        $output = $this->master->prosesStok('0', 'Stok Barang Tersedia');
        $this->template($output);
    }
    function retur_customer(){
        $output = $this->load->view('tr_retur_customer/h_retur_customer', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(), 'ket_header' => 'Transaksi Retur Customer'));
    }

    function index() {
        $this->tr_pengadaan();
    }

    public function template($output = null) {
        $this->load->view('template_kepalatoko', $output);
    }

    public function templatekosong($output = null) {
        $this->load->view('example', $output);
    }

    //transaksi
    function tr_pengadaan() {
        $this->load->model('Get_master_model', 'getMaster', true);
        $output = $this->load->view('tr_pengadaan/h_pengadaan', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(), 'ket_header' => 'Transaksi Pengadaan'));
    }

    function tr_penjualan(){
        $output = $this->load->view('tr_penjualan/h_penjualan', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(), 'ket_header' => 'Transaksi Penjualan'));
    }
    function tr_retur() {
        $this->load->model('Get_master_model', 'getMaster', true);
        $output = $this->load->view('tr_retur/h_retur', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(), 'ket_header' => 'Transaksi Retur'));
    }

    function tr_pengiriman() {
        $this->load->model('Get_master_model', 'getMaster', true);
        $output = $this->load->view('tr_pengiriman/h_pengiriman', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(), 'ket_header' => 'Transaksi Pengiriman'));
    }

    function tr_permintaan() {
        $this->load->model('Get_master_model', 'getMaster', true);
        $output = $this->load->view('tr_permintaan/h_permintaan', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(), 'ket_header' => 'Transaksi Permintaan'));
    }

    public function profile($idUser = null) {
        $output = $this->master->UserTokoSingle($idUser);
        if (empty($idUser)) {
            $output = (object) array('output' => 'Profile Kosong', 'js_files' => array(), 'css_files' => array(), 'ket_header' => 'Profile Tidak Ditemukan');
        }
        $this->template($output);
    }

    public function customer() {
        $output = $this->master->customer();
        $this->template($output);
    }
    public function Usertoko() {
        $output = $this->master->Usertoko();
        $this->template($output);
    }

}
