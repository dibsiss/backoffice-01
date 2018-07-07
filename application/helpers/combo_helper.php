<?php
function cmb_antar_toko($penjualan=null){
	$ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
	$idUserGlobal = $ci->session->userdata('owner');
	if($statusUser!='superadmin'){
		if($statusUser=='toko'){
			$idGudangPadaToko = $ci->session->userdata('toko_segudang');
			$ci->db->where('id_toko !=',$idUserGlobal);
			$id_gudang = $idGudangPadaToko;
		}else{
			//jika gudang maka id gudang adalah idnya sendiri
			$id_gudang = $idUserGlobal;
		}
		$getToko=$ci->db->get_where('mst_toko',array('id_gudang'=>$id_gudang))->result();
		$cmb = "<option value=''></option>";
		foreach ($getToko as $gt) {
			$cmb .= "<option value='$gt->id_toko'>$gt->nama </option>";
		}
	}else{
		$cmb = "<option value=''></option>";
		$getGudang=$ci->db->get('mst_gudang')->result();
		foreach ($getGudang as $gt) {
			$cmb .= "<optgroup label='Gudang $gt->nama'>";
			if(empty($penjualan)){
				$cmb .= "<option value='$gt->id_gudang'>Gudang $gt->nama </option>";
			}
			$getToko=$ci->db->get_where('mst_toko',array('id_gudang'=>$gt->id_gudang))->result();
			foreach ($getToko as $go) {
				$cmb .= "<option value='$go->id_toko'>Toko $go->nama </option>";
			}
			$cmb .= " </optgroup>";
		}
	}
    return $cmb;
}
function cmb_mst_barang(){
	$ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
	$idGudangPadaToko = $ci->session->userdata('toko_segudang');
	$radios=$ci->db->join('mst_barang_detail',"mst_barang_detail.id_barang = mst_harga.id_barang")->get_where('mst_harga',array('id_toko'=>$idUserGlobal))->result();
	$cmb = "<option value=''></option>";
	foreach ($radios as $gt) {
		$cmb .= "<option value='$gt->id_barang'>$gt->id_barang $gt->nama </option>";
	}
    return $cmb;
}
function cmb_mst_barang_mutasi(){
	$ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
	$idGudangPadaToko = $ci->session->userdata('toko_segudang');
	$radios=$ci->db->join('mst_barang_detail',"mst_barang_detail.id_barang = mst_harga.id_barang")->get_where('mst_harga',array('id_toko'=>$idGudangPadaToko))->result();
	$cmb = "<option value=''></option>";
	foreach ($radios as $gt) {
		$cmb .= "<option value='$gt->id_barang'>$gt->id_barang $gt->nama </option>";
	}
    return $cmb;
}
function cmb_mst_barang_mutasi_gudang(){
    $ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
    $idGudangPadaToko = $ci->session->userdata('toko_segudang');
    $radios=$ci->db->join('mst_barang_detail',"mst_barang_detail.id_barang = mst_harga.id_barang")->get_where('mst_harga',array('id_toko'=>$idUserGlobal))->result();
    $cmb = "<option value=''></option>";
    foreach ($radios as $gt) {
        $cmb .= "<option value='$gt->id_barang'>$gt->id_barang $gt->nama </option>";
    }
    return $cmb;
}
function cmb_mst_barang_koreksi(){
	$ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
	$idGudangPadaToko = $ci->session->userdata('toko_segudang');
	$radios=$ci->db->join('mst_barang_detail',"mst_barang_detail.id_barang = tr_koreksi.id_barang")->get_where('tr_koreksi',array('id_pemilik'=>$idUserGlobal))->result();
	$cmb = "<option value=''></option>";
	foreach ($radios as $gt) {
		$cmb .= "<option value='$gt->id_barang'>$gt->id_barang $gt->nama </option>";
	}
    return $cmb;
}

function cmb_stock(){
	$ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
	$idGudangPadaToko = $ci->session->userdata('toko_segudang');
	switch ($statusUser) {
        case 'superadmin':
            $cmb .= "<option value='$statusUser'>Sendiri</option>";
            $cmb .= "<option value='gudang'>Gudang</option>";
            break;
        case 'gudang' :
            $cmb .= "<option value='$statusUser'>Sendiri</option>";
            $cmb .= "<option value='$idUserGlobal'>Toko</option>";
            break;
        case 'toko':
            $cmb .= "<option value='$idUserGlobal'>Sendiri</option>";
            $cmb .= "<option value='$idGudangPadaToko'>Toko Segudang</option>";
            break;
        default:
            $cmb .= "<option value='test'>Test</option>";
            break;
    }
    return $cmb;
}

function radio_retur(){
	$ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
	$ci->db->like('owner',$statusUser);
	$radios=$ci->db->get_where('mst_retur',array('is_hide'=>0))->result();
	$radio = "<div class='radio' >";
	$radio .= "<label><input name='id_retur' id='radio-default' value='semua' checked type='radio'>Semua</label>";
	foreach($radios as $key=>$r){
		$radio .= "<label><input name='id_retur' value='".$r->id_retur."' type='radio'>".$r->status."</label>";
	}
	$radio .= "</div>";
	return $radio;
}

function cmb_pegawai() {
    $ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
    $cmb = "<option value=''></option>";
    switch ($statusUser) {
        case 'superadmin':
            $getUsers = $ci->db->get('user')->result();
            foreach ($getUsers as $gt) {
                $cmb .= "<option value='$gt->id_user'>$gt->fullname</option>";
            }
            break;
        case 'toko' :
            $getUsers = $ci->db->get_where('user_toko', array('id_toko' => $idUserGlobal))->result();
            foreach ($getUsers as $gt) {
                $cmb .= "<option value='$gt->id_usertoko'>$gt->fullname</option>";
            }
            break;
        case 'gudang':
            $getUsers = $ci->db->get_where('user_gudang', array('id_gudang' => $idUserGlobal))->result();
            foreach ($getUsers as $gt) {
                $cmb .= "<option value='$gt->id_usergudang'>$gt->fullname</option>";
            }
            break;
        default:
            $cmb .= "<option value='test'>Test</option>";
            break;
    }
    return $cmb;
}

function cmb_suppliyer() {
    $ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
    $idSupliyer = $ci->session->userdata('id_supplier');
    $idGudangPadaToko = $ci->session->userdata('toko_segudang');
    //get option for superadmin and gudang 
    //beda di superadmin dan gudang hanya jika dia gudang maka tampil tambahan suppliyer adalah dari superadmin
    $optionUmum = $ci->db->get_where('mst_supliyer')->result();
    $umum = '';
    $cmb = "<option value=''></option>";
    $umum .= "<optgroup label='Supliyer'>";
    foreach ($optionUmum as $optUmum) {
        ($optUmum->id_supliyer == $idSupliyer) ? $selected = 'selected' : $selected = '';
        $umum .= "<option value='$optUmum->id_supliyer' $selected>$optUmum->nama</option>";
    }
    $umum .= " </optgroup>";
    switch ($statusUser) {
        case 'superadmin':
            $cmb .= $umum;
            break;
        case 'gudang':
            ($idSupliyer == 'superadmin') ? $selectedSuperadmin = 'selected' : $selectedSuperadmin = '';
            $cmb .= "<optgroup label='Superadmin'>";
            $cmb .= "<option value='superadmin' $selectedSuperadmin>Superadmin</option>";
            $umum .= " </optgroup>";
            $cmb .= $umum;
            break;
        case 'toko':
            $idGudang = $ci->db->query("select a.id_gudang,b.nama from mst_toko a
            inner join mst_gudang b on a.id_gudang = b.id_gudang where a.id_toko = '$idUserGlobal'")->result();
            $cmb .= "<optgroup label='Gudang'>";
            foreach ($idGudang as $ig) {
                ($ig->id_gudang == $idSupliyer) ? $selected = 'selected' : '';
                $cmb .= "<option value='$ig->id_gudang'>$ig->nama</option>";
            }
            $cmb .= " </optgroup>";
			$cmb .= "<optgroup label='Toko Segudang'>";
			$getTokoSegudang = $ci->db->get_where('mst_toko',array('id_gudang'=>$idGudangPadaToko,'id_toko !='=>$idUserGlobal))->result();
			foreach($getTokoSegudang as $gts){
				$cmb .= "<option value='$gts->id_toko'>$gts->nama</option>";
			}
			$cmb .= " </optgroup>";
            break;
        default :
            break;
    }
    return $cmb;
}


function cmb_jenis_non_fisik() {
    $ci = get_instance();
    $getJenis = $ci->db->get('mst_jenis_non_fisik')->result();
    $cmb = "<option value=''></option>";
    foreach ($getJenis as $gt) {
        $cmb .= "<option value='$gt->id_jenis_non_fisik'>$gt->nama</option>";
    }
    return $cmb;
}

function cmb_customer() {
    $ci = get_instance();
    $idUserGlobal = $ci->session->userdata('owner');
    $idCustomer = $ci->session->userdata('id_customer');
    $getCustomer = $ci->db->get_where('mst_customer', array('id_toko' => $idUserGlobal))->result();
    $cmb = "<option value=''></option>";
    foreach ($getCustomer as $gt) {
        ($gt->id_customer == $idCustomer) ? $selected = 'selected' : $selected = '';
        $cmb .= "<option value='$gt->id_customer' $selected>$gt->nama</option>";
    }
    return $cmb;
}

function cmb_jenis_retur() {
    $ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
    $idRetur = $ci->session->userdata('id_retur');
    $getRetur = $ci->db->get_where('mst_retur', array('owner' => $statusUser))->result();
    $cmb = "<option value=''></option>";
    foreach ($getRetur as $gt) {
        ($gt->id_retur == $idRetur) ? $selected = 'selected' : $selected = '';
        $cmb .= "<option value='$gt->id_retur' $selected>$gt->status</option>";
    }
    return $cmb;
}

function cmb_pengiriman() {
    $ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
    $id_tujuan = $ci->session->userdata('id_tujuan');
    $cmb = "<option value=''></option>";
    switch ($statusUser) {
        case 'superadmin':
            $gudang = $ci->db->get('mst_gudang')->result();
            $cmb .= "<optgroup label='Gudang'>";
            foreach ($gudang as $gd) {
                ($id_tujuan == $gd->id_gudang) ? $selected = 'selected' : $selected = '';
                $cmb .= "<option value='$gd->id_gudang' $selected>$gd->nama</option>";
            }
            $cmb .= " </optgroup>";
            break;
        case 'gudang':
            $toko = $ci->db->get_where('mst_toko', array('id_gudang' => $idUserGlobal))->result();
            foreach ($toko as $tk) {
                ($id_tujuan == $tk->id_toko) ? $selected = 'selected' : $selected = '';
                $cmb .= "<option value='$tk->id_toko' $selected>$tk->nama</option>";
            }
            break;
        case 'toko':
            //get id gudang yang masih dalam satu toko
            $gudangSejenis = $ci->session->userdata('toko_segudang');
			$ci->db->where('id_toko !=',$idUserGlobal);
            $toko = $ci->db->get_where('mst_toko', array('id_gudang' => $gudangSejenis))->result();
            foreach ($toko as $tk) {
                ($id_tujuan == $tk->id_toko) ? $selected = 'selected' : $selected = '';
                $cmb .= "<option value='$tk->id_toko' $selected>$tk->nama</option>";
            }
            break;
        default:
            break;
    }
    return $cmb;
}

function cmb_barang() {
    $ci = get_instance();
    $cmb = "<option value=''></option>";
    $cmbs = $ci->db->get('mst_barang')->result();
    foreach ($cmbs as $gd) {
        $cmb .= "<option value='$gd->id_barang' $selected>$gd->id_barang  $gd->nama</option>";
    }
    return $cmb;
}

function cmb_permintaan() {
    $ci = get_instance();
    $statusUser = $ci->session->userdata('hak_user');
    $idUserGlobal = $ci->session->userdata('owner');
    $id_tujuan = $ci->session->userdata('id_tujuan_permintaan');
    $cmb = "<option value=''></option>";
    switch ($statusUser) {
        case 'gudang':
            //jika dia gudang maka dia hanya bisa minta kepada superadmin
            ($id_tujuan == 'superadmin') ? $selected = 'selected' : $selected = '';
            $cmb .= "<option value='superadmin' $selected>Superadmin</option>";
            break;
        case 'toko':
            //get id gudang yang masih dalam satu toko
            $gudangSejenis = $ci->session->userdata('toko_segudang');
            $gudang = $ci->db->get_where('mst_gudang', array('id_gudang' => $gudangSejenis))->result();
            $cmb .= "<optgroup label='Gudang'>";
            foreach ($gudang as $gd) {
                ($id_tujuan == $gd->id_gudang) ? $selected = 'selected' : $selected = '';
                $cmb .= "<option value='$gd->id_gudang' $selected>$gd->nama</option>";
            }
            $cmb .= " </optgroup>";
            //jika toko dia juga bisa meminta dari toko yang lain yang masih satu gudang dengan dia
            $cmb .= "<optgroup label='Toko Lain'>";
            $toko = $ci->db->get_where('mst_toko', array('id_gudang' => $gudangSejenis))->result();
            foreach ($toko as $tk) {
                ($id_tujuan == $tk->id_toko) ? $selected = 'selected' : $selected = '';
                $cmb .= "<option value='$tk->id_toko' $selected>$tk->nama</option>";
            }
            $cmb .= " </optgroup>";
            break;
        default:
            break;
    }
    return $cmb;
}

?>