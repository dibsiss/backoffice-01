<?php
//functi untuk generate imey insertTempImey 1984
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi_model extends CI_Model {

    var $id_user;
    var $owner;
    var $tokoSegudang;
	var $hakUser;

    function __construct() {
        parent::__construct();
        $this->hakUser = @$this->session->userdata('hak_user'); //superadmin=1,2,3 toko user loginnya
        $this->id_user = @$this->session->userdata('id_user'); //superadmin=1,2,3 toko user loginnya
        $this->owner = @$this->session->userdata('owner'); //superadmin,g001,t001
        $this->tokoSegudang = @$this->session->userdata('toko_segudang');
        date_default_timezone_set("Asia/Jakarta");
    }
	function prosesMutasiAwal(){
		$id_toko = $this->input->post('id_tempat',true);
		$this->db->where('id_tempat',$id_toko)->delete('history_mutasi');//delete old data
		$query = "insert into history_mutasi(idhistory_mutasi,id_tempat,id_barang,jumlah,sisa_stok,id_user,idh_transaksi,idd_transaksi,jenis_trans,keterangan,harga) select uuid_short(),id_toko,id_barang,count(*),0,2,'transaksi Awal','Awal Stok','masuk','Input Awal Stok',harga_beli from mst_stok a where a.id_toko = '$id_toko' group by a.id_barang,a.id_toko";
		$cek=$this->db->query($query);
		if($cek){
			$hasil = "<br><div class='alert alert-success text-center'>Sukses Insert History Awal</div> ";
		}else{
			$hasil = "<br><div class='alert alert-danger text-center'>Gagal Insert History Awal</div>";
		}
		return $hasil;
	}
	// $this->insertHistory($gt->id_barang,$jumlah,$idh_pengiriman,$gt->idd_pengiriman,'keluar','d_pengirimannn',$gt->harga_jual,'','','is_reject');
	function insertHistory($id_barang=null,$jumlah=null,$idh_trans=null,$idd_trans=null,$jenis_trans=null,$keterangan=null,$harga=null,$argumen=null,$injectOwner=null,$is_reject=null){
		(!empty($injectOwner))? $owner = $injectOwner : $owner=$this->owner;
		$this->db->where(array('id_toko'=>$owner,'id_barang'=>$id_barang));
		$whereStatus = "status = 0";
		//untuk yang or where
		if(!empty($argumen)){
			$is_retur = $argumen['is_retur'];
			// $this->db->or_where('is_retur', $is_retur);
			$whereStatus = "(status = 0 or is_retur = '$is_retur')";
		}
		$this->db->where($whereStatus);
		$getSisa = $this->db->get('mst_stok')->result();
		$stok=count($getSisa);
		// die();
		if(!empty($is_reject)){
			$stok = 0;
			$jenis_trans='reject';
		}
		$params = array(
			'idhistory_mutasi'=>$this->general->genNumberTemp('history_mutasi', 'idhistory_mutasi', $this->id_user, 'THM', 4),
			'id_tempat'=>$owner,
			'id_barang'=>$id_barang,
			'jumlah'=>$jumlah,
			'sisa_stok'=>$stok,
			'id_user'=>$this->id_user,
			'idh_transaksi'=>$idh_trans,
			'idd_transaksi'=>$idd_trans,
			'jenis_trans'=>$jenis_trans,
			'keterangan'=>$keterangan,
			'harga'=>$harga
		);
		$this->db->insert('history_mutasi',$params);		
	}
	
	function insertHeaderReject($id=null){
		$keterangan = $this->input->post('keterangan',true);
		$getPengirimanReject = $this->db->get_where('h_pengiriman',array('idh_pengiriman'=>$id))->row();
		$params = array('idh_reject'=>$idh_reject=$this->general->genNumberTemp('h_reject', 'idh_reject', $this->id_user, 'TRJ', 4),
						'idh_pengiriman'=>$id,
						'tgl_reject'=>$getPengirimanReject->tgl_input,
						'id_sumber'=>$this->owner,
						'id_tujuan'=>$id_tujuan=$getPengirimanReject->id_sumber,
						'id_user'=>$this->id_user,
						'keterangan'=>$keterangan);
		$this->db->insert('h_reject',$params);
		$this->insertDetailReject($id,$idh_reject,$id_tujuan);
	}
	function insertDetailReject($idh_pengiriman=null,$idh_reject=null,$id_tujuan=null){
		$getDetailPengirimanReject=$this->db->select("a.*,count(*) as jumlah_hp")->group_by('a.id_barang')->get_where('d_pengiriman a', array('a.idh_pengiriman' => $idh_pengiriman,'is_trouble'=>1))->result();;
		//$getDetailPengirimanReject=$this->db->get_where('d_pengiriman',array('idh_pengiriman'=>$idh_pengiriman,'is_trouble'=>1))->result();
		foreach($getDetailPengirimanReject as $gr){
			$params= array('idd_reject'=>$idd_reject=$this->general->genNumberTemp('d_reject', 'idd_reject', $this->id_user, 'DRJ', 4),
							'idh_reject'=>$idh_reject,
							'id_barang'=>$id_barang=$gr->id_barang,
							'harga_jual'=>$harga=$gr->harga_jual
			);
			$this->db->insert('d_reject',$params);
			//insert imey reject
			if($gr->imey=='imey_pengiriman'){
				//artinya barangnya bertipe non hp, get imey berasal dari tabel imey pengiriman
				$getImey= $this->db->get_where('imey_pengiriman',array('idd_pengiriman'=>$gr->idd_pengiriman,'is_trouble'=>1))->result();
				//jumlah untuk history
				$jumlahBarang = count($getImey);
			}else{
				//artinya barang bertipe hp dan get imey berasal dari tabel d_pengiriman
				$getImey= $this->db->get_where('d_pengiriman',array('idd_pengiriman'=>$gr->idd_pengiriman,'is_trouble'=>1))->result();
				//jumlah untuk history
				$jumlahBarang = $gr->jumlah_hp;
			}
			//insert history 
			$this->insertHistory($id_barang,$jumlahBarang,$idh_reject,$idd_reject,'masuk','tr_reject',@$harga,'');
			//proses insert ke imey reject
			foreach($getImey as $gi){
					$params2 = array(
						'idimey_reject'=>$idimey_reject=$this->general->genNumberTemp('imey_reject', 'idimey_reject', $this->id_user, 'IRJ', 4),
						'idd_reject'=>$idd_reject,
						'imey'=>$imey=$gi->imey,
					);
					$this->db->insert('imey_reject',$params2);
					//update mst stok biar available
					$this->db->set('is_retur',0)->set('status',0)->where(array('id_toko'=>$id_tujuan,'id_barang'=>$id_barang,'imey'=>$imey))->update('mst_stok');
				}
			
		}
	}
	function tandaiReject($id=null){
		$this->insertHeaderReject($id);
		//set is apliyed biar ndak keluar di notifikasi
		$this->db->set('is_appliyed', $this->id_user)->where('idh_pengiriman', $id)->update('h_pengiriman');
	}
	
	function transaksiLain(){
		$crud = new grocery_CRUD();
        $crud->set_table('tr_lain');
        $crud->set_subject('Transaksi Lain-Lain');
        $crud->field_type('idtr_lain', 'invisible');
        $crud->field_type('id_toko', 'invisible');
        $crud->field_type('id_user', 'invisible');
        $crud->field_type('tgl', 'invisible');
        $crud->callback_before_insert(array($this, 'genLain'));
        $crud->columns('kegiatan', 'jenis', 'nominal', 'keterangan');
		$crud->set_rules('nominal','Nominal','integer');
        $crud->where('id_toko', $this->owner);
		$crud->unset_edit();
		$crud->unset_delete();
        $crud->where('id_user', $this->id_user);
        $crud->required_fields('kegiatan', 'jenis', 'nominal', 'keterangan');
        $output = $crud->render();
        $output->ket_header = 'Transaksi Lain-Lain';
		$this->templateNotif($output);
        // return $output;
	}
	function genLain($post_array) {
        $post_array['idtr_lain'] = $this->general->genNumberTemp('tr_lain', 'idtr_lain', $this->id_user, 'TRL', 4);
        $post_array['id_toko'] = $this->owner;
        $post_array['id_user'] = $this->id_user;
        return $post_array;
    }
	function setKoreksi(){
		$id_barang = $this->input->post('id_barang',true);
		$keterangan = $this->input->post('keterangan');
		$jenis = $this->input->post('jenis',true);
		$koreksi = $this->input->post('koreksi',true);
		$getJumlah=$this->general->idToInWherePrimitif($koreksi);
		$params = array(
			'idtr_koreksi'=>$idtr_koreksi = $this->general->genNumberTemp('tr_koreksi', 'idtr_koreksi', $this->id_user, 'TRK', 4),
			'id_barang' => $id_barang,
			'keterangan' => $keterangan,
			'id_pemilik' => $this->owner,
			'id_user' => $this->id_user
		);
		//insert into db tr koreksi
		$this->db->insert('tr_koreksi',$params);
		//insert ke imey koreksi
		//jika barang bukan hp
		if($jenis==0){
			$jumlah = str_replace("'","",$getJumlah);
			$getImey = $this->db->get_where('mst_stok',array('id_toko'=>$this->owner,'id_barang'=>$id_barang,'status'=>0,'is_retur'=>0),$jumlah)->result();//$getJumlah untuk limit
		}else{
			$getImey = $this->db->where_in('imey',$getJumlah,false)->get_where('mst_stok',array('id_toko'=>$this->owner,'id_barang'=>$id_barang,'status'=>0,'is_retur'=>0))->result();//$getJumlah untuk limit
		}
		//insert to imey koreksi
		foreach($getImey as $key=>$val){
			$params2 = array(
				'idimey_koreksi'=>$idimey_koreksi=$this->general->genNumberTemp('imey_koreksi', 'idimey_koreksi', $this->id_user, 'IMK', 4),
				'idtr_koreksi'=>$idtr_koreksi,
				'imey'=>$imeyUpdate[]=$val->imey
			);
			$this->db->insert('imey_koreksi',$params2);
			$hargaBeli = $val->harga_beli;
		}
		
		//insert ke log history
		$this->insertHistory($id_barang,count($getImey),$idtr_koreksi,$idtr_koreksi,'keluar','tr_koreksi',@$hargaBeli,'');
		//insert ke log history diinsert ke log dulu baru diproses karena untuk get datanya
		//update mst stok set status dan is retur
		$updateImey=$this->general->idToInWherePrimitif($imeyUpdate);
		$this->db->where_in('imey',$updateImey,false)->set(array('status'=>4,'is_retur'=>$idtr_koreksi))->where(array('id_toko'=>$this->owner,'id_barang'=>$id_barang,'status'=>0,'is_retur'=>0))->update('mst_stok');
		
	}
	
	function setReady($id_toko=null,$id_barang=null,$imey=null){
		$this->db->set(array('status'=>0, 'is_retur'=>0))->where(array('id_toko'=>$id_toko,'id_barang'=>$id_barang,'imey'=>$imey))->update('mst_stok');
	}
    function showImeyReturRusak($idh_retur = null, $id_barang = null, $is_hp = null) {
        $this->load->view('tr_input_retur_rusak/imey_retur_rusak');
    }

    function showTableReturRusak($idh_retur = null, $id_barang = null, $is_hp = null) {
        $getDetails = $this->db->get_where('d_temp_retur_rusak', array('idtemp_retur_rusak' => $idh_retur))->result();
        $data = array('isi' => $getDetails, 'is_hp' => $is_hp);
        $this->load->view('tr_input_retur_rusak/table_retur_rusak', $data);
    }

    function insertReturHarga() {
        $this->form_validation->set_rules('nota_penerimaan_harga', 'Nota Penerimaan', 'required');
        $this->form_validation->set_rules('tgl_nota', 'Tanggal Nota', 'required');
        $this->form_validation->set_rules('hargaBaru[]', 'Harga Baru', 'required|is_natural');
        if ($this->form_validation->run() == true) {
            $idh_retur = $this->prosesSimpanReturHarga();
            $this->db->set('is_replay', 1)->where('idh_retur', $idh_retur['idh_retur'])->update('h_retur');
            $data = array('success' => 1, 'message' => 'sukses','idh_retur_harga'=>$idh_retur['idh_retur_harga']);
        } else {
            $data = array('success' => 0, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

    function insertReturCustomer() {
        $this->form_validation->set_rules('nota_penerimaan_customer', 'Nota Penerimaan', 'required');
        $this->form_validation->set_rules('tgl_nota', 'Tanggal Nota', 'required');
        if ($this->form_validation->run() == true) {
            $idh_retur_customer = $this->prosesSimpanReturCustomer();
            $data = array('success' => 1,'idh_retur_customer'=>$idh_retur_customer, 'message' => 'sukses');
        } else {
            $data = array('success' => 0, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

    function prosesSimpanReturCustomer() {
        $no_nota = $this->input->post('nota_penerimaan_customer', true);
        $tgl = $this->input->post('tgl_nota', true);
        $idh_penjualan = $this->input->post('idh_penjualan', true);
        //get header
        $idh_retur_customer = $this->prosesSimpanHeaderReturCustomer($idh_penjualan, $no_nota, $tgl);
        $idd_retur_customer = $this->prosesSimpanDetailReturCustomer($idh_penjualan, $idh_retur_customer);
        //delete temporari
        $this->db->delete('temp_retur_customer', array('id_user' => $this->id_user, 'idh_penjualan' => $idh_penjualan));
		return $idh_retur_customer;
	}

    function prosesSimpanHeaderReturCustomer($idh_penjualan = null, $no_nota = null, $tgl_nota = null) {
        $params = array('idh_retur_customer' => $id_retur_customer = $this->general->genNumberTemp('h_retur_customer', 'idh_retur_customer', $this->id_user, 'HRC', 4),
            'idh_penjualan' => $idh_penjualan,
            'no_nota' => $no_nota,
            'tgl_nota' => $tgl_nota,
            'id_toko' => $this->owner,
            'id_user' => $this->id_user,
            'is_replay' => 0
        );
        $this->db->insert('h_retur_customer', $params);
        return $id_retur_customer;
    }

    function prosesSimpanDetailReturCustomer($idh_penjualan = null, $idh_retur_customer) {
        //getidh temporari
        $getHTemp = $this->db->get_where('temp_retur_customer', array('id_user' => $this->id_user, 'idh_penjualan' => $idh_penjualan))->row();
        //get Detail temporari
        $getDTemp = $this->db->get_where('d_temp_retur_customer', array('idtemp_retur_customer' => @$getHTemp->idtemp_retur_customer))->result();
        foreach ($getDTemp as $de) {
            $params = array('idd_retur_customer' => $idd_retur_customer = $this->general->genNumberTemp('d_retur_customer', 'idd_retur_customer', $this->id_user, 'DRC', 4),
                'idh_retur_customer' => $idh_retur_customer,
                'id_barang' => $id_barang=$de->id_barang,
                'keterangan' => $de->keterangan
            );
            $this->db->insert('d_retur_customer', $params);
            $this->prosesSimpanImeyReturCustomer($idh_retur_customer,$idd_retur_customer,$id_barang, $de->imey, $de->harga);
        }
    }

    function prosesSimpanImeyReturCustomer($idh_retur_customer=null,$idd_retur_customer = null, $id_barang=null, $imey = null, $harga = null) {
        $params = array('idimey_retur_customer' => $idimey_retur_customer = $this->general->genNumberTemp('imey_retur_customer', 'idimey_retur_customer', $this->id_user, 'IRC', 4),
            'idd_retur_customer' => $idd_retur_customer,
            'imey' => $imey,
            'harga' => $harga
        );
        $this->db->insert('imey_retur_customer', $params);
		$this->insertHistory($id_barang,1,$idh_retur_customer,$idd_retur_customer,'masuk','d_retur_customer',$harga,'');//1 adalah jumlah barang yang diretur
		$this->db->set(array('status'=>0,'is_retur'=>'0'))->where(array('id_toko'=>$this->owner,'id_barang'=>$id_barang,'imey'=>$imey))->update('mst_stok');
    }

    function insertReturRusak() {
        $this->form_validation->set_rules('nota_penerimaan_rusak', 'Nota Penerimaan', 'required');
        $this->form_validation->set_rules('tgl_nota', 'Tanggal Nota', 'required');
        if ($this->form_validation->run() == true) {
            $idh_retur = $this->prosesSimpanReturRusak();
            $this->db->set('is_replay', 1)->where('idh_retur', $idh_retur['idh_retur'])->update('h_retur');
            $data = array('success' => 1, 'message' => 'sukses','idh_retur_rusak'=>$idh_retur['idh_retur_rusak']);
        } else {
            $data = array('success' => 0, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

    function prosesSimpanReturRusak() {
        $idh_retur = $this->input->post('idh_retur', true);
        $nota = $this->input->post('nota_penerimaan_rusak', true);
        $tgl_nota = $this->input->post('tgl_nota', true);
        $id_supliyer = $this->input->post('id_supliyer', true);
        //save header
        $idh_retur_rusak = $this->prosesSimpanHeaderReturRusak($idh_retur, $nota, $tgl_nota, $id_supliyer);
        //get header
        $header = $this->db->get_where('temp_retur_rusak', array('id_user' => $this->id_user, 'idh_retur' => $idh_retur))->result();
        foreach ($header as $hd) {
            $isHp = $this->master->isHp($hd->id_barang);
            $detail = $this->prosesSimpanDetailReturRusak($idh_retur, $idh_retur_rusak, $hd->id_barang, $id_supliyer);
            $this->prosesSimpanImeyRetur_rusak($detail['idd_retur_rusak'], $hd->idtemp_retur_rusak, $id_supliyer, $hd->id_barang, $idh_retur, $isHp['is_hp']);
        }
        return array('idh_retur'=>$idh_retur,'idh_retur_rusak'=>$idh_retur_rusak);
    }
	
	function prosesSimpanDetailReturRusak($idh_retur = null, $idh_retur_rusak = null, $id_barang = null, $id_supliyer) {
        $params = array('idd_retur_rusak' => $idd_retur_rusak = $this->general->genNumberTemp('d_retur_rusak', 'idd_retur_rusak', $this->id_user, 'DRS', 4),
            'idh_retur_rusak' => $idh_retur_rusak,
            'id_barang' => $id_barang
        );
        $this->db->insert('d_retur_rusak', $params);
        $data = array('idd_retur_rusak' => $idd_retur_rusak);
        return $data;
    }

    function prosesSimpanImeyRetur_rusak($idd_retur_rusak = null, $idtemp_retur_rusak = null, $id_supliyer = null, $id_barang_awal = null, $idh_retur = null, $isHp = null) {
        //logika digroup dulu baru kemudian diselect berdasarkan group tersebut
        //untuk menentukan harga
        $getJumlahBarang = $this->db->select('*,count(*) as jumlah_barang')->group_by('id_barang_kembalian')->get_where("d_temp_retur_rusak", array('idtemp_retur_rusak' => $idtemp_retur_rusak))->result();
        foreach ($getJumlahBarang as $g) {
            $jumlahBarang = $g->jumlah_barang;
            $id_barang = $g->id_barang_kembalian;
            $harga_lama = $g->harga;
            $harga = $this->hargaBeli($this->owner, $id_barang, $jumlahBarang, $harga_lama);
            $this->prosesDetailImeyReturRusak($harga_lama, $harga, $id_barang, $idtemp_retur_rusak, $idd_retur_rusak, $id_supliyer, $idh_retur, $isHp);
			//insert histori mutasi
			$this->insertHistory($id_barang,$jumlahBarang,$idtemp_retur_rusak,$idd_retur_rusak,'masuk','d_retur_rusak',$harga,'');
			//insert histori mutasi
		}
    }

    function prosesDetailImeyReturRusak($harga_lama = null, $harga = null, $id_barang = null, $idtemp_retur_rusak = null, $idd_retur_rusak = null, $id_supliyer = null, $idh_retur = null, $isHp = null) {
        //get imey from temporari
        $imeys = $this->db->get_where("d_temp_retur_rusak", array('id_barang_kembalian' => $id_barang, 'idtemp_retur_rusak' => $idtemp_retur_rusak))->result();
        foreach ($imeys as $im) {
            $imey = $im->imey;
            ($imey == 'imeyNonHp') ? $imey = $this->general->imeyNonHp($this->owner, $id_barang) : $imey = $imey;
            $params = array('idimey_retur_rusak' => $idimey_retur_rusak = $this->general->genNumberTemp('imey_retur_rusak', 'idimey_retur_rusak', $this->id_user, 'IRS', 4),
                'idd_retur_rusak' => $idd_retur_rusak,
                'jenis_kembalian' => $im->jenis_kembalian,
                'id_barang_kembalian' => $im->id_barang_kembalian,
                'harga_beli_lama' => @$harga_lama,
                'harga_beli_baru' => @$harga,
                'imey' => $imey
            );
            $this->db->insert('imey_retur_rusak', $params);
            //update stok
            $idd_temp = $im->idd_temp_retur_rusak;
            $this->updateStokReturRusak($id_supliyer, $id_barang, $harga, $imey, $idh_retur, $idd_temp, $isHp);
        }
    }

    function updateStokReturRusak($id_supliyer = null, $id_barang = null, $harga = null, $imey = null, $idh_retur = null, $idd_temp = null, $isHp = null) {
        $cekExist = $this->db->get_where('mst_stok', array('id_toko' => $this->owner, 'id_supliyer' => $id_supliyer, 'id_barang' => $id_barang, 'imey' => $imey, 'is_retur' => $idh_retur))->row();
//        $isHp = $this->master->isHp($id_barang)['is_hp'];
        //jika kosong maka insert dengan data baru
        if (empty($cekExist)) {
            //insert baru
            $this->db->query("insert into mst_stok values('$this->owner','$id_barang','$id_supliyer','$imey',0,'$harga',0,'$isHp')");
        } else { // jika sudah ada maka update data lama menjadi available
            //update barang lama set status menjadi available
            $this->db->query("update mst_stok set status = 0, is_retur = 0 where id_toko = '$this->owner' and id_barang = '$id_barang' and id_supliyer = '$id_supliyer' and imey = '$imey' and is_retur = '$idh_retur' ");
        }
        //update harga barang lama yang statusnya masih tersedia
        $this->db->set('harga_beli', $harga)->where(array('status' => 0, 'is_retur' => 0, 'id_toko' => $this->owner, 'id_barang' => $id_barang))->update("mst_stok");
    }

    

    function prosesSimpanHeaderReturRusak($idh_retur = null, $nota = null, $tgl_nota = null, $id_supliyer = null) {
        $params = array(
            'idh_retur_rusak' => $idh_retur_rusak = $this->general->genNumberTemp('h_retur_rusak', 'idh_retur_rusak', $this->id_user, 'HRS', 4),
            'id_supliyer' => $id_supliyer,
            'no_nota' => $nota,
            'tgl_nota' => $tgl_nota,
            'idh_retur' => $idh_retur
        );
        $this->db->insert('h_retur_rusak', $params);
        return $idh_retur_rusak;
    }

    function prosesSimpanReturHarga() {
        //simpan header
        $idh_retur_harga = $this->simpanHeaderReturHarga();
        $this->simpanDetailReturHarga($idh_retur_harga['idh_retur_harga'], $idh_retur_harga['idh_retur']);
        return $idh_retur_harga;
    }

    function simpanDetailReturHarga($idh_retur_harga = null, $idh_retur = null) {
        $hargaBaru = $this->input->post('hargaBaru');
        foreach ($hargaBaru as $b => $h) {
            //get harga lama
            $hargaLamas = $this->db->get_where('mst_stok', array('id_barang' => $b, 'is_retur' => $idh_retur));
            $hargaLama = @$hargaLamas->row()->harga_beli;
            $params = array('idd_retur_harga' => $idd_retur_harga = $this->general->genNumberTemp('d_retur_harga', 'idd_retur_harga', $this->id_user, 'DRH', 4),
                'idh_retur_harga' => $idh_retur_harga,
                'id_barang' => $b,
                'harga_beli_lama' => $hargaLama,
                'harga_beli_baru' => $h
            );
            $this->db->insert('d_retur_harga', $params);
			//insert into log history
			$this->insertHistory($b,count($hargaLamas->result()),$idh_retur_harga,$idd_retur_harga,'masuk','d_retur_harga',$h,'');
			//insert into log history
            //insert imey retur harga
            foreach ($hargaLamas->result() as $imeyRetur) {
                $params2 = array('idimey_retur_harga' => $this->general->genNumberTemp('imey_retur_harga', 'idimey_retur_harga', $this->id_user, 'IRH', 4),
                    'idd_retur_harga' => $idd_retur_harga,
                    'imey' => $imeyRetur->imey);
                $this->db->insert('imey_retur_harga', $params2);
            };
            //update mst_stok, set status barang available, hapus status retur dan update harga beli baru
            $this->db->query("update mst_stok set status = 0, is_retur = 0,harga_beli = '$h' where id_barang = '$b' and is_retur = '$idh_retur'");
        };
    }

    function simpanHeaderReturHarga() {
        $id_supliyer = $this->input->post('id_supliyer');
        $no_nota = url_title($this->input->post('nota_penerimaan_harga'));
        $tgl_nota = $this->input->post('tgl_nota');
        $idh_retur = $this->input->post('idh_retur');
        $params = array('idh_retur_harga' => $idh_retur_harga = $this->general->genNumberTemp('h_retur_harga', 'idh_retur_harga', $this->id_user, 'HRH', 4),
            'id_supliyer' => $id_supliyer,
            'no_nota' => $no_nota,
            'tgl_nota' => $tgl_nota,
            'idh_retur' => $idh_retur
        );
        $this->db->insert('h_retur_harga', $params);
        $data = array('idh_retur_harga' => $idh_retur_harga, 'idh_retur' => $idh_retur);
        return $data;
    }

    function showTempReturCustomer($id_temp = null, $idh_penjualan = null, $status = null) {
        $idtemp_retur_customer = $id_temp;
        if ($status == 'awal') {
            $message = "";
            $idtemp_retur_customer = $this->prosesInputHeaderReturCustomer($idh_penjualan);
        } else if ($status == 'kedua') {
            $message = "Masih Ada Transaksi Yang Belum Diselesaikan, Transaksi Yang ditampilkan Adalah Transaksi Tersebut, Jika Ingin Membuat Transaksi Baru Silahkan Tekan Tombol Cancel Terlebih Dahulu";
        }

        $hasil = $this->prosesGetReturCustomer($idh_penjualan);
        $data = array('header' => $hasil['header'], 'idtemp_retur_customer' => $idtemp_retur_customer, 'idh_penjualan' => @$idh_penjualan, 'pesan' => @$message);
        $this->load->view('tr_retur_customer/detail_retur_customer', $data);
    }

    function prosesInputHeaderReturCustomer($idh_penjualan = null) {
        $params = array('idtemp_retur_customer' => $idtemp_retur_customer = $this->general->genNumberTemp('temp_retur_customer', 'idtemp_retur_customer', $this->id_user, 'TRC', 4),
            'id_user' => $this->id_user,
            'idh_penjualan' => $idh_penjualan
        );
        $this->db->insert('temp_retur_customer', $params);
        return $idtemp_retur_customer;
    }

    function showReturCustomer($id_retur = null) {
        $hasil = $this->prosesGetReturCustomer($id_retur);
        $data = array('header' => $hasil['header']);
        $this->load->view('tr_retur_customer/temp_retur_customer', $data);
    }

    function prosesGetReturCustomer($id_retur = null) {
        $dataHeader = $this->db->get_where('h_penjualan', array('idh_penjualan' => $id_retur, 'id_toko' => $this->owner))->row();
        return $data = array('header' => $dataHeader);
    }

    function showReturHarga($id_retur = null) {
        $dataHeader = $this->db->get_where('h_retur', array('idh_retur' => $id_retur, 'is_replay' => 0, 'id_sumber' => $this->owner))->row();
        $data = array('header' => $dataHeader);
        $this->load->view('tr_input_retur_harga/detail_retur_harga', $data);
    }

    function showReturRusak($id_retur = null) {
        $dataHeader = $this->db->get_where('h_retur', array('idh_retur' => $id_retur, 'is_replay' => 0, 'id_sumber' => $this->owner))->row();
        $data = array('header' => $dataHeader);
        $this->load->view('tr_input_retur_rusak/temp_retur_rusak', $data);
    }

    function showTempReturRusak($id_retur = null, $status = null) { //status digunakan untuk proses insert ke temporari ato tidak => jika status awal maka insert jika status kedua maka tak usah
        if ($status == 'awal') {
            $this->insertTempReturRusak($id_retur);
        } else {
            //else digunakan untuk menampilkan pesan bahwa masih ada proses transaksi yang belum selesai dan harus diselesaikan terlebih dahulu
            $message = "Masih Ada Transaksi Yang Belum Diselesaikan, Transaksi Yang ditampilkan Adalah Transaksi Tersebut, Jika Ingin Membuat Transaksi Baru Silahkan Tekan Tombol Cancel Terlebih Dahulu";
        }
        $dataHeader = $this->db->get_where('h_retur', array('idh_retur' => $id_retur, 'is_replay' => 0, 'id_sumber' => $this->owner))->row();
        // $this->general->testPre($dataHeader);
		$data = array('header' => $dataHeader, 'idh_retur' => $id_retur, 'pesan' => @$message);
        $this->load->view('tr_input_retur_rusak/detail_retur_rusak', $data);
    }

    function deleteTempReturRusak($idh_retur = null) {
        //cek apakah user yang sedang aktif adalah user yang berhak menghapus temp
        $cekAkses = $this->db->get_where("temp_retur_rusak", array('idh_retur' => $idh_retur, 'id_user' => $this->id_user))->row();
        if (!empty($cekAkses)) {
            $this->db->delete("temp_retur_rusak", array('idh_retur' => $idh_retur, 'id_user' => $this->id_user));
            $hasil = array("sukses" => true, "pesan" => "");
        } else {
            $hasil = array("sukses" => false, "pesan" => "Anda Tidak Berhak Menghapus Data Ini");
        }
        $return = json_encode($hasil);
        return $return;
    }

    function deleteStatusRetur($idtemp_retur_customer = null, $idh_penjualan = null) {
        $getDetail = $this->db->get_where('d_temp_retur_customer', array('idtemp_retur_customer' => $idtemp_retur_customer))->result();
        if (!empty($getDetail)) {
            foreach ($getDetail as $gd) {
                //artinya barang tersebut non fisik
                if ($gd->harga != 0) {
                    $this->db->set('is_retur', 0)->where(array('idh_penjualan' => $idh_penjualan, 'id_barang' => $gd->id_barang, 'nomer' => $gd->imey, 'harga' => $gd->harga))->update('d_penjualan_non_fisik');
                } else {
                    //get id detail penjualan
                    $getIdDetail = $this->db->query("select a.idh_penjualan,a.id_barang,b.imey,b.idimey_penjualan from d_penjualan a
                    inner join imey_penjualan b on a.idd_penjualan = b.idd_penjualan
                    where b.imey = '$gd->imey' and a.idh_penjualan = '$idh_penjualan' and a.id_barang = '$gd->id_barang'")->row();
                    if (!empty($getDetail)) {
                        $this->db->set('is_retur', 0)->where('idimey_penjualan', $getIdDetail->idimey_penjualan)->update('imey_penjualan');
                    }
                }
            }
        }
    }

    function deleteTempReturCustomer($idh_penjualan = null) {
        //cek apakah user yang sedang aktif adalah user yang berhak menghapus temp
        $cekAkses = $this->db->get_where("temp_retur_customer", array('idh_penjualan' => $idh_penjualan, 'id_user' => $this->id_user))->row();
        if (!empty($cekAkses)) {
            //update status barang
            $this->deleteStatusRetur($cekAkses->idtemp_retur_customer, $cekAkses->idh_penjualan);
            $this->db->delete("temp_retur_customer", array('idh_penjualan' => $idh_penjualan, 'id_user' => $this->id_user));
            $hasil = array("sukses" => true, "pesan" => "");
        } else {
            $hasil = array("sukses" => false, "pesan" => "Anda Tidak Berhak Menghapus Data Ini");
        }
        $return = json_encode($hasil);
        return $return;
    }

    function insertDetailReturCustomer() {
        $idimey_penjualan = $this->input->post('idimey_penjualan', true);
        $status = $this->input->post('status', true);
		$getHargaBarang = @$this->db->get_where('harga_jual_barang',array('idimey_penjualan'=>$idimey_penjualan))->row();
        if ($status == 0) {
            $params = array('idd_temp_retur_customer' => $this->general->genNumberTemp('d_temp_retur_customer', 'idd_temp_retur_customer', $this->id_user, 'DRC', 4),
                'idtemp_retur_customer' => $this->input->post('idhtemp', true),
                'id_barang' => $this->input->post('id_barang', true),
                'imey' => $this->input->post('imey', true),
                'harga' => @$getHargaBarang->harga_jual,
                'keterangan' => $this->input->post('keterangan', true)
            );
            $this->db->insert('d_temp_retur_customer', $params);
            //update status imey retur di tabel imey penjualan
            $this->db->set('is_retur', 1)->where('idimey_penjualan', $idimey_penjualan)->update('imey_penjualan');
        }
        return true;
    }

    function insertDetailReturCustomerNonFisik() {
        $idimey_penjualan = $this->input->post('idimey_penjualan', true);
        $status = $this->input->post('status', true);
        if ($status == 0) {
            $params = array('idd_temp_retur_customer' => $this->general->genNumberTemp('d_temp_retur_customer', 'idd_temp_retur_customer', $this->id_user, 'DRC', 4),
                'idtemp_retur_customer' => $this->input->post('idhtemp', true),
                'id_barang' => $this->input->post('id_barang', true),
                'imey' => $this->input->post('imey', true),
                'harga' => $this->input->post('harga', true),
                'keterangan' => $this->input->post('keterangan', true)
            );
            $this->db->insert('d_temp_retur_customer', $params);
            //update status imey retur di tabel imey penjualan
            $this->db->set('is_retur', 1)->where('idd_penjualan', $idimey_penjualan)->update('d_penjualan_non_fisik');
        }
        return true;
    }

    function unistallDetailReturCustomer() {
        $idimey_penjualan = $this->input->post('idimey_penjualan', true);
        $idtemp_retur_customer = $this->input->post('idhtemp', true);
        $id_barang = $this->input->post('id_barang', true);
        $imey = $this->input->post('imey', true);
        $jenis = $this->input->post('jenis', true);
        //diselect dulu jika ada maka hapus, jika tidak ada maka kemungkinan returnya sudah dilakukan dilain waktu jadi harusnya tidak bisa
        $cekExist = $this->db->get_where('d_temp_retur_customer', array('id_barang' => $id_barang, 'imey' => $imey, 'idtemp_retur_customer' => $idtemp_retur_customer))->row();
        //hapus detail
        if (!empty($cekExist)) {
            $this->db->where(array('id_barang' => $id_barang, 'imey' => $imey, 'idtemp_retur_customer' => $idtemp_retur_customer))->delete('d_temp_retur_customer');
            //update status imey retur di tabel imey penjualan
            if ($jenis == 'fisik') {
                $this->db->set('is_retur', 0)->where('idimey_penjualan', $idimey_penjualan)->update('imey_penjualan');
            } else {
                $this->db->set('is_retur', 0)->where('idd_penjualan', $idimey_penjualan)->update('d_penjualan_non_fisik');
            }
        }
    }

    function prosesSejenisReturRusak($is_hp = null, $idh_retur = null, $id_barang = null, $imey = null) {
        $getHretur = $this->db->get_where('temp_retur_rusak',array('idtemp_retur_rusak'=>$idh_retur))->row();
        $getHargaBarang = $this->db->get_where('mst_stok', array('id_toko' => $this->owner, 'id_barang' => $id_barang, 'is_retur' => $getHretur->idh_retur))->row();
        if ($is_hp == 1) {
            $params4 = array(
                'idd_temp_retur_rusak' => $iddtemp_retur_rusak = $this->general->genNumberTemp('d_temp_retur_rusak', 'idd_temp_retur_rusak', $this->id_user, 'IRS', 4),
                'idtemp_retur_rusak' => $idh_retur,
                'jenis_kembalian' => 'sejenis',
                'id_barang_kembalian' => $id_barang,
                'harga' => $getHargaBarang->harga_beli,
                'imey' => url_title($imey));
            $this->db->insert('d_temp_retur_rusak', $params4);
        } else {
            if ($imey > 0) {
                for ($i = 1; $i <= $imey; $i++) {
                    $params = array(
                        'idd_temp_retur_rusak' => $iddtemp_retur_rusak = $this->general->genNumberTemp('d_temp_retur_rusak', 'idd_temp_retur_rusak', $this->id_user, 'IRS', 4),
                        'idtemp_retur_rusak' => $idh_retur,
                        'jenis_kembalian' => 'sejenis',
                        'harga' => $getHargaBarang->harga_beli,
                        'id_barang_kembalian' => $id_barang,
                        'imey' => 'imeyNonHp'); //tempimeypalsu
                    $this->db->insert('d_temp_retur_rusak', $params);
                }
            }
        }
    }

    function prosesLainJenisReturRusak($is_hp = null, $idh_retur = null, $imey = null, $id_barang = null, $harga = null) {
        if ($is_hp == 1) {
            $params4 = array(
                'idd_temp_retur_rusak' => $iddtemp_retur_rusak = $this->general->genNumberTemp('d_temp_retur_rusak', 'idd_temp_retur_rusak', $this->id_user, 'IRS', 4),
                'idtemp_retur_rusak' => $idh_retur,
                'jenis_kembalian' => 'lain_jenis',
                'id_barang_kembalian' => $id_barang,
                'harga' => $harga,
                'imey' => url_title($imey));
            $this->db->insert('d_temp_retur_rusak', $params4);
        } else {
            if ($imey > 0) {
                for ($i = 1; $i <= $imey; $i++) {
                    $params = array(
                        'idd_temp_retur_rusak' => $iddtemp_retur_rusak = $this->general->genNumberTemp('d_temp_retur_rusak', 'idd_temp_retur_rusak', $this->id_user, 'IRS', 4),
                        'idtemp_retur_rusak' => $idh_retur,
                        'jenis_kembalian' => 'lain_jenis',
                        'id_barang_kembalian' => $id_barang,
                        'harga' => $harga,
                        'imey' => 'imeyNonHp'); //tempimeypalsu
                    $this->db->insert('d_temp_retur_rusak', $params);
                }
            }
        }
    }

    function prosesUangReturRusak($is_hp = null, $idh_retur = null, $imey = null) {
		$getIdUang = $this->db->like('nama','uang')->limit(1)->get('mst_barang')->row();
        $params4 = array(
            'idd_temp_retur_rusak' => $iddtemp_retur_rusak = $this->general->genNumberTemp('d_temp_retur_rusak', 'idd_temp_retur_rusak', $this->id_user, 'IRS', 4),
            'idtemp_retur_rusak' => $idh_retur,
            'jenis_kembalian' => 'uang',
            'imey' => $imey,
			'id_barang_kembalian'=>@$getIdUang->id_barang,
			'harga'=>$imey);
        $this->db->insert('d_temp_retur_rusak', $params4);
    }

    function editTempReturRusak() {
        $pilihan = $this->input->post('pilihan', true);
        $idh_retur = $this->input->post('idh_retur', true);
        $is_hp = $this->input->post('is_hp', true);
        $id_barang = $this->input->post('id_barang', true);
        $this->form_validation->set_rules('pilihan', 'Pilihan Barang Pengganti', 'required');
        if ($pilihan == 'uang') {
            $this->form_validation->set_rules('nominal', 'Nominal Uang', 'required|is_natural_no_zero');
            $nominal = $this->input->post('nominal', true);
        } else if ($pilihan == 'sejenis') {
            if ($is_hp == 1) {
                $this->form_validation->set_rules('imey', 'Imey', 'required');
                $imey = $this->input->post('imey', true);
            } else {
                $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|is_natural_no_zero');
                $imey = $this->input->post('jumlah', true);
            }
        } else if ($pilihan == 'lain_jenis') {
            $this->form_validation->set_rules('id_barang', 'ID Barang', 'required');
            $this->form_validation->set_rules('harga', 'Harga Barang', 'required|is_natural_no_zero');
            $harga = $this->input->post('harga', true);
            if ($is_hp == 1) {
                $this->form_validation->set_rules('imey', 'Imey', 'required');
                $imey = $this->input->post('imey', true);
            } else {
                $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|is_natural_no_zero');
                $imey = $this->input->post('jumlah', true);
            }
        }
        if ($this->form_validation->run() == true) {
            if ($pilihan == 'sejenis') {
                $this->prosesSejenisReturRusak($is_hp, $idh_retur, $id_barang, $imey);
            } else if ($pilihan == 'uang') {
                $this->prosesUangReturRusak($is_hp, $idh_retur, $nominal);
            } else {
                $this->prosesLainJenisReturRusak($is_hp, $idh_retur, $imey, $id_barang, $harga);
            }
            $data = array('success' => 1, 'message' => 'sukses');
        } else {
            $data = array('success' => 0, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return json_encode($data);
    }

    function insertTempReturRusak($idh_retur = null) {
        //insert barang dengan tipe bukan hp
        $getDetail = $this->db->get_where('d_retur', array('idh_retur' => $idh_retur, 'imey' => 'imey_retur'))->result();
        if (!empty($getDetail)) {
            foreach ($getDetail as $dt1) {
                $params = array('idtemp_retur_rusak' => $idtemp_retur_rusak = $this->general->genNumberTemp('temp_retur_rusak', 'idtemp_retur_rusak', $this->id_user, 'TRS', 4),
                    'id_user' => $this->id_user,
                    'idh_retur' => $idh_retur,
                    'id_barang' => $id_barang = $dt1->id_barang);
                $this->db->insert('temp_retur_rusak', $params);
                //get imey detail in table imey
                $getImey = $this->db->get_where('imey_retur', array('idd_retur' => $dt1->idd_retur))->result();
                $getHargaBarang = $this->db->get_where('mst_stok', array('id_toko' => $this->owner, 'id_barang' => $id_barang, 'is_retur' => $idh_retur))->row();
                foreach ($getImey as $im1) {
                    $params2 = array(
                        'idd_temp_retur_rusak' => $iddtemp_retur_rusak = $this->general->genNumberTemp('d_temp_retur_rusak', 'idd_temp_retur_rusak', $this->id_user, 'IRS', 4),
                        'idtemp_retur_rusak' => $idtemp_retur_rusak,
                        'id_barang_kembalian' => $id_barang,
                        'harga' => $getHargaBarang->harga_beli,
                        'imey' => $im1->imey);
                    $this->db->insert('d_temp_retur_rusak', $params2);
                }
            }
        }
        //insert barang denga tipe hp
        $getDetailHp = $this->db->get_where('d_retur', array('idh_retur' => $idh_retur, 'imey != ' => 'imey_retur'))->result();
        $idHpDump = 'pertama';
        if (!empty($getDetailHp)) {
            foreach ($getDetailHp as $dH) {
                $idHp = $dH->id_barang;
                $id_barang = $idHp;
                $getHargaBarang = $this->db->get_where('mst_stok', array('id_toko' => $this->owner, 'id_barang' => $id_barang, 'is_retur' => $idh_retur))->row();
                if ($idHpDump != $idHp) {
                    //ketika pertama kali maka insert header dan detailnya
                    $params3 = array('idtemp_retur_rusak' => $idtemp_retur_rusak = $this->general->genNumberTemp('temp_retur_rusak', 'idtemp_retur_rusak', $this->id_user, 'TRS', 4),
                        'id_user' => $this->id_user,
                        'idh_retur' => $idh_retur,
                        'id_barang' => $idHp);
                    $this->db->insert('temp_retur_rusak', $params3);
                    //insert imey
                    $params4 = array(
                        'idd_temp_retur_rusak' => $iddtemp_retur_rusak = $this->general->genNumberTemp('d_temp_retur_rusak', 'idd_temp_retur_rusak', $this->id_user, 'IRS', 4),
                        'idtemp_retur_rusak' => $idtemp_retur_rusak,
                        'harga' => @$getHargaBarang->harga_beli,
                        'id_barang_kembalian' => $id_barang,
                        'imey' => $dH->imey);
                    $this->db->insert('d_temp_retur_rusak', $params4);
                } else {
                    //ketika kedua maka insert detailnya saja
                    $params4 = array(
                        'idd_temp_retur_rusak' => $iddtemp_retur_rusak = $this->general->genNumberTemp('d_temp_retur_rusak', 'idd_temp_retur_rusak', $this->id_user, 'IRS', 4),
                        'idtemp_retur_rusak' => $idtemp_retur_rusak,
                        'harga' => @$getHargaBarang->harga_beli,
                        'id_barang_kembalian' => $id_barang,
                        'imey' => $dH->imey);
                    $this->db->insert('d_temp_retur_rusak', $params4);
                }
                $idHpDump = $dH->id_barang;
            }
        }
        //insert session
//        $this->session->set_userdata('idKunciReturRusak', $this->id_user);
        return $this->id_user;
    }

    function getReturCustomer() {
        $cari = $this->input->get('q', true);
        $data = array();
        if ($cari) {
            $orLike = "(c.imey like '%$cari%' or a.idh_penjualan like '%$cari%')";
            $data = $this->db->select("a.idh_penjualan as id,a.tgl, a.id_user, a.id_customer,b.id_barang as name,c.imey")->limit(30)->join("d_penjualan b", "a.idh_penjualan = b.idh_penjualan")->join("imey_penjualan c", "b.idd_penjualan= c.idd_penjualan")->where($orLike)->get_where("h_penjualan a", array('a.id_toko' => $this->owner, 'c.is_retur' => 0))->result_array();
            $orlike2 = "(b.nomer like '%$cari%' or a.idh_penjualan like '%$cari%')";
            $data1 = $this->db->select("a.idh_penjualan as id,a.tgl, a.id_user, a.id_customer,b.id_barang as name,b.nomer as imey")->limit(30)->join("d_penjualan_non_fisik b", "a.idh_penjualan = b.idh_penjualan")->where($orlike2)->get_where("h_penjualan a", array('a.id_toko' => $this->owner, 'b.is_retur' => 0))->result_array();
            $hasil = array_merge($data, $data1);
        }
        return $hasil;
    }

    function getReturHarga() {
        $cari = $this->input->get('q', true);
        $data = array();
        if ($cari) {
            $orLike = "(a.idh_retur like '%$cari%' or a.tgl_nota like '%$cari%')";
            $data = $this->db->select("a.idh_retur as id,b.nama as name, a.tgl_nota,a.id_user,a.keterangan")->join("mst_retur c", "a.id_retur = c.id_retur and c.status like '%Harga%'")->join("mst_supliyer b", "a.id_supliyer = b.id_supliyer")->where($orLike)->get_where("h_retur a", array('a.id_sumber' => $this->owner, 'a.is_replay' => 0))->result_array();
        }
        return $data;
    }

    function getReturRusak() {
        $cari = $this->input->get('q', true);
        $data = array();
        if ($cari) {
            $orLike = "(a.idh_retur like '%$cari%' or a.tgl_nota like '%$cari%')";
            $data = $this->db->select("a.idh_retur as id,b.nama as name, a.tgl_nota,a.id_user,a.keterangan")->join("mst_retur c", "a.id_retur = c.id_retur and c.status like '%Rusak%'")->join("mst_supliyer b", "a.id_supliyer = b.id_supliyer")->where($orLike)->get_where("h_retur a", array('a.id_sumber' => $this->owner, 'a.is_replay' => 0))->result_array();
        }
        return $data;
    }

    function simpanPenjualan() {
        $idh_penjualan = $this->session->userdata('idh_penjualan');
        $existFisik = @$this->db->get_where('keuntungan_fisik', array('idh_penjualan' => $idh_penjualan))->result();
        $existNonFisik = @$this->db->get_where('keuntungan_non_fisik', array('idh_penjualan' => $idh_penjualan))->result();
        if ((!empty($existFisik)) || (!empty($existNonFisik))) {
			//insert history mutasi
			if(!empty($existFisik)){
				foreach($existFisik as $ef){
					$this->insertHistory($ef->id_barang,$ef->jumlah,$idh_penjualan,$ef->idd_penjualan,'keluar','d_penjualan',$ef->jumlah_fisik/$ef->jumlah,'');
				}
			}
			if(!empty($existNonFisik)){
				foreach($existNonFisik as $enf){
					$this->insertHistory($enf->id_barang,1,$idh_penjualan,$enf->idd_penjualan,'keluar','d_penjualan_non_fisik',$enf->harga,'');
				}
			}
			//insert history mutasi
            $this->destroySessionPenjualan();
            $data = array('success' => true,'id_penjualan'=>$idh_penjualan, 'message' => 'Terimakasih');
        } else {
            $data = array('success' => false, 'message' => 'Anda Belum Memilih Barang Untuk Dijual');
        }
        return $data;
    }

    function truncatePenjualan() {
        //untuk truncate data datanya yang belum disimpan akan dihapus dulu
        $idh_penjualan = $this->session->userdata('idh_penjualan');
        $getDetail = $this->db->select('idd_penjualan')->get_where('d_penjualan', array('idh_penjualan' => $idh_penjualan))->result();
        $idd_penjualan = $this->general->convertArray($getDetail, 'idd_penjualan');
        $this->prosesDeleteDetailPenjualan($idd_penjualan);
        //delete header 
        $this->db->delete('h_penjualan', array('idh_penjualan' => $idh_penjualan));
        $this->destroySessionPenjualan();
    }

    function destroySessionPenjualan() {
        $data = array('idh_penjualan', 'id_customer');
        $this->session->unset_userdata($data);
    }

    function deleteDetailPenjualan($idd_penjualan = null) {
        if (!empty($idd_penjualan)) {
            $this->prosesDeleteDetailPenjualan($idd_penjualan);
        }
        return $this->session->userdata('idh_penjualan');
    }

    function prosesDeleteDetailPenjualan($idd_penjualan = null) {
        if (!empty($idd_penjualan)) {
            foreach ($idd_penjualan as $id) {
                if (strlen($id) == 30) {//jika panjang iddetailnya adalah 30 maka dia adalah fisik
                    $listImey = $this->db->select("a.id_barang,b.imey")->join("d_penjualan a", "a.idd_penjualan=b.idd_penjualan")->get_where('imey_penjualan b', array('a.idd_penjualan' => $id))->result();
                    $id_barang = @$listImey[0]->id_barang;
                    $imeys = $this->general->idToInWhere($listImey, 'imey');
                    $whereImey = array($imeys);
                    //update status stok barang
                    $this->db->set(array('status'=> 0,'is_retur'=>0))->where(array('id_barang' => $id_barang, 'id_toko' => $this->owner,))->where_in('imey', $whereImey, false)->update('mst_stok'); //set barang menjadi availabe dengan set is retur = 0 dan status = 0
                    //delete detail penjualan
                    $this->db->delete('d_penjualan', array('idd_penjualan' => $id));
                } else if (strlen($id) == 32) {//jika panjang idnya adalah 32 maka dia adalah non fisik
                    //jika non fisik yang dilakukan hanyalah menghapus di tabel detail penjualannya saja
                    $this->db->delete("d_penjualan_non_fisik", array('idd_penjualan' => $id));
                }
            }
        }
    }

    function editRowPenjualan() {
        //edit row ini hanya bisa digunakan untuk menambah keranjang belanja dengan barang bertipe non hp karena untuk barng dengan tipe hp harus ditambah dengan fungsi diatasnya
        $name = $this->input->post('name');
        $jumlahInput = $this->input->post('value');
        $pk = $this->input->post('pk');
        //cek stok digudang
        $getList = $this->db->get_where('d_penjualan', array('idd_penjualan' => $pk))->row();
        $id_barang = $getList->id_barang;
        $idd_penjualan = @$getList->idd_penjualan;
        $jumlahDb = $getList->jumlah;
        //jika jumlah input lebih besar dari jumlah db maka berarti tambah
        if ($jumlahInput > $jumlahDb) {
            //cek selisih penambahan
            $jumlahTambah = $jumlahInput - $jumlahDb;
            //cek stok di db yang tersedia
            $cekStok = $this->db->where(array('id_toko' => $this->owner, 'id_barang' => $id_barang, 'status' => 0))->count_all_results('mst_stok');
            if ($cekStok >= $jumlahTambah) { //jika stok masih mencukupi maka proses
                //get list barang dengan status tersedia dan masukkan di imey penjualan
                $this->insertImeyPenjualan($id_barang, 'gakAdaImeyBos', $jumlahTambah, $idd_penjualan);
                $this->prosesEditRowPenjualan($name, $pk, $jumlahInput);
                echo json_encode(array('status' => 'sukses', 'pesan' => 'Data Terupdate 3'));
            } else { //jika stok tidak mencukupi maka cukup sampai disini
                echo json_encode(array('status' => 'gagal', 'pesan' => 'Stok Tidak Mencukupi'));
            }
        } else {
            //cek selisih pengurangan
            $jumlahKurang = $jumlahDb - $jumlahInput;
            //get list barang ditransaksi ini sesuai dengan id barang dan limit sesuai jumlah pengurangan
            $getListImey = $this->db->limit($jumlahKurang)->get_where('imey_penjualan', array('idd_penjualan' => $pk))->result();
            //hapus list imey di imey penjualan sesuai dengan idimey_penjualan yang didapat
            //update data stok barang di mst stok sesuai dengan imey yang didapat
            $updateStok = $this->general->idToInWhere($getListImey, 'imey');
            $deleteImey = $this->general->idToInWhere($getListImey, 'idimey_penjualan');
            $this->db->query("update mst_stok set status = 0 where status = 1 and id_toko = '$this->owner' and id_barang = '$id_barang' and imey in ($updateStok)");
            $this->db->query("delete from imey_penjualan where idimey_penjualan in ($deleteImey)");
            $this->prosesEditRowPenjualan($name, $pk, $jumlahInput);
            echo json_encode(array('status' => 'sukses', 'pesan' => 'Data Terupdate'));
        }
    }

    function editRowPenjualanHarga() {
        $name = $this->input->post('name');
        $jumlahInput = $this->input->post('value');
        $pk = $this->input->post('pk');
        //sebelum diproses pastikan bahwa ada potongan atau diskon untuk barang yang bersangkutan
        $getDetail = $this->db->get_where('d_penjualan', array('idd_penjualan' => $pk))->row();
        $id_barang = @$getDetail->id_barang;
        $id_gudang = $this->tokoSegudang;
        if ($name == 'diskon') {
            //diskon id_toko diisi dengna id gudang karena yang mengatur harga adalah pihak gudang
            $getDiskon = $this->db->get_where('mst_harga', array('id_toko' => $id_gudang, 'id_barang' => $id_barang))->row();
            $diskon = @$getDiskon->diskon;
            if ($jumlahInput <= $diskon) {
                $this->prosesEditRowPenjualan($name, $pk, $jumlahInput);
                echo json_encode(array('status' => 'sukses', 'pesan' => 'Data Terupdate'));
            } else {
                echo json_encode(array('status' => 'gagal', 'pesan' => 'Diskon Yang Dimasukkan Terlalu Besar'));
            }
        } else {
            //potongan
            $getPotongan = $this->db->get_where('mst_harga', array('id_toko' => $id_gudang, 'id_barang' => $id_barang))->row();
            $potongan = $getPotongan->potongan;
            if ($jumlahInput <= $potongan) {
                $this->prosesEditRowPenjualan($name, $pk, $jumlahInput);
                echo json_encode(array('status' => 'sukses', 'pesan' => 'Data Terupdate'));
            } else {
                echo json_encode(array('status' => 'gagal', 'pesan' => 'Potongan Yang Dimasukkan Terlalu Besar'));
            }
        }
    }

    function prosesEditRowPenjualan($name = null, $pk = null, $jumlahInput = null) {
        if ($name == 'diskon') {
            $this->db->set('potongan', 0)->where('idd_penjualan', $pk)->update('d_penjualan');
        } else if ($name == 'potongan') {
            $this->db->set('diskon', 0)->where('idd_penjualan', $pk)->update('d_penjualan');
        }
        $this->db->set($name, $jumlahInput)->where('idd_penjualan', $pk)->update('d_penjualan');
    }

    function showTablePenjualan($idh_penjualan = null) {
        $isiTable = $this->db->get_where('d_penjualan', array('idh_penjualan' => $idh_penjualan))->result();
        $isiTableNonFisik = $this->db->get_where('d_penjualan_non_fisik', array('idh_penjualan' => $idh_penjualan))->result();
        $data = array('isiTable' => @$isiTable, 'isitTableNonFisik' => @$isiTableNonFisik);
//        $this->general->testPre($data);
        $this->load->view('tr_penjualan/table_penjualan', $data);
    }

    function cek_harga_beli() {
        $id_barang = $this->input->post('id_barang_non_fisik');
        //get harga beli untuk barang non fisik dari mst harga untuk gudang ttt
        $getHargaBeli = $this->db->get_where('mst_harga', array('id_toko' => $this->tokoSegudang, 'id_barang' => $id_barang))->row();
        if (!empty($getHargaBeli)) {
            return true;
        } else {
            $this->form_validation->set_message('cek_harga_beli', 'Harga Jual Belum Ditetapkan Digudang');
            return false;
        }
    }

    function insertPenjualanNonFisik() {
        $this->form_validation->set_rules('id_barang_non_fisik', 'Barang', 'required|callback_cek_harga_beli');
        $this->form_validation->set_rules('jenis_non_fisik', 'Jenis Barang', 'required');
        $this->form_validation->set_rules('nomer', 'Nomer Pelanggan', 'required');
        $this->form_validation->set_rules('harga_non_fisik', 'Harga', 'required|is_natural_no_zero');
        $session_idh_penjualan = $this->session->userdata('idh_penjualan');
        if ($this->form_validation->run() == true) {
            //jika kosong maka insert header terlebih dahulu
            if (empty($session_idh_penjualan)) {
                $id_customer = $this->input->post('id_customer2', true);
                $session_idh_penjualan = $this->prosesInsertHeaderPenjualan($id_customer);
            }
            $this->insertDetailPenjualanNonFisik($session_idh_penjualan);
            $data = array('success' => 1, 'message' => 'sukses', 'idh_penjualan' => $session_idh_penjualan);
        } else {
            $data = array('success' => 0, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

    function insertDetailPenjualanNonFisik($idh_penjualan = null) {
        $id_barang = $this->input->post('id_barang_non_fisik');
        $jenis = $this->input->post('jenis_non_fisik');
        $nomer = $this->input->post('nomer');
        $harga = $this->input->post('harga_non_fisik');
        $params = array(
            'idd_penjualan' => $idd_penjualan = $this->general->genNumberTemp('d_penjualan_non_fisik', 'idd_penjualan', $this->id_user, 'DPJNF', 4),
            'idh_penjualan' => $idh_penjualan,
            'id_barang' => $id_barang,
            'id_jenis_non_fisik' => $jenis,
            'nomer' => $nomer,
            'harga' => $harga
        );
        $this->db->insert('d_penjualan_non_fisik', $params);
    }

    function prosesInsertHeaderPenjualan($id_customer = null) {
        (empty($id_customer)) ? $id_customer = 0 : $id_customer = $id_customer;
        $params = array(
            'idh_penjualan' => $idh_penjualan = $this->general->genNumberTemp('h_penjualan', 'idh_penjualan', $this->id_user, 'HPJ', 4),
            'id_toko' => $this->owner,
            'id_user' => $this->id_user,
            'id_customer' => $id_customer
        );
        $this->db->insert('h_penjualan', $params);
        $session_id_penjualan = $idh_penjualan;
        $sessionku = array('idh_penjualan' => $idh_penjualan, 'id_customer' => $id_customer);
        $this->session->set_userdata($sessionku);
        return $session_id_penjualan;
    }

    function insertPenjualan() {
        $jumlah = $this->input->post('jumlah');
        $id_barang = $this->input->post('id_barang');
        $diskon = $this->input->post('diskon');
        $potongan = $this->input->post('potongan');
        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|is_natural_no_zero|callback_cek_stok[' . $id_barang . ',' . $jumlah . ']');
        $this->form_validation->set_rules('diskon', 'Diskon', 'is_natural|callback_cek_diskon['. $id_barang . ',' . $diskon . ']');
        $this->form_validation->set_rules('potongan', 'Potongan', 'is_natural|callback_cek_potongan['. $id_barang . ',' . $potongan . ']');
//cek barang tersebut handphone atau bukan 

        if ($this->form_validation->run() == true) {

            //inisialisasi jika session belum ada maka indikasi transaksi pertama
            $session_id_penjualan = $this->session->userdata('idh_penjualan');
            if (empty($session_id_penjualan)) {
                //inser header
                $id_customer = $this->input->post('id_customer', true);
                (empty($id_customer)) ? $id_customer = 0 : $id_customer = $id_customer;
                $session_id_penjualan = $this->prosesInsertHeaderPenjualan($id_customer);
            }
            //insert detail
            $this->insertDetailPenjualan($session_id_penjualan);
            $data = array('success' => 1, 'message' => 'sukses', 'idh_penjualan' => $session_id_penjualan);
        } else {
            $data = array('success' => 0, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

    function insertDetailPenjualan($idh_penjualan = null) {
        //get barang sejenis pada transaksi ini jika ada barang sejenis maka update record yang ada jika tidak maka buat record baru
        $id_barang = $this->input->post('id_barang');
        $jumlah = $this->input->post('jumlah');
        $imey = @$this->input->post('imey', true);
        $cekExist = $this->db->get_where("d_penjualan", array('idh_penjualan' => $idh_penjualan, 'id_barang' => $id_barang))->row();
        $idd_penjualan = @$cekExist->idd_penjualan;
        if (empty($cekExist)) {
            $this->prosesInputPenjualan($idh_penjualan, $id_barang);
        } else {
            $this->db->set('jumlah', 'jumlah+' . $jumlah, false)->where(array('idd_penjualan' => $cekExist->idd_penjualan))->update('d_penjualan');
            //update stok dan insert imey baru di imey penjualan
            $this->insertImeyPenjualan($id_barang, $imey, $jumlah, $idh_penjualan, $idd_penjualan);
        }
    }

    function prosesInputPenjualan($idh_penjualan, $id_barang) {
        $imey = $this->input->post('imey');
        $jumlah = $this->input->post('jumlah');
        $diskon = $this->input->post('diskon');
        $potongan = $this->input->post('potongan');
        $harga = $this->input->post('harga_jual');
        $params2 = array(
            'idd_penjualan' => $idd_penjualan = $this->general->genNumberTemp('d_penjualan', 'idd_penjualan', $this->id_user, 'DPJ', 4),
            'idh_penjualan' => $idh_penjualan,
            'id_barang' => $id_barang,
            'jumlah' => $jumlah,
            'diskon' => $diskon,
            'potongan' => $potongan,
            'harga' => $harga
        );
        $this->db->insert('d_penjualan', $params2);
        //insert imey baru ditable imey
        $this->insertImeyPenjualan($id_barang, $imey, $jumlah, $idh_penjualan,$idd_penjualan);
    }

    function insertImeyPenjualan($id_barang = null, $imey = null, $jumlah = null,$idh_penjualan=null, $idd_penjualan = null) {
        //cek tipe barang, jika bertipe hp maka pencarian hanya berdasarkan imey(karena hp hanya bisa ditambah satu persatu tidak bisa langsung banyak)
        //jika barang bukan bertipe hp maka pencarian bisa berdasarkan imey atau status barang ready
        $isHp = $this->master->isHp($id_barang)['is_hp'];
        if ($isHp == 1) {
            $getListImey = $this->db->query("select * from mst_stok where id_toko = '$this->owner' and id_barang = '$id_barang' and imey = '$imey' limit $jumlah")->result();
        } else {
            $getListImey = $this->db->query("select * from mst_stok where id_toko = '$this->owner' and id_barang = '$id_barang' and (imey = '$imey' or status=0) limit $jumlah")->result();
        }
        foreach ($getListImey as $i) {
            $params3 = array('idimey_penjualan' => $idd_imey = $this->general->genNumberTemp('imey_penjualan', 'idimey_penjualan', $this->id_user, 'IPJ', 4),
                'idd_penjualan' => $idd_penjualan,
                'imey' => $i->imey
            );
            $this->db->insert('imey_penjualan', $params3);
            //ubah status barang ditabel stok
        }
        $listImey = $this->general->idToInWhere($getListImey, 'imey');
        $this->db->query("update mst_stok set status = 1, is_retur = '$idh_penjualan' where id_barang='$id_barang' and id_toko = '$this->owner' and imey in ($listImey)");
//        $this->db->set('status', 1)->where(array('id_barang' => $i->id_barang, 'id_toko' => $this->owner, 'imey' => $i->imey))->update('mst_stok');
    }

    function getBarangJual() {
        $cari = $this->input->get('q', true);
        $data = array();
        if ($cari) {
            $orLike = "(a.id_barang like '%$cari%' or a.nama like '%$cari%' or b.imey like '%$cari%' )";
            $data = $this->db->limit(20)->where(array('b.id_toko' => $this->owner, 'b.status' => 0, 'c.id_toko' => $this->tokoSegudang))->where($orLike)->select("a.id_barang as id,a.nama as name, b.imey, b.is_hp,c.id_toko,c.harga_jual, c.potongan, c.diskon")->join("mst_stok b", "a.id_barang = b.id_barang")->join("mst_harga c", "a.id_barang = c.id_barang")->get("mst_barang a")->result_array();
        }
        return $data;
    }

    function getBarangJualNonFisik() {
        $cari = $this->input->get('q', true);
        $data = array();
        if ($cari) {
            $orLike = "(a.id_barang like '%$cari%' or a.nama like '%$cari%')";
            #$data = $this->db->limit(10)->where(array('id_category' => $this->config->item('id_non_fisik')))->where($orLike)->select("id_barang as id,nama as name")->get("mst_barang a")->result_array();
            $data = $this->db->limit(20)->where(array('b.id_toko' => $this->tokoSegudang, 'c.jenis' => 'ELEKTRIK'))->where($orLike)->select("a.id_barang as id,a.nama as name, b.harga_jual, b.potongan, b.diskon")->join("mst_harga b", "a.id_barang = b.id_barang")->join('mst_category c',"a.id_category = c.id_category")->get("mst_barang a")->result_array();
        }
        return $data;
    }

	function simpanPenerimaan($idh_pengiriman = null) {
        $data = array(
            'idh_pengiriman' => $idh_pengiriman
        );
        $fieldKeterangan = 'keterangan-' . $idh_pengiriman;
        $keterangan = $this->input->post($fieldKeterangan, true);
        $this->form_validation->set_rules($fieldKeterangan, 'Keterangan', 'required');
        $this->form_validation->set_rules('idh_pengiriman', 'idh_pengiriman', 'callback_cek_complain[' . $idh_pengiriman . ']');
        if ($this->form_validation->run() == true) {
//insert header penerimaan
            $h_params = array('idh_penerimaan' => $idh_penerimaan = $this->general->genNumberTemp('h_penerimaan', 'idh_penerimaan', $this->id_user, 'HTERIMA', 4),
                'idh_pengiriman' => $idh_pengiriman,
                'id_user' => $this->id_user,
                'keterangan' => $keterangan);
            $this->db->insert('h_penerimaan', $h_params);
            //jika ada pengiriman yang statusnya problem maka tandai h pengiriman dengan is reject
            $getReject = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $idh_pengiriman, 'is_trouble' => 1))->row();
            //membuat satu variabel untuk menampung apakah dia reject atau tidak => untuk digunakan memunculkan notif reject dan tidak
			$isReject = 0;
			if (!empty($getReject)) {
				$isReject = 1;
                $this->db->set('is_reject', 1)->where('idh_pengiriman', $idh_pengiriman)->update('h_pengiriman');
            }
            //tandai bahwa pengiriman dengan id tersebut telah diterima
            $this->db->set('is_arrived', 1)->where('idh_pengiriman', $idh_pengiriman)->update('h_pengiriman');
			
			//insert Detail
            $getDetail = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $idh_pengiriman))->result();
            foreach ($getDetail as $det) {
                $is_hp = @$det->imey;
                $is_trouble = @$det->is_trouble;
                $idd_pengiriman = @$det->idd_pengiriman;
                $id_barang = @$det->id_barang;
				//harga beli dipenerimaan adalah harga jual di pengiriman
                $hargaBeli = @$det->harga_jual;
                if ($is_trouble == 0) { //artinya statusnya tidak ada yang eror
                    //cek dia bertipe hp atau non hp
                    if ($is_hp != 'imey_pengiriman') { //dia statusnya adalah hp karena imeynya bukan imey pengiriman
                        $imeyHp = @$det->imey;
                        //untuk menentukan harga belinya ($id_toko = null, $id_barang = null, $jumlah = null, $harga = null) {
                        $this->prosesInsertDetailPenerimaan($idh_penerimaan, $idh_pengiriman, $idd_pengiriman, $id_barang, $imeyHp, 1, $hargaBeli);
                    } else {//masukkan untuk barang yang bukan tipe hp
                        $this->prosesInsertDetailPenerimaan($idh_penerimaan, $idh_pengiriman, $idd_pengiriman, $id_barang, 'imey_penerimaan', 0, $hargaBeli);
                    }
                    //updte stok
                } else {//jika statusnya tidak trouble maka cek jika bukan hp maka ambil imey yang tidak ada trouble dan masukkan kestoknya
                    //diambil yang yang bukan hp karena jika hp maka yang statusnya istrouble 1 maka pasti error
                    if ($is_hp == 'imey_pengiriman') { // artinya jika barang tersebut bukan tie hap
                        $this->prosesInsertDetailPenerimaan($idh_penerimaan, $idh_pengiriman, $idd_pengiriman, $id_barang, 'imey_penerimaan', 0, $hargaBeli);
                    }
                }
            }
			////insert history reject 
			$getTrouble = $this->db->query("select *, count(*) as jumlah_hp from d_pengiriman where idh_pengiriman = '$idh_pengiriman' and is_trouble = 1 group by id_barang")->result();
			if(!empty($getTrouble)){
				foreach($getTrouble as $gt){
					$jumlah = $gt->jumlah_hp;
					if($gt->imey=='imey_pengiriman'){
						$getJumlahTrouble = $this->db->get_where('imey_pengiriman',array('idd_pengiriman'=>$gt->idd_pengiriman,'is_trouble'=>1))->result();
						$jumlah=count($getJumlahTrouble);
					}
					//sini cuy
					$this->insertHistory($gt->id_barang,$jumlah,$idh_pengiriman,$gt->idd_pengiriman,'keluar','d_pengiriman',$gt->harga_jual,'','','is_reject');
				}
			}
            $data = array('success' => 1, 'message' => 'sukses','is_reject'=>$isReject,'id_pengiriman'=>$idh_pengiriman,'id_penerimaan'=>$idh_penerimaan);
        } else {
            $data = array('success' => 0, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }
	
    function prosesInsertDetailPenerimaan($idh_penerimaan = null, $idh_pengiriman = null, $idd_pengiriman = null, $id_barang = null, $imey = null, $is_hp = null, $harga = null) {
        //get supliyer pengirim
		$getSupliyer = $this->db->get_where('h_pengiriman',array('idh_pengiriman'=>$idh_pengiriman))->row();
		$id_supliyer = @$getSupliyer->id_sumber;
		//cek apakah pengirima ini berasal dari toko yang lain, jika iyah maka id sumber diganti dengan gudang bukan toko yang bersangkutan
		$cekSumberTokos = @$this->db->get_where('data_sumber',array('id'=>$id_supliyer))->row();
		$cekSumberToko = @$cekSumberTokos->gudang;
		($cekSumberToko=='toko')? $id_supliyer = $this->tokoSegudang : $id_supliyer = $id_supliyer;
		if ($is_hp == 1) {
            //untuk update stok
            $getCountHp = $this->db->query("select count(*) as jumlah from d_pengiriman where idh_pengiriman = '$idh_pengiriman' and id_barang = '$id_barang' and is_trouble = 0 group by id_barang")->row();
            $jumlahHp = @$getCountHp->jumlah;
            $hargaBaru = $this->hargaBeli($this->owner, $id_barang, $jumlahHp, $harga);
            $d_params = array(
                'idd_penerimaan' => $idd_penerimaan = $this->general->genNumberTemp('d_penerimaan', 'idd_penerimaan', $this->id_user, 'DTERIMA', 4),
                'idh_penerimaan' => $idh_penerimaan,
                'id_barang' => $id_barang,
                'imey' => $imey,
                'harga_history' => $harga,
                'harga_beli' => $hargaBaru,
                'jumlah' => $jumlahHp);
                // 'jumlah' => 1); gak ngerti
            $this->db->insert('d_penerimaan', $d_params);
			//cek karena hp diinsert sesuai record maka dihistory cukup sekali saja
			//insert history penerimaan
			$getHistory = $this->db->get_where('history_mutasi',array('id_barang'=>$id_barang,'idh_transaksi'=>$idh_penerimaan,'id_tempat'=>$this->owner,'jenis_trans'=>'masuk'))->row();
			if(empty($getHistory)){
				$this->insertHistory($id_barang,$jumlahHp,$idh_penerimaan,$idd_penerimaan,'masuk','d_penerimaan',$hargaBaru,'');
			}
			//insert history
			//logika pertama hanya diupdate
            // $this->db->set(array('id_toko' => $this->owner, 'status' => 0, 'is_retur' => 0, 'harga_beli' => $hargaBaru))->where(array('imey' => $imey, 'status' => 3, 'is_retur' => $idh_pengiriman))->update('mst_stok');
            //logika kedua harus diinsert dengan id supliyer dirubah
			//cek apakah distok sudah ada barang dengan id tersebut
			
			$cekStok = $this->db->get_where('mst_stok',array('id_toko'=>$this->owner,'id_barang'=>$id_barang,'id_supliyer'=>$id_supliyer,'imey'=>$imey))->row();
			if(empty($cekStok)){
				//artinya pengadaan barang baru 
				$paramsHp = array('id_toko'=>$this->owner,'id_barang'=>$id_barang,'id_supliyer'=>$id_supliyer,'imey'=>$imey,'status'=>0,'harga_beli'=>$hargaBaru,'is_retur'=>0,'is_hp'=>1);
				$this->db->insert('mst_stok',$paramsHp);
			}else{
				//jika tidak kosong artinya barang sebelumnya pernah distok//kemungkinan pengadaan berasal dari retur
				$this->db->set(array('is_retur'=>0,'status'=>0))->where(array('id_toko'=>$this->owner,'id_barang'=>$id_barang,'id_supliyer'=>$id_supliyer,'imey'=>$imey))->update('mst_stok');
			}
			//update stok lama yang belum terjual harga barunya diseting mengikuti yang baru
            $this->db->set('harga_beli', $hargaBaru)->where(array('id_barang' => $id_barang, 'id_toko' => $this->owner, 'status' => 0))->update('mst_stok');
			
        } else {
            $getDetailImey = $this->db->get_where('imey_pengiriman', array('idd_pengiriman' => $idd_pengiriman, 'is_trouble' => 0));
            $cekKosong = $getDetailImey->result();
            if (!empty($cekKosong)) {
                $jumlah = count($cekKosong);
                $hargaBaru = $this->hargaBeli($this->owner, $id_barang, $jumlah, $harga);
                $d_params = array(
                    'idd_penerimaan' => $idd_penerimaan = $this->general->genNumberTemp('d_penerimaan', 'idd_penerimaan', $this->id_user, 'DTERIMA', 4),
                    'idh_penerimaan' => $idh_penerimaan,
                    'id_barang' => $id_barang,
                    'imey' => 'imey_penerimaan',
                    'harga_history' => $harga,
                    'harga_beli' => $hargaBaru,
                    'jumlah' => $jumlah);
                $this->db->insert('d_penerimaan', $d_params);
				//memasukkan history penerimaan 
				$this->insertHistory($id_barang,$jumlah,$idh_penerimaan,$idd_penerimaan,'masuk','d_penerimaan',$hargaBaru,'');
				//memasukkan history penerimaan 
                //memasukkan di imey penerimaan
                foreach ($cekKosong as $i) {
                    $imey_params = array('idimey_penerimaan' => $this->general->genNumberTemp('imey_penerimaan', 'idimey_penerimaan', $this->id_user, 'ITRIMA', 4),
                        'idd_penerimaan' => $idd_penerimaan,
                        'imey' => $imey=$i->imey);
                    $this->db->insert('imey_penerimaan', $imey_params);
					//insert stoks
					$cekStok = $this->db->get_where('mst_stok',array('id_toko'=>$this->owner,'id_barang'=>$id_barang,'id_supliyer'=>$id_supliyer,'imey'=>$imey))->row();
					if(empty($cekStok)){
						//artinya pengadaan barang baru 
						$paramsNonHp = array('id_toko'=>$this->owner,'id_barang'=>$id_barang,'id_supliyer'=>$id_supliyer,'imey'=>$i->imey,'status'=>0,'harga_beli'=>$hargaBaru,'is_retur'=>0,'is_hp'=>0);
						$this->db->insert('mst_stok',$paramsNonHp);
					}else{
						//jika tidak kosong artinya barang sebelumnya pernah distok//kemungkinan pengadaan berasal dari retur
						$this->db->set(array('is_retur'=>0,'status'=>0))->where(array('id_toko'=>$this->owner,'id_barang'=>$id_barang,'id_supliyer'=>$id_supliyer,'imey'=>$imey))->update('mst_stok');
					}
                }
                //update stok
                // $arrayImey = $this->general->idToInWhere($cekKosong, 'imey');
                //update stok baru logika lama
                //$this->db->query("update mst_stok set id_toko = '$this->owner', status = 0, is_retur = 0, harga_beli = '$hargaBaru' where imey in ($arrayImey) and status = 3 and is_retur = '$idh_pengiriman'");
                //logika baru jika ada barang baru bukan update yang lama tapi insert
				
				//update stok lama
                $this->db->query("update mst_stok set harga_beli = '$hargaBaru' where id_toko = '$this->owner' and id_barang = '$id_barang' and status = 0");
            }
        }
    }

    

    function insertStokPenerimaan($isHp = null, $h_temp = null) {
//rule pengadaan jika stok suatu id barang di suatu tempat itu masih ada pada saat proses transaksi maka harga belinya dipdate menjadi rata-rata(stok lama * harga lama)+ (stok baru*harga baru)/stok baru + stok lama;
        if ($isHp == 1) {
//cek apakah barang dengan id tersebut masih ada stok digudang apa tidak
            $exist = $this->db->get_where('mst_stok', array('id_barang' => $h_temp->id_barang, 'status' => 0, 'id_toko' => $this->owner));
            if (empty($exist)) {
                $this->db->query("insert into mst_stok select '$this->owner','$h_temp->id_barang',imei,0,$h_temp->harga_beli,0,1 from d_pengiriman where idh_pengiriman = '$h_temp->idh_pengiriman'");
            } else {
//update harga beli
//                $test = count($exist->result());
//                $test = $exist->row()->harga_beli;
                $stokLama = count($exist->result()) * $exist->row()->harga_beli;
                $stokBaru = $h_temp->jumlah * $h_temp->harga_beli;
                $jumlahStok = count($exist->result()) + $h_temp->jumlah;
                $hargaBeliBaru = ($stokBaru + $stokLama) / $jumlahStok;
//update stok yang lama didapat dari variabel exist
                $this->db->set('harga_beli', $hargaBeliBaru);
                $this->db->where('id_barang', $h_temp->id_barang);
                $this->db->where('status', 0);
                $this->db->where('id_toko', $this->owner);
                $this->db->update('mst_stok');
//insert stok baru dengan harga beli baru
                $this->db->query("insert into mst_stok select '$this->owner','$h_temp->id_barang',imei,0,'$hargaBeliBaru',0 from d_temp where idh_temp = '$h_temp->idh_temp'");
            }
        } else {
            $exist = $this->db->get_where('mst_stok', array('id_toko' => $this->owner, 'id_barang' => $h_temp->id_barang))->row();
            if (!empty($exist)) {
                if ($exist->stock > 0) {
//update jika stok masih ada maka set harga beli adalah rata2 dari harga stok lama dan baru
                    $stokLama = $exist->stock * $exist->harga_beli;
                    $stokBaru = $h_temp->jumlah * $h_temp->harga_beli;
                    $jumlahStok = $exist->stock + $h_temp->jumlah;
                    $hargaBeliBaru = ($stokBaru + $stokLama) / $jumlahStok;
                    $this->db->set('stock', 'stock+' . $h_temp->jumlah, FALSE);
                    $this->db->set('harga_beli', $hargaBeliBaru);
                    $this->db->where('id_toko', $this->owner);
                    $this->db->where('id_barang', $h_temp->id_barang);
                    $this->db->update('mst_tempat');
                } else {
                    $this->db->set('stock', 'stock+' . $h_temp->jumlah, FALSE);
                    $this->db->set('harga_beli', $h_temp->harga_beli);
                    $this->db->where('id_toko', $this->owner);
                    $this->db->where('id_barang', $h_temp->id_barang);
                    $this->db->update('mst_tempat');
                }
            } else {
                $this->db->insert('mst_tempat', array('id_toko' => $this->owner, 'id_barang' => $h_temp->id_barang, 'stock' => $h_temp->jumlah, 'harga_beli' => $h_temp->harga_beli));
            }
        }
    }

    function cek_complain($str = null, $param = null) {
//ternyata variabel param itu isinya array jadi yang difungsi set rules callback_cek_stok[idbarang,jumlah]
        $fields = explode(',', $param);
        $idh_pengiriman = $fields[0];
        $isComplain = $this->db->get_where('h_pengiriman', array('idh_pengiriman' => $idh_pengiriman, 'is_reject' => 1))->row();
        if (!empty($isComplain)) {
            $getDetailComplain = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $idh_pengiriman, 'is_trouble' => 1))->row();
            if (empty($getDetailComplain)) {
                $this->form_validation->set_message('cek_complain', 'Status Transaksi Complain, Tapi Anda Belum Memilih Barang Untuk Dicomplain');
                return false;
            }
            return true;
        }
        return true;
    }

    function showImeyPenerimaan($idh_pengiriman = null, $id_barang = null, $is_hp = null) {
        $getHeader = $this->db->get_where('h_pengiriman', array('idh_pengiriman' => $idh_pengiriman))->row();
        $getDetails = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $idh_pengiriman, 'id_barang' => $id_barang))->result();
        //jika is hp adalah 0 maka detail imeynya didapat dari tabel imey pengiriman bukan ditabel detail itu sendiri
        if ($is_hp == 0) {
            $getDetails = $this->db->get_where('imey_pengiriman', array('idd_pengiriman' => $getDetails[0]->idd_pengiriman))->result();
        }
        $data = array('isi' => $getDetails, 'tombolComplain' => $getHeader->is_reject, 'is_hp' => $is_hp);
        $this->load->view('tr_penerimaan/list_imey', $data);
    }

    function showImeyPengiriman($idh_pengiriman = null, $id_barang = null, $is_hp = null) {
        $getHeader = $this->db->get_where('h_pengiriman', array('idh_pengiriman' => $idh_pengiriman))->row();
        $getDetails = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $idh_pengiriman, 'id_barang' => $id_barang))->result();
        //jika is hp adalah 0 maka detail imeynya didapat dari tabel imey pengiriman bukan ditabel detail itu sendiri
        if ($is_hp == 0) {
            $getDetails = $this->db->get_where('imey_pengiriman', array('idd_pengiriman' => $getDetails[0]->idd_pengiriman))->result();
        }
        $data = array('isi' => $getDetails, 'is_hp' => $is_hp);
        $this->load->view('tr_pengiriman/list_imey', $data);
    }

    function showImeyPengirimanNotif($idh_pengiriman = null, $id_barang = null, $is_hp = null) {
        $getHeader = $this->db->get_where('h_pengiriman', array('idh_pengiriman' => $idh_pengiriman))->row();
        $getDetails = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $idh_pengiriman, 'id_barang' => $id_barang))->result();
        //jika is hp adalah 0 maka detail imeynya didapat dari tabel imey pengiriman bukan ditabel detail itu sendiri
        if ($is_hp == 0) {
            $getDetails = $this->db->get_where('imey_pengiriman', array('idd_pengiriman' => $getDetails[0]->idd_pengiriman))->result();
        }
        $data = array('isi' => $getDetails, 'is_hp' => $is_hp);
        $this->load->view('tr_pengiriman/list_invoice_imey', $data);
    }

    function showImeyReturCustomer($idtemp = null, $idh_retur = null, $id_barang = null, $is_hp = null, $status = null) {
        //20170608-201704UT0001-TRC-0001/20170607-201704UT0001-HPJ-0001/BUZZER-0000001/0/isi
        $getBarangs = $this->prosesshowImeyReturCustomer($idtemp, $idh_retur, $id_barang, $is_hp, $status);
        $data = array('isi' => $getBarangs, 'is_hp' => @$is_hp, 'id_barang' => @$id_barang, 'idh_penjualan' => @$idh_retur, 'idtemp' => @$idtemp);
        $status = $this->uri->segment(7);
        if ($status == 'isi') {
            $this->load->view('tr_retur_customer/list_imey', $data);
        } else {
            $this->load->view('tr_retur_customer/list_imey_kosong', $data);
        }
    }

    function prosesshowImeyReturCustomer($idtemp = null, $idh_retur = null, $id_barang = null, $is_hp = null, $status = null) {
        $getHeader = @$this->db->get_where('h_penjualan', array('idh_penjualan' => $idh_retur))->row();
        $getDetails = @$this->db->get_where('d_penjualan', array('idh_penjualan' => $idh_retur, 'id_barang' => $id_barang))->row();
        $getBarangs = @$this->db->get_where('imey_penjualan', array('idd_penjualan' => $getDetails->idd_penjualan))->result();
        return $getBarangs;
    }

    function showImeyReturNotif($idh_retur = null, $id_barang = null, $is_hp = null) {
        $getHeader = $this->db->get_where('h_retur', array('idh_retur' => $idh_retur))->row();
        $getDetails = $this->db->get_where('d_retur', array('idh_retur' => $idh_retur, 'id_barang' => $id_barang))->result();
        //jika is hp adalah 0 maka detail imeynya didapat dari tabel imey pengiriman bukan ditabel detail itu sendiri
        if ($is_hp == 0) {
            $getDetails = $this->db->get_where('imey_retur', array('idd_retur' => $getDetails[0]->idd_retur))->result();
        }
        $data = array('isi' => $getDetails, 'is_hp' => $is_hp);
        $this->load->view('tr_retur/list_invoice_imey', $data);
    }

    function showImeyPengirimanReject($idh_pengiriman = null, $id_barang = null, $is_hp = null) {
        $getHeader = $this->db->get_where('h_pengiriman', array('idh_pengiriman' => $idh_pengiriman))->row();
        $getDetails = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $idh_pengiriman, 'id_barang' => $id_barang))->result();
        //jika is hp adalah 0 maka detail imeynya didapat dari tabel imey pengiriman bukan ditabel detail itu sendiri
        if ($is_hp == 0) {
            $getDetails = $this->db->get_where('imey_pengiriman', array('idd_pengiriman' => $getDetails[0]->idd_pengiriman, 'is_trouble' => 1))->result();
        }
        $data = array('isi' => $getDetails, 'is_hp' => $is_hp);
        $this->load->view('tr_pengiriman/list_invoice_imey', $data);
    }

    function setDetailComplain($idd_pengiriman = null, $is_hp = null) {
        //kita override ketika is hp adalah 0 artinya idd_pengiriman ini adalah id dari table imey pengiriman bukan dari tabel detail pengiriman langsung
        if ($is_hp == 0) {
            //get detail pengiriman
            $getDetail = $this->db->get_where('imey_pengiriman', array('idimey_pengiriman' => $idd_pengiriman))->row();
            //set is trouble pada imey pengiriman
            $this->db->set('is_trouble', 1)->where('idimey_pengiriman', $idd_pengiriman)->update('imey_pengiriman');
            //override iddetail pengiriman
            $idd_pengiriman = $getDetail->idd_pengiriman;
        }
        $this->db->set('is_trouble', 1)->where('idd_pengiriman', $idd_pengiriman)->update('d_pengiriman');
    }

    function destroyDetailComplain($idd_pengiriman = null, $is_hp = null) {
        if ($is_hp == 0) {
            //set is trouble pada table imey pengiriman
            $this->db->set('is_trouble', 0)->where('idimey_pengiriman', $idd_pengiriman)->update('imey_pengiriman');
            //get id detail untuk mengetahui apakah di table imey pengiriman dengan id detail yang sama masih terdapat status is trouble
            //jika masih maka field pad table detail dibiarkan is troublenya masih 1
            //jika kosong maka filed is trouble pada table detail di jadikan 0
            $getIdDetail = $this->db->get_where('imey_pengiriman', array('idimey_pengiriman' => $idd_pengiriman))->row();
            $getDetail = $this->db->get_where('imey_pengiriman', array('idd_pengiriman' => $getIdDetail->idd_pengiriman, 'is_trouble' => 1))->row();
            if (empty($getDetail)) {
                $this->db->set('is_trouble', 0)->where('idd_pengiriman', $getIdDetail->idd_pengiriman)->update('d_pengiriman');
            }
        } else {
            $this->db->set('is_trouble', 0)->where('idd_pengiriman', $idd_pengiriman)->update('d_pengiriman');
        }
    }

    function setComplain($idh_pengiriman = null) {
        $this->db->set('is_reject', 1)->where('idh_pengiriman', $idh_pengiriman)->update('h_pengiriman');
    }

    function destroyComplain($idh_pengiriman = null) {
        $this->db->set('is_reject', 0)->where('idh_pengiriman', $idh_pengiriman)->update('h_pengiriman');
        $this->db->set('is_trouble', 0)->where('idh_pengiriman', $idh_pengiriman)->where('is_trouble', 1)->update('d_pengiriman');
        $getDetail = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $idh_pengiriman))->result();
        $idDetailImey = $this->general->idToInWhere($getDetail, 'idd_pengiriman');
        $this->db->query("update imey_pengiriman set is_trouble = 0 where idd_pengiriman in ($idDetailImey)");
    }

    function readPenerimaan($id = null) {
        if ($id != null) {
            $this->db->where('idh_pengiriman', $id);
        }
        $this->db->where('id_tujuan', $this->owner);
        $isi = $this->db->order_by('tgl_input', 'desc')->get_where('h_pengiriman', array('is_arrived' => 0))->result();
        $penerimaan = array('detail' => $isi);
        $data = array('output' => $this->load->view('tr_penerimaan/table_penerimaan', $penerimaan, true), 'ket_header' => 'Notifikasi Penerimaan Barang');
        $this->templateNotif($data);
//        $this->general->templateGudangAccounting($data);
    }

    function readRetur($id = null) {
        if ($id != null) {
            $this->db->where('idh_retur', $id);
        }
        $this->db->where('id_supliyer', $this->owner);
        $isi = $this->db->order_by('tgl_input', 'desc')->get_where('h_retur', array('is_replay' => 0))->result();
        $retur = array('detail' => $isi);
        $data = array('output' => $this->load->view('tr_retur/invoice_retur', $retur, true), 'ket_header' => 'Notifikasi Retur Barang');
        $this->templateNotif($data);
//        $this->general->templateGudangAccounting($data);
    }

    function readPermintaan($id = null) {
        if ($id != null) {
            $this->db->where('idh_permintaan', $id);
        }
        $this->db->where('id_tujuan', $this->owner);
        $isi = $this->db->order_by('tgl_input', 'desc')->get_where('h_permintaan', array('is_appliyed' => 0))->result();
        $permintaan = array('detail' => $isi);
        $data = array('output' => $this->load->view('tr_permintaan/invoice_permintaan', $permintaan, true), 'ket_header' => 'Notifikasi Permintaan Barang');
        $this->templateNotif($data);
    }

    function readPengiriman($id = null) {
        if ($id != null) {
            $this->db->where('idh_pengiriman', $id);
        }
        $this->db->where('id_sumber', $this->owner);
        $isi = $this->db->order_by('tgl_input', 'desc')->get_where('h_pengiriman', array('is_arrived' => 0))->result();
        $pengiriman = array('detail' => $isi);
        $data = array('output' => $this->load->view('tr_pengiriman/invoice_kirim_sumber', $pengiriman, true), 'ket_header' => 'Notifikasi Pengiriman Barang');
        $this->templateNotif($data);
    }

    function readReject($id = null) {
        if ($id != null) {
            $this->db->where('idh_pengiriman', $id);
        }
        $this->db->where('id_sumber', $this->owner);
        $this->db->where('is_reject', 1);
        $this->db->where('is_appliyed', 0);
        $isi = $this->db->order_by('tgl_input', 'desc')->get_where('h_pengiriman', array('is_arrived' => 1))->result();
        $pengiriman = array('detail' => $isi);
        $data = array('output' => $this->load->view('tr_pengiriman/invoice_reject', $pengiriman, true), 'ket_header' => 'Notifikasi Reject Barang');
        $this->templateNotif($data);
    }

    function templateNotif($data = null) {

        $status = $this->session->userdata('status');
        switch ($status) {
            case 'superadmin':
                $this->templateSuperadmin($data);
                break;
            case 'accounting':
                $this->templateGudangAccounting($data);
                break;
            case 'admin':
                $this->templateGudangAdmin($data);
                break;
            case 'kepala_toko':
                $this->templateKepalaToko($data);
                break;
            case 'kasir' :
                $this->templateKasir($data);
                break;
            case 'sales' :
                $this->templateSales($data);
                break;
            default:
                echo 'undefined akses';
                break;
        }
    }

    public function templateGudangAccounting($output = null) {
        $this->load->view('template_gudangaccounting', $output);
    }

    public function templateGudangAdmin($output = null) {
        $this->load->view('template_gudangadmin', $output);
    }

    public function templateKepalaToko($output = null) {
        $this->load->view('template_kepalatoko', $output);
    }

    public function templateSuperadmin($output = null) {
        $this->load->view('template_superadmin', $output);
    }
    public function templateKasir($output = null) {
        $this->load->view('template_kasir', $output);
    }

    function cekImageExist($file = null) {
        if (file_exists(FCPATH . '/assets/uploads/files/' . $file)) {
            return true;
        } else {
            return false;
        }
    }

    function getTujuan($id_toko = null) {
        if (strpos($id_toko, 'T') !== false) {
            $namaSumbers = @$this->db->get_where('mst_toko', array('id_toko' => $id_toko))->row();
            $namaSumber = 'Toko ' . $namaSumbers->nama;
            (empty($namaSumbers->foto)) ? $foto = 'default-toko.png' : $foto = $namaSumbers->foto;
        } elseif (strpos($id_toko, 'G') !== false) {
            $namaSumbers = @$this->db->get_where('mst_gudang', array('id_gudang' => $id_toko))->row();
            $namaSumber = 'Gudang ' . $namaSumbers->nama;
            (empty($namaSumbers->foto)) ? $foto = 'default-gudang.png' : $foto = $namaSumbers->foto;
            ($this->cekImageExist($foto)) ? $foto = $foto : $foto = 'default-gudang.png';
        } 
		elseif (strpos($id_toko, 'SUP') !== false) {
            $namaSumbers = @$this->db->get_where('mst_supliyer', array('id_supliyer' => $id_toko))->row();
            $namaSumber = 'Supliyer ' . $namaSumbers->nama;
            $foto = 'default-gudang.png';
        } 
		else {
            $namaSumber = 'Superadmin';
            $foto = 'default-superadmin.png';
        }
        $data = array('nama_tujuan' => $namaSumber, 'foto_tujuan' => $foto);
        return $data;
    }

    function getPenanggungJawab($id_toko = null) {
        if (strpos($id_toko, 'T') !== false) {
            $penanggungJawab = @$this->db->get_where('user_toko', array('id_usertoko' => $id_toko))->row()->fullname;
        } elseif (strpos($id_toko, 'G') !== false) {
            $penanggungJawab = @$this->db->get_where('user_gudang', array('id_usergudang' => $id_toko))->row()->fullname;
        } else {
            $penanggungJawab = @$this->db->get_where('user', array('id_user' => $id_toko))->row()->fullname;
        }
        $data = array('penanggungJawab' => $penanggungJawab);
        return $data;
    }

    function prosesDetailNotif($hk = null) {
        if (strpos($hk->id_sumber, 'T') !== false) {
            $namaSumbers = @$this->db->get_where('mst_toko', array('id_toko' => $hk->id_sumber))->row();
            $penanggungJawab = @$this->db->get_where('user_toko', array('id_usertoko' => $hk->id_user))->row()->fullname;
            $namaSumber = 'Toko ' . $namaSumbers->nama;
            (empty($namaSumbers->foto)) ? $foto = 'default-toko.png' : $foto = $namaSumbers->foto;
            //untuk jika filenamenya ada didatabase tapi filenya ndak ada di direktori
            ($this->cekImageExist($foto)) ? $foto = $foto : $foto = 'default-toko.png';
        } elseif (strpos($hk->id_sumber, 'G') !== false) {
            $namaSumbers = @$this->db->get_where('mst_gudang', array('id_gudang' => $hk->id_sumber))->row();
            $penanggungJawab = @$this->db->get_where('user_gudang', array('id_usergudang' => $hk->id_user))->row()->fullname;
            $namaSumber = 'Gudang ' . $namaSumbers->nama;
            (empty($namaSumbers->foto)) ? $foto = 'default-gudang.png' : $foto = $namaSumbers->foto;
            ($this->cekImageExist($foto)) ? $foto = $foto : $foto = 'default-gudang.png';
        } else {
            $namaSumber = 'Superadmin';
            $penanggungJawab = @$this->db->get_where('user', array('id_user' => $hk->id_user))->row()->fullname;
            $foto = 'default-superadmin.png';
        }
        $data = array('penanggung_jawab' => $penanggungJawab, 'nama_sumber' => $namaSumber, 'foto' => $foto);
        return $data;
    }

    function detailNotif($hk = null) {
        $getNotif = $this->prosesDetailNotif($hk);
        $getTujuan = $this->getTujuan($hk->id_tujuan);
        $data = array('penanggung_jawab' => $getNotif['penanggung_jawab'], 'nama_sumber' => $getNotif['nama_sumber'], 'foto' => $getNotif['foto'], 'nama_tujuan' => $getTujuan['nama_tujuan'], 'foto_tujuan' => $getTujuan['foto_tujuan']);
        return $data;
    }

    function getNotifKirim() {
        $result = $this->db->order_by('tgl_input', 'desc')->get_where('h_pengiriman', array('is_arrived' => 0, 'id_tujuan' => $this->owner))->result();
        $jumlah = count($result);
        if (!empty($result)) {
            $data = array('jumlah' => $jumlah, 'hasil_kirim' => @$result);
            $this->load->view('pluggins/notif_kirim', $data);
        }
    }

    function getNotifKirimSumber() {
        $result = $this->db->order_by('tgl_input', 'desc')->get_where('h_pengiriman', array('is_arrived' => 0, 'id_sumber' => $this->owner))->result();
        $jumlah = count($result);
        if (!empty($result)) {
            $data = array('jumlah' => $jumlah, 'hasil_kirim' => @$result);
            $this->load->view('pluggins/notif_kirim_sumber', $data);
        }
    }

    function getNotifReject() {
        $result = $this->db->order_by('tgl_input', 'desc')->get_where('h_pengiriman', array('is_arrived' => 1, 'is_appliyed' => 0, 'is_reject' => 1, 'id_sumber' => $this->owner))->result();
        $jumlah = count($result);
        if (!empty($result)) {
            $data = array('jumlah' => $jumlah, 'hasil_kirim' => @$result);
            $this->load->view('pluggins/notif_reject', $data);
        }
    }

    function getNotifPermintaan() {
        $result = $this->db->order_by('tgl_input', 'desc')->get_where('h_permintaan', array('is_appliyed' => 0, 'id_tujuan' => $this->owner))->result();
        $jumlah = count($result);
        if (!empty($result)) {
            $data = array('jumlah' => $jumlah, 'hasil_permintaan' => @$result);
            $this->load->view('pluggins/notif_permintaan', $data);
        }
    }

    function getNotifRetur() {
        $result = $this->db->order_by('tgl_input', 'desc')->get_where('h_retur', array('is_replay' => 0, 'id_supliyer' => $this->owner))->result();
        $jumlah = count($result);
        if (!empty($result)) {
            $data = array('jumlah' => $jumlah, 'hasil_retur' => @$result);
            $this->load->view('pluggins/notif_retur', $data);
        }
    }

    function insertHeaderPengiriman() {
        $this->form_validation->set_rules('id_tujuan', 'Tujuan', 'required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');
//        $this->form_validation->set_rules('token', 'token', 'callback_check_hp');
//cek barang tersebut handphone atau bukan 
        if ($this->form_validation->run() == true) {
            $params = array(
                'idh_pengiriman' => $idh_retur = $this->general->genNumberTemp('h_pengiriman', 'idh_pengiriman', $this->id_user, 'HKIRIM', 4),
                'id_sumber' => $this->owner,
                'id_tujuan' => $id_tujuan = $this->input->post('id_tujuan', true),
                'tgl_nota' => $tgl_nota = date("Y-m-d H:i:s"),
                'id_user' => $this->id_user,
                'is_arrived' => 0,
                'keterangan' => $keterangan = $this->input->post('keterangan', true)
            );
            $this->db->insert('h_pengiriman', $params);
            $sessionku = array('keterangan' => $keterangan, 'idh_pengiriman' => $idh_retur, 'id_tujuan' => $id_tujuan);
            $this->session->set_userdata($sessionku);
            $data = array('success' => 1, 'message' => 'sukses', 'idh_pengiriman' => $this->session->userdata('idh_pengiriman'));
        } else {
            $data = array('success' => 0, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

    function insertHeaderPermintaan() {
        $this->form_validation->set_rules('id_tujuan', 'Tujuan', 'required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');
        if ($this->form_validation->run() == true) {
            $params = array(
                'idh_permintaan' => $idh_permintaan = $this->general->genNumberTemp('h_permintaan', 'idh_permintaan', $this->id_user, 'HMINTA', 4),
                'id_sumber' => $this->owner,
                'id_tujuan' => $id_tujuan = $this->input->post('id_tujuan', true),
                'id_user' => $this->id_user,
                'is_appliyed' => 0,
                'keterangan' => $keterangan = $this->input->post('keterangan', true)
            );
            $this->db->insert('h_permintaan', $params);
            $sessionku = array('keterangan_permintaan' => $keterangan, 'idh_permintaan' => $idh_permintaan, 'id_tujuan_permintaan' => $id_tujuan);
            $this->session->set_userdata($sessionku);
            $data = array('success' => 1, 'message' => 'sukses', 'idh_permintaan' => $this->session->userdata('idh_permintaan'));
        } else {
            $data = array('success' => 0, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

    function showDetailPengiriman($idh_pengiriman = null) {
        $this->db->select("a.*,count(*) as jumlah_hp");
        $this->db->group_by('a.id_barang');
        $isiTable = $this->db->get_where('d_pengiriman a', array('a.idh_pengiriman' => $idh_pengiriman))->result();
        $data = array('isiTable' => @$isiTable);
        $this->load->view('tr_pengiriman/table_pengiriman', $data);
    }

    function showDetailPermintaan($idh_permintaan = null) {
        $this->db->select("a.*");
        $isiTable = $this->db->get_where('d_permintaan a', array('a.idh_permintaan' => $idh_permintaan))->result();
        $data = array('isiTable' => @$isiTable);
        $this->load->view('tr_permintaan/table_permintaan', $data);
    }

    function insertDetailPengirimanHp($imey = null) {
        $dataBarang = $this->db->get_where('mst_stok', array('imey' => $imey))->row();
        $id_pengiriman = $this->session->userdata('idh_pengiriman');
        //getIdBarang
        $idBarang = @$this->db->get_where('mst_stok', array('imey' => $imey))->row();
        $data = array(
            'imey' => $id_barang = $idBarang->id_barang
        );
        $this->form_validation->set_rules('imey', 'Imey', 'callback_cek_harga[' . $id_barang . ',' . $imey . ']');
        if ($this->form_validation->run() == true) {
			if($this->owner=='superadmin'){
					$hargaJual = $this->getHargaJual($id_barang);
				}else{
					$gethargaJual = @$this->db->get_where('mst_stok',array('id_toko'=>$this->owner,'id_barang'=>$id_barang,'status'=>0,'is_retur'=>0))->row();
					$hargaJual = @$gethargaJual->harga_beli;
				}
            $params = array(
                'idd_pengiriman' => $idh_pengiriman = $this->general->genNumberTemp('d_pengiriman', 'idd_pengiriman', $this->id_user, 'DKIRIM', 4),
                'idh_pengiriman' => $id_pengiriman,
                'id_barang' => @$dataBarang->id_barang,
                'imey' => @$imey,
				'harga_jual'=>$hargaJual,
                'jumlah' => 1
            );
            $this->db->insert('d_pengiriman', $params);
            $this->db->update('mst_stok', array('status' => 3, 'is_retur' => $id_pengiriman), array('imey' => $imey, 'id_toko' => $this->owner));
            $data = array('success' => true, 'message' => 'Barang Berhasil di Dikirim');
        } else {
            $data = array('success' => false, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

	function getHargaJual($id_barang=null){
		$getHargaJual = $this->db->get_where('mst_harga',array('id_toko'=>$this->owner,'id_barang'=>$id_barang))->row();
		return $hargaJual = @$getHargaJual->harga_jual;
	}
    function insertDetailPengirimanNonHp($id_barang = null, $jumlah = null) {
        $data = array(
            'id_barang' => $id_barang,
            'jumlah' => $jumlah
        );
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|is_natural_no_zero|callback_cek_stok[' . $id_barang . ',' . $jumlah . ']|callback_cek_harga[' . $id_barang . ',' . $jumlah . ']');
        if ($this->form_validation->run() == true) {
//cek apakah di detail pengiriman sudah ada barang dengan idheader yang bersangkutan
            $idh_pengiriman = $this->session->userdata('idh_pengiriman');
            $exist = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $idh_pengiriman, 'id_barang' => $id_barang));
            $cek = @$exist->row();
            if (empty($cek)) {
				//get harga jual barang #masalah harga jual
				if($this->owner=='superadmin'){
					$hargaJual = $this->getHargaJual($id_barang);
				}else{
					$gethargaJual = @$this->db->get_where('mst_stok',array('id_toko'=>$this->owner,'id_barang'=>$id_barang,'status'=>0,'is_retur'=>0))->row();
					$hargaJual = @$gethargaJual->harga_beli;
				}
				//insert into detail
                $params = array(
                    'idd_pengiriman' => $idd_pengiriman = $this->general->genNumberTemp('d_pengiriman', 'idd_pengiriman', $this->id_user, 'DKIRIM', 4),
                    'idh_pengiriman' => $idh_pengiriman = $this->session->userdata('idh_pengiriman'),
                    'id_barang' => @$id_barang,
                    'imey' => 'imey_pengiriman',
					'harga_jual'=>$hargaJual,
                    'jumlah' => $jumlah
                );
                $this->db->insert('d_pengiriman', $params);
            } else {
                $idd_pengiriman = $cek->idd_pengiriman;
                $this->db->set('jumlah', 'jumlah+' . $jumlah, false);
                $this->db->where('idh_pengiriman', $idh_pengiriman);
                $this->db->where('id_barang', $id_barang);
                $this->db->update('d_pengiriman');
            }
//update barang
            $this->prosesNonHp($jumlah, $id_barang, 3, $idd_pengiriman, 'imey_pengiriman'); //3 adalah id status barang Sedang dikirm
            $data = array('success' => true, 'message' => 'Barang Berhasil di Dikirim');
        } else {
            $data = array('success' => false, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

    function cek_harga($jumlah = null, $param = null) {
//ternyata variabel param itu isinya array jadi yang difungsi set rules callback_cek_stok[idbarang,jumlah]
        $fields = explode(',', $param);
        $id_barang = $fields[0];
		//jika usernya toko maka harga jualnya ikut digudang
		if($this->hakUser != 'toko'){
			$hargaDb = $this->db->get_where('mst_harga', array('id_barang' => $id_barang, 'id_toko' => $this->owner))->row();
			if (empty($hargaDb)) {
				$this->form_validation->set_message('cek_harga', 'Silahkan Atur Harga Untuk Barang Ini Terlebih Dahulu');
				return false;
			} else {
				return true;
			}
		}else{
			return true;
		}
    }

    function cek_stok_permintaan($jumlah = null, $param = null) {
//ternyata variabel param itu isinya array jadi yang difungsi set rules callback_cek_stok[idbarang,jumlah]
        $sess_id_tujuan = $this->session->userdata('id_tujuan_permintaan');
        $sess_idh_permintaan = $this->session->userdata('idh_permintaan');
        $fields = explode(',', $param);
        $id_barang = $fields[0];
        $jumlah = $fields[1];
        $stokDb = $this->db->select('count(*) as stock', '*')->group_by('id_barang')->get_where('mst_stok', array('is_retur' => 0, 'status' => 0, 'id_barang' => $id_barang, 'id_toko' => $sess_id_tujuan))->row();
        //get jumlah total dari yang diinputkan user dan yang sudah dimasukkan ke database
        $getJumlahDb = $this->db->get_where('d_permintaan', array('idh_permintaan' => $sess_idh_permintaan, 'id_barang' => $id_barang))->row();
        (!empty($getJumlahDb)) ? $jumlah = $jumlah + $getJumlahDb->jumlah : $jumlah = $jumlah;
        if ($jumlah > $stokDb->stock) {
            $this->form_validation->set_message('cek_stok_permintaan', 'Stok Tidak Mencukupi');
            return false;
        } else {
            return true;
        }
    }

    //karena detail permintaan antara hp dan non hp dibedakan dengan id barang maka fungsinya dijadikan satu untuk memasukkan ke detail barang
    function insertDetailPermintaan($id_barang = null, $jumlah = null) {
        $data = array(
            'id_barang' => $id_barang,
            'jumlah' => $jumlah
        );
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|is_natural_no_zero|callback_cek_stok_permintaan[' . $id_barang . ',' . $jumlah . ']');
        if ($this->form_validation->run() == true) {
//cek apakah di detail pengiriman sudah ada barang dengan idheader yang bersangkutan kampreto
            $idh_permintaan = $this->session->userdata('idh_permintaan');
            $exist = $this->db->get_where('d_permintaan', array('idh_permintaan' => $idh_permintaan, 'id_barang' => $id_barang));
            $cek = @$exist->row();
            if (empty($cek)) {
                $params = array(
                    'idd_permintaan' => $idd_permintaan = $this->general->genNumberTemp('d_permintaan', 'idd_permintaan', $this->id_user, 'DMINTA', 4),
                    'idh_permintaan' => $idh_permintaan = $this->session->userdata('idh_permintaan'),
                    'id_barang' => @$id_barang,
                    'jumlah' => $jumlah
                );
                $this->db->insert('d_permintaan', $params);
            } else {
                $idd_permintaan = $cek->idd_permintaan;
                $this->db->set('jumlah', 'jumlah+' . $jumlah, false);
                $this->db->where('idd_permintaan', $idd_permintaan);
                $this->db->update('d_permintaan');
            }
            $data = array('success' => true, 'message' => 'Barang Berhasil di Diproses');
        } else {
            $data = array('success' => false, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

    function deleteDetailPengirimanMulti($id_detail = null) {
        foreach ($id_detail as $key => $d) {
            $getDetail = $this->db->get_where('d_pengiriman', array('idd_pengiriman' => $d))->row();
            $hp = $getDetail->imey;
            ($hp == 'imey_pengiriman') ? $is_hp = 0 : $is_hp = 1;
            $idh_pengiriman = $this->prosesDeleteDetailPengiriman($d, 'multi', $is_hp);
        }
        return $idh_pengiriman['idh_pengiriman'];
    }

    function deleteDetailPermintaanMulti($id_detail = null) {
        if (!empty($id_detail)) {
            $idd_tunggal = $id_detail[0];
            //get id header
            $getHeader = $this->db->get_where("d_permintaan", array('idd_permintaan' => $idd_tunggal))->row();
            $idh_permintaan = $getHeader->idh_permintaan;
            $idDetail = $this->general->idToInWherePrimitif($id_detail);
            $this->db->query("delete from d_permintaan where idd_permintaan in ($idDetail)");
            return $idh_permintaan;
        }
    }

    function deleteDetailPengirimanSingle($id_detail = null, $is_hp = null) {
        foreach ($id_detail as $key => $d) {
            $idh_pengiriman = $this->prosesDeleteDetailPengiriman($d, 'single', $is_hp);
        }
        return $idh_pengiriman['idh_pengiriman'];
    }

    function prosesDeleteDetailPengiriman($id_detail = null, $type = null, $is_hp = null) {
        #jika tipe hp maka yang di ubah adalah tabel detail retur dan update mst stok
        if ($is_hp == 1) {
            //get detail retur
            $detail = $this->db->get_where('d_pengiriman', array('idd_pengiriman' => $id_detail));
            $idh_pengiriman = $detail->row()->idh_pengiriman;
            $id_barang = $detail->row()->id_barang;
            //get detail retur
            if ($type == 'multi') {
                //get barang sejenis
                $getSejenis = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $idh_pengiriman, 'id_barang' => $id_barang))->result();
                //convert to id in
                $arrayImey = $this->general->idToInWhere($getSejenis, 'imey');
                $arrayDetail = $this->general->idToInWhere($getSejenis, 'idd_pengiriman');
                $this->db->query("delete from d_pengiriman where idd_pengiriman in ($arrayDetail)");
            } else {
                $arrayImey = "'" . $detail->row()->imey . "'";
                $this->db->delete('d_pengiriman', array('idd_pengiriman' => $id_detail));
            }
            $this->db->query("update mst_stok set status = 0, is_retur = 0 where id_toko = '$this->owner' and is_hp = 1 and imey in ($arrayImey)");
        } else {
            if ($type == 'multi') {
                //id detail adalah isinya id detail retur
                $getImey = $this->db->get_where('imey_pengiriman', array('idd_pengiriman' => $id_detail))->result();
                $arrayImey = $this->general->idToInWhere($getImey, 'imey');
                //untuk Pengiriman
                $id_detail_pengiriman = $id_detail;
                //proses delete detailnya
                $this->db->delete('d_pengiriman', array('idd_pengiriman' => $id_detail_pengiriman));
            } else {
                //id detail adalah isinya id detail imey retur
                $getImey = $this->db->get_where('imey_pengiriman', array('idimey_pengiriman' => $id_detail))->row();
                $arrayImey = "'" . $getImey->imey . "'";
                //delete imey retur yang bersangkutan dan update jumlah di table detail sesuai dengan jumlah count imey retur
                $this->db->delete('imey_pengiriman', array('idimey_pengiriman' => $id_detail));
                $id_detail_pengiriman = $getImey->idd_pengiriman;
                $this->db->query("update d_pengiriman set jumlah = jumlah-1 where idd_pengiriman = '$id_detail_pengiriman'");
                //cek jumlah terakhir dari imey yang bersangkutan => jika jumlahnya 0 maka record di tabel detail juga dihapus
                $detail = $this->db->get_where('d_pengiriman', array('idd_pengiriman' => $id_detail_pengiriman));
                $stokAkhir = @$detail->row()->jumlah;
                //get idheader retur
                //jika stokakhirnya adalah kosong maka tabel didetail dedelete sekalian
                if ($stokAkhir <= 0) {
                    $this->db->delete('d_pengiriman', array('idd_pengiriman' => $id_detail_pengiriman));
                }
            }
            $this->db->query("update mst_stok set status = 0, is_retur = 0 where id_toko = '$this->owner' and is_hp = 0 and imey in ($arrayImey)");
        }
        $data = array('idh_pengiriman' => $this->session->userdata('idh_pengiriman'));
        return $data;
    }
	
    function insertPengiriman() {
        $idh_pengiriman = $this->session->userdata('idh_pengiriman');
        // $exist = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $idh_pengiriman));
        $exist = $this->db->select("a.*,count(*) as jumlah_hp")->group_by('a.id_barang')->get_where('d_pengiriman a', array('a.idh_pengiriman' => $idh_pengiriman));
        if (!empty($exist->row())) {
			//insert history pengiriman
			$is_retur = array('is_retur'=>$idh_pengiriman);
			foreach($exist->result() as $r){
				($r->imey=='imey_pengiriman')?$jumlah = $r->jumlah : $jumlah = $r->jumlah_hp;
				$this->insertHistory($r->id_barang,$jumlah,$idh_pengiriman,$r->idd_pengiriman,'keluar','d_pengiriman',$r->harga_jual,$is_retur);
			}
            $this->destroySessionPengiriman();
            //update keterangan yang masuk, simpan keterangan yang terakhir
            $idh_pengiriman = $this->input->post('idh_pengiriman', true);
            $keterangan = $this->input->post('keterangan', true);
            $this->db->set('keterangan', $keterangan)->where('idh_pengiriman', $idh_pengiriman)->update('h_pengiriman');
            $result = array('hasil'=>true,'id_pengiriman'=>$idh_pengiriman);
        } else {
            $result = array('hasil'=>false);
        }
        return $result;
    }

    function insertPermintaan() {
        $idh_permintaan = $this->session->userdata('idh_permintaan');
        $exist = $this->db->get_where('d_permintaan', array('idh_permintaan' => $idh_permintaan))->row();
        if (!empty($exist)) {
            $this->destroySessionPermintaan();
            $result = true;
        } else {
            $result = false;
        }
        return $result;
    }

    function destroySessionPermintaan() {
//        'keterangan_permintaan' => $keterangan, 'idh_permintaan' => $idh_permintaan, 'id_tujuan_permintaan' => $id_tujuan
        $data = array('keterangan_permintaan', 'idh_permintaan', 'id_tujuan_permintaan');
        $this->session->unset_userdata($data);
    }

    function destroySessionPengiriman() {
        $data = array('keterangan', 'idh_pengiriman', 'id_tujuan', 'tgl_nota', 'no_nota_pengiriman');
        $this->session->unset_userdata($data);
    }

    function truncatePermintaan() {
        $idh_permintaan = $this->session->userdata('idh_permintaan');
        $this->db->delete('h_permintaan', array('idh_permintaan' => $idh_permintaan));
        $this->destroySessionPermintaan();
    }

    function truncatePengiriman() {
        $idh_pengiriman = $this->session->userdata('idh_pengiriman');
        $getDetail = $this->db->get_where('d_pengiriman', array('idh_pengiriman' => $idh_pengiriman))->result();
        $idd_pengiriman = $this->general->convertArray($getDetail, 'idd_pengiriman');
		//insert history cancel pengiriman
		foreach($getDetail as $de){
			$this->insertHistory($de->id_barang,$de->jumlah,$idh_pengiriman,$de->idd_pengiriman,'masuk','d_pengiriman',$de->harga_jual,'');
		}
		//insert history cancel pengiriman
        $this->deleteDetailPengirimanMulti($idd_pengiriman);
        $this->db->delete('h_pengiriman', array('idh_pengiriman' => $idh_pengiriman));
        $this->destroySessionPengiriman();
    }

//pengiriman=====================================================
    function insertRetur() {
        $idh_retur = $this->session->userdata('idh_retur');
        $exist = $this->db->select("count(*) as jumlah_hp, a.*")->group_by('a.id_barang')->get_where('d_retur a', array('a.idh_retur' => $idh_retur));
        if (!empty($exist->row())) {
			//insert ke history log
			foreach($exist->result() as $r){
				$cekHp = $this->master->isHp($r->id_barang);
				//untuk get jumlah barang
				if($cekHp['is_hp']==1){
					//ini untuk hp
					$jumlah = $r->jumlah_hp;
				}else{
					$jumlah = $r->jumlah;
				}
				//getHarga 
				$is_retur = array('is_retur'=>$r->idh_retur);
				$getHarga = $this->db->get_where('mst_stok',array('id_toko'=>$this->owner,'id_barang'=>$r->id_barang,'is_retur'=>$r->idh_retur))->row();
				$this->insertHistory($r->id_barang,$jumlah,$idh_retur,$r->idd_retur,'keluar','d_retur',@$getHarga->harga_beli,$is_retur);
			}
			//insert ke history log
            $this->destroySessionRetur();
            $result = array('hasil'=>true,'idh_retur'=>$idh_retur);
        } else {
            $result = array('hasil'=>false);
        }
        return $result;
    }

    function truncateRetur() {
        $idh_retur = $this->session->userdata('idh_retur');
        $getDetail = $this->db->select("count(*) as jumlah_hp, a.*")->group_by('a.id_barang')->get_where('d_retur a', array('a.idh_retur' => $idh_retur))->result();
        $idd_retur = $this->general->convertArray($getDetail, 'idd_retur');
		if(!empty($getDetail)){
			//insert into log history
			foreach($getDetail as $gd){
				$cekHp = $this->master->isHp($gd->id_barang);
				//untuk get jumlah barang
				if($cekHp['is_hp']==1){
					//ini untuk hp
					$jumlah = $gd->jumlah_hp;
				}else{
					$jumlah = $gd->jumlah;
				}
				$is_retur = array('is_retur'=>$gd->idh_retur);
				$getHarga = $this->db->get_where('mst_stok',array('id_toko'=>$this->owner,'id_barang'=>$gd->id_barang,'is_retur'=>$gd->idh_retur))->row();
				$this->insertHistory($gd->id_barang,$jumlah,$idh_retur,$gd->idd_retur,'masuk','d_retur',@$getHarga->harga_beli,$is_retur);
			}
			//hapus detail retur 
			$this->deleteDetailRetur($idd_retur);
			
		}
        $this->db->delete('h_retur', array('idh_retur' => $idh_retur));
        $this->destroySessionRetur();
    }

    function destroySessionRetur() {
        $data = array('keterangan', 'idh_retur', 'id_retur', 'id_supplier', 'tgl_nota', 'no_nota_retur');
        $this->session->unset_userdata($data);
    }

    function deleteDetailRetur($id_detail) {
        foreach ($id_detail as $key => $d) {
//get detail retur
            $detail = $this->db->get_where('d_retur', array('idd_retur' => $d))->row();
//get idheader retur
            $idh_retur = $detail->idh_retur;
//get is hp or not
            $is_hp = $this->master->isHp($detail->id_barang)['is_hp'];
            if ($is_hp == 1) {
                $this->db->update('mst_stok', array('is_retur' => 0, 'status' => 0), array('id_toko' => $this->owner, 'is_hp' => 1, 'imey' => $detail->imey));
            } else {
                $getImey = $this->db->get_where('imey_retur', array('idd_retur' => $d))->result();
                $arrayImey = $this->general->idToInWhere($getImey, 'imey');
                $this->db->query("update mst_stok set is_retur = 0, status = 0 where imey in ($arrayImey) and is_hp = 0 and id_toko = '$this->owner'");
            }
            $this->db->delete('d_retur', array('idd_retur' => $d));
        }
        return $idh_retur;
    }

//=================================
    function deleteDetailReturMulti($id_detail = null) {
        foreach ($id_detail as $key => $d) {
            //getishp or not
            $getDetail = $this->db->get_where('d_retur', array('idd_retur' => $d))->row();
            $hp = $getDetail->imey;
            ($hp == 'imey_retur') ? $is_hp = 0 : $is_hp = 1;
            $idh_retur = $this->prosesDeleteDetailRetur($d, 'multi', $is_hp);
        }
        return @$getDetail->idh_retur;
    }

    #=========================batas

    function deleteDetailReturSingle($id_detail = null, $is_hp = null) {
        foreach ($id_detail as $key => $d) {
            $idh_retur = $this->prosesDeleteDetailRetur($d, 'single', $is_hp);
        }
        return $idh_retur['idh_retur'];
    }

    function prosesDeleteDetailRetur($id_detail = null, $type = null, $is_hp = null) {

        #jika tipe hp maka yang di ubah adalah tabel detail retur dan update mst stok
        if ($is_hp == 1) {
            $detail = $this->db->get_where('d_retur', array('idd_retur' => $id_detail));
            //get idheader retur
            $idh_retur = $detail->row()->idh_retur;
            $id_barang = $detail->row()->id_barang;
            //get detail retur
            if ($type == 'multi') {
                //get barang sejenis
                $getSejenis = $this->db->get_where('d_retur', array('idh_retur' => $idh_retur, 'id_barang' => $id_barang))->result();
                //convert to id in
                $arrayImey = $this->general->idToInWhere($getSejenis, 'imey');
                $arrayDetail = $this->general->idToInWhere($getSejenis, 'idd_retur');
                $this->db->query("delete from d_retur where idd_retur in ($arrayDetail)");
            } else {
                $arrayImey = "'" . $detail->row()->imey . "'";
                $this->db->delete('d_retur', array('idd_retur' => $id_detail));
            }

            $this->db->query("update mst_stok set status = 0, is_retur = 0 where id_toko = '$this->owner' and is_hp = 1 and imey in ($arrayImey)");
        } else {
            if ($type == 'multi') {
                //id detail adalah isinya id detail retur
                $getImey = $this->db->get_where('imey_retur', array('idd_retur' => $id_detail))->result();
                $arrayImey = $this->general->idToInWhere($getImey, 'imey');
                //untuk returnya
                $id_detail_retur = $id_detail;
                //proses delete detailnya
                $this->db->delete('d_retur', array('idd_retur' => $id_detail));
            } else {
                //id detail adalah isinya id detail imey retur
                $getImey = $this->db->get_where('imey_retur', array('idimey_retur' => $id_detail))->row();
                $arrayImey = "'" . $getImey->imey . "'";
                //delete imey retur yang bersangkutan dan update jumlah di table detail sesuai dengan jumlah count imey retur
                $this->db->delete('imey_retur', array('idimey_retur' => $id_detail));
                $id_detail_retur = $getImey->idd_retur;
                $this->db->query("update d_retur set jumlah = jumlah-1 where idd_retur = '$id_detail_retur'");
                //cek jumlah terakhir dari imey yang bersangkutan => jika jumlahnya 0 maka record di tabel detail juga dihapus
                $detail = $this->db->get_where('d_retur', array('idd_retur' => $id_detail_retur));
                $stokAkhir = @$detail->row()->jumlah;
                //get idheader retur
                //jika stokakhirnya adalah kosong maka tabel didetail dedelete sekalian
                if ($stokAkhir <= 0) {
                    $this->db->delete('d_retur', array('idd_retur' => $id_detail_retur));
                }
            }
            $this->db->query("update mst_stok set status = 0, is_retur = 0 where id_toko = '$this->owner' and is_hp = 0 and imey in ($arrayImey)");
        }

        $data = array('idh_retur' => $this->session->userdata('idh_pengiriman'));
        return $data;
    }

//==========================================================batas


    function showDetailRetur($idh_retur = null) {
        $this->db->select("a.*,count(*) as jumlah_hp");
        $this->db->group_by('a.id_barang');
        $isiTable = $this->db->get_where('d_retur a', array('a.idh_retur' => $idh_retur))->result();
        $data = array('isiTable' => @$isiTable);
        $this->load->view('tr_retur/table_retur', $data);
    }

	function get_harga_barang($id_barang=null,$id_gudang=null){
		return $get_barang = $this->db->get_where('mst_harga',array('id_toko'=>$id_gudang,'id_barang'=>$id_barang))->row();
	}
	  
	function cek_diskon($jumlah = null, $param = null) {
//ternyata variabel param itu isinya array jadi yang difungsi set rules callback_cek_stok[idbarang,jumlah]
        $fields = explode(',', $param);
        $id_barang = $fields[0];
        $jumlah = $fields[1];
        $stokDbs = $this->get_harga_barang($id_barang,$this->tokoSegudang);;
        (empty($stokDbs)) ? $stokDb = 0 : $stokDb = $stokDbs->diskon;
        if ($jumlah > $stokDb) {
            $this->form_validation->set_message('cek_diskon', 'Diskon Melebihi Batas');
            return false;
        } else {
            return true;
        }
    }		
	
	function cek_potongan($jumlah = null, $param = null) {
//ternyata variabel param itu isinya array jadi yang difungsi set rules callback_cek_stok[idbarang,jumlah]
        $fields = explode(',', $param);
        $id_barang = $fields[0];
        $jumlah = $fields[1];
        $stokDbs = $this->get_harga_barang($id_barang,$this->tokoSegudang);;
        (empty($stokDbs)) ? $stokDb = 0 : $stokDb = $stokDbs->potongan;
        if ($jumlah > $stokDb) {
            $this->form_validation->set_message('cek_potongan', 'Potongan Melebihi Batas');
            return false;
        } else {
            return true;
        }
    }	
	
	function cek_stok($jumlah = null, $param = null) {
//ternyata variabel param itu isinya array jadi yang difungsi set rules callback_cek_stok[idbarang,jumlah]
        $fields = explode(',', $param);
        $id_barang = $fields[0];
        $jumlah = $fields[1];
        $stokDbs = @$this->db->select('count(*) as stock', '*')->group_by('id_barang')->get_where('mst_stok', array('is_retur' => 0, 'status' => 0, 'id_barang' => $id_barang, 'id_toko' => $this->owner))->row();
        (empty($stokDbs)) ? $stokDb = 0 : $stokDb = $stokDbs->stock;
        if ($jumlah > $stokDb) {
            $this->form_validation->set_message('cek_stok', 'Stok Tidak Mencukupi');
            return false;
        } else {
            return true;
        }
    }

    function insertDetailReturNonHp() {
//        $data = array(
//            'id_barang' => $id_barang,
//            'jumlah' => $jumlah
//        );
        $id_barang = $this->input->post('id_barang', true);
        $jumlah = $this->input->post('jumlah', true);
        $keterangan = $this->input->post('keterangan', true);
//        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|is_natural_no_zero|callback_cek_stok[' . $id_barang . ',' . $jumlah . ']');
        if ($this->form_validation->run() == true) {
            $idh_retur = $this->session->userdata('idh_retur');
            $exist = $this->db->get_where('d_retur', array('idh_retur' => $idh_retur, 'id_barang' => $id_barang));
            $cek = @$exist->row();
            if (empty($cek)) {
                $params = array(
                    'idd_retur' => $idd_retur = $this->general->genNumberTemp('d_retur', 'idd_retur', $this->id_user, 'DRT', 4),
                    'idh_retur' => $idh_retur = $this->session->userdata('idh_retur'),
                    'id_barang' => @$id_barang,
                    'imey' => 'imey_retur',
                    'jumlah' => $jumlah,
                    'keterangan' => $keterangan
                );
                $this->db->insert('d_retur', $params);
            } else {
                //set idd retur didapat dari
                $idd_retur = $cek->idd_retur;
                $this->db->set('jumlah', 'jumlah+' . $jumlah, false);
                $this->db->set('keterangan', $keterangan);
                $this->db->where('idh_retur', $idh_retur);
                $this->db->where('idd_retur', $idd_retur);
                $this->db->update('d_retur');
            }

            $this->prosesNonHp($jumlah, $id_barang, 2, $idd_retur, 'imey_retur'); //2 adalah id status barang retur
            $data = array('success' => true, 'message' => 'Barang Berhasil di Retur');
        } else {
            $data = array('success' => false, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

    function prosesNonHp($limit = null, $id_barang = null, $status_update = null, $id_detail = null, $table = null) {
        $barang = $this->db->limit($limit)->get_where('mst_stok', array('status' => 0, 'id_barang' => $id_barang, 'id_toko' => $this->owner));
        switch ($table) {
            case 'imey_retur':
                //generate id Imey
                foreach ($barang->result() as $i) {
                    //insert ke tabel imey retur dan update table stok
                    $id_imey = $this->general->genNumberTemp('imey_retur', 'idimey_retur', $this->id_user, 'IRT', 4);
                    $this->db->query("insert into imey_retur values('$id_imey','$id_detail','$i->imey')");
                    $idh_retur = $this->session->userdata('idh_retur');
                    $this->db->query("update mst_stok set is_retur = '$idh_retur' ,status = $status_update where imey = '$i->imey' and id_toko = '$this->owner' and is_hp = 0 ");
                }
                break;
            case 'imey_pengiriman':
                //generate id Imey
                foreach ($barang->result() as $i) {
                    //insert ke tabel imey retur dan update table stok
                    $id_imey = $this->general->genNumberTemp('imey_pengiriman', 'idimey_pengiriman', $this->id_user, 'IKIRIM', 4);
                    $this->db->query("insert into imey_pengiriman values('$id_imey','$id_detail','$i->imey',0)");
                    $idh_pengiriman = $this->session->userdata('idh_pengiriman');
                    $this->db->query("update mst_stok set is_retur = '$idh_pengiriman' ,status = $status_update where imey = '$i->imey' and id_toko = '$this->owner' and is_hp = 0 ");
                }
                break;

            default :
                break;
        }
    }

    function insertDetailReturHp() {
        $imey = $this->input->post('id_barang');
        $keterangan = $this->input->post('keterangan');
        $dataBarang = $this->db->get_where('mst_stok', array('imey' => $imey))->row();
        $params = array(
            'idd_retur' => $idh_retur = $this->general->genNumberTemp('d_retur', 'idd_retur', $this->id_user, 'DRT', 4),
            'idh_retur' => $id_retur = $this->session->userdata('idh_retur'),
            'id_barang' => @$dataBarang->id_barang,
            'imey' => @$imey,
            'jumlah' => 1,
            'keterangan' => $keterangan
        );
        $this->db->insert('d_retur', $params);
        $this->db->update('mst_stok', array('status' => 2, 'is_retur' => $this->session->userdata('idh_retur')), array('imey' => $imey, 'id_toko' => $this->owner));
    }

    function insertHeaderRetur() {
        $this->form_validation->set_rules('id_supliyer', 'Supliyer', 'required');
        $this->form_validation->set_rules('id_retur', 'Jenis Retur', 'required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');
//        $this->form_validation->set_rules('token', 'token', 'callback_check_hp');
//cek barang tersebut handphone atau bukan 
        if ($this->form_validation->run() == true) {
            $params = array(
                'idh_retur' => $idh_retur = $this->general->genNumberTemp('h_retur', 'idh_retur', $this->id_user, 'HRT', 4),
                'id_sumber' => $this->owner,
                'id_supliyer' => $id_supp = $this->input->post('id_supliyer', true),
                'tgl_nota' => $tgl_nota = date("Y-m-d H:i:s"),
                'id_retur' => $id_retur = $this->input->post('id_retur', true),
                'id_user' => $this->id_user,
                'is_replay' => 0,
                'keterangan' => $keterangan = $this->input->post('keterangan', true)
            );
            $this->db->insert('h_retur', $params);
            $sessionku = array('keterangan' => $keterangan, 'idh_retur' => $idh_retur, 'id_retur' => $id_retur, 'id_supplier' => $id_supp);
            $this->session->set_userdata($sessionku);
            $data = array('success' => 1, 'message' => 'sukses', 'idh_retur' => $this->session->userdata('idh_retur'));
        } else {
            $data = array('success' => 0, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

//===============================
    function editTempPengadaan() {
        $idh = $this->input->post('idh_temp');
        $jmlId = count($idh);
        if ($jmlId == 1) {
            $id = $idh[0];
            $getDetail = $this->db->query("select b.*,a.* from h_temp a
            inner join mst_barang b on a.id_barang = b.id_barang
            where a.idh_temp = '$id'")->row();
            $is_hp = $this->master->isHp($getDetail->id_barang)['is_hp'];
            if ($is_hp == 1) {
                $this->db->select('imei');
                $imeys = $this->db->get_where('d_temp', array('idh_temp' => $id))->result();
                $imey = $this->general->convertArray($imeys, 'imei');
                $getDetail->imey = $imey;
            }
            $getDetail->status = 1;
        } else {
            $getDetail = array('status' => 0);
        }
        $hasil = json_encode($getDetail);
        return $hasil;
    }

    function isHpOrNot() {
        $imey = $this->input->post('imey', true);
        $id_barang = $this->input->post('id_barang', true);
        $jumlah = $this->input->post('jumlah', true);
        $is_hp = $this->master->isHp($id_barang);
        $cekHp = $is_hp['is_hp'];
        if ($cekHp == 1) {
            $tempImey = count(array_filter($imey));
            if ($tempImey == $jumlah) {
                return true;
            } else {
                $this->form_validation->set_message('check_hp', 'Jumlah Beli dan Jumlah Input Imey Tidak Sesuai');
                return false;
            }
        } else {
            return true;
        }
    }

    function deleteTempPengadaan() {
        $idh = $this->input->post('idh_temp');
        $idtemp = implode("','", (array) $idh);
        $idTemp = "'" . $idtemp . "'";
        $this->db->query("delete from h_temp where idh_temp in ($idTemp)");
    }

    function deleteDetailTempPengadaan($idd_temp = null, $idh_temp = null) {
//update jumlah header
        $this->db->set('jumlah', 'jumlah-1', false);
        $this->db->where('idh_temp', $idh_temp);
        $this->db->update('h_temp');
//delete detail hp
        $this->db->delete('d_temp', array('idd_temp' => $idd_temp));
        return true;
    }

	function check_imey_hp(){
		$imey=$this->input->post('imey');
		$id_barang = $this->input->post('id_barang', true);
		$imey=array_filter($imey);
		$imey=array_map('strtoupper', $imey);
		$imey=array_map('url_title', $imey);
		$results = implode("','", $imey);
        $result = "'" . $results . "'";
		
		//cek no nota
		$hasil= false;
		$no_nota = $this->session->userdata('no_nota');
		
		//cek temporari
		if(!empty($no_nota)){
			//pengadaan baru awal cek hanya di tabel mst stok
			$getIdHtemp = $this->db->get_where('h_temp',array('no_nota'=>$no_nota))->row();
			$idHTemp = @$getIdHtemp->idh_temp;
			$cekTempImey = $this->db->query("select * from d_temp where idh_temp = '$idHTemp' and imei in ($result)")->row();
			if(empty($cekTempImey)){
				$hasil1 = true;
			}else{
				$hasil1 = false;
			}
		}else{
			$hasil1 = true;
		}
		//cek mst stok
		$cekHpImey = $this->db->query("select * from mst_stok where id_toko = '$this->owner' and id_barang='$id_barang' and imey in ($result)")->row();
		if(empty($cekHpImey)){
			$hasil2 = true;
		}else{
			$hasil2 = false;
		}
		
		$hasil = $hasil1 && $hasil2;
		
		if($hasil){
			return true;
		}else{
			$this->form_validation->set_message('check_imey_hp', 'Imey Telah Ada');
            return false;
			
		}		
	}
    function insertTempPengadaan() {
        $no_nota = $this->session->userdata('no_nota');
		//jika cekimeyHpExist == true artinye cek imey sebelumnya belum pernah diinput
		//batas cek
			if (!empty($no_nota)) {
				$data = $this->insertTempPengadaanLanjut();
			} else {
				$data = $this->insertTempPengadaanBaru();
			}
        echo json_encode($data);
    }

    function updateImeyTempPengadaan($idh_temp = null, $jml = null) {
//get input user
        $input = $this->input->post('imey');
        $inputFilter = array_filter($input);
        $getId = $this->db->get_where('h_temp', array('idh_temp' => $idh_temp))->row();
        $is_hp = $this->master->isHp($getId->id_barang)['is_hp'];
        if ($is_hp == 1) {
            if ($jml != $getId->jumlah) {
                $dataImey = $this->db->get_where('d_temp', array('idh_temp' => $idh_temp))->result();
                $imeys = $this->general->convertArray($dataImey, 'imei');
//jika inputannya lebih banyak dari jumlah yang ada didatabase maka berarti insert jika sebaliknya maka berarti delete
                if (count($inputFilter) > count($imeys)) {
                    $isSame = array_diff($inputFilter, $imeys);
                    foreach ($isSame as $key => $val) {
                        $this->insertTempImeyManual($idh_temp, $val);
                    }
                } else {
                    $isSame = array_diff($imeys, $inputFilter);
                    $test = implode("','", (array) $isSame);
                    $idImeyHapus = "'" . $test . "'";
                    $this->db->query("delete from d_temp where idh_temp = '$idh_temp' and imei in ($idImeyHapus)");
                }
            }
        }
    }

    function insertTempImeyManual($idh_temp = null, $val = null) {
        $params = array('idd_temp' => $this->general->genNumberTemp('d_temp', 'idd_temp', $this->id_user, 'IMEY', 4),
            'idh_temp' => $idh_temp,
            'imei' => url_title($val));
        $this->db->insert('d_temp', $params);
    }

    function updateTempPengadaan() {
        $no_nota = $this->session->userdata('no_nota');
        $this->form_validation->set_rules('harga_beli', 'Harga Beli', 'required|integer');
        $this->form_validation->set_rules('jumlah', 'Jumlah Beli', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('token', 'token', 'callback_check_hp|callback_check_imey_hp');
        if ($this->form_validation->run() == true) {
            $idh_temp = $this->input->post('idh_temp', true);
            $jumlah = $this->input->post('jumlah', true);
            $harga_beli = $this->input->post('harga_beli', true);
            $diskon = $this->input->post('diskon', true);
            $potongan = $this->input->post('potongan', true);
//update temp imey jika jumlah baru yang diisikan tidak sama dengan jumlah yang sudah tersimpan didatabase
            $this->updateImeyTempPengadaan($idh_temp, $jumlah);
            $this->db->set('harga_beli', $harga_beli);
            $this->db->set('diskon', $diskon);
            $this->db->set('potongan', $potongan);
            $this->db->set('harga_diskon', $this->diskon($harga_beli, $diskon, $potongan));
            $this->db->set('jumlah', $jumlah);
            $this->db->where('idh_temp', $idh_temp);
            $this->db->update('h_temp');
            $data = array('success' => 1, 'message' => 'sukses', 'no_nota' => $this->session->userdata('no_nota'));
        } else {
            $data = array('success' => 0, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

    function insertTempPengadaanLanjut() {
        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('harga_beli', 'Harga Beli', 'required|integer');
        $this->form_validation->set_rules('jumlah', 'Jumlah Beli', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('token', 'token', 'callback_check_hp|callback_check_imey_hp');
//cek barang tersebut handphone atau bukan 
        $id_barang = $this->input->post('id_barang', true);
        $is_hp = $this->master->isHp($id_barang)['is_hp'];
//jika barang yang diinputkan sudah ada di list maka update jumlah dari list
        $no_nota = $this->session->userdata('no_nota');
        $tgl_nota = $this->session->userdata('tgl_nota');
        $cekExist = $this->db->get_where('h_temp', array('id_barang' => $id_barang, 'no_nota' => $no_nota, 'tgl_nota' => $tgl_nota))->row();
        if ($this->form_validation->run() == true) {
            if (empty($cekExist)) {
                $params = array(
                    'idh_temp' => $idh_temp = $this->general->genNumberTemp('h_temp', 'idh_temp', $this->id_user, 'TMPHPG', 4),
                    'id_supliyer' => $this->session->userdata('id_supplier'),
                    'tgl_nota' => $this->session->userdata('tgl_nota'),
                    'tgl_tempo' => $this->session->userdata('tgl_tempo'),
                    'no_nota' => $no_nota = $this->session->userdata('no_nota'),
                    'id_barang' => $id_barang,
                    'is_hp' => $is_hp,
                    'jumlah' => $this->input->post('jumlah', true),
                    'harga_beli' => $harga_beli = $this->input->post('harga_beli', true),
                    'potongan' => $potongan = $this->input->post('potongan', true),
                    'diskon' => $diskon = $this->input->post('diskon', true),
                    'idh_retur' => 0,
                    'harga_diskon' => $this->diskon($harga_beli, $diskon, $potongan)
                );
                $this->db->insert('h_temp', $params);
                $this->insertTempImey($idh_temp);
            } else {
                $jumlahBaru = $this->input->post('jumlah', true);
                $this->db->set('jumlah', 'jumlah+' . $jumlahBaru, false);
                $harga_beli = $this->input->post('harga_beli', true);
                $diskon = $this->input->post('diskon', true);
                $potongan = $this->input->post('potongan', true);
                $this->db->set('harga_beli', $harga_beli);
                $this->db->set('diskon', $diskon);
                $this->db->set('potongan', $potongan);
                $this->db->set('harga_diskon', $this->diskon($harga_beli, $diskon, $potongan));
                $this->db->where('no_nota', $no_nota);
                $this->db->where('tgl_nota', $tgl_nota);
                $this->db->where('id_barang', $id_barang);
                $this->db->update('h_temp');
//insert Tempnya
                $this->insertTempImey($cekExist->idh_temp);
            }
            $data = array('success' => 1, 'message' => 'sukses', 'no_nota' => $this->session->userdata('no_nota'));
        } else {
            $data = array('success' => 0, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }

        return $data;
    }

    function diskon($hargaAwal = null, $diskon = null, $potongan = null) {
        $hasil = $hargaAwal;
        if ($diskon != 0) {
            $hargaDiskon = $hargaAwal * ($diskon / 100);
            $hasil = $hargaAwal - $hargaDiskon;
        }
        if ($potongan != 0) {
            $hasil = $hargaAwal - $potongan;
        }
        return $hasil;
    }

    function insertTempPengadaanBaru() {
        $this->form_validation->set_rules('id_supliyer', 'Supliyer', 'required');
        $this->form_validation->set_rules('no_nota', 'Nota', 'required');
        $this->form_validation->set_rules('tgl_nota', 'Tangga Nota', 'required');
        $this->form_validation->set_rules('tgl_tempo', 'Tanggal Tempo', 'required');
        $this->form_validation->set_rules('id_barang', 'Barang', 'required');
        $this->form_validation->set_rules('harga_beli', 'Harga Beli', 'required|integer');
        $this->form_validation->set_rules('diskon', 'Diskon', 'integer');
        $this->form_validation->set_rules('potongan', 'Potongan', 'integer');
        $this->form_validation->set_rules('jumlah', 'Jumlah Beli', 'required|is_natural_no_zero');
        $this->form_validation->set_rules('token', 'token', 'callback_check_hp|callback_check_imey_hp');
        
//cek barang tersebut handphone atau bukan 
        $id_barang = $this->input->post('id_barang', true);
        $is_hp = $this->master->isHp($id_barang)['is_hp'];
        if ($this->form_validation->run() == true) {
            $params = array(
                'idh_temp' => $idh_temp = $this->general->genNumberTemp('h_temp', 'idh_temp', $this->id_user, 'TMPHPG', 4),
                'id_supliyer' => $id_supp = $this->input->post('id_supliyer', true),
                'tgl_nota' => $tgl_nota = $this->input->post('tgl_nota', true),
                'tgl_tempo' => $tgl_tempo = $this->input->post('tgl_tempo', true),
                'no_nota' => $no_nota = url_title(strtoupper(trim($this->input->post('no_nota', true)))),
                'id_barang' => $id_barang,
                'is_hp' => $is_hp,
                'jumlah' => $this->input->post('jumlah', true),
                'harga_beli' => $harga_beli = $this->input->post('harga_beli', true),
                'diskon' => $diskon = $this->input->post('diskon', true),
                'potongan' => $potongan = $this->input->post('potongan', true),
                'idh_retur' => 0,
                'harga_diskon' => $this->diskon($harga_beli, $diskon, $potongan)
            );
            $this->db->insert('h_temp', $params);
            $this->insertTempImey($idh_temp);
            $sessionku = array('id_supplier' => $id_supp, 'tgl_nota' => $tgl_nota, 'tgl_tempo' => $tgl_tempo, 'no_nota' => $no_nota);
            $this->session->set_userdata($sessionku);
            $data = array('success' => 1, 'message' => 'sukses', 'no_nota' => $this->session->userdata('no_nota'));
        } else {
            $data = array('success' => 0, 'message' => '<h4>Terjadi Kesalahan</h4>' . validation_errors());
        }
        return $data;
    }

    function insertTempImey($idh_temp = null) {
        $imey = $this->input->post('imey');
        if (!empty($imey)) {
            foreach ($imey as $key => $val) {
                if (!empty($val)) {
					$val = strtoupper($val);
                    $params = array('idd_temp' => $this->general->genNumberTemp('d_temp', 'idd_temp', $this->id_user, 'IMEY', 4),
                        'idh_temp' => $idh_temp,
                        'imei' => url_title($val));
                    $this->db->insert('d_temp', $params);
                }
            }
        }
    }

    function insertPengadaan() {
        $noNota = $this->session->userdata('no_nota');
        $getTemp = @$this->db->get_where('h_temp', array('no_nota' => $noNota));
        $cekExist = $getTemp->row();
        if (!empty($cekExist)) {
#insert header
            //cek is refund
            $id_retur = $getTemp->row()->idh_retur;
            ($id_retur != 0) ? $is_retur = 1 : $is_retur = 0;

            $params1 = array(
                'idh_pengadaan' => $idh_pengadaan = $this->general->genNumberTemp('h_pengadaan', 'idh_pengadaan', $this->id_user, 'HPG', 4),
                'id_supliyer' => $id_supp = $getTemp->row()->id_supliyer,
                'tgl_nota' => $tgl_nota = $getTemp->row()->tgl_nota,
                'tgl_jatuhtempo' => $tgl_tempo = $getTemp->row()->tgl_tempo,
                'no_nota' => $no_nota = $getTemp->row()->no_nota,
                'id_user' => $this->session->userdata('id_user'),
                'id_toko' => $this->owner,
                'is_refund' => $is_retur,
                'id_refund' => $id_retur);
            $this->db->insert('h_pengadaan', $params1);
            //================================================= update h retur ================================
            $this->db->set('is_replay', 1)->where('idh_retur', $id_retur)->update('h_retur');
// #insert detail
            foreach ($getTemp->result() as $dt) {
                $params2 = array(
                    'idd_pengadaan' => $idd_pengadaan = $this->general->genNumberTemp('d_pengadaan', 'idd_pengadaan', $this->id_user, 'DPG', 4),
                    'idh_pengadaan' => $idh_pengadaan,
                    'id_barang' => $id_barang = $dt->id_barang,
                    'harga_beli' => $harga_beli = $dt->harga_beli,
                    'diskon' => $diskon = $dt->diskon,
                    'potongan' => $potongan = $dt->potongan,
                    'harga_diskon' => $harga_potongan=$dt->harga_diskon,
                    'jumlah' => $jumlah = $dt->jumlah
                );
                $this->db->insert('d_pengadaan', $params2);
				//insert history pengadaan
				$this->insertHistory($id_barang,$jumlah,$idh_pengadaan,$idd_pengadaan,'masuk','d_pengadaan',$harga_potongan);
//bagikan ke tabel mst barang dan mst_stok
                $this->insertStokPengadaan($dt->is_hp, $dt, $idd_pengadaan);
            }
// #delete temporari
            $this->db->delete('h_temp', array('no_nota' => $noNota));
            $this->destroySessionPengadaaan();
            return $idh_pengadaan;
        } else {
            return false;
        }
    }

    function insertImeyPengadaan($idd_pengadaan = null, $imey = null) {
        $params = array('idimey_pengadaan' => $idImeyPengadaan = $this->general->genNumberTemp('imey_pengadaan', 'idimey_pengadaan', $this->id_user, 'IPG', 4),
            'idd_pengadaan' => $idd_pengadaan,
            'imey' => $imey);
        $this->db->insert('imey_pengadaan', $params);
    }

    function truncatePengadaan() {
        $noNota = $this->session->userdata('no_nota');
// #delete temporari
        $this->db->delete('h_temp', array('no_nota' => $noNota));
        $this->destroySessionPengadaaan();
        return true;
    }

    function hargaBeli($id_toko = null, $id_barang = null, $jumlah = null, $harga = null) {
        $exist = $this->db->get_where('mst_stok', array('id_toko' => $id_toko, 'id_barang' => $id_barang, 'status' => 0));
        $cekExist = $exist->row();
//inisialisasi result jika kosong maka langsung diisi dengan harga baru yang ditentukan
        $result = $harga;
        if (!empty($cekExist)) {
            $stokLama = count($exist->result()) * $exist->row()->harga_beli;
            $stokBaru = $jumlah * $harga;
            $jumlahStok = count($exist->result()) + $jumlah;
            $result = ($stokBaru + $stokLama) / $jumlahStok;
        }
        return $result;
    }

    function insertStokPengadaan($isHp = null, $h_temp = null, $idd_pengadaan = null) {
//rule pengadaan jika stok suatu id barang di suatu tempat itu masih ada pada saat proses transaksi maka harga belinya dipdate menjadi rata-rata(stok lama * harga lama)+ (stok baru*harga baru)/stok baru + stok lama;
        $exist = $this->db->get_where('mst_stok', array('id_barang' => $h_temp->id_barang, 'status' => 0, 'id_toko' => $this->owner));
        $hargaBeliBaru = $this->hargaBeli($this->owner, $h_temp->id_barang, $h_temp->jumlah, $h_temp->harga_diskon);
        if ($isHp == 1) {
//cek apakah barang dengan id tersebut masih ada stok digudang apa tidak
            if (empty($exist)) {
                $this->db->query("insert into mst_stok select '$this->owner','$h_temp->id_barang','$h_temp->id_supliyer',imei,0,$h_temp->harga_diskon,0,1 from d_temp where idh_temp = '$h_temp->idh_temp'");
            } else {
//update harga beli
//update stok yang lama didapat dari variabel exist
                $this->db->set('harga_beli', $hargaBeliBaru);
                $this->db->where('id_barang', $h_temp->id_barang);
                $this->db->where('status', 0);
                $this->db->where('id_toko', $this->owner);
                $this->db->update('mst_stok');
//insert stok baru dengan harga beli baru
                $this->db->query("insert into mst_stok select '$this->owner','$h_temp->id_barang','$h_temp->id_supliyer',imei,0,'$hargaBeliBaru',0,1 from d_temp where idh_temp = '$h_temp->idh_temp'");
            }
            //insert to imey pengadaan
            $getImeyHp = $this->db->get_where('d_temp', array('idh_temp' => $h_temp->idh_temp))->result();
            foreach ($getImeyHp as $imeyHp) {
                $this->insertImeyPengadaan($idd_pengadaan, $imeyHp->imei);
            }
        } else {
//untuk non hp
            if (!empty($exist)) {
//update harga beli
//update stok yang lama didapat dari variabel exist
                $this->db->set('harga_beli', $hargaBeliBaru);
                $this->db->where('id_barang', $h_temp->id_barang);
                $this->db->where('status', 0);
                $this->db->where('id_toko', $this->owner);
                $this->db->update('mst_stok');
//insert dengan harga beli adalah rata-rata dari stok lama + stok baru
                $jumlahInsert = $h_temp->jumlah;
                for ($i = 1; $i <= $jumlahInsert; $i++) {
                    $imeyNonHp = $this->general->imeyNonHp($this->owner, $h_temp->id_barang);
                    $this->db->query("insert into mst_stok values('$this->owner','$h_temp->id_barang','$h_temp->id_supliyer','$imeyNonHp',0,$hargaBeliBaru,0,0)");
                    $this->insertImeyPengadaan($idd_pengadaan, $imeyNonHp);
                }
            } else {
//jika stoknya kosong maka harga beli adalah harga baru
                $jumlahInsert = $h_temp->jumlah;
                for ($i = 1; $i <= $jumlahInsert; $i++) {
                    $imeyNonHp = $this->general->imeyNonHp($this->owner, $h_temp->id_barang);
                    $this->db->query("insert into mst_stok values('$this->owner','$h_temp->id_barang','$h_temp->id_supliyer','$imeyNonHp',0,$h_temp->harga_beli,0,0)");
                    $this->insertImeyPengadaan($idd_pengadaan, $imeyNonHp);
                }
            }
        }
    }

    function destroySessionPengadaaan() {
        $data = array('id_supplier', 'tgl_nota', 'tgl_tempo', 'no_nota', 'idh_retur_pengadaan');
        $this->session->unset_userdata($data);
    }

    function showTempTabel($id_nota = null) {
        $isiTable = $this->db->get_where('h_temp', array('no_nota' => $id_nota))->result();
        $data = array('isiTable' => $isiTable);
        $this->load->view('tr_pengadaan/table_pengadaan', $data);
    }

}
