<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Gudang_accounting extends Gudang_accounting_Controller {

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
//        $this->session->set_userdata('id_user', 'nasrul123');
        $this->id_user = $this->session->userdata('id_user');
        $this->hak_user = $this->session->userdata('hak_user');
        $this->owner = $this->session->userdata('owner');
        $this->load->helper('combo_helper');
    }
    function index(){
        $this->tr_pengadaan();
    }
     //====================================function query
    function inputReturHarga() {
        $output = $this->load->view('tr_input_retur_harga/h_retur_harga', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(), 'ket_header' => 'Transaksi Pengembalian Retur Harga'));
    }
    function inputReturRusak() {
        $output = $this->load->view('tr_input_retur_rusak/h_retur_rusak', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(), 'ket_header' => 'Transaksi Pengembalian Retur Rusak'));
    }

    public function template($output = null) {
        $this->load->view('template_gudangaccounting', $output);
    }

    public function templatekosong($output = null) {
        $this->load->view('example', $output);
    }
    //transaksi
    function tr_pengadaan() {
        $this->load->model('Get_master_model', 'getMaster', true);
        $output = $this->load->view('tr_pengadaan/h_pengadaan', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(),'ket_header'=>'Transaksi Pengadaan'));
    }
    function tr_retur() {
        $this->load->model('Get_master_model', 'getMaster', true);
        $output = $this->load->view('tr_retur/h_retur', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(),'ket_header'=>'Transaksi Retur'));
    }
    function tr_pengiriman() {
        $this->load->model('Get_master_model', 'getMaster', true);
        $output = $this->load->view('tr_pengiriman/h_pengiriman', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(),'ket_header'=>'Transaksi Pengiriman'));
    }
    
    function tr_permintaan() {
        $this->load->model('Get_master_model', 'getMaster', true);
        $output = $this->load->view('tr_permintaan/h_permintaan', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(),'ket_header'=>'Transaksi Permintaan'));
    }
    
     public function profile($idUser=null) {
        $output = $this->master->UserGudangSingle($idUser);
        if(empty($idUser)){
            $output = (object) array('output' => 'Profile Kosong', 'js_files' => array(), 'css_files' => array(),'ket_header'=>'Profile Tidak Ditemukan');
        }
        $this->template($output);
    }
    public function stokTersedia(){
        $output=$this->master->prosesStok('0','Stok Barang Tersedia');
        $this->template($output);
    }
    function menclek(){
        $this->general->testpre($this->session->userdata());
    }
     public function Usergudang() {
        $output = $this->master->Usergudang();
        $this->template($output);
    }

    public function Usertoko() {
        $output = $this->master->Usertoko();
        $this->template($output);
    }
    public function merk() {
        $output = $this->master->merk();
        $this->template($output);
    }
    public function mst_retur() {
        $output = $this->master->mst_retur();
        $this->template($output);
    }
    public function category() {
        $output = $this->master->category();
        $this->template($output);
    }
    function jenis_non_fisik() {
        $output = $this->master->jenis_non_fisik();
        $this->template($output);
    }
    public function barang() {
        $output = $this->master->barang();
        $this->template($output);
    }

    public function supliyer() {
        $output = $this->master->supliyer();
        $this->template($output);
    }
     public function Gtoko($idgudang = null) {
        $output = $this->master->Gtoko($idgudang);
        $this->templatekosong($output);
    }

    public function toko() {
        $output = $this->master->toko();
        $this->template($output);
    }
     public function gudang() {
        $output = $this->master->gudang();
        $this->template($output);
    }

   

}
