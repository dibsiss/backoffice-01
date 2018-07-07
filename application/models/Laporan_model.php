<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_model extends CI_Model {

    var $id_user;
    var $owner;
	var $hakUser;
    var $tokoSegudang;

    function __construct() {
        parent::__construct();
		$this->hakUser = @$this->session->userdata('hak_user');
        $this->id_user = @$this->session->userdata('id_user'); //superadmin=1,2,3 toko user loginnya
        $this->owner = @$this->session->userdata('owner'); //superadmin,g001,t001
        $this->tokoSegudang = @$this->session->userdata('toko_segudang');
        date_default_timezone_set("Asia/Jakarta");
    }
	
	function prosesMutasi(){
		$tanggal = $this->input->post('tanggal_rentang');
        $tanggal_mulai = substr($tanggal, 0, 10);
        $tanggal_selesai = substr($tanggal, -10);
		$id_barang = $this->input->post('id_barang');
		$id_tempat = $this->input->post('id_tempat');
		
		(!empty($tanggal)) ? $this->db->where("date(tgl) between '$tanggal_mulai' and '$tanggal_selesai'") : '';
		(!empty($id_barang)) ? $this->db->where("id_barang",$id_barang) : '';
		(!empty($id_tempat)) ? $this->db->where("id_tempat",$id_tempat) : $this->db->where("id_tempat",$this->owner);
		$laporan = $this->db->order_by('tgl','asc')->get('history_mutasi')->result();
		$data = array('header' => $laporan);
        $this->load->view('laporan/mutasi/detail_mutasi', $data);
	}
	
	function prosesSetoranKasir(){
		$this->form_validation->set_rules('tgl','Tanggal','required');
		if ($this->form_validation->run() == true){
			$tgl = $this->input->post('tgl',true);
			$invoice = $this->invoiceSetoran($tgl);
			$data = array('sukses'=>true,'pesan'=>'data masuk','invoice'=>$invoice);
		}
		else{
			$data = array('sukses'=>false,'pesan'=>'<b>Terjadi Kesalahan</b> '.validation_errors());
		}
		echo json_encode($data);
		//get pemasukan 
	}
	function invoiceSetoran($date=null){
		$getDetailToko = $this->db->get_where('mst_toko',array('id_toko'=>$this->owner))->row();
		$getSetoranFisik = $this->db->select_sum('jumlah_fisik')->get_where('keuntungan_fisik',array('id_toko'=>$this->owner,'date(tgl)'=>$date))->row();
		$getSetoranNonFisik = $this->db->select_sum('harga')->get_where('keuntungan_non_fisik',array('id_toko'=>$this->owner,'date(tgl)'=>$date))->row();
		$getLain = $this->db->select_sum('nominal')->get_where('tr_lain',array('id_toko'=>$this->owner,'date(tgl)'=>$date,'jenis'=>'Pemasukan'))->row();
		
		$getSetoranFisikKeluar = $this->db->select_sum('harga')->get_where('detail_pengeluaran_toko',array('id_toko'=>$this->owner,'date(tgl_input)'=>$date,'jenis'=>'FISIK'))->row();
		$getSetoranNonFisikKeluar = $this->db->select_sum('harga')->get_where('detail_pengeluaran_toko',array('id_toko'=>$this->owner,'date(tgl_input)'=>$date,'jenis'=>'ELEKTRIK'))->row();
		$getLainKeluar = $this->db->select_sum('nominal')->get_where('tr_lain',array('id_toko'=>$this->owner,'date(tgl)'=>$date,'jenis'=>'Pengeluaran'))->row();
		
		if(!empty($getSetoranFisik) || !empty($getSetoranNonFisik) || !empty($getLain)){
			$detail = true;
		}else{
			$detail=false;
		}
		$data = array('fisikKeluar'=>$getSetoranFisikKeluar,'nonFisikKeluar'=>$getSetoranNonFisikKeluar,'lainKeluar'=>$getLainKeluar,'tgl_setor'=>$date,'detail'=>$detail,'detailToko'=>$getDetailToko,'setoranFisik'=>$getSetoranFisik,'setoranNonFisik'=>$getSetoranNonFisik,'setoranLain'=>$getLain);
		return $this->load->view('laporan/setoran/invoice_setoran',$data,true);
	}
	function getNamaUser($idUser=null){
		return @$this->db->get_where('data_user',array('id_user'=>$idUser))->row();
	}
	function getNamaTempat($idTempat=null){
		return @$this->db->get_where('data_sumber',array('id'=>$idTempat))->row();
	}
	function changePrintBarcode($id=null){
		$this->db->set('value',$id)->like('nama','barcode')->update('pengaturan');
	}
	function printBarcode($id_toko=null,$id_barang=null){
		$getData=$this->db->get_where('mst_stok',array('id_barang'=>$id_barang,'status'=>0,'id_toko'=>$this->owner))->result();
		$data = array('getData'=>$getData);
		$this->load->view('laporan/stock/barcode',$data);
	}
	
	function prosesKoreksi(){
		$tanggal = $this->input->post('tanggal_rentang');
        $tanggal_mulai = substr($tanggal, 0, 10);
        $tanggal_selesai = substr($tanggal, -10);
		$id_barang = $this->input->post('id_barang');
		(!empty($tanggal)) ? $this->db->where("date(tgl) between '$tanggal_mulai' and '$tanggal_selesai'") : '';
		(!empty($id_barang)) ? $this->db->where("id_barang",$id_barang) : '';
		$laporan = $this->db->order_by('tgl','desc')->get_where('tr_koreksi',array('id_pemilik'=>$this->owner))->result();
		$data = array('header' => $laporan);
        $this->load->view('laporan/koreksi/detail_koreksi', $data);
	}
	
	function prosesAntarToko(){
		$tanggal = $this->input->post('tanggal_rentang');
        $tanggal_mulai = substr($tanggal, 0, 10);
        $tanggal_selesai = substr($tanggal, -10);
		$id_sumber = @$this->input->post('id_toko_sumber');
		$id_tujuan = @$this->input->post('id_toko_tujuan');
		(!empty($tanggal)) ? $this->db->where("date(tgl_input) between '$tanggal_mulai' and '$tanggal_selesai'") : '';
		if($this->hakUser=='gudang'){
			//jika gudang maka jika id sumber dan id tujuan kosong maka id sumber diisi dengan idtoko dibawahnya
		$getToko=$this->prosesGetTokoSegudang($this->owner);
		$tokoWhereIn = $this->general->idToInWhere($getToko,'id_toko');
			(!empty($id_sumber)) ? $this->db->where("id_sumber",$id_sumber) : $this->db->where_in('id_sumber',$tokoWhereIn,false);
			(!empty($id_tujuan)) ? $this->db->where("id_tujuan",$id_tujuan) : $this->db->where_in('id_tujuan',$tokoWhereIn,false);
		}elseif($this->hakUser=='toko'){
		//jika toko maka jika id tujuan kosong id tujuan akan diinput dengan id toko segudang
		//dan set id sumber adalah dirinya sendiri	
			$getToko=$this->prosesGetTokoSegudang($this->tokoSegudang);
			$tokoWhereIn = $this->general->idToInWhere($getToko,'id_toko');
			$this->db->where('id_sumber',$this->owner);
			(!empty($id_tujuan)) ? $this->db->where("id_tujuan",$id_tujuan) : $this->db->where_in('id_tujuan',$tokoWhereIn,false);
		}else{
			//untuk proses superadmin
			(!empty($id_sumber)) ? $this->db->where("id_sumber",$id_sumber) : '';
			(!empty($id_tujuan)) ? $this->db->where("id_tujuan",$id_tujuan) : '';
		}
		$laporan = $this->db->order_by('tgl_input','desc')->get('h_pengiriman')->result();
		$data = array('header' => $laporan);
        $this->load->view('laporan/kirim_antar_toko/detail_kirim', $data);
	}
	
	function pengadaanRentang() {
        $tanggal = $this->input->post('tanggal_rentang');
        $tanggal_mulai = substr($tanggal, 0, 10);
        $tanggal_selesai = substr($tanggal, -10);
        $jenis_barang = $this->input->post('jenis_barang');
        $id_transaksi = $this->input->post('id_transaksi');
        $id_supliyer = $this->input->post('id_supliyer');

        (!empty($id_supliyer)) ? $this->db->where('a.id_supliyer',$id_supliyer) : '';
        if(!empty($id_transaksi)) { 
			$id_transaksi = trim($id_transaksi);
			$this->db->like('a.idh_pengadaan',$id_transaksi); 
		};
        (!empty($tanggal)) ? $this->db->where("date(a.tgl_input) between '$tanggal_mulai' and '$tanggal_selesai'") : '';
        if($jenis_barang == 'hp'){
            $this->db->where('b.nama_category', 'HANDPHONE');
        }else if($jenis_barang=='nonhp'){
            $this->db->where('b.nama_category !=', 'HANDPHONE');
        }else{
            $this->db->where('0', '0');
        }
		$this->db->group_by('a.idh_pengadaan');
		$this->db->join('d_pengadaan c','a.idh_pengadaan = c.idh_pengadaan');
		$this->db->join('mst_barang_detail b', 'c.id_barang = b.id_barang');
		
		
        $getHeader = $this->db->get_where('h_pengadaan a',array('a.id_toko'=>$this->owner))->result();;
        $data = array('header' => $getHeader, 'jenis_barang' => $jenis_barang);
        $this->load->view('laporan/pengadaan/lap_pengadaan_detail', $data);
    }
function penerimaanRentang() {
        
		$tanggal = $this->input->post('tanggal_rentang');
        $tanggal_mulai = substr($tanggal, 0, 10);
        $tanggal_selesai = substr($tanggal, -10);
        $jenis_barang = $this->input->post('jenis_barang');
        $id_transaksi_kirim = $this->input->post('id_transaksi_kirim');
        $id_transaksi_terima = $this->input->post('id_transaksi_terima');
        $id_supliyer = $this->input->post('id_supliyer');
        $id_pegawai = $this->input->post('id_pegawai');

        (!empty($id_supliyer)) ? $this->db->where('e.id_sumber',$id_supliyer) : '';
        (!empty($id_pegawai)) ? $this->db->where('a.id_user',$id_pegawai) : '';
        (!empty($id_transaksi_terima)) ? $this->db->where('a.idh_penerimaan',$id_transaksi_terima) : '';
        (!empty($id_transaksi_kirim)) ? $this->db->where('a.idh_pengiriman',$id_transaksi_kirim) : '';
        if(!empty($id_transaksi)) { 
			$id_transaksi = trim($id_transaksi);
			$this->db->like('a.idh_pengadaan',$id_transaksi); 
		};
        (!empty($tanggal)) ? $this->db->where("date(a.tgl_input) between '$tanggal_mulai' and '$tanggal_selesai'") : '';
        if($jenis_barang == 'hp'){
            $this->db->where('b.nama_category', 'HANDPHONE');
        }else if($jenis_barang=='nonhp'){
            $this->db->where('b.nama_category !=', 'HANDPHONE');
        }else{
            $this->db->where('0', '0');
        }
		$this->db->group_by('a.idh_penerimaan');
		$this->db->join('d_penerimaan c','a.idh_penerimaan = c.idh_penerimaan');
		$this->db->join('mst_barang_detail b', 'c.id_barang = b.id_barang');
		$this->db->join('h_pengiriman e','a.idh_pengiriman = e.idh_pengiriman');
		$this->db->select("a.*,e.id_sumber,b.*");
		$this->db->where('id_tujuan',$this->owner);
        $getHeader = $this->db->get('h_penerimaan a')->result();
		$data = array('header' => $getHeader, 'jenis_barang' => $jenis_barang);
        $this->load->view('laporan/penerimaan/lap_penerimaan_detail', $data);
		
    }

    function lapKoreksi() {
        $crud = new grocery_CRUD();
        $crud->set_table('history_mutasi');
        $crud->set_subject('Laporan Koreksi Barang');
        $crud->columns('idh_transaksi', 'id_barang','tgl','id_user','jumlah','sisa_stok');
        $crud->add_action('Invoice', '', 'laporan/invoiceKoreksi', 'emodal fa-file-word-o');
        $crud->where('id_tempat', $this->owner);
		$crud->where('keterangan','tr_koreksi');
		$crud->set_primary_key('id_user','data_user');
		$crud->set_relation('id_user','data_user','fullname');
		$crud->set_relation('id_tempat','data_sumber','nama');
		$crud->set_relation('id_barang','mst_barang','nama');
        $crud->display_as('idh_transksi', 'Id Transaksi')
        ->display_as('id_barang', 'Barang');
        $crud->unset_add();
        $crud->unset_delete();
        $crud->unset_read();
        $crud->unset_edit();
        $output = $crud->render();
        $output->ket_header = 'Laporan Koreksi Toko';
        return $output;
    }
	
	function pengadaan($status = null, $title = null) {
        $crud = new grocery_CRUD();
        $crud->set_table('h_pengadaan');
        $crud->set_subject('Laporan Pengadaan');
        $crud->columns('idh_pengadaan', 'id_supliyer', 'tgl_nota', 'no_nota', 'id_user');
        $crud->add_action('Invoice', '', 'laporan/invoicePengadaan', 'emodal fa-file-word-o');
        $crud->where('id_toko', $this->owner);
		$crud->set_primary_key('id_user','data_user');
		$crud->set_relation('id_user','data_user','fullname');
		$crud->set_relation('id_supliyer','mst_supliyer','nama');
        $crud->display_as('idh_pengadaan', 'Id Transaksi')
                ->display_as('id_supliyer', 'Supliyer');
        $crud->unset_add();
        $crud->unset_delete();
        $crud->unset_read();
        $crud->unset_edit();
        $output = $crud->render();
        $output->ket_header = $title;
        return $output;
    }
		
	function pengiriman($status = null, $title = null) {
        $crud = new grocery_CRUD();
        $crud->set_table('h_pengiriman');
        $crud->set_subject('Laporan Pengiriman');
		$crud->set_primary_key('id_user','data_user');
		$crud->set_primary_key('id','data_sumber');
        $crud->columns('idh_pengiriman', 'tgl_nota', 'id_tujuan', 'id_user');
        $crud->add_action('Invoice', '', 'laporan/invoicePengiriman', 'emodal fa-file-word-o');
        $crud->where('id_sumber', $this->owner);
		$crud->set_relation('id_tujuan','data_sumber','nama');
		$crud->set_relation('id_user','data_user','fullname');
        $crud->display_as('idh_pengiriman', 'Id Transaksi')
                ->display_as('id_user', 'Penanggung Jawab')
                ->display_as('id_tujuan', 'Tujuan');
        $crud->unset_add();
        $crud->unset_delete();
        $crud->unset_read();
        $crud->unset_edit();
        $output = $crud->render();
        $output->ket_header = $title;
        return $output;
    }
	
	function headerPenjualan() {
        $crud = new grocery_CRUD();
        $crud->set_table('h_penjualan');
        $crud->set_subject('Laporan Penjualan');
		$crud->set_primary_key('id_user','data_user');
		$crud->set_primary_key('id','data_sumber');
        $crud->columns('idh_penjualan', 'tgl', 'id_toko', 'id_user');
        $crud->add_action('Invoice', '', 'laporan/invoicePenjualan', 'emodal fa-file-word-o');
        $crud->where('id_toko', $this->owner);
		$crud->set_relation('id_toko','data_sumber','nama');
		$crud->set_relation('id_user','data_user','fullname');
        $crud->display_as('idh_penjualan', 'Id Transaksi')
                ->display_as('id_user', 'Penanggung Jawab')
                ->display_as('id_toko', 'Toko');
        $crud->unset_add();
        $crud->unset_delete();
        $crud->unset_read();
        $crud->unset_edit();
        $output = $crud->render();
        $output->ket_header = 'Invoice Penjualan';
        return $output;
    }
	
	function headerReturCustomer() {
        $crud = new grocery_CRUD();
        $crud->set_table('h_retur_customer');
        $crud->set_subject('Laporan Retur Customer');
		$crud->set_primary_key('id_user','data_user');
		$crud->set_primary_key('id','data_sumber');
        $crud->columns('idh_retur_customer', 'tgl_input', 'id_toko', 'id_user');
        $crud->add_action('Invoice', '', 'laporan/invoiceReturCustomer', 'emodal fa-file-word-o');
        $crud->where('id_toko', $this->owner);
		$crud->set_relation('id_toko','data_sumber','nama');
		$crud->set_relation('id_user','data_user','fullname');
        $crud->display_as('idh_retur_customer', 'Id Transaksi')
                ->display_as('id_user', 'Penanggung Jawab')
                ->display_as('id_toko', 'Toko');
        $crud->unset_add();
        $crud->unset_delete();
        $crud->unset_read();
        $crud->unset_edit();
        $output = $crud->render();
        $output->ket_header = 'Invoice Retur Customer';
        return $output;
    }
	//asdkflja
	function retur($title = null) {
        $crud = new grocery_CRUD();
        $crud->set_table('h_retur');
        $crud->set_subject('Laporan Retur Barang');
		$crud->set_primary_key('id_user','data_user');
		$crud->set_primary_key('id','data_sumber');
        $crud->columns('idh_retur', 'tgl_nota','id_retur', 'id_supliyer', 'id_user');
        $crud->add_action('Invoice', '', 'laporan/invoiceRetur', 'emodal fa-file-word-o');
        $crud->where('id_sumber', $this->owner);
		$crud->set_relation('id_retur','mst_retur','status');
		$crud->set_relation('id_supliyer','data_sumber','nama');
		$crud->set_relation('id_user','data_user','fullname');
        $crud->display_as('idh_retur', 'Id Transaksi')
                ->display_as('id_retur', 'Jenis Retur')
                ->display_as('id_user', 'Penanggung Jawab')
                ->display_as('id_supliyer', 'Tujuan Retur');
        $crud->unset_add();
        $crud->unset_delete();
        $crud->unset_read();
        $crud->unset_edit();
        $output = $crud->render();
        $output->ket_header = $title;
        return $output;
    }
	
	function returSesuatu($title = null,$sesuatu=null) {
		if(!empty($sesuatu)){
			$getIdRetur = $this->db->like(array('status'=>$sesuatu,'owner'=>$this->hakUser))->get('mst_retur')->row();
			$where = $getIdRetur->id_retur;
		}
        $crud = new grocery_CRUD();
        $crud->set_table('h_retur');
        $crud->set_subject('Laporan Retur Barang');
		$crud->set_primary_key('id_user','data_user');
		$crud->set_primary_key('id','data_sumber');
        $crud->columns('idh_retur', 'tgl_nota','id_retur', 'id_supliyer', 'id_user');
        $crud->add_action('Invoice', '', 'laporan/invoiceRetur', 'emodal fa-file-word-o');
        $crud->where('id_sumber', $this->owner);
        $crud->where('h_retur.id_retur', $where);
		$crud->set_relation('id_retur','mst_retur','status');
		$crud->set_relation('id_supliyer','data_sumber','nama');
		$crud->set_relation('id_user','data_user','fullname');
        $crud->display_as('idh_retur', 'Id Transaksi')
                ->display_as('id_retur', 'Jenis Retur')
                ->display_as('id_user', 'Penanggung Jawab')
                ->display_as('id_supliyer', 'Tujuan Retur');
        $crud->unset_add();
        $crud->unset_delete();
        $crud->unset_read();
        $crud->unset_edit();
        $output = $crud->render();
        $output->ket_header = $title;
        return $output;
    }
	
	function invoicePengembalianReturRusak() {
        $crud = new grocery_CRUD();
        $crud->set_table('invoice_retur_rusak');
        $crud->set_subject('Laporan Pengembalian Retur Rusak');
		$crud->set_primary_key('idh_retur_rusak');
		// $crud->set_primary_key('id','data_sumber');
        $crud->columns('idh_retur_rusak','no_nota', 'tgl_nota','idh_retur');
        $crud->add_action('Invoice', '', 'laporan/invoiceInputReturRusak', 'emodal fa-file-word-o');
        $crud->where('id_sumber', $this->owner);
        // $crud->where('h_retur.id_retur', $where);
		$crud->set_relation('id_retur','mst_retur','status');
		$crud->set_relation('id_supliyer','data_sumber','nama');
		$crud->set_relation('id_user','data_user','fullname');
        $crud->display_as('idh_retur', 'Id Transaksi')
                ->display_as('id_retur', 'Jenis Retur');
        $crud->unset_add();
        $crud->unset_delete();
        $crud->unset_read();
        $crud->unset_edit();
        $output = $crud->render();
        $output->ket_header = 'Laporan Pengembalian Retur Rusak';
        return $output;
    }
	
	function invoicePengembalianReturHarga() {
        $crud = new grocery_CRUD();
        $crud->set_table('invoice_retur_harga');
        $crud->set_subject('Laporan Pengembalian Retur Harga');
		$crud->set_primary_key('idh_retur_harga');
		// $crud->set_primary_key('id','data_sumber');
        $crud->columns('idh_retur_harga','no_nota', 'tgl_nota','idh_retur');
        $crud->add_action('Invoice', '', 'laporan/invoiceInputReturharga', 'emodal fa-file-word-o');
        $crud->where('id_sumber', $this->owner);
        // $crud->where('h_retur.id_retur', $where);
		$crud->set_relation('id_retur','mst_retur','status');
		$crud->set_relation('id_supliyer','data_sumber','nama');
		$crud->set_relation('id_user','data_user','fullname');
        $crud->display_as('idh_retur', 'Id Transaksi')
                ->display_as('id_retur', 'Jenis Retur');
        $crud->unset_add();
        $crud->unset_delete();
        $crud->unset_read();
        $crud->unset_edit();
        $output = $crud->render();
        $output->ket_header = 'Laporan Pengembalian Retur Harga';
        return $output;
    }
	

    function stockRentang() {
		// $this->general->testPre($_POST);
        $jenis_barang = $this->input->post('jenis_barang', true);
        $id_barang = $this->input->post('id_barang', true);
        $imey = $this->input->post('imey', true);
        $id_supliyer = $this->input->post('id_supliyer', true);
		$id_toko = $this->input->post('id_toko',true);
		$id_gudang = $this->input->post('id_gudang',true);
		$status_milik = $this->input->post('status_milik',true);
		if(!empty($id_toko)){
			$id_toko = $id_toko;
		}else if(!empty($id_gudang)){
			$id_toko = $id_gudang;
		}else{
			$id_toko = $this->owner;
		}
        if($jenis_barang == 'hp'){
            $this->db->where('b.nama_category', 'HANDPHONE');
        }else if($jenis_barang=='nonhp'){
            $this->db->where('b.nama_category !=', 'HANDPHONE');
        }else{
            $this->db->where('0', '0');
        }
        (!empty($id_barang)) ? $this->db->where('a.id_barang', $id_barang) : '';
        (!empty($imey)) ? $this->db->where('a.imey', $imey) : '';
        (!empty($id_supliyer)) ? $this->db->where('a.id_supliyer', $id_supliyer) : '';
        $this->db->select("count(*) as jumlah,b.nama as nama_barang, a.*");
		$this->db->where('a.id_toko',$id_toko);
        $this->db->group_by(array('a.id_toko', 'a.id_barang', 'a.status'));
        $this->db->join('mst_barang_detail b', 'a.id_barang = b.id_barang');
        
        $getStocks = $this->db->get_where('mst_stok a', array('a.status'=>0))->result();
        $data = array('stocks' => $getStocks,'imey'=>$imey);
        $this->load->view('laporan/stock/lap_stock_detail', $data);
		
    }
    function pengirimanRentang(){
        $status_kirim = $this->input->post('status_kirim',true);
        $status_reject = $this->input->post('status_reject',true);
        $id_transaksi = $this->input->post('id_transaksi',true);
        $tanggal = $this->input->post('tanggal_rentang',true);
        $tanggal_mulai = substr($tanggal, 0, 10);
        $tanggal_selesai = substr($tanggal, -10);
        $id_tujuan = $this->input->post('id_tujuan',true);
        $id_user = $this->input->post('id_user',true);
        ($status_kirim=='sampai')?$this->db->where('is_arrived',1) : ($status_kirim=='belumsampai')? $this->db->where('is_arrived',0) : $this->db->where(0,0);
        ($status_reject=='reject')?$this->db->where('is_reject',1) : ($status_reject=='nonreject')? $this->db->where('is_reject',0) : $this->db->where(0,0);
        (!empty($id_transaksi))? $this->db->where('idh_pengiriman',$id_transaksi) : '';
        (!empty($tanggal))?$this->db->where("date(tgl_input) between '$tanggal_mulai' and '$tanggal_selesai'") : '';
        (!empty($id_tujuan))?$this->db->where('id_tujuan',$id_tujuan):'';
        (!empty($id_user))?$this->db->where('id_user',$id_user):'';
        $getHeaders=$this->db->get_where('h_pengiriman',array('id_sumber'=>$this->owner))->result();
        $data = array('header' => $getHeaders);
        $this->load->view('laporan/pengiriman/lap_pengiriman_detail', $data);
    }
	
	function penjualanRentang($id_tokoo=null){
		// $this->general->testPre($_POST);
		
        $status_fisik = $this->input->post('status_fisik',true);
        $jenis_barang = $this->input->post('jenis_barang',true);
        $id_transaksi = $this->input->post('id_transaksi',true);
        $tanggal = $this->input->post('tanggal_rentang',true);
        $tanggal_mulai = substr($tanggal, 0, 10);
        $tanggal_selesai = substr($tanggal, -10);
        $id_customer = $this->input->post('id_customer',true);
        $id_user = $this->input->post('id_user',true);
        if($jenis_barang == 'hp'){
            $this->db->where('b.nama_category', 'HANDPHONE');
        }else if($jenis_barang=='nonhp'){
            $this->db->where('b.nama_category !=', 'HANDPHONE');
        }else{
            $this->db->where(0, 0);
        }
		if($status_fisik == 'fisik'){
            $this->db->where('b.jenis', 'FISIK');
			$this->db->join('d_penjualan c', 'a.idh_penjualan = c.idh_penjualan');
			$joinalias = 'c';
        }else if($status_fisik=='elektrik'){
            $this->db->where('b.jenis', 'ELEKTRIK');
			$this->db->join('d_penjualan_non_fisik e', 'a.idh_penjualan = e.idh_penjualan');
			$joinalias = 'e';
        }else{
			$fisikSemua = true;
            $this->db->where(0, 0);
			$this->db->join('d_penjualan c', 'a.idh_penjualan = c.idh_penjualan');
			$joinalias = 'c';
        }
        (!empty($id_transaksi))? $this->db->where('a.idh_penjualan',$id_transaksi) : '';
        (!empty($tanggal))?$this->db->where("date(tgl) between '$tanggal_mulai' and '$tanggal_selesai'") : '';
		(!empty($id_customer))?$this->db->where('id_customer',$id_customer):'';
        (!empty($id_user))?$this->db->where('id_user',$id_user):'';
		$this->db->group_by('a.idh_penjualan');
		//pengecekan apakah request berasal dari superadmin/gudang ato berasal dari toko yang bersangkutan
		//jika parameter id_tokoo tidak kosong artinya request berasal dari superadmin/gudang
		if(!empty($id_tokoo)){
			$fisikSemua = true;
			$status_fisik = 'semua';
			$jenis_barang = 'NonHp';
			if($id_tokoo=='semua'){
				//jika id_tokoo == semua artinya superadmin/gudang tidak memilih
				if($this->hakUser=='gudang'){
					//get id toko yang berada dalam gudang tersebut
					$getToko=$this->prosesGetTokoSegudang($this->owner);
					$tokoWhereIn = $this->general->idToInWhere($getToko,'id_toko');
					$whereIdToko = "id_toko in ($tokoWhereIn)";
				}else{
					$whereIdToko = "0=0";
				}
			}else{
				$whereIdToko = "id_toko = '$id_tokoo'";
			}
		}else{
			//jika parameter id_tokoo kosong artinya request berasal dari toko yang bersangkutan dan idWhere toko disetting ke session owner
			$idTokoWhere = $this->owner;
			$whereIdToko = "id_toko = '$idTokoWhere'";
		}
		
		$this->db->join('mst_barang_detail b', "b.id_barang =$joinalias.id_barang");
        $getHeaders=$this->db->group_by('a.idh_penjualan')->where($whereIdToko)->get('h_penjualan a')->result();
		
		//jika dinyalakan maka ketika ada transaksi non fisik dan fisik recordnya jadi double
		// if(@$fisikSemua){
			// $this->db->join('d_penjualan_non_fisik e', 'a.idh_penjualan = e.idh_penjualan');
			// $this->db->group_by('a.idh_penjualan');
			// $getHeaders2=$this->db->where($whereIdToko)->get('h_penjualan a')->result();
			// $getHeaders = array_merge($getHeaders,$getHeaders2);
		// }
		
		$data = array('header' => $getHeaders,'fisik'=>$status_fisik,'jenis_barang' => $jenis_barang);
        if($this->hakUser=='toko'){
			$templateDetail = 'laporan/penjualan/lap_penjualan_detail';
		}else{
			$templateDetail = 'laporan/penjualan/lap_penjualan_detail_superadmin';
		}
		$this->load->view($templateDetail, $data);

	}
function returRentang(){
	// $this->general->testPre($_POST);
        $status_retur = $this->input->post('status_retur',true);
        $jenis_retur = $this->input->post('id_retur',true);
        $id_transaksi = $this->input->post('id_transaksi',true);
        $tanggal = $this->input->post('tanggal_rentang',true);
        $tanggal_mulai = substr($tanggal, 0, 10);
        $tanggal_selesai = substr($tanggal, -10);
        $id_tujuan = $this->input->post('id_tujuan',true);
        $id_user = $this->input->post('id_user',true);
        
		($status_retur=='semua')?$this->db->where(0,0) : $this->db->where('is_replay',$status_retur);
        ($jenis_retur=='semua')?$this->db->where(0,0) : $this->db->where('id_retur',$jenis_retur);
        (!empty($id_transaksi))? $this->db->where('idh_retur',$id_transaksi) : '';
        (!empty($tanggal))?$this->db->where("date(tgl_input) between '$tanggal_mulai' and '$tanggal_selesai'") : '';
        (!empty($id_tujuan))?$this->db->where('id_supliyer',$id_tujuan):'';
        (!empty($id_user))?$this->db->where('id_user',$id_user):'';
        $getHeaders=$this->db->get_where('h_retur',array('id_sumber'=>$this->owner))->result();
        $data = array('header' => $getHeaders);
        $this->load->view('laporan/retur/lap_retur_detail', $data);
    }
	
	function prosesReturCustomer(){
        $id_transaksi = $this->input->post('id_transaksi',true);
        $tanggal = $this->input->post('tanggal_rentang',true);
        $tanggal_mulai = substr($tanggal, 0, 10);
        $tanggal_selesai = substr($tanggal, -10);
               
		
        (!empty($id_transaksi))? $this->db->where('idh_retur_customer',$id_transaksi) : '';
        (!empty($tanggal))?$this->db->where("date(tgl_input) between '$tanggal_mulai' and '$tanggal_selesai'") : '';
      
        $getHeaders=$this->db->get_where('h_retur_customer',array('id_toko'=>$this->owner))->result();
        $data = array('header' => $getHeaders);
        $this->load->view('laporan/retur/lap_retur_customer_detail', $data);
	}
	
	function getGudang($idCombo=null){
		if($this->hak_user == 'superadmin'){
			$getGudang=$this->db->get('mst_gudang')->result();
			$data = array('gudang'=>$getGudang);
			$this->load->view('laporan/stock/cmb_gudang',$data);
		}else if($this->hak_user == 'gudang'){
			if($idCombo != 'gudang'){
				$this->getToko($idCombo);
			}
		}else{
			$this->getToko($idCombo);
		}
	}
function getToko($idGudang=null){
		$getToko=$this->prosesGetTokoSegudang($idGudang);
		$data = array('toko'=>$getToko);
		$this->load->view('laporan/stock/cmb_toko',$data);
	}
	function prosesGetTokoSegudang($idGudang=null){
		// return $getToko=$this->db->get_where('mst_toko',array('id_gudang'=>$idGudang))->result();
		return $getToko=$this->db->query("select* from mst_toko where id_gudang = '$idGudang'")->result();
	}

}
