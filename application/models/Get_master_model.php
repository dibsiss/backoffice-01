<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Get_master_model extends CI_Model {
	var $hakUser;   
   function __construct() {
        parent::__construct();
		$this->hakUser = @$this->session->userdata('hak_user');
    }

    function getBarang() {
        $barangs = $this->db->get('mst_barang')->result();
        foreach ($barangs as $b) {
            ?>
            <option value="<?php echo $b->id_barang ?>"><?php echo $b->nama ?></option>
            <?php
        }
    }

    function getSupliyer() {
        $supliyer = $this->db->get('mst_supliyer')->result();
        foreach ($supliyer as $s) {
            ?>
            <option value="<?php echo $s->id_supliyer ?>"><?php echo $s->nama ?></option>
            <?php
        }
    }

    function getListNonHp($id_supliyer = null) {
        $this->getNonHp();
        $this->datatables->where('a.id_supliyer', $id_supliyer);
        $this->datatables->add_column('view', '<a href="javascript:void(0)" onclick=insertReturNonHp("$1") class="btn btn-primary btn-sm">Retur</a>', 'id_barang');
        return $this->datatables->generate();
    }

    function getListHp($id_supliyer = null) {
        $this->getHp();
        $this->datatables->where('a.id_supliyer', $id_supliyer);
        $this->datatables->add_column('view', '<a href="javascript:void(0)" onclick=insertReturHp("$1") class="btn btn-primary btn-sm">Retur</a>', 'imey');
        return $this->datatables->generate();
    }

    function getListHpPengiriman() {
        $this->getHp();
		if($this->hakUser != 'toko'){
			$this->datatables->add_column('view', '<a href="javascript:void(0)" onclick=showSetHarga("$1","imey") class="btn btn-xs btn-success btn-sm">Set Harga</a> <a href="javascript:void(0)" onclick=insertPengirimanHp("$1") class="btn btn-xs btn-primary btn-sm">Kirim</a>', 'imey');
        }else{
			$this->datatables->add_column('view', '<a href="javascript:void(0)" onclick=insertPengirimanHp("$1") class="btn btn-xs btn-primary btn-sm">Kirim</a>', 'imey');
		}
		return $this->datatables->generate();
    }

    function getListNonHpPengiriman() {
        $this->getNonHp();
		if($this->hakUser != 'toko'){
			$this->datatables->add_column('view', '<a href="javascript:void(0)" onclick=showSetHarga("$1") class="btn btn-xs btn-success btn-sm">Set Harga</a> <a href="javascript:void(0)" onclick=insertPengirimanNonHp("$1") class="btn btn-xs btn-primary btn-sm">Kirim</a>', 'id_barang');
        }else{
			$this->datatables->add_column('view', '<a href="javascript:void(0)" onclick=insertPengirimanNonHp("$1") class="btn btn-xs btn-primary btn-sm">Kirim</a>', 'id_barang');
		}
		return $this->datatables->generate();
    }

    function getHp() {
        $this->datatables->select('b.nama,a.id_barang,a.imey,a.harga_beli');
        $this->datatables->from('mst_stok a');
        $this->datatables->join('mst_barang b', 'a.id_barang = b.id_barang');
        $this->datatables->where('a.id_toko', $this->owner);
        $this->datatables->where('a.status', 0);
        $this->datatables->where('a.is_retur', 0);
        $this->datatables->where('a.is_hp', 1);
    }

    function getListNonHpPermintaan($is_hp = null, $tujuan = null){
        $this->getPermintaan($is_hp,$tujuan);
        $this->datatables->add_column('view', '<a href="javascript:void(0)" onclick=insertPermintaanNonHp("$1") class="btn btn-primary btn-sm">Proses</a>', 'id_barang');
        return $this->datatables->generate();
    }
    function getListHpPermintaan($is_hp = null, $tujuan = null){
        $this->getPermintaan($is_hp,$tujuan);
        $this->datatables->add_column('view', '<a href="javascript:void(0)" onclick=insertPermintaanHp("$1") class="btn btn-primary btn-sm">Proses</a>', 'id_barang');
        return $this->datatables->generate();
    }
    function getPermintaan($is_hp = null, $tujuan = null) {
        $this->datatables->select('b.nama,count(*) as stock,a.id_barang,a.harga_beli');
        $this->datatables->from('mst_stok a');
        $this->datatables->join('mst_barang b', 'a.id_barang = b.id_barang');
        $this->datatables->where('a.id_toko', $tujuan);
        $this->datatables->where('a.status', 0);
        $this->datatables->where('a.is_retur', 0);
        if ($is_hp == 1) {
            $this->datatables->where('a.is_hp', 1);
        } else {
            $this->datatables->where('a.is_hp', 0);
        }
        $this->datatables->group_by('a.id_barang');
    }

    function getNonHp() {
        $this->datatables->select('b.nama,count(*) as stock,a.id_barang,a.harga_beli');
        $this->datatables->from('mst_stok a');
        $this->datatables->join('mst_barang b', 'a.id_barang = b.id_barang');
        $this->datatables->where('a.id_toko', $this->owner);
        $this->datatables->where('a.status', 0);
        $this->datatables->where('a.is_retur', 0);
        $this->datatables->where('a.is_hp', 0);
        $this->datatables->group_by('a.id_barang');
    }

    function getListImey($idh_temp = null) {
        $this->datatables->select('idd_temp,imei');
        $this->datatables->from('d_temp');
        $this->datatables->where('idh_temp', $idh_temp);
        $this->datatables->add_column('view', '<a href="javascript:void(0)" onclick=deleteImey("$1") class="btn btn-primary btn-sm">Delete</a>', 'idd_temp');
        return $this->datatables->generate();
    }

    function getListImeyPengiriman($idh_pengiriman = null, $id_barang = null, $is_hp = null) {
        if ($is_hp == 1) {
            $this->datatables->select('idd_pengiriman,imey');
            $this->datatables->from('d_pengiriman');
            $this->datatables->where('idh_pengiriman', $idh_pengiriman);
            $this->datatables->where('id_barang', $id_barang);
            $this->datatables->add_column('view', '<a href="javascript:void(0)" onclick=deleteImey("$1",1) class="btn btn-primary btn-sm">Delete</a>', 'idd_pengiriman');
        } else {
            $this->datatables->select('idimey_pengiriman,imey');
            $this->datatables->from('imey_pengiriman');
            $this->datatables->where('idd_pengiriman', $idh_pengiriman);
            $this->datatables->add_column('view', '<a href="javascript:void(0)" onclick=deleteImey("$1",0) class="btn btn-primary btn-sm">Delete</a>', 'idimey_pengiriman');
        }
        return $this->datatables->generate();
    }

    function getListImeyRetur($idh_retur = null, $id_barang = null, $is_hp = null) {
        if ($is_hp == 1) {
            $this->datatables->select('idd_retur,imey');
            $this->datatables->from('d_retur');
            $this->datatables->where('idh_retur', $idh_retur);
            $this->datatables->where('id_barang', $id_barang);
            $this->datatables->add_column('view', '<a href="javascript:void(0)" onclick=deleteImey("$1",1) class="btn btn-primary btn-sm">Delete</a>', 'idd_retur');
        } else {
            $this->datatables->select('idimey_retur,imey');
            $this->datatables->from('imey_retur');
            $this->datatables->where('idd_retur', $idh_retur);
            $this->datatables->add_column('view', '<a href="javascript:void(0)" onclick=deleteImey("$1",0) class="btn btn-primary btn-sm">Delete</a>', 'idimey_retur');
        }
        return $this->datatables->generate();
    }

}
