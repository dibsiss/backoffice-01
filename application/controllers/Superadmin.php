<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Superadmin extends Superadmin_Controller {

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
        $this->load->model('Laporan_model', 'lp', true);
        $this->id_user = $this->session->userdata('id_user');
        $this->hak_user = $this->session->userdata('hak_user');
        $this->owner = $this->session->userdata('owner');
        $this->load->helper('combo_helper');
    }

	function changePrintBarcode($id=null){
		if(($id==0) || ($id==1)){
			$this->lp->changePrintBarcode($id);
		}
	}
	
		function history_awal(){
		$temp = $this->load->view("superadmin/header_history", "", true);
		$output = array('output' => $temp, 'ket_header' => 'Insert History Awal');
        $this->tr->templateNotif(@$output);
	}
	function prosesMutasiAwal(){
		echo $this->tr->prosesMutasiAwal();
	}
	
	public function mst_bank()
	{
		$output = $this->grocery_crud->render();
		$this->template($output);
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
        $this->load->view('template_superadmin', $output);
    }

    public function templatekosong($output = null) {
        $this->load->view('example', $output);
    }

    public function stokTersedia() {
        $output = $this->master->prosesStok('0', 'Stok Barang Tersedia');
        $this->template($output);
    }

    function jenis_non_fisik() {
        $output = $this->master->jenis_non_fisik();
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

    public function index() {
		$getPengaturan = $this->db->get_where('pengaturan')->result();
		$dataPengaturan = array('pengaturan'=>$getPengaturan);
        $output = array('output' => $this->load->view('superadmin/pengaturan',$dataPengaturan,true), 'ket_header' => 'Beranda');
        $this->load->view('template_superadmin', $output);
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

    public function Usergudang() {
        $output = $this->master->Usergudang();
        $this->template($output);
    }

    public function Usertoko() {
        $output = $this->master->Usertoko();
        $this->template($output);
    }

    public function User() {
        $output = $this->master->User();
        $this->template($output);
    }

    //transaksi
    function tr_pengadaan() {
        $this->load->model('Get_master_model', 'getMaster', true);
        $output = $this->load->view('tr_pengadaan/h_pengadaan', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(), 'ket_header' => 'Transaksi Pengadaan Barang'));
    }

    function tr_retur() {
        $this->load->model('Get_master_model', 'getMaster', true);
        $output = $this->load->view('tr_retur/h_retur', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(), 'ket_header' => 'Transaksi Retur Barang'));
    }

    function tr_pengiriman() {
        $this->load->model('Get_master_model', 'getMaster', true);
        $output = $this->load->view('tr_pengiriman/h_pengiriman', '', true);
        $this->template((object) array('output' => $output, 'js_files' => array(), 'css_files' => array(), 'ket_header' => 'Transaksi Pengiriman'));
    }

}
