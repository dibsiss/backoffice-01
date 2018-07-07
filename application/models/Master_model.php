<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Master_model extends CI_Model {
var $hakUser,$owner,$tokoSegudang;
    function __construct() {
        parent::__construct();
		 $this->hakUser = @$this->session->userdata('hak_user'); 
		 $this->owner = @$this->session->userdata('owner'); 
		 $this->tokoSegudang = @$this->session->userdata('toko_segudang'); 
        date_default_timezone_set("Asia/Jakarta");
    }

    function getById($table=null,$fieldId=null,$id=null){
        return $this->db->get_where($table,array($fieldId=>$id))->row();
    }
    function dateNow() {
        return gmdate("Y-m-d H:i:s", time() + 60 * 60 * 7);
    }

    function ownerHarga($id = null) {
        return $this->db->get_where('mst_harga', array('id_harga' => $id))->row()->id_toko;
    }

    function cekExistHarga() {
        $idToko = $this->owner;
        $idBarang = $this->input->post('id_barang');
        $cekDb = $this->db->get_where('mst_harga', array('id_toko' => $idToko, 'id_barang' => $idBarang))->row();
        if (empty($cekDb)) {
            return true;
        } else {
            $this->form_validation->set_message('cekExistHarga', 'Harga Barang Telah di Atur');
            return false;
        }
    }

    function setHarga($id_barang = null,$is_imey=null) {
		//jika is imey tidak kosongartinya barang tersebut adalah hp dan yang dilempar adalah imey bukan id barang ,jadi harus diget id barangnya dulu
		if(!empty($is_imey) && $is_imey != 'undefined'){
			$getIdBarang = @$this->db->get_where('mst_stok',array('imey'=>$id_barang,'id_toko'=>$this->owner))->row();
			$id_barang = @$getIdBarang->id_barang;
		}
		
		$cekExist = $this->db->get_where('mst_harga', array('id_toko' => $this->owner, 'id_barang' => $id_barang,))->row();
        if (!empty($cekExist)) {
			$harga = $this->general->formatRupiah($cekExist->harga_jual);
            echo "<center><h3>Harga Telah Disetting</h3><h1>Harga Jual Rp. $harga </h1></center>";
			
		} else {
            $crud=$this->prosesMstHarga("id_barang = '$id_barang'");
            $crud->unset_back_to_list();
            $crud->unset_delete();
            $crud->unset_read();
//            $crud->unset_edit();
            $output = $crud->render();
            $this->load->view('example',$output);
        }
    }

    function mstHarga() {
        $getIdBarang = $this->db->select('id_barang')->group_by('id_barang')->get_where('mst_stok', array('id_toko' => $this->owner))->result();
        if (!empty($getIdBarang)) {
            //get barang yang ada distok
            $whereId = $this->general->idToInWhere($getIdBarang, 'id_barang');
//            $whereIdBarang = "id_barang in ($whereId)";
            //get barang yang non fisik
            $getIdNonFisik = $this->db->select('id_barang')->get_where('mst_barang_detail', array('jenis' => 'ELEKTRIK'))->result();
            $whereku ="";
            if(!empty($getIdNonFisik)){
            $whereNon = $this->general->idToInWhere($getIdNonFisik, 'id_barang');
            $whereku .= $whereNon.',';
            }           

            $whereku .= $whereId;
            $whereIdBarang = "id_barang in ($whereku)";
        } else {
//            $whereIdBarang = "0=2";
            //get barang non fisik saja
			
            $getIdNonFisik = $this->db->select('id_barang')->get_where('mst_barang_detail', array('jenis' => 'ELEKTRIK'))->result();
            if (!empty($getIdNonFisik)) {
            $whereku = $this->general->idToInWhere($getIdNonFisik, 'id_barang');
			$whereIdBarang = "id_barang in ($whereku)";
        }
        }
        $crud = $this->prosesMstHarga(@$whereIdBarang);
        $output = $crud->render();
        $state = $crud->getState();
        $state_info = $crud->getStateInfo();
        $output->ket_header = 'Master Harga';
        if ($state == 'edit') {
            $primary_key = $state_info->primary_key;
            $cekDb = @$this->ownerHarga($primary_key);
            if ($cekDb == $this->owner) {
                $hasil = $output;
            } else {
                $hasil = '<center>Anda Tidak Berhak Mengakses Halaman Ini</center>';
            }
            //Do your awesome coding here. 
        } else if ($state == 'delete') {
            $cekDb = @$this->ownerHarga($primary_key);
            if ($cekDb == $this->owner) {
                $hasil = $output;
            } else {
                $hasil = '<center>Anda Tidak Berhak Mengakses Halaman Ini</center>';
            }
        } else {
            $hasil = $output;
        }
        return $hasil;
    }

    function prosesMstHarga($whereIdBarang = null) {
        $crud = new grocery_CRUD();
        $crud->set_table('mst_harga');
        $crud->set_subject('Harga');
        // $crud->set_relation('id_barang', 'mst_barang', 'nama', $whereIdBarang);
        $crud->set_relation('id_barang', 'mst_barang', 'nama');
        $crud->required_fields('id_barang', 'harga_jual', 'potongan', 'diskon');
        $crud->set_rules('id_barang', 'Barang', 'callback_cekExistHarga');
        $crud->where('id_toko', $this->owner);
        $crud->unset_read();
        $crud->columns('id_barang', 'harga_jual', 'potongan', 'diskon');
        $crud->field_type('id_harga', 'invisible');
        $crud->field_type('id_toko', 'invisible');
        $crud->display_as('harga_jual', 'Harga Beli/Harga Jual');
        $crud->display_as('id_barang', 'Barang');
        $crud->field_type('id_user', 'invisible');
        $crud->field_type('tgl_update', 'invisible');
        $crud->callback_before_insert(array($this, 'genHarga'));
        $crud->callback_after_insert(array($this, 'logHarga'));
        $crud->callback_after_update(array($this, 'logHargaUpdate'));
        $crud->edit_fields('harga_jual', 'potongan', 'diskon');
        $crud->callback_field('harga_jual', array($this, 'ubahHargaJual'));
        $crud->callback_column('harga_jual', array($this, 'colomnHargaJual'));
        return $crud;
    }

    function colomnHargaJual($value = null, $row = null) {
        $getHargaBeli = @$this->db->get_where('mst_stok', array('id_toko' => $this->owner, 'id_barang' => $row->id_barang, 'status' => 0))->row();
        $hargaBeli = @$getHargaBeli->harga_beli;
        (empty($hargaBeli)) ? $harga = 0 : $harga = $hargaBeli;
        return 'Rp. ' . $harga . ' / Rp.' . $value;
    }

    function ubahHargaJual($value = null, $primary_key = null) {
        $data = array('value' => $value);
        return $this->load->view('combo/tampil_harga', $data, true);
    }

    function logHarga($post_array = null, $primary_key = null) {
        //insert into log
        $this->insertLogHarga($post_array);
        return true;
    }

    function genHarga($post_array) {
        $post_array['id_harga'] = $this->general->genNumberTemp('mst_harga', 'id_harga', 'HARGA', 'HG', 4);
        $post_array['id_toko'] = $this->owner;
        $post_array['id_user'] = $this->id_user;
        return $post_array;
    }

    function logHargaUpdate($post_array = null, $primary_key = null) {
        $this->db->set(array('id_user' => $this->owner, 'tgl_update' => $this->dateNow()))->where('id_harga', $primary_key)->update('mst_harga');
        $getPostArray = $this->db->get_where('mst_harga', array('id_harga' => $primary_key))->result_array();
        $this->insertLogHarga($getPostArray[0]);
        return $post_array;
    }

    function insertLogHarga($post_array = null) {
        $params = array('idlog_harga' => $this->general->genNumberTemp('log_harga', 'idlog_harga', 'LOG', 'LH', 4),
            'id_toko' => $this->owner,
            'id_barang' => $post_array['id_barang'],
            'harga_jual' => $post_array['harga_jual'],
            'potongan' => $post_array['potongan'],
            'diskon' => $post_array['diskon'],
            'id_user' => $this->id_user,
            'tgl_isi' => $this->dateNow()
        );
        $this->db->insert('log_harga', $params);
    }

    function prosesStok($status = null, $title = null) {
        $crud = new grocery_CRUD();
        $crud->set_table('cek_stok');
        $crud->set_subject('Stok');
        $crud->set_primary_key('id_toko');
        $crud->columns('id_barang', 'kategori', 'merek', 'nama', 'stok', 'imey');
        $crud->callback_column('imey', array($this, 'callbackShowImeyColumn'));
        $crud->where('id_toko', $this->owner);
        $crud->where('status', $status);
		// $crud->add_action('Print Barcode', '', 'demo/action_more','fa fa-print');
		$crud->add_action('Print Barcode', '', '','fa fa-barcode',array($this,'just_a_test'));
		if($this->hakUser!='toko'){
			$crud->add_action('Koreksi Stok', '', '','emodal fa fa-upload',array($this,'koreksiStok'));
		}
		//        $crud->unset_operations();
        $crud->unset_add();
        $crud->unset_delete();
        $crud->unset_read();
        $crud->unset_edit();
        $output = $crud->render();
        $output->ket_header = $title."		<script>
			$(function () {
				$(document).on('hide.bs.modal', '.modal', function (event) {
					location.reload();
				});
			});
		</script>";
        return $output;
    }
	function just_a_test($primary_key , $row)
	{
	return site_url('laporan/printBarcode/'.$primary_key.'/'.$row->id_barang).'" target=_blank';
	}
	
	function koreksiStok($primary_key , $row){
		return site_url('umum/koreksiStok/'.$row->id_barang);
	}
 

    function showImeyColumn($idToko = null, $idBarang = null) {
        $isi = $this->db->select('imey')->get_where("mst_stok", array('id_toko' => $idToko, 'id_barang' => $idBarang, 'status' => 0, 'is_retur' => 0))->result();
        $data = array('isi' => $isi);
        $this->load->view('tr_pengiriman/list_invoice_imey', $data);
    }

    public function callbackShowImeyColumn($value, $row) {
        $idBarang = $row->id_barang;
        $idToko = $row->id_toko;
        $cekHp = $this->isHp($row->id_barang)['is_hp'];
        ($cekHp == 1) ? $return = "<a href='javascript:void(0)' onclick=emodal('" . site_url('umum/showImeyColumn/' . $idToko . '/' . $idBarang) . "','Imey') class='btn btn-primary text-center'>Imey</a>" : $return = '';
        return $return;
    }

    function isHp($id_barang = null) {
        $isHp = $this->db->query("select a.*,b.nama as is_hp,b.jenis from mst_barang a
        inner join mst_category b on a.id_category = b.id_category
        where a.id_barang ='$id_barang'")->row();
        $jenisHp = @$isHp->is_hp;
        ($jenisHp == 'HANDPHONE') ? $is_hp = 1 : $is_hp = 0;
        return $hasil = array('is_hp' => $is_hp, 'result' => $isHp);
    }

    function getDetailBarang($where = null) {
        return $data = $this->db->query("SELECT a.*,b.nama as category, c.nama as merk
		FROM mst_barang a
		inner join mst_category b on a.id_category = b.id_category
		inner join mst_merk c on a.id_merk = c.id_merk
		$where
		");
        //jika ingin retun multi maka where dikosongi
        //jika ingin berdasarkan id barang tertentu = a.idmst_barang = 'idbarangnya'
    }

    function customer() {
        $crud = new grocery_CRUD();
        $crud->set_table('mst_customer');
        $crud->set_subject('Customer');
        $crud->field_type('id_customer', 'invisible');
        $crud->field_type('id_toko', 'invisible');
        $crud->callback_before_insert(array($this, 'genCustomer'));
        $crud->columns('nama', 'alamat', 'hp', 'agen_id');
        $crud->where('id_toko', $this->owner);
        $crud->required_fields('nama', 'alamat', 'hp', 'agen_id');
        $output = $crud->render();
        $output->ket_header = 'Master Customer';
        return $output;
    }

    function genCustomer($post_array) {
        $post_array['id_customer'] = $this->general->genNumberTemp('mst_customer', 'id_customer', $this->id_user, 'CT', 4);
        $post_array['id_toko'] = $this->owner;
        return $post_array;
    }

    function mst_retur() {
        $crud = new grocery_CRUD();
        $crud->set_table('mst_retur');
        $crud->set_subject('Return');
        $crud->field_type('id_retur', 'invisible');
        $crud->callback_before_insert(array($this, 'genReturn'));
        $crud->columns('owner', 'status', 'is_hide');
        $crud->field_type('is_hide', 'dropdown', array('0' => 'Tampil', '1' => 'Sembunyi'));
        $crud->required_fields('status');
        $output = $crud->render();
        $output->ket_header = 'Master Retur';
        return $output;
    }

    function genReturn($post_array) {
        $post_array['id_retur'] = $this->general->genNumber('mst_retur', 'id_retur', 'RT', 4);
        return $post_array;
    }

    function category() {
        $crud = new grocery_CRUD();
        $crud->set_table('mst_category');
        $crud->set_subject('Kategori');
        $crud->field_type('id_category', 'invisible');
        $crud->callback_before_insert(array($this, 'genKategori'));
        $crud->columns('nama', 'jenis');
        $crud->set_rules('nama', 'nama', 'required');
        $crud->set_rules('jenis', 'jenis', 'required');
        $output = $crud->render();
        return $output;
    }

    function genKategori($post_array) {
        $post_array['id_category'] = $this->general->genNumber('mst_category', 'id_category', 'CT', 4);
        return $post_array;
    }

    function merk() {
        $crud = new grocery_CRUD();
        $crud->set_table('mst_merk');
        $crud->set_subject('Merk');
        $crud->field_type('id_merk', 'invisible');
        $crud->callback_before_insert(array($this, 'genMerk'));
        $crud->columns('id_merk', 'nama');
        $crud->display_as('id_merk', 'ID MERK');
        $crud->display_as('nama', 'NAMA MERK');
        $crud->set_rules('nama', 'nama', 'required');
        $crud->set_rules('nama', 'nama', 'trim|alpha');
        $crud->set_lang_string('insert_error', 'nama sudah ada di database');
        $output = $crud->render();
        $output->ket_header = 'Master Merk';
        return $output;
    }

    function genMerk($post_array) {
        $nama = strtoupper($post_array['nama']);
        $i = $this->db->get_where('mst_merk', array('nama' => $nama))->row();
        if (empty($i)) {
            $post_array['id_merk'] = $this->general->genNumber('mst_merk', 'id_merk', 'MERK', 4);
            $post_array['nama'] = $nama;
            return $post_array;
        } else {
            return false;
        }
    }

    function jenis_non_fisik() {
        $crud = new grocery_CRUD();
        $crud->set_table('mst_jenis_non_fisik');
        $crud->set_subject('Jenis Non Fisik');
        $crud->field_type('id_jenis_non_fisik', 'invisible');
        $crud->callback_before_insert(array($this, 'genjenis_non_fisik'));
        $crud->columns('nama');
        $output = $crud->render();
        $output->ket_header = 'Master Non Fisik';
        return $output;
    }

    function genjenis_non_fisik($post_array) {
        $post_array['id_jenis_non_fisik'] = $this->general->genNumber('mst_jenis_non_fisik', 'id_jenis_non_fisik', 'NF', 4);
        return $post_array;
    }

    function supliyer() {
        $crud = new grocery_CRUD();
        $crud->set_table('mst_supliyer');
        $crud->set_subject('Supliyer');
        $crud->field_type('id_supliyer', 'invisible');
        $crud->callback_before_insert(array($this, 'genSupliyer'));
        $crud->columns('id_supliyer', 'nama', 'norekening', 'alamat', 'website');
        $crud->display_as('id_supliyer', 'ID SUPLIYER');
        $crud->display_as('nama', 'NAMA SUPLIYER');
        $output = $crud->render();
        $output->ket_header = 'Master Supliyer';
        return $output;
    }

    function gensupliyer($post_array) {
        $post_array['id_supliyer'] = $this->general->genNumber('mst_supliyer', 'id_supliyer', 'SUP', 4);
        return $post_array;
    }

    function genToko($post_array) {
        $post_array['id_toko'] = $this->general->gentoko($post_array['id_gudang']);
        return $post_array;
    }

    function Gtoko($idgudang = null) {
        $crud = new grocery_CRUD();
        $crud->set_table('mst_toko');
        $crud->set_subject('Toko');
        $crud->field_type('id_toko', 'invisible');
        $crud->columns('id_toko', 'nama', 'alamat_toko', 'telp_toko', 'foto', 'id_gudang');
        $crud->display_as('id_toko', 'ID TOKO');
        $crud->display_as('nama', 'NAMA TOKO');
        $crud->display_as('id_gudang', 'GUDANG');
        $crud->set_field_upload("foto", "assets/uploads/files", "jpg|png");
        $crud->set_rules('nama', 'nama', 'required');
        $crud->set_rules('alamat', 'alamat', 'required');
        $crud->set_rules('telp_toko', 'telp', 'required');
//        $crud->set_rules('foto', 'foto', 'required');
        $crud->set_rules('id_gudang', 'gudang', 'required');
        $crud->callback_before_insert(array($this, 'genToko'));
        //     if (!empty($idgudang)) {
        $crud->where('mst_toko.id_gudang', $idgudang);
        $crud->field_type('id_gudang', 'hidden', $idgudang);
        //       } else {
        //           $crud->set_relation('id_gudang', 'mst_gudang', 'nama');
        //       }
        $output = $crud->render();
        $output->ket_header = 'Master Toko';
        return $output;
    }

    function toko() {
        $crud = new grocery_CRUD();
        $crud->set_table('mst_toko');
        $crud->set_subject('Toko');
        $crud->field_type('id_toko', 'invisible');
        $crud->columns('id_toko', 'nama', 'alamat_toko', 'telp_toko', 'foto', 'id_gudang');
        $crud->display_as('id_toko', 'ID TOKO');
        $crud->display_as('nama', 'NAMA TOKO');
        $crud->display_as('id_gudang', 'GUDANG');
        $crud->set_field_upload("foto", "assets/uploads/files", "jpg|png");
        $crud->set_rules('nama', 'nama', 'required');
        $crud->set_rules('alamat', 'alamat', 'required');
        $crud->set_rules('telp_toko', 'telp', 'required');
//        $crud->set_rules('foto', 'foto', 'required');
        $crud->set_rules('id_gudang', 'gudang', 'required');
        $crud->set_relation('id_gudang', 'mst_gudang', 'nama');
        $crud->callback_before_insert(array($this, 'genToko'));
//        if (!empty($idgudang)) {
//            $crud->where('mst_toko.id_gudang', $idgudang);
//            $crud->field_type('id_gudang', 'hidden', $idgudang);
//        } else {
//            $crud->set_relation('id_gudang', 'mst_gudang', 'nama');
//        }
        $output = $crud->render();
        $output->ket_header = 'Master Toko';
        return $output;
    }

    function genBarang($post_array) {
        $post_array['id_barang'] = $this->general->genbarang($post_array['id_merk']);
        return $post_array;
    }

    function barang() {
        $crud = new grocery_CRUD();
        $crud->set_table('mst_barang');
        $crud->set_subject('Barang');
        $crud->field_type('id_barang', 'invisible');
        $crud->callback_before_insert(array($this, 'genBarang'));
        $crud->columns('id_barang', 'nama', 'id_category', 'id_merk');
        $crud->display_as('id_barang', 'ID BARANG');
        $crud->display_as('nama', 'NAMA BARANG');
        $crud->display_as('id_category', 'Kategori');
        $crud->display_as('id_merk', 'Merek');
        //    $crud->set_field_upload("foto","assets/uploads/files","jpg|png");
        $crud->set_relation('id_category', 'mst_category', 'nama');
        $crud->set_relation('id_merk', 'mst_merk', 'nama');
        $crud->set_rules('nama', 'nama', 'required');
        $crud->set_rules('id_category', 'id_category', 'required');
        $crud->set_rules('id_merk', 'id_merk', 'required');
        $output = $crud->render();
        $output->ket_header = 'Master Barang';
        return $output;
    }

    function genGudang($post_array) {
        $post_array['id_gudang'] = $this->general->gengudang();
        return $post_array;
    }

    function gudang() {
        $crud = new grocery_CRUD();
        $crud->set_table('mst_gudang');
        $crud->set_subject('gudang');
        $crud->field_type('id_gudang', 'invisible');
        $crud->callback_before_insert(array($this, 'genGudang'));
        $crud->columns('id_gudang', 'nama', 'alamat_gudang', 'telp_gudang', 'foto');
        $crud->display_as('id_gudang', 'ID GUDANG');
        $crud->display_as('nama', 'NAMA GUDANG');
        $crud->add_action('Add Toko', '', 'superadmin/Gtoko', 'emodal fa-user');
        $crud->set_field_upload("foto", "assets/uploads/files", "jpg|png");
        $crud->set_rules('nama', 'nama', 'required');
        $crud->set_rules('alamat_gudang', 'alamat', 'required');
        $crud->set_rules('telp_gudang', 'telp', 'required');
//        $crud->set_rules('foto', 'foto', 'required');
        $output = $crud->render();
        $output->ket_header = 'Master Gudang';
        return $output;
    }

    function genUserGudang($post_array) {
        $post_array['id_usergudang'] = $this->general->genNumber('user_gudang', 'id_usergudang', 'UG', 4);
        $post_array['password'] = md5($post_array['password']);
        return $post_array;
    }

    function editgudang($post_array) {
        $idUserToko = $post_array['id_usergudang'];
        $passwordInput = $post_array['password'];
        $getUser = $this->db->get_where('user_gudang', array('id_usergudang' => $idUserToko))->row();
        $passwordDb = $getUser->password;
        if ($passwordDb != $passwordInput) {
            $post_array['password'] = md5($post_array['password']);
        }
        return $post_array;
    }

    function Usergudang() {
        $crud = new grocery_CRUD();
        $crud->set_table('user_gudang');
        $crud->set_subject('user');
        $crud->field_type('id_usergudang', 'invisible');
        $crud->callback_before_insert(array($this, 'genUserGudang'));
        $crud->callback_before_update(array($this, 'editgudang'));
        $crud->columns('id_usergudang', 'fullname', 'username', 'password', 'status', 'foto', 'id_gudang');
        $crud->display_as('id_gudang', 'Nama Gudang');
        $crud->set_field_upload("foto", "assets/uploads/files", "jpg|png");
		if($this->hak_user == 'gudang'){
			$crud->columns('id_usergudang', 'fullname', 'username', 'password', 'status', 'foto');
			$id_gudang = $this->owner;
			$crud->where('id_gudang',$id_gudang);
			$crud->field_type('id_gudang','hidden',$id_gudang);
		}else{
		$crud->columns('id_usergudang', 'fullname', 'username', 'password', 'status', 'foto', 'id_gudang');
        $crud->display_as('id_gudang', 'Nama Gudang');
        $crud->set_relation('id_gudang', 'mst_gudang', 'nama');
        }
		$crud->set_rules('fullname', 'fullname', 'required');
        $crud->set_rules('username', 'username', 'required');
		$crud->field_type('status','dropdown',array('admin' => 'Admin', 'accounting' => 'SPV'));
        $crud->change_field_type('password', 'password');
        $crud->set_rules('status', 'status', 'required');
        $crud->add_action('user toko', '', 'superadmin/toko', 'emodal fa-user');
//        $crud->set_rules('foto', 'foto', 'required');
        $output = $crud->render();
        $output->ket_header = 'User Gudang';
        return $output;
    }

    function UserGudangSingle() {
        $crud = new grocery_CRUD();
        $crud->set_table('user_gudang');
        $crud->set_subject('User Gudang');
        $crud->field_type('id_usergudang', 'invisible');
        $crud->callback_before_update(array($this, 'editgudang'));
        $crud->columns('id_usergudang', 'fullname', 'username', 'password', 'foto');
        $crud->edit_fields('id_usergudang', 'fullname', 'username', 'password', 'foto');
        $crud->set_field_upload("foto", "assets/uploads/files", "jpg|png");
        $crud->set_rules('fullname', 'fullname', 'required');
        $crud->set_rules('username', 'username', 'required');
        $crud->change_field_type('password', 'password');
//        $crud->set_rules('foto', 'foto', 'required');
        $crud->unset_back_to_list();
        $crud->unset_delete();
        $output = $crud->render();
        $output->ket_header = 'User Gudang';
        $state = $crud->getState();
        $state_info = $crud->getStateInfo();
        if ($state == 'edit') {
            $primary_key = $state_info->primary_key;
            if ($primary_key == @$this->id_user) {
                $output = $output;
            } else {
                $output = (object) array('ket_header' => 'Terjadi Kesalahan', 'output' => '<center>Anda Tidak Berhak Mengakses Halaman Ini</center>', 'js_files' => array(), 'css_files' => array());
            }
        } else {
            $output = (object) array('ket_header' => 'Terjadi Kesalahan', 'output' => '<center>Anda Tidak Berhak Mengakses Halaman Ini</center>', 'js_files' => array(), 'css_files' => array());
        }
        return $output;
    }

    function edituser($post_array) {
        $idUserToko = $post_array['id_user'];
        $passwordInput = $post_array['password'];
        $getUser = $this->db->get_where('user', array('id_user' => $idUserToko))->row();
        $passwordDb = $getUser->password;
        if ($passwordDb != $passwordInput) {
            $post_array['password'] = md5($post_array['password']);
        }
        return $post_array;
    }

    function genUsersuper($post_array) {
        $post_array['password'] = md5($post_array['password']);
        return $post_array;
    }

    function User() {
        $crud = new grocery_CRUD();

        $crud->set_table('user');
        $crud->set_subject('user');
//        $crud->field_type('id_usergudang', 'invisible');
        $crud->callback_before_insert(array($this, 'genUsersuper'));
        $crud->callback_before_update(array($this, 'edituser'));
//        $crud->columns('id_usergudang', 'fullname', 'username', 'password', 'status', 'foto', 'id_gudang');
//        $crud->display_as('id_gudang', 'ID GUDANG');
//        $crud->display_as('nama', 'NAMA GUDANG');
        $crud->set_field_upload("foto", "assets/uploads/files", "jpg|png");
//        $crud->set_relation('id_gudang', 'mst_gudang', 'nama');
        $crud->set_rules('fullname', 'fullname', 'required');
        $crud->set_rules('username', 'username', 'required');
        $crud->change_field_type('password', 'password');
//        $crud->set_rules('status', 'status', 'required');
//        $crud->add_action('user toko', '', 'superadmin/toko', 'emodal fa-user');
//        $crud->set_rules('foto', 'foto', 'required');
        $output = $crud->render();
        $output->ket_header = 'User Superadmin';
        return $output;
    }

    function UserTokoSingle() {
        $crud = new grocery_CRUD();
        $crud->set_table('user_toko');
        $crud->set_subject('user');
        $crud->field_type('id_usertoko', 'invisible');
        $crud->callback_edit_field('password', array($this, 'editusertokosingle'));
        $crud->columns('id_usertoko', 'fullname', 'username', 'password', 'status', 'foto', 'id_toko');
        $crud->edit_fields('id_usertoko', 'fullname', 'username', 'password', 'foto');
        $crud->set_field_upload("foto", "assets/uploads/files", "jpg|png");
        $crud->set_rules('fullname', 'fullname', 'required');
        $crud->set_rules('username', 'username', 'required');
        $crud->change_field_type('password', 'password');
//        $crud->set_rules('foto', 'foto', 'required');
        $crud->unset_back_to_list();
        $crud->unset_delete();
        $output = $crud->render();
        $output->ket_header = 'User Toko';
        $state = $crud->getState();
        $state_info = $crud->getStateInfo();
        if ($state == 'edit') {
            $primary_key = $state_info->primary_key;
            if ($primary_key == @$this->id_user) {
                $output = $output;
            } else {
                $output = (object) array('ket_header' => 'Terjadi Kesalahan', 'output' => '<center>Anda Tidak Berhak Mengakses Halaman Ini</center>', 'js_files' => array(), 'css_files' => array());
            }
        } else {
            $output = (object) array('ket_header' => 'Terjadi Kesalahan', 'output' => '<center>Anda Tidak Berhak Mengakses Halaman Ini</center>', 'js_files' => array(), 'css_files' => array());
        }
        return $output;
    }

    function Usertoko() {
        $hakUser = $this->session->userdata('hak_user');
        $crud = new grocery_CRUD();
        $crud->set_table('user_toko');
        $crud->set_subject('user');
        $crud->field_type('id_usertoko', 'invisible');
        $crud->callback_before_insert(array($this, 'genUserToko'));
        $crud->callback_edit_field('password', array($this, 'editusertoko'));
        $crud->columns('id_usertoko', 'fullname', 'username', 'password', 'status', 'foto', 'id_toko');
        $crud->display_as('id_usertoko', 'ID User');
        $crud->display_as('nama', 'NAMA TOKO');
        $crud->set_field_upload("foto", "assets/uploads/files", "jpg|png");
		if($this->hakUser=='superadmin'){
			$crud->set_relation('id_toko', 'mst_toko', 'nama');
		}else if($this->hakUser=='gudang'){
			$crud->where('id_gudang',$this->owner);//menampilkan user yang masih dalam satu gudang dengannya
			$crud->set_relation('id_toko','mst_toko','nama',array('id_gudang'=>$this->owner));
		}else{
			$crud->where('id_toko',$this->owner); //menampilkan user milik toko sendiri
			$crud->field_type('id_toko','hidden',$this->owner); //field id toko diubah menjadi hidden dan diset ke variabel session toko
		}
        
        $crud->set_rules('fullname', 'fullname', 'required');
        $crud->set_rules('username', 'username', 'required');
        $crud->change_field_type('password', 'password');
        $crud->set_rules('status', 'status', 'required');
        if($hakUser=='toko'){
            $crud->field_type('status','dropdown',array('kasir'=>'Kasir','sales'=>'Sales'));
        }else{
            $crud->field_type('status','dropdown',array('kepala_toko'=>'Kepala Toko','kasir'=>'Kasir','sales'=>'Sales'));
        }
//        $crud->set_rules('foto', 'foto', 'required');
        //     $crud->callback_before_insert(array($this, 'cek_usergudang_before_user_save'));
        $output = $crud->render();
        $output->ket_header = 'User Toko';
        return $output;
    }

    function genUserToko($post_array) {
        $post_array['id_usertoko'] = $this->general->genNumber('user_toko', 'id_usertoko', 'UT', 4);
        $post_array['password'] = md5($post_array['password']);
        return $post_array;
    }

    function editusertoko($value, $primary) {
        $data = array('isi' => $primary);
        return $this->load->view('user/password_toko', $data, true);
    }

    function updatePasswordToko($id) {
        $this->form_validation->set_rules('password', 'Password', 'required');
        if ($this->form_validation->run() == true) {
            $password = md5($this->input->post('password'));
            $this->db->update('user_toko', array('id_usertoko' => $id));
        } else {
            $this->load->view('user/form_password_toko');
        }
    }

    function editusertokoSingle($value, $primary) {
        $data = array('isi' => $primary);
        return $this->load->view('user/password_toko', $data, true);
    }

    function updatePasswordTokoSingle($id) {
        $this->form_validation->set_rules('password', 'Password', 'required');
        if ($this->form_validation->run() == true) {
            $password = md5($this->input->post('password'));
            $this->db->update('user_toko', array('id_usertoko' => $id));
        } else {
            $this->load->view('user/form_password_toko');
        }
    }

}
