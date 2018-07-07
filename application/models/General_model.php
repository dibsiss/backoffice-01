<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class General_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }
    
	function createPaginasi($siteUrl=null,$count=null,$perPage=null,$uriSegment=null,$getData=null){
		(empty($perPage))? $perPage = 50 : $perPage=$perPage;
		 $this->load->library("pagination");
		 $config = array();
        $config["base_url"] = $siteUrl;
        $config["total_rows"] = $count;
        $config["per_page"] = $perPage;
        $config["uri_segment"] = $uriSegment;

        $this->pagination->initialize($config);

        $page = ($this->uri->segment($uriSegment)) ? $this->uri->segment($uriSegment) : 0;
        $data["results"] = $getData($config["per_page"], $page);
        $data["links"] = $this->pagination->create_links();
		return $data;
	}
	
    function convertArray($array = null, $field = null) {
        foreach ($array as $key => $val) {
            $data[] = $val->$field;
        }
        return $data;
    }

    function imeyNonHp($id_toko = null, $id_barang) {
        #format yang dihasilkan 20170908-BUZZER-0000001-00001 // tahunbulantanggal-id_barang-counter
        $hariIni = $hariini = date('Ymd');
        $hasil = $this->db->order_by('imey', 'desc')->get_where('mst_stok', array('id_toko' => $id_toko, 'id_barang' => $id_barang))->row();
        if (!empty($hasil)) {
            $bulanDatabase = substr($hasil->imey, 0, 8); //20160901BR0001
        } else {
            $bulanDatabase = 'gakPodoBos';
        }
        if ($bulanDatabase == $hariIni) {
            $counter = substr($hasil->imey, -5) + 1;
            $number = $hariIni . '-' . $id_barang . '-' . substr('00000' . $counter, -5);
        } else {
            $number = $hariIni . '-' . $id_barang . '-00001' ;
        }
        return $number;
    }
 
    function idToInWhere($array = null, $field = null) {
        $singleArray = $this->convertArray($array, $field);
        $results = implode("','", $singleArray);
        $result = "'" . $results . "'";
        return $result;
    }
    function listImey($array = null, $field = null) {
        $singleArray = $this->convertArray($array, $field);
        $results = implode(" #", $singleArray);
        $result = "#" . $results;
        return $result;
    }
    function idToInWhereWithoutQuote($array = null, $field = null) {
        $singleArray = $this->convertArray($array, $field);
        $results = implode("','", $singleArray);
        $result = $results;
        return $result;
    }
    function idToInWherePrimitif($array = null){
        $results = implode("','", $array);
        $result = "'" . $results . "'";
        return $result;
    }
    
    function extrackArray($array = null, $field = null) {
        $singleArray = $this->convertArray($array, $field);
        $result = implode("<br>", $singleArray);
        return $result;
    }
    

    function formatRupiah($rupiah = null) {
        return number_format($rupiah, 0, ".", ".");
    }

    function genNumberTemp($tabel = null, $field = null, $kode = null, $huruf = null, $jml = null) {
        //contoh penggunaan $id = $this->general->genNumberTemp('pmb_prodi','idpmb_prodi','PRODI',4);  
        //$this->general->genNumber('mst_category','id_category','CT',4);
        $bulanIni = $hariini = date('Ymd');
        $likewhere = $hariini . '-' . $kode.'-';

        $hasil = $this->db->query("select * from $tabel where $field like '%$likewhere%' order by $field desc limit 1")->row();
        if (!empty($hasil)) {
            $bulanDatabase = substr($hasil->$field, 0, 8); //20160901BR0001
        } else {
            $bulanDatabase = 'gakPodoBos';
        }
        if ($bulanDatabase == $bulanIni) {
            $counter = substr($hasil->$field, -$jml) + 1;
            $number = $bulanIni . '-' . $kode . '-' . $huruf . '-' . substr('0000' . $counter, -$jml);
        } else {
            $number = $bulanIni . '-' . $kode . '-' . $huruf . '-' . substr('0001', -$jml);
        }
        return $number;
    }

    function genNumber($tabel = null, $field = null, $kode = null, $jml = null) {
        //contoh penggunaan $id = $this->general->genNumber('pmb_prodi','idpmb_prodi','PRODI',10);  
        //$this->general->genNumber('mst_category','id_category','CT',4);
        $bulanIni = $hariini = date('Y') . date('m');

        $hasil = $this->db->query("select * from $tabel order by $field desc limit 1")->row();
        if (!empty($hasil)) {
            $bulanDatabase = substr($hasil->$field, 0, 6); //201609BR0001
        } else {
            $bulanDatabase = 'gakPodoBos';
        }
        if ($bulanDatabase == $bulanIni) {
            $counter = substr($hasil->$field, -$jml) + 1;
            $number = $bulanIni . $kode . substr('0000' . $counter, -$jml);
        } else {
            $number = $bulanIni . $kode . substr('0001', -$jml);
        }
        return $number;
    }

    function gengudang() {
        $gudang = $this->db->query("select * from mst_gudang order by id_gudang desc limit 1")->row();
        if (!empty($gudang->id_gudang)) {
            $database = substr($gudang->id_gudang, -4) + 1;
            $hasil = 'G' . substr('000' . $database, -4);
        } else {
            $hasil = 'G0001';
        }
        return $hasil;
    }

    function gentoko($id_gudang = null) {
        $toko = $this->db->query("select * from mst_toko  where id_gudang like '%$id_gudang%' order by id_toko desc limit 1")->row();

        if (!empty($toko)) {
            $database = substr($toko->id_toko, -4) + 1;
            $hasil = $id_gudang . '-T' . substr('000' . $database, -4);
        } else {
            $hasil = $id_gudang . '-T0001';
        }
        return $hasil;
    }

    function genbarang($id_merk = null) {
        //mengambil nama merek dari tabel merek
        $merk = $this->db->get_where('mst_merk', array('id_merk' => $id_merk))->row();
        $idCek = $merk->nama . '-' . date('Y') . date('m'); //axis-201901
        //select tabel barang dengan syarat nama merek + tahun tanggal sekarang
        $this->db->like('id_barang', $idCek);
        $this->db->order_by('id_barang', 'desc');
        $cekId = $this->db->get('mst_barang')->row(); //axis-201901001

        if (!empty($cekId)) {
            $count = substr($cekId->id_barang, -3) + 1;
            $hasil = $idCek . substr('00' . $count, -3);
        } else {
            $hasil = $idCek . '001';
        }
        return $hasil;
    }

    function testPre($array = null) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }

}
