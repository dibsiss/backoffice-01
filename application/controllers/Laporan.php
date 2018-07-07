<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

    var $hak_user;
    var $owner;
    var $id_user;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('grocery_CRUD');
        $this->load->library('Grocery_crud_multi');
        $this->load->helper(array('form', 'url'));
        $this->load->model('general_model', 'general', true);
        $this->load->model('Transaksi_model', 'tr', true);
        $this->load->model('master_model', 'master', true);
        $this->load->library('datatables');
        $this->load->model('laporan_model', 'laporan', true);
        $this->hak_user = $this->session->userdata('hak_user');
        $this->owner = $this->session->userdata('owner');
        $this->id_user = $this->session->userdata('id_user');
        $this->load->helper('combo_helper');
    }
	function prosesMutasi(){
		$this->laporan->prosesMutasi();
	}
	function lapSetoran(){
		$temp=$this->load->view("laporan/setoran/h_setoran", "", true);
		$output = array('output' => $temp, 'ket_header' => 'Laporan Setoran Toko');
        $this->tr->templateNotif(@$output);
	}
	function prosesSetoranKasir(){
		$this->laporan->prosesSetoranKasir();
	}
	
	function lapAntarToko(){
		if($this->hak_user=='toko'){
			$temp = $this->load->view("laporan/kirim_antar_toko/lap_antar_toko_toko", "", true);
		}else{
			$temp = $this->load->view("laporan/kirim_antar_toko/lap_antar_toko", "", true);
		}
		$output = array('output' => $temp, 'ket_header' => 'Laporan Antar Toko');
        $this->tr->templateNotif(@$output);
	}
	
	function mutasi(){
		$temp = $this->load->view("laporan/mutasi/lap_mutasi", "", true);
		$output = array('output' => $temp, 'ket_header' => 'Laporan Mutasi Barang');
        $this->tr->templateNotif(@$output);
	}
    function mutasi_gudang(){
        $temp = $this->load->view("laporan/mutasi/lap_mutasi_gudang", "", true);
        $output = array('output' => $temp, 'ket_header' => 'Laporan Mutasi Barang');
        $this->tr->templateNotif(@$output);
    }
	function prosesAntarToko(){
		$this->laporan->prosesAntarToko();
	}
	function prosesReturCustomer(){
		$this->laporan->prosesReturCustomer();
	}
	
	function koreksi(){
		$output = array('output' => $this->load->view("laporan/koreksi/lap_koreksi", "", true), 'ket_header' => 'Laporan Koreksi Barang');
        $this->tr->templateNotif(@$output);
	}
	
	function printBarcode($id_toko=null,$id_barang=null){
		if($id_toko==$this->owner){
			$this->laporan->printBarcode($id_toko,$id_barang);
		}
		
	}
    function index() {
        $output = array('output' => $this->load->view("laporan/pengadaan/lap_pengadaan", "", true), 'ket_header' => 'Laporan Pengadaan Barang');
        $this->tr->templateNotif(@$output);
    }
	function penerimaan() {
        $output = array('output' => $this->load->view("laporan/penerimaan/lap_penerimaan", "", true), 'ket_header' => 'Laporan Penerimaan Barang');
        $this->tr->templateNotif(@$output);
    }
	function penjualan() {
		if($this->hak_user=='toko'){
			$template = $this->load->view("laporan/penjualan/lap_penjualan", "", true);
		}else{
			$template = $this->load->view("laporan/penjualan/lap_penjualan_superadmin", "", true);
		}
        $output = array('output' =>$template , 'ket_header' => 'Laporan Penjualan Barang');
        $this->tr->templateNotif(@$output);
    }
	function pengadaan(){
		$this->index();
	}

    function penjualanRentang() {
        $this->laporan->penjualanRentang();
    }  
	function penjualanRentangSuperadmin() {
		$id_toko = $this->input->post('id_toko');
		if(empty($id_toko)){
			$id_toko = 'semua';
		}
        $this->laporan->penjualanRentang($id_toko);
    }
	function pengadaanRentang() {
        $this->laporan->pengadaanRentang();
    }
	function prosesKoreksi(){
		$this->laporan->prosesKoreksi();
	}
	function penerimaanRentang() {
        $this->laporan->penerimaanRentang();
    }
	
    function headerPenjualan() {
        $output = $this->laporan->headerPenjualan('','Invoice Penjualan');
        $this->tr->templateNotif($output);
    }
	function headerReturCustomer() {
        $output = $this->laporan->headerReturCustomer('','Invoice Retur Customer');
        $this->tr->templateNotif($output);
    }
	
	function lapPengadaan() {
        $output = $this->laporan->pengadaan('','Invoice Pengadaan');
        $this->tr->templateNotif($output);
    }
	function lapKoreksi() {
        $output = $this->laporan->lapKoreksi();
        $this->tr->templateNotif($output);
    }
	function lapPengiriman() {
        $output = $this->laporan->Pengiriman('','Invoice Pengiriman');
        $this->tr->templateNotif($output);
    }
	function lapRetur() {
        $output = $this->laporan->retur('Invoice Retur');
        $this->tr->templateNotif($output);
    }
	function lapReturRusak() {
        $output = $this->laporan->returSesuatu('Invoice Retur','rusak');
        $this->tr->templateNotif($output);
    }
	function lapPengembalianReturRusak() {
        $output = $this->laporan->invoicePengembalianReturRusak();
        $this->tr->templateNotif($output);
    }
	function lapPengembalianReturHarga() {
        $output = $this->laporan->invoicePengembalianReturHarga();
        $this->tr->templateNotif($output);
    }
	
	function lapReturHarga() {
        $output = $this->laporan->returSesuatu('Invoice Retur Harga','harga');
        $this->tr->templateNotif($output);
    }
	
	function invoiceInputReturRusak($idh_retur_rusak=null){
		$getHeader = $this->db->get_where('h_retur_rusak',array('idh_retur_rusak'=>$idh_retur_rusak))->row();
		$data = array('header'=>$getHeader);
		$this->load->view('tr_input_retur_rusak/invoice_retur_rusak',$data);
	}
	function invoiceInputReturHarga($idh_retur_harga=null){
		$getHeader = $this->db->get_where('h_retur_harga',array('idh_retur_harga'=>$idh_retur_harga))->row();
		$data = array('header'=>$getHeader);
		$this->load->view('tr_input_retur_harga/invoice_retur_harga',$data);
	}
	
    function invoiceReject($idh_pengiriman=null,$idh_penerimaan=null){
		$getHeader = $this->db->get_where('h_pengiriman',array('idh_pengiriman'=>$idh_pengiriman,'id_tujuan'=>$this->owner))->row();
        $getDetail = $this->db->get_where('d_pengiriman',array('is_trouble'=>1,'idh_pengiriman'=>$idh_pengiriman))->result();
        //jika header kosong artinya transaksi ini bukan milik dari toko yang bersangkutan
        (empty($getHeader))?$getDetail = '' : '';
        $getDetailPenerimaan = $this->db->get_where('h_penerimaan',array('idh_penerimaan'=>$idh_penerimaan))->row();
		$data = array('header'=>$getHeader,'detail'=>$getDetail,'detailPenerimaan'=>$getDetailPenerimaan);
        $this->load->view('laporan/penerimaan/invoice_reject_penerimaan',$data);
    }

	function invoiceRetur($idh_retur=null){
        $getHeader = $this->db->get_where('h_retur',array('idh_retur'=>$idh_retur,'id_sumber'=>$this->owner))->row();
        $getDetail = $this->db->get_where('d_retur',array('idh_retur'=>$idh_retur))->result();
        //jika header kosong artinya transaksi ini bukan milik dari toko yang bersangkutan
        (empty($getHeader))?$getDetail = '' : '';
        $data = array('header'=>$getHeader,'detail'=>$getDetail);
        $this->load->view('laporan/retur/invoice_retur',$data);
    }
	function invoicePengiriman($idh_pengiriman=null){
        $getHeader = $this->db->get_where('h_pengiriman',array('idh_pengiriman'=>$idh_pengiriman,'id_sumber'=>$this->owner))->row();
        $getDetail = $this->db->get_where('d_pengiriman',array('idh_pengiriman'=>$idh_pengiriman))->result();
        //jika header kosong artinya transaksi ini bukan milik dari toko yang bersangkutan
        (empty($getHeader))?$getDetail = '' : '';
        $data = array('header'=>$getHeader,'detail'=>$getDetail);
        $this->load->view('laporan/pengiriman/invoice_pengiriman',$data);
    }	
	
	function invoicePenjualan($idh_penjualan=null){
        $getHeader = $this->db->get_where('h_penjualan',array('idh_penjualan'=>$idh_penjualan,'id_toko'=>$this->owner))->row();
        $getDetailFisik = $this->db->get_where('d_penjualan',array('idh_penjualan'=>$idh_penjualan))->result();
        $getDetailNonFisik = $this->db->get_where('d_penjualan_non_fisik',array('idh_penjualan'=>$idh_penjualan))->result();
        $getDetail= array_merge($getDetailFisik,$getDetailNonFisik);
		//jika header kosong artinya transaksi ini bukan milik dari toko yang bersangkutan
        (empty($getHeader))?$getDetail = '' : '';
        $data = array('header'=>$getHeader,'detail'=>$getDetail);
        $this->load->view('laporan/penjualan/invoice_penjualan',$data);
    }
	
	function invoiceReturCustomer($idh_retur_customer=null){
        $getHeader = $this->db->get_where('h_retur_customer',array('idh_retur_customer'=>$idh_retur_customer,'id_toko'=>$this->owner))->row();
        $getDetail = $this->db->get_where('d_retur_customer',array('idh_retur_customer'=>$idh_retur_customer))->result();
       //jika header kosong artinya transaksi ini bukan milik dari toko yang bersangkutan
        (empty($getHeader))?$getDetail = '' : '';
        $data = array('header'=>$getHeader,'detail'=>$getDetail);
        $this->load->view('laporan/retur/invoice_retur_customer',$data);
    }
	function invoicePengadaan($idh_pengadaan=null){
        $getHeader = $this->db->get_where('h_pengadaan',array('idh_pengadaan'=>$idh_pengadaan,'id_toko'=>$this->owner))->row();
        $getDetail = $this->db->get_where('d_pengadaan',array('idh_pengadaan'=>$idh_pengadaan))->result();
        //jika header kosong artinya transaksi ini bukan milik dari toko yang bersangkutan
        (empty($getHeader))?$getDetail = '' : '';
        $data = array('header'=>$getHeader,'detail'=>$getDetail);
        $this->load->view('laporan/pengadaan/invoice_pengadaan',$data);
    }
	function invoiceKoreksi($idh_mutasi=null){ //koreksi didapat dari datamutasi
       $getData = $this->db->get_where('history_mutasi',array('idhistory_mutasi'=>$idh_mutasi))->row();
        $data = array('header'=>$getData);
        $this->load->view('laporan/koreksi/invoice_koreksi',$data);
    }

    function stock(){
        $output = array('output' => $this->load->view("laporan/stock/lap_stock", "", true), 'ket_header' => 'Laporan Stok Barang');
        $this->tr->templateNotif(@$output);
    }
    function stockRentang() {
        $this->laporan->stockRentang();
    }
    function pengiriman(){
        $output = array('output' => $this->load->view("laporan/pengiriman/lap_pengiriman", "", true), 'ket_header' => 'Laporan Pengiriman Barang');
        $this->tr->templateNotif(@$output);
    }
    function pengirimanRentang() {
        $this->laporan->pengirimanRentang();
    }
	function retur(){
        $output = array('output' => $this->load->view("laporan/retur/lap_retur", "", true), 'ket_header' => 'Laporan Retur Barang');
        $this->tr->templateNotif(@$output);
    }
	function returCustomer(){
        $output = array('output' => $this->load->view("laporan/retur/lap_retur_customer", "", true), 'ket_header' => 'Laporan Retur Customer');
        $this->tr->templateNotif(@$output);
    }
    function returRentang() {
        $this->laporan->returRentang();
    }
	function getGudang($idCombo=null){
		$this->laporan->getGudang($idCombo);
	}
	function getToko($idGudang=null){
		$this->laporan->getToko($idGudang);
	}
}
