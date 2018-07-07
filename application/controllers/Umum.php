
<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Umum extends CI_Controller {

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
        $this->load->model('get_master_model', 'get_master', true);
        $this->hak_user = $this->session->userdata('hak_user');
        $this->owner = $this->session->userdata('owner');
        $this->id_user = $this->session->userdata('id_user');
        $this->load->helper('combo_helper');
    }

	function lain(){
		$output=$this->tr->transaksiLain();
	}
	function koreksiStok($id_barang=null){
		$getBarangs = @$this->db->get_where('cek_stok',array('status'=>0,'id_toko'=>$this->owner,'id_barang'=>$id_barang))->row();
		$getImey = $this->db->select('imey')->get_where("mst_stok", array('id_toko' => $this->owner, 'id_barang' => $id_barang, 'status' => 0, 'is_retur' => 0))->result();
		$data = array('id_barang'=>$id_barang,'barangs'=>$getBarangs,'imeys'=>$getImey);
		$this->load->view('tr_koreksi/koreksi_stok',$data);
	}
	function simpanKoreksi(){
		$jenis = $this->input->post('jenis',true);
		//jenis 0 artinya barang non hp
		if($jenis==0){
			$stokBarang = $this->input->post('stok',true);
			$this->form_validation->set_rules('koreksi[]', 'Koreksi', 'required|greater_than[0]|less_than_equal_to['.$stokBarang.']',
				array('greater_than'=>'Jumlah Koreksi Barang Harus Diatas 0','less_than'=>'Jumlah Koreksi Barang Tidak Boleh Lebih Dari '.$stokBarang)
			);

		}else{
			$this->form_validation->set_rules('koreksi[]', 'Koreksi', 'required');
		}
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'required');
		$id_barang = $this->input->post('id_barang',true);
		if ($this->form_validation->run() == true)
		{
				$this->tr->setKoreksi();
				$this->koreksiStok($id_barang);
		}
		else
		{
				$this->koreksiStok($id_barang);
		}
	}
	
    function showTipeGanti($jenis=null,$isHp=null){
        $data = array('is_hp'=>$isHp);
        if($jenis=='sejenis'){
            $this->load->view('tr_input_retur_rusak/pengganti_sejenis',$data);
        }else if($jenis=='lain_jenis'){
            $this->load->view('tr_input_retur_rusak/pengganti_lain_jenis',$data);
        }else if($jenis=='uang'){
            $this->load->view('tr_input_retur_rusak/pengganti_uang',$data);
        }else{
            echo 'Silahkan Pilih Tipe Ganti';
        }
    }
    function showIsianLainJenis($id_barang=null){
        $isHp = $this->master->isHp($id_barang)['is_hp'] ;
        $data = array('is_hp'=>$isHp);
        $tampilan=$this->load->view('tr_input_retur_rusak/pengganti_sejenis',$data,true);
        $return = array('tampilan'=>$tampilan,'is_hp'=>$isHp);
        echo json_encode($return);
    }
    function testarray() {
        $test = new stdClass();
        $test->nama = "nasrul";
        $test->alamat = "paciran";
        $this->general->testPre($test);
//        echo $test['nama'];
        echo $test->nama;
    }

    function testPost($test = null) {
        $this->general->testPre($test);
    }

    function deleteTempReturRusak($idh_retur = null) {
        echo $this->tr->deleteTempReturRusak($idh_retur);
    }

    function deleteTempReturCustomer($idh_retur = null) {
        echo $this->tr->deleteTempReturCustomer($idh_retur);
    }

    function showReturHarga($id_retur = null) {
        echo $this->tr->showReturHarga($id_retur);
    }
    function insertReturCustomer(){
//        $this->testPost($_POST);
        $hasil = $this->tr->insertReturCustomer();
        echo json_encode($hasil);
    }

    function showReturCustomer($idh_penjualan = null) {
        $getTemp = $this->db->get_where('temp_retur_customer', array('id_user' => $this->id_user))->row();
        if (empty($getTemp)) {
            //ketika pertama muncul tombol proses retur customer
            echo $this->tr->showReturCustomer($idh_penjualan);
        } else {
            $idtempReturCustomer = $getTemp->idtemp_retur_customer;
            //ada transaksi yang telah dilakukan sebelumnya
            echo $this->tr->showTempReturCustomer($idtempReturCustomer, $getTemp->idh_penjualan, 'kedua');
        }
    }

    function showTempReturCustomer($idh_penjualan = null) {
        echo $this->tr->showTempReturCustomer('',$idh_penjualan, 'awal');
    }

    function showReturRusak($id_retur = null) {
        //cek di temp retur rusak apakah id user ini pernah melakukan retur apa belum
        $sessionReturRusak = $this->db->get_where('temp_retur_rusak', array('id_user' => $this->id_user))->row();
        if (empty($sessionReturRusak)) {
            //jika kosong artinya ini adalah transaksi baru // user ini tidak memiliki tanggungan transaksi yang belum selesai
            echo $this->tr->showReturRusak($id_retur);
        } else {
            //jika sudah ada maka cek apakah di header transaksi h retur rusak id retur ini telah di selesaikan oleh user yang lain jika sudah maka temporari dihapus dan ditampilkan sesuai dengan id yang dipilih
            //jika tidak ada maka tampilkan data yang ada ditemporari terlebih dahulu
            $cekHReturRusak = $this->db->get_where('h_retur_rusak', array('idh_retur' => $sessionReturRusak->idh_retur))->row();
            if (empty($cekHReturRusak)) {
                //jika kosong maka tampilkan data dalam temporari/ artinya belum di selesaikan oleh user lain
                // echo 'ifpertama';
				echo $this->tr->showTempReturRusak(@$sessionReturRusak->idh_retur, 'kedua');
            } else {
                //delete temporari dan tampilkan data sesuai dengan yang dipilih oleh user
                // echo 'ifkedua';
				$this->db->delete('temp_retur_rusak', array('idh_retur' => $sessionReturRusak->idh_retur, 'id_user' => $this->id_user));
                echo $this->tr->showTempReturRusak($id_retur, 'kedua'); //kedua karena yang pertama adalah berada di event showTempReturRusak
            }
        }
    }

    function showTempReturRusak($id_retur = null) {
        echo $this->tr->showTempReturRusak($id_retur, 'awal');
    }

    function editTempReturRusak() {
//        $this->testPost($_POST);
        echo $this->tr->editTempReturRusak();
    }

    function showImeyReturRusak($idh_retur = null, $id_barang = null, $is_hp = null) {
        $this->tr->showImeyReturRusak($idh_retur, $id_barang, $is_hp);
    }

    function insertDetailReturCustomer() {
//        $this->testPost($_POST);
        echo $this->tr->insertDetailReturCustomer();
    }
    function insertDetailReturCustomerNonFisik() {
//        $this->testPost($_POST);
        echo $this->tr->insertDetailReturCustomerNonFisik();
    }
    function unistallDetailReturCustomer() {
//        $this->testPost($_POST);
        echo $this->tr->unistallDetailReturCustomer();
    }

    function showImeyReturCustomer($idh_retur = null, $id_barang = null, $is_hp = null, $status = null) {
        $this->tr->showImeyReturCustomer($idh_retur, $id_barang, $is_hp, $status);
    }

    function deleteRecordReturRusak($id = null) {
        $this->db->where('idd_temp_retur_rusak', $id)->delete("d_temp_retur_rusak");
    }
    
    function insertReturRusak() {
//        $this->testPost($_POST);
        $hasil = $this->tr->insertReturRusak();
        echo json_encode($hasil);
    }

    function showTableReturRusak($idh_retur = null, $id_barang = null, $is_hp = null) {
        $this->tr->showTableReturRusak($idh_retur, $id_barang, $is_hp);
    }

    function showHargaBeli($value = null) {
        $barang = @$this->db->get_where('mst_stok', array('id_toko' => $this->owner, 'id_barang' => $value, 'status' => 0))->row();
        echo $hargaBeli = 'Rp. ' . $this->general->formatRupiah(@$barang->harga_beli);
    }

    function cek_harga_beli() {
        return $this->tr->cek_harga_beli();
    }

//    function cekRetur(){
//        $data = $this->db->select("a.idh_penjualan as id,a.tgl, a.id_user, a.id_customer,b.id_barang as name,c.imey")->limit(30)->join("d_penjualan b", "a.idh_penjualan = b.idh_penjualan")->join("imey_penjualan c", "b.idd_penjualan= c.idd_penjualan")->get_where("h_penjualan a", array('a.id_toko' => $this->owner, 'c.is_retur' => 0))->result_array();
//        $this->general->testPre($data);
//        $data1 = $this->db->select("a.idh_penjualan as id,a.tgl, a.id_user, a.id_customer,b.id_barang as name,b.nomer as imey")->limit(30)->join("d_penjualan_non_fisik b", "a.idh_penjualan = b.idh_penjualan")->get_where("h_penjualan a", array('a.id_toko' => $this->owner, 'b.is_retur' => 0))->result_array();
//        $this->general->testPre($data1);
//        $testing = array_merge($data,$data1);
//        $this->general->testpre($testing);
//        
//    }
    
    function getReturCustomer() {
        $data = $this->tr->getReturCustomer();
        echo json_encode($data);
    }

    function getReturHarga() {
        $data = $this->tr->getReturHarga();
        echo json_encode($data);
    }

    function getReturRusak() {
        $data = $this->tr->getReturRusak();
        echo json_encode($data);
    }

    function editRowPenjualanHarga() {
        $this->tr->editRowPenjualanHarga();
    }

    function simpanPenjualan() {
		$jenis_bayar = $this->input->post('jenis_bayar',true);
		$jumlahTotal = $this->input->post('jumlahTotal',true);
		if($jenis_bayar==0){
			$this->form_validation->set_rules('jumlahBayar', 'Jumlah Bayar', 'required|greater_than_equal_to['.$jumlahTotal.']');
			//get data penjualan
			$bank = $this->input->post('jumlahBayar',true);
			$no_referensi = $this->input->post('uangKembali',true);
		}else{
			$this->form_validation->set_rules('bank', 'Bank', 'required');
			$this->form_validation->set_rules('no_referensi', 'No Referensi', 'required');
			//get data penjualan
			$bank = $this->input->post('bank',true);
			$no_referensi = $this->input->post('no_referensi',true);
		}
		
		 if ($this->form_validation->run() == true){
			 //simpan jenis pembayaran uang kembali dan seterusnya
			$idh_penjualan = $this->session->userdata('idh_penjualan');
			$this->db->set(array('jenis_bayar'=>$jenis_bayar,'bank'=>$bank,'no_ref'=>$no_referensi))->where('idh_penjualan',$idh_penjualan)->update('h_penjualan');
			 //simpan transaksi
			 $result = $this->tr->simpanPenjualan();
		 }else{
			 $result = array('success' => false, 'message' => 'Terjadi Kesalahan '.validation_errors());
		 }
		 echo json_encode($result);
		// $this->general->testPre($_POST);
        
    }

    function truncatePenjualan() {
        $this->tr->truncatePenjualan();
    }

    function editRowPenjualan() {
        $this->tr->editRowPenjualan();
    }

    function showTablePenjualan($idh_penjualan = null) {
        $this->tr->showTablePenjualan($idh_penjualan);
    }

    function deleteDetailPenjualan() {
        $id_detail = $this->input->post('idd_penjualan');
        echo $this->tr->deleteDetailPenjualan($id_detail);
    }

    function insertPenjualan() {
        $hasil = $this->tr->insertPenjualan();
        echo json_encode($hasil);
    }

    function insertPenjualanNonFisik() {
        $hasil = $this->tr->insertPenjualanNonFisik();
        echo json_encode($hasil);
    }

    function getBarangJual() {
        $data = $this->tr->getBarangJual();
        echo json_encode($data);
    }

    function getBarangJualNonFisik() {
        $data = $this->tr->getBarangJualNonFisik();
        echo json_encode($data);
    }

    function waktuNow() {
        echo gmdate("Y-m-d H:i:s", time() + 60 * 60 * 7);
    }

    function cekOwner($idToko = null) {
        if ($idToko != $this->owner) {
            die('Akses Ditolak');
        }
    }

    function showImeyColumn($idToko = null, $idBarang = null) {
        $this->cekOwner($idToko);
        $this->master->showImeyColumn($idToko, $idBarang);
    }

    function tandaiPermintaan($id = null) {
        $this->db->set('is_appliyed', $this->id_user)->where('idh_permintaan', $id)->update('h_permintaan');
    }

    function tandaiReject() {
		$idh_pengiriman = $this->input->post('idh_pengiriman',true);
        $this->tr->tandaiReject($idh_pengiriman);
    }

    function tandaiRetur($id = null) {
		//insert history
		$getBarangsHistory = $this->db->query("select *, count(*) as jumlah_hp from d_retur where idh_retur = '$id' group by id_barang")->result();
		foreach($getBarangsHistory as $gb){
			$getHarga = $this->db->get('mst_stok',array('is_retur'=>$id,'id_toko'=>$this->owner,'id_barang'=>$gb->id_barang))->row();
			if($imey=='imey_retur'){
				//barang bertipe non hp jumlah didapat dari jumlah
				$this->tr->insertHistory($gb->id_barang,$gb->jumlah,$id,$gb->idd_retur,'masuk','d_retur',$getHarga->harga_beli,'','','');
			}else{
				//barang bertipe hp jumlah didapat dari count record
				$this->tr->insertHistory($gb->id_barang,$gb->jumlah_hp,$id,$gb->idd_retur,'masuk','d_retur',$getHarga->harga_beli,'','','');
			}
		}
		//update stok barang menjadi status ready
		$getBarangs = $this->db->get_where('d_retur',array('idh_retur'=>$id))->result();
        //generate getBarangs
		foreach($getBarangs as $g){
			$id_barang = $g->id_barang;
			$imey=@$g->imey;
			$idd_retur = $g->idd_retur;
			if($imey=='imey_retur'){
				//artinya barang bertipe non hp dan imey harus dicari di imey retur
				//get imey from imey retur
				$getImeys = $this->db->get_where('imey_retur',array('idd_retur'=>$idd_retur))->result();
				$imeyIn=$this->general->idToInWhere($getImeys,'imey');
				$imeys = array($imeyIn);
				//update mst stok
				$this->db->where_in('imey',$imeys,false);
				$this->db->set(array('status'=>0,'is_retur'=>0))->where(array('id_toko'=>$this->owner,'id_barang'=>$id_barang))->update('mst_stok');
			}else{
				//artinya barang bertipe hp dan imey bisa didapatkan langsung di tabel detail
				$this->tr->setReady($this->owner,$g->id_barang,$imey);
			}
		}
		
		$this->db->set('is_replay', $this->id_user)->where('idh_retur', $id)->update('h_retur');
    }
	
    function barcode() {
        $this->load->view('pluggins/barcode');
    }

/*
    function bikin_barcode($code) {
		ob_clean();
		//header("Content-type: image/png");
		// ob_start();
       
        // $code = '20170218-2-DPG-0001';
		if(!empty($code)){
			$this->load->library('zend');
			$this->zend->load('Zend/Barcode');
			// Zend_Barcode::render('Codabar', 'image', array('text' => $code), array());
			Zend_Barcode::render('code39', 'image', array('text' => $code), array());
		}
		
    }
	*/
	function bikin_barcode($code) {
		ob_clean();
		// header("Content-type: image/png;charset=utf-8");
		// header("Content-type: image/png");
		if(!empty($code)){
			$this->load->library('zend');
			$this->zend->load('Zend/Barcode');
			// Zend_Barcode::render('code39', 'image', array('text' => $code), array());	
			Zend_Barcode::render('code39', 'image', array('text'=>$code), array());			
		}
    }

    function mst_harga() {
        $output = $this->master->mstHarga();
        $this->tr->templateNotif($output);
    }
    function setHarga($id_barang=null,$imey=null){
        $output = $this->master->setHarga($id_barang,$imey);
    }

    function cekExistHarga() {
        return $this->master->cekExistHarga();
    }

    function simpanPenerimaan($idh_pengiriman = null) {
        $result = $this->tr->simpanPenerimaan($idh_pengiriman);
        echo json_encode($result);
    }
	
    function cek_complain($keterangan = null, $idh_pengiriman = null) {
        return $this->tr->cek_complain($keterangan, $idh_pengiriman);
    }

    function showImeyPenerimaan($idh_penerimaan = null, $id_barang = null, $is_hp = null) {
        $this->tr->showImeyPenerimaan($idh_penerimaan, $id_barang, $is_hp);
    }

    function showImeyPengirimanReject($idh_pengiriman = null, $id_barang = null, $is_hp = null) {
        $this->tr->showImeyPengirimanReject($idh_pengiriman, $id_barang, $is_hp);
    }

    function showImeyPengirimanNotif($idh_pengiriman = null, $id_barang = null, $is_hp = null) {
        $this->tr->showImeyPengirimanNotif($idh_pengiriman, $id_barang, $is_hp);
    }

    function showImeyReturNotif($idh_retur = null, $id_barang = null, $is_hp = null) {
        $this->tr->showImeyReturNotif($idh_retur, $id_barang, $is_hp);
    }

    function setDetailComplain($idd_pengiriman = null, $is_hp = null) {
        $this->tr->setDetailComplain($idd_pengiriman, $is_hp);
    }

    function destroyDetailComplain($idd_pengiriman = null, $is_hp = null) {
        $this->tr->destroyDetailComplain($idd_pengiriman, $is_hp);
    }

    function setComplain($idh_pengiriman = null) {
        $this->tr->setComplain($idh_pengiriman);
    }

    function destroyComplain($idh_pengiriman = null) {
        $this->tr->destroyComplain($idh_pengiriman);
    }

    function readPenerimaan($id = null) {
        $this->tr->readPenerimaan($id);
    }

    function readRetur($id = null) {
        $this->tr->readRetur($id);
    }

    function readPengiriman($id = null) {
        $this->tr->readPengiriman($id);
    }

    function readReject($id = null) {
        $this->tr->readReject($id);
    }

    function readPermintaan($id = null) {
        $this->tr->readPermintaan($id);
    }

    function getNotifKirim() {
        $hasil = $this->tr->getNotifKirim();
    }

    function getNotifKirimSumber() {
        $hasil = $this->tr->getNotifKirimSumber();
    }

    function getNotifPermintaan() {
        $hasil = $this->tr->getNotifPermintaan();
    }

    function getNotifReject() {
        $hasil = $this->tr->getNotifReject();
    }

    function getNotifRetur() {
        $hasil = $this->tr->getNotifRetur();
    }

    function listBarangPengiriman() {
        $this->load->view('tr_pengiriman/list_barang');
    }

    function listBarangPermintaan() {
        $this->load->view('tr_permintaan/list_barang');
    }

    function insertHeaderPengiriman() {
        $hasil = $this->tr->insertHeaderPengiriman();
        echo json_encode($hasil);
    }

    function insertHeaderPermintaan() {
        $hasil = $this->tr->insertHeaderPermintaan();
        echo json_encode($hasil);
    }

    function showDetailPengiriman($idh_pengiriman = null) {
        $this->tr->showDetailPengiriman($idh_pengiriman);
    }

    function showDetailPermintaan($idh_permintaan = null) {
        $this->tr->showDetailPermintaan($idh_permintaan);
    }

    function insertDetailPengirimanNonHp($id_barang = null, $jumlah = null) {
        $data = $this->tr->insertDetailPengirimanNonHp($id_barang, $jumlah);
        echo json_encode($data);
    }

    function insertDetailPengirimanHp($imey = null) {
        $data = $this->tr->insertDetailPengirimanHp($imey);
        echo json_encode($data);
    }

	// function testing(){
		// echo $this->general->genNumberTemp('d_pengiriman', 'idd_pengiriman', $this->id_user, 'DKIRIM', 4);
	// }
	
    function insertDetailPermintaanNonHp($id_barang = null, $jumlah = null) {
        $data = $this->tr->insertDetailPermintaan($id_barang, $jumlah);
        echo json_encode($data);
    }

    function insertDetailPermintaanHp($id_barang = null, $jumlah = null) {
        $data = $this->tr->insertDetailPermintaan($id_barang, $jumlah);
        echo json_encode($data);
    }

    function deleteDetailPengiriman() {
        $id_detail = $this->input->post('idd_pengiriman');
        echo $this->tr->deleteDetailPengirimanMulti($id_detail);
    }

    function deleteDetailPermintaan() {
        $id_detail = $this->input->post('idd_permintaan');
        echo $this->tr->deleteDetailPermintaanMulti($id_detail);
    }

    function deleteDetailPengirimanImey($idd_pengiriman = null, $is_hp = null) {
        $id = array('idd_pengiriman' => $idd_pengiriman);
        $this->tr->deleteDetailPengirimanSingle($id, $is_hp);
    }

    function insertPengiriman() {
        $result = $this->tr->insertPengiriman();
        if ($result['hasil']==true) {
            $data = array('success' => 1,'id_pengiriman'=>$result['id_pengiriman'], 'message' => 'Data Berhasil Disimpan');
        } else {
            $data = array('success' => 0, 'message' => 'Data Barang Belum Diisi !!');
        }
		header('Content-Type: application/json');
        echo json_encode($data);
    }

	function cek_testing(){
		$this->db->where(array('id_toko'=>$this->owner,'id_barang'=>'MENTARI-0000001','status'=>0));
		$this->db->or_where('is_retur', '20171124-2-HKIRIM-0001');
		$getSisa = $this->db->get('mst_stok')->result();
		echo count($getSisa);
		// $this->general->testPre($getSisa);
	}
	
	
    function insertPermintaan() {
        $result = $this->tr->insertPermintaan();
        if ($result) {
            $data = array('success' => 1, 'message' => 'Data Berhasil Disimpan');
        } else {
            $data = array('success' => 0, 'message' => 'Data Barang Belum Diisi !!');
        }
        echo json_encode($data);
    }

    function truncatePermintaan() {
        $this->tr->truncatePermintaan();
    }

    function truncatePengiriman() {
        $this->tr->truncatePengiriman();
    }

    function deleteDetailRetur() {
        $id_detail = $this->input->post('idd_retur');
        echo $this->tr->deleteDetailReturMulti($id_detail);
    }

    function deleteDetailReturImey($idd_retur = null, $is_hp = null) {
        $id = array('idd_retur' => $idd_retur);
        $this->tr->deleteDetailReturSingle($id, $is_hp);
    }

    function insertReturHarga() {
        $hasil = $this->tr->insertReturHarga();
        echo json_encode($hasil);
    }

    function insertRetur() {
        $result = $this->tr->insertRetur();
        if ($result['hasil']) {
            $data = array('success' => 1,'idh_retur'=>$result['idh_retur'], 'message' => 'Data Berhasil Disimpan');
        } else {
            $data = array('success' => 0, 'message' => 'Data Barang Belum Diisi !!');
        }
        echo json_encode($data);
    }

    function truncateRetur() {
        $this->tr->truncateRetur();
    }

//    function insertDetailReturNonHp($id_barang = null, $jumlah = null) {
//        $data = $this->tr->insertDetailReturNonHp($id_barang, $jumlah);
//        echo json_encode($data);
//    }
    function insertDetailReturNonHp() {
        $data = $this->tr->insertDetailReturNonHp();
        echo json_encode($data);
    }

    function cek_stok($id_barang = null, $jml = null) {
        return $this->tr->cek_stok($id_barang, $jml);
    }
	
    function cek_diskon($id_barang = null,$params=null) {
        return $this->tr->cek_diskon($id_barang,$params);
    }
	
    function cek_potongan($id_barang = null,$params=null) {
        return $this->tr->cek_potongan($id_barang,$params);
    }

    function cek_harga($id_barang = null, $jml = null) {
        return $this->tr->cek_harga($id_barang, $jml);
    }

    function cek_stok_permintaan($id_barang = null, $jml = null) {
        return $this->tr->cek_stok_permintaan($id_barang, $jml);
    }

    function showDetailRetur($idh_retur = null) {
        $this->tr->showDetailRetur($idh_retur);
    }

    function insertHeaderRetur() {
        $hasil = $this->tr->insertHeaderRetur();
        echo json_encode($hasil);
    }

    function listBarang($id_supliyer = null) {
        $data = array('id_supliyer' => $id_supliyer);
        $this->load->view('tr_retur/list_barang', $data);
    }

    function insertDetailReturHp() {
        $this->tr->insertDetailReturHp();
    }

    function getListHp($id_supliyer = null) {
        header('Content-Type: application/json');
        echo $this->get_master->getListHp($id_supliyer);
    }

    function getListNonHp($id_supliyer = null) {
        header('Content-Type: application/json');
        echo $this->get_master->getListNonHp($id_supliyer);
    }

    function showImeyRetur($idh_retur = null, $id_barang = null, $is_hp = null) {
        $data = array('idh_retur' => $idh_retur, 'id_barang' => $id_barang, 'is_hp' => $is_hp);
        $this->load->view('tr_retur/list_imey', $data);
    }

    function getListImeyRetur($idh_retur = null, $id_barang = null, $is_hp = null) {
        header('Content-Type: application/json');
        echo $this->get_master->getListImeyRetur($idh_retur, $id_barang, $is_hp);
    }

    function getListHpPengiriman() {
        header('Content-Type: application/json');
        echo $this->get_master->getListHpPengiriman();
    }

    function getListHpPermintaan($is_hp = null, $tujuan = null) {
        header('Content-Type: application/json');
        echo $this->get_master->getListHpPermintaan($is_hp, $tujuan);
    }

    function getListNonHpPengiriman() {
        header('Content-Type: application/json');
        echo $this->get_master->getListNonHpPengiriman();
    }

    function getListNonHpPermintaan($is_hp = null, $tujuan = null) {
        header('Content-Type: application/json');
        echo $this->get_master->getListNonHpPermintaan($is_hp, $tujuan);
    }

    function showImeyPengiriman($idh_pengiriman = null, $id_barang = null, $is_hp = null) {
        $data = array('idh_pengiriman' => $idh_pengiriman, 'id_barang' => $id_barang, 'is_hp' => $is_hp);
        $this->load->view('tr_pengiriman/list_imey', $data);
    }

    function getListImeyPengiriman($idh_pengiriman = null, $id_barang = null, $is_hp = null) {
        header('Content-Type: application/json');
        echo $this->get_master->getListImeyPengiriman($idh_pengiriman, $id_barang, $is_hp);
    }

    #=================================

    function showDetailBarangReturPengadaan($idRetur = null) {
        $dataBarang = $this->db->select('a.*,b.nama,count(*) as jumlah_hp')->join("mst_barang b", "b.id_barang = a.id_barang")->group_by('a.id_barang')->get_where('d_retur a', array('a.idh_retur' => $idRetur))->result();
        $data = array('tabel' => $dataBarang);
        $this->load->view('tr_pengadaan/list_imey_retur', $data);
    }

    public function templatekosong($output = null) {
        $this->load->view('example', $output);
    }

    function templateBootstrapKosong($data = null) {
        $this->load->view('pluggins/template_bootstrap_kosong', $data);
    }

    function showReturPengadaan($id_supliyer = null) {
        $getRetur = $this->db->select('a.*,b.status')->join('mst_retur b', "b.id_retur = a.id_retur")->get_where('h_retur a', array('a.id_sumber' => $this->owner, 'a.id_supliyer' => $id_supliyer, 'a.is_replay' => 0))->result();
        $data = array('tabel' => $getRetur);
        echo $hasil = $this->load->view('tr_pengadaan/table_retur', $data, true);
    }

    function editTempPengadaan() {
        echo $result = $this->tr->editTempPengadaan();
    }

    function deleteTempPengadaan() {
        $this->tr->deleteTempPengadaan();
        echo $this->session->userdata('no_nota');
    }

    function getBarang() {
        $namaBarang = $_GET['search'];
        $this->db->like('nama', $namaBarang);
        $this->db->limit(20);
        $data = $this->db->get('mst_barang')->result();
        $hasil = array();
        foreach ($data as $key => $value) {
            $hasil[] = array('id' => $value->id_barang, 'val' => $value->nama);
        }
        echo json_encode($hasil);
    }

    function getBarangById($id = null) {
        $this->db->select('a.*, b.nama as nama_category');
        $this->db->join('mst_category b', 'a.id_category = b.id_category');
        $data = $this->db->get_where('mst_barang a', array('id_barang' => $id))->row();
        echo json_encode($data);
    }

    function insertKeranjang() {
        $this->tr->insertTempPengadaan();
    }

    function updateKeranjang() {
        $hasil = $this->tr->updateTempPengadaan();
        echo json_encode($hasil);
    }

    function insertPengadaan() {
        $tr = $this->tr->insertPengadaan();
        echo ($tr) ? $tr : false;
    }
	
    function showTempPengadaan($no_nota = null) {
        $this->tr->showTempTabel($no_nota);
    }
	

    function check_hp() {
        $hasil = $this->tr->isHpOrNot();
        return $hasil;
    }
	function check_imey_hp() {
        $hasil = $this->tr->check_imey_hp();
        return $hasil;
    }

    function truncatePengadaan() {
        $this->tr->truncatePengadaan();
    }

    function showImey($idh_temp = null) {
        $data = array('idh_temp' => $idh_temp);
        $this->load->view('tr_pengadaan/list_imey', $data);
    }

    function getListImey($idh_temp = null) {
        header('Content-Type: application/json');
        echo $this->get_master->getListImey($idh_temp);
    }

    function deleteDetailTempPengadaan($idd_temp = null, $idh_temp = null) {
        $this->tr->deleteDetailTempPengadaan($idd_temp, $idh_temp);
    }

    function changePasswordToko($id) {
        $this->load->view('user/form_password_toko');
    }

}
